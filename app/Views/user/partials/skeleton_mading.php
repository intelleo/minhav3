<?php

/**
 * Partial: Skeleton loader untuk daftar mading
 * Param: $count (int) jumlah skeleton, default 4
 */
$count = isset($count) ? (int) $count : 4;
?>

<div class="space-y-4">
  <?php for ($i = 0; $i < $count; $i++): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-5 animate-pulse">
      <div class="flex justify-between items-start mb-3">
        <div class="h-4 w-1/3 bg-gray-200 rounded"></div>
        <div class="h-3 w-24 bg-gray-100 rounded"></div>
      </div>
      <div class="flex gap-2 mb-3">
        <div class="h-6 w-20 bg-gray-200 rounded-full"></div>
        <div class="h-6 w-16 bg-gray-100 rounded-full"></div>
      </div>
      <div class="h-3 w-3/4 bg-gray-200 rounded mb-2"></div>
      <div class="h-3 w-2/3 bg-gray-200 rounded"></div>
      <div class="flex justify-between items-center mt-4">
        <div class="flex gap-4">
          <div class="h-3 w-10 bg-gray-100 rounded"></div>
          <div class="h-3 w-10 bg-gray-100 rounded"></div>
          <div class="h-3 w-10 bg-gray-100 rounded"></div>
        </div>
        <div class="h-3 w-16 bg-gray-100 rounded"></div>
      </div>
    </div>
  <?php endfor; ?>
</div>