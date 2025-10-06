<!-- Avatar reusable: foto atau inisial berdasarkan parameter $foto dan $nama -->
<div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center overflow-hidden">
  <?php
  $foto = (string)($foto ?? '');
  $nama = trim((string)($nama ?? 'User'));

  // Buat inisial dari maksimal dua kata
  $initials = '';
  $words = preg_split('/\s+/', $nama) ?: [];
  if (!empty($words)) {
    $initials .= mb_strtoupper(mb_substr($words[0], 0, 1));
    if (isset($words[1]) && $words[1] !== '') {
      $initials .= mb_strtoupper(mb_substr($words[1], 0, 1));
    }
  }

  // Normalisasi URL foto → dukung absolut/relatif/filename
  $fotoUrl = '';
  if ($foto !== '') {
    if (preg_match('/^https?:\/\//i', $foto)) {
      // URL absolut
      $fotoUrl = $foto;
    } else {
      // Jika hanya filename tanpa path, prepend uploads/profiles/
      $path = (strpos($foto, '/') === false) ? ('uploads/profiles/' . $foto) : ltrim($foto, '/');
      $fotoUrl = base_url($path);
    }
  }
  ?>

  <?php if ($fotoUrl !== ''): ?>
    <img
      src="<?= esc($fotoUrl) ?>"
      alt="Foto Profil"
      class="w-full h-full object-cover rounded-full"
      onerror="this.classList.add('hidden'); var s=this.nextElementSibling; if(s){s.classList.remove('hidden');}">
    <span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold hidden items-center justify-center text-sm">
      <?= esc($initials ?: 'U') ?>
    </span>
  <?php else: ?>
    <span class="w-full h-full bg-indigo-100 text-[#1c68c5] font-semibold flex items-center justify-center text-sm">
      <?= esc($initials ?: 'U') ?>
    </span>
  <?php endif; ?>
</div>