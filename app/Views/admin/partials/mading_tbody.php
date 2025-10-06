<?php if (!empty($mading)): ?>
  <?php foreach ($mading as $row): ?>
    <tr class="border-t">
      <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800"><?= esc($row['judul']) ?></td>
      <td class="px-4 py-3"><?= ucfirst($row['category'] ?? '-') ?></td>
      <td class="px-4 py-3">
        <span class="px-2 py-1 text-xs font-medium rounded-full <?= ($row['status'] === 'aktif' ? 'bg-green-100 text-green-800' : ($row['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) ?>"><?= ucfirst($row['status']) ?></span>
      </td>
      <td class="px-4 py-3 text-gray-600 text-xs">
        <?= esc($row['tgl_mulai'] ?: '-') ?> s/d <?= esc($row['tgl_akhir'] ?: '-') ?>
      </td>
      <td class="px-4 py-3 text-gray-600 text-xs"><?= esc($row['created_at'] ?? '-') ?></td>
      <td class="px-4 py-3">
        <?php if (!empty($row['file']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $row['file'])): ?>
          <img src="<?= base_url($row['file']) ?>"
            alt="<?= esc($row['judul']) ?>"
            class="w-16 h-12 object-cover rounded border"
            loading="lazy"
            onerror="this.style.display='none'">
        <?php else: ?>
          <span class="text-gray-400 text-xs">-</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr>
    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
  </tr>
<?php endif; ?>