<?php
// app/Controllers/Auth/LoginController.php
namespace App\Controllers\Auth;

use CodeIgniter\HTTP\RedirectResponse as RedirectResponse;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLogin;

class LoginController extends ShieldLogin
{
    // Show your custom login view
    public function loginView(): RedirectResponse | string
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'errors' => session()->getFlashdata('errors') ?? []
        ]);
    }

    // Handle login — supports email OR username
    public function loginAction(): RedirectResponse
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Detect if input is email or username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field     => $login,
            'password' => $password,
        ];

        $result = auth()->attempt($credentials, $remember);

        if (! $result->isOK()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['login' => 'Invalid credentials. Please try again.']);
        }

        return redirect()->to('/dashboard')->with('message', 'Welcome back! 📖');
    }
}