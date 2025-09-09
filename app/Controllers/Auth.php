<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function login(): string
    {
        return view('login');
    }

    public function attempt(): RedirectResponse
    {
        $request = service('request');
        $session = session();

        $username = trim((string) $request->getPost('username'));
        $password = (string) $request->getPost('password');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            $session->set('user', [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'logged_in' => true,
            ]);
            // Role-based landing pages if desired; both go to /dashboard for now
            return redirect()->to('/dashboard');
        }

        $session->setFlashdata('error', 'Invalid username or password');
        return redirect()->back()->withInput();
    }

    public function logout(): RedirectResponse
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}


