/*
 * Router SPA ringan (gaya PJAX) untuk tampilan CI4
 * - Mencegat klik link internal
 * - Mengambil HTML penuh dan mengganti isi .content saja
 * - Memperbarui history dan menjalankan ulang skrip pada konten baru
 */
(function () {
  const CONTENT_SELECTOR = ".content";
  const LINK_SELECTOR = "a[href]";
  const CACHE_PREFIX = "spa_cache:"; // key localStorage per URL

  function getCacheKey(url) {
    try {
      const u = new URL(url, window.location.origin);
      return `${CACHE_PREFIX}${u.pathname}${u.search}`;
    } catch (_) {
      return `${CACHE_PREFIX}${url}`;
    }
  }

  function saveContentToCache(url, html, title) {
    try {
      const key = getCacheKey(url);
      const payload = {
        html,
        title: title || document.title,
        ts: Date.now(),
      };
      localStorage.setItem(key, JSON.stringify(payload));
    } catch (e) {
      // abaikan jika storage penuh/private mode
    }
  }

  function loadContentFromCache(url) {
    try {
      const raw = localStorage.getItem(getCacheKey(url));
      if (!raw) return null;
      return JSON.parse(raw);
    } catch (_) {
      return null;
    }
  }

  function showOfflineBanner(target) {
    const host =
      target || document.querySelector(CONTENT_SELECTOR) || document.body;
    const banner = document.createElement("div");
    banner.setAttribute("data-offline-banner", "true");
    banner.style.background = "#fef3c7";
    banner.style.border = "1px solid #f59e0b";
    banner.style.color = "#92400e";
    banner.style.padding = "10px 12px";
    banner.style.borderRadius = "6px";
    banner.style.margin = "8px 0 16px";
    banner.style.fontSize = "14px";
    banner.textContent = "Maaf, koneksi internet Anda offline.";
    host.prepend(banner);
  }

  // Cek apakah link adalah link internal (domain yang sama)
  function isInternalLink(anchor) {
    if (!anchor || anchor.target === "_blank") return false;
    const url = new URL(anchor.href, window.location.origin);
    return url.origin === window.location.origin;
  }

  // Pasang handler klik untuk semua link dalam container
  function attachLinkHandlers(root) {
    const container = root || document;
    container.querySelectorAll(LINK_SELECTOR).forEach((a) => {
      a.addEventListener("click", onLinkClick);
    });
  }

  // Handler saat link diklik: cegat, lalu navigasi via AJAX
  async function onLinkClick(e) {
    const a = e.currentTarget;
    if (!isInternalLink(a)) return;
    if (
      a.hasAttribute("download") ||
      a.getAttribute("href").startsWith("#") ||
      a.dataset.noSpa === "true"
    )
      return;
    e.preventDefault();
    const url = a.href;
    navigate(url, true);
  }

  // Tandai menu navigasi yang aktif berdasarkan URL
  function updateActiveNav(url) {
    document.querySelectorAll("a.site").forEach((el) => {
      if (el.classList.contains("aktif")) el.classList.remove("aktif");
      try {
        const href = new URL(el.href, location.origin).pathname;
        const target = new URL(url, location.origin).pathname;
        // Pemetaan custom: child-path -> parent-menu
        const groupMap = {
          "/Likes": "/Profile",
          "/Notifications": "/Profile",
          // Admin navigation mapping
          "/Admin/MasterData/users": "/Admin/MasterData",
          "/Admin/MasterData/chatbot": "/Admin/MasterData",
          "/Admin/MasterData": "/Admin/MasterData",
          "/Admin/Dashboard": "/Admin/Dashboard",
          "/Admin/Mading": "/Admin/Mading",
          "/Admin/Reports": "/Admin/Reports",
          "/Admin/Settings": "/Admin/Settings",
          // User navigation mapping
          "/Mading/detail": "/Mading",
        };

        // Default behavior: exact or prefix match
        if (href === target || target.startsWith(href)) {
          el.classList.add("aktif");
          return;
        }
        // Terapkan group mapping
        for (const [child, parent] of Object.entries(groupMap)) {
          if (target.startsWith(child) && href.startsWith(parent)) {
            el.classList.add("aktif");
            return;
          }
        }
      } catch (_) {}
    });
  }

  // Jalankan ulang semua <script> yang ada pada konten baru
  function executeScriptsFrom(node) {
    const scripts = node.querySelectorAll("script");
    scripts.forEach((oldScript) => {
      const newScript = document.createElement("script");
      // Salin semua atribut (type, src, dll.)
      [...oldScript.attributes].forEach((attr) =>
        newScript.setAttribute(attr.name, attr.value)
      );
      if (oldScript.src) {
        // Skrip eksternal
        newScript.src = oldScript.src;
        document.body.appendChild(newScript);
      } else {
        // Skrip inline
        newScript.textContent = oldScript.textContent;
        document.body.appendChild(newScript);
      }
      // Hapus skrip temp setelah dieksekusi
      setTimeout(
        () =>
          newScript.parentNode && newScript.parentNode.removeChild(newScript),
        0
      );
    });
  }

  // Muat halaman secara asinkron dan ganti isi .content saja
  async function navigate(url, push) {
    const contentEl = document.querySelector(CONTENT_SELECTOR);
    if (!contentEl) {
      window.location.href = url; // fallback
      return;
    }

    // Check if this is an admin URL - now support SPA navigation
    if (url.includes("/Admin/")) {
      console.log("[spa-router] Admin URL detected, using SPA navigation");
      // Continue with SPA navigation instead of full page reload
    }
    contentEl.style.opacity = "0.5";
    try {
      // Gunakan instance axios global (window.api) jika ada, jika tidak fallback ke axios default
      const res = await (window.api ? window.api.get(url) : axios.get(url));
      const html =
        typeof res.data === "string" ? res.data : res.request.responseText;
      const doc = new DOMParser().parseFromString(html, "text/html");
      const newContent = doc.querySelector(CONTENT_SELECTOR);
      const newTitle = doc.querySelector("title");

      // Debug logging
      console.log("SPA Router - URL:", url);
      console.log("SPA Router - Content found:", !!newContent);
      console.log("SPA Router - HTML length:", html.length);

      if (!newContent) {
        console.error(
          "SPA Router - Content not found. Available elements:",
          Array.from(doc.querySelectorAll("*"))
            .map((el) => el.className)
            .filter((c) => c)
        );
        console.error(
          "SPA Router - Looking for .content, found elements with classes:",
          Array.from(doc.querySelectorAll("*"))
            .map((el) => el.className)
            .filter((c) => c && c.includes("content"))
        );
        console.error(
          "SPA Router - Full HTML preview:",
          html.substring(0, 500) + "..."
        );
        throw new Error(
          "Kontainer konten (.content) tidak ditemukan pada response"
        );
      }
      // Ganti konten
      contentEl.innerHTML = newContent.innerHTML;
      // Perbarui judul halaman
      if (newTitle) document.title = newTitle.textContent;
      // Simpan ke cache untuk offline
      saveContentToCache(url, contentEl.innerHTML, document.title);
      // Perbarui riwayat browser (Back/Forward akan bekerja)
      if (push) history.pushState({ url }, "", url);
      // Pasang ulang handler untuk link baru
      attachLinkHandlers(contentEl);
      // Jalankan ulang script di dalam konten (untuk fitur per-halaman)
      executeScriptsFrom(contentEl);
      // Perbarui status menu aktif
      updateActiveNav(url);
      // Sinkronkan badge/dot notifikasi setelah navigasi SPA (hanya jika user sudah login)
      if (
        window.__refreshNotifCount &&
        document.querySelector('meta[name="csrf-token"]')
      ) {
        try {
          window.__refreshNotifCount();
        } catch (_) {}
      }
      // Scroll ke atas agar pengalaman navigasi konsisten
      window.scrollTo({ top: 0, behavior: "smooth" });
    } catch (err) {
      console.error("[spa-router] gagal memuat halaman:", err);
      // Jika offline atau fetch gagal, coba tampilkan dari cache
      const cached = loadContentFromCache(url);
      if (cached && cached.html) {
        contentEl.innerHTML = cached.html;
        if (cached.title) document.title = cached.title;
        if (push) history.pushState({ url }, "", url);
        attachLinkHandlers(contentEl);
        executeScriptsFrom(contentEl);
        updateActiveNav(url);
        showOfflineBanner(contentEl);
        if (window.showAlert && navigator && navigator.onLine === false) {
          window.showAlert("warning", "Anda dalam mode offline", 3000);
        }
      } else {
        // Tidak ada cache, lakukan hard navigate sebagai jalan terakhir
        window.location.href = url;
      }
    } finally {
      contentEl.style.opacity = "";
    }
  }

  // Dukung tombol Back/Forward browser
  window.addEventListener("popstate", (e) => {
    const url = (e.state && e.state.url) || location.href;
    navigate(url, false);
  });

  document.addEventListener("DOMContentLoaded", () => {
    attachLinkHandlers(document); // Pasang handler pertama kali saat halaman dimuat
    // Simpan konten awal ke cache untuk URL saat ini
    const initialContent = document.querySelector(CONTENT_SELECTOR);
    if (initialContent) {
      saveContentToCache(
        location.href,
        initialContent.innerHTML,
        document.title
      );
      if (navigator && navigator.onLine === false) {
        showOfflineBanner(initialContent);
        if (window.showAlert) window.showAlert("warning", "Anda offline", 3000);
      }
    }
  });

  // Ekspos fungsi untuk memasang handler pada kontainer yang baru disisipkan (mis. lazy load)
  window.__spaAttachLinks = attachLinkHandlers;
  window.__spaNavigate = navigate;

  // Notifikasi perubahan status online/offline
  window.addEventListener("online", () => {
    const banner = document.querySelector('[data-offline-banner="true"]');
    if (banner) banner.remove();
    if (window.showAlert) window.showAlert("success", "Kembali online", 2500);
  });
  window.addEventListener("offline", () => {
    showOfflineBanner();
    if (window.showAlert) window.showAlert("warning", "Anda offline", 3000);
  });
})();
