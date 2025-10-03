<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>

<style>
  :root {
    --primary: #247de3;
    --primary-dark: #1c68c5;
    --secondary: #f8f9fa;
    --dark: #343a40;
    --light: #f8f9fa;
    --gray: #6c757d;
    --chat-user: #e1f5fe;
    --chat-bot: #f0f4ff;
    --paragraf: 0.8rem;
    --line-height: 1.5rem;
  }

  .likes-grid {

    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
  }

  .like-card {
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 12px;
    padding: 1rem;
    transition: box-shadow .2s ease, transform .2s ease;
  }

  .like-card:hover {
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, .08);
    transform: translateY(-2px);
  }

  .like-title {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: .25rem;
  }

  .like-meta {
    font-size: .85rem;
    color: #6b7280;
    display: flex;
    gap: .75rem;
  }

  .like-desc {
    margin-top: .5rem;
    font-size: .9rem;
    color: #374151;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  @media (max-width: 1024px) {
    .likes-grid {
      grid-template-columns: 1fr 1fr;
    }
  }

  @media (max-width: 640px) {
    .likes-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="mt-[-5rem] flex flex-col gap-6">
  <?php if (!empty($likedMading)): ?>
    <?php foreach ($likedMading as $mading): ?>
      <?= view('user/partials/mading_card_item', ['mading' => $mading]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="text-center py-10 text-gray-500 bg-white">
      <i class="ri-error-warning-line text-6xl block mb-4 text-gray-300"></i>
      <p>Belum ada mading yang kamu like.</p>
    </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>