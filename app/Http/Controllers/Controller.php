<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Pet Shop API",
 *    version="1.0.0",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Get(
     * path="/api/v1/currency-converter/{currency}/{amount}",
     * summary="Convert currency",
     * description="Convert amount from currency to EURO",
     * operationId="convertCurrency",
     * tags={"currency-converter"},
     *     @OA\Parameter(
     *    description="the currency",
     *    in="path",
     *    name="currency",
     *    required=true,
     *    example="gbp",
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     *     @OA\Parameter(
     *    description="the amount to convert",
     *    in="path",
     *    name="amount",
     *    required=true,
     *    example=3250,
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Conversion successful",
     * @OA\JsonContent(
     *       @OA\Property(property="amount", type="number", example=1003.98),
     *       @OA\Property(property="rate", type="number", example=0.83665),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     */
    public function currencyConverter()
    {
    }
}
