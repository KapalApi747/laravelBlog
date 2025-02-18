<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $folder = 'users';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Homepagina van mijn users
        $users = User::withTrashed()->with(['roles', 'photo'])->orderBy('id', 'desc')->paginate(20);
        //return view('backend.users.index',['users' => $users]);
        return view('backend.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Weergave voor een nieuwe user
        $roles = Role::pluck('name', 'id')->all();
        return view('backend.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Messages bij de velden
        $messages = [
            'name.required' => 'De naam is verplicht.',
            'email.required' => 'Het e-mailadres is verplicht.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'password.required' => 'Het wachtwoord is verplicht.',
            'password.min' => 'Het wachtwoord moet minimaal :min tekens bevatten.',
            'role_id.required' => 'Selecteer minimaal 1 rol voor de gebruiker.',
            'role_id.*.exists' => '1 van de geselecteerde rollen bestaat niet.',
            'role_id.array' => 'De rollen moeten een lijst van ID\'s zijn.',
            'is_active.required' => 'Selecteer of de gebruiker actief is.',
            'photo_id.image' => 'De geüploade afbeelding moet een geldig afbeeldingsbestand zijn.',
        ];

        // Wegschrijven van 1 enkele user
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'is_active' => 'required|in:0,1',
            'role_id' => 'required|array|exists:roles,id',
            'password' => 'required|min:6',
            'photo_id' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], $messages);

        // Password hashing
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Controleer of er een foto is geupload en sla deze op
        if ($request->hasFile('photo_id')) {
            $file = $request->file('photo_id');
            $uniqueName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $this->folder . '/' . $uniqueName;

            // Opslaan in het public path onder users
            $file->storeAs('', $filePath, 'public');
            $photo = Photo::create([
                'path' => $filePath,
                'alternate_text' => $validatedData['name'],
            ]);
            $validatedData['photo_id'] = $photo->id;
        }

        // Gebruiker aanmaken
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'is_active' => $validatedData['is_active'],
            'password' => $validatedData['password'],
            'photo_id' => $validatedData['photo_id'],
        ]);

        // Array van rollen wegschrijven naar de role_user tussentabel
        // Sync doet een detach en een attach in 1 keer
        $user->roles()->sync($validatedData['role_id']);

        // Redirect naar users
        return redirect()->route('users.index')->with('message', 'User created successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Weergeven van 1 enkele user
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::with('roles', 'photo')->findOrFail($id);
        $roles = Role::pluck('name', 'id')->all();
        $photoDetails = [
            'exists' => false,
            'filesize' => 0,
            'width' => 'N/A',
            'height' => 'N/A',
            'extension' => ''
        ];

        // Controleer of er een foto is en of deze bestaat op de 'public' disk
        if ($user->photo && Storage::disk('public')->exists($user->photo->path)) {
            $photoDetails['exists'] = true;
            $photoDetails['filesize'] = round(Storage::disk('public')->size($user->photo->path) / 1024, 2);
            $photoPath = Storage::disk('public')->path($user->photo->path);
            $dimensions = getimagesize($photoPath);
            $photoDetails['width'] = $dimensions[0] ?? 'N/A';
            $photoDetails['height'] = $dimensions[1] ?? 'N/A';
            $photoDetails['extension'] = Str::upper(pathinfo($user->photo->path, PATHINFO_EXTENSION));
        }
        return view('backend.users.edit', compact('user', 'roles', 'photoDetails'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Haal de gebruiker op of geef een 404 als deze niet bestaat
        $user = User::findOrFail($id);
        // Validatieberichten
        $messages = [
            'name.required' => 'De naam is verplicht.',
            'email.required' => 'Het e-mailadres is verplicht.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'role_id.required' => 'Selecteer minimaal één rol.',
            'role_id.array' => 'De rollen moeten een lijst van ID\'s zijn.',
            'role_id.*.exists' => 'Een van de geselecteerde rollen bestaat niet.',
            'is_active.required' => 'Selecteer of de gebruiker actief is.',
            'password.min' => 'Het wachtwoord moet minimaal :min tekens bevatten.',
            'photo_id.image' => 'De geüploade afbeelding moet een geldig afbeeldingsbestand zijn.',
            'photo_id.mimes' => 'De afbeelding moet een bestand van het type: jpg, jpeg, png, gif zijn.',
            'photo_id.max' => 'De afbeelding mag maximaal :max kilobytes zijn.',
        ];

        // Valideer de input; de e-mail validatie houdt rekening met de huidige gebruiker
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|array',
            'role_id.*' => 'exists:roles,id',
            'is_active' => 'required|in:0,1',
            'password' => 'nullable|min:6',
            'photo_id' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], $messages);

        // Verwerk het wachtwoord: als er een nieuw wachtwoord is ingevuld, hash deze; anders laat je de oude waarde intact.
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Verwerk de foto: als er een nieuwe foto is geüpload
        if ($request->hasFile('photo_id')) {
            $file = $request->file('photo_id');
            // Genereer een unieke bestandsnaam met behulp van een UUID
            $uniqueName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            // Gebruik de class-property (bijv. 'users') en bouw het bestandspad op als 'users/uniquename.ext'
            $filePath = $this->folder . '/' . $uniqueName;
            // Sla het bestand op in de 'public'-disk (die verwijst naar storage/app/public)
            $file->storeAs('', $filePath, 'public');

            // Controleer of de gebruiker al een foto-record heeft
            if ($user->photo && Storage::disk('public')->exists($user->photo->path)) {
                // Verwijder de oude fysieke foto
                Storage::disk('public')->delete($user->photo->path);
                // Update het bestaande Photo-record met de nieuwe bestandsnaam en alternate text
                $user->photo->update([
                    'path' => $filePath,
                    'alternate_text' => $validatedData['name']
                ]);
                // Gebruik hetzelfde photo record id
                $validatedData['photo_id'] = $user->photo->id;
            } else {
                // Als er nog geen foto-record is, maak er dan een nieuw aan
                $photo = Photo::create([
                    'path' => $filePath,
                    'alternate_text' => $validatedData['name']
                ]);
                $validatedData['photo_id'] = $photo->id;
            }
        }

        // Werk de gebruiker bij met de gevalideerde data
        $user->update($validatedData);

        // Synchroniseer de rollen voor de gebruiker
        $user->roles()->sync($validatedData['role_id']);

        return redirect()->back()->with('message', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete van een user
        $user = User::with('photo')->findOrFail($id);

        // Controleer of het fysieke bestand bestaat
        if ($user->photo) {
            if (Storage::disk('public')->exists($user->photo->path)) {
                Storage::disk('public')->delete($user->photo->path);
            }
        }

        // Verwijderen van de user
        $user->delete();

        // Redirect
        return redirect()->route('users.index')->with('message', 'User deleted successfully!');
    }

    public
    function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('users.index')->with('message', 'User restored successfully!');
    }
}
