<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>
<div class="mt-[-5rem]">
  <div class=" w-full bg-white shadow-lg rounded-2xl p-6">
    <h1 class="text-2xl font-bold text-blue-600 mb-4">Panduan Penggunaan Minha</h1>
    <p class="text-gray-600 mb-6">
      Berikut panduan singkat penggunaan <span class="font-semibold text-indigo-500">Minha Verse</span> untuk mahasiswa. Klik bagian di bawah untuk membuka detail panduan.
    </p>

    <!-- Accordion -->
    <div class="space-y-4">
      <!-- Item 1 -->
      <div class="border-0 rounded-lg overflow-hidden">
        <button class="w-full flex justify-between items-center p-4 bg-blue-50 hover:bg-blue-100  transition" onclick="toggleAccordion(1)">
          <span class="font-medium text-gray-800 ">1. Cara Membuat Postingan</span>
          <svg id="icon-1" class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="content-1" class="max-h-0 overflow-hidden transition-all duration-300 bg-white">
          <div class="p-4 text-gray-600 border-2 border-blue-100">
            Untuk membuat postingan baru, klik tombol <span class="font-semibold">"Tambah Post"</span> di bagian atas halaman.
            Kamu bisa menulis teks, menambahkan gambar, lalu tekan <span class="font-semibold">"Kirim"</span>.
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="border-0 rounded-lg overflow-hidden">
        <button class="w-full flex justify-between items-center p-4 bg-blue-50 hover:bg-blue-100  transition" onclick="toggleAccordion(2)">
          <span class="font-medium text-gray-800 ">2. Cara Follow dan Mutual</span>
          <svg id="icon-2" class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="content-2" class="max-h-0 overflow-hidden transition-all duration-300 bg-white">
          <div class="p-4 text-gray-600 border-2 border-blue-100">
            Klik tombol <span class="font-semibold">"Ikuti"</span> di profil pengguna.
            Jika pengguna tersebut juga mengikuti kamu, maka statusnya berubah menjadi <span class="font-semibold">Mutual</span>.
          </div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="border-0 rounded-lg overflow-hidden">
        <button class="w-full flex justify-between items-center p-4 bg-blue-50 hover:bg-blue-100  transition" onclick="toggleAccordion(3)">
          <span class="font-medium text-gray-800 ">3. Cara Like, Komentar, dan Share</span>
          <svg id="icon-3" class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="content-3" class="max-h-0 overflow-hidden transition-all duration-300 bg-white">
          <div class="p-4 text-gray-600 border-2 border-blue-100">
            Gunakan ikon ❤️ untuk memberikan like, 💬 untuk komentar, dan 🔁 untuk membagikan postingan.
            Semua aktivitas akan muncul di notifikasi teman kamu.
          </div>
        </div>
      </div>

      <!-- Item 4 -->
      <div class="border-0 rounded-lg overflow-hidden">
        <button class="w-full flex justify-between items-center p-4 bg-blue-50 hover:bg-blue-100  transition" onclick="toggleAccordion(4)">
          <span class="font-medium text-gray-800 ">4. Notifikasi & Pesan</span>
          <svg id="icon-4" class="w-5 h-5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="content-4" class="max-h-0 overflow-hidden transition-all duration-300 bg-white">
          <div class="p-4 text-gray-600 border-2 border-blue-100">
            Semua notifikasi (like, komentar, follow baru) bisa dilihat di ikon 🔔.
            Untuk mengirim pesan, buka profil teman lalu pilih tombol <span class="font-semibold">"Kirim Pesan"</span>.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleAccordion(id) {
    const content = document.getElementById(`content-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    if (content.classList.contains("max-h-0")) {
      content.classList.remove("max-h-0");
      content.classList.add("max-h-96");
      icon.classList.add("rotate-180");
    } else {
      content.classList.add("max-h-0");
      content.classList.remove("max-h-96");
      icon.classList.remove("rotate-180");
    }
  }
</script>
<?= $this->endSection() ?>