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
    <div class="overflow-x-auto results-container" data-base-url="<?= site_url('Admin/MasterData/chatbot') ?>">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="judul">
                <span>Judul</span>
                <span class="sort-indicator" data-for="judul"></span>
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="deskripsi">
                <span>Deskripsi</span>
                <span class="sort-indicator" data-for="deskripsi"></span>
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="kategori">
                <span>Kategori</span>
                <span class="sort-indicator" data-for="kategori"></span>
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="created_at">
                <span>Dibuat</span>
                <span class="sort-indicator" data-for="created_at"></span>
              </button>
            </th>
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
        Total Layanan: <span class="font-medium" data-stat="total"><?= $stats['total'] ?></span>
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
              <p class="text-sm text-gray-500"><span data-stat="<?= strtolower($kategori) ?>"><?= $count ?></span> Layanan</p>
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
<div id="addLayananModal" class="fixed inset-0 bg-[#00000049] bg-opacity-50 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col modal-mobile modal-content-mobile">
      <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
        <h3 class="text-lg font-semibold text-gray-900">Tambah Layanan Informasi</h3>
      </div>
      <form id="addLayananForm" class="px-6 py-4 flex-1 overflow-y-auto modal-scroll">
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
            rows="6"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"
            placeholder="Masukkan deskripsi layanan..."
            required></textarea>
        </div>
        <div class="mb-6">
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
      </form>
      <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2 flex-shrink-0">
        <button
          type="button"
          id="cancelAddBtn"
          class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
          Cancel
        </button>
        <button
          type="submit"
          form="addLayananForm"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          Tambah Layanan
        </button>
      </div>
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

    // Sorting state management
    let currentSort = {
      sortBy: '',
      sortDir: ''
    };

    // Check if elements exist before using them
    if (!searchInput) {
      console.error('searchInput element not found');
      return;
    }
    if (!kategoriFilter) {
      console.error('kategoriFilter element not found');
      return;
    }
    if (!addBtn) {
      console.error('addBtn element not found');
      return;
    }
    if (!addModal) {
      console.error('addModal element not found');
      return;
    }
    if (!addForm) {
      console.error('addForm element not found');
      return;
    }

    // Search functionality - using AJAX like user management
    function performSearch() {
      const search = searchInput.value;
      const kategori = kategoriFilter.value;

      loadChatbotData(1, search, kategori, currentSort.sortBy, currentSort.sortDir);
    }

    // Sorting functionality
    function handleSort(sortBy) {
      console.log('Sorting clicked:', sortBy);
      console.log('Current sort before:', currentSort);

      if (currentSort.sortBy === sortBy) {
        // Toggle direction if same column
        currentSort.sortDir = currentSort.sortDir === 'asc' ? 'desc' : 'asc';
      } else {
        // New column, default to ascending
        currentSort.sortBy = sortBy;
        currentSort.sortDir = 'asc';
      }

      console.log('Current sort after:', currentSort);

      // Update sort indicators
      updateSortIndicators();

      // Reload data with new sorting
      const search = searchInput.value;
      const kategori = kategoriFilter.value;
      console.log('Loading data with sort:', currentSort.sortBy, currentSort.sortDir);
      loadChatbotData(1, search, kategori, currentSort.sortBy, currentSort.sortDir);
    }

    // Update sort indicators
    function updateSortIndicators() {
      // Clear all indicators
      document.querySelectorAll('.sort-indicator').forEach(indicator => {
        indicator.textContent = '';
      });

      // Set active indicator
      if (currentSort.sortBy) {
        const activeIndicator = document.querySelector(`.sort-indicator[data-for="${currentSort.sortBy}"]`);
        if (activeIndicator) {
          activeIndicator.textContent = currentSort.sortDir === 'desc' ? '▼' : '▲';
        }
      }
    }

    // Load chatbot data with AJAX
    function loadChatbotData(page = 1, search = '', kategori = '', sortBy = '', sortDir = '') {
      const params = new URLSearchParams({
        page: page,
        search: search,
        kategori: kategori
      });

      // Add sorting parameters if provided
      if (sortBy) params.append('sortBy', sortBy);
      if (sortDir) params.append('sortDir', sortDir);

      console.log('AJAX Request URL:', `<?= site_url('Admin/MasterData/chatbot') ?>?${params}`);
      console.log('Sorting params:', {
        sortBy,
        sortDir
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
            const chatbotTableBody = document.getElementById('chatbotTableBody');
            const chatbotPagination = document.getElementById('chatbotPagination');
            if (chatbotTableBody) {
              chatbotTableBody.innerHTML = data.html;
            }
            if (chatbotPagination) {
              chatbotPagination.innerHTML = data.pagination;
            }

            // Update stats jika ada
            if (data.stats) {
              updateStats(data.stats);
            }
          } else {
            console.error('Error loading data:', data.message);
            alert('Error loading data: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    // Load specific page - using AJAX
    window.loadChatbotPage = function(page) {
      const search = searchInput.value;
      const kategori = kategoriFilter.value;
      loadChatbotData(page, search, kategori, currentSort.sortBy, currentSort.sortDir);
    };

    // Update stats function
    function updateStats(stats) {
      // Update total count
      const totalElements = document.querySelectorAll('[data-stat="total"]');
      totalElements.forEach(el => {
        if (el.textContent !== undefined) {
          el.textContent = stats.total || 0;
        }
      });

      // Update category counts
      const akademikElements = document.querySelectorAll('[data-stat="akademik"]');
      akademikElements.forEach(el => {
        if (el.textContent !== undefined) {
          el.textContent = stats.akademik || 0;
        }
      });

      const administrasiElements = document.querySelectorAll('[data-stat="administrasi"]');
      administrasiElements.forEach(el => {
        if (el.textContent !== undefined) {
          el.textContent = stats.administrasi || 0;
        }
      });

      const umumElements = document.querySelectorAll('[data-stat="umum"]');
      umumElements.forEach(el => {
        if (el.textContent !== undefined) {
          el.textContent = stats.umum || 0;
        }
      });
    }

    // Event listeners
    if (searchBtn) {
      searchBtn.addEventListener('click', performSearch);
    }
    if (resetBtn) {
      resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        kategoriFilter.value = '';
        // Reset sorting
        currentSort.sortBy = '';
        currentSort.sortDir = '';
        updateSortIndicators();
        loadChatbotData(1);
      });
    }

    // Add search on input change - using AJAX
    searchInput.addEventListener('input', function() {
      clearTimeout(searchInput.searchTimeout);
      searchInput.searchTimeout = setTimeout(performSearch, 500);
    });

    // Add filter on change
    kategoriFilter.addEventListener('change', performSearch);

    // Add sorting event listeners
    document.addEventListener('click', function(e) {
      if (e.target.closest('.sort-btn')) {
        e.preventDefault();
        const sortBtn = e.target.closest('.sort-btn');
        const sortBy = sortBtn.getAttribute('data-sort-by');
        if (sortBy) {
          handleSort(sortBy);
        }
      }
    });

    // Add modal functionality
    if (addBtn) {
      addBtn.addEventListener('click', function() {
        // Reset form
        addForm.reset();
        document.querySelector('#addLayananModal h3').textContent = 'Tambah Layanan Informasi';
        const submitBtn = document.querySelector('#addLayananModal button[type="submit"]');
        if (submitBtn) {
          submitBtn.textContent = 'Tambah Layanan';
        }

        // Remove hidden input if exists
        const hiddenInput = document.getElementById('layanan_id');
        if (hiddenInput) {
          hiddenInput.remove();
        }

        addModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      });
    }

    if (cancelAddBtn) {
      cancelAddBtn.addEventListener('click', function() {
        addModal.classList.add('hidden');
        addForm.reset();
        document.body.style.overflow = 'auto';
      });
    }


    // Add/Edit form submission
    if (addForm) {
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
        const url = isEdit ? '<?= site_url('Admin/MasterData/updateLayananTest') ?>' : '<?= site_url('Admin/MasterData/addLayananTest') ?>';

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
              document.body.style.overflow = 'auto';

              // Reset form title and button
              document.querySelector('#addLayananModal h3').textContent = 'Tambah Layanan Informasi';
              const submitBtn = document.querySelector('#addLayananModal button[type="submit"]');
              if (submitBtn) {
                submitBtn.textContent = 'Tambah Layanan';
              }

              // Remove hidden input if exists
              const hiddenInput = document.getElementById('layanan_id');
              if (hiddenInput) {
                hiddenInput.remove();
              }

              // Show success message
              if (typeof window.showAlert === 'function') {
                window.showAlert('success', isEdit ? 'Layanan berhasil diperbarui!' : 'Layanan berhasil ditambahkan!');
              } else {
                alert(isEdit ? 'Layanan berhasil diperbarui!' : 'Layanan berhasil ditambahkan!');
              }

              // Reload data dengan AJAX seperti user management
              const search = searchInput.value;
              const kategori = kategoriFilter.value;
              loadChatbotData(1, search, kategori, currentSort.sortBy, currentSort.sortDir);
            } else {
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Error: ' + response.data.message);
              } else {
                alert('Error: ' + response.data.message);
              }
            }
          })
          .catch(function(error) {
            console.error('Error:', error);
            if (error.response) {
              console.error('Error response:', error.response.data);
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Error: ' + (error.response.data.message || error.message));
              } else {
                alert('Error: ' + (error.response.data.message || error.message));
              }
            } else {
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Terjadi kesalahan saat ' + (isEdit ? 'memperbarui' : 'menambahkan') + ' layanan: ' + error.message);
              } else {
                alert('Terjadi kesalahan saat ' + (isEdit ? 'memperbarui' : 'menambahkan') + ' layanan: ' + error.message);
              }
            }
          });
      });
    }

    // Close modal when clicking outside
    if (addModal) {
      addModal.addEventListener('click', function(e) {
        if (e.target === addModal) {
          addModal.classList.add('hidden');
        }
      });
    }

    // Event listeners untuk edit, delete, dan view layanan
    document.addEventListener('click', function(e) {
      // Edit layanan
      if (e.target.closest('.edit-layanan')) {
        e.preventDefault();
        const layananId = e.target.closest('.edit-layanan').dataset.id;
        console.log('Edit layanan ID:', layananId);
        editLayanan(layananId);
      }

      // Delete layanan
      if (e.target.closest('.delete-layanan')) {
        e.preventDefault();
        const layananId = e.target.closest('.delete-layanan').dataset.id;
        console.log('Delete layanan ID:', layananId);
        deleteLayanan(layananId);
      }

      // View layanan
      if (e.target.closest('.view-layanan')) {
        e.preventDefault();
        const layananId = e.target.closest('.view-layanan').dataset.id;
        console.log('View layanan ID:', layananId);
        viewLayanan(layananId);
      }
    });

    // Edit layanan function
    function editLayanan(layananId) {
      // Show loading state
      if (typeof window.showAlert === 'function') {
        window.showAlert('info', 'Memuat data layanan...', 2000);
      }

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
            const judulEl = document.getElementById('judul');
            const deskripsiEl = document.getElementById('deskripsi');
            const kategoriEl = document.getElementById('kategori');
            if (judulEl) judulEl.value = data.data.judul;
            if (deskripsiEl) deskripsiEl.value = data.data.deskripsi;
            if (kategoriEl) kategoriEl.value = data.data.kategori;

            // Change form title and submit button
            const modalTitle = document.querySelector('#addLayananModal h3');
            if (modalTitle) {
              modalTitle.textContent = 'Edit Layanan Informasi';
            }
            const submitBtn = document.querySelector('#addLayananModal button[type="submit"]');
            if (submitBtn) {
              submitBtn.textContent = 'Update Layanan';
            }

            // Add hidden input for layanan ID
            let hiddenInput = document.getElementById('layanan_id');
            if (!hiddenInput) {
              hiddenInput = document.createElement('input');
              hiddenInput.type = 'hidden';
              hiddenInput.id = 'layanan_id';
              hiddenInput.name = 'layanan_id';
              const addLayananForm = document.getElementById('addLayananForm');
              if (addLayananForm) {
                addLayananForm.appendChild(hiddenInput);
              }
            }
            hiddenInput.value = layananId;

            // Show modal
            addModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
          } else {
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Error: ' + data.message);
            } else {
              alert('Error: ' + data.message);
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (typeof window.showAlert === 'function') {
            window.showAlert('error', 'Terjadi kesalahan saat mengambil data layanan');
          } else {
            alert('Terjadi kesalahan saat mengambil data layanan');
          }
        });
    }

    // Delete layanan function with custom confirmation
    function deleteLayanan(layananId) {
      // Use custom confirmation dialog
      if (typeof window.showConfirm === 'function') {
        window.showConfirm(
          'Hapus Layanan',
          'Apakah Anda yakin ingin menghapus layanan ini? Tindakan ini tidak dapat dibatalkan.',
          function() {
            // On Confirm - proceed with deletion
            performDelete(layananId);
          },
          function() {
            // On Cancel - do nothing
            console.log('User membatalkan penghapusan layanan');
          }
        );
      } else {
        // Fallback to browser confirm
        if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
          performDelete(layananId);
        }
      }
    }

    // Perform actual deletion
    function performDelete(layananId) {
      // Show loading state
      if (typeof window.showAlert === 'function') {
        window.showAlert('info', 'Menghapus layanan...', 2000);
      }

      // Get CSRF token from meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      // Create URLSearchParams
      const params = new URLSearchParams();
      params.append('layanan_id', layananId);
      params.append('csrf_test_name', csrfToken);

      console.log('Delete - CSRF Token:', csrfToken);
      console.log('Delete - Layanan ID:', layananId);

      // Submit via Axios
      axios.post('<?= site_url('Admin/MasterData/deleteLayananTest') ?>', params.toString(), {
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
            // Show success message
            if (typeof window.showAlert === 'function') {
              window.showAlert('success', 'Layanan berhasil dihapus!');
            } else {
              alert('Layanan berhasil dihapus!');
            }

            // Reload data dengan AJAX seperti user management
            const search = searchInput.value;
            const kategori = kategoriFilter.value;
            loadChatbotData(1, search, kategori, currentSort.sortBy, currentSort.sortDir);
          } else {
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Error: ' + response.data.message);
            } else {
              alert('Error: ' + response.data.message);
            }
          }
        })
        .catch(function(error) {
          console.error('Delete error:', error);
          if (error.response) {
            console.error('Delete error response:', error.response.data);
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Error: ' + (error.response.data.message || error.message));
            } else {
              alert('Error: ' + (error.response.data.message || error.message));
            }
          } else {
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Terjadi kesalahan saat menghapus layanan: ' + error.message);
            } else {
              alert('Terjadi kesalahan saat menghapus layanan: ' + error.message);
            }
          }
        });
    }

    // View layanan function with custom modal
    function viewLayanan(layananId) {
      // Show loading state
      if (typeof window.showAlert === 'function') {
        window.showAlert('info', 'Memuat data layanan...', 2000);
      }

      fetch(`<?= site_url('Admin/MasterData/getLayanan') ?>?id=${layananId}`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show view modal with custom styling
            showViewModal(data.data);
          } else {
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Error: ' + data.message);
            } else {
              alert('Error: ' + data.message);
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (typeof window.showAlert === 'function') {
            window.showAlert('error', 'Terjadi kesalahan saat mengambil data layanan');
          } else {
            alert('Terjadi kesalahan saat mengambil data layanan');
          }
        });
    }

    // Show view modal function
    function showViewModal(data) {
      // Create modal if it doesn't exist
      let viewModal = document.getElementById('viewLayananModal');
      if (!viewModal) {
        viewModal = document.createElement('div');
        viewModal.id = 'viewLayananModal';
        viewModal.className = 'fixed inset-0 bg-[#00000049] bg-opacity-50 hidden z-50';
        viewModal.innerHTML = `
          <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col modal-mobile modal-content-mobile">
              <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold text-gray-900">Detail Layanan Informasi</h3>
                  <button type="button" id="closeViewModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-xl"></i>
                  </button>
                </div>
              </div>
              <div class="px-6 py-4 flex-1 overflow-y-auto modal-scroll">
                <div class="space-y-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Layanan</label>
                    <div class="text-sm text-gray-900 font-medium break-words" id="view-judul"></div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <div class="text-sm" id="view-kategori"></div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Layanan</label>
                    <div class="text-sm text-gray-900 whitespace-pre-wrap break-words max-h-96 overflow-y-auto bg-gray-50 p-4 rounded-lg border modal-scroll" id="view-deskripsi"></div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dibuat</label>
                    <div class="text-sm text-gray-500" id="view-created"></div>
                  </div>
                </div>
              </div>
              <div class="px-6 py-4 border-t border-gray-200 flex justify-end flex-shrink-0">
                <button type="button" id="closeViewModalBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                  Tutup
                </button>
              </div>
            </div>
          </div>
        `;
        document.body.appendChild(viewModal);

        // Add event listeners for close buttons
        const closeViewModalEl = document.getElementById('closeViewModal');
        const closeViewModalBtnEl = document.getElementById('closeViewModalBtn');
        if (closeViewModalEl) {
          closeViewModalEl.addEventListener('click', closeViewModal);
        }
        if (closeViewModalBtnEl) {
          closeViewModalBtnEl.addEventListener('click', closeViewModal);
        }

        // Close modal when clicking outside
        viewModal.addEventListener('click', function(e) {
          if (e.target === viewModal) {
            closeViewModal();
          }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && !viewModal.classList.contains('hidden')) {
            closeViewModal();
          }
        });
      }

      // Populate modal with data
      const viewJudul = document.getElementById('view-judul');
      const viewDeskripsi = document.getElementById('view-deskripsi');
      const viewCreated = document.getElementById('view-created');
      if (viewJudul) viewJudul.textContent = data.judul;
      if (viewDeskripsi) viewDeskripsi.textContent = data.deskripsi;
      if (viewCreated) viewCreated.textContent = new Date(data.created_at).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });

      // Set kategori with color
      const kategoriElement = document.getElementById('view-kategori');
      const kategoriColors = {
        'Akademik': 'bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold',
        'Administrasi': 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold',
        'Umum': 'bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-semibold'
      };
      const colorClass = kategoriColors[data.kategori] || 'bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
      kategoriElement.innerHTML = `<span class="${colorClass}">${data.kategori}</span>`;

      // Show modal
      viewModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';

      function closeViewModal() {
        viewModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
    }

    // Filter by category functionality - using AJAX
    document.addEventListener('click', function(e) {
      if (e.target.closest('.filter-by-category')) {
        const kategori = e.target.closest('.filter-by-category').dataset.kategori;
        kategoriFilter.value = kategori;
        loadChatbotData(1, searchInput.value, kategori, currentSort.sortBy, currentSort.sortDir);
      }
    });

    // View category functionality - using AJAX
    document.addEventListener('click', function(e) {
      if (e.target.closest('.view-category')) {
        const kategori = e.target.closest('.view-category').dataset.kategori;
        kategoriFilter.value = kategori;
        searchInput.value = '';
        loadChatbotData(1, '', kategori, currentSort.sortBy, currentSort.sortDir);
      }
    });
  });
</script>

<?= $this->endSection() ?>