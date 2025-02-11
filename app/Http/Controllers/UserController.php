<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Homepagina van mijn users
        $users = User::all();
        $roles = Role::all();
        //return view('backend.users.index',['users' => $users]);
        return view('backend.users.index', compact('users','roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Weergave voor een nieuwe user
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Wegschrijven van 1 enkele user
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
    public function edit(string $id)
    {
        // Weergave van 1 enkele user met de waarden opgehaald uit de DB
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Het overschrijven van de gewijzigde waarden uit de function edit
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete van een user
    }
}
