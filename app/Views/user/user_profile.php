<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>

<style>
  .profile-container {
    margin-top: -11rem;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    background-color: white;
    padding: 4px;
    border: white 1px solid;
    /* border-radius: 6px; */
  }

  /* quick feature cards */
  .quick-cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .quick-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #eef2f7;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
  }

  .quick-card:hover {
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
  }

  .quick-card .icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }

  .icon-blue {
    background: #eff6ff;
    color: #247de3;
  }

  .icon-green {
    background: #ecfdf5;
    color: #059669;
  }

  .icon-orange {
    background: #fff7ed;
    color: #c2410c;
  }

  .quick-card .text .title {
    font-weight: 600;
    font-size: 0.95rem;
  }

  .quick-card .text .desc {
    font-size: 0.8rem;
    color: #6b7280;
  }

  .profile-card {
    background: white;
    border-radius: 12px;
    /* box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); */
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
  }

  .profile-header {

    background: linear-gradient(135deg, #247de3, #1e40af);
    padding: 2rem;
    text-align: center;
    color: white;
  }

  .profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: white;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.2);
  }

  .profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .profile-avatar i {
    font-size: 2.5rem;
    color: #247de3;
  }

  .profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: white;
  }

  .profile-role {
    font-size: 0.9rem;
    opacity: 0.9;
  }

  .profile-body {
    padding: 1.5rem;
  }

  .info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
  }

  .info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }

  .info-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: #247de3;
  }

  .info-text h4 {
    font-size: 0.8rem;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .info-text p {
    font-size: 0.95rem;
    font-weight: 500;
    color: #1f2937;
  }

  .form-card {
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    ;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .form-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
  }

  .form-header i {
    font-size: 1.25rem;
    color: #247de3;
    margin-right: 0.75rem;
  }

  .form-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
    font-size: 0.9rem;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.2s ease;
    font-family: inherit;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    border-color: #247de3;
    box-shadow: 0 0 0 3px rgba(36, 125, 227, 0.1);
  }

  .form-group textarea {
    resize: vertical;
    min-height: 100px;
  }

  .form-group small {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
  }

  .file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
  }

  .file-input-wrapper input[type=file] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }

  .file-input-label {
    display: block;
    padding: 1rem;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f9fafb;
  }

  .file-input-label:hover {
    border-color: #247de3;
    background: #eff6ff;
  }

  .file-input-label i {
    display: block;
    font-size: 1.5rem;
    color: #247de3;
    margin-bottom: 0.5rem;
  }

  .preview-image {
    margin-top: 1rem;
    text-align: center;
  }

  .preview-image img {
    max-width: 120px;
    max-height: 120px;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }

  .form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
  }

  .btn {
    padding: 0.75rem 1.25rem;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }

  /* Smooth transition untuk tombol foto */
  #savePhotoBtn,
  #cancelPhoto {
    transition: opacity 0.3s ease, transform 0.3s ease;
  }

  #savePhotoBtn[style*="none"],
  #cancelPhoto[style*="none"] {
    opacity: 0;
    transform: translateY(-10px);
  }

  .btn-primary {
    background: #247de3;
    color: white;
  }

  .btn-primary:hover {
    background: #1e40af;
  }

  .btn-secondary {
    background: #f3f4f6;
    color: #374151;
  }

  .btn-secondary:hover {
    background: #e5e7eb;
  }

  .btn-danger {
    background: #ef4444;
    color: white;
  }

  .btn-danger:hover {
    background: #dc2626;
  }

  .tabs {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
  }

  .tab {
    padding: 0.75rem 1rem;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
    font-size: 0.9rem;
  }

  .tab.active {
    color: #247de3;
  }

  .tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background: #247de3;
  }

  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
  }


  /* Responsive */
  @media (max-width: 768px) {
    .profile-container {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    .form-actions {
      flex-direction: column;
    }

    .tabs {
      justify-content: center;
    }

    .tab {
      text-align: center;
    }
  }
</style>



<div class="profile-container">
  <div class="profile-card">
    <div class="profile-header">
      <div class="profile-avatar">
        <?php
        $namaUser = trim((string)($user['namalengkap'] ?? 'Pengguna'));
        $words = preg_split('/\s+/', $namaUser);
        $initials = '';
        if ($words && count($words) > 0) {
          $initials .= mb_strtoupper(mb_substr($words[0], 0, 1));
          if (isset($words[1])) {
            $initials .= mb_strtoupper(mb_substr($words[1], 0, 0));
          }
        }
        $foto = $user['foto_profil'] ?? '';
        // Normalisasi ke URL absolut jika yang disimpan relatif
        $fotoUrl = '';
        if (!empty($foto)) {
          if (preg_match('/^https?:\/\//i', (string)$foto)) {
            $fotoUrl = (string)$foto;
          } else {
            $fotoUrl = base_url(ltrim((string)$foto, '/'));
          }
        }
        ?>
        <?php if (!empty($fotoUrl)): ?>
          <?= img_tag($fotoUrl, 'Profile Avatar', ['id' => 'profileAvatarImg', 'width' => 100, 'height' => 100]) ?>
        <?php else: ?>
          <div id="profileAvatarInitials" aria-label="Avatar Inisial" class="w-full h-full flex items-center justify-center bg-[#e5f0ff] text-[#1e40af] font-bold text-4xl">
            <?= esc($initials ?: 'U') ?>
          </div>
        <?php endif; ?>
      </div>
      <h2 class="profile-name"><?= esc($user['namalengkap'] ?? 'Pengguna') ?></h2>
      <p class="profile-role text-white text-sm"><?= esc($user['bio'] ?? 'Belum ada bio') ?></p>
    </div>
    <div class="profile-body">
      <div class="profile-info">
        <div class="info-item">
          <div class="info-icon">
            <i class="ri-id-card-line"></i>
          </div>
          <div class="info-text">
            <h4>NPM</h4>
            <p><?= esc($user['npm'] ?? '-') ?></p>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon">
            <i class="ri-graduation-cap-line"></i>
          </div>
          <div class="info-text">
            <h4>Jurusan</h4>
            <p><?= esc(ucwords($user['jurusan'] ?? '-')) ?></p>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon">
            <i class="ri-verified-badge-line"></i>
          </div>
          <div class="info-text">
            <h4>Status</h4>
            <p><?= esc(ucfirst($user['status'] ?? '-')) ?></p>
          </div>
        </div>


      </div>
    </div>
  </div>

  <div class="edit-forms">
    <div class="quick-cards flex max-lg:flex-col">
      <a href="<?= site_url('Notifications') ?>" class="quick-card relative">
        <div class="icon icon-blue"><i class="ri-notification-3-line"></i></div>
        <div class="text">
          <div class="title">Notifikasi</div>
          <div class="desc">Lihat aktivitas terbaru Anda</div>
        </div>
        <span id="cardNotifDot" class="absolute -right-1 -top-1 w-3 h-3 bg-red-500 rounded-full hidden"></span>
      </a>
      <a href="<?= site_url('Likes') . '?menu=Profile' ?>" class="quick-card">
        <div class="icon icon-green"><i class="ri-thumb-up-line"></i></div>
        <div class="text">
          <div class="title">Like Saya</div>
          <div class="desc">Konten yang Anda sukai</div>
        </div>
      </a>
      <!-- <a href="<?= site_url('Profile') . '?menu=posts' ?>" class="quick-card">
        <div class="icon icon-orange"><i class="ri-article-line"></i></div>
        <div class="text">
          <div class="title">Postingan Saya</div>
          <div class="desc">Kelola postingan Anda</div>
        </div>
      </a> -->
    </div>

    <div class="tabs">
      <div class="tab active" data-tab="photo-tab">Foto Profil</div>
      <div class="tab" data-tab="bio-tab">Edit Bio</div>
      <div class="tab" data-tab="password-tab">Password</div>
    </div>

    <div class="tab-content active" id="photo-tab">
      <div class="form-card">
        <div class="form-header">
          <i class="ri-camera-line"></i>
          <h3>Ubah Foto Profil</h3>
        </div>
        <form id="photoForm">
          <div class="form-group">
            <label for="profilePhoto">Pilih Foto Baru</label>
            <div class="file-input-wrapper">
              <input type="file" id="profilePhoto" accept="image/*">
              <label for="profilePhoto" class="file-input-label">
                <i class="ri-upload-cloud-line"></i>
                <span>Klik untuk mengunggah foto</span>
              </label>
            </div>
            <div class="preview-image" id="imagePreview"></div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="savePhotoBtn" style="display: none;">
              <i class="ri-save-line"></i>
              Simpan Perubahan
            </button>
            <button type="button" class="btn btn-secondary" id="cancelPhoto" style="display: none;">
              <i class="ri-close-line"></i>
              Batal
            </button>
            <?php if (!empty($user['foto_profil'])): ?>
              <button type="button" class="btn btn-danger" id="deletePhoto">
                <i class="ri-delete-bin-line text-base text-white"></i>
                Hapus Foto
              </button>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <div class="tab-content" id="bio-tab">
      <div class="form-card">
        <div class="form-header">
          <i class="ri-edit-line"></i>
          <h3>Edit Bio</h3>
        </div>
        <form id="bioForm">
          <div class="form-group">
            <label for="bio">Bio Anda</label>
            <textarea id="bio" placeholder="Ceritakan tentang diri Anda..." rows="4" maxlength="500"><?= esc($user['bio'] ?? '') ?></textarea>
            <small class="text-gray-500">
              <span id="bioCounter">0</span>/500 karakter
            </small>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="ri-save-line text-base text-white"></i>
              Simpan Bio
            </button>
            <button type="button" class="btn btn-secondary" id="cancelBio">
              <i class="ri-close-line text-base "></i>
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="tab-content" id="password-tab">
      <div class="form-card">
        <div class="form-header">
          <i class="ri-lock-line"></i>
          <h3>Ubah Password</h3>
        </div>
        <form id="passwordForm">
          <div class="form-group">
            <label for="currentPassword">Password Saat Ini</label>
            <input type="password" id="currentPassword" placeholder="Masukkan password saat ini" required>
          </div>
          <div class="form-group">
            <label for="newPassword">Password Baru</label>
            <input type="password" id="newPassword" placeholder="Masukkan password baru" required>
          </div>
          <div class="form-group">
            <label for="confirmPassword">Konfirmasi Password Baru</label>
            <input type="password" id="confirmPassword" placeholder="Konfirmasi password baru" required>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="ri-save-line text-base text-white"></i>
              Simpan Perubahan
            </button>
            <button type="button" class="btn btn-secondary" id="cancelPassword">
              <i class="ri-close-line text-base"></i>
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<script>
  (function() {
    // Elemen DOM
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    const photoForm = document.getElementById('photoForm');
    const passwordForm = document.getElementById('passwordForm');
    const profilePhotoInput = document.getElementById('profilePhoto');
    const imagePreview = document.getElementById('imagePreview');
    const profileAvatarImg = document.getElementById('profileAvatarImg');
    const cancelPhotoBtn = document.getElementById('cancelPhoto');
    const cancelPasswordBtn = document.getElementById('cancelPassword');
    const deletePhotoBtn = document.getElementById('deletePhoto');
    const savePhotoBtn = document.getElementById('savePhotoBtn');
    const bioForm = document.getElementById('bioForm');
    const bioTextarea = document.getElementById('bio');
    const cancelBioBtn = document.getElementById('cancelBio');
    const bioCounter = document.getElementById('bioCounter');

    // Fungsi untuk menampilkan/menyembunyikan tombol foto dengan transisi smooth
    function togglePhotoButtons(show) {
      if (savePhotoBtn) {
        if (show) {
          savePhotoBtn.style.display = 'flex';
          setTimeout(() => {
            savePhotoBtn.style.opacity = '1';
            savePhotoBtn.style.transform = 'translateY(0)';
          }, 10);
        } else {
          savePhotoBtn.style.opacity = '0';
          savePhotoBtn.style.transform = 'translateY(-10px)';
          setTimeout(() => {
            savePhotoBtn.style.display = 'none';
          }, 300);
        }
      }

      if (cancelPhotoBtn) {
        if (show) {
          cancelPhotoBtn.style.display = 'flex';
          setTimeout(() => {
            cancelPhotoBtn.style.opacity = '1';
            cancelPhotoBtn.style.transform = 'translateY(0)';
          }, 10);
        } else {
          cancelPhotoBtn.style.opacity = '0';
          cancelPhotoBtn.style.transform = 'translateY(-10px)';
          setTimeout(() => {
            cancelPhotoBtn.style.display = 'none';
          }, 300);
        }
      }
    }

    // Tampilkan dot notifikasi pada card
    (function() {
      const http = (window.api || window.axios || null);
      const dot = document.getElementById('cardNotifDot');
      if (http && dot) {
        http.get('<?= site_url('Notifications/count') ?>')
          .then(function(res) {
            var count = (res && res.data && res.data.count) ? parseInt(res.data.count) : 0;
            if (count > 0) dot.classList.remove('hidden');
            else dot.classList.add('hidden');
          }).catch(function(_) {});
      }
    })();

    // Event listener untuk tab
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const tabId = tab.getAttribute('data-tab');

        // Hapus kelas active dari semua tab dan tab content
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(tc => tc.classList.remove('active'));

        // Tambahkan kelas active ke tab yang dipilih
        tab.classList.add('active');

        // Tampilkan tab content yang sesuai
        document.getElementById(tabId).classList.add('active');
      });
    });

    // Event listener untuk input foto
    if (profilePhotoInput) {
      profilePhotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          // Validasi tipe file
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
          if (!allowedTypes.includes(file.type)) {
            window.showAlert('error', 'Tipe file tidak didukung. Gunakan JPG, PNG, atau WebP.');
            e.target.value = ''; // Reset input
            togglePhotoButtons(false);
            imagePreview.innerHTML = '';
            return;
          }

          const reader = new FileReader();

          reader.onload = function(event) {
            // Tampilkan preview gambar
            imagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
            // Tampilkan tombol simpan dan batal
            togglePhotoButtons(true);
          }

          reader.readAsDataURL(file);
        } else {
          // Jika tidak ada file, sembunyikan tombol
          togglePhotoButtons(false);
          imagePreview.innerHTML = '';
        }
      });
    }

    // Event listener untuk form bio
    if (bioForm) {
      bioForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const bio = bioTextarea.value.trim();

        // Validasi bio
        if (bio.length > 500) {
          window.showAlert('error', 'Bio maksimal 500 karakter');
          return;
        }

        const formData = new FormData();
        formData.append('bio', bio);

        // Tambahkan CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('<?= csrf_token() ?>', csrfToken);

        (window.api || axios).post('<?= site_url('/Profile/update-bio') ?>', formData)
          .then(res => {
            window.showAlert('success', res?.data?.message || 'Bio berhasil diperbarui!');

            // Update bio di header profile
            const profileRole = document.querySelector('.profile-role');
            if (profileRole) {
              profileRole.textContent = bio || 'Belum ada bio';
            }
          })
          .catch(err => {
            const msg = err?.response?.data?.message || 'Gagal memperbarui bio';
            window.showAlert('error', msg);
          });
      });
    }

    // Event listener untuk tombol batal bio
    if (cancelBioBtn) {
      cancelBioBtn.addEventListener('click', function() {
        // Reset ke nilai asli
        bioTextarea.value = '<?= esc($user['bio'] ?? '') ?>';
        updateBioCounter();
      });
    }

    // Event listener untuk karakter counter bio
    if (bioTextarea && bioCounter) {
      // Update counter saat load
      updateBioCounter();

      bioTextarea.addEventListener('input', updateBioCounter);
    }

    // Fungsi untuk update karakter counter
    function updateBioCounter() {
      if (bioTextarea && bioCounter) {
        const length = bioTextarea.value.length;
        bioCounter.textContent = length;

        // Ubah warna jika mendekati limit
        if (length > 450) {
          bioCounter.style.color = '#ef4444';
        } else if (length > 400) {
          bioCounter.style.color = '#f59e0b';
        } else {
          bioCounter.style.color = '#6b7280';
        }
      }
    }

    // Event listener untuk form foto
    if (photoForm) {
      photoForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const file = profilePhotoInput && profilePhotoInput.files ? profilePhotoInput.files[0] : null;
        if (!file) {
          window.showAlert('error', 'Silakan pilih foto terlebih dahulu');
          return;
        }

        const formData = new FormData();
        formData.append('profilePhoto', file);

        // Tambahkan CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('<?= csrf_token() ?>', csrfToken);

        (window.api || axios).post('<?= site_url('/Profile/update-photo') ?>', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          .then(res => {
            // Reload halaman setelah sukses agar tampilan segar dan konsisten
            window.location.reload();
          })
          .catch(err => {
            console.error('Upload error:', err);
            const msg = err?.response?.data?.message || err?.message || 'Gagal memperbarui foto profil';
            console.log('Error details:', err?.response?.data);
            window.showAlert('error', msg);
          });
      });
    }

    // Event listener untuk form password
    if (passwordForm) {
      passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validasi password
        if (newPassword !== confirmPassword) {
          window.showAlert('error', 'Password baru dan konfirmasi tidak cocok');
          return;
        }

        if (newPassword.length < 6) {
          window.showAlert('error', 'Password baru minimal 6 karakter');
          return;
        }

        const form = new FormData();
        form.append('currentPassword', currentPassword);
        form.append('newPassword', newPassword);
        form.append('confirmPassword', confirmPassword);

        // Tambahkan CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.append('<?= csrf_token() ?>', csrfToken);

        (window.api || axios).post('<?= site_url('/Profile/update-password') ?>', form)
          .then(() => {
            window.showAlert('success', 'Password berhasil diperbarui!');
            passwordForm.reset();
          })
          .catch(err => {
            const msg = err?.response?.data?.message || 'Gagal memperbarui password';
            window.showAlert('error', msg);
          });
      });
    }

    // Event listener untuk tombol batal
    if (cancelPhotoBtn) {
      cancelPhotoBtn.addEventListener('click', function() {
        photoForm && photoForm.reset();
        if (imagePreview) imagePreview.innerHTML = '';
        // Sembunyikan tombol setelah reset
        togglePhotoButtons(false);
      });
    }

    if (cancelPasswordBtn) {
      cancelPasswordBtn.addEventListener('click', function() {
        passwordForm && passwordForm.reset();
      });
    }

    // Event listener untuk tombol hapus foto
    if (deletePhotoBtn) {
      deletePhotoBtn.addEventListener('click', function() {
        window.showConfirm(
          'Hapus Foto Profil',
          'Apakah Anda yakin ingin menghapus foto profil? Tindakan ini tidak dapat dibatalkan.',
          function() {
            // Konfirmasi - hapus foto
            const formData = new FormData();
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('<?= csrf_token() ?>', csrfToken);

            (window.api || axios).post('<?= site_url('/Profile/delete-photo') ?>', formData)
              .then(() => {
                // Sembunyikan gambar dan tampilkan initials
                if (profileAvatarImg) profileAvatarImg.style.display = 'none';
                const initials = document.getElementById('profileAvatarInitials');
                if (initials) initials.style.display = 'flex';

                // Sembunyikan tombol hapus
                deletePhotoBtn.style.display = 'none';

                window.showAlert('success', 'Foto profil berhasil dihapus!');
              })
              .catch(err => {
                const msg = err?.response?.data?.message || 'Gagal menghapus foto profil';
                window.showAlert('error', msg);
              });
          },
          function() {
            // Batal - tidak melakukan apa-apa
            console.log('Hapus foto dibatalkan');
          }
        );
      });
    }
  })();
</script>

<?= $this->endSection() ?>