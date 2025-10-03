<!-- Avatar: Foto atau Inisial -->
<div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center overflow-hidden">
  <?php
  $foto = session('foto_profil');
  $nama = session('namalengkap');
  $inisial = strtoupper(substr($nama, 0, 1));
  ?>

  <?php if ($foto): ?>
    <!-- Jika ada foto profil -->
    <img
      src="<?= base_url($foto) ?>"
      alt="Foto Profil"
      class="w-full h-full object-cover rounded-full"
      onerror="this.style.display='none'; this.parentNode.innerHTML = '<?= $inisial ?>';">
  <?php endif; ?>

  <!-- Jika tidak ada foto, atau foto error -->
  <?php if (!$foto || !$foto): ?>
    <span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold flex items-center justify-center">
      <?= $inisial ?>
    </span>
  <?php endif; ?>
</div>