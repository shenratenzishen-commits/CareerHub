<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ── Static users (always available) ──────────────────────
    private function staticUsers(): array
    {
        return [
            ['id'=>1,'email'=>'superadmin@careerhub.com','password'=>'super123', 'name'=>'Super Admin',   'role'=>'superadmin','status'=>'active','verified'=>true],
            ['id'=>2,'email'=>'admin1@careerhub.com',    'password'=>'admin123', 'name'=>'Admin One',     'role'=>'admin',     'status'=>'active','verified'=>true],
            ['id'=>3,'email'=>'student@careerhub.com',   'password'=>'password', 'name'=>'Juan dela Cruz','role'=>'student',   'status'=>'active','verified'=>true],
            ['id'=>4,'email'=>'employer@careerhub.com',  'password'=>'password', 'name'=>'HR Manager',    'role'=>'employer',  'status'=>'active','verified'=>true],
        ];
    }

    // ── Show Login ────────────────────────────────────────────
    public function showLogin()
    {
        return view('auth.login');
    }

    // ── Show Register ─────────────────────────────────────────
    public function showRegister()
    {
        return view('auth.register');
    }

    // ── Login ─────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        $allUsers = array_merge($this->staticUsers(), session('registered_users', []));

        $user = collect($allUsers)->first(fn($u) =>
            $u['email'] === $request->email && $u['password'] === $request->password
        );

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (($user['status'] ?? 'active') === 'banned') {
            return back()->withErrors(['email' => 'Your account has been banned. Contact support.'])->withInput();
        }

        // Email verification removed: allow immediate login for all users
        session(['user' => $user]);
        // OTP verification has been removed — allow immediate login for all roles

        return match($user['role']) {
            'superadmin' => redirect()->route('superadmin.dashboard')->with('success', 'Welcome, Super Admin!'),
            'admin'      => redirect()->route('admin.dashboard')->with('success', 'Welcome, ' . $user['name'] . '!'),
            default      => redirect()->route('dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!'),
        };
    }

    // ── Register ──────────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|min:2',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:student,employer',
        ]);

        // Check duplicate email
        $allUsers = array_merge($this->staticUsers(), session('registered_users', []));
        if (collect($allUsers)->firstWhere('email', $request->email)) {
            return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
        }

        $allIds = array_column($allUsers, 'id');
        $newId  = count($allIds) ? max($allIds) + 1 : 10;

        // Save user as verified (verification disabled)
        $users   = session('registered_users', []);
        $users[] = [
            'id'         => $newId,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => $request->password,
            'role'       => $request->role,
            'status'     => 'active',
            'verified'   => true,
            'created_at' => now()->format('M d, Y'),
        ];
        session(['registered_users' => $users]);

        return redirect()->route('login')
            ->with('success', 'Account created! You may now login.');
    }

    // ── Show Verify Notice ────────────────────────────────────
    public function showVerifyNotice()
    {
        $email = session('pending_verify_email');
        return view('auth.verify.notice', compact('email'));
    }

    // ── Verify Email via Token ────────────────────────────────
    public function verifyEmail(Request $request, string $token)
    {
        $tokens = session('verify_tokens', []);

        if (!isset($tokens[$token])) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid or expired verification link.']);
        }

        $data = $tokens[$token];

        if (now()->timestamp > $data['expires_at']) {
            unset($tokens[$token]);
            session(['verify_tokens' => $tokens]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Verification link has expired. Please register again.']);
        }

        // Mark user as verified
        $users = session('registered_users', []);
        foreach ($users as &$u) {
            if ($u['email'] === $data['email']) {
                $u['verified'] = true;
            }
        }
        session(['registered_users' => $users]);

        // Remove used token
        unset($tokens[$token]);
        session(['verify_tokens' => $tokens]);
        session()->forget('pending_verify_email');

        // Auto-login
        $user = collect($users)->firstWhere('email', $data['email']);
        if ($user) {
            session(['user' => $user]);
        }

        return redirect()->route('dashboard')
            ->with('success', '✅ Email verified successfully! Welcome to CareerHub.');
    }

    // ── Resend Verification ───────────────────────────────────
    public function resendVerification(Request $request)
    {
        $email = session('pending_verify_email') ?? $request->email;

        if (!$email) {
            return back()->withErrors(['email' => 'No email found. Please register again.']);
        }

        $users = session('registered_users', []);
        $user  = collect($users)->firstWhere('email', $email);

        if (!$user) {
            return back()->withErrors(['email' => 'Account not found.']);
        }

        if ($user['verified'] ?? false) {
            return redirect()->route('login')->with('success', 'Your email is already verified. Please login.');
        }

        // Generate new token
        $token  = Str::random(64);
        $tokens = session('verify_tokens', []);
        // Remove old tokens for this email
        foreach ($tokens as $t => $d) {
            if ($d['email'] === $email) unset($tokens[$t]);
        }
        $tokens[$token] = ['email' => $email, 'expires_at' => now()->addHours(24)->timestamp];
        session(['verify_tokens' => $tokens]);
        session(['pending_verify_email' => $email]);

        $verifyUrl = route('verify.email', ['token' => $token]);
        $this->sendVerificationEmail($email, $user['name'], $verifyUrl);

        return back()->with('success', 'Verification email resent! Check your inbox.');
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout()
    {
        session()->forget(['user', 'saved_jobs', 'applied_jobs', 'saved_internships', 'applied_internships', 'pending_verify_email']);
        return redirect()->route('landing');
    }

    // ── Send Verification Email ───────────────────────────────
    private function sendVerificationEmail(string $email, string $name, string $url): void
    {
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($email, $name, $url) {
                $message->to($email, $name)
                    ->subject('Verify Your CareerHub Email Address')
                    ->html("
                        <div style='font-family:Inter,sans-serif;max-width:520px;margin:0 auto;background:#f8fafc;padding:32px;border-radius:16px'>
                            <div style='text-align:center;margin-bottom:24px'>
                                <div style='background:#4f46e5;color:#fff;width:48px;height:48px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px'>✉</div>
                                <h2 style='color:#1e293b;margin:0;font-size:22px'>Verify Your Email</h2>
                            </div>
                            <div style='background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.06)'>
                                <p style='color:#475569;margin-top:0'>Hi <strong>{$name}</strong>,</p>
                                <p style='color:#475569'>Welcome to <strong>CareerHub</strong>! Please verify your email address to activate your account.</p>
                                <div style='text-align:center;margin:28px 0'>
                                    <a href='{$url}' style='background:#4f46e5;color:#fff;padding:14px 36px;border-radius:50px;text-decoration:none;font-weight:600;font-size:15px;display:inline-block'>
                                        ✅ Verify Email Address
                                    </a>
                                </div>
                                <p style='color:#94a3b8;font-size:13px'>This link expires in <strong>24 hours</strong>. If you did not create an account, ignore this email.</p>
                                <hr style='border:none;border-top:1px solid #e2e8f0;margin:20px 0'>
                                <p style='color:#94a3b8;font-size:12px;margin:0'>Or copy this link: <br><a href='{$url}' style='color:#4f46e5;word-break:break-all'>{$url}</a></p>
                            </div>
                            <p style='text-align:center;color:#94a3b8;font-size:12px;margin-top:20px'>&copy; " . date('Y') . " CareerHub. All rights reserved.</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            // Mail failed — log it but don't crash the app
            \Illuminate\Support\Facades\Log::error('Verification email failed: ' . $e->getMessage());
        }
    }
}
