<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints relacionados à autenticação de usuários"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Realizar login",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="admin@admin"),
     *             @OA\Property(property="password", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'status' => true,
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Realizar logout",
     *     tags={"Autenticação"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        $user = auth()->user();
        return response()->json(['status' => true, 'user' => $user, 'message' => 'Logout realizado com sucesso']);
    }

    /**
     * @OA\Get(
     *     path="/validate-token",
     *     summary="Validar token JWT",
     *     tags={"Autenticação"},
     *     @OA\Response(
     *         response=200,
     *         description="Token válido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token válido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token inválido ou expirado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Token inválido")
     *         )
     *     )
     * )
     */
    public function validateToken(Request $request)
    {
        try {
            $token = JWTAuth::parseToken()->authenticate();
            return response()->json(['status' => true, 'message' => 'Token válido']);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token de autorização não encontrado'], 401);
        }
    }
}
