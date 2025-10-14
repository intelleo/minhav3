<?php

/**
 * Comment Context Menu Partial
 * Komponen untuk long press context menu pada komentar
 * 
 * Props:
 * - $currentUser: Nama user saat ini
 */
$currentUser = $currentUser ?? session('namalengkap') ?? '';
?>

<!-- Context Menu Overlay -->
<div id="comment-context-menu-overlay"
  class="context-menu-overlay fixed inset-0 z-40 hidden"
  onclick="hideContextMenu('comment-context-menu')">
</div>

<!-- Context Menu -->
<div id="comment-context-menu"
  class="context-menu fixed z-[9999] hidden bg-white rounded-lg shadow-xl border border-gray-200 py-2 min-w-[160px] transform transition-all duration-200 ease-out"
  data-comment-id=""
  data-comment-author=""
  data-current-user="<?= esc($currentUser) ?>"
  style="display: none;">

  <!-- Menu Items akan di-generate dinamis berdasarkan permission -->
  <div id="context-menu-items">
    <!-- Items akan diisi oleh JavaScript -->
  </div>

  <!-- Divider -->
  <div class="border-t border-gray-100 my-1"></div>

  <!-- Cancel Button -->
  <button type="button"
    class="context-menu-item w-full px-4 py-2 text-left text-sm text-gray-500 hover:bg-gray-50 transition-colors duration-150"
    onclick="hideContextMenu('comment-context-menu')">
    <i class="ri-close-line text-base mr-3"></i>
    <span>Batal</span>
  </button>
</div>

<!-- Context Menu CSS -->
<style>
  /* Context Menu Styles */
  .context-menu-overlay {
    background: rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(2px);
  }

  .context-menu {
    opacity: 1;
    transform: scale(1);
    transition: opacity 0.2s ease-out, transform 0.2s ease-out;
  }

  .context-menu.hidden {
    opacity: 0;
    transform: scale(0.95);
    transition: opacity 0.15s ease-in, transform 0.15s ease-in;
  }

  .context-menu-item {
    border: none;
    background: none;
    cursor: pointer;
    outline: none;
  }

  .context-menu-item:hover {
    transform: translateX(2px);
  }

  .context-menu-item:active {
    transform: translateX(1px) scale(0.98);
  }

  /* Animations */
  @keyframes contextMenuShow {
    from {
      opacity: 0;
      transform: scale(0.95) translateY(-10px);
    }

    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  @keyframes contextMenuHide {
    from {
      opacity: 1;
      transform: scale(1) translateY(0);
    }

    to {
      opacity: 0;
      transform: scale(0.95) translateY(-10px);
    }
  }

  /* Long Press Animation */
  .long-press-active {
    transform: scale(1.02);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    transition: all 0.2s ease;
  }

  .long-press-pulse {
    animation: longPressPulse 0.6s ease-in-out infinite;
  }

  @keyframes longPressPulse {

    0%,
    100% {
      box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }

    50% {
      box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.1);
    }
  }

  /* Own Comment Styling */
  .own-comment {
    border-left-color: #3b82f6 !important;
    border-left-width: 4px !important;
  }

  .own-comment:hover {
    background-color: rgba(59, 130, 246, 0.02);
    border-color: rgba(59, 130, 246, 0.2);
  }

  .own-comment .author {
    color: #1d4ed8;
    font-weight: 600;
  }

  /* Context Menu Permission Indicator */
  .context-menu-item[data-action="edit"],
  .context-menu-item[data-action="delete"] {
    position: relative;
  }

  /* .context-menu-item[data-action="edit"]::after,
  .context-menu-item[data-action="delete"]::after {
    content: '✓';
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #059669;
    font-weight: bold; */
</style>

<!-- Context Menu JavaScript -->
<script>
  // Context Menu JavaScript
  let longPressTimer = null;
  let longPressThreshold = 800; // 800ms
  let isLongPressing = false;

  // Long Press Detection
  function startLongPress(element, event) {
    if (longPressTimer) return;

    isLongPressing = false;
    longPressTimer = setTimeout(() => {
      isLongPressing = true;
      showContextMenu(element, event);
    }, longPressThreshold);

    // Add visual feedback
    element.classList.add('long-press-active');
  }

  function cancelLongPress(element) {
    if (longPressTimer) {
      clearTimeout(longPressTimer);
      longPressTimer = null;
    }

    if (isLongPressing) {
      isLongPressing = false;
    }

    element.classList.remove('long-press-active', 'long-press-pulse');
  }

  // Show Context Menu
  function showContextMenu(element, event) {
    const menuId = 'comment-context-menu';
    const menu = document.getElementById(menuId);
    const overlay = document.getElementById(menuId + '-overlay');

    if (!menu || !overlay) return;

    // Get comment data
    const commentId = element.closest('[data-comment-id]')?.dataset.commentId ||
      element.dataset.commentId || '';
    const commentAuthor = element.closest('[data-comment-author]')?.dataset.commentAuthor ||
      element.dataset.commentAuthor || '';
    const currentUser = menu.dataset.currentUser || '';

    // Set comment data
    menu.dataset.commentId = commentId;
    menu.dataset.commentAuthor = commentAuthor;

    // Generate menu items based on permission
    generateContextMenuItems(commentAuthor, currentUser);

    // Position menu
    positionContextMenu(menu, event);

    // Show menu
    overlay.classList.remove('hidden');
    menu.style.display = 'block';
    menu.classList.remove('hidden');

    // Add pulse animation
    element.classList.add('long-press-pulse');

    // Prevent default context menu
    event.preventDefault();
  }

  // Generate Context Menu Items based on permission
  function generateContextMenuItems(commentAuthor, currentUser) {
    const menuItemsContainer = document.getElementById('context-menu-items');
    if (!menuItemsContainer) return;

    let menuItemsHTML = '';

    // Check if user owns the comment
    const isOwnComment = (commentAuthor === currentUser);

    // Debug logging
    console.log('Context Menu Permission Check:', {
      commentAuthor: commentAuthor,
      currentUser: currentUser,
      isOwnComment: isOwnComment
    });

    // Edit - hanya untuk komentar sendiri
    if (isOwnComment) {
      menuItemsHTML += `
      <button type="button" 
              class="context-menu-item w-full px-4 py-2 text-left text-sm flex items-center gap-3 text-blue-600 hover:bg-blue-50 transition-colors duration-150"
              data-action="edit"
              onclick="handleContextMenuAction('edit', '', this)">
        <i class="ri-edit-line text-base"></i>
        <span>Edit</span>
      </button>
    `;
    }

    // Delete - hanya untuk komentar sendiri
    if (isOwnComment) {
      menuItemsHTML += `
      <button type="button" 
              class="context-menu-item w-full px-4 py-2 text-left text-sm flex items-center gap-3 text-red-600 hover:bg-red-50 transition-colors duration-150"
              data-action="delete"
              onclick="handleContextMenuAction('delete', '', this)">
        <i class="ri-delete-bin-line text-base"></i>
        <span>Hapus</span>
      </button>
    `;
    }

    // Report - hanya untuk komentar orang lain
    if (!isOwnComment && commentAuthor) {
      //   menuItemsHTML += `
      //   <button type="button" 
      //           class="context-menu-item w-full px-4 py-2 text-left text-sm flex items-center gap-3 text-orange-600 hover:bg-orange-50 transition-colors duration-150"
      //           data-action="report"
      //           onclick="handleContextMenuAction('report', '', this)">
      //     <i class="ri-flag-line text-base"></i>
      //     <span>Laporkan</span>
      //   </button>
      // `;
    }

    // Copy - untuk semua komentar
    menuItemsHTML += `
    <button type="button" 
            class="context-menu-item w-full px-4 py-2 text-left text-sm flex items-center gap-3 text-gray-600 hover:bg-gray-50 transition-colors duration-150"
            data-action="copy"
            onclick="handleContextMenuAction('copy', '', this)">
      <i class="ri-file-copy-line text-base"></i>
      <span>Salin</span>
    </button>
  `;

    // Update menu items
    menuItemsContainer.innerHTML = menuItemsHTML;
  }

  // Position Context Menu
  function positionContextMenu(menu, event) {
    const rect = menu.getBoundingClientRect();
    const viewport = {
      width: window.innerWidth,
      height: window.innerHeight
    };

    let x = event.clientX;
    let y = event.clientY;

    // Adjust horizontal position
    if (x + rect.width > viewport.width) {
      x = viewport.width - rect.width - 10;
    }

    // Adjust vertical position
    if (y + rect.height > viewport.height) {
      y = event.clientY - rect.height - 10;
    }

    // Ensure minimum distance from edges
    x = Math.max(10, x);
    y = Math.max(10, y);

    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
  }

  // Hide Context Menu
  function hideContextMenu(menuId) {
    const menu = document.getElementById(menuId);
    const overlay = document.getElementById(menuId + '-overlay');

    if (menu) {
      menu.style.display = 'none';
      menu.classList.add('hidden');
    }

    if (overlay) {
      overlay.classList.add('hidden');
    }

    // Remove visual feedback
    document.querySelectorAll('.long-press-active, .long-press-pulse').forEach(el => {
      el.classList.remove('long-press-active', 'long-press-pulse');
    });
  }

  // Handle Context Menu Actions
  async function handleContextMenuAction(action, commentId, button) {
    const menu = document.getElementById('comment-context-menu');
    const actualCommentId = menu.dataset.commentId || commentId;

    // Hide context menu
    hideContextMenu('comment-context-menu');

    console.log('Context menu action:', action, 'for comment:', actualCommentId);

    // For delete, do not hit server here. Let deleteComment handle confirmation and deletion.
    if (action === 'delete') {
      await deleteComment(actualCommentId);
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/Mading/context-menu-action', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: new URLSearchParams({
          action: action,
          comment_id: actualCommentId,
          csrf_test_name: csrfToken
        })
      });

      const data = await response.json();
      if (data && data.csrf) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', data.csrf);
      }

      if (data.success) {
        // Handle success based on action
        switch (action) {
          case 'edit':
            await startEditInForm(actualCommentId, data.content);
            break;
          case 'delete':
            await deleteComment(actualCommentId);
            break;
          case 'report':
            await reportComment(actualCommentId);
            break;
          case 'copy':
            await copyComment(actualCommentId, data.content);
            break;
        }

        // Show success message
        showNotification(data.message, 'success');
      } else {
        // Show error message
        showNotification(data.message, 'error');
      }
    } catch (error) {
      console.error('Error handling context menu action:', error);
      showNotification('Gagal memproses aksi. Coba lagi.', 'error');
    }
  }

  // Safe HTML escape fallback
  function __escapeHtmlFallback(str) {
    return (str || '').toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
  const escapeHtmlSafe = window.escapeHtml || __escapeHtmlFallback;

  // Action Handlers
  async function editComment(commentId, content = '') {
    console.log('Edit comment:', commentId);

    // Find comment element
    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (!commentElement) return;

    // Prevent duplicate edit form
    const existingForm = commentElement.querySelector('.edit-form');
    if (existingForm) {
      const existingTextarea = existingForm.querySelector('textarea');
      if (existingTextarea) {
        existingTextarea.focus();
        existingTextarea.setSelectionRange(existingTextarea.value.length, existingTextarea.value.length);
      }
      return;
    }

    // Find content element robustly
    let contentElement = commentElement.querySelector('.content');
    if (!contentElement) {
      // Fallback: first paragraph inside body/content wrapper
      contentElement = commentElement.querySelector('.comment-root-body p, .comment-reply-body p, p');
    }
    if (!contentElement) return;

    // Create edit form
    const editForm = document.createElement('form');
    editForm.className = 'edit-form mt-2';
    editForm.innerHTML = `
    <div class="flex gap-2">
      <textarea class="flex-1 p-2 border border-gray-300 rounded-lg text-sm resize-none" 
                rows="3" maxlength="500">${content}</textarea>
      <div class="flex flex-col gap-1">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
          <i class="ri-check-line"></i>
        </button>
        <button type="button" class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600" 
                onclick="cancelEdit(this)">
          <i class="ri-close-line"></i>
        </button>
      </div>
    </div>
  `;

    // Hide original content
    contentElement.style.display = 'none';

    // Insert edit form
    contentElement.parentNode.insertBefore(editForm, contentElement.nextSibling);

    // Focus textarea
    const textarea = editForm.querySelector('textarea');
    textarea.focus();
    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    commentElement.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });

    // Also hide main form submit while inline edit is active to avoid double submit
    const mainForm = document.getElementById('comment-form');
    if (mainForm) {
      const submitBtn = mainForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.classList.add('hidden');
    }

    // Handle form submission
    editForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const newContent = textarea.value.trim();

      if (newContent === content) {
        cancelEdit(editForm);
        return;
      }

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch('/Mading/update-comment', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
          },
          credentials: 'same-origin',
          body: new URLSearchParams({
            comment_id: commentId,
            content: newContent,
            csrf_test_name: csrfToken
          })
        });

        const result = await response.json();
        if (result && result.csrf) {
          const meta = document.querySelector('meta[name="csrf-token"]');
          if (meta) meta.setAttribute('content', result.csrf);
        }

        if (result.success) {
          const updated = (result && typeof result.content === 'string') ? result.content : newContent;
          // Preserve mention if exists
          const mentionSpan = contentElement.querySelector('.mention');
          if (mentionSpan) {
            contentElement.innerHTML = `${mentionSpan.outerHTML} ${escapeHtmlSafe(updated)}`;
          } else {
            contentElement.textContent = updated;
          }
          // highlight
          contentElement.classList.add('bg-yellow-50');
          setTimeout(() => contentElement.classList.remove('bg-yellow-50'), 600);

          cancelEdit(editForm);
          showNotification('Komentar berhasil diperbarui', 'success');
        } else {
          showNotification(result.message, 'error');
        }
      } catch (error) {
        console.error('Error updating comment:', error);
        showNotification('Gagal memperbarui komentar', 'error');
      }
    });
  }

  // Edit via main comment form (scroll and prefill)
  async function startEditInForm(commentId, content) {
    const form = document.getElementById('comment-form');
    if (!form) return;

    const textarea = form.querySelector('textarea[name="isi_komentar"]') || form.querySelector('textarea');
    if (!textarea) return;

    // Prefill content and focus
    textarea.value = content || '';
    textarea.focus();
    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    form.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });

    // Store current edit id globally
    window.currentEditCommentId = commentId;

    // Hide default submit button to avoid double actions
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.classList.add('hidden');

    // Show edit action bar below form if not exists
    let editBar = document.getElementById('edit-action-bar');
    if (!editBar) {
      editBar = document.createElement('div');
      editBar.id = 'edit-action-bar';
      editBar.className = 'mt-2 flex items-center gap-2';
      editBar.innerHTML = `
       
        <button type="button"  id="save-edit-btn" class="p-3  bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Simpan Perubahan</button>
        <button type="button" id="cancel-edit-btn" class="p-3  bg-gray-500 text-white text-sm rounded hover:bg-gray-600">Batal</button>
      `;
      const buttonsRow = form.querySelector('.mt-2.flex.justify-end') || form.querySelector('.mt-2');
      if (buttonsRow && buttonsRow.parentNode) {
        buttonsRow.parentNode.insertBefore(editBar, buttonsRow.nextSibling);
      } else {
        form.appendChild(editBar);
      }

      // Attach handlers
      const saveEditBtn = document.getElementById('save-edit-btn');
      const cancelEditBtn = document.getElementById('cancel-edit-btn');
      if (saveEditBtn) {
        saveEditBtn.addEventListener('click', saveEditFromForm);
      }
      if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', cancelEditFromForm);
      }
    }
  }

  async function saveEditFromForm() {
    const form = document.getElementById('comment-form');
    const textarea = form && (form.querySelector('textarea[name="isi_komentar"]') || form.querySelector('textarea'));
    const commentId = window.currentEditCommentId;
    if (!form || !textarea || !commentId) return;

    const newContent = (textarea.value || '').trim();
    if (newContent.length < 3) {
      showNotification('Komentar terlalu pendek (minimal 3 karakter)', 'error');
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/Mading/update-comment', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: new URLSearchParams({
          comment_id: commentId,
          content: newContent,
          csrf_test_name: csrfToken
        })
      });

      const result = await response.json();
      if (result && result.csrf) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', result.csrf);
      }

      if (result.success) {
        const updated = (result && typeof result.content === 'string') ? result.content : newContent;
        // Update comment content in DOM for all instances with same id
        const containers = document.querySelectorAll(`[data-comment-id="${commentId}"]`);
        containers.forEach(container => {
          let contentElement = container.querySelector('.content') || container.querySelector('.comment-root-body p, .comment-reply-body p, p');
          if (!contentElement) return;
          const mentionSpan = contentElement.querySelector('.mention');
          const safeText = escapeHtmlSafe(updated);
          if (mentionSpan) {
            contentElement.innerHTML = `${mentionSpan.outerHTML} ${safeText}`;
          } else {
            contentElement.textContent = updated;
          }
          contentElement.classList.add('bg-yellow-50');
          setTimeout(() => contentElement.classList.remove('bg-yellow-50'), 600);
        });

        // Reset form and state
        textarea.value = '';
        cancelEditFromForm();
        showNotification('Komentar berhasil diperbarui', 'success');
      } else {
        showNotification(result.message || 'Gagal memperbarui komentar', 'error');
      }
    } catch (err) {
      console.error(err);
      showNotification('Gagal memperbarui komentar', 'error');
    }
  }

  function cancelEditFromForm() {
    const editBar = document.getElementById('edit-action-bar');
    if (editBar && editBar.parentNode) editBar.parentNode.removeChild(editBar);
    window.currentEditCommentId = null;
    const form = document.getElementById('comment-form');
    if (form) {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.classList.remove('hidden');
      const textarea = form.querySelector('textarea[name="isi_komentar"]') || form.querySelector('textarea');
      if (textarea) textarea.value = '';
    }
  }

  function cancelEdit(editForm) {
    const contentElement = editForm.previousElementSibling;
    contentElement.style.display = '';
    editForm.remove();
    const mainForm = document.getElementById('comment-form');
    if (mainForm) {
      const submitBtn = mainForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.classList.remove('hidden');
    }
    if (window.currentEditCommentId) window.currentEditCommentId = null;
  }

  async function deleteComment(commentId) {
    console.log('Delete comment:', commentId);

    // Konfirmasi penghapusan (custom)
    const ok = await confirmDelete('Hapus komentar ini beserta semua balasan?');
    if (!ok) {
      showNotification('Dibatalkan', 'info');
      return;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/Mading/context-menu-action', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: new URLSearchParams({
          action: 'delete',
          comment_id: commentId,
          csrf_test_name: csrfToken
        })
      });

      const result = await response.json();
      if (result && result.csrf) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', result.csrf);
      }

      if (result.success) {
        // Hapus semua elemen komentar yang termasuk dalam thread
        const ids = Array.isArray(result.deletedIds) ? result.deletedIds : [commentId];
        ids.forEach(id => {
          const elements = document.querySelectorAll(`[data-comment-id="${id}"]`);
          elements.forEach(el => {
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateX(-20px)';
            setTimeout(() => {
              el.remove();
            }, 300);
          });
        });
        updateCommentCount(ids.length);
        // Jika section menjadi kosong, tampilkan placeholder atau sembunyikan sentinel
        const list = document.getElementById('comments-list');
        if (list) {
          const remaining = list.querySelectorAll('[data-comment-id]').length;
          if (remaining === 0) {
            const skeleton = document.getElementById('comments-skeleton');
            if (skeleton) skeleton.innerHTML = '';
            list.innerHTML = '<div class="text-sm text-gray-500">Belum ada komentar.</div>';
          }
        }
        showNotification('Komentar berhasil dihapus', 'success');
        // Reload halaman setelah penghapusan berhasil untuk memastikan komentar tampil dengan benar
        setTimeout(() => {
          window.location.reload();
        }, 1000); // Delay 1 detik untuk menampilkan notifikasi sukses
      } else {
        showNotification(result.message, 'error');
      }
    } catch (error) {
      console.error('Error deleting comment:', error);
      showNotification('Gagal menghapus komentar', 'error');
    }
  }

  async function reportComment(commentId) {
    console.log('Report comment:', commentId);

    // Show report modal or form
    const reason = prompt('Alasan melaporkan komentar ini:');
    if (reason && reason.trim()) {
      // TODO: Send report to server
      console.log('Report reason:', reason);
    }
  }

  async function copyComment(commentId, content = '') {
    console.log('Copy comment:', commentId);

    try {
      await navigator.clipboard.writeText(content);
      console.log('Comment copied to clipboard');
    } catch (error) {
      console.error('Failed to copy comment:', error);
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = content;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
    }
  }

  function updateCommentCount(deleted = 1) {
    // Update comment count in header and footer
    const countElements = document.querySelectorAll('[data-comments-footer-count], #comments-count-header');
    countElements.forEach(element => {
      const currentCount = parseInt(element.textContent.replace(/[^\d]/g, '')) || 0;
      const next = Math.max(0, currentCount - (deleted || 1));
      element.textContent = next;
    });
  }

  // Show Notification
  function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[10000] px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium transform transition-all duration-300 ease-out ${
      type === 'success' ? 'bg-green-500' : 
      type === 'error' ? 'bg-red-500' : 
      type === 'warning' ? 'bg-yellow-500' : 
      'bg-blue-500'
    }`;

    notification.textContent = message;
    notification.style.transform = 'translateX(100%)';
    notification.style.opacity = '0';

    // Add to body
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
      notification.style.transform = 'translateX(0)';
      notification.style.opacity = '1';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
      notification.style.transform = 'translateX(100%)';
      notification.style.opacity = '0';
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification);
        }
      }, 300);
    }, 3000);
  }

  // Custom confirm dialog
  function confirmDelete(message = 'Yakin?') {
    return new Promise(resolve => {
      const overlay = document.createElement('div');
      overlay.className = 'fixed inset-0 z-[10000] bg-black/30 flex items-center justify-center p-4';
      const box = document.createElement('div');
      box.className = 'bg-white rounded-lg shadow-xl w-full max-w-xs p-4';
      box.innerHTML = `
        <div class="text-sm text-gray-800 mb-4">${message}</div>
        <div class="flex justify-end gap-2">
          <button id="confirm-cancel" class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">Batal</button>
          <button id="confirm-ok" class="px-3 py-2 text-sm bg-red-600 text-white hover:bg-red-700 rounded">Hapus</button>
        </div>
      `;
      overlay.appendChild(box);
      document.body.appendChild(overlay);
      const cleanup = (val) => {
        if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
        resolve(val);
      };
      const confirmCancel = box.querySelector('#confirm-cancel');
      const confirmOk = box.querySelector('#confirm-ok');
      if (confirmCancel) {
        confirmCancel.addEventListener('click', () => cleanup(false));
      }
      if (confirmOk) {
        confirmOk.addEventListener('click', () => cleanup(true));
      }
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) cleanup(false);
      });
    });
  }

  // Initialize long press listeners
  document.addEventListener('DOMContentLoaded', function() {
    // Add long press listeners to comment elements
    document.addEventListener('mousedown', function(event) {
      const commentElement = event.target.closest('.comment-root, .comment-reply');
      if (commentElement) {
        startLongPress(commentElement, event);
      }
    });

    document.addEventListener('mouseup', function(event) {
      const commentElement = event.target.closest('.comment-root, .comment-reply');
      if (commentElement) {
        cancelLongPress(commentElement);
      }
    });

    document.addEventListener('mouseleave', function(event) {
      const commentElement = event.target.closest('.comment-root, .comment-reply');
      if (commentElement) {
        cancelLongPress(commentElement);
      }
    });

    // Touch events for mobile
    document.addEventListener('touchstart', function(event) {
      const commentElement = event.target.closest('.comment-root, .comment-reply');
      if (commentElement) {
        startLongPress(commentElement, event);
      }
    });

    document.addEventListener('touchend', function(event) {
      const commentElement = event.target.closest('.comment-root, .comment-reply');
      if (commentElement) {
        cancelLongPress(commentElement);
      }
    });
  });
</script>