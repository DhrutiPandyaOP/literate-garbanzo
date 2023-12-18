<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            PhotoADKing                                             //
// File:             Utils.php                                              //
// Since:            20-september-2017                                      //
//                                                                          //
// Author:           Dipali Dhanani                                         //
// Email:            dipali.dhanani@optimumbrew.com                         //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use Config;
use DB;

class Utils extends Controller
{
    // get base url
    public function getBaseUrl()
    {

        return Config::get('constant.ACTIVATION_LINK_PATH');
    }

    // generate otp
    public function generateOTP()
    {
        $string = '0123456789';
        $string_shuffled = str_shuffle($string);
        $otp = substr($string_shuffled, 1, 4);

        return $otp;
    }

    // generate order number
    public function generateOrderNumber()
    {
        $date = date('Ymd');
        $order_number_fetch = DB::select('SELECT order_number FROM order_master ORDER BY id DESC LIMIT 1');
        if (count($order_number_fetch) == 0) {
            $counter = 1;
        } else {
            $last_counter = substr($order_number_fetch[0]->order_number, 8, strlen($order_number_fetch[0]->order_number));
            $counter = $last_counter + 1;
        }

        return $date.$counter;
    }

    // generate service number
    public function generateServiceNumber()
    {
        $date = date('Ymd');
        $service_number_fetch = DB::select('SELECT service_number FROM book_service ORDER BY id DESC LIMIT 1');
        if (count($service_number_fetch) == 0) {
            $counter = 1;
        } else {
            $last_counter = substr($service_number_fetch[0]->service_number, 8, strlen($service_number_fetch[0]->service_number));
            $counter = $last_counter + 1;
        }

        return $date.$counter;
    }

    // get interval in minutes from time stamp
    public function getInterval($end_at, $start_at)
    {
        return round(abs(strtotime($end_at) - strtotime($start_at)) / 60, 2);
    }
}
