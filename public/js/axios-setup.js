/*
 * Setup global Axios dengan dukungan CSRF untuk CodeIgniter 4
 * - Membaca token CSRF dari meta tag
 * - Menyematkan token ke header setiap request
 * - Mengambil token CSRF baru dari response (jika disediakan backend)
 * - Mengekspos instance Axios sebagai window.api untuk dipakai di seluruh halaman
 */
(function () {
  if (typeof axios === "undefined") {
    console.error(
      "[axios-setup] Axios belum termuat. Pastikan skrip axios disertakan sebelum file ini."
    );
    return;
  }

  const CSRF_HEADER = "X-CSRF-TOKEN";
  // Ambil token CSRF awal dari meta tag
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  let csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";

  // Base URL untuk API (diambil dari meta, fallback ke '/')
  const baseURL =
    document.querySelector('meta[name="base-url"]')?.getAttribute("content") ||
    "/";

  const api = axios.create({
    baseURL: baseURL.replace(/\/$/, ""),
    timeout: 15000,
    headers: {
      [CSRF_HEADER]: csrfToken, // Sertakan header CSRF pada setiap request
      "X-Requested-With": "XMLHttpRequest", // Tandai sebagai AJAX agar backend merespons JSON
    },
  });

  // Pastikan juga axios global (jika dipakai langsung) memakai header AJAX
  axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

  // Perbarui token CSRF jika backend mengirimkan token baru di response
  api.interceptors.response.use((response) => {
    const data = response && response.data;
    const newToken = data && (data.csrf || data.csrf_hash);
    if (newToken) {
      csrfToken = newToken;
      if (csrfMeta) csrfMeta.setAttribute("content", newToken); // Update meta agar form non-Axios juga ikut terbaru
      api.defaults.headers[CSRF_HEADER] = newToken; // Update header default untuk request selanjutnya
    }
    return response;
  });

  // Ekspos secara global agar mudah dipakai: window.api.get/post/dll
  window.api = api;
})();
