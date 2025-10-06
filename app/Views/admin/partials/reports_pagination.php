<?php if (!empty($pagination) && ($pagination['total_pages'] ?? 1) > 1): ?>
  <div class="mt-6 flex items-center justify-between">
    <div class="text-sm text-gray-600">
      Showing <span class="font-medium">
        <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?>
      </span>
      to <span class="font-medium">
        <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?>
      </span>
      of <span class="font-medium"><?= $pagination['total'] ?></span> results
    </div>
    <div class="flex items-center space-x-2">
      <?php
      $baseUrl = current_url();
      $qs = $_GET;
      $pageKey = $pagination['query_key_page'] ?? 'page';
      $perKey = $pagination['query_key_perpage'] ?? 'per_page';
      $qs[$perKey] = $pagination['per_page'];
      ?>
      <?php if ($pagination['current_page'] > 1): ?>
        <?php $qs[$pageKey] = $pagination['current_page'] - 1; ?>
        <a href="<?= $baseUrl . '?' . http_build_query($qs) ?>"
          class="pagination-link px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
          data-page="<?= $pagination['current_page'] - 1 ?>">
          Previous
        </a>
      <?php endif; ?>

      <?php
      $startPage = max(1, $pagination['current_page'] - 2);
      $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
      ?>
      <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <?php $qs[$pageKey] = $i; ?>
        <a href="<?= $baseUrl . '?' . http_build_query($qs) ?>"
          class="pagination-link px-3 py-2 text-sm border rounded-lg <?= $i == $pagination['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?>"
          data-page="<?= $i ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
        <?php $qs[$pageKey] = $pagination['current_page'] + 1; ?>
        <a href="<?= $baseUrl . '?' . http_build_query($qs) ?>"
          class="pagination-link px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
          data-page="<?= $pagination['current_page'] + 1 ?>">
          Next
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>