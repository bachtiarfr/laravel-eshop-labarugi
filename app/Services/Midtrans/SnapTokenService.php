<?php
 
namespace App\Services\Midtrans;
 
use Midtrans\Snap;
 
class SnapTokenService extends Midtrans
{
    public function getSnapToken($params)
    {
        $snapToken = Snap::getSnapToken($params);
 
        return $snapToken;
    }
}