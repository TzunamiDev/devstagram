<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request, User $user)
    {


        // Modificar el Request
        $request->request->add(['username' => Str::slug($request->username)]);


        $this->validate($request, [
            'username' => [
                'required',
                'unique:users,username,'.auth()->user()->id,
                'min:3',
                'max:30',
                'not_in:twitter,editar-perfil'
            ],
            'email' => [
                'required',
                'unique:users,email,'.auth()->user()->id,
                'email',
                'max:60',
                function($attribute, $value, $fail) use ($request) {
                    if($value !== auth()->user()->email) {
                        if(!$request->input('old_password')) {
                            $fail('Para poder cambiar el email el campo old_password no debe estar vacío');
                        }
                    }
                },
            ],
            'old_password' => [
                'nullable',
                'current_password:web'
            ],
            'password' => [
                'nullable',
                'confirmed',
                'min:6',
                'different:old_password',
                function($attribute, $value, $fail) use ($request) {
                    if(!$request->input('old_password')) {
                        $fail('Para poder cambiar la contraseña el campo old_password no debe estar vacío');
                    }
                },
            ]
        ]);

        if($request->imagen) {
            $imagen = $request->file('imagen');

            $nombreImagen = Str::uuid() . "." . $imagen->extension();
    
            $imagenServidor = Image::make($imagen);
            $imagenServidor->fit(1000, 1000);
    
            $imagenPath = public_path('perfiles') . '/' . $nombreImagen;
            $imagenServidor->save($imagenPath);
        }

        // Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario->email = $request->email;

        if($request->password) {
            $usuario->password = Hash::make($request->password);
            Auth::logoutOtherDevices($request->old_password);
            // auth()->logout();
        }
        
        $usuario->save();

        // Redireccionar
        return redirect()->route('posts.index', $usuario->username);
    }
}
