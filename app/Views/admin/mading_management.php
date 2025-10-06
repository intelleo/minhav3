<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-mading-management mt-[-5rem] relative">


  <div id="admin-mading-list" class="flex flex-col gap-4" data-loaded-page="0">
    <?= $this->include('user/partials/skeleton_mading', ['count' => 4]) ?>
    <div id="admin-mading-sentinel"></div>
  </div>
</div>

<script>
  (function() {
    function http() {
      return (window.api || window.axios || null);
    }

    function setSkeleton(visible) {
      const list = document.getElementById('admin-mading-list');
      if (!list) return;
      const skel = list.querySelector('.space-y-4');
      if (skel) skel.style.display = visible ? '' : 'none';
    }

    window.__adminMadingLoading = window.__adminMadingLoading || false;

    async function loadMoreAdminMading() {
      if (window.__adminMadingLoading) return;
      const cli = http();
      if (!cli) {
        setTimeout(loadMoreAdminMading, 120);
        return;
      }
      const list = document.getElementById('admin-mading-list');
      const current = parseInt(list.dataset.loadedPage || '0', 10);
      const next = current + 1;
      window.__adminMadingLoading = true;
      if (next === 1) setSkeleton(true);
      try {
        const {
          data
        } = await cli.get('<?= site_url('Admin/Mading/list-html') ?>', {
          params: {
            page: next,
            perPage: 6
          }
        });
        if (!data || !data.success) {
          if (next === 1) setSkeleton(false);
          return;
        }
        const sentinel = document.getElementById('admin-mading-sentinel');
        const html = (data.html || '').trim();
        if (html) {
          sentinel.insertAdjacentHTML('beforebegin', html);
          list.dataset.loadedPage = String(next);

          // Re-attach SPA link handlers untuk link baru
          if (typeof window.__adminSpaAttachLinks === 'function') {
            window.__adminSpaAttachLinks(list);
          }
        } else {
          // Tidak ada data untuk halaman ini
          if (next === 1) {
            if (!document.getElementById('admin-mading-empty')) {
              sentinel.insertAdjacentHTML('beforebegin', '<div id="admin-mading-empty" class="text-center py-10 text-gray-500">Tidak ada postingan mading.</div>');
            }
          }
          // Hentikan pengamatan agar tidak memicu muat lagi tanpa data
          try {
            if (window.__adminMadingIO) window.__adminMadingIO.unobserve(sentinel);
          } catch (_) {}
        }
        if (next === 1) setSkeleton(false);
      } catch (e) {
        console.error('Gagal memuat daftar mading (admin):', e);
        if (window.showAlert) showAlert('error', 'Gagal memuat mading');
      } finally {
        window.__adminMadingLoading = false;
      }
    }

    if (window.__adminMadingIO) {
      try {
        window.__adminMadingIO.disconnect();
      } catch (_) {}
    }
    window.__adminMadingIO = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) loadMoreAdminMading();
      });
    }, {
      rootMargin: '0px 0px 200px 0px'
    });

    // Clear SPA cache untuk halaman mading (jika ada)
    try {
      const madingCacheKeys = Object.keys(localStorage).filter(k => k.includes('admin_spa_cache') && k.includes('Mading'));
      madingCacheKeys.forEach(k => localStorage.removeItem(k));
    } catch (e) {
      console.log('Cache clear failed:', e);
    }

    // Jalankan segera agar berfungsi baik di SSR/SPA
    (function initNow() {
      const sentinel = document.getElementById('admin-mading-sentinel');
      if (sentinel) window.__adminMadingIO.observe(sentinel);
      const list = document.getElementById('admin-mading-list');
      if (list && list.dataset.loadedPage === '0') loadMoreAdminMading();
    })();

    // Fungsi Like/Unlike untuk admin
    window.toggleLike = async function(madingId) {
      try {
        const {
          data
        } = await (window.api || window.axios).post('<?= site_url('Mading/like') ?>', new URLSearchParams({
          mading_id: madingId
        }));
        if (!data?.success) {
          if (window.showAlert) showAlert('error', data?.message || 'Gagal like.');
          return;
        }

        // Cari elemen like button berdasarkan madingId
        const likeButtons = document.querySelectorAll(`[onclick="toggleLike(${madingId})"]`);
        likeButtons.forEach(btn => {
          const icon = btn.querySelector('#like-icon');
          const countSpan = btn.querySelector('#like-count');

          if (data.liked) {
            icon.classList.replace('ri-heart-line', 'ri-heart-fill');
            icon.classList.remove('text-gray-600');
            icon.classList.add('text-red-600');
            btn.dataset.liked = '1';
          } else {
            icon.classList.replace('ri-heart-fill', 'ri-heart-line');
            icon.classList.remove('text-red-600');
            icon.classList.add('text-gray-600');
            btn.dataset.liked = '0';
          }

          countSpan.textContent = data.total_likes >= 1000 ?
            (data.total_likes / 1000).toFixed(1).replace(/\.0$/, '') + 'k' :
            data.total_likes.toString();
        });

        // CSRF akan diupdate otomatis oleh interceptor jika server mengirimkan data.csrf / data.csrf_hash
      } catch (err) {
        console.error('Error:', err);
        if (window.showAlert) showAlert('error', 'Gagal memproses like. Coba refresh halaman.');
      }
    };

  })();
</script>

</div>

<?= $this->endSection() ?>