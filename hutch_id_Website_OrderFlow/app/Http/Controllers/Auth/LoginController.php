<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate role selection
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'required|in:administrator,pemilik_umkm,staf_penjualan,operator_gudang',
        ], [
            'role.required' => 'Silakan pilih role terlebih dahulu.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password harus diisi.',
        ]);

        // Check if user exists and role matches
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ]);
        }

        // Verify role matches
        if ($user->role !== $request->role) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'role' => 'Role tidak sesuai dengan akun ' . $request->email . '. Akun ini adalah: ' . $this->getRoleLabel($user->role),
                ]);
        }

        // Attempt to authenticate
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form with an error message.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get role label for display.
     *
     * @param string $role
     * @return string
     */
    private function getRoleLabel($role)
    {
        $labels = [
            'administrator' => 'Administrator',
            'pemilik_umkm' => 'Pemilik UMKM',
            'staf_penjualan' => 'Staf Penjualan',
            'operator_gudang' => 'Operator Gudang',
        ];
        
        return $labels[$role] ?? $role;
    }
}
