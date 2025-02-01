<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API для биллинга",
 *     description="Этот API предоставляет систему биллинга, в которой пользователи могут зарегистрироваться, проверять свой баланс, выполнять транзакции (пополнение/снятие) и просматривать историю транзакций. Все операции проходят проверку, исключая возможность отрицательного баланса.",
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Локальный сервер для разработки API биллинга."
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Используйте Bearer Token для аутентификации. Все защищенные маршруты требуют действительного токена после входа пользователя в систему."
 * )
 */

class AuthController extends Controller
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Регистрация нового пользователя",
     *     tags={"Аутентификация"},
     *     description="Этот метод позволяет зарегистрировать нового пользователя в системе.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Иван Иванов",
     *                     description="Имя пользователя (от 3 до 20 символов)"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="ivan@example.com",
     *                     description="Уникальный email пользователя"
     *                 ),
     *                 @OA\Property(
     *                      property="password",
     *                     type="string",
     *                     format="password",
     *                     example="password123",
     *                     description="Пароль (минимум 8 символов)"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
     *                     example="password123",
     *                     description="Подтверждение пароля"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная регистрация",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Флаг успешности запроса"),
     *             @OA\Property(property="messaged", type="string", example="Registered", description="Сообщение о результате операции"),
     *             @OA\Property(property="token", type="string", example="3|QRkUnUf2syjUmOyvSiO8p6hjaZaeOnHJPuwgkunL7f07d3b2", description="Токен аутентификации")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Поле email является обязательным.", description="Описание ошибки"),
     *             @OA\Property(property="errors", type="object", example={"email": {"Поле email является обязательным."}}, description="Детализированные ошибки валидации")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:20|',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = $this->userRepository->register($request->only([
            'name',
            'email',
            'password'
        ]));

        return response()->json([
            'success' => true,
            'messaged' => 'Registered',
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Вход пользователя в систему",
     *     tags={"Аутентификация"},
     *     description="Этот метод используется для входа пользователя в систему с использованием email и пароля.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                  @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="ivan@example.com",
     *                     description="Email пользователя"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                      example="password123",
     *                     description="Пароль пользователя (минимум 8 символов)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вход",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Флаг успешности запроса"),
     *             @OA\Property(property="messaged", type="string", example="Logged In", description="Сообщение о результате операции"),
     *             @OA\Property(property="token", type="string", example="3|QRkUnUf2syjUmOyvSiO8p6hjaZaeOnHJPuwgkunL7f07d3b2", description="Токен аутентификации")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка аутентификации",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Флаг успешности запроса"),
     *             @OA\Property(property="messaged", type="string", example="The email or password is incorrect", description="Сообщение об ошибке")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Поле email является обязательным.", description="Описание ошибки"),
     *             @OA\Property(property="errors", type="object", example={"email": {"Поле email является обязательным."}}, description="Детализированные ошибки валидации")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        $user = $this->userRepository->login($request->only([
            'email',
            'password'
        ]));

        if ($user == null) {
            return response()->json([
                'success' => false,
                'messaged' => 'The email or password is incorrect',
            ],401);
        }

        return response()->json([
            'success' => true,
            'messaged' => 'Logged In',
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }
}
