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
        $address = "http://ip-api.com/php/" . $hostname;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $address,
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
        $resp = unserialize($response);

        $ispName = 'نامشخص2';
        $internetType = 3;
        if (isset($resp['isp'])) {
            $isp = $resp['isp'];
            $as = $resp ['as'];
            $company = Isp::query()->where('name', $as)->orWhere('isp', $isp)->first();

            if ($company) {
                $ispName = $company->persian_name;
                $internetType = $company->internet_type;
            }
        }

        return response()->json([
            'message' => "successful",
            'data'    => [
                'isp_name'      => $ispName,
                'internet_type' => $internetType
            ]
        ]);
    }
}
