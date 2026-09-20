<?php
namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function login(LoginRequest $request): Response
    {
        if (! Auth::attempt($request->safe()->only(['email', 'password']) + ['active' => true])) {
            Log::notice('Login rejected', ['request_id' => $request->attributes->get('request_id')]);
            throw ValidationException::withMessages(['email' => ['The supplied credentials are invalid.']]);
        }
        $request->session()->regenerate();
        return response()->noContent();
    }
    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    }
}

