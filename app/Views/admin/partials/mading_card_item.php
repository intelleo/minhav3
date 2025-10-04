<?php
// Partial item kartu mading untuk admin menggunakan uimading_helper (border, badge, admin)
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
        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $mading['status'] === 'aktif' ? 'bg-green-100 text-green-800' : ($mading['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
          <?= ucfirst($mading['status']) ?>
        </span>
      </div>
      <p class="text-gray-600 text-sm mt-2"><?= mading_excerpt($mading['deskripsi'], 120) ?></p>
    </div>
    <div class="p-5 bg-gray-50 flex justify-between items-center">
      <div class="flex space-x-4 text-gray-500 text-sm">
        <span class="flex items-center"><i class="ri-eye-line mr-1"></i><?= number_format($mading['views']) ?></span>
        <span class="flex items-center"><i class="ri-heart-line mr-1"></i><?= number_format($mading['total_likes']) ?></span>
        <span class="flex items-center"><i class="ri-chat-3-line mr-1"></i><?= $mading['total_comments'] >= 1000 ? number_format($mading['total_comments'] / 1000, 1) . 'k' : $mading['total_comments'] ?></span>
      </div>
      <?= mading_admin_badge($mading['username']) ?>
    </div>
  </div>
</a>