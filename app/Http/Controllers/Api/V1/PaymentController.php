<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use PaytmWallet;


class PaymentController extends ApiController
{
    /**
     * Redirect the user to the Payment Gateway.
     *
     * @return Response
     */
    public function initiate($orderId)
    {
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
            'order' => $orderId,
            'user' => 2,
            'mobile_number' => 9990129661,
            'email' => 'mrgndrakr@gmail.com',
            'amount' => 100.0,
            'callback_url' => env('APP_URL').'/api/v1/payment/callback'
        ]);
        return $payment->receive();
    }


    /**
     * Obtain the payment information.
     *
     * @return Object
     */
    public function callback()
    {
        $transaction = PaytmWallet::with('receive');

        $res = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm

        if($transaction->isSuccessful()){
            echo '<h2 style="color:green">Success</h2>';
        }
        else if($transaction->isFailed()){
            echo '<h2 style="color:red">Error</h2>';
        }
        else if($transaction->isOpen()){
            echo "Processing" . "<br>";
        }
        echo "OrderId: " . $transaction->getOrderId() . "<br>"; // Get order id

        unset($res['CHECKSUMHASH']);
        unset($res['MID']);
        unset($res['TXNID']);

//        Cookie::queue(Cookie::make('payment_txn', json_encode($res), 2));

        return response('')->cookie(
            'payment_txn', json_encode($res), 2
        );
    }
}
