<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Mading extends BaseController
{
  public function index()
  {
    $data = [
      'title' => 'E-mading Management',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/mading_management', $data);
    }

    return view('admin/mading_management', $data);
  }
}
