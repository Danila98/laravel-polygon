<?php

namespace App\Http\Controllers\Api\Accounting;

use App\DataAdapter\Accounting\TransactionAdapter;
use App\Http\Controllers\Api\ApiController;
use App\Models\Accounting\Transaction;
use App\Repository\Accounting\TransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{

    protected TransactionRepository $transactionRepository;
    protected TransactionAdapter $transactionAdapter;

    public function __construct(TransactionRepository $transactionRepository, TransactionAdapter $transactionAdapter)
    {
        $this->transactionRepository = $transactionRepository;
        $this->transactionAdapter = $transactionAdapter;
    }
    /**
     * @OA\Get(
     *      path="/api/transaction",
     *      tags={"Транзакции"},
     *      security={ {"bearer": {} }},
     *      summary="Получить список Транзакций",
     *      description="Вернет список Транзакций",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                property="transactions",
     *                type="array",
     *                example={{
     *                  "id": 1,
     *                  "description": "Fanger",
     *                  "amount": 12312,
     *                  "type": "income",
     *                  "user": {
     *                      "email": "test@test.ru",
     *                      "name": "test",
     *                      "id": 1,
     *                      },
     *                   "category": {
     *                      "id": 1,
     *                      "name": "test",
     *                      "limit": 2323124
     *                       }
     *                },
     *                  {
     *                  "id": 2,
     *                  "description": "Fanger",
     *                  "amount": 12312,
     *                  "type": "income",
     *                  "user": {
     *                      "email": "test@test.ru",
     *                      "name": "test",
     *                      "id": 1,
     *                      },
     *                  "category": {
     *                      "id": 1,
     *                      "name": "test",
     *                      "limit": 2323124
     *                       }
     *                }},
     *                @OA\Items(

     *                ),
     *             ),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

    public function list(Request $request)
    {
        if ($request->get('category_id'))
        {
            $transactions = $this->transactionRepository->findByAuthUserAndCategory($request->get('category_id'));
        }else
        {
            $transactions = $this->transactionRepository->findByAuthUser();
        }

        return $this->sendResponse(200, ['transactions' => $this->transactionAdapter->getArrayModelData($transactions)]);
    }
    /**
     * @OA\Get(
     *      path="/api/transaction/{transaction_id}",
     *      tags={"Транзакции"},
     *      security={ {"bearer": {} }},
     *      summary="Получить Транзакцию по id",
     *      description="Вернет Транзакцию по id",
     *      @OA\Parameter(
     *      description="ID Транзакции",
     *      in="path",
     *      name="id",
     *      required=true,
     *      example="1",
     *      @OA\Schema(
     *          type="integer",
     *           format="int64"
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="int", example=1),
     *              @OA\Property(property="description", type="string", example="test"),
     *              @OA\Property(property="type", type="string", example="outcome"),
     *              @OA\Property(property="amount", type="int", example=123),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      )
     *     )
     */
    public function show(int $id)
    {
        if (!$transactions = Transaction::find($id))
        {
            return $this->sendError( 404, 'Not found');
        }

        return $this->sendResponse(200 ,[ 'transaction' => $this->transactionAdapter->getModelData($transactions)]);
    }
    /**
     * Создает Тразнакцию
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @OA\Post(
     * path="/api/transaction/add",
     * summary="Добавить Транзакцию",
     * description="Нужно передать все поля",
     * tags={"Транзакции"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"description","type", "amount", "category_id"},
     *       @OA\Property(property="description", type="string",  example="test"),
     *       @OA\Property(property="type", type="string",  example="income"),
     *       @OA\Property(property="amount", type="int", example=65.21),
     *       @OA\Property(property="category_id", type="int", example=2),
     *    ),
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Вернет ошибку валидации, если поля не валидны или какие-то не отправлены",
     * )
     * )
     */
    public function store(Request $request)
    {

        $request->validate([
            'description' => 'required',
            'category_id' => 'required',
            'amount' => 'required',
            'type' => 'required',
        ]);
        $request =  $request->all();
        $transaction = Transaction::create([
            'description' => $request['description'],
            'category_id' => $request['category_id'],
            'amount' => $request['amount'],
            'type' => $request['type'],
        ]);
        return $this->sendResponse(201, ['transaction' => $transaction]);
    }
    /**
     * Обновляет Транзакцию
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $transaction_id
     * @return \Illuminate\Http\JsonResponse
     * @OA\Put(
     * path="/api/transaction/update/{transaction_id}",
     * summary="Обновить Транзакцию",
     * description="Нужно передать все поля",
     * tags={"Транзакции"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"description","type", "amount", "category_id"},
     *       @OA\Property(property="description", type="string",  example="test"),
     *       @OA\Property(property="type", type="string",  example="income"),
     *       @OA\Property(property="amount", type="int", example=65.21),
     *       @OA\Property(property="category_id", type="int", example=2),
     *    ),
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Вернет ошибку валидации, если поля не валидны или какие-то не отправлены",
     * )
     * )
     */
    public function update(Request $request, int $transaction_id): JsonResponse
    {
        $request->validate([
            'description' => 'required|string',
            'category_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'type' => 'required|string',
        ]);
        $transaction = Transaction::find($transaction_id);
        $transaction->update($request->all());
        return $this->sendResponse(201, ['transaction' => $transaction]);
    }

    /**
     * Удаляет Категорию
     *
     * @param  int $transaction_id
     * @return \Illuminate\Http\JsonResponse
     * @OA\Delete(
     *      path="/api/transaction/delete/{transaction_id}",
     *      tags={"Транзакции"},
     *      security={ {"bearer": {} }},
     *      summary="Удалить Транзакцию по id",
     *      description="Вернет булевое значение",
     *      @OA\Parameter(
     *      description="ID категории",
     *      in="path",
     *      name="id",
     *      required=true,
     *      example="1",
     *      @OA\Schema(
     *          type="integer",
     *           format="int64"
     *        )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="bool", example=true),
     *          ),
     *       ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      )
     *     )
     */
    public function destroy(int $transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        $transaction->delete();
        return $this->sendResponse(201);
    }
}
