<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
  public function index()
  {
    $data = [
      'title' => 'Settings',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/settings', $data);
    }

    return view('admin/settings', $data);
  }
}
