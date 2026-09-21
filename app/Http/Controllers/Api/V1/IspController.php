<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Isp;
use App\Models\IspLog;

class IspController extends Controller
{
    public function getIsp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $ispName = 'نامشخص';
        $internetType = 3;

        $log = IspLog::where('ip', $ip)->first();

        if ($log) {
            $company = Isp::query()
                ->where('asn', $log->asn)
                ->orWhere('as_name', $log->as_name)
                ->first();

            if ($company) {
                $ispName = $company->persian_name;
                $internetType = $company->internet_type;
            }

            return response()->json([
                'message' => 'successful',
                'data' => [
                    'isp_name' => $ispName,
                    'internet_type' => $internetType,
                ],
            ]);
        }

        $token = '?token=35ffeb6559359c';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.ipinfo.io/lite/' . $ip . $token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            curl_close($curl);

            return response()->json([
                'message' => 'successful',
                'data' => [
                    'isp_name' => 'نامشخص',
                    'internet_type' => 3,
                ],
            ]);
        }

        curl_close($curl);

        $response = json_decode($response, true);

        if (!isset($response['asn'])) {
            return response()->json([
                'message' => 'successful',
                'data' => [
                    'isp_name' => 'نامشخص',
                    'internet_type' => 3,
                ],
            ]);
        }

        IspLog::updateOrCreate(
            [
                'ip' => $response['ip'] ?? $ip,
            ],
            [
                'asn' => $response['asn'],
                'as_name' => $response['as_name'] ?? null,
            ]
        );

        $company = Isp::query()
            ->where('asn', $response['asn'])
            ->orWhere('as_name', $response['as_name'] ?? '')
            ->first();

        if ($company) {
            $ispName = $company->persian_name;
            $internetType = $company->internet_type;
        }

        return response()->json([
            'message' => 'successful',
            'data' => [
                'isp_name' => $ispName,
                'internet_type' => $internetType,
            ],
        ]);
    }
}
