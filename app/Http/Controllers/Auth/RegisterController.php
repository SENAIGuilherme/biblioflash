<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        // Remove formatação do CPF antes da validação
        if ($request->has('cpf') && $request->cpf) {
            $request->merge([
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf)
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', 'min:8', 'string'],
            'cpf' => 'nullable|string|size:11|unique:users,cpf',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'data_nascimento' => 'nullable|date|before:today',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'endereco' => $request->endereco,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'cep' => $request->cep,
            'data_nascimento' => $request->data_nascimento,
            'tipo' => 'cliente', // Usuários registrados são clientes por padrão
            'ativo' => true,
            'ultimo_acesso' => now(),
        ]);

        // Log da atividade
        ActivityLog::logCreate($user);
        ActivityLog::logActivity('register', $user, [], [], 'Usuário se registrou no sistema');

        // Login automático após registro
        Auth::login($user);

        return redirect('/')->with('success', 'Conta criada com sucesso! Bem-vindo ao BiblioFlash!');
    }
}