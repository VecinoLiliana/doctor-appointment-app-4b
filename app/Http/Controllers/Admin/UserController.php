<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255|unique:users,name',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|min:5|max:20|regex:/[A-Za-z0-9\-]+$/|unique:users',
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);
            $user = User::create($data);

            $user->roles()->attach($data['role_id']);

            session()->flash('swall', [
                'icon' => 'success',
                'title' => 'Usuario Creado',
                'text' => 'El usuario fue creado exitosamente',
            ]);
                return redirect()->route('admin.users.index') -> with('success', 'El usuario fue creado exitosamente');
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
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //No permitir que el usuario logeado se borre a sí mismo
        if (Auth::id() == $user->id) {
            session()->flash('swall', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puede eliminarse a sí mismo',
            ]);
            abort(403, 'Este usuario no puede eliminarse');
        }

        //Eliminar los roles asociados a un usuario
        $user->roles()->detach();

        //ELiminar el usuario
        $user -> delete();

        session()->flash('swall', [
            'icon' => 'success',
            'title' => 'Usuario Eliminado',
            'text' => 'El usuario fue eliminado exitosamente',
        ]);
        return redirect()->route('admin.users.index');
    }
}
