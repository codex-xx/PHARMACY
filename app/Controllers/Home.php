<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $user = session()->get('user');
        if (is_array($user) && !empty($user['logged_in'])) {
            return redirect()->to('/dashboard');
        }
        return redirect()->to('/login');
    }
}
