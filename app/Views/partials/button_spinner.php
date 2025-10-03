<style>
  /* Spinner bulat minimal - berwarna biru (#2563eb) seperti tombol utama */
  .btn-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid white;
    border-top-color: #2563eb;
    /* Warna biru untuk spinner */
    border-radius: 50%;
    animation: btn-spin 1s linear infinite;
  }

  @keyframes btn-spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  /* Pertahankan warna background biru untuk button yang memiliki spinner */
  [data-spinner="button"]:disabled {
    background-color: #2563eb !important;
    /* bg-blue-600 */
    color: white;
    opacity: 1;
    /* Override default disabled opacity */
  }
</style>

<script>
  (function() {
    function spinOn(btn) {
      if (!btn || btn.disabled) return;
      btn.dataset.originalHtml = btn.innerHTML;
      btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span>';
      // Disable setelah submit dipicu agar tidak membatalkan submit default
      setTimeout(function() {
        btn.disabled = true;
      }, 0);
      // Safety restore jika halaman tidak berpindah (mis. error tanpa reload)
      btn.dataset.restoreOnTimeout = '1';
      setTimeout(function() {
        if (btn.dataset.restoreOnTimeout === '1') {
          restoreButtonSpinner(btn);
        }
      }, 15000);
    }

    window.restoreButtonSpinner = function(btn) {
      if (typeof btn === 'string') btn = document.querySelector(btn);
      if (!btn) return;
      btn.disabled = false;
      if (btn.dataset.originalHtml) {
        btn.innerHTML = btn.dataset.originalHtml;
        delete btn.dataset.originalHtml;
      }
      delete btn.dataset.restoreOnTimeout;
    };

    // Auto-attach setelah DOM siap
    document.addEventListener('DOMContentLoaded', function() {
      // Dengarkan submit form agar tidak membatalkan submit default
      document.querySelectorAll('form').forEach(function(form) {
        if (form.dataset.spinnerBound === '1') return;
        form.dataset.spinnerBound = '1';

        form.addEventListener('submit', function(e) {
          // Cari tombol yang memicu submit
          var submitter = e.submitter || form.querySelector('[data-spinner="button"]') || form.querySelector('button[type="submit"], input[type="submit"]');
          if (submitter && submitter.matches('[data-spinner="button"]')) {
            spinOn(submitter);
          }
        });
      });

      // Untuk elemen non-form (mis. <a> atau button standalone), tetap dukung click
      document.querySelectorAll('[data-spinner="button"]').forEach(function(btn) {
        if (btn.closest('form')) return; // sudah ditangani oleh listener form
        if (btn.dataset.spinnerClickBound === '1') return;
        btn.dataset.spinnerClickBound = '1';
        btn.addEventListener('click', function() {
          spinOn(btn);
        });
      });
    });

    // Ekspor helper untuk pemakaian manual jika diperlukan
    window.attachButtonSpinner = function(selector) {
      // Re-scan manual jika diperlukan
      document.querySelectorAll(selector || '[data-spinner="button"]').forEach(function(btn) {
        if (btn.closest('form')) return;
        if (btn.dataset.spinnerClickBound === '1') return;
        btn.dataset.spinnerClickBound = '1';
        btn.addEventListener('click', function() {
          spinOn(btn);
        });
      });
    };
  })();
</script>