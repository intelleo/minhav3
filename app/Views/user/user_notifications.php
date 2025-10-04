<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>

<style>
  .notif-list {
    margin-top: -5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .notif-item {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 12px;
    padding: 0.875rem 1rem;
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    position: relative;
    transition: transform .2s ease, opacity .2s ease, height .2s ease, margin .2s ease;
    will-change: transform;
  }

  .notif-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5f0ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e40af;
    font-weight: 700;
  }

  .notif-meta {
    font-size: 0.8rem;
    color: #6b7280;
  }

  .notif-link {
    color: #247de3;
    text-decoration: none;
  }

  .notif-link:hover {
    text-decoration: underline;
  }

  /* Swipe hint background (optional) */
  .notif-item::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(239, 68, 68, .05), transparent);
    pointer-events: none;
    border-radius: 12px;
    opacity: 0;
    transition: opacity .2s ease;
  }

  .notif-item.swiping::after {
    opacity: 1;
  }

  .undo-bar {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: white;
    color: #fff;
    border-radius: 10px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, .2);
    z-index: 10000;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity .2s ease, transform .2s ease;
  }

  .undo-bar.show {
    opacity: 1;
    transform: translateY(0);
  }

  .undo-btn {
    background: #10b981;
    color: #fff;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
  }

  .undo-btn:hover {
    background: #059669;
  }
</style>

<div class="notif-list">
  <?php if (!empty($notifications)): ?>
    <?php foreach ($notifications as $n): ?>
      <?php
      $name = trim((string)($n['replier_name'] ?? 'Pengguna'));
      $isAdmin = strpos($name, '[ADMIN]') === 0;
      $cleanName = $isAdmin ? str_replace('[ADMIN]', '', $name) : $name;
      $initial = mb_strtoupper(mb_substr(trim($cleanName), 0, 1));
      $photo = trim((string)($n['replier_photo'] ?? ''));
      $photoUrl = '';
      if ($photo !== '') {
        if (preg_match('/^https?:\/\//i', $photo)) {
          // Jika absolut, normalisasi ke host saat ini dengan mempertahankan path
          $parts = @parse_url($photo);
          $path = isset($parts['path']) ? $parts['path'] : '';
          if ($path !== '') {
            $photoUrl = base_url(ltrim($path, '/'));
          } else {
            $photoUrl = $photo; // fallback
          }
        } else {
          // Relatif -> jadikan absolut terhadap base_url
          $photoUrl = base_url(ltrim($photo, '/'));
        }
      }
      $isSeen = isset($seenIds) && is_array($seenIds) ? in_array((int)($n['id'] ?? 0), array_map('intval', $seenIds), true) : false;
      ?>
      <div class="notif-item" data-id="<?= (int)($n['id'] ?? 0) ?>" style="<?= $isSeen ? '' : 'background:#eff6ff' ?>">
        <div style="position:relative;width:40px;height:40px;flex:0 0 40px;">
          <?php if (!empty($photoUrl)): ?>
            <?= img_tag($photoUrl, 'avatar', ['class' => 'rounded-full h-full w-full object-cover', 'width' => 40, 'height' => 40]) ?>
            <div class="notif-avatar hidden" style="position:absolute;left:0;top:0;width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:<?= $isAdmin ? '#dbeafe' : '#e5f0ff'; ?>;color:<?= $isAdmin ? '#2563eb' : '#1e40af'; ?>">
              <?= esc($initial) ?>
            </div>
          <?php else: ?>
            <div class="notif-avatar" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:<?= $isAdmin ? '#dbeafe' : '#e5f0ff'; ?>;color:<?= $isAdmin ? '#2563eb' : '#1e40af'; ?>">
              <?= esc($initial) ?>
            </div>
          <?php endif; ?>
        </div>
        <div>
          <div class="text-sm text-gray-800">
            <strong><?= esc($cleanName) ?></strong>
            <?php if ($isAdmin): ?>
              <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full ml-1">ADMIN</span>
            <?php endif; ?>
            membalas komentar kamu di
            <a class="notif-link" href="<?= site_url('Mading/detail/' . (int)$n['mading_id']) ?>">"<?= esc($n['judul']) ?>"</a>
          </div>
          <div class="notif-meta">
            <?= esc(date('d M Y H:i', strtotime((string)($n['created_at'] ?? date('Y-m-d H:i:s'))))) ?>
          </div>
          <?php if (!empty($n['isi_komentar'])): ?>
            <div class="text-sm text-gray-600 mt-1">"<?= esc($n['isi_komentar']) ?>"</div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="text-center py-10 text-gray-500 bg-white">
      <i class="ri-notification-3-line text-6xl block mb-4 text-gray-300"></i>
      <p>Belum ada notifikasi</p>
    </div>
  <?php endif; ?>
</div>

<div id="undoBar" class="undo-bar" style="display:none" class="max-lg:right-0 max-lg:leading-0">
  <span>Notifikasi dihapus.</span>
  <button id="undoBtn" class="undo-btn">Urungkan</button>
  <button id="undoClose" class="undo-btn" style="background:#6b7280">Tutup</button>
  <span id="undoTimer" style="font-size:12px;opacity:.8">3</span>
  <i class="ri-notification-off-line" style="opacity:.8"></i>

</div>

<script>
  (function() {
    const list = document.querySelector('.notif-list');
    if (!list) return;

    let undoData = null; // { node, index, timeout }
    const undoBar = document.getElementById('undoBar');
    const undoBtn = document.getElementById('undoBtn');
    const undoClose = document.getElementById('undoClose');
    const undoTimerEl = document.getElementById('undoTimer');

    function showUndo(node, index) {
      // Clear previous
      if (undoData && undoData.timeout) {
        clearInterval(undoData.timeout);
      }
      undoData = {
        node,
        index,
        seconds: 3,
        timeout: null
      };
      undoTimerEl.textContent = String(undoData.seconds);
      undoBar.style.display = 'flex';
      requestAnimationFrame(() => undoBar.classList.add('show'));
      undoData.timeout = setInterval(() => {
        undoData.seconds -= 1;
        undoTimerEl.textContent = String(undoData.seconds);
        if (undoData.seconds <= 0) {
          finalizeDelete();
        }
      }, 1000);
    }

    function hideUndo() {
      if (!undoData) return;
      undoBar.classList.remove('show');
      setTimeout(() => {
        undoBar.style.display = 'none';
      }, 200);
      if (undoData.timeout) clearInterval(undoData.timeout);
    }

    function undo() {
      if (!undoData) return;
      // Restore
      const children = Array.from(list.children);
      const refNode = children[undoData.index] || null;
      list.insertBefore(undoData.node, refNode);
      undoData.node.style.transform = '';
      undoData.node.style.opacity = '';
      undoData.node.style.height = '';
      undoData.node.style.margin = '';
      hideUndo();
      undoData = null;
    }

    function finalizeDelete() {
      if (!undoData) return;
      hideUndo();
      // Here we could call API to persist deletion (e.g., mark as dismissed)
      undoData = null;
    }

    if (undoBtn) undoBtn.addEventListener('click', undo);
    if (undoClose) undoClose.addEventListener('click', finalizeDelete);

    function attachSwipe(item) {
      let startX = 0;
      let currentX = 0;
      let dragging = false;

      function onStart(e) {
        dragging = true;
        startX = (e.touches ? e.touches[0].clientX : e.clientX);
        item.classList.add('swiping');
      }

      function onMove(e) {
        if (!dragging) return;
        currentX = (e.touches ? e.touches[0].clientX : e.clientX);

        const dxRaw = currentX - startX;
        const dx = dxRaw * 0.2; // natural, langsung ikut gerakan jari
        const translate = Math.min(0, dx);

        item.style.transform = `translateX(${translate}px)`;
        item.style.opacity = String(1 + translate / 300);
      }

      function onEnd() {
        if (!dragging) return;
        dragging = false;
        item.classList.remove('swiping');

        const dx = currentX - startX; // natural, tanpa friction

        if (dx < -140) { // threshold hapus tetap sama
          // Dismiss with animation
          const index = Array.from(list.children).indexOf(item);
          const placeholderHeight = getComputedStyle(item).height;

          item.style.transform = 'translateX(-120%)';
          item.style.opacity = '0';

          // Collapse height after slide
          setTimeout(() => {
            item.style.height = placeholderHeight;
            // force reflow
            void item.offsetHeight;
            item.style.height = '0px';
            item.style.margin = '0';
          }, 150);

          // Remove and show undo
          setTimeout(() => {
            const node = item; // keep reference
            if (node.parentNode) node.parentNode.removeChild(node);

            // Persist di server: tandai dismissed
            const id = parseInt(item.getAttribute('data-id') || '0');
            if (id) {
              (window.api || window.axios || null)
              ?.post('<?= site_url('Notifications/dismiss') ?>' + '/' + id)
                .then(function() {
                  if (window.__refreshNotifCount) window.__refreshNotifCount();
                })
                .catch(function() {});
            }

            showUndo(node, index);
          }, 350);
        } else {
          // Reset
          item.style.transform = '';
          item.style.opacity = '';
        }
      }


      item.addEventListener('touchstart', onStart, {
        passive: true
      });
      item.addEventListener('touchmove', onMove, {
        passive: true
      });
      item.addEventListener('touchend', onEnd);
      // Optional mouse support
      item.addEventListener('mousedown', onStart);
      window.addEventListener('mousemove', onMove);
      window.addEventListener('mouseup', onEnd);
    }

    document.querySelectorAll('.notif-item').forEach(function(item) {
      // Nonaktifkan swipe jika fokus long-press
      // attachSwipe(item);

      // Long-press to delete (konsep seperti komentar)
      let lpTimer = null;
      let lpActive = false;
      const LP_THRESHOLD_MS = 600;

      function startLongPress(e) {
        if (lpTimer) clearTimeout(lpTimer);
        lpActive = true;
        item.classList.add('swiping');
        lpTimer = setTimeout(() => {
          if (!lpActive) return;
          doDismiss(item);
        }, LP_THRESHOLD_MS);
      }

      function cancelLongPress() {
        lpActive = false;
        if (lpTimer) {
          clearTimeout(lpTimer);
          lpTimer = null;
        }
        item.classList.remove('swiping');
      }

      item.addEventListener('touchstart', startLongPress, {
        passive: true
      });
      item.addEventListener('touchend', cancelLongPress);
      item.addEventListener('touchmove', cancelLongPress);
      item.addEventListener('mousedown', startLongPress);
      item.addEventListener('mouseup', cancelLongPress);
      item.addEventListener('mouseleave', cancelLongPress);

      function doDismiss(targetItem) {
        const index = Array.from(list.children).indexOf(targetItem);
        const placeholderHeight = getComputedStyle(targetItem).height;
        targetItem.style.transform = 'translateX(-120%)';
        targetItem.style.opacity = '0';
        setTimeout(() => {
          targetItem.style.height = placeholderHeight;
          void targetItem.offsetHeight;
          targetItem.style.height = '0px';
          targetItem.style.margin = '0';
        }, 150);
        setTimeout(() => {
          const node = targetItem;
          if (node.parentNode) node.parentNode.removeChild(node);
          const id = parseInt(targetItem.getAttribute('data-id') || '0');
          if (id) {
            var http = (window.api || window.axios || null);
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var headers = {
              'X-Requested-With': 'XMLHttpRequest'
            };
            if (csrfMeta && csrfMeta.content) headers['X-CSRF-TOKEN'] = csrfMeta.content;
            http?.post('<?= site_url('Notifications/dismiss') ?>' + '/' + id, null, {
                headers: headers
              })
              .then(function(res) {
                try {
                  var newToken = (res && res.headers && (res.headers['x-csrf-token'] || res.headers['X-CSRF-TOKEN'])) || null;
                  if (newToken && csrfMeta) csrfMeta.setAttribute('content', newToken);
                } catch (_) {}
                if (window.__refreshNotifCount) window.__refreshNotifCount();
              })
              .catch(function() {});
          }
          showUndo(node, index);
        }, 350);
      }
      // Klik link notifikasi: tandai seen lalu navigasi, dan sembunyikan dot segera
      var link = item.querySelector('a.notif-link');
      if (link) {
        link.addEventListener('click', function(ev) {
          ev.preventDefault();
          var href = link.getAttribute('href');
          var id = parseInt(item.getAttribute('data-id') || '0');
          var http = (window.api || window.axios || null);
          // Optimistic UI: hapus background dan dot segera
          item.style.background = '';
          try {
            var dot = document.getElementById('navNotifDot');
            var dotMobile = document.getElementById('navNotifDotMobile');
            var cardDot = document.getElementById('cardNotifDot');
            if (dot) dot.classList.add('hidden');
            if (dotMobile) dotMobile.classList.add('hidden');
            if (cardDot) cardDot.classList.add('hidden');
          } catch (_) {}
          if (http && id) {
            http.post('<?= site_url('Notifications/seen') ?>' + '/' + id).finally(function() {
              if (window.__refreshNotifCount) window.__refreshNotifCount();
              // Navigasi menggunakan SPA router
              if (window.__spaNavigate) {
                window.__spaNavigate(href, true);
              } else {
                window.location.href = href;
              }
            });
          } else {
            // Navigasi menggunakan SPA router
            if (window.__spaNavigate) {
              window.__spaNavigate(href, true);
            } else {
              window.location.href = href;
            }
          }
        });
      }
      // Tandai seen saat diklik
      item.addEventListener('click', function() {
        var id = parseInt(item.getAttribute('data-id') || '0');
        if (!id) return;
        var http = (window.api || window.axios || null);
        if (!http) return;
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var headers = {
          'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrfMeta && csrfMeta.content) headers['X-CSRF-TOKEN'] = csrfMeta.content;
        http.post('<?= site_url('Notifications/seen') ?>' + '/' + id, null, {
            headers: headers
          })
          .then(function(res) {
            try {
              var newToken = (res && res.headers && (res.headers['x-csrf-token'] || res.headers['X-CSRF-TOKEN'])) || null;
              if (newToken && csrfMeta) csrfMeta.setAttribute('content', newToken);
            } catch (_) {}
            item.style.background = '';
            if (window.__refreshNotifCount) window.__refreshNotifCount();
          })
          .catch(function() {});
      });
    });
  })();
</script>

<?= $this->endSection() ?>