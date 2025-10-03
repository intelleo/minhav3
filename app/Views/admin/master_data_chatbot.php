<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-chatbot-management mt-[-5rem]">


  <!-- Search & Filter -->
  <div class="bg-white rounded-lg shadow-md py-8 px-4 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <input
          type="text"
          id="searchInput"
          placeholder="Cari layanan informasi..."
          value="<?= esc($filters['search'] ?? '') ?>"
          class="w-full px-4 py-2 border border-gray-300 text-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="flex gap-2">
        <select
          id="kategoriFilter"
          class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="" class="text-gray-600">Semua Kategori</option>
          <?php foreach ($kategoriList as $kategori): ?>
            <option value="<?= esc($kategori) ?>" <?= ($filters['kategori'] ?? '') == $kategori ? 'selected' : '' ?> class="text-gray-600">
              <?= esc($kategori) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="flex gap-2">
          <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
            <i class="ri-add-line mr-1 text-white"></i>
            Tambah Layanan
          </button>
          <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
            <i class="ri-upload-line mr-1 text-white"></i>
            Import
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Chatbot Q&A Table -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>

            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="chatbotTableBody">
          <?= view('admin/partials/chatbot_tbody', ['layanan' => $layanan]) ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Categories Section -->
  <div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800">Kategori Layanan</h3>
      <div class="text-sm text-gray-500">
        Total Layanan: <span class="font-medium"><?= $stats['total'] ?></span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <?php
      $kategoriStats = [
        'Akademik' => $stats['akademik'],
        'Administrasi' => $stats['administrasi'],
        'Umum' => $stats['umum']
      ];

      $kategoriColors = [
        'Akademik' => 'bg-blue-50 border-blue-200',
        'Administrasi' => 'bg-green-50 border-green-200',
        'Umum' => 'bg-purple-50 border-purple-200'
      ];

      $kategoriIcons = [
        'Akademik' => 'ri-graduation-cap-line',
        'Administrasi' => 'ri-file-list-line',
        'Umum' => 'ri-information-line'
      ];
      ?>

      <?php foreach ($kategoriStats as $kategori => $count): ?>
        <div class="border border-gray-200 rounded-lg p-4 <?= $kategoriColors[$kategori] ?>">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <i class="<?= $kategoriIcons[$kategori] ?> text-lg"></i>
                <h4 class="font-medium text-gray-900"><?= esc($kategori) ?></h4>
              </div>
              <p class="text-sm text-gray-500"><?= $count ?> Layanan</p>
            </div>
            <div class="flex gap-2">
              <button
                class="text-blue-600 hover:text-blue-900 filter-by-category"
                data-kategori="<?= esc($kategori) ?>"
                title="Filter berdasarkan <?= esc($kategori) ?>">
                <i class="ri-filter-line"></i>
              </button>
              <button
                class="text-green-600 hover:text-green-900 view-category"
                data-kategori="<?= esc($kategori) ?>"
                title="Lihat layanan <?= esc($kategori) ?>">
                <i class="ri-eye-line"></i>
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Pagination -->
  <div id="chatbotPagination">
    <?= view('admin/partials/chatbot_pagination', [
      'pagination' => $pagination,
      'filters' => $filters
    ]) ?>
  </div>

</div>

<!-- Add Layanan Modal -->
<div id="addLayananModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Tambah Layanan Informasi</h3>
      </div>
      <form id="addLayananForm" class="px-6 py-4">
        <div class="mb-4">
          <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Layanan</label>
          <input
            type="text"
            id="judul"
            name="judul"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Masukkan judul layanan..."
            required>
        </div>
        <div class="mb-4">
          <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Layanan</label>
          <textarea
            id="deskripsi"
            name="deskripsi"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Masukkan deskripsi layanan..."
            required></textarea>
        </div>
        <div class="mb-4">
          <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori Layanan</label>
          <select
            id="kategori"
            name="kategori"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
            <option value="">Pilih Kategori</option>
            <option value="Akademik">Akademik</option>
            <option value="Administrasi">Administrasi</option>
            <option value="Umum">Umum</option>
          </select>
        </div>
        <div class="flex justify-end gap-2">
          <button
            type="button"
            id="cancelAddBtn"
            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Tambah Layanan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Chatbot Management JavaScript
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const kategoriFilter = document.getElementById('kategoriFilter');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    const addBtn = document.querySelector('.bg-blue-600');
    const addModal = document.getElementById('addLayananModal');
    const addForm = document.getElementById('addLayananForm');
    const cancelAddBtn = document.getElementById('cancelAddBtn');

    // Search functionality
    function performSearch() {
      const search = searchInput.value;
      const kategori = kategoriFilter.value;

      loadChatbotData(1, search, kategori);
    }

    // Load chatbot data with AJAX
    function loadChatbotData(page = 1, search = '', kategori = '') {
      const params = new URLSearchParams({
        page: page,
        search: search,
        kategori: kategori
      });

      fetch(`<?= site_url('Admin/MasterData/chatbot') ?>?${params}`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('chatbotTableBody').innerHTML = data.html;
            document.getElementById('chatbotPagination').innerHTML = data.pagination;
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    // Load specific page
    window.loadChatbotPage = function(page) {
      const search = searchInput.value;
      const kategori = kategoriFilter.value;
      loadChatbotData(page, search, kategori);
    };

    // Event listeners
    searchBtn.addEventListener('click', performSearch);
    resetBtn.addEventListener('click', function() {
      searchInput.value = '';
      kategoriFilter.value = '';
      loadChatbotData(1);
    });

    // Add modal functionality
    addBtn.addEventListener('click', function() {
      addModal.classList.remove('hidden');
    });

    cancelAddBtn.addEventListener('click', function() {
      addModal.classList.add('hidden');
      addForm.reset();
    });


    // Add/Edit form submission
    addForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Get form data
      const formData = new FormData(addForm);
      const data = Object.fromEntries(formData.entries());

      console.log('Form Data:', data);

      // Create URLSearchParams
      const params = new URLSearchParams();
      Object.keys(data).forEach(key => {
        params.append(key, data[key]);
      });

      console.log('URLSearchParams:', params.toString());

      // Determine if it's edit or add
      const layananId = document.getElementById('layanan_id');
      const isEdit = layananId && layananId.value;
      const url = isEdit ? '<?= site_url('Admin/MasterData/updateLayanan') ?>' : '<?= site_url('Admin/MasterData/addLayananTest') ?>';

      console.log('Request URL:', url);
      console.log('Is Edit:', isEdit);

      // Submit via Axios (following addUser pattern)
      axios.post(url, params.toString(), {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(function(response) {
          console.log('Response data:', response.data);

          // Update CSRF token if provided
          if (response.data && response.data.csrf) {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', response.data.csrf);
            console.log('CSRF token updated:', response.data.csrf);
          }

          if (response.data.success) {
            addModal.classList.add('hidden');
            addForm.reset();

            // Reset form title and button
            document.querySelector('#addLayananModal h3').textContent = 'Tambah Layanan Informasi';
            document.querySelector('#addLayananForm button[type="submit"]').textContent = 'Tambah Layanan';

            // Remove hidden input if exists
            const hiddenInput = document.getElementById('layanan_id');
            if (hiddenInput) {
              hiddenInput.remove();
            }

            loadChatbotData(1); // Reload data
            alert(isEdit ? 'Layanan berhasil diperbarui!' : 'Layanan berhasil ditambahkan!');
          } else {
            alert('Error: ' + response.data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          if (error.response) {
            console.error('Error response:', error.response.data);
            alert('Error: ' + (error.response.data.message || error.message));
          } else {
            alert('Terjadi kesalahan saat ' + (isEdit ? 'memperbarui' : 'menambahkan') + ' layanan: ' + error.message);
          }
        });
    });

    // Close modal when clicking outside
    addModal.addEventListener('click', function(e) {
      if (e.target === addModal) {
        addModal.classList.add('hidden');
      }
    });

    // Edit layanan functionality
    document.addEventListener('click', function(e) {
      if (e.target.closest('.edit-layanan')) {
        const layananId = e.target.closest('.edit-layanan').dataset.id;
        editLayanan(layananId);
      }
    });

    // Delete layanan functionality
    document.addEventListener('click', function(e) {
      if (e.target.closest('.delete-layanan')) {
        const layananId = e.target.closest('.delete-layanan').dataset.id;
        if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
          deleteLayanan(layananId);
        }
      }
    });

    // View layanan functionality
    document.addEventListener('click', function(e) {
      if (e.target.closest('.view-layanan')) {
        const layananId = e.target.closest('.view-layanan').dataset.id;
        viewLayanan(layananId);
      }
    });

    // Edit layanan function
    function editLayanan(layananId) {
      fetch(`<?= site_url('Admin/MasterData/getLayanan') ?>?id=${layananId}`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Populate form with existing data
            document.getElementById('judul').value = data.data.judul;
            document.getElementById('deskripsi').value = data.data.deskripsi;
            document.getElementById('kategori').value = data.data.kategori;

            // Change form title and submit button
            document.querySelector('#addLayananModal h3').textContent = 'Edit Layanan Informasi';
            document.querySelector('#addLayananForm button[type="submit"]').textContent = 'Update Layanan';

            // Add hidden input for layanan ID
            let hiddenInput = document.getElementById('layanan_id');
            if (!hiddenInput) {
              hiddenInput = document.createElement('input');
              hiddenInput.type = 'hidden';
              hiddenInput.id = 'layanan_id';
              hiddenInput.name = 'layanan_id';
              document.getElementById('addLayananForm').appendChild(hiddenInput);
            }
            hiddenInput.value = layananId;

            // Show modal
            addModal.classList.remove('hidden');
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat mengambil data layanan');
        });
    }

    // Delete layanan function
    function deleteLayanan(layananId) {
      // Get CSRF token from meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      // Create URLSearchParams
      const params = new URLSearchParams();
      params.append('layanan_id', layananId);
      params.append('csrf_test_name', csrfToken);

      console.log('Delete - CSRF Token:', csrfToken);
      console.log('Delete - Layanan ID:', layananId);

      // Submit via Axios
      axios.post('<?= site_url('Admin/MasterData/deleteLayanan') ?>', params.toString(), {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
          }
        })
        .then(function(response) {
          console.log('Delete response:', response.data);

          // Update CSRF token if provided
          if (response.data && response.data.csrf) {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', response.data.csrf);
          }

          if (response.data.success) {
            loadChatbotData(1); // Reload data
            alert('Layanan berhasil dihapus!');
          } else {
            alert('Error: ' + response.data.message);
          }
        })
        .catch(function(error) {
          console.error('Delete error:', error);
          if (error.response) {
            console.error('Delete error response:', error.response.data);
            alert('Error: ' + (error.response.data.message || error.message));
          } else {
            alert('Terjadi kesalahan saat menghapus layanan: ' + error.message);
          }
        });
    }

    // View layanan function
    function viewLayanan(layananId) {
      fetch(`<?= site_url('Admin/MasterData/getLayanan') ?>?id=${layananId}`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show view modal (you can create a separate view modal)
            alert(`Judul: ${data.data.judul}\n\nDeskripsi: ${data.data.deskripsi}\n\nKategori: ${data.data.kategori}`);
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat mengambil data layanan');
        });
    }

    // Filter by category functionality
    document.addEventListener('click', function(e) {
      if (e.target.closest('.filter-by-category')) {
        const kategori = e.target.closest('.filter-by-category').dataset.kategori;
        kategoriFilter.value = kategori;
        loadChatbotData(1, searchInput.value, kategori);
      }
    });

    // View category functionality
    document.addEventListener('click', function(e) {
      if (e.target.closest('.view-category')) {
        const kategori = e.target.closest('.view-category').dataset.kategori;
        kategoriFilter.value = kategori;
        searchInput.value = '';
        loadChatbotData(1, '', kategori);
      }
    });
  });
</script>

<?= $this->endSection() ?>