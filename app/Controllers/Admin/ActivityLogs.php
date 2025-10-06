<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ActivityLogs extends BaseController
{
  use \CodeIgniter\API\ResponseTrait;

  private function getActivityLogs(): array
  {
    $db = \Config\Database::connect();

    // Filters
    $req = service('request');
    $type = (string) ($req->getGet('type') ?? '');
    $from = (string) ($req->getGet('from') ?? '');
    $to   = (string) ($req->getGet('to') ?? '');
    $page = max(1, (int) ($req->getGet('page') ?? 1));
    $perPage = max(1, (int) ($req->getGet('per_page') ?? 10));

    $items = [];

    $ranges = function ($builder, $dateCol) use ($from, $to) {
      if (!empty($from)) {
        $builder->where("$dateCol >=", $from);
      }
      if (!empty($to)) {
        $builder->where("$dateCol <=", $to);
      }
    };

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

    if (in_array($type, ['user', 'mading', 'comment'], true)) {
      $items = array_values(array_filter($items, fn($it) => $it['type'] === $type));
    }

    usort($items, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

    $total = count($items);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    $pageItems = array_slice($items, $offset, $perPage);

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
    $logs = $this->getActivityLogs();

    $data = [
      'title' => 'Activity Logs',
      'admin' => [
        'id' => session('admin_id'),
        'username' => session('admin_username'),
      ],
      'logs' => $logs['items'],
      'pagination' => $logs['pagination'],
      'filters' => $logs['filters'],
    ];

    if (service('request')->isAJAX()) {
      return view('admin/activity_logs', $data);
    }

    return view('admin/activity_logs', $data);
  }
}
