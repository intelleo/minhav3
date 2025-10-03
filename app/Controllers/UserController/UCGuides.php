<?php

namespace App\Controllers\UserController;

use App\Controllers\BaseController;

class UCGuides extends BaseController
{
  public function index()
  {
    $data = [
      'title' => 'Panduan Minha',
    ];
    return view('user/user_panduan', $data);
  }
}
