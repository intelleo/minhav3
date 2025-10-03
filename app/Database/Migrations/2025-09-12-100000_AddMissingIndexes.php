<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingIndexes extends Migration
{
  public function up()
  {
    // Index untuk minha_layanan_informasi
    $this->db->query("ALTER TABLE minha_layanan_informasi ADD INDEX idx_kategori (kategori)");
    $this->db->query("ALTER TABLE minha_layanan_informasi ADD INDEX idx_created_at (created_at)");
    $this->db->query("ALTER TABLE minha_layanan_informasi ADD INDEX idx_judul (judul)");
    $this->db->query("ALTER TABLE minha_layanan_informasi ADD FULLTEXT idx_search (judul, deskripsi)");

    // Index untuk minha_mading_online
    $this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_status (status)");
    $this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_category (category)");
    $this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_created_at (created_at)");
    $this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_views (views)");
    $this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_status_category (status, category)");
    $this->db->query("ALTER TABLE minha_mading_online ADD INDEX idx_tgl_range (tgl_mulai, tgl_akhir)");
    $this->db->query("ALTER TABLE minha_mading_online ADD FULLTEXT idx_search (judul, deskripsi)");

    // Index untuk minha_mading_comments
    $this->db->query("ALTER TABLE minha_mading_comments ADD INDEX idx_created_at (created_at)");
    $this->db->query("ALTER TABLE minha_mading_comments ADD INDEX idx_mading_created (mading_id, created_at)");
    $this->db->query("ALTER TABLE minha_mading_comments ADD INDEX idx_mading_parent (mading_id, parent_id)");
    $this->db->query("ALTER TABLE minha_mading_comments ADD INDEX idx_user_created (user_id, created_at)");

    // Index untuk minha_mading_likes
    $this->db->query("ALTER TABLE minha_mading_likes ADD INDEX idx_user_id (user_id)");
    $this->db->query("ALTER TABLE minha_mading_likes ADD INDEX idx_created_at (created_at)");

    // Index untuk minha_user_auth
    $this->db->query("ALTER TABLE minha_user_auth ADD INDEX idx_created_at (created_at)");
    $this->db->query("ALTER TABLE minha_user_auth ADD INDEX idx_status_jurusan (status, jurusan)");

    // Index untuk minha_auth_admin
    $this->db->query("ALTER TABLE minha_auth_admin ADD INDEX idx_created_at (created_at)");
  }

  public function down()
  {
    // Drop indexes untuk minha_layanan_informasi
    $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_kategori");
    $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_created_at");
    $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_judul");
    $this->db->query("ALTER TABLE minha_layanan_informasi DROP INDEX idx_search");

    // Drop indexes untuk minha_mading_online
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_status");
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_category");
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_created_at");
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_views");
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_status_category");
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_tgl_range");
    $this->db->query("ALTER TABLE minha_mading_online DROP INDEX idx_search");

    // Drop indexes untuk minha_mading_comments
    $this->db->query("ALTER TABLE minha_mading_comments DROP INDEX idx_created_at");
    $this->db->query("ALTER TABLE minha_mading_comments DROP INDEX idx_mading_created");
    $this->db->query("ALTER TABLE minha_mading_comments DROP INDEX idx_mading_parent");
    $this->db->query("ALTER TABLE minha_mading_comments DROP INDEX idx_user_created");

    // Drop indexes untuk minha_mading_likes
    $this->db->query("ALTER TABLE minha_mading_likes DROP INDEX idx_user_id");
    $this->db->query("ALTER TABLE minha_mading_likes DROP INDEX idx_created_at");

    // Drop indexes untuk minha_user_auth
    $this->db->query("ALTER TABLE minha_user_auth DROP INDEX idx_created_at");
    $this->db->query("ALTER TABLE minha_user_auth DROP INDEX idx_status_jurusan");

    // Drop indexes untuk minha_auth_admin
    $this->db->query("ALTER TABLE minha_auth_admin DROP INDEX idx_created_at");
  }
}
