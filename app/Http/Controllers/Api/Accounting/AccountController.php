<?php

namespace App\Http\Controllers\Api\Accounting;

use App\DataAdapter\Accounting\AccountAdapter;
use App\Http\Controllers\Api\ApiController;
use App\Models\Accounting\Account;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends ApiController
{

    /**
     * @OA\Get(
     *      path="/api/account/{user_id}",
     *      tags={"Аккаунт"},
     *      security={ {"bearer": {} }},
     *      summary="Получить Аккаунт по id пользователя",
     *      description="Вернет Аккаунт по id",
     *      @OA\Parameter(
     *      description="ID Аккаунта",
     *      in="path",
     *      name="uid",
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
     *              @OA\Property(property="total", type="float", example=12.21),
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
    public function getAccount($userId, AccountAdapter $adapter)
    {
        $account = User::find($userId)->account;
        return $this->sendResponse(200, ['account' => $adapter->getModelData($account)]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     * @OA\Post(
     * path="/api/account/store",
     * summary="Установить начальное значение счета",
     * description="Установить начальное значение счета",
     * tags={"Аккаунт"},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"uid", "total"},
     *       @OA\Property(property="uid", type="int", example=11),
     *       @OA\Property(property="total", type="float", example=10000),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Вернет ошибку валидации, если поля не валидны или какие-то не отправлены",
     * )
     * )
     */
    public function setTotal(Request $request)
    {
        $account = new Account();
        $account->user_id = $request->all()['uid'];
        $account->total = $request->all()['total'];

        $account->save();
        return $this->sendResponse(201, ['account' => $account], 'success');
    }
}
