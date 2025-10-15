<?php
$alertTypes = [
  'error'   => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'ri-error-warning-line'],
  'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'icon' => 'ri-checkbox-circle-line'],
  'info'    => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'icon' => 'ri-information-line'],
  'warning' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200', 'icon' => 'ri-alert-line'],
];
?>

<!-- Container Alert -->
<div id="alert-container" class="fixed top-4 right-4 space-y-2 z-[9999] max-w-[15rem]">
  <?php foreach ($alertTypes as $type => $style): ?>
    <?php if (session()->has($type)): ?>
      <div
        id="alert-<?= $type ?>"
        class="flex items-center gap-3 p-4  <?= $style['bg'] ?> <?= $style['border'] ?> border rounded-lg shadow-lg <?= $style['text'] ?>"
        style="max-width: 380px; opacity: 0; transform: translateY(10px); transition: opacity 0.3s ease, transform 0.3s ease;"
        role="alert">
        <i class="<?= $style['icon'] ?> text-xl"></i>
        <span class="text-sm flex-1"><?= esc(session($type)) ?></span>
        <button
          type="button"
          onclick="closeAlert('alert-<?= $type ?>')"
          class="text-gray-500 hover:text-gray-700 focus:outline-none">
          <i class="ri-close-line text-lg"></i>
        </button>
      </div>

      <script>
        // Animasi muncul
        (function() {
          const alert = document.getElementById('alert-<?= $type ?>');
          if (alert) {
            // Tunggu DOM ready
            window.addEventListener('load', function() {
              // Beri jeda kecil agar lebih smooth
              setTimeout(() => {
                alert.style.opacity = '1';
                alert.style.transform = 'translateY(0)';
              }, 100);

              // Auto close setelah 3 detik
              setTimeout(() => {
                closeAlert('alert-<?= $type ?>');
              }, 3000);
            });
          }
        })();

        // Fungsi close (pastikan hanya didefinisikan sekali)
        window.closeAlert = window.closeAlert || function(alertId) {
          const alert = document.getElementById(alertId);
          if (alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(10px)';
            setTimeout(() => {
              alert.remove(); // hapus dari DOM
            }, 300);
          }
        };
      </script>
    <?php endif; ?>
  <?php endforeach; ?>
</div>