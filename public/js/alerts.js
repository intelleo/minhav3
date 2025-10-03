/*
 * Sistem alert global untuk menampilkan notifikasi (sukses/error/info) dan konfirmasi
 * - Kompatibel dengan SPA (konten berganti) dan Axios
 * - API: window.showAlert(type, message, timeoutMs)
 * - API: window.showConfirm(title, message, onConfirm, onCancel)
 * - type: 'success' | 'error' | 'info' | 'warning'
 */
(function () {
  const CONTAINER_ID = "global-alerts";

  function ensureContainer() {
    let el = document.getElementById(CONTAINER_ID);
    if (!el) {
      el = document.createElement("div");
      el.id = CONTAINER_ID;
      el.style.position = "fixed";
      el.style.top = "16px";
      el.style.right = "16px";
      el.style.zIndex = "9999";
      el.style.display = "flex";
      el.style.flexDirection = "column";
      el.style.gap = "8px";
      document.body.appendChild(el);
    }
    return el;
  }

  function makeAlertElement(type, message) {
    const wrap = document.createElement("div");
    wrap.className = "shadow-md rounded-lg px-4 py-3 text-sm transition-all";
    wrap.style.maxWidth = "420px";
    wrap.style.opacity = "0";

    const color =
      type === "success"
        ? ["#dcfce7", "#16a34a"]
        : type === "error"
        ? ["#fee2e2", "#dc2626"]
        : type === "warning"
        ? ["#fef9c3", "#ca8a04"]
        : ["#e0f2fe", "#0284c7"];

    wrap.style.background = color[0];
    wrap.style.borderLeft = `4px solid ${color[1]}`;
    wrap.style.color = "#1f2937";

    wrap.innerHTML = `<div style="display:flex;align-items:flex-start;gap:8px">
      <div style="line-height:1.2;flex:1">${message}</div>
      <button type="button" aria-label="Tutup" style="color:#6b7280">✕</button>
    </div>`;

    const btn = wrap.querySelector("button");
    btn.addEventListener("click", () => wrap.remove());

    requestAnimationFrame(() => {
      wrap.style.opacity = "1";
    });

    return wrap;
  }

  function showAlert(type, message, timeoutMs = 3000) {
    const container = ensureContainer();
    const el = makeAlertElement(type, message);
    container.appendChild(el);
    if (timeoutMs > 0) {
      setTimeout(() => el && el.remove(), timeoutMs);
    }
  }

  function makeConfirmElement(title, message, onConfirm, onCancel) {
    const overlay = document.createElement("div");
    overlay.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      opacity: 0;
      transition: opacity 0.3s ease;
    `;

    const dialog = document.createElement("div");
    dialog.style.cssText = `
      background: white;
      border-radius: 12px;
      padding: 24px;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      transform: scale(0.95);
      transition: transform 0.3s ease;
    `;

    dialog.innerHTML = `
      <div style="margin-bottom: 16px;">
        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #1f2937;">${title}</h3>
        <p style="margin: 0; color: #6b7280; line-height: 1.5;">${message}</p>
      </div>
      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" id="confirmCancel" style="
          padding: 8px 16px;
          border: 1px solid #d1d5db;
          background: white;
          color: #374151;
          border-radius: 6px;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s ease;
        ">Batal</button>
        <button type="button" id="confirmOk" style="
          padding: 8px 16px;
          border: none;
          background: #ef4444;
          color: white;
          border-radius: 6px;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s ease;
        ">Konfirmasi</button>
      </div>
    `;

    overlay.appendChild(dialog);
    document.body.appendChild(overlay);

    // Animate in
    requestAnimationFrame(() => {
      overlay.style.opacity = "1";
      dialog.style.transform = "scale(1)";
    });

    // Event listeners
    const cancelBtn = dialog.querySelector("#confirmCancel");
    const okBtn = dialog.querySelector("#confirmOk");

    const closeDialog = () => {
      overlay.style.opacity = "0";
      dialog.style.transform = "scale(0.95)";
      setTimeout(() => {
        if (overlay.parentNode) {
          overlay.parentNode.removeChild(overlay);
        }
      }, 300);
    };

    cancelBtn.addEventListener("click", () => {
      closeDialog();
      if (onCancel) onCancel();
    });

    okBtn.addEventListener("click", () => {
      closeDialog();
      if (onConfirm) onConfirm();
    });

    // Close on overlay click
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) {
        closeDialog();
        if (onCancel) onCancel();
      }
    });

    // Close on Escape key
    const handleEscape = (e) => {
      if (e.key === "Escape") {
        closeDialog();
        if (onCancel) onCancel();
        document.removeEventListener("keydown", handleEscape);
      }
    };
    document.addEventListener("keydown", handleEscape);

    return overlay;
  }

  function showConfirm(title, message, onConfirm, onCancel) {
    return makeConfirmElement(title, message, onConfirm, onCancel);
  }

  // Integrasi dengan Axios (jika ada): tampilkan error default
  if (window.api && window.api.interceptors) {
    window.api.interceptors.response.use(
      (res) => res,
      (err) => {
        try {
          const msg =
            err?.response?.data?.message ||
            err?.message ||
            "Terjadi kesalahan koneksi";
          showAlert("error", msg, 4000);
        } catch (_) {
          showAlert("error", "Terjadi kesalahan tak terduga", 4000);
        }
        return Promise.reject(err);
      }
    );
  }

  window.showAlert = showAlert;
  window.showConfirm = showConfirm;
})();
