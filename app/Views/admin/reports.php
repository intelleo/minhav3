<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-reports mt-[-5rem]">

  <!-- Statistics Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 ">
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center">
        <div class="p-3 bg-blue-100 rounded-lg">
          <i class="ri-message-3-line text-2xl text-blue-600"></i>
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-800"><?= $stats['total_comments'] ?></h3>
          <p class="text-sm text-gray-600">Total Komentar</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 ">
      <div class="flex items-center">
        <div class="p-3 bg-green-100 rounded-lg">
          <i class="ri-news-line text-2xl text-green-600"></i>
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-800"><?= $stats['total_mading'] ?></h3>
          <p class="text-sm text-gray-600">Total Mading</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center">
        <div class="p-3 bg-purple-100 rounded-lg">
          <i class="ri-eye-line text-2xl text-purple-600"></i>
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-800"><?= $stats['total_views'] ?></h3>
          <p class="text-sm text-gray-600">Total Views</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center">
        <div class="p-3 bg-red-100 rounded-lg">
          <i class="ri-heart-line text-2xl text-red-600"></i>
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-800"><?= $stats['total_likes'] ?></h3>
          <p class="text-sm text-gray-600">Total Likes</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs Section -->
  <div class="bg-white rounded-lg shadow mt-8">
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
        <button onclick="showTab('comments')" id="tab-comments" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
          <i class="ri-message-3-line mr-2"></i>
          Komentar Mading
        </button>
        <button onclick="showTab('mading')" id="tab-mading" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
          <i class="ri-news-line mr-2"></i>
          Laporan Mading
        </button>
      </nav>
    </div>

    <!-- Comments Tab -->
    <div id="content-comments" class="tab-content">
      <div class="p-6">
        <div class="mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Daftar Komentar Mading</h3>
          <p class="text-sm text-gray-600">Kelola dan balas komentar dari pengguna</p>
        </div>

        <?php if (empty($comments)): ?>
          <div class="text-center py-12">
            <i class="ri-message-3-line text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Belum ada komentar</p>
          </div>
        <?php else: ?>
          <div class="space-y-4">
            <?php foreach ($comments as $comment): ?>
              <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center overflow-hidden">
                      <?php
                      $rawFoto = (string)($comment['foto_profil'] ?? '');
                      $namaCmt = trim((string)($comment['namalengkap'] ?? 'User'));

                      // Inisial dari maksimal 2 kata
                      $inis = '';
                      $nmParts = preg_split('/\s+/', $namaCmt) ?: [];
                      if (!empty($nmParts)) {
                        $inis .= mb_strtoupper(mb_substr($nmParts[0], 0, 1));
                        if (isset($nmParts[1]) && $nmParts[1] !== '') {
                          $inis .= mb_strtoupper(mb_substr($nmParts[1], 0, 1));
                        }
                      }

                      // Normalisasi URL foto
                      $fotoUrl = '';
                      if ($rawFoto !== '') {
                        if (preg_match('/^https?:\/\//i', $rawFoto)) {
                          $fotoUrl = $rawFoto;
                        } else {
                          // Jika hanya filename tanpa path, prepend uploads/profiles/
                          $path = (strpos($rawFoto, '/') === false) ? ('uploads/profiles/' . $rawFoto) : ltrim($rawFoto, '/');
                          $fotoUrl = base_url($path);
                        }
                      }
                      ?>

                      <?php if ($fotoUrl !== ''): ?>
                        <img src="<?= esc($fotoUrl) ?>" alt="Foto Profil" class="w-full h-full object-cover rounded-full" onerror="this.classList.add('hidden'); var s=this.nextElementSibling; if(s){s.classList.remove('hidden');}">
                        <span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold hidden items-center justify-center text-sm">
                          <?= esc($inis ?: 'U') ?>
                        </span>
                      <?php else: ?>
                        <span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold flex items-center justify-center text-sm">
                          <?= esc($inis ?: 'U') ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-gray-900"><?= esc($comment['namalengkap']) ?></p>
                        <p class="text-xs text-gray-500"><?= date('d M Y H:i', strtotime($comment['created_at'])) ?></p>
                      </div>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <?= esc($comment['mading_judul']) ?>
                      </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-700"><?= esc($comment['isi_komentar']) ?></p>

                    <!-- Reply Form -->
                    <div class="mt-3">
                      <button onclick="toggleReplyForm(<?= $comment['id'] ?>)" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="ri-reply-line mr-1"></i>
                        Balas
                      </button>

                      <div id="reply-form-<?= $comment['id'] ?>" class="hidden mt-3">
                        <form class="reply-form" data-parent-id="<?= $comment['id'] ?>" data-mading-id="<?= $comment['mading_id'] ?>">
                          <div class="flex space-x-2">
                            <textarea name="reply" rows="2" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis balasan..."></textarea>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                              <i class="ri-send-plane-line"></i>
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <!-- Pagination Comments -->
          <?php if (!empty($pagination['comments'])): ?>
            <?= view('admin/partials/reports_pagination', ['pagination' => $pagination['comments']]) ?>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Mading Reports Tab -->
    <div id="content-mading" class="tab-content hidden">
      <div class="p-6">
        <div class="mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Laporan Mading</h3>
          <p class="text-sm text-gray-600">Statistik views, likes, dan komentar per mading</p>
        </div>

        <?php if (empty($madingReports)): ?>
          <div class="text-center py-12">
            <i class="ri-news-line text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Belum ada mading</p>
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mading</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Likes</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($madingReports as $mading): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900"><?= esc($mading['judul']) ?></div>
                      <div class="text-sm text-gray-500"><?= esc($mading['admin_username']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <?php
                      $statusClass = [
                        'aktif' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'nonaktif' => 'bg-red-100 text-red-800'
                      ];
                      $class = $statusClass[$mading['status']] ?? 'bg-gray-100 text-gray-800';
                      ?>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $class ?>">
                        <?= ucfirst($mading['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex items-center">
                        <i class="ri-eye-line mr-1 text-gray-400"></i>
                        <?= number_format($mading['views'] ?? 0) ?>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex items-center">
                        <i class="ri-heart-line mr-1 text-red-400"></i>
                        <?= number_format($mading['total_likes'] ?? 0) ?>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex items-center">
                        <i class="ri-message-3-line mr-1 text-blue-400"></i>
                        <?= number_format($mading['total_comments'] ?? 0) ?>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <?= date('d M Y', strtotime($mading['created_at'])) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <!-- Pagination Mading -->
          <?php if (!empty($pagination['mading'])): ?>
            <?= view('admin/partials/reports_pagination', ['pagination' => $pagination['mading']]) ?>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  // CSRF helper (name + hash) yang bisa diperbarui setelah request
  const CSRF = {
    name: '<?= csrf_token() ?>',
    hash: '<?= csrf_hash() ?>'
  };

  function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('hidden');
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
      button.classList.remove('active', 'border-blue-500', 'text-blue-600');
      button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
  }

  function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    form.classList.toggle('hidden');
  }

  // Handle reply form submission (works for SPA and normal load)
  (function bindReplyHandlers() {
    const attach = () => {
      document.querySelectorAll('.reply-form').forEach(form => {
        if (form.dataset.bound === '1') return;
        form.dataset.bound = '1';
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          if (this.dataset.loading === '1') return;
          this.dataset.loading = '1';

          const formData = new FormData(this);
          const parentId = this.dataset.parentId;
          const madingId = this.dataset.madingId;

          formData.append('parent_id', parentId);
          formData.append('mading_id', madingId);
          // Sertakan CSRF di body
          formData.append(CSRF.name, CSRF.hash);

          // Disable tombol submit saat proses
          const submitBtn = this.querySelector('button[type="submit"]');
          const prevText = submitBtn ? submitBtn.innerHTML : '';
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="ri-loader-4-line animate-spin mr-1"></span> Mengirim...';
          }

          fetch('<?= site_url("Admin/Reports/replyComment") ?>', {
              method: 'POST',
              // Kirim credential cookie session admin
              credentials: 'same-origin',
              // Sertakan CSRF juga di header agar aman dengan konfigurasi yang berbeda
              headers: {
                'X-CSRF-TOKEN': CSRF.hash,
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                if (window.showAlert) {
                  window.showAlert('success', 'Balasan berhasil dikirim!');
                } else {
                  alert('Balasan berhasil dikirim!');
                }
                this.reset();
                // Sembunyikan wrapper form balasan berdasarkan id
                const wrapper = document.getElementById('reply-form-' + parentId);
                if (wrapper) {
                  wrapper.classList.add('hidden');
                }
                // Perbarui CSRF hash untuk request berikutnya
                if (data.csrf) {
                  CSRF.hash = data.csrf;
                }
                // Sisipkan balasan ke DOM tanpa reload
                if (data.reply) {
                  const containerCard = wrapper ? wrapper.closest('.border') : null;
                  if (containerCard) {
                    const replyNode = document.createElement('div');
                    replyNode.className = 'mt-3 ml-12 border-l pl-4';
                    const waktu = new Date((data.reply.created_at || '').replace(' ', 'T'));
                    const waktuStr = isNaN(waktu.getTime()) ? (data.reply.created_at || '') : waktu.toLocaleString();
                    replyNode.innerHTML = `
                    <div class="flex items-start space-x-3">
                      <div class="w-8 h-8 rounded-full flex items-center justify-center bg-purple-100 text-purple-700 text-xs font-semibold">A</div>
                      <div>
                        <div class="text-xs text-gray-500">${escapeHtml(waktuStr)}</div>
                        <div class="text-sm text-gray-800">${escapeHtml(data.reply.isi_komentar || '')}</div>
                      </div>
                    </div>`;
                    containerCard.appendChild(replyNode);
                  }
                }
              } else {
                if (window.showAlert) {
                  window.showAlert('error', 'Gagal mengirim balasan: ' + (data.message || 'Terjadi kesalahan'));
                } else {
                  alert('Gagal mengirim balasan: ' + (data.message || 'Terjadi kesalahan'));
                }
              }
            })
            .catch(error => {
              console.error('Error:', error);
              if (window.showAlert) {
                window.showAlert('error', 'Terjadi kesalahan saat mengirim balasan');
              } else {
                alert('Terjadi kesalahan saat mengirim balasan');
              }
            })
            .finally(() => {
              this.dataset.loading = '0';
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = prevText;
              }
            });
        });
      });
    };

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', attach);
    } else {
      attach();
    }
  })();

  // Utility: escape HTML agar aman disisipkan
  function escapeHtml(str) {
    return (str || '').toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
</script>

<?= $this->endSection() ?>