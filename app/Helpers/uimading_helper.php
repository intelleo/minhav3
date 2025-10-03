<?php

if (!function_exists('mading_category_badge')) {
  /**
   * Buat badge kategori dengan warna berbeda
   * 
   * @param string $category
   * @return string
   */
  function mading_category_badge(string $category): string
  {
    $category = strtolower($category);

    $colors = [
      'edukasi'     => 'bg-blue-50 text-blue-700 border-blue-200',
      'pengumuman'  => 'bg-green-50 text-green-700 border-green-200',
      'event'       => 'bg-orange-50 text-orange-700 border-orange-200',
      'berita'      => 'bg-pink-50 text-pink-700 border-pink-200',
    ];


    $icons = [
      'edukasi'     => 'ri-book-2-fill text-blue-700',
      'pengumuman'  => 'ri-news-fill text-green-700',
      'event'       => 'ri-calendar-event-fill text-orange-700',
      'berita'      => 'ri-newspaper-fill text-pink-700',
    ];

    $class = $colors[$category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
    $icon  = $icons[$category] ?? 'ri-article-fill';

    return '
        <span class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full font-medium border ' . $class . '">
            <i class="' . $icon . ' text-sm"></i>
            ' . ucfirst($category) . '
        </span>';
  }
}

// fungsi border card mading
// --------------------------------------------------------------------
if (!function_exists('mading_card_border')) {
  /**
   * Tentukan warna border kiri berdasarkan kategori
   * 
   * @param string $category
   * @return string
   */
  function mading_card_border(string $category): string
  {
    $category = strtolower($category);

    return match ($category) {
      'edukasi'     => 'border-blue-500',
      'pengumuman'  => 'border-green-500',
      'event'       => 'border-orange-500',
      'berita'      => 'border-pink-500',
      default       => 'border-gray-500',
    };
  }
}

// --------------------------------------------------------------------

if (!function_exists('mading_date_format')) {
  /**
   * Format tanggal mading
   * 
   * @param string $date
   * @return string
   */
  function mading_date_format(string $date): string
  {
    if (empty($date)) return '-';

    $timestamp = strtotime($date);
    return date('d M Y', $timestamp);
  }
}

// --------------------------------------------------------------------

if (!function_exists('mading_admin_badge')) {
  /**
   * Tampilkan badge admin
   * 
   * @param string $username
   * @return string
   */
  function mading_admin_badge(string $username = null): string
  {
    $name = $username ? esc($username) : 'Admin Tidak Diketahui';

    return '
        <div class="flex items-center mt-2">
            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                <i class="ri-shield-user-fill text-blue-600 text-xs"></i>
            </div>
            <span class="text-xs text-gray-600">Admin: ' . $name . '</span>
        </div>';
  }
}

// --------------------------------------------------------------------

if (!function_exists('mading_excerpt')) {
  /**
   * Potong deskripsi dengan batas kata
   * 
   * @param string $text
   * @param int $limit
   * @return string
   */
  function mading_excerpt(string $text, int $limit = 100): string
  {
    if (strlen($text) <= $limit) {
      return esc($text);
    }

    $excerpt = substr($text, 0, $limit);
    $lastSpace = strrpos($excerpt, ' ');

    return $lastSpace ? esc(substr($text, 0, $lastSpace)) . '...' : esc($excerpt) . '...';
  }

  // dashboard overview mading
  // --------------------------------------------------------------------

  if (!function_exists('mading_category_color')) {
    /**
     * Ambil warna kategori untuk background dan text
     * 
     * @param string $category
     * @param string $type bg|text
     * @return string
     */
    function mading_category_color(string $category, string $type = 'bg'): string
    {
      $colors = [
        'edukasi'     => ['#3B82F6', '#1D4ED8'],     // blue-500, blue-700
        'pengumuman'  => ['#10B981', '#059669'],     // green-500, green-700
        'event'       => ['#F59E0B', '#D97706'],     // orange-500, orange-700
        'berita'      => ['#EC4899', '#BE185D'],     // pink-500, pink-700
      ];

      $hex = $colors[strtolower($category)] ?? ['#6B7280', '#374151']; // gray

      return $type === 'bg' ? $hex[0] : $hex[1];
    }
  }

  // --------------------------------------------------------------------

  if (!function_exists('mading_category_icon')) {
    /**
     * Ambil ikon berdasarkan kategori
     * 
     * @param string $category
     * @return string
     */
    function mading_category_icon(string $category): string
    {
      return match (strtolower($category)) {
        'edukasi'     => 'ri-book-2-fill text-blue-700',
        'pengumuman'  => 'ri-news-fill text-green-700',
        'event'       => 'ri-calendar-event-fill text-orange-700',
        'berita'      => 'ri-newspaper-fill text-pink-700',
        default       => 'ri-article-fill'
      };
    }
  }

  // --------------------------------------------------------------------

  /**
   * Format waktu relatif yang lebih spesifik, contoh:
   * - "baru saja" (< 5 detik)
   * - "10 detik yang lalu"
   * - "1 menit yang lalu"
   * - "2 jam yang lalu"
   * - "Kemarin" (jika tanggalnya kemarin)
   * - "3 hari yang lalu" (maksimal 6 hari)
   * - "12 Mar 2025" (jika >= 7 hari)
   */
  function mading_time_ago(string $datetime): string
  {
    // Ubah string waktu ke timestamp Unix
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
      return '-'; // fallback jika format tidak valid
    }

    $now = time();
    $diff = max(0, $now - $timestamp); // selisih detik, minimal 0

    // Batasan waktu harian
    $todayStart = strtotime('today');
    $yesterdayStart = strtotime('yesterday');

    // 1) Detail untuk detik dan menit
    if ($diff < 5) {
      return 'baru saja';
    }
    if ($diff < 60) {
      $sec = $diff;
      return $sec . ' detik yang lalu';
    }
    if ($diff < 3600) { // < 1 jam
      $min = floor($diff / 60);
      return $min . ' menit yang lalu';
    }

    // 2) Jam dalam hari yang sama
    if ($timestamp >= $todayStart) {
      $hours = floor($diff / 3600);
      return $hours . ' jam yang lalu';
    }

    // 3) Kemarin
    if ($timestamp >= $yesterdayStart) {
      return 'Kemarin';
    }

    // 4) Hari lalu sampai maksimal 6 hari
    $days = floor($diff / 86400);
    if ($days < 7) {
      return $days . ' hari yang lalu';
    }

    // 5) Di atas 7 hari tampilkan tanggal lengkap
    return date('d M Y', $timestamp);
  }

  // hilight nama balasan komentar jadi biru
  if (!function_exists('highlight_mentions')) {
    /**
     * Highlight mention @Nama Pengguna menjadi biru
     * - Mendukung nama lengkap dengan spasi (misal: "@Budi Santoso")
     * - Hanya menyorot bagian mention, tidak mengubah teks lain
     * - Menggunakan regex Unicode dan callback agar aman dan presisi
     * 
     * @param string $text
     * @return string
     */
    function highlight_mentions(string $text): string
    {
      // Escape HTML agar aman dari XSS
      $text = esc($text);

      // Pola mention:
      // (^|\s)  : awal baris atau spasi (agar tidak nyangkut di tengah kata)
      // @        : simbol mention
      // (Nama    : minimal satu kata, huruf unicode di awal, lalu huruf/angka/underscore
      //   ( spasi Nama ) {0,6} : opsional hingga 6 kata tambahan, dipisah spasi
      // )\b      : batas kata di akhir agar tidak menyapu tanda baca berikutnya
      $pattern = '/(^|\s)@(\p{L}[\p{L}\p{N}_]+(?:\s+\p{L}[\p{L}\p{N}_]+){0,6})\b/u';

      return preg_replace_callback($pattern, function ($m) {
        // $m[1] = prefix (awal/spasi), $m[2] = nama lengkap setelah '@'
        $name = $m[2];
        return $m[1] . '<span class="text-blue-600 font-medium">@' . $name . '</span>';
      }, $text);
    }
  }
}
