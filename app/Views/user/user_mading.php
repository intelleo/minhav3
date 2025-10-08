<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>

<!-- Mading Cards (Lazy Load) -->
<div class="mt-[-5rem] flex flex-col gap-6" id="mading-list" data-loaded-page="0">
  <!-- Skeleton awal -->
  <?= $this->include('user/partials/skeleton_mading', ['count' => 4]) ?>
  <div id="mading-sentinel"></div>
</div>

<script>
  // Helper skeleton
  function setMadingSkeletonVisible(visible) {
    const list = document.getElementById('mading-list');
    if (!list) return;
    const skel = list.querySelector('.space-y-4'); // skeleton partial root
    if (skel) skel.style.display = visible ? '' : 'none';
  }

  window.__isLoadingMading = window.__isLoadingMading || false;

  function __getHttpClient() {
    return (window.api || window.axios || null);
  }
  async function loadMoreMading() {
    if (window.__isLoadingMading) return;
    const http = __getHttpClient();
    if (!http) {
      setTimeout(loadMoreMading, 120);
      return;
    }
    const list = document.getElementById('mading-list');
    const currentPage = parseInt(list.dataset.loadedPage || '0', 10);
    const nextPage = currentPage + 1;
    window.__isLoadingMading = true;
    if (nextPage === 1) setMadingSkeletonVisible(true);
    try {
      const {
        data
      } = await http.get('<?= site_url('Mading/list-html') ?>', {
        params: {
          page: nextPage,
          perPage: 6
        }
      });
      if (!data?.success) return;
      const htmlPart = data.html || '';
      const sentinel = document.getElementById('mading-sentinel');
      let html = '';
      if (nextPage === 1 && !htmlPart) {
        html += `<div class="text-center bg-white py-10 text-gray-500">
        <img src="<?= base_url('img/icon-chat.webp') ?>" alt="Empty" class="w-10 h-10 mx-auto mb-4">
        <p>Tidak ada postingan mading.</p></div>`;
      } else {
        html += htmlPart;
      }
      sentinel.insertAdjacentHTML('beforebegin', html);
      // Pasang ulang SPA handler untuk link baru
      if (window.__spaAttachLinks) window.__spaAttachLinks(document.getElementById('mading-list'));
      list.dataset.loadedPage = String(nextPage);
      if (nextPage === 1) setMadingSkeletonVisible(false);
    } catch (err) {
      console.error('Gagal memuat mading:', err);
      if (window.showAlert) showAlert('error', 'Gagal memuat mading');
    } finally {
      window.__isLoadingMading = false;
    }
  }

  if (window.__madingIO) {
    try {
      window.__madingIO.disconnect();
    } catch (e) {}
  }
  window.__madingIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) loadMoreMading();
    });
  }, {
    rootMargin: '0px 0px 200px 0px'
  });

  (function initMadingLazyLoad() {
    const sentinel = document.getElementById('mading-sentinel');
    if (sentinel) window.__madingIO.observe(sentinel);
    const list = document.getElementById('mading-list');
    if (list && list.dataset.loadedPage === '0') {
      loadMoreMading();
    }
  })();
</script>

<?= $this->endSection() ?>