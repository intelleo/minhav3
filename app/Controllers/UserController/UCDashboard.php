<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MadingModel;
use App\Models\MadingLikeModel;
use App\Models\MadingCommentModel;
use App\Models\LayananModel;

class UCDashboard extends BaseController
{
    public function indexHome()
    {
        $madingModel = new MadingModel();
        $likeModel = new MadingLikeModel();
        $commentModel = new MadingCommentModel();
        $layananModel = new LayananModel();

        // Overview stats untuk user
        try {
            $stats = [
                'mading_active' => $madingModel->where('status', 'aktif')->countAllResults(),
                'my_likes' => ($likeModel->where('user_id', session('user_id'))->countAllResults()),
                'my_comments' => ($commentModel->where('user_id', session('user_id'))->countAllResults()),
                'chatbot_entries' => $layananModel->countAllResults(),
            ];
        } catch (\Exception $e) {
            $stats = [
                'mading_active' => 0,
                'my_likes' => 0,
                'my_comments' => 0,
                'chatbot_entries' => 0,
            ];
        }

        $latest = $madingModel->getLatest(3);
        // Enrich latest mading with likes/comments/views for better overview cards
        if (is_array($latest) && !empty($latest)) {
            $latest = $madingModel->enrichMadingDataPublic($latest);
        }
        $data['latestMading'] = $latest;
        $data['stats'] = $stats;
        $data['title'] = 'Dashboard';
        return view('user/user_dashboard', $data);
    }
}
