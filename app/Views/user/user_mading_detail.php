<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>
<?= $this->include('partials/alert') ?>

<!-- Context Menu untuk Komentar -->
<?= $this->include('partials/components/comment_context_menu', [
  'currentUser' => session('namalengkap') ?? ''
]) ?>
<div class="mt-[-5rem] ">
  <!-- Detail Mading -->

  <div class="bg-white rounded-xl shadow-sm border-l-4 <?= mading_card_border($mading['category']) ?> overflow-hidden mb-6">
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-start mb-4 max-lg:flex-col max-lg:items-start">
        <h1 class="text-2xl font-bold text-gray-800"><?= esc($mading['judul']) ?></h1>
        <span class="text-sm text-gray-500 flex items-center">
          <i class="ri-calendar-2-line text-gray-500 mr-1"></i>
          <?= mading_date_format($mading['tgl_mulai']) ?>
          <?php if ($mading['tgl_akhir']): ?>
            - <?= mading_date_format($mading['tgl_akhir']) ?>
          <?php endif; ?>
        </span>
      </div>

      <!-- Meta -->
      <div class="flex flex-wrap gap-2 mb-4">
        <?= mading_category_badge($mading['category']) ?>
      </div>

      <!-- Author -->
      <?= mading_admin_badge($mading['username']) ?>

      <!-- Content -->
      <div class="prose mt-4 text-gray-700">
        <p><?= nl2br(esc($mading['deskripsi'])) ?></p>
      </div>

      <!-- File -->
      <?php if ($mading['file']): ?>
        <div class="mt-4">
          <a href="<?= base_url($mading['file']) ?>" target="_blank"
            class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
            <i class="ri-attachment-line mr-1"></i>
            Unduh Lampiran
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Footer -->
    <!-- Footer -->
    <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
      <div class="flex space-x-6 text-sm text-gray-500">
        <!-- Views -->
        <span>
          <i class="ri-eye-line mr-1"></i>
          <?= number_format($mading['views']) ?> Dilihat
        </span>

        <!-- Likes -->
        <!-- Like Button -->
        <?php $userLiked = (new \App\Models\MadingLikeModel())
          ->where('mading_id', $mading['id'])
          ->where('user_id', session('user_id'))
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

        <!-- Comments -->
        <span class="flex items-center">
          <i class="ri-chat-3-line mr-1"></i>
          <span data-comments-footer-count><?= $mading['total_comments'] >= 1000 ? number_format($mading['total_comments'] / 1000, 1) . 'k' : $mading['total_comments'] ?></span>
        </span>
      </div>
      <button class="text-blue-600 hover:text-blue-800 text-sm"
        onclick="document.getElementById('comment-form').scrollIntoView()">
        <i class="ri-chat-3-line mr-1"></i> Komentari
      </button>
    </div>
  </div>

  <!-- Comments Section -->
  <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Komentar (<span id="comments-count-header"><?= $mading['total_comments'] >= 1000 ? number_format($mading['total_comments'] / 1000, 1) . 'k' : $mading['total_comments'] ?></span>)</h3>

    <!-- Form Komentar -->
    <form action="<?= site_url('Mading/komentar') ?>" method="post" id="comment-form" class="mb-6" onsubmit="return submitComment(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="mading_id" value="<?= $mading['id'] ?>">
      <input type="hidden" name="parent_id" id="parent_id" value="">

      <div class="flex">
        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3 flex-shrink-0">
          <span class="text-indigo-600 font-semibold">
            <?= strtoupper(substr(session('namalengkap'), 0, 1)) ?>
          </span>
        </div>
        <div class="flex-1">
          <textarea
            name="isi_komentar"
            placeholder="Tulis komentar Anda..."
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
            rows="3"
            required></textarea>
          <div class="mt-2 flex justify-end gap-2">
            <button type="button" id="cancel-reply" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm hidden" onclick="cancelReply()">
              Batal Balas
            </button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
              Kirim Komentar
            </button>
          </div>
        </div>
      </div>
    </form>


    <!-- Daftar Komentar -->
    <div class="space-y-4" id="comments-list" data-loaded-page="1">
      <!-- Skeleton Loader Komentar (partial) -->
      <div id="comments-skeleton"><?= $this->include('user/partials/skeleton_comment', ['count' => 3]) ?></div>
      <div id="comments-sentinel"></div>
    </div>
  </div>


</div>

<!-- Script untuk Reply -->
<script>
  function replyTo(button) {
    const commentId = button.dataset.commentId;
    const username = button.dataset.username;

    const textarea = document.querySelector('#comment-form textarea');
    const parentIdInput = document.getElementById('parent_id');
    const cancelButton = document.getElementById('cancel-reply');

    // Ubah placeholder menjadi "Balas {nama user}" tanpa mengubah isi textarea
    textarea.placeholder = `Balas komentar ${username}`;
    textarea.focus();

    // Set parent_id ke ID komentar yang dibalas
    parentIdInput.value = commentId;

    // Tampilkan tombol batal
    cancelButton.classList.remove('hidden');
  }

  function cancelReply() {
    const textarea = document.querySelector('#comment-form textarea');
    const parentIdInput = document.getElementById('parent_id');
    const cancelButton = document.getElementById('cancel-reply');

    textarea.value = '';
    // Kembalikan placeholder default
    textarea.placeholder = 'Tulis komentar Anda...';
    parentIdInput.value = '';
    cancelButton.classList.add('hidden');
  }
</script>

<!-- likes -->

<script>
  // Submit komentar via Axios tanpa reload halaman
  async function submitComment(e) {
    e.preventDefault();
    const form = document.getElementById('comment-form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const url = form.getAttribute('action');

    // Disable button to prevent multiple submissions
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';
    // Pastikan input CSRF hidden di form menggunakan token terbaru dari meta
    const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    const csrfInput = form.querySelector('input[name^="csrf_"]');
    if (csrfMetaTag && csrfInput) {
      csrfInput.value = csrfMetaTag.getAttribute('content') || '';
    }
    const formData = new URLSearchParams(new FormData(form));
    try {
      const res = await window.api.post(url, formData);
      const data = res && res.data;
      const contentType = res && res.headers && res.headers['content-type'];
      // Jika backend mengembalikan HTML (misal redirect/CSRF), lakukan reload halaman
      if (!data || (typeof data !== 'object' && contentType && contentType.indexOf('application/json') === -1)) {
        window.location.reload();
        return false;
      }
      if (!data.success) {
        console.warn('Submit komentar gagal:', {
          status: res.status,
          data
        });
        if (window.showAlert) showAlert('error', data.message || 'Gagal mengirim komentar');

        // Re-enable button on error
        submitBtn.disabled = false;
        submitBtn.textContent = 'Kirim Komentar';
        return false;
      }
      // Reset form dan placeholder
      form.reset();
      cancelReply();
      // Muat ulang bagian komentar saja
      await reloadCommentsSection();

      // Re-enable button
      submitBtn.disabled = false;
      submitBtn.textContent = 'Kirim Komentar';
      // Update counter komentar pada header dan footer (dari totalComments jika tersedia)
      if (typeof data.totalComments === 'number') {
        const countHeader = document.getElementById('comments-count-header');
        if (countHeader) countHeader.textContent = formatAngka(data.totalComments);
        const footerCount = document.querySelector('[data-comments-footer-count]');
        if (footerCount) footerCount.textContent = formatAngka(data.totalComments);
      } else if (typeof data.totalRoots === 'number') {
        const countHeader = document.getElementById('comments-count-header');
        if (countHeader) countHeader.textContent = formatAngka(data.totalRoots);
        const footerCount = document.querySelector('[data-comments-footer-count]');
        if (footerCount) footerCount.textContent = formatAngka(data.totalRoots);
      }
      // Tampilkan notifikasi sukses
      if (window.showAlert) showAlert('success', 'Komentar terkirim');
      return false;
    } catch (err) {
      console.error('Gagal submit komentar:', err);
      if (window.showAlert) showAlert('error', 'Gagal mengirim komentar. Coba lagi.');

      // Hide spinner on error
      if (window.spinnerLoader) {
        window.spinnerLoader.hide(submitBtn);
      }
      return false;
    }
  }

  // Ambil ulang HTML halaman ini dan ganti hanya container komentar
  async function reloadCommentsSection() {
    try {
      // Reset list, lalu muat page 1
      const list = document.getElementById('comments-list');
      list.innerHTML = '<div id="comments-sentinel"></div>';
      list.dataset.loadedPage = '0';
      await loadMoreComments();
    } catch (err) {
      console.error('Gagal memuat ulang komentar:', err);
      if (window.showAlert) showAlert('error', 'Gagal memuat ulang komentar');
    }
  }

  // Render komentar dengan nested threading (fleksibel seperti modern comment system)
  function renderCommentNodeFromJson(node) {
    let html = '';

    // Check if this is user's own comment
    const isOwnComment = (node.namalengkap === '<?= session('namalengkap') ?? '' ?>');
    const ownCommentClass = isOwnComment ? 'own-comment' : '';

    // Render komentar utama
    html += `<div class="comment-root border-l-2 border-gray-200 pl-2 ml-2 mb-4 cursor-pointer ${ownCommentClass}" ` +
      `data-comment-id="${node.id}" data-comment-author="${escapeHtml(node.namalengkap || 'User')}" ` +
      `onmousedown="startLongPress(this, event)" onmouseup="cancelLongPress(this)" ` +
      `ontouchstart="startLongPress(this, event)" ontouchend="cancelLongPress(this)">` +
      `<div class="comment-root-header flex items-start mb-2">` +
      `<div class=\"avatar w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-2 flex-shrink-0 overflow-hidden\">` +
      `${renderAvatar(node)}` +
      `</div>` +
      `<div class="meta"><strong class="author text-sm">${escapeHtml(node.namalengkap || 'User')}</strong>` +
      `<span class="time text-xs text-gray-500 ml-2">${formatTimeAgo(node.created_at)}</span></div>` +
      `</div>` +
      `<div class="comment-root-body"><p class="content text-gray-700 text-[0.85rem] mb-2 ml-10">${escapeHtml(node.isi_komentar || '')}</p></div>` +
      `<div class="comment-root-actions">` +
      `<button class="reply-btn text-xs text-blue-600 hover:underline ml-10 cursor-pointer" ` +
      `data-comment-id="${node.id}" data-username="${escapeHtml(node.namalengkap || 'User')}" onclick="replyTo(this)">Balas</button>` +
      `</div>`;

    // Render balasan dengan posisi yang sama (flat structure - semua sejajar)
    if (node.replies && node.replies.length > 0) {
      const replyCount = countAllReplies(node.replies); // Count all nested replies
      const repliesId = `replies-${node.id}`;

      html += `<button type="button" class="replies-toggle text-xs text-blue-600 hover:underline ml-10 mt-1" ` +
        `data-count="${replyCount}" onclick="toggleReplies('${repliesId}', this)">Lihat ${replyCount} balasan lainnya</button>` +
        `<div id="${repliesId}" class="comment-replies mt-3 hidden">`;

      // Render semua balasan secara flat (tidak nested) - semua sejajar
      const allReplies = flattenReplies(node.replies);
      allReplies.forEach(reply => {
        html += renderFlatReplyNode(reply, node.namalengkap);
      });

      html += `</div>`;
    }

    html += `</div>`;
    return html;
  }

  // Flatten nested replies menjadi array flat (semua sejajar)
  function flattenReplies(replies) {
    let flatReplies = [];

    replies.forEach(reply => {
      // Tambahkan reply ini ke array flat
      flatReplies.push(reply);

      // Jika ada nested replies, tambahkan juga secara flat
      if (reply.replies && reply.replies.length > 0) {
        const nestedFlat = flattenReplies(reply.replies);
        flatReplies = flatReplies.concat(nestedFlat);
      }
    });

    return flatReplies;
  }

  // Render balasan komentar dengan posisi yang sama (flat structure)
  function renderFlatReplyNode(reply, parentName) {
    // Semua balasan memiliki posisi yang sama - tidak ada indentasi berbeda
    const indentClass = 'ml-0 '; // Posisi tetap sama untuk SEMUA balasan
    const borderClass = 'border-blue-50'; // Border biru untuk semua balasan

    const bgClass = 'bg-white'; // Background abu-abu untuk semua balasan
    const avatarSize = 'w-6 h-6'; // Ukuran avatar sama untuk semua balasan
    const textSize = 'text-xs'; // Ukuran teks sama untuk semua balasan

    // Check if this is user's own comment
    const isOwnReply = (reply.namalengkap === '<?= session('namalengkap') ?? '' ?>');
    const ownReplyClass = isOwnReply ? 'own-comment' : '';

    let html = `<div class="comment-reply mt-2 p-2 rounded-md ${indentClass} border ${borderClass} ${bgClass} cursor-pointer ${ownReplyClass}" ` +
      `data-comment-id="${reply.id}" data-comment-author="${escapeHtml(reply.namalengkap || 'User')}" ` +
      `onmousedown="startLongPress(this, event)" onmouseup="cancelLongPress(this)" ` +
      `ontouchstart="startLongPress(this, event)" ontouchend="cancelLongPress(this)">` +
      `<div class="comment-reply-header flex items-start mb-1">` +
      `<div class=\"avatar ${avatarSize} rounded-full bg-gray-100 flex items-center justify-center mr-2 flex-shrink-0 overflow-hidden\">` +
      `${renderAvatar(reply, false)}` +
      `</div>` +
      `<div class="meta"><strong class="author text-xs">${escapeHtml(reply.namalengkap || 'User')}</strong>` +
      `<span class="time text-xs text-gray-500 ml-2">${formatTimeAgo(reply.created_at)}</span></div>` +
      `</div>` +
      `<div class="comment-reply-body"><p class="content text-gray-600 ${textSize} mt-1 ml-8">` +
      `<span class="mention text-blue-600 font-medium">@${escapeHtml(parentName || 'User')}</span> ` +
      `${escapeHtml(reply.isi_komentar || '')}</p></div>` +
      `<div class="comment-reply-actions">` +
      `<button class="reply-btn text-xs text-blue-600 hover:underline ml-8 cursor-pointer" ` +
      `data-comment-id="${reply.id}" data-username="${escapeHtml(reply.namalengkap || 'User')}" onclick="replyTo(this)">Balas</button>` +
      `</div>`;

    html += `</div>`;
    return html;
  }

  // Render balasan komentar dengan posisi yang sama (tidak ada indentasi berbeda) - DEPRECATED
  function renderNestedReplyNode(reply, parentName, depth = 1) {
    // Fungsi ini tidak digunakan lagi, semua balasan menggunakan renderFlatReplyNode
    return renderFlatReplyNode(reply, parentName);
  }

  // Render balasan komentar (flat threading - dengan tombol balas) - DEPRECATED
  function renderReplyNode(reply, parentName) {
    return `<div class="comment-reply mt-3 ml-6 border-b border-gray-100 p-2 rounded-md bg-gray-50">` +
      `<div class="comment-reply-header flex items-start mb-1">` +
      `<div class=\"avatar w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center mr-2 flex-shrink-0 overflow-hidden\">` +
      `${renderAvatar(reply, true)}` +
      `</div>` +
      `<div class="meta"><strong class="author text-xs">${escapeHtml(reply.namalengkap || 'User')}</strong>` +
      `<span class="time text-xs text-gray-500 ml-2">${formatTimeAgo(reply.created_at)}</span></div>` +
      `</div>` +
      `<div class="comment-reply-body"><p class="content text-gray-600 text-[0.75rem] mt-1 ml-8">` +
      `<span class="mention text-blue-600 font-medium">@${escapeHtml(parentName || 'User')}</span> ` +
      `${escapeHtml(reply.isi_komentar || '')}</p></div>` +
      `<div class="comment-reply-actions">` +
      `<button class="reply-btn text-xs text-blue-600 hover:underline ml-8 cursor-pointer" ` +
      `data-comment-id="${reply.id}" data-username="${escapeHtml(reply.namalengkap || 'User')}" onclick="replyTo(this)">Balas</button>` +
      `</div>` +
      `</div>`;
  }

  function escapeHtml(str) {
    return (str || '').toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Render avatar: gunakan foto profil jika tersedia, jika tidak gunakan inisial
  function renderAvatar(node, small = false) {
    const sizeClass = small ? 'text-xs' : 'text-sm';
    const foto = (node.foto_profil || '').toString().trim();
    const nama = node.namalengkap || 'U';
    const inisial = (nama || 'U').toString().trim().charAt(0).toUpperCase();
    if (foto) {
      let src = '';
      if (/^https?:\/\//i.test(foto)) {
        // Normalisasi absolut → ambil path lalu gabungkan dengan base_url saat ini
        try {
          const u = new URL(foto);
          const p = (u.pathname || '').replace(/^\/+/, '');
          src = `<?= base_url('') ?>${p}`;
        } catch (e) {
          src = foto; // fallback
        }
      } else {
        const rel = foto.replace(/^\/+/, '');
        src = `<?= base_url('') ?>${rel}`;
      }
      return `<img src="${src}" alt="Foto Profil" class="w-full h-full object-cover" loading="lazy" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling?.classList?.remove('hidden');">` +
        `<span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold hidden items-center justify-center ${sizeClass}">${inisial}</span>`;
    }
    return `<span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold flex items-center justify-center ${sizeClass}">${inisial}</span>`;
  }

  function formatTimeAgo(iso) {
    // Debug: log waktu yang diterima
    console.log('formatTimeAgo input:', iso, 'type:', typeof iso);

    let date;
    if (typeof iso === 'string' && iso.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
      // Format: 'YYYY-MM-DD HH:mm:ss' dari MySQL (waktu lokal Asia/Jakarta)
      // Tambahkan 'Z' untuk memastikan parse sebagai UTC, lalu konversi ke lokal
      const utcString = iso.replace(' ', 'T') + '+07:00'; // Asia/Jakarta = UTC+7
      date = new Date(utcString);
      console.log('Parsed date:', date, 'Local time:', date.toLocaleString());
    } else {
      // Fallback untuk format lain
      date = new Date(iso);
    }

    const now = new Date();
    const diff = Math.floor((now.getTime() - date.getTime()) / 1000);

    console.log('Time diff (seconds):', diff);

    if (diff < 5) return 'baru saja';
    if (diff < 60) return `${diff} detik yang lalu`;
    const m = Math.floor(diff / 60);
    if (m < 60) return `${m} menit yang lalu`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h} jam yang lalu`;
    const d = Math.floor(h / 24);
    if (d === 1) return 'Kemarin';
    if (d < 30) return `${d} hari yang lalu`;
    return date.toLocaleDateString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    });
  }

  // Tampilkan/sembunyikan skeleton komentar
  function setCommentsSkeletonVisible(visible) {
    const skel = document.getElementById('comments-skeleton');
    if (!skel) return;
    skel.style.display = visible ? '' : 'none';
  }

  // State untuk infinite scroll (gunakan global untuk hindari redeklarasi saat SPA)
  window.__isLoadingComments = window.__isLoadingComments || false;

  function __getHttpClient() {
    return (window.api || window.axios || null);
  }
  async function loadMoreComments() {
    if (window.__isLoadingComments) return;
    const http = __getHttpClient();
    if (!http) {
      setTimeout(loadMoreComments, 120);
      return;
    }
    const list = document.getElementById('comments-list');
    const currentPage = parseInt(list.dataset.loadedPage || '0', 10);
    const nextPage = currentPage + 1;
    window.__isLoadingComments = true;
    if (nextPage === 1) setCommentsSkeletonVisible(true);
    try {
      const {
        data
      } = await http.get(`<?= site_url('Mading/comments/' . $mading['id']) ?>`, {
        params: {
          page: nextPage,
          perPage: 5
        }
      });
      if (!data?.success) return;
      const items = data.items || [];
      const container = document.getElementById('comments-list');
      const sentinel = document.getElementById('comments-sentinel');
      let html = '';
      if (nextPage === 1 && items.length === 0) {
        html += '<p class="text-gray-500 text-center py-4 bg-gray-50 rounded-lg">Belum ada komentar.</p>';
      } else {
        items.forEach(node => {
          html += renderCommentNodeFromJson(node, 0);
        });
      }
      sentinel.insertAdjacentHTML('beforebegin', html);
      list.dataset.loadedPage = String(nextPage);
      // Update header count dari data.totalRoots bila ingin
      if (nextPage === 1) setCommentsSkeletonVisible(false);
    } catch (err) {
      console.error('Gagal memuat komentar:', err);
      if (window.showAlert) showAlert('error', 'Gagal memuat komentar');
    } finally {
      window.__isLoadingComments = false;
    }
  }

  // IntersectionObserver untuk infinite scroll (satu instance global)
  if (window.__commentsIO) {
    try {
      window.__commentsIO.disconnect();
    } catch (_) {}
  }
  window.__commentsIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        loadMoreComments();
      }
    });
  }, {
    rootMargin: '0px 0px 200px 0px'
  });

  // Inisialisasi segera (berjalan untuk SSR dan SPA)
  (function initCommentsLazyLoad() {
    const sentinel = document.getElementById('comments-sentinel');
    if (sentinel) window.__commentsIO.observe(sentinel);
    // Muat page pertama saat konten siap
    const list = document.getElementById('comments-list');
    if (list && (list.dataset.loadedPage === '1' || list.dataset.loadedPage === '0')) {
      list.dataset.loadedPage = '0';
      loadMoreComments();
    }
  })();

  function toggleReplies(containerId, btn) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.classList.toggle('hidden');
    const total = btn.dataset.count || '';
    if (el.classList.contains('hidden')) {
      btn.textContent = total ? `Lihat ${total} balasan lainnya` : 'Lihat balasan';
    } else {
      btn.textContent = 'Sembunyikan balasan';
    }
  }

  // Fungsi untuk menghitung total replies termasuk nested
  function countAllReplies(replies) {
    let count = 0;
    if (!replies || !Array.isArray(replies)) return count;

    replies.forEach(reply => {
      count++; // Count current reply
      if (reply.replies && Array.isArray(reply.replies)) {
        count += countAllReplies(reply.replies); // Recursively count nested replies
      }
    });

    return count;
  }

  // Catatan: CSRF dihandle global oleh axios-setup (window.api + meta),
  // tidak perlu variabel lokal di halaman ini.

  // Update tampilan like saat konten ini dimuat (dukungan SPA)
  (function applyInitialLikeState() {
    const icon = document.getElementById('like-icon');
    const btn = document.getElementById('like-btn');
    if (!icon) return;
    const likedInit = btn && btn.dataset.liked === '1';
    if (likedInit) {
      icon.classList.replace('ri-heart-line', 'ri-heart-fill');
      icon.classList.remove('text-gray-600');
      icon.classList.add('text-red-600');
    } else {
      icon.classList.replace('ri-heart-fill', 'ri-heart-line');
      icon.classList.remove('text-red-600');
      icon.classList.add('text-gray-600');
    }
  })();

  // Fungsi Like/Unlike (Axios)
  async function toggleLike(madingId) {
    try {
      const {
        data
      } = await window.api.post('<?= site_url('Mading/like') ?>', new URLSearchParams({
        mading_id: madingId
      }));
      if (!data?.success) {
        if (window.showAlert) showAlert('error', data?.message || 'Gagal like.');
        return;
      }

      const icon = document.getElementById('like-icon');
      const countSpan = document.getElementById('like-count');

      if (data.liked) {
        icon.classList.replace('ri-heart-line', 'ri-heart-fill');
        icon.classList.remove('text-gray-600');
        icon.classList.add('text-red-600');
        const btn = document.getElementById('like-btn');
        if (btn) btn.dataset.liked = '1';
      } else {
        icon.classList.replace('ri-heart-fill', 'ri-heart-line');
        icon.classList.remove('text-red-600');
        icon.classList.add('text-gray-600');
        const btn = document.getElementById('like-btn');
        if (btn) btn.dataset.liked = '0';
      }

      countSpan.textContent = formatAngka(data.total_likes);
      // CSRF akan diupdate otomatis oleh interceptor jika server mengirimkan data.csrf / data.csrf_hash
    } catch (err) {
      console.error('Error:', err);
      if (window.showAlert) showAlert('error', 'Gagal memproses like. Coba refresh halaman.');
    }
  }

  function formatAngka(angka) {
    return angka >= 1000 ?
      (angka / 1000).toFixed(1).replace(/\.0$/, '') + 'k' :
      angka;
  }
</script>


<?= $this->endSection() ?>