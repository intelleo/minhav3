<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
  public function getActivityLogs(): array
  {
    $db = \Config\Database::connect();

    // Filters
    $type = (string) ($this->request->getGet('type') ?? ''); // user|mading|comment|''(all)
    $from = (string) ($this->request->getGet('from') ?? '');
    $to   = (string) ($this->request->getGet('to') ?? '');
    $page = max(1, (int) ($this->request->getGet('page') ?? 1));
    $perPage = max(1, (int) ($this->request->getGet('per_page') ?? 10));

    $items = [];

    // Build queries per type
    $ranges = function ($builder, $dateCol) use ($from, $to) {
      if (!empty($from)) {
        $builder->where("$dateCol >=", $from);
      }
      if (!empty($to)) {
        $builder->where("$dateCol <=", $to);
      }
    };

    $fetch = function () use ($db, $ranges) {
      $items = [];

      // Users
      $u = $db->table('user_auth');
      $ranges($u, 'created_at');
      $users = $u->select('id, namalengkap, npm, created_at')->get()->getResultArray();
      foreach ($users as $row) {
        $items[] = [
          'type' => 'user',
          'title' => 'User baru terdaftar',
          'detail' => ($row['namalengkap'] ?? '') !== '' ? $row['namalengkap'] : (($row['npm'] ?? '') !== '' ? $row['npm'] : ('User #' . ($row['id'] ?? '-'))),
          'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
        ];
      }

      // Mading
      $m = $db->table('mading_online');
      $ranges($m, 'created_at');
      $mading = $m->select('id, judul, created_at')->get()->getResultArray();
      foreach ($mading as $row) {
        $items[] = [
          'type' => 'mading',
          'title' => 'Mading dipublikasikan',
          'detail' => $row['judul'],
          'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
        ];
      }

      // Komentar
      $c = $db->table('mading_comments');
      $ranges($c, 'created_at');
      $comments = $c->select('id, isi_komentar, created_at')->get()->getResultArray();
      foreach ($comments as $row) {
        $items[] = [
          'type' => 'comment',
          'title' => 'Komentar baru',
          'detail' => mb_strimwidth((string)($row['isi_komentar'] ?? ''), 0, 80, '...'),
          'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
        ];
      }

      return $items;
    };

    // Get and filter by type
    $all = $fetch();
    if (in_array($type, ['user', 'mading', 'comment'], true)) {
      $all = array_values(array_filter($all, function ($it) use ($type) {
        return $it['type'] === $type;
      }));
    }

    // Sort desc
    usort($all, function ($a, $b) {
      return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });

    $total = count($all);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    $pageItems = array_slice($all, $offset, $perPage);

    return [
      'items' => $pageItems,
      'pagination' => [
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'query_key_page' => 'page',
        'query_key_perpage' => 'per_page',
      ],
      'filters' => [
        'type' => $type,
        'from' => $from,
        'to' => $to,
      ]
    ];
  }

  public function index()
  {
    // Build activity logs
    $logs = $this->getActivityLogs();

    $data = [
      'title' => 'Settings',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'logs' => $logs['items'],
      'pagination' => $logs['pagination'],
      'filters' => $logs['filters'],
    ];

    // Check if AJAX request
    if ($this->request->isAJAX()) {
      // Return full layout for SPA router to parse
      return view('admin/settings', $data);
    }

    return view('admin/settings', $data);
  }
}
