<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateNotificationSettingRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class NotificationSettingController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function edit()
    {       
        $notificationSetting = Auth::user()->notificationSetting ?? new NotificationSetting();
        $frequencies = NotificationSetting::FREQUENCIES_INTERVALS;
        return view('notification-settings.edit', compact('notificationSetting', 'frequencies'));
    }

    public function update(UpdateNotificationSettingRequest $request)
    {
        $notificationSetting = Auth::user()->notificationSetting;
        
        if (!$notificationSetting) {
            $notificationSetting = new NotificationSetting();
            $notificationSetting->user_id = auth()->id();
        }

        $this->authorize('update', $notificationSetting);

        $data = $request->validated();
        $notificationSetting->updateFromRequest($data);

        return redirect()->route('dashboard')
            ->with('toast', ['type' => 'success', 'message' => 'Configurações de notificação atualizadas com sucesso!', 'duration' => 5000]);
    }
}