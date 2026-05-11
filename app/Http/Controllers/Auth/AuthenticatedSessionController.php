<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|string',
            'taken_at' => 'required|integer',
        ]);

        // Enforce "taken now"
        if (abs(now()->timestamp - $request->taken_at) > 60) {
            throw ValidationException::withMessages([
                'email' => 'Photo must be taken live'
            ]);
        }

        // Decode photo
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo);
        $image = base64_decode($image);

        // Compress if larger than 100kb
        if (strlen($image) > 102400) {
            $imgRes = @imagecreatefromstring($image);
            if ($imgRes !== false) {
                $quality = 90;
                $compressedImage = $image;
                while (strlen($compressedImage) > 102400 && $quality >= 10) {
                    ob_start();
                    imagejpeg($imgRes, null, $quality);
                    $compressedImage = ob_get_clean();
                    $quality -= 10;
                }
                $image = $compressedImage;
            }
        }

        $path = 'login_photos/' . uniqid() . '.jpg';
        Storage::disk('private')->put($path, $image);

        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            return back()->withErrors([
                'password' => '⚠ Invalid credentials',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Audit log
        Auth::user()->loginAudits()->create([
            'branch_id' => env('BRANCH_ID'),
            'photo_path' => $path,
            'photo_taken_at' => date('Y-m-d H:i:s', $request->taken_at),
            'type' => 'in',
        ]);

        return redirect()->intended(route('pos', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        $user = Auth::user();

        if ($user) {
            $user->loginAudits()->create([
                'branch_id' => env('BRANCH_ID'),
                'type' => 'out',
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
