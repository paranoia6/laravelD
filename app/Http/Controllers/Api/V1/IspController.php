<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Isp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IspController extends Controller
{
    public function getIsp()
    {
        //'5.217.133.1'
        //$_SERVER['REMOTE_ADDR']
        $ip = $_SERVER['REMOTE_ADDR'];
        $hostname = gethostbyaddr($ip);
        $token = "?token=35ffeb6559359c";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.ipinfo.io/lite/'.$hostname . $token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);

        $isp_name = 'نامشخص';
        $internet_type = 3;
        if (isset($response['asn'])) {
            $asName = $response['as_name'];
            $asn = $response ['asn'];
            $company = Isp::query()->where('asn', $asn)->orWhere('as_name', $asName)->first();

            if ($company) {
                $isp_name = $company->persian_name;
                $internet_type = $company->internet_type;
            }
        }
        return response()->json([
            'message' => "successful",
            'data'    => [
                'isp_name'      => $isp_name,
                'internet_type' => $internet_type
            ]
        ]);
    }
}
