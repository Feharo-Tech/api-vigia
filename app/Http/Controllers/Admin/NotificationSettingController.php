<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateNotificationSettingRequest;


class NotificationSettingController extends Controller
{
    public function edit()
    {       
        $notificationSetting = Auth::user()->notificationSetting ?? new NotificationSetting();
        $frequencies = NotificationSetting::FREQUENCIES_INTERVALS;
        return view('admin.notification-settings.edit', compact('notificationSetting', 'frequencies'));
    }

    public function update(UpdateNotificationSettingRequest $request)
    {
        $notificationSetting = Auth::user()->notificationSetting;
        
        if (!$notificationSetting) {
            $notificationSetting = new NotificationSetting();
            $notificationSetting->user_id = auth()->id();
        }

        $data = $request->validated();
        $data['email_notifications'] = $data['email_notifications'] ?? false;
        
        $notificationSetting->fill($data);
        $notificationSetting->save();

        return redirect()->route('dashboard')
            ->with('toast', ['type' => 'success', 'message' => 'Configurações de notificação atualizadas com sucesso!', 'duration' => 5000]);
    }
}