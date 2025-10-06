<?php if ($pagination['total_pages'] > 1): ?>
  <div class="mt-6 flex items-center justify-between">
    <div class="text-sm text-gray-700">
      Showing <span class="font-medium"><?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?></span>
      to <span class="font-medium"><?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?></span>
      of <span class="font-medium"><?= $pagination['total'] ?></span> results
    </div>
    <div class="flex gap-2">
      <?php if ($pagination['current_page'] > 1): ?>
        <a href="#" class="pagination-link px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
          data-page="<?= $pagination['current_page'] - 1 ?>">
          Previous
        </a>
      <?php else: ?>
        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" disabled>
          Previous
        </button>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
        <a href="#" class="pagination-link px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 <?= $i == $pagination['current_page'] ? 'bg-blue-50 text-blue-600' : '' ?>"
          data-page="<?= $i ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
        <a href="#" class="pagination-link px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
          data-page="<?= $pagination['current_page'] + 1 ?>">
          Next
        </a>
      <?php else: ?>
        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" disabled>
          Next
        </button>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>