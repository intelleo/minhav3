<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UCChatbot extends BaseController
{
    public function indexChatbot()
    {
        $data['title'] = 'Chatbot Minha';
        return view('user/user_chatbot', $data);
    }
}
