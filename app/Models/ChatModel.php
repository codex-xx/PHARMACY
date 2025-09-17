<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['sender_id', 'sender_role', 'receiver_id', 'receiver_role', 'message', 'created_at'];

    public function fetchConversation(int $userId, int $otherUserId, ?int $sinceId = null): array
    {
        $builder = $this->builder();
        $builder->groupStart()
            ->groupStart()
                ->where('sender_id', $userId)
                ->where('receiver_id', $otherUserId)
            ->groupEnd()
            ->orGroupStart()
                ->where('sender_id', $otherUserId)
                ->where('receiver_id', $userId)
            ->groupEnd()
        ->groupEnd()
        ->orderBy('id', 'ASC');

        if ($sinceId !== null) {
            $builder->where('id >', $sinceId);
        }

        return $builder->get()->getResultArray();
    }
}

?>

