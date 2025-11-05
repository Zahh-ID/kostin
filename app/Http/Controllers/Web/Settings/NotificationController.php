<?php

namespace App\Http\Controllers\Web\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('settings.notifications', [
            'user' => $user,
            'preferences' => [
                'email' => $user->getAttribute('notify_email') ?? true,
                'whatsapp' => $user->getAttribute('notify_whatsapp') ?? false,
                'timezone' => $user->getAttribute('timezone') ?? config('app.timezone'),
                'language' => $user->getAttribute('locale') ?? app()->getLocale(),
            ],
        ]);
    }
}
