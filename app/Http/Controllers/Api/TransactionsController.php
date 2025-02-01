<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTransaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/user/transaction/deposit",
     *     summary="Пополнение баланса",
     *     tags={"Транзакции"},
     *     description="Этот метод позволяет пользователю отправить запрос на пополнение баланса. Транзакция обрабатывается в фоновом режиме.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"amount"},
     *                 @OA\Property(
     *                     property="amount",
     *                     type="number",
     *                     format="float",
     *                     example=100.50,
     *                     description="Сумма пополнения (минимум 1)"
     *                 )
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Запрос на снятие успешно отправлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Флаг успешности запроса"),
     *             @OA\Property(property="message", type="string", example="Your diposit request is being processed.", description="Сообщение о результате операции")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Поле amount является обязательным.", description="Описание ошибки"),
     *             @OA\Property(property="errors", type="object", example={"amount": {"Поле amount является обязательным."}}, description="Детализированные ошибки валидации")
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
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user_id = Auth::user()->id;
        $amount = $request->post('amount');
        $type = 'deposit';

        ProcessTransaction::dispatch($user_id, $amount, $type);

        return response()->json([
            'success' => true,
            'message' => 'Your deposit request is being processed.'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/transaction/withdraw",
     *     summary="Снятие средств",
     *     tags={"Транзакции"},
     *     description="Этот метод позволяет пользователю отправить запрос на снятие средств. Транзакция обрабатывается в фоновом режиме.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"amount"},
     *                 @OA\Property(
     *                     property="amount",
     *                     type="number",
     *                     format="float",
     *                     example=100.50,
     *                     description="Сумма пополнения (минимум 1)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Запрос на снятие успешно отправлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Флаг успешности запроса"),
     *             @OA\Property(property="message", type="string", example="Your withdraw request is being processed.", description="Сообщение о результате операции")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Поле amount является обязательным.", description="Описание ошибки"),
     *             @OA\Property(property="errors", type="object", example={"amount": {"Поле amount является обязательным."}}, description="Детализированные ошибки валидации")
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

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user_id = Auth::user()->id;
        $amount = $request->post('amount');
        $type = 'withdraw';

        ProcessTransaction::dispatch($user_id, $amount, $type);

        return response()->json([
            'success' => true,
            'message' => 'Your withdraw request is being processed.'
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/user/transaction/history",
     *     summary="История транзакций",
     *     tags={"Транзакции"},
     *     description="Этот метод позволяет пользователю получить историю транзакций, с возможностью указания ограничения на количество записей.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page_limit",
     *         in="query",
     *         required=false,
     *         description="Ограничение на количество транзакций на странице (максимум 100)",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="История транзакций успешно получена",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Флаг успешности запроса"),
     *             @OA\Property(property="content", type="array", description="Список транзакций",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="deposit", description="Тип транзакции (например, депозит)"),
     *                     @OA\Property(property="amount", type="string", example="100.00", description="Сумма транзакции"),
     *                     @OA\Property(property="description", type="string", example="Deposit successful", description="Описание транзакции"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-31T18:32:08.000000Z", description="Дата и время создания"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-31T18:32:08.000000Z", description="Дата и время последнего обновления")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Поле page_limit должно быть числом.", description="Описание ошибки"),
     *             @OA\Property(property="errors", type="object", example={"page_limit": {"Поле page_limit должно быть числом."}}, description="Детализированные ошибки валидации")
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


    public function showHistory(Request $request, TransactionRepositoryInterface $transactionRepository)
    {
        $request->validate([
            'page_limit' => 'numeric|min:1|max:100'
        ]);

        $user_id = Auth::user()->id;
        $page_limit = $request->get('page_limit',20);

        $content = $transactionRepository->history($user_id, $page_limit);

        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }
}
