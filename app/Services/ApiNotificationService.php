<?php

namespace App\Services;

use App\Mail\ApiFailureNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ApiNotificationService
{
    public static function notify($api, $reason, $errorCount, $lastResponseTime, $lastStatusCode)
    {
        $user = $api->user;

        if (!$user->notificationSetting) {
            return;
        }

        if (!$user->notificationSetting->email_notifications) {
            return;
        }

        $lastNotificationTime = $user->notificationSetting->last_notification_sent_at;

        $notificationFrequency = $user->notificationSetting->notification_frequency;

        if ($lastNotificationTime && $lastNotificationTime->diffInMinutes(Carbon::now()) < $notificationFrequency) {
            return;
        }

        $user->notificationSetting->update([
            'last_notification_sent_at' => Carbon::now(),
        ]);

        $to = $user->notificationSetting->notification_email;

        Mail::to($to)->send(new ApiFailureNotification(
            $api,
            $reason,
            $errorCount,
            $lastResponseTime,
            $lastStatusCode
        ));
    }
}
