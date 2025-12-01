<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index()
    {
        $userModel = model('UserModel');
        $data = [
            'users' => $userModel->findAll(),
            'username' => session()->get('user')['username'] ?? '',
            'role' => session()->get('user')['role'] ?? ''
        ];
        // Only admin can access
        if ($data['role'] !== 'admin') {
            return redirect()->to('/dashboard');
        }
        return view('users/index', $data);
    }

    public function create()
    {
        $role = session()->get('user')['role'] ?? '';
        if ($role !== 'admin') {
            return redirect()->to('/dashboard');
        }
        return view('users/create');
    }

    public function store()
    {
        $role = session()->get('user')['role'] ?? '';
        if ($role !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,cashier]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = model('UserModel');
        $data = [
            'username' => $this->request->getPost('username'),
            'phone' => $this->request->getPost('phone'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role')
        ];

        if ($userModel->insert($data)) {
            return redirect()->to('/users')->with('success', 'User created successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }
    }

    public function edit($id)
    {
        $role = session()->get('user')['role'] ?? '';
        if ($role !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = model('UserModel');
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found');
        }

        return view('users/edit', ['user' => $user]);
    }

    public function update($id)
    {
        $role = session()->get('user')['role'] ?? '';
        if ($role !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = model('UserModel');
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$id}]",
            'phone' => 'required|min_length[10]|max_length[15]',
            'role' => 'required|in_list[admin,cashier]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role')
        ];

        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($userModel->update($id, $data)) {
            return redirect()->to('/users')->with('success', 'User updated successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }
    }

    public function delete($id)
    {
        $role = session()->get('user')['role'] ?? '';
        if ($role !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = model('UserModel');
        $userModel->delete($id);
        return redirect()->to('/users')->with('success', 'User deleted successfully');
    }
}
