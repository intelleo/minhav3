<?php if (!empty($mading)): ?>
  <?php foreach ($mading as $row): ?>
    <tr class="border-t border-gray-200">
      <td class="px-4 py-3  font-medium text-gray-800"><?= esc($row['judul']) ?></td>
      <td class="px-4 py-3"><?= ucfirst($row['category'] ?? '-') ?></td>
      <td class="px-4 py-3">
        <span class="px-2 py-1 text-xs font-medium rounded-full <?= ($row['status'] === 'aktif' ? 'bg-green-100 text-green-800' : ($row['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) ?>"><?= ucfirst($row['status']) ?></span>
      </td>
      <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
        <?= esc($row['tgl_mulai'] ?: '-') ?> s/d <?= esc($row['tgl_akhir'] ?: '-') ?>
      </td>
      <td class="px-4 py-3 text-gray-600 text-xs"><?= esc($row['created_at'] ?? '-') ?></td>
      <td class="px-4 py-3">
        <?php if (!empty($row['file']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $row['file'])): ?>
          <img src="<?= base_url($row['file']) ?>"
            alt="<?= esc($row['judul']) ?>"
            class="w-16 h-12 object-cover rounded border border-gray-200"
            loading="lazy"
            onerror="this.style.display='none'">
        <?php else: ?>
          <span class="text-gray-400 text-xs">-</span>
        <?php endif; ?>
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
        <div class="flex gap-2">
          <button onclick="updateMadingStatus(<?= $row['id'] ?>, '<?= $row['status'] == 'aktif' ? 'nonaktif' : 'aktif' ?>')"
            class="text-blue-600 hover:text-blue-900"
            title="<?= $row['status'] == 'aktif' ? 'Deactivate' : 'Activate' ?>">
            <i class="ri-<?= $row['status'] == 'aktif' ? 'eye-off' : 'eye' ?>-line"></i>
          </button>
          <button class="text-green-600 hover:text-green-900" title="Edit"
            data-action="edit-mading"
            data-mading-id="<?= $row['id'] ?>"
            data-judul="<?= esc($row['judul']) ?>"
            data-category="<?= esc($row['category']) ?>"
            data-deskripsi="<?= esc($row['deskripsi']) ?>"
            data-tgl-mulai="<?= esc($row['tgl_mulai']) ?>"
            data-tgl-akhir="<?= esc($row['tgl_akhir']) ?>"
            data-status="<?= esc($row['status']) ?>"
            data-file="<?= esc($row['file'] ?? '') ?>">
            <i class="ri-edit-2-line"></i>
          </button>
          <button onclick="deleteMading(<?= $row['id'] ?>)" class="text-red-600 hover:text-red-900" title="Delete">
            <i class="ri-delete-bin-line"></i>
          </button>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr>
    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
  </tr>
<?php endif; ?>