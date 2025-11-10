<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //no se
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validar que se cree bien en BD
        $request->validate(['name' => 'required|unique:roles,name']);

        //Variable de un solo uso para alerta
        session()->flash('swal',
            [

            'icon'=>'success',
            'title'=>'Role creado exitosamente',
            'text'=>'El rol se ha creado correctamente'

        ]);

        //Si pasa la validación crear el rol
        Role::create(['name'=> $request->name]);

        //Redirecciona a la tabla principal
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {

        //Restringir la acción a los primeros 4 roles
        if ($role->id <=4){
            //Variable de un solo uso
            session()->flash('swal',
                [
                    'icon'=>'error',
                    'title'=>'Error',
                    'text'=>'No puedes editar este rol'
                ]
            );
            return redirect()->route('admin.roles.index');
        }
        //
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //Validar que se cree bien en BD
        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]);

        //Si el campo no cambio no actualices
        if ($role->name === $request->name){
            session()->flash('swal',
                [
                    'icon'=>'info',
                    'title'=>'Sin cambios',
                    'text'=>'No se detectaron modificaciones'
                ]
            );
            //Redireción al mismo lugar
            return redirect()->route('admin.roles.edit', $role);
        }

        //Si pasa la validación edita el rol
        $role->update(['name'=>$request->name]);

        //Variable de un solo uso para alerta
        session()->flash('swal',
            [
                'icon'=>'success',
                'title'=>'Role actualizado exitosamente',
                'text'=>'El rol se ha actualizado correctamente'

            ]);

        //Redirecciona a la tabla principal
        return redirect()->route('admin.roles.index', $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //Restringir la acción a los primeros 4 roles
        if ($role->id <=4){
            //Variable de un solo uso
            session()->flash('swal',
                [
                    'icon'=>'error',
                    'title'=>'Error',
                    'text'=>'No puedes eliminar este rol'
                ]
            );
            return redirect()->route('admin.roles.index');
        }
        //Borrar el elemento
        $role->delete();

        //Alerta
        session()->flash('swal',
            [
                'icon'=>'success',
                'title'=>'Role eliminado exitosamente',
                'text'=>'El rol se ha eliminado correctamente'
            ]
        );
        //Redireccionar al mismo lugar
        return redirect()->route('admin.roles.index');
    }
}
