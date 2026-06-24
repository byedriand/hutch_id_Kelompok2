<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Get role label
        $roleLabels = [
            'administrator' => 'Administrator',
            'staf_penjualan' => 'Staf Penjualan',
            'operator_gudang' => 'Operator Gudang',
        ];
        
        $roleLabel = $roleLabels[$user->role] ?? $user->role;
        
        // Get WhatsApp business number from config
        $whatsappBusinessNumber = config('services.fonnte.sender_number', '6281224360829');
        $whatsappBusinessNumberFormatted = $this->formatWhatsAppNumber($whatsappBusinessNumber);
        
        return view('user.profile', compact('user', 'roleLabel', 'whatsappBusinessNumber', 'whatsappBusinessNumberFormatted'));
    }

    /**
     * Format WhatsApp number for display
     */
    private function formatWhatsAppNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Format: 62812-2436-0829
        if (strlen($phone) >= 12) {
            return '+' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 3) . '-' . substr($phone, 5, 4) . '-' . substr($phone, 9);
        }
        
        return $phone;
    }

    /**
     * Delete the user account.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = Auth::user();
        Auth::logout();
        
        $user->delete();
        
        return redirect()->route('login')->with('success', 'Akun berhasil dihapus.');
    }
}
