<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-lg shadow-md p-4">
    <div class="flex items-center">
      <div class="p-2 bg-blue-100 rounded-lg">
        <i class="ri-team-fill text-2xl text-blue-600"></i>
      </div>
      <div class="ml-4">
        <p class="text-sm text-gray-600">Total Users</p>
        <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?></p>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-lg shadow-md p-4">
    <div class="flex items-center">
      <div class="p-2 bg-green-100 rounded-lg">
        <i class="ri-user-check-fill text-2xl text-green-600"></i>
      </div>
      <div class="ml-4">
        <p class="text-sm text-gray-600">Active</p>
        <p class="text-2xl font-bold text-gray-900"><?= $stats['active'] ?></p>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-lg shadow-md p-4">
    <div class="flex items-center">
      <div class="p-2 bg-yellow-100 rounded-lg">
        <i class="ri-time-fill text-2xl text-yellow-600"></i>
      </div>
      <div class="ml-4">
        <p class="text-sm text-gray-600">Pending</p>
        <p class="text-2xl font-bold text-gray-900"><?= $stats['pending'] ?></p>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-lg shadow-md p-4">
    <div class="flex items-center">
      <div class="p-2 bg-red-100 rounded-lg">
        <i class="ri-user-forbid-fill text-2xl text-red-600"></i>
      </div>
      <div class="ml-4">
        <p class="text-sm text-gray-600">Inactive</p>
        <p class="text-2xl font-bold text-gray-900"><?= $stats['nonaktif'] ?></p>
      </div>
    </div>
  </div>
</div>