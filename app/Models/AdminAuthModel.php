<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminAuthModel extends Model
{
  protected $table            = 'auth_admin';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = false;

  protected $allowedFields    = ['username', 'password', 'created_at', 'updated_at'];

  protected $useTimestamps = true;
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';

  /**
   * Find admin by username.
   */
  public function findByUsername(string $username): ?array
  {
    $admin = $this->where('username', $username)->first();
    return $admin ?: null;
  }
}
