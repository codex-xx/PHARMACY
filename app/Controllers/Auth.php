<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

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

    public function register(): string
    {
        return view('register');
    }

    public function registerPost(): RedirectResponse
    {
        $request = service('request');
        $session = session();

        $username = trim((string) $request->getPost('username'));
        $email = trim((string) $request->getPost('email'));
        $password = (string) $request->getPost('password');
        $passwordConfirm = (string) $request->getPost('password_confirm');
        $phone = trim((string) $request->getPost('phone'));

        if ($password !== $passwordConfirm) {
            $session->setFlashdata('error', 'Passwords do not match');
            return redirect()->back()->withInput();
        }

        $userModel = new \App\Models\UserModel();
        $existing = $userModel->where('username', $username)->orWhere('email', $email)->first();
        if ($existing) {
            $session->setFlashdata('error', 'Username or email already exists');
            return redirect()->back()->withInput();
        }

        // Validate phone (basic): must be 11 digits and start with 09
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (!preg_match('/^09\d{9}$/', $cleanPhone)) {
            $session->setFlashdata('error', 'Invalid phone number. Use 11 digits starting with 09.');
            return redirect()->back()->withInput();
        }

        // Generate verification code and store pending registration in session
        $code = random_int(100000, 999999);
        $pending = [
            'username' => $username,
            'email' => $email ?: null,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $cleanPhone,
            'code' => (string) $code,
            'expires_at' => date('Y-m-d H:i:s', time() + 60 * 10), // 10 minutes
        ];

        $session->set('pending_registration', $pending);

        // Send SMS with verification code
        try {
            $sms = service('sms');
            $message = "Your Pharmacy verification code is: {$code}. It will expire in 10 minutes.";
            $sendResult = $sms->send($cleanPhone, $message);

            if (!$sendResult['success']) {
                // keep pending but notify user
                $session->setFlashdata('error', 'Unable to send verification code via SMS: ' . ($sendResult['message'] ?? ''));
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'SMS sending failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        return redirect()->to('/register/verify');
    }

    /**
     * Show verification form for pending registration
     */
    public function verifyRegister(): string
    {
        $session = session();
        $pending = $session->get('pending_registration');
        if (!$pending) {
            $session->setFlashdata('error', 'No pending registration found. Please register first.');
            return redirect()->to('/register');
        }

        return view('register_verify', ['phone' => $pending['phone']]);
    }

    /**
     * Handle verification code submission and create account
     */
    public function verifyRegisterPost(): RedirectResponse
    {
        $request = service('request');
        $session = session();

        $code = trim((string) $request->getPost('code'));
        $pending = $session->get('pending_registration');
        if (!$pending) {
            $session->setFlashdata('error', 'No pending registration found. Please register first.');
            return redirect()->to('/register');
        }

        if ($pending['expires_at'] < date('Y-m-d H:i:s')) {
            $session->remove('pending_registration');
            $session->setFlashdata('error', 'Verification code expired. Please register again.');
            return redirect()->to('/register');
        }

        if (!hash_equals((string) $pending['code'], (string) $code)) {
            $session->setFlashdata('error', 'Invalid verification code.');
            return redirect()->back()->withInput();
        }

        // Create user
        $userModel = new \App\Models\UserModel();
        $existing = $userModel->where('username', $pending['username'])->orWhere('email', $pending['email'])->first();
        if ($existing) {
            $session->remove('pending_registration');
            $session->setFlashdata('error', 'Username or email already exists');
            return redirect()->to('/register');
        }

        $userModel->insert([
            'username' => $pending['username'],
            'email' => $pending['email'] ?: null,
            'password_hash' => $pending['password_hash'],
            'role' => 'staff',
            'phone' => $pending['phone'],
        ]);

        $session->remove('pending_registration');
        $session->setFlashdata('success', 'Account created successfully. You can now log in.');
        return redirect()->to('/login');
    }

    public function forgotPassword(): string
    {
        return view('forgot_password');
    }

    public function forgotPasswordPost(): RedirectResponse
    {
        $request = service('request');
        $session = session();
        $email = trim((string) $request->getPost('email'));

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();
        if (!$user) {
            $session->setFlashdata('error', 'If the email exists, a reset link has been sent.');
            return redirect()->back()->withInput();
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 60 * 30); // 30 minutes
        $userModel->update($user['id'], [
            'password_reset_token' => $token,
            'password_reset_expires_at' => $expiresAt,
        ]);

        $resetUrl = site_url('reset-password/' . $token);
        $subject = 'Password Reset Request - Pharmacy POS';
        
        $body = "
<html>
  <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
      <h2 style='color: #007bff; text-align: center;'>Pharmacy POS</h2>
      <hr style='border: none; border-top: 1px solid #ddd;'>
      
      <h3>Password Reset Request</h3>
      
      <p>Hello <strong>{$user['username']}</strong>,</p>
      
      <p>We received a request to reset your password. Click the button below to reset it:</p>
      
      <div style='text-align: center; margin: 30px 0;'>
        <a href='{$resetUrl}' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
      </div>
      
      <p>Or copy and paste this link in your browser:</p>
      <p style='word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 3px;'>{$resetUrl}</p>
      
      <p><strong>Note:</strong> This link will expire in 30 minutes for security reasons.</p>
      
      <p>If you did not request this password reset, please ignore this email.</p>
      
      <hr style='border: none; border-top: 1px solid #ddd;'>
      <p style='font-size: 12px; color: #666; text-align: center;'>
        Pharmacy POS &copy; 2025. All rights reserved.
      </p>
    </div>
  </body>
</html>
        ";

        $sent = $this->sendEmail($email, $subject, $body, true);
        if (!$sent) {
            $session->setFlashdata('error', 'Unable to send reset email. Please contact support.');
            return redirect()->back()->withInput();
        }

        $session->setFlashdata('success', 'If the email exists, a reset link has been sent.');
        return redirect()->to('/login');
    }

    public function validateResetToken(string $token): RedirectResponse
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('password_reset_token', $token)
            ->where('password_reset_expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        
        if (!$user) {
            session()->setFlashdata('error', 'Invalid or expired reset link.');
            return redirect()->to('/login');
        }
        
        // Store token in session
        session()->set('reset_token', $token);
        
        // Redirect to clean URL
        return redirect()->to('/reset-password');
    }

    public function resetPassword(): string
    {
        $session = session();
        $token = $session->get('reset_token');
        
        if (!$token) {
            $session->setFlashdata('error', 'No valid reset token found. Please request a new password reset.');
            return redirect()->to('/login')->send();
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('password_reset_token', $token)
            ->where('password_reset_expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        if (!$user) {
            $session->setFlashdata('error', 'Invalid or expired reset token.');
            $session->remove('reset_token');
            return redirect()->to('/login')->send();
        }
        return view('reset_password', ['token' => $token]);
    }

    public function resetPasswordPost(): RedirectResponse
    {
        $request = service('request');
        $session = session();
        $token = $session->get('reset_token');
        $password = (string) $request->getPost('password');
        $passwordConfirm = (string) $request->getPost('password_confirm');
        
        if (!$token) {
            $session->setFlashdata('error', 'Invalid reset token. Please request a new password reset.');
            return redirect()->to('/login');
        }
        
        if ($password !== $passwordConfirm) {
            $session->setFlashdata('error', 'Passwords do not match');
            return redirect()->back();
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('password_reset_token', $token)
            ->where('password_reset_expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        if (!$user) {
            $session->setFlashdata('error', 'Invalid or expired reset token.');
            $session->remove('reset_token');
            return redirect()->to('/login');
        }

        $userModel->update($user['id'], [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
        
        // Clear the session token
        $session->remove('reset_token');
        
        $session->setFlashdata('success', 'Password updated. You can now log in.');
        return redirect()->to('/login');
    }

    private function sendEmail(string $toEmail, string $subject, string $body, bool $isHtml = false): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = (string) env('SMTP_HOST', '');
            $mail->SMTPAuth = true;
            $mail->Username = (string) env('SMTP_USER', '');
            $mail->Password = (string) env('SMTP_PASS', '');
            $mail->SMTPSecure = (string) env('SMTP_SECURE', PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port = (int) env('SMTP_PORT', 587);

            $fromEmail = (string) env('MAIL_FROM', 'no-reply@example.com');
            $fromName = (string) env('MAIL_FROM_NAME', 'Pharmacy POS');

            if (!$mail->Host || !$mail->Username || !$mail->Password) {
                // SMTP not configured
                log_message('error', 'SMTP not configured in .env');
                return false;
            }

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;
            if (!$isHtml) {
                $mail->AltBody = $body;
            }
            return $mail->send();
        } catch (PHPMailerException $e) {
            log_message('error', 'PHPMailer error: ' . $e->getMessage());
            return false;
        }
    }
}


