<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Reports extends BaseController
{
  public function index()
  {
    $data = [
      'title' => 'Reports & Analytics',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/reports', $data);
    }

    return view('admin/reports', $data);
  }
}
