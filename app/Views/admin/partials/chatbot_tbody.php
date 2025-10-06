<?php if (empty($layanan)): ?>
  <tr>
    <td colspan="7" class="px-6 py-12 text-center">
      <div class="text-gray-500">
        <img src="<?= base_url('img/icon-chat.webp') ?>" alt="" width="40" class="block mx-auto">
        <p>Tidak ada layanan ditemukan</p>

      </div>
    </td>
  </tr>
<?php else: ?>
  <?php foreach ($layanan as $item): ?>
    <tr class="hover:bg-gray-50">

      <td class="px-6 py-4">
        <div class="text-sm font-medium text-gray-900"><?= esc($item['judul']) ?></div>
        <div class="text-sm text-gray-500">ID: <?= $item['id'] ?></div>
      </td>
      <td class="px-6 py-4">
        <div class="text-sm text-gray-900 max-w-xs">
          <div class="line-clamp-3 break-words"><?= esc($item['deskripsi']) ?></div>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <?php
        $kategoriColors = [
          'Akademik' => 'bg-blue-100 text-blue-800',
          'Administrasi' => 'bg-green-100 text-green-800',
          'Umum' => 'bg-purple-100 text-purple-800'
        ];
        $colorClass = $kategoriColors[$item['kategori']] ?? 'bg-gray-100 text-gray-800';
        ?>
        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $colorClass ?>">
          <?= esc($item['kategori']) ?>
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        <?= date('M j, Y', strtotime($item['created_at'])) ?>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex gap-2">
          <button class="text-blue-600 hover:text-blue-900 edit-layanan" data-id="<?= $item['id'] ?>">
            <i class="ri-edit-line"></i>
          </button>
          <button class="text-green-600 hover:text-green-900 view-layanan" data-id="<?= $item['id'] ?>">
            <i class="ri-eye-line"></i>
          </button>
          <button class="text-red-600 hover:text-red-900 delete-layanan" data-id="<?= $item['id'] ?>">
            <i class="ri-delete-bin-line"></i>
          </button>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>