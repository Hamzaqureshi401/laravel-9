<?php

namespace App\Utilities;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Redis;

class RedisHelper implements RedisHelperInterface
{
    public function storeRecentMessage(mixed $id, string $messageSubject, string $toEmailAddress): void
    {
        $key = "recent_message:{$id}";
        $data = ['subject' => $messageSubject, 'to' => $toEmailAddress];
        $redis = Redis::connection();
        \Log::debug("Stored data for key: {$key}");
        $redis->set($key, json_encode($data));
    }

     public function getRecentMessages(){

        $key = "recent_message:*";
        $redis = Redis::connection();
        $keys = $redis->keys($key);
        //dd($keys);
        $messages = [];
        foreach ($keys as $key) {
            $redis = Redis::connection();
            $data = $redis->get('recent_message:'.explode(':', $key)[1]);
            $message = json_decode($data, true);

            if ($message) {
                $messages[] = [
                    'key' => $key,
                    'subject' => $message['subject'],
                    'to' => $message['to'],
                ];
            }
        }
        return $messages;
    }
}
