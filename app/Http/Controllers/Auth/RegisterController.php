<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Normalización ligera (limpia XSS simples y espacios)
        $data['nombre'] = isset($data['nombre']) ? trim(strip_tags((string)$data['nombre'])) : null;
        $data['email']  = isset($data['email'])  ? mb_strtolower(trim((string)$data['email'])) : null;

        // Guardamos los datos normalizados para que create() reciba lo mismo
        request()->merge($data);

        return Validator::make($data, [
            'nombre' => ['required','string','min:2','max:120'],
            'email'  => ['required','string','email:rfc,dns','max:190', Rule::unique('usuarios','email')],
            'password' => [
                'required','confirmed',
                Password::min(8)->letters()->numbers()->mixedCase()->uncompromised()
            ],
        ], [
            'email.unique' => 'Este correo ya está registrado.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'nombre'        => trim($data['nombre']),
            'email'         => mb_strtolower($data['email']),
            'password_hash' => Hash::make($data['password']),
        ]);
    }
}
