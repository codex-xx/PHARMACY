<?php

namespace App\Controllers;

use App\Models\UserModel;

class Settings extends BaseController
{
    public function index()
    {
        if ($resp = $this->requireRole(['admin'])) return $resp;

        $user = $this->currentUser();

        return view('settings/index', [
            'user' => $user,
            'currentTheme' => session()->get('theme') ?? 'light',
        ]);
    }

    public function updateProfile()
    {
        $user = $this->currentUser();
        $userModel = new UserModel();

        $rules = [
            'username' => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$user['id']}]",
            'phone' => 'required|min_length[10]|max_length[15]',
            'current_password' => [
                'rules' => 'required',
                'errors' => ['required' => 'Current password is required to make changes']
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify current password
        if (empty($user['password_hash']) || !password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('errors', ['current_password' => 'Incorrect current password']);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'phone' => $this->request->getPost('phone'),
        ];

        // If new password provided, hash it
        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($userModel->update($user['id'], $data)) {
            // Update session with new username
            $session = session();
            $session->set('user', array_merge($user, ['username' => $data['username']]));

            return redirect()->back()->with('success', 'Profile updated successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }
    }

    public function updateTheme()
    {
        $theme = $this->request->getPost('theme');

        if (in_array($theme, ['light', 'dark'])) {
            session()->set('theme', $theme);
        }

        // Return JSON response for AJAX
        return $this->response->setJSON(['success' => true, 'theme' => $theme]);
    }
}
