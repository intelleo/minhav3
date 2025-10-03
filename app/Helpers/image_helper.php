<?php

if (!function_exists('img_tag')) {
  /**
   * Render tag <img> dengan default lazy, decoding async, dan normalisasi URL.
   *
   * @param string $path Relatif atau absolut
   * @param string $alt  Alt text
   * @param array  $attrs Atribut tambahan: ['class' => '...', 'width' => 40, 'height' => 40, 'fetchpriority' => 'high', 'srcset' => '...', 'sizes' => '...']
   * @return string
   */
  function img_tag(string $path, string $alt = '', array $attrs = []): string
  {
    $src = '';
    $path = trim($path);
    if ($path !== '') {
      if (preg_match('/^https?:\/\//i', $path)) {
        // Absolut -> pakai path-nya ke base_url saat ini jika host berbeda
        $parts = @parse_url($path);
        $rel = isset($parts['path']) ? $parts['path'] : '';
        $src = $rel !== '' ? base_url(ltrim($rel, '/')) : $path;
      } else {
        $src = base_url(ltrim($path, '/'));
      }
    }

    // Default attributes
    $final = array_merge([
      'loading' => 'lazy',
      'decoding' => 'async',
      'alt' => $alt,
      'src' => $src,
    ], $attrs);

    // Build attribute string safely
    $htmlAttrs = '';
    foreach ($final as $k => $v) {
      if ($v === null || $v === false) continue;
      $htmlAttrs .= ' ' . htmlspecialchars((string)$k, ENT_QUOTES) . '="' . htmlspecialchars((string)$v, ENT_QUOTES) . '"';
    }

    return '<img' . $htmlAttrs . ' />';
  }
}
