<?php if (!empty($users)): ?>
  <?php foreach ($users as $user): ?>
    <tr class="hover:bg-gray-50">

      <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
          <div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center overflow-hidden">
            <?php
            $foto = $user['foto_profil'] ?? '';
            $nama = $user['namalengkap'] ?? 'User';
            $words = preg_split('/\s+/', $nama);
            $initials = '';
            if ($words && count($words) > 0) {
              $initials .= mb_strtoupper(mb_substr($words[0], 0, 1));
              if (isset($words[1])) {
                $initials .= mb_strtoupper(mb_substr($words[1], 0, 1));
              }
            }

            // Normalisasi URL foto
            $fotoUrl = '';
            if (!empty($foto)) {
              if (preg_match('/^https?:\/\//i', $foto)) {
                $fotoUrl = $foto;
              } else {
                $fotoUrl = base_url(ltrim($foto, '/'));
              }
            }
            ?>

            <?php if (!empty($fotoUrl)): ?>
              <?= img_tag($fotoUrl, 'Profile Avatar', [
                'class' => 'w-full h-full object-cover rounded-full',
                'onerror' => "this.style.display='none'; this.parentNode.innerHTML = '<span class=\'w-full h-full bg-blue-100 text-blue-600 font-semibold flex items-center justify-center text-sm\'>" . $initials . "</span>';"
              ]) ?>
            <?php else: ?>
              <span class="w-full h-full bg-blue-100 text-blue-600 font-semibold flex items-center justify-center text-sm">
                <?= esc($initials ?: 'U') ?>
              </span>
            <?php endif; ?>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-900"><?= esc($user['namalengkap']) ?></div>
            <div class="text-sm text-gray-500">ID: <?= $user['id'] ?></div>
          </div>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($user['npm']) ?></td>
      <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
          <?= ucwords(str_replace('_', ' ', $user['jurusan'])) ?>
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <?php
        $statusColors = [
          'aktif' => 'bg-green-100 text-green-800',
          'pending' => 'bg-yellow-100 text-yellow-800',
          'nonaktif' => 'bg-red-100 text-red-800'
        ];
        $statusColor = $statusColors[$user['status']] ?? 'bg-gray-100 text-gray-800';
        ?>
        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusColor ?>">
          <?= ucfirst($user['status']) ?>
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        <?= date('M d, Y', strtotime($user['created_at'])) ?>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex gap-2">
          <button onclick="updateUserStatus(<?= $user['id'] ?>, '<?= $user['status'] == 'aktif' ? 'nonaktif' : 'aktif' ?>')"
            class="text-blue-600 hover:text-blue-900"
            title="<?= $user['status'] == 'aktif' ? 'Deactivate' : 'Activate' ?>">
            <i class="ri-<?= $user['status'] == 'aktif' ? 'user-forbid' : 'user-check' ?>-line"></i>
          </button>
          <button class="text-green-600 hover:text-green-900" title="Edit"
            data-action="edit-user"
            data-user-id="<?= $user['id'] ?>"
            data-namalengkap="<?= esc($user['namalengkap']) ?>"
            data-npm="<?= esc($user['npm']) ?>"
            data-jurusan="<?= esc($user['jurusan']) ?>"
            data-status="<?= esc($user['status']) ?>">
            <i class="ri-edit-2-line"></i>
          </button>
          <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-red-600 hover:text-red-900" title="Delete">
            <i class="ri-delete-bin-line"></i>
          </button>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
<?php else: ?>
  <!-- Empty State -->
  <tr>
    <td colspan="7" class="px-6 py-12 text-center">
      <div class="text-gray-500 flex flex-col gap-2 justify-center">
        <img src="<?= base_url('img/icon-chat.webp') ?>" alt="" width="40" class="block mx-auto">
        <p class="text-sm">tidak ada data</p>

      </div>
    </td>
  </tr>
<?php endif; ?>