<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Modelos\Usuario;

class LoginController extends Controller
{
    public function login(){
        return view('login.inicio');
    }

    public function acceder(Request $request){

        Validator::make($request->all(), [
            'usuario' => 'required|min:7|exists:usuario,nombre',
            'contrasena' => 'required|min:8',
            'g-recaptcha-response' => 'required|captcha',
        ],[
            'usuario.required' => 'Debe ingresar el usuario',
            'usuario.min' => 'El usuario debe ser mínimo 7 caracteres',
            'usuario.exists' => 'El usuario no existe',
            'contrasena.required' => 'Debe ingresar la contraseña',
            'contrasena.min' => 'la contraseña debe tener mínimo 8 caracteres',
            'g-recaptcha-response.required' => 'El captcha es obligatorio',
            'g-recaptcha-response.captach' => 'Haga clic en el captcha mostrado'
        ])->validate();

        $usuario = Usuario::where('nombre',$request->usuario)->first();

        if(Hash::check($request->contrasena,$usuario->contrasena)){
            session([
                'logeado' => true,
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'trash' => $usuario->trash,
                'id_rol' => $usuario->id_rol,
                'rol' => $usuario->rol->nombre,
            ]);
            return redirect('/');
        }else if($usuario->trash){
            $this->logout();
        }
    }

    public function logout(){
        session::flush();
        DB::disconnect();
        return redirect('login');
    }
}
