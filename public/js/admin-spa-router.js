/*
 * Router SPA ringan khusus area Admin
 * - Mencegat klik link internal di bawah /admin
 * - Mengambil HTML dan replace isi .content
 * - Update history & jalankan ulang script
 */
(function () {
  const CONTENT_SELECTOR = ".content";
  const LINK_SELECTOR = "a[href]";
  const ADMIN_PREFIX = "/Admin";
  const CACHE_PREFIX = "admin_spa_cache:";

  function isAdminInternalLink(anchor) {
    if (!anchor || anchor.target === "_blank") return false;
    const url = new URL(anchor.href, window.location.origin);
    // Exclude certain admin paths from SPA handling (force full reload)
    const EXCLUDE_PATH_PREFIXES = [
      "/Admin/MasterData/users", // Master Data Users harus full reload
    ];

    const isExcluded = EXCLUDE_PATH_PREFIXES.some((p) =>
      url.pathname.startsWith(p)
    );

    return (
      url.origin === window.location.origin &&
      url.pathname.startsWith(ADMIN_PREFIX) &&
      !isExcluded
    );
  }

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
      localStorage.setItem(
        key,
        JSON.stringify({ html, title: title || document.title, ts: Date.now() })
      );
    } catch (_) {}
  }

  function loadContentFromCache(url) {
    try {
      const raw = localStorage.getItem(getCacheKey(url));
      return raw ? JSON.parse(raw) : null;
    } catch (_) {
      return null;
    }
  }

  function attachLinkHandlers(root) {
    const container = root || document;
    container.querySelectorAll(LINK_SELECTOR).forEach((a) => {
      a.addEventListener("click", onLinkClick);
    });
  }

  async function onLinkClick(e) {
    const a = e.currentTarget;
    if (!isAdminInternalLink(a)) return;
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

        // Admin navigation mapping
        const groupMap = {
          "/Admin/MasterData/users": "/Admin/MasterData",
          "/Admin/MasterData/chatbot": "/Admin/MasterData",
          "/Admin/Dashboard": "/Admin/Dashboard",
          "/Admin/Mading": "/Admin/Mading",
          "/Admin/Reports": "/Admin/Reports",
          "/Admin/Settings": "/Admin/Settings",
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

  function executeScriptsFrom(node) {
    const scripts = node.querySelectorAll("script");
    scripts.forEach((oldScript) => {
      const newScript = document.createElement("script");
      [...oldScript.attributes].forEach((attr) =>
        newScript.setAttribute(attr.name, attr.value)
      );
      if (oldScript.src) {
        newScript.src = oldScript.src;
        document.body.appendChild(newScript);
      } else {
        newScript.textContent = oldScript.textContent;
        document.body.appendChild(newScript);
      }
      setTimeout(
        () =>
          newScript.parentNode && newScript.parentNode.removeChild(newScript),
        0
      );
    });
  }

  async function navigate(url, push) {
    const contentEl = document.querySelector(CONTENT_SELECTOR);
    if (!contentEl) {
      window.location.href = url;
      return;
    }
    contentEl.style.opacity = "0.5";
    try {
      const res = await (window.api ? window.api.get(url) : axios.get(url));
      const html =
        typeof res.data === "string" ? res.data : res.request.responseText;
      const doc = new DOMParser().parseFromString(html, "text/html");
      const newContent = doc.querySelector(CONTENT_SELECTOR);
      const newTitle = doc.querySelector("title");
      if (!newContent)
        throw new Error(
          "Kontainer konten (.content) tidak ditemukan pada response"
        );
      contentEl.innerHTML = newContent.innerHTML;
      if (newTitle) document.title = newTitle.textContent;
      saveContentToCache(url, contentEl.innerHTML, document.title);
      if (push) history.pushState({ url }, "", url);
      attachLinkHandlers(contentEl);
      executeScriptsFrom(contentEl);
      updateActiveNav(url);

      // Re-initialize admin search filter if available
      if (typeof window.initializeAdminSearchFilter === "function") {
        setTimeout(() => {
          window.initializeAdminSearchFilter();
        }, 100);
      }

      // Re-initialize add user modal if available
      if (typeof window.initializeAddUserModal === "function") {
        setTimeout(() => {
          window.initializeAddUserModal();
        }, 200);
      }

      // Re-initialize pagination if available
      if (typeof window.initializePagination === "function") {
        setTimeout(() => {
          window.initializePagination();
        }, 300);
      }

      window.scrollTo({ top: 0, behavior: "smooth" });
    } catch (err) {
      const cached = loadContentFromCache(url);
      if (cached && cached.html) {
        contentEl.innerHTML = cached.html;
        if (cached.title) document.title = cached.title;
        if (push) history.pushState({ url }, "", url);
        attachLinkHandlers(contentEl);
        executeScriptsFrom(contentEl);
        updateActiveNav(url);

        // Re-initialize admin search filter if available
        if (typeof window.initializeAdminSearchFilter === "function") {
          setTimeout(() => {
            window.initializeAdminSearchFilter();
          }, 100);
        }

        // Re-initialize add user modal if available
        if (typeof window.initializeAddUserModal === "function") {
          setTimeout(() => {
            window.initializeAddUserModal();
          }, 200);
        }

        // Re-initialize pagination if available
        if (typeof window.initializePagination === "function") {
          setTimeout(() => {
            window.initializePagination();
          }, 300);
        }
      } else {
        window.location.href = url;
      }
    } finally {
      contentEl.style.opacity = "";
    }
  }

  window.addEventListener("popstate", (e) => {
    const url = (e.state && e.state.url) || location.href;
    if (new URL(url, location.origin).pathname.startsWith(ADMIN_PREFIX)) {
      navigate(url, false);
    }
  });

  document.addEventListener("DOMContentLoaded", () => {
    if (location.pathname.startsWith(ADMIN_PREFIX)) {
      attachLinkHandlers(document);
      const initialContent = document.querySelector(CONTENT_SELECTOR);
      if (initialContent) {
        saveContentToCache(
          location.href,
          initialContent.innerHTML,
          document.title
        );
      }
    }
  });

  // Ekspos fungsi untuk navigasi programatik
  window.__adminSpaAttachLinks = attachLinkHandlers;
  window.__adminSpaNavigate = navigate;
})();
