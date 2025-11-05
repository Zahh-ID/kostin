<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(Request $request): View
    {
        $midtrans = config('services.midtrans');

        return view('admin.settings', [
            'appName' => config('app.name'),
            'appEnv' => config('app.env'),
            'appUrl' => config('app.url'),
            'mailDriver' => config('mail.default'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'midtrans' => [
                'is_production' => $midtrans['is_production'] ?? false,
                'server_key' => $midtrans['server_key'] ?? null,
                'client_key' => $midtrans['client_key'] ?? null,
            ],
        ]);
    }
}
