<?php if (($pagination['total_pages'] ?? 1) > 1): ?>
  <?php for ($i = 1; $i <= (int)$pagination['total_pages']; $i++): $isActive = $i === (int)$pagination['current_page']; ?>
    <a href="#" data-page="<?= $i ?>" class="pagination-link px-3 py-1 border rounded <?= $isActive ? 'bg-gray-800 text-white' : '' ?>"><?= $i ?></a>
  <?php endfor; ?>
<?php endif; ?>