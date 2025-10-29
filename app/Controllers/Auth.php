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
            // Block login until phone is verified
            if (empty($user['phone_verified_at'])) {
                $session->set('pending_phone_verify_user_id', $user['id']);
                $session->setFlashdata('error', 'Please verify your phone number before logging in.');
                return redirect()->to('/verify-phone');
            }
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
        $phone = trim((string) $request->getPost('phone'));
        $password = (string) $request->getPost('password');
        $passwordConfirm = (string) $request->getPost('password_confirm');

        if ($password !== $passwordConfirm) {
            $session->setFlashdata('error', 'Passwords do not match');
            return redirect()->back()->withInput();
        }

        $userModel = new \App\Models\UserModel();
        $existing = $userModel->where('username', $username)->orWhere('email', $email)->orWhere('phone', $phone)->first();
        if ($existing) {
            $session->setFlashdata('error', 'Username, email, or phone already exists');
            return redirect()->back()->withInput();
        }

        $verificationCode = (string) random_int(100000, 999999);

        $userId = $userModel->insert([
            'username' => $username,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'staff',
            'phone_verification_code' => $verificationCode,
            'phone_verified_at' => null,
        ], true);

        // Send SMS (placeholder logs) and email the verification code
        $this->sendSms($phone, "Your verification code is: {$verificationCode}");
        if ($email) {
            $this->sendEmail(
                $email,
                'Verify your phone number',
                "Use this code to verify your phone number: {$verificationCode}"
            );
        }

        $session->set('pending_phone_verify_user_id', $userId);
        $session->setFlashdata('success', 'Account created. Enter the code sent to your phone to verify.');
        return redirect()->to('/verify-phone');
    }

    public function verifyPhone(): string
    {
        return view('verify_phone');
    }

    public function verifyPhonePost(): RedirectResponse
    {
        $request = service('request');
        $session = session();
        $userId = (int) ($session->get('pending_phone_verify_user_id') ?? 0);
        $code = trim((string) $request->getPost('code'));

        if (!$userId) {
            $session->setFlashdata('error', 'No phone verification session found.');
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/login');
        }

        if (($user['phone_verification_code'] ?? '') !== $code) {
            $session->setFlashdata('error', 'Invalid verification code.');
            return redirect()->back();
        }

        $userModel->update($userId, [
            'phone_verification_code' => null,
            'phone_verified_at' => date('Y-m-d H:i:s'),
        ]);

        $session->remove('pending_phone_verify_user_id');
        $session->setFlashdata('success', 'Phone verified. You can now log in.');
        return redirect()->to('/login');
    }

    public function resendVerificationCode(): RedirectResponse
    {
        $session = session();
        $userId = (int) ($session->get('pending_phone_verify_user_id') ?? 0);
        if (!$userId) {
            $session->setFlashdata('error', 'No verification session found.');
            return redirect()->to('/login');
        }
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/login');
        }

        $verificationCode = (string) random_int(100000, 999999);
        $userModel->update($userId, ['phone_verification_code' => $verificationCode]);
        $this->sendSms($user['phone'] ?? null, "Your verification code is: {$verificationCode}");
        if (!empty($user['email'])) {
            $this->sendEmail(
                $user['email'],
                'Verify your phone number',
                "Use this code to verify your phone number: {$verificationCode}"
            );
        }
        $session->setFlashdata('success', 'Verification code resent.');
        return redirect()->to('/verify-phone');
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
        $subject = 'Password Reset Request';
        $body = 'Click the link to reset your password: ' . $resetUrl;

        $sent = $this->sendEmail($email, $subject, $body);
        if (!$sent) {
            $session->setFlashdata('error', 'Unable to send reset email. Please contact support.');
            return redirect()->back()->withInput();
        }

        $session->setFlashdata('success', 'If the email exists, a reset link has been sent.');
        return redirect()->to('/login');
    }

    public function resetPassword(string $token): string
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('password_reset_token', $token)
            ->where('password_reset_expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        if (!$user) {
            session()->setFlashdata('error', 'Invalid or expired reset token.');
            return redirect()->to('/login');
        }
        return view('reset_password', ['token' => $token]);
    }

    public function resetPasswordPost(string $token): RedirectResponse
    {
        $request = service('request');
        $session = session();
        $password = (string) $request->getPost('password');
        $passwordConfirm = (string) $request->getPost('password_confirm');
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
            return redirect()->to('/login');
        }

        $userModel->update($user['id'], [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
        $session->setFlashdata('success', 'Password updated. You can now log in.');
        return redirect()->to('/login');
    }

    private function sendSms(?string $phone, string $message): void
    {
        if (!$phone) {
            return;
        }
        log_message('info', 'SMS to ' . $phone . ': ' . $message);
    }

    private function sendEmail(string $toEmail, string $subject, string $body): bool
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
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $body;
            return $mail->send();
        } catch (PHPMailerException $e) {
            log_message('error', 'PHPMailer error: ' . $e->getMessage());
            return false;
        }
    }
}


