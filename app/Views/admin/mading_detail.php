<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-mading-detail mt-[-5rem]">
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
        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $mading['status'] === 'aktif' ? 'bg-green-100 text-green-800' : ($mading['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
          <?= ucfirst($mading['status']) ?>
        </span>
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
    <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
      <div class="flex space-x-6 text-sm text-gray-500">
        <!-- Views -->
        <span>
          <i class="ri-eye-line mr-1"></i>
          <?= number_format($mading['views']) ?> Dilihat
        </span>

        <!-- Likes -->
        <?php $adminLiked = (new \App\Models\MadingLikeModel())
          ->where('mading_id', $mading['id'])
          ->where('user_id', session('admin_id'))
          ->first() !== null; ?>
        <button
          type="button"
          id="like-btn"
          class="flex items-center gap-1 text-gray-600 hover:text-red-600 cursor-pointer transition-colors duration-200"
          data-liked="<?= $adminLiked ? '1' : '0' ?>"
          onclick="toggleLike(<?= $mading['id'] ?>)">
          <i id="like-icon" class="<?= $adminLiked ? 'ri-heart-fill text-red-600' : 'ri-heart-line text-gray-600' ?> text-lg"></i>
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
    <form action="<?= site_url('Admin/Mading/komentar') ?>" method="post" id="comment-form" class="mb-6" onsubmit="return submitComment(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="mading_id" value="<?= $mading['id'] ?>">
      <input type="hidden" name="parent_id" id="parent_id" value="">

      <div class="flex">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
          <span class="text-blue-600 font-semibold">
            <?= strtoupper(substr(session('admin_username'), 0, 1)) ?>
          </span>
        </div>
        <div class="flex-1">
          <textarea
            name="isi_komentar"
            placeholder="Tulis komentar sebagai admin..."
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
      <!-- Skeleton Loader Komentar -->
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

    textarea.placeholder = `Balas komentar ${username}`;
    textarea.focus();
    parentIdInput.value = commentId;
    cancelButton.classList.remove('hidden');
  }

  function cancelReply() {
    const textarea = document.querySelector('#comment-form textarea');
    const parentIdInput = document.getElementById('parent_id');
    const cancelButton = document.getElementById('cancel-reply');

    textarea.value = '';
    textarea.placeholder = 'Tulis komentar sebagai admin...';
    parentIdInput.value = '';
    cancelButton.classList.add('hidden');
  }
</script>

<!-- Script untuk Komentar dan Like -->
<script>
  // Submit komentar via Axios
  async function submitComment(e) {
    e.preventDefault();
    const form = document.getElementById('comment-form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const url = form.getAttribute('action');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    const csrfInput = form.querySelector('input[name^="csrf_"]');
    if (csrfMetaTag && csrfInput) {
      csrfInput.value = csrfMetaTag.getAttribute('content') || '';
    }

    const formData = new URLSearchParams(new FormData(form));

    try {
      const res = await window.api.post(url, formData);
      const data = res && res.data;

      if (!data || !data.success) {
        if (window.showAlert) showAlert('error', data?.message || 'Gagal mengirim komentar');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Kirim Komentar';
        return false;
      }

      form.reset();
      cancelReply();
      await reloadCommentsSection();

      submitBtn.disabled = false;
      submitBtn.textContent = 'Kirim Komentar';

      if (typeof data.totalComments === 'number') {
        const countHeader = document.getElementById('comments-count-header');
        if (countHeader) countHeader.textContent = formatAngka(data.totalComments);
        const footerCount = document.querySelector('[data-comments-footer-count]');
        if (footerCount) footerCount.textContent = formatAngka(data.totalComments);
      }

      if (window.showAlert) showAlert('success', 'Komentar terkirim');
      return false;
    } catch (err) {
      console.error('Gagal submit komentar:', err);
      if (window.showAlert) showAlert('error', 'Gagal mengirim komentar. Coba lagi.');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Kirim Komentar';
      return false;
    }
  }

  // Reload komentar section
  async function reloadCommentsSection() {
    try {
      const list = document.getElementById('comments-list');
      list.innerHTML = '<div id="comments-sentinel"></div>';
      list.dataset.loadedPage = '0';
      await loadMoreComments();
    } catch (err) {
      console.error('Gagal memuat ulang komentar:', err);
      if (window.showAlert) showAlert('error', 'Gagal memuat ulang komentar');
    }
  }

  // Render komentar dengan nested threading
  function renderCommentNodeFromJson(node) {
    let html = '';
    const isOwnComment = (node.namalengkap === '<?= session('admin_username') ?? '' ?>');
    const ownCommentClass = isOwnComment ? 'own-comment' : '';
    const isAdmin = (node.user_type === 'admin');
    const adminBadge = isAdmin ? '<span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Admin</span>' : '';

    html += `<div class="comment-root border-l-2 border-gray-200 pl-2 ml-2 mb-4 cursor-pointer ${ownCommentClass}" ` +
      `data-comment-id="${node.id}" data-comment-author="${escapeHtml(node.namalengkap || 'User')}" ` +
      `onmousedown="startLongPress(this, event)" onmouseup="cancelLongPress(this)" ` +
      `ontouchstart="startLongPress(this, event)" ontouchend="cancelLongPress(this)">` +
      `<div class="comment-root-header flex items-start mb-2">` +
      `<div class="avatar w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-2 flex-shrink-0 overflow-hidden">` +
      `${renderAvatar(node)}` +
      `</div>` +
      `<div class="meta"><strong class="author text-sm">${escapeHtml(node.namalengkap || 'User')}</strong>${adminBadge}` +
      `<span class="time text-xs text-gray-500 ml-2">${formatTimeAgo(node.created_at)}</span></div>` +
      `</div>` +
      `<div class="comment-root-body"><p class="content text-gray-700 text-[0.85rem] mb-2 ml-10">${escapeHtml(node.isi_komentar || '')}</p></div>` +
      `<div class="comment-root-actions">` +
      `<button class="reply-btn text-xs text-blue-600 hover:underline ml-10 cursor-pointer" ` +
      `data-comment-id="${node.id}" data-username="${escapeHtml(node.namalengkap || 'User')}" onclick="replyTo(this)">Balas</button>` +
      `</div>`;

    if (node.replies && node.replies.length > 0) {
      const replyCount = countAllReplies(node.replies);
      const repliesId = `replies-${node.id}`;

      html += `<button type="button" class="replies-toggle text-xs text-blue-600 hover:underline ml-10 mt-1" ` +
        `data-count="${replyCount}" onclick="toggleReplies('${repliesId}', this)">Lihat ${replyCount} balasan lainnya</button>` +
        `<div id="${repliesId}" class="comment-replies mt-3 hidden">`;

      const allReplies = flattenReplies(node.replies);
      allReplies.forEach(reply => {
        html += renderFlatReplyNode(reply, node.namalengkap);
      });

      html += `</div>`;
    }

    html += `</div>`;
    return html;
  }

  // Flatten nested replies
  function flattenReplies(replies) {
    let flatReplies = [];
    replies.forEach(reply => {
      flatReplies.push(reply);
      if (reply.replies && reply.replies.length > 0) {
        const nestedFlat = flattenReplies(reply.replies);
        flatReplies = flatReplies.concat(nestedFlat);
      }
    });
    return flatReplies;
  }

  // Render balasan komentar
  function renderFlatReplyNode(reply, parentName) {
    const isOwnReply = (reply.namalengkap === '<?= session('admin_username') ?? '' ?>');
    const ownReplyClass = isOwnReply ? 'own-comment' : '';
    const isAdmin = (reply.user_type === 'admin');
    const adminBadge = isAdmin ? '<span class="ml-1 px-1 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">Admin</span>' : '';

    let html = `<div class="comment-reply mt-2 p-2 rounded-md ml-0 border border-blue-50 bg-white cursor-pointer ${ownReplyClass}" ` +
      `data-comment-id="${reply.id}" data-comment-author="${escapeHtml(reply.namalengkap || 'User')}" ` +
      `onmousedown="startLongPress(this, event)" onmouseup="cancelLongPress(this)" ` +
      `ontouchstart="startLongPress(this, event)" ontouchend="cancelLongPress(this)">` +
      `<div class="comment-reply-header flex items-start mb-1">` +
      `<div class="avatar w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center mr-2 flex-shrink-0 overflow-hidden">` +
      `${renderAvatar(reply, false)}` +
      `</div>` +
      `<div class="meta"><strong class="author text-xs">${escapeHtml(reply.namalengkap || 'User')}</strong>${adminBadge}` +
      `<span class="time text-xs text-gray-500 ml-2">${formatTimeAgo(reply.created_at)}</span></div>` +
      `</div>` +
      `<div class="comment-reply-body"><p class="content text-gray-600 text-xs mt-1 ml-8">` +
      `<span class="mention text-blue-600 font-medium">@${escapeHtml(parentName || 'User')}</span> ` +
      `${escapeHtml(reply.isi_komentar || '')}</p></div>` +
      `<div class="comment-reply-actions">` +
      `<button class="reply-btn text-xs text-blue-600 hover:underline ml-8 cursor-pointer" ` +
      `data-comment-id="${reply.id}" data-username="${escapeHtml(reply.namalengkap || 'User')}" onclick="replyTo(this)">Balas</button>` +
      `</div>`;

    html += `</div>`;
    return html;
  }

  function escapeHtml(str) {
    return (str || '').toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Render avatar
  function renderAvatar(node, small = false) {
    const sizeClass = small ? 'text-xs' : 'text-sm';
    const foto = (node.foto_profil || '').toString().trim();
    const nama = node.namalengkap || 'U';
    const inisial = (nama || 'U').toString().trim().charAt(0).toUpperCase();
    const isAdmin = (node.user_type === 'admin');
    const bgColor = isAdmin ? 'bg-blue-100 text-blue-600' : 'bg-indigo-100 text-[#1c68c5]';

    if (foto && foto !== 'null') {
      let src = '';
      if (/^https?:\/\//i.test(foto)) {
        try {
          const u = new URL(foto);
          const p = (u.pathname || '').replace(/^\/+/, '');
          src = `<?= base_url('') ?>${p}`;
        } catch (e) {
          src = foto;
        }
      } else {
        const rel = foto.replace(/^\/+/, '');
        src = `<?= base_url('') ?>${rel}`;
      }
      return `<img src="${src}" alt="Foto Profil" class="w-full h-full object-cover" loading="lazy" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling?.classList?.remove('hidden');">` +
        `<span class="w-full h-full ${bgColor} font-semibold hidden items-center justify-center ${sizeClass}">${inisial}</span>`;
    }
    return `<span class="w-full h-full ${bgColor} font-semibold flex items-center justify-center ${sizeClass}">${inisial}</span>`;
  }

  function formatTimeAgo(iso) {
    let date;
    if (typeof iso === 'string' && iso.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
      const utcString = iso.replace(' ', 'T') + '+07:00';
      date = new Date(utcString);
    } else {
      date = new Date(iso);
    }

    const now = new Date();
    const diff = Math.floor((now.getTime() - date.getTime()) / 1000);

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

  // Skeleton komentar
  function setCommentsSkeletonVisible(visible) {
    const skel = document.getElementById('comments-skeleton');
    if (!skel) return;
    skel.style.display = visible ? '' : 'none';
  }

  // State untuk infinite scroll
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
      } = await http.get(`<?= site_url('Admin/Mading/comments/' . $mading['id']) ?>`, {
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

      if (nextPage === 1) setCommentsSkeletonVisible(false);
    } catch (err) {
      console.error('Gagal memuat komentar:', err);
      if (window.showAlert) showAlert('error', 'Gagal memuat komentar');
    } finally {
      window.__isLoadingComments = false;
    }
  }

  // IntersectionObserver untuk infinite scroll
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

  // Inisialisasi
  (function initCommentsLazyLoad() {
    const sentinel = document.getElementById('comments-sentinel');
    if (sentinel) window.__commentsIO.observe(sentinel);
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

  function countAllReplies(replies) {
    let count = 0;
    if (!replies || !Array.isArray(replies)) return count;
    replies.forEach(reply => {
      count++;
      if (reply.replies && Array.isArray(reply.replies)) {
        count += countAllReplies(reply.replies);
      }
    });
    return count;
  }

  // Like functionality
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

  async function toggleLike(madingId) {
    try {
      const {
        data
      } = await window.api.post('<?= site_url('Admin/Mading/like') ?>', new URLSearchParams({
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