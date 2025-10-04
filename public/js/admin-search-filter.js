/**
 * Admin Search & Filter System
 * Reusable JavaScript untuk search dan filter di halaman admin
 * Menggunakan Axios untuk AJAX requests tanpa reload
 */

// Check if AdminSearchFilter already exists
if (typeof AdminSearchFilter !== "undefined") {
  console.warn("AdminSearchFilter already exists, skipping redefinition");
} else {
  class AdminSearchFilter {
    constructor(options = {}) {
      this.options = {
        // Default options
        searchInput: ".search-input",
        statusSelect: ".status-select",
        jurusanSelect: ".jurusan-select",
        resultsContainer: ".results-container",
        paginationContainer: ".pagination-container",
        loadingClass: "loading",
        baseUrl: window.location.pathname,
        debounceDelay: 500,
        autoLoad: true, // kontrol apakah akan load data di initial render
        ...options,
      };

      this.currentFilters = {
        search: "",
        status: "",
        jurusan: "",
        page: 1,
        sortBy: "",
        sortDir: "",
      };

      this.debounceTimer = null;
      this.init();
    }

    init() {
      this.bindEvents();
      if (this.options.autoLoad) {
        this.loadInitialData();
      }
    }

    bindEvents() {
      // Delegated search input (robust untuk SSR/SPA)
      if (window.__adminSearchInputHandler) {
        document.removeEventListener("input", window.__adminSearchInputHandler);
      }
      window.__adminSearchInputHandler = (e) => {
        if (e.target && e.target.matches(this.options.searchInput)) {
          this.debounce(() => {
            this.currentFilters.search = e.target.value;
            this.currentFilters.page = 1;
            this.performSearch();
          });
        }
      };
      document.addEventListener("input", window.__adminSearchInputHandler);

      // Delegated status select
      if (window.__adminStatusChangeHandler) {
        document.removeEventListener(
          "change",
          window.__adminStatusChangeHandler
        );
      }
      window.__adminStatusChangeHandler = (e) => {
        if (e.target && e.target.matches(this.options.statusSelect)) {
          this.currentFilters.status = e.target.value;
          this.currentFilters.page = 1;
          this.performSearch();
        }
      };
      document.addEventListener("change", window.__adminStatusChangeHandler);

      // Delegated jurusan select
      if (window.__adminJurusanChangeHandler) {
        document.removeEventListener(
          "change",
          window.__adminJurusanChangeHandler
        );
      }
      window.__adminJurusanChangeHandler = (e) => {
        if (e.target && e.target.matches(this.options.jurusanSelect)) {
          this.currentFilters.jurusan = e.target.value;
          this.currentFilters.page = 1;
          this.performSearch();
        }
      };
      document.addEventListener("change", window.__adminJurusanChangeHandler);

      // Delegated pagination clicks
      if (window.__adminPaginationClickHandler) {
        document.removeEventListener(
          "click",
          window.__adminPaginationClickHandler
        );
      }
      window.__adminPaginationClickHandler = (e) => {
        const target = e.target.closest && e.target.closest(".pagination-link");
        if (target) {
          e.preventDefault();
          const page = parseInt(target.dataset.page);
          if (page && page !== this.currentFilters.page) {
            this.currentFilters.page = page;
            this.performSearch();
          }
        }
      };
      document.addEventListener("click", window.__adminPaginationClickHandler);
    }

    debounce(func) {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(func, this.options.debounceDelay);
    }

    async performSearch() {
      this.showLoading();

      try {
        const params = new URLSearchParams();
        Object.keys(this.currentFilters).forEach((key) => {
          if (this.currentFilters[key]) {
            params.append(key, this.currentFilters[key]);
          }
        });

        // Gunakan baseUrl dari container jika tersedia (untuk SSR/SPA safety)
        let baseUrl = this.options.baseUrl;
        const rc = document.querySelector(this.options.resultsContainer);
        if (rc && rc.getAttribute("data-base-url")) {
          baseUrl = rc.getAttribute("data-base-url");
        }

        const url = `${baseUrl}?${params.toString()}`;
        console.log(
          "[AdminSearchFilter] performSearch url:",
          url,
          "filters:",
          this.currentFilters
        );
        const response = await axios.get(url);

        if (response.data.success !== false) {
          this.updateResults(response.data);
        } else {
          this.showError("Gagal memuat data");
        }
      } catch (error) {
        console.error("Search error:", error);
        this.showError("Terjadi kesalahan saat memuat data");
      } finally {
        this.hideLoading();
      }
    }

    updateResults(data) {
      // Update tbody only
      const tbody = document.querySelector("#users-tbody");
      if (tbody && data.html) {
        tbody.innerHTML = data.html;
      }

      // Update pagination
      const paginationContainer = document.querySelector(
        this.options.paginationContainer
      );
      if (paginationContainer && data.pagination) {
        paginationContainer.innerHTML = data.pagination;
      }

      // Update statistics if available
      if (data.stats) {
        this.updateStatistics(data.stats);
      }

      // Update URL without reload
      this.updateURL();
    }

    updateStatistics(stats) {
      // Update stat cards jika ada
      const statElements = {
        total: ".stat-total",
        active: ".stat-active",
        pending: ".stat-pending",
        inactive: ".stat-inactive",
      };

      Object.keys(statElements).forEach((key) => {
        const element = document.querySelector(statElements[key]);
        if (element && stats[key]) {
          element.textContent = stats[key];
        }
      });
    }

    updateURL() {
      const params = new URLSearchParams();
      Object.keys(this.currentFilters).forEach((key) => {
        if (this.currentFilters[key]) {
          params.append(key, this.currentFilters[key]);
        }
      });

      const newUrl = `${this.options.baseUrl}?${params.toString()}`;
      window.history.pushState({}, "", newUrl);
    }

    showLoading() {
      const resultsContainer = document.querySelector(
        this.options.resultsContainer
      );
      if (resultsContainer) {
        resultsContainer.classList.add(this.options.loadingClass);
      }
    }

    hideLoading() {
      const resultsContainer = document.querySelector(
        this.options.resultsContainer
      );
      if (resultsContainer) {
        resultsContainer.classList.remove(this.options.loadingClass);
      }
    }

    showError(message) {
      // Simple error display - bisa dikustomisasi
      const resultsContainer = document.querySelector(
        this.options.resultsContainer
      );
      if (resultsContainer) {
        resultsContainer.innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="ri-error-warning-line text-4xl mb-2"></i>
                    <p>${message}</p>
                </div>
            `;
      }
    }

    loadInitialData() {
      // Load data saat pertama kali halaman dimuat
      this.performSearch();
    }

    // Public methods untuk external use
    setFilter(key, value) {
      this.currentFilters[key] = value;
      this.currentFilters.page = 1;
      this.performSearch();
    }

    // Sorting handler
    setSort(sortBy) {
      if (!sortBy) return;
      if (this.currentFilters.sortBy === sortBy) {
        this.currentFilters.sortDir =
          this.currentFilters.sortDir === "asc" ? "desc" : "asc";
      } else {
        this.currentFilters.sortBy = sortBy;
        this.currentFilters.sortDir = "asc";
      }
      console.log(
        "[AdminSearchFilter] setSort:",
        this.currentFilters.sortBy,
        this.currentFilters.sortDir
      );
      this.currentFilters.page = 1;
      this.performSearch();
    }

    resetFilters() {
      this.currentFilters = {
        search: "",
        status: "",
        jurusan: "",
        page: 1,
      };

      // Reset form inputs
      const searchInput = document.querySelector(this.options.searchInput);
      if (searchInput) searchInput.value = "";

      const statusSelect = document.querySelector(this.options.statusSelect);
      if (statusSelect) statusSelect.value = "";

      const jurusanSelect = document.querySelector(this.options.jurusanSelect);
      if (jurusanSelect) jurusanSelect.value = "";

      this.performSearch();
    }
  }

  // Utility functions
  const AdminSearchUtils = {
    // Format number dengan separator
    formatNumber: (num) => {
      return new Intl.NumberFormat("id-ID").format(num);
    },

    // Format date
    formatDate: (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },

    // Show notification
    showNotification: (message, type = "info") => {
      // Simple notification - bisa dikustomisasi dengan library lain
      const notification = document.createElement("div");
      notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === "success"
          ? "bg-green-500"
          : type === "error"
          ? "bg-red-500"
          : "bg-blue-500"
      } text-white`;
      notification.textContent = message;

      document.body.appendChild(notification);

      setTimeout(() => {
        notification.remove();
      }, 3000);
    },

    // Debounce function
    debounce: (func, wait) => {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    },
  };

  // Auto-initialize function for SPA compatibility
  function initializeAdminSearchFilter() {
    // Cek apakah ada search form di halaman
    const hasSearchUI =
      document.querySelector(".search-input") ||
      document.querySelector(".status-select") ||
      document.querySelector(".jurusan-select");
    if (!hasSearchUI) return;

    // Destroy existing instance if any
    if (window.adminSearchFilter) {
      window.adminSearchFilter = null;
    }

    // Ambil baseUrl dari container jika ada
    const rc = document.querySelector(".results-container");
    const baseUrl =
      rc && rc.getAttribute("data-base-url")
        ? rc.getAttribute("data-base-url")
        : window.location.pathname;

    window.adminSearchFilter = new AdminSearchFilter({
      searchInput: ".search-input",
      statusSelect: ".status-select",
      jurusanSelect: ".jurusan-select",
      resultsContainer: ".results-container",
      paginationContainer: ".pagination-container",
      baseUrl: baseUrl,
      autoLoad: false, // gunakan data SSR; AJAX hanya saat interaksi
    });

    // Pastikan results container tidak dalam state loading
    if (rc) rc.classList.remove("loading");

    console.log("AdminSearchFilter initialized for SPA", { baseUrl });
  }

  // Auto-initialize on DOM ready (respect global disable flag)
  document.addEventListener("DOMContentLoaded", function () {
    if (window.__DISABLE_ADMIN_SEARCH_AUTOINIT) {
      return;
    }
    initializeAdminSearchFilter();
  });

  // Re-initialize when SPA content changes
  if (typeof window.__adminSpaAttachLinks === "function") {
    const originalAttachLinks = window.__adminSpaAttachLinks;
    window.__adminSpaAttachLinks = function (root) {
      originalAttachLinks(root);
      // Re-initialize search filter after SPA navigation
      setTimeout(initializeAdminSearchFilter, 100);
    };
  }

  // Expose initialization function for manual use
  window.initializeAdminSearchFilter = initializeAdminSearchFilter;

  // Export untuk penggunaan manual
  window.AdminSearchFilter = AdminSearchFilter;
  window.AdminSearchUtils = AdminSearchUtils;
} // End of if check
