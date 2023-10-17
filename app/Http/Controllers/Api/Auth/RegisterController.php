<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use Illuminate\Http\JsonResponse;
use App\UseCases\Auth\VerifyAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse //Register Method
    {
        $request->validate([
            'name' => 'required|string|max:50|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                Password::min(5)->letters()
            ]
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->email_verification_token = Str::uuid()->toString();

        $user->save();

        Mail::to($user->email)->send(new VerificationEmail($user));

        return response()->json([
            'success' => true,
            'message' => 'Please check your email , you email has been verified .',
        ]);
    }

    public function verify(int $id, string $hash): JsonResponse //Verify Method
    {
        $user = User::where('id', $id)->where('email_verification_token', $hash)->first();

        if ($user) {
            $user->markEmailAsVerified();

            return Redirect::to("http://localhost:3000/auth/login");
        } else {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 400);
        }
    }
}
