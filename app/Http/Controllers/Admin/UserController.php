<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Mail\UsuarioCriado;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $senhaTemporaria = $request->password;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true
        ]);

        Mail::to($request->email)->send(new UsuarioCriado($request->name, $request->email, $senhaTemporaria));

        return redirect()->route('admin.users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Usuário criado com sucesso!',
            'duration' => 5000,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->all());

        return redirect()->route('admin.users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Usuário atualizado com sucesso!',
            'duration' => 5000,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Usuário removido com sucesso!',
            'duration' => 5000,
        ]);
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return redirect()->route('admin.users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Status do usuário atualizado!',
            'duration' => 5000,
        ]);
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        return redirect()->route('admin.users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Nível administrador do usuário atualizado!',
            'duration' => 5000,
        ]);
    }
}
