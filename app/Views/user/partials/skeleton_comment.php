<?php

/**
 * Partial: Skeleton loader untuk komentar
 * Param: $count (int) jumlah skeleton, default 3
 */
$count = isset($count) ? (int) $count : 3;
?>

<div class="space-y-3">
  <?php for ($i = 0; $i < $count; $i++): ?>
    <div class="animate-pulse">
      <div class="flex items-start gap-2 mb-2">
        <div class="w-8 h-8 rounded-full bg-gray-200"></div>
        <div class="flex-1">
          <div class="h-3 w-32 bg-gray-200 rounded mb-2"></div>
          <div class="h-3 w-20 bg-gray-100 rounded"></div>
        </div>
      </div>
      <div class="ml-10 space-y-2">
        <div class="h-3 bg-gray-200 rounded w-3/4"></div>
        <div class="h-3 bg-gray-200 rounded w-2/3"></div>
      </div>
    </div>
  <?php endfor; ?>
</div>