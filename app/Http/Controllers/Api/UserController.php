<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/user/info",
     *     summary="Информация о пользователе",
     *     tags={"Пользователь"},
     *     description="Этот метод позволяет пользователю получить информацию о своей учетной записи, включая баланс и другие данные.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Информация о пользователе успешно получена",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Флаг успешности запроса"),
     *             @OA\Property(property="content", type="object", description="Информация о пользователе",
     *                 @OA\Property(property="id", type="integer", example=1, description="ID пользователя"),
     *                 @OA\Property(property="name", type="string", example="Dr. Savion Stroman", description="Имя пользователя"),
     *                 @OA\Property(property="email", type="string", example="mr@test.com", description="Email пользователя"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-31T07:13:14.000000Z", description="Дата и время создания учетной записи"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-31T07:13:14.000000Z", description="Дата и время последнего обновления учетной записи"),
     *                 @OA\Property(property="balance", type="string", example="170.00", description="Баланс пользователя")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка аутентификации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.", description="Сообщение об ошибке аутентификации")
     *         )
     *     )
     * )
     */

    public function info()
    {
        $user_id = Auth::user()->id;
        $content = $this->userRepository->info($user_id);

        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }
}
