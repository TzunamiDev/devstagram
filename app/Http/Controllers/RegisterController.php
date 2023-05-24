<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class RegisterController extends Controller
{
    public function index()
    {

        // $files = Storage::disk('oci')->files('/');
        // $files = Storage::disk('oci')->exists("/gatolag.jpg");
        // $data = [
        //     'url_imagen_registrar' => Storage::disk('oci')->url('images/registrar.jpg'),
        //     'url_imagen_login' => Storage::disk('oci')->url('images/login.jpg'),
        // ];

        
        // return view('auth.register', compact('data'));
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // dd($request);
        // dd($request->get('name'));

        // Modificar el Request (hacerlo cómo última opción, es recomendable modificar el request lo menos posible)
        $request->request->add(['username' => Str::slug($request->username)]);

        // Validación
        $this->validate($request, [
            'name' => 'required|max:30',
            // 'username'=> 'required|unique:users|min:3|max:30|regex:/^[a-zA-Z0-9_-]+$/u|doesnt_start_with:_,-|doesnt_end_with:_,-',
            'username'=> 'required|unique:users|min:3|max:30',
            'email' => 'required|unique:users|email|max:60',
            'password' => 'required|confirmed|min:6',
        ]);

        
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Autenticar un usuario
        /* auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ]); */

        // Otra formade de autenticar
        auth()->attempt($request->only('email', 'password'));

        // Redireccionar
        return redirect()->route('posts.index', auth()->user()->username);
    }
}
