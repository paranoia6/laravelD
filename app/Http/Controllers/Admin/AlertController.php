<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::query()
            ->latest('priority')
            ->latest('id')
            ->paginate(20);

        return view('admin.alerts.index', compact('alerts'));
    }

    public function show(Alert $alert)
    {
        return view('admin.alerts.show', compact('alert'));
    }
}
