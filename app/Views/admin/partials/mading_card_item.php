<?php
// Partial item kartu mading menggunakan uimading_helper (border, badge, admin)
// Param: $mading (array required)
?>
<a href="<?= site_url('Admin/Mading/detail/' . $mading['id']) ?>" class="block">
  <div class="bg-white rounded-xl shadow-sm border-l-4 <?= mading_card_border($mading['category']) ?> overflow-hidden transition-all duration-300 hover:shadow-md hover:translate-y-[-5px] cursor-pointer">
    <div class="p-5 border-b border-gray-100">
      <div class="flex justify-between items-start mb-3 max-lg:flex-col max-lg:items-start">
        <h3 class="text-lg font-semibold text-gray-800"><?= esc($mading['judul']) ?></h3>
        <span class="text-sm text-gray-500 flex items-center">
          <i class="ri-calendar-2-line text-gray-500 mr-1"></i>
          <?= mading_date_format($mading['tgl_mulai']) ?>
        </span>
      </div>
      <div class="flex items-center flex-wrap gap-2 mb-3">
        <?= mading_category_badge($mading['category']) ?>

      </div>
      <p class="text-gray-600 text-sm mt-2"><?= mading_excerpt($mading['deskripsi'], 120) ?></p>

      <!-- Gambar jika ada -->
      <?php if (!empty($mading['file']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $mading['file'])): ?>
        <div class="mt-3">
          <img src="<?= base_url($mading['file']) ?>"
            alt="<?= esc($mading['judul']) ?>"
            class="w-full h-48 object-cover rounded-lg"
            loading="lazy"
            onerror="this.style.display='none'">
        </div>
      <?php endif; ?>
    </div>
    <div class="p-5 bg-gray-50 flex justify-between items-center">
      <div class="flex space-x-4 text-gray-500 text-sm">
        <span class="flex items-center"><i class="ri-eye-line mr-1"></i><?= number_format($mading['views']) ?></span>
        <!-- Like Button -->
        <?php $userLiked = (new \App\Models\MadingLikeModel())
          ->where('mading_id', $mading['id'])
          ->where('user_id', session('admin_id'))
          ->first() !== null; ?>
        <button
          type="button"
          id="like-btn"
          class="flex items-center gap-1 text-gray-600 hover:text-red-600  cursor-pointer transition-colors duration-200"
          data-liked="<?= $userLiked ? '1' : '0' ?>"
          onclick="toggleLike(<?= $mading['id'] ?>)">
          <i id="like-icon" class="<?= $userLiked ? 'ri-heart-fill text-red-600' : 'ri-heart-line text-gray-600' ?> text-lg"></i>
          <span id="like-count"><?= number_format($mading['total_likes']) ?></span>
        </button>
        <span class="flex items-center"><i class="ri-chat-3-line mr-1"></i><?= $mading['total_comments'] >= 1000 ? number_format($mading['total_comments'] / 1000, 1) . 'k' : $mading['total_comments'] ?></span>
      </div>
      <?= mading_admin_badge($mading['username']) ?>
    </div>
  </div>
</a>