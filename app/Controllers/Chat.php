<?php

namespace App\Controllers;

use App\Models\ChatModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    protected function ensureLoggedIn()
    {
        $user = session()->get('user');
        if (!is_array($user) || empty($user['logged_in'])) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['error' => 'Not authenticated']);
        }
        return null;
    }

    public function fetch()
    {
        if ($resp = $this->ensureLoggedIn()) return $resp;

        $currentUser = session()->get('user');
        $otherUserId = (int) $this->request->getGet('otherUserId');
        $sinceId = $this->request->getGet('sinceId');
        $sinceId = $sinceId !== null ? (int) $sinceId : null;

        if ($otherUserId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['error' => 'Missing otherUserId']);
        }

        $model = new ChatModel();
        $messages = $model->fetchConversation((int) $currentUser['id'], $otherUserId, $sinceId);

        return $this->response->setJSON([
            'messages' => $messages,
            'lastId' => empty($messages) ? $sinceId : (int) end($messages)['id']
        ]);
    }

    public function send()
    {
        if ($resp = $this->ensureLoggedIn()) return $resp;

        $currentUser = session()->get('user');
        $receiverId = (int) $this->request->getPost('receiver_id');
        $message = trim((string) $this->request->getPost('message'));

        if ($receiverId <= 0 || $message === '') {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['error' => 'receiver_id and message required']);
        }

        $model = new ChatModel();
        $db = \Config\Database::connect();
        $receiver = $db->table('users')->select('role')->where('id', $receiverId)->get(1)->getRowArray();
        $receiverRole = (string) ($receiver['role'] ?? '');
        $senderRole = (string) ($currentUser['role'] ?? '');
        $data = [
            'sender_id' => (int) $currentUser['id'],
            'sender_role' => $senderRole,
            'receiver_id' => $receiverId,
            'receiver_role' => $receiverRole,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $id = $model->insert($data, true);

        return $this->response->setJSON([
            'success' => true,
            'id' => (int) $id,
            'created_at' => $data['created_at']
        ]);
    }
}

?>

