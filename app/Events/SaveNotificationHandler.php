<?php

namespace App\Events;

use App\Eloquent\Notification;

class SaveNotificationHandler
{
    public function handle($data)
    {
        app(Notification::class)->create([
            'user_id' => $data['current_user_id'],
            'target_id' => $data['target_id'],
            'type' => $data['type'],
        ]);
    }
}
