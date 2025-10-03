<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MadingModel;

class UCDashboard extends BaseController
{
    public function indexHome()
    {
        $madingModel = new MadingModel();
        $data['latestMading'] = $madingModel->getLatest(3);
        $data['title'] = 'Dashboard';
        return view('user/user_dashboard', $data);
    }
}
