<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        // وضعیت تراکنش
        if ($request->State !== "OK") {
            return response("<h2>پرداخت ناموفق بود</h2><p>وضعیت تراکنش: {$request->State}</p>", 200);
        }

        // Verify تراکنش
        $verify = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://sep.shaparak.ir/onlinepg/verify', [
            "RefNum" => $request->RefNum,
            "TerminalId" => "31266886"
        ]);

        $verifyResult = $verify->json();

        if (!isset($verifyResult["ResultCode"]) || $verifyResult["ResultCode"] != 0) {
            return response("<h2>خطا در Verify تراکنش</h2><p>" . json_encode($verifyResult) . "</p>", 200);
        }

        // ذخیره سفارش
        $order = new Order();
        $order->product_id = 1; // ICDL
        $order->resnum = $request->ResNum;
        $order->refnum = $request->RefNum;
        $order->status = 'paid';
        $order->save();

        // نمایش رسید دیجیتال
        $transaction = $verifyResult["TransactionDetail"];
        return response("
            <h2>پرداخت موفق بود 🎉</h2>
            <p>رسید دیجیتال (RefNum): {$request->RefNum}</p>
            <p>RRN: {$transaction['RRN']}</p>
            <p>مبلغ: " . number_format($transaction['OrginalAmount']) . " تومان</p>
        ", 200);
    }
}
