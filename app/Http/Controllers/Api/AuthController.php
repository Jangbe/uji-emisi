<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($valid->fails()) return response()->json([
            'meta' => [
                'status' => 422,
                'message' => $valid->getException()
            ],
            'data' => $valid->messages()->toArray()
        ]);

        $responseFailed = [
            'meta' => [
                'status' => 404,
                'message' => 'Username atau password salah'
            ],
            'data' => []
        ];
        $user = User::where(['username' => $request->username, 'user_kategori' => 'bengkel', 'is_admin' => 0])->first();
        if (is_null($user)) return response()->json($responseFailed);
        if (!password_verify($request->password, $user->password)) return response()->json($responseFailed);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'meta' => [
                'status' => 200,
                'message' => 'Masuk berhasil, selamat datang kembali'
            ],
            'data' => compact('user', 'token')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'meta' => [
                'status' => 200,
                'message' => 'Logout berhasil'
            ],
            'data' => []
        ]);
    }
}
