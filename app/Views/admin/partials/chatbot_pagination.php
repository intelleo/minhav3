<div class="mt-6 flex items-center justify-between">
  <div class="text-sm text-gray-700">
    Showing
    <span class="font-medium"><?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?></span>
    to
    <span class="font-medium"><?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?></span>
    of
    <span class="font-medium"><?= $pagination['total'] ?></span>
    results
  </div>

  <?php if ($pagination['total_pages'] > 1): ?>
    <div class="flex gap-2">
      <!-- Previous Button -->
      <button
        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 <?= $pagination['current_page'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
        <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>
        onclick="loadChatbotPage(<?= $pagination['current_page'] - 1 ?>)">
        Previous
      </button>

      <!-- Page Numbers -->
      <?php
      $startPage = max(1, $pagination['current_page'] - 2);
      $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);

      for ($i = $startPage; $i <= $endPage; $i++):
      ?>
        <button
          class="px-3 py-2 text-sm border rounded-lg <?= $i == $pagination['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?>"
          onclick="loadChatbotPage(<?= $i ?>)">
          <?= $i ?>
        </button>
      <?php endfor; ?>

      <!-- Next Button -->
      <button
        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
        <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>
        onclick="loadChatbotPage(<?= $pagination['current_page'] + 1 ?>)">
        Next
      </button>
    </div>
  <?php endif; ?>
</div>
