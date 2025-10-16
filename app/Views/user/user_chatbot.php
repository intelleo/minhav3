<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>
<?= $this->include('partials/alert') ?>
<div class="mt-[-5rem] z-0">
  <flowise-fullchatbot></flowise-fullchatbot>
</div>

<script type="module">
  import Chatbot from "https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js"
  Chatbot.initFull({
    chatflowid: "131bf514-1e11-40a8-9325-6aa4b82b897b",
    apiHost: "https://chatbot.minha.my.id",
    chatflowConfig: {
      /* Konfigurasi Chatflow */
    },
    observersConfig: {
      /* Konfigurasi Observer */
    },
    theme: {
      button: {
        backgroundColor: 'white',
        right: 20,
        bottom: 20,
        size: 48,
        dragAndDrop: true,
        iconColor: '#247de3',
        customIconSrc: 'https://raw.githubusercontent.com/walkxcode/dashboard-icons/main/svg/google-messages.svg',
        autoWindowOpen: {
          autoOpen: true,
          openDelay: 2,
          autoOpenOnMobile: false
        }
      },
      tooltip: {
        showTooltip: true,
        tooltipMessage: 'Butuh bantuan? 👋',
        tooltipBackgroundColor: 'black',
        tooltipTextColor: 'white',
        tooltipFontSize: 16
      },

      customCSS: `
        /* Avatar ukuran 44px + bulat */
        .flowise-avatar{
          width: 44px !important;
          height: 44px !important;
          border-radius: 50% !important;
          object-fit: cover !important;
        }
      `,
      chatWindow: {
        showTitle: true,
        showAgentMessages: true,
        title: '',
        titleAvatarSrc: '',
        welcomeMessage: 'Halo 👋! Ada yang bisa saya bantu?',
        errorMessage: 'Ups, terjadi kesalahan. Silakan coba lagi.',
        backgroundColor: '#ffffff',
        backgroundImage: '',
        height: '100%',
        width: '100%',
        fontSize: 14,
        starterPrompts: [
          "Bagaimana cara melakukan pembayaran UKT?",
          "Apa saja syarat untuk mengajukan beasiswa?",
          "Bagaimana cara cek nilai akademik?",

        ],
        starterPromptFontSize: 14,
        clearChatOnReload: false,
        sourceDocsTitle: 'Sumber:',
        renderHTML: true,
        botMessage: {
          backgroundColor: '#f7f8ff',
          textColor: '#303235',
          showAvatar: false,
          avatarSrc: 'img/icon-chat.png'
        },
        userMessage: {
          backgroundColor: '#3B81F6',
          textColor: '#ffffff',
          showAvatar: false,
          avatarSrc: 'img/avatar.png'
        },
        textInput: {
          placeholder: 'Ketik pertanyaan Anda...',
          backgroundColor: '#ffffff',
          textColor: '#303235',
          sendButtonColor: '#3B81F6',
          maxChars: 300,
          maxCharsWarningMessage: 'Batas maksimal 300 karakter. Silakan kurangi teks Anda.',
          autoFocus: true,
          sendMessageSound: true,
          sendSoundLocation: 'send_message.mp3',
          receiveMessageSound: true,
          receiveSoundLocation: 'receive_message.mp3'
        },
        feedback: {
          color: '#303235'
        },
        dateTimeToggle: {
          date: true,
          time: true
        },
        footer: {
          textColor: '#303235',
          text: '',
          company: '',
          companyLink: ''
        }
      }
    }
  })

  // --- Tambahkan lazy loading avatar ---
  const observer = new MutationObserver((mutations, obs) => {
    const avatars = document.querySelectorAll('.flowise-avatar')
    if (avatars.length > 0) {
      avatars.forEach(img => {
        if (!img.hasAttribute('loading')) {
          img.setAttribute('loading', 'lazy')
        }
      })
      // Stop observer biar tidak boros
      obs.disconnect()
    }
  })

  observer.observe(document.body, {
    childList: true,
    subtree: true
  })
</script>

<?= $this->endSection() ?>