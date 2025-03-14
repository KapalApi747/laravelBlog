<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Keyword;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $categoryIds = request('category_ids', []);

        $posts = Post::with(['author', 'photo', 'categories'])
            ->published()
            ->filter($search)
            ->inCategories($categoryIds)
            ->sortable()
            ->paginate(5)
            ->appends(request()->query());

        $categories = Category::pluck('name', 'id');
        return view('backend.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        return view('backend.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = auth()->user()->id;

        // Afbeelding opslaan
        if ($request->hasFile('photo_id')) {
            $file = $request->file('photo_id');
            $path = $file->store('posts', 'public');
            $photo = Photo::create([
                'path' => $path,
                'alternate_text' => $validated['title'],
            ]);
            $validated['photo_id'] = $photo->id;
        }

        $post = Post::create(Arr::except($validated, ['keywords']));

        // Store keywords if provided
        if (!empty($validated['keywords'])) {
            // Convert comma-separated string into an array
            $keywordsArray = explode(',', $validated['keywords']);

            foreach ($keywordsArray as $keyword) {
                $trimmedKeyword = trim($keyword);

                // Check if keyword already exists to avoid duplicates
                $keywordModel = Keyword::firstOrCreate(['name' => $trimmedKeyword]);

                // Attach keyword to post (polymorphic relation)
                $post->keywords()->attach($keywordModel->id);
            }
        }

        // Categorieën koppelen
        $post->categories()->sync($request->categories);

        // Events triggeren
        PostCreated::dispatch($post);

        return redirect()->route('posts.index')->with('message', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('backend.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $categories = Category::pluck('name', 'id');
        return view('backend.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('update', $post);
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = $post->author_id;
        // Afbeelding bijwerken
        if ($request->hasFile('photo_id')) {
            if ($post->photo && Storage::disk('public')->exists($post->photo->path)) {
                Storage::disk('public')->delete($post->photo->path);
            }
            $file = $request->file('photo_id');
            $path = $file->store('posts', 'public');
            // Controleer of de post al een afbeelding heeft
            if ($post->photo) {
                $post->photo->update([
                    'path' => $path,
                    'alternate_text' => $validated['title']
                ]);
                $validated['photo_id'] = $post->photo->id;
            } else {
                // Maak een nieuwe foto aan
                $photo = Photo::create([
                    'path' => $path,
                    'alternate_text' => $validated['title']
                ]);
                $validated['photo_id'] = $photo->id;
            }
        } else {
            // Voorkom dat een null-waarde wordt toegevoegd aan photo_id
            unset($validated['photo_id']);
        }
        $post->update($validated);
        $post->categories()->sync($request->categories);
        $post->keywords()->sync($request->keywords);

        return redirect()->route('posts.index')->with('message', 'Post succesvol bijgewerkt.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Fysisch op de schijf wissen
        if($post->photo && Storage::disk('public')->exists($post->photo->path)) {
            Storage::disk('public')->delete($post->photo->path);
        }

        // Soft delete database
        $post->delete();
        return redirect()->route('posts.index')->with('message', 'Post deleted successfully!');
    }
}
