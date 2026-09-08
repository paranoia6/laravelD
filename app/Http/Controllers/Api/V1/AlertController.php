<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $buildNumber = (int) $request->header('X-App-Build', 0);

        $alerts = Alert::query()
            ->availableFor($buildNumber)
            ->orderByDesc('priority')
            ->get();

        $forceUpdate = $alerts
            ->firstWhere('type.value', 'force_update');

        $alerts = $alerts
            ->reject(fn (Alert $alert) => $alert->type->value === 'force_update')
            ->values();

        return response()->json([
            'message' => 'successful',
            'data' => [
                'force_update' => $forceUpdate,
                'alerts' => $alerts,
            ],
        ]);
    }
}
