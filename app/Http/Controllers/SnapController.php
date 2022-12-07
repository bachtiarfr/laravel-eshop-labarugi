<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Veritrans\Midtrans;

class SnapController extends Controller
{
    public function __construct()
    {   
        Midtrans::$serverKey = 'SB-Mid-server-eipVBl9D1YpmuIOuELOa-Sdl';
        //set is production to true for production mode
        Midtrans::$isProduction = false;
    }

    public function snap()
    {
        return view('snap_checkout');
    }

    public function token(Request $request)
    {
        // dd($request->all());
        // dd('masuk');
        error_log('masuk ke snap token dri ajax');
        $midtrans = new Midtrans;

        $transaction_details = array(
            'order_id'      => uniqid(),
            'gross_amount'  => (int)$request->dataPayment['order_total_price']
        );

        // Populate items
        $items = [
            array(
                'id'        => $request->id,
                'price'     => (int)$request->dataPayment['order_total_price'],
                'quantity'  => 1,
                // 'name'      => $request->dataPayment['product_name'],
                'name'      => 'Test',
            )
        ];

        // Populate customer's billing address
        $billing_address = array(
            'first_name'    => $request->dataPayment['first_name'],
            'last_name'     => $request->dataPayment['last_name'],
            'address'       => $request->dataPayment['address2'],
            'city'          => $request->dataPayment['address2'],
            'postal_code'   => $request->dataPayment['post_code'],
            'phone'         => $request->dataPayment['phone'],
            'country_code'  => 'IDN'
            );

        // Populate customer's shipping address
        $shipping_address = array(
            'first_name'    => $request->dataPayment['first_name'],
            'last_name'     => $request->dataPayment['last_name'],
            'address'       => $request->dataPayment['address2'],
            'city'          => $request->dataPayment['address2'],
            'postal_code'   => $request->dataPayment['post_code'],
            'phone'         => $request->dataPayment['phone'],
            'country_code'  => 'IDN'
            );

        // Populate customer's Info
        $customer_details = array(
            'first_name'      => $request->dataPayment['first_name'],
            'last_name'       => $request->dataPayment['last_name'],
            'email'           => $request->dataPayment['email'],
            'phone'           => $request->dataPayment['phone'],
            'billing_address' => $request->dataPayment['address2'],
            'shipping_address'=> $request->dataPayment['address2']
            );

        // Data yang akan dikirim untuk request redirect_url.
        $credit_card['secure'] = true;
        //ser save_card true to enable oneclick or 2click
        //$credit_card['save_card'] = true;

        $time = time();
        $custom_expiry = array(
            'start_time' => date("Y-m-d H:i:s O",$time),
            'unit'       => 'hour', 
            'duration'   => 2
        );
        
        $transaction_data = array(
            'transaction_details'=> $transaction_details,
            'item_details'       => $items,
            'customer_details'   => $customer_details,
            'credit_card'        => $credit_card,
            'expiry'             => $custom_expiry
        );
    
        try
        {
            $snap_token = $midtrans->getSnapToken($transaction_data);
            //return redirect($vtweb_url);
            echo $snap_token;
        } 
        catch (Exception $e) 
        {   
            return $e->getMessage;
        }
    }

    public function finish(Request $request)
    {
        // dd($request->all());
        // $result = $request->input('result_data');
        // $result = json_decode($result);
        // echo $result->status_message . '<br>';
        // echo 'RESULT <br><pre>';
        // var_dump($result);
        // echo '</pre>' ;
        return back();

    }

    public function notification()
    {
        $midtrans = new Midtrans;
        echo 'test notification handler';
        $json_result = file_get_contents('php://input');
        $result = json_decode($json_result);

        if($result){
        $notif = $midtrans->status($result->order_id);
        }

        error_log(print_r($result,TRUE));

        /*
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        if ($transaction == 'capture') {
          // For credit card transaction, we need to check whether transaction is challenge by FDS or not
          if ($type == 'credit_card'){
            if($fraud == 'challenge'){
              // TODO set payment status in merchant's database to 'Challenge by FDS'
              // TODO merchant should decide whether this transaction is authorized or not in MAP
              echo "Transaction order_id: " . $order_id ." is challenged by FDS";
              } 
              else {
              // TODO set payment status in merchant's database to 'Success'
              echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
              }
            }
          }
        else if ($transaction == 'settlement'){
          // TODO set payment status in merchant's database to 'Settlement'
          echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
          } 
          else if($transaction == 'pending'){
          // TODO set payment status in merchant's database to 'Pending'
          echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
          } 
          else if ($transaction == 'deny') {
          // TODO set payment status in merchant's database to 'Denied'
          echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        }*/
   
    }
}    