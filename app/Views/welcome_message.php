<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Minha - Chatbot Akademik Universitas Handayani Makassar</title>
    <link rel="stylesheet" href="<?= base_url('css/landingPage.css') ?>">
    <link rel="stylesheet" href="<?= base_url('fonts/remixicon.css') ?>">
</head>
<?= $this->include('partials/button_spinner') ?>

<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <div class="logo-icon">
                    <img src="<?= base_url('img/icon-tr.webp') ?>" alt="logo-mihnha ai" width="40" />
                </div>
                <div class="logo-text">Minha</div>
            </div>

            <nav>
                <ul>
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#demo">Demo</a></li>
                    <li><a href="#features">Fitur</a></li>
                    <li><a href="#how-it-works">Cara Kerja</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </nav>

            <a href="<?= site_url('/login') ?>" class="cta-button" data-spinner="button">Mulai Chat</a>
        </div>
    </header>

    <div class="container mobile-header">
        <div class="logo-mobile">
            <div class="logo-icon">
                <img src="<?= base_url('img/icon-tr.webp') ?>" alt="logo-mihnha ai" width="40" />
                <div class="logo-text">Minha</div>
            </div>
            <!-- Mobile Header -->
            <div id="btnMobile">
                <button id="menuToggle">
                    <i class="ri-menu-3-line"></i>
                </button>
            </div>

        </div>
        <nav id="mobileNav">
            <ul>
                <li><a href="#home">Beranda</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#demo">Demo</a></li>
                <li><a href="#features">Fitur</a></li>
                <li><a href="#how-it-works">Cara Kerja</a></li>
                <li><a href="#faq">FAQ</a></li>
                <a href="/login" class="cta-button-mobile" data-spinner="button">Mulai Chat</a>
            </ul>
        </nav>
    </div>
    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const menuToggle = document.getElementById("menuToggle");
            const mobileNav = document.getElementById("mobileNav");

            menuToggle.addEventListener("click", () => {
                mobileNav.classList.toggle("active");
            });

            // Optional: close menu when a link is clicked
            mobileNav.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    mobileNav.classList.remove("active");
                });
            });
        });
    </script>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container hero-content">
            <div class="hero-text">
                <!-- <div class="hero-image-mobile">
                    <img src="<?= base_url('img/hero.webp') ?>" alt="" width="450" />
                </div> -->
                <h1>
                    <span>Minha</span>, Chatbot Layanan Akademik Universitas Handayani
                    Makassar
                </h1>
                <p>
                    Dapatkan informasi akademik kapan saja, di mana saja dengan chatbot
                    cerdas kami. Minha siap membantu menjawab pertanyaan seputar informasi akademik universitas handayani makassar.
                </p>
                <a href="/login" class="cta-button" data-spinner="button">Coba Sekarang</a>
            </div>
            <div class="hero-image">
                <img src="<?= base_url('img/hero.webp') ?>" alt="" width="450" />
            </div>
        </div>
    </section>
    <!-- about Section -->
    <section class="tentang" id="about">
        <div class="container">
            <div class="section-title">
                <div class="img">
                    <img src="<?= base_url('img/about.webp') ?>" alt="">
                </div>
                <div class="tentang-text">
                    <h2>Tentang Minha?</h2>
                    <p>
                        Minha adalah sebuah chatbot akademik yang dirancang khusus untuk mendukung mahasiswa Universitas Handayani Makassar dalam memperoleh informasi seputar kampus dengan lebih mudah. Melalui penerapan teknologi Artificial Intelligence (AI), Minha mampu memberikan jawaban yang cepat, akurat, dan relevan terhadap berbagai kebutuhan informasi, mulai dari jadwal perkuliahan, layanan administrasi, hingga panduan akademik lainnya.
                        <br>

                    </p>
                </div>

            </div>
        </div>
    </section>


    <!-- Demo Chatbot Section -->
    <section class="demo-chatbot" id="demo">
        <div class="container">
            <div class="section-title">
                <h2>Coba Minha Sekarang</h2>
                <p>Eksplorasi kemampuan Minha dengan memilih salah satu pertanyaan di bawah</p>
            </div>

            <div class="chatbot-container">
                <div class="chatbot-header">
                    <h3>Minha Chatbot Demo</h3>
                    <p>Layanan Informasi Akademik Universitas Handayani</p>
                </div>

                <div class="chatbot-messages" id="chat-messages">
                    <div class="message-container bot-message-container">
                        <div class="message-avatar bot-avatar">
                            <img src="<?= base_url('img/icon-chat.webp') ?>" alt="">
                        </div>
                        <div class="message bot-message">
                            <div class="message-sender">Minha</div>
                            Halo! Saya Minha, asisten virtual Universitas Handayani Makassar. Saya di sini untuk membantu Anda dengan informasi akademik. Silakan pilih salah satu pertanyaan di bawah untuk memulai.
                        </div>
                    </div>
                </div>

                <div class="chatbot-options">
                    <div class="options-title">Pilih pertanyaan:</div>
                    <div class="option-buttons">
                        <button class="option-button" onclick="askQuestion('jadwal')">
                            📅 Bagaimana jadwal kuliah hari ini?
                        </button>
                        <button class="option-button" onclick="askQuestion('nilai')">
                            📊 Bagaimana cara cek nilai akademik?
                        </button>
                        <button class="option-button" onclick="askQuestion('wisuda')">
                            🎓 Apa persyaratan untuk wisuda?
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Fitur Unggulan Minha</h2>
                <p>
                    Minha dirancang khusus untuk memudahkan mahasiswa Universitas
                    Handayani Makassar dalam mengakses informasi akademik
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="<?= base_url('img/information.png') ?>" alt="">
                    </div>
                    <h3>Informasi Perkuliahan</h3>
                    <p>
                        Akses jadwal kuliah, ruangan, dan informasi mata kuliah dengan
                        mudah
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="<?= base_url('img/report.png') ?>" alt="">
                    </div>
                    <h3>Informasi Administrasi</h3>
                    <p>lihat informasi seputar administrasi kampus, seperti jadwal pembayaran spp dan administrasi lainnya</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="<?= base_url('img/date.png') ?>" alt="">
                    </div>
                    <h3>Kalender Akademik</h3>
                    <p>
                        Pantau jadwal penting akademik dan tidak akan ketinggalan deadline
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="<?= base_url('img/whiteboard.png') ?>" alt="">
                    </div>
                    <h3>Mading Online</h3>
                    <p>
                        Dapatkan informasi pengumuman, event, dan berita terbaru
                        kampus
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="<?= base_url('img/work.png') ?>" alt="">
                    </div>
                    <h3>Bimbingan Karir</h3>
                    <p>
                        Informasi tentang peluang magang, job fair, dan lowongan kerja
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="<?= base_url('img/clock.png') ?>" alt="">
                    </div>
                    <h3>24/7 Tersedia</h3>
                    <p>
                        Minha siap membantu kapan saja, bahkan di luar jam kerja
                        administrasi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>Bagaimana Minha Bekerja?</h2>
                <p>
                    Hanya perlu tiga langkah sederhana untuk mendapatkan informasi yang
                    Anda butuhkan
                </p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Mulai Percakapan</h3>
                    <p>
                        Klik tombol "Mulai Chat" dan sapa Minha dengan salam atau
                        pertanyaan langsung
                    </p>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Ajukan Pertanyaan</h3>
                    <p>
                        Tanyakan apa saja seputar akademik, jadwal, nilai, atau informasi
                        kampus
                    </p>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Dapatkan Jawaban</h3>
                    <p>
                        Minha akan memberikan jawaban yang akurat dan informatif dalam
                        hitungan detik
                    </p>
                </div>
            </div>
        </div>
    </section>



    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="container">
            <div class="section-title">
                <h2>Pertanyaan Umum</h2>
                <p>
                    Berikut adalah beberapa pertanyaan yang sering diajukan tentang
                    Minha
                </p>
            </div>

            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Bagaimana cara mengakses Minha?</span>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Anda dapat mengakses Minha melalui website resmi Universitas
                            Handayani Makassar atau langsung melalui halaman ini dengan
                            menekan tombol "Mulai Chat".
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Apakah Minha dapat diakses 24 jam?</span>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Ya, Minha tersedia 24/7 untuk menjawab pertanyaan Anda. Namun,
                            untuk informasi yang memerlukan pembaruan data real-time,
                            mungkin ada jeda waktu update tertentu.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Apakah perlu login untuk menggunakan Minha?</span>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Ya, untuk mengakses minha di perlukan login menggunakan NPM mahasiswa Universitas
                            Handayani Makassar.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Bagaimana jika Minha tidak memahami pertanyaan saya?</span>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        <p>
                            Minha terus belajar dan berkembang. Jika pertanyaan Anda tidak
                            dipahami, coba gunakan kata kunci yang lebih sederhana atau
                            hubungi administrasi kampus untuk bantuan lebih lanjut.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="chat">
        <div class="container">
            <h2>Siap Menggunakan Minha?</h2>
            <p>
                Mulai percakapan dengan Minha sekarang dan dapatkan informasi akademik
                yang Anda butuhkan dengan cepat dan mudah.
            </p>
            <a href="/login" class="cta-button white" data-spinner="button">Mulai Chat Sekarang</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">Minha</div>
                    <p>
                        Chatbot layanan informasi akademik Universitas Handayani Makassar
                        yang siap membantu 24/7.
                    </p>
                </div>

                <div class="footer-links">
                    <h3>Tautan Cepat</h3>
                    <ul>
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#about">Tentang</a></li>
                        <li><a href="#demo">Demo</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#how-it-works">Cara Kerja</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h3>Kontak</h3>
                    <p>Universitas Handayani Makassar</p>
                    <p> Jl. Adhyaksa Baru No. 1, Makassar - Sulawesi Selatan Indonesia</p>
                    <p>Email: info@handayani.ac.id</p>
                    <p>Telepon: (0411) 4673395</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>
                    &copy; 2025 Minha - Universitas Handayani Makassar. All rights
                    reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Script untuk FAQ accordion
        document.querySelectorAll(".faq-question").forEach((question) => {
            question.addEventListener("click", () => {
                const answer = question.nextElementSibling;
                const isOpen = answer.classList.contains("open");

                // Tutup semua jawaban
                document.querySelectorAll(".faq-answer").forEach((item) => {
                    item.classList.remove("open");
                });

                document.querySelectorAll(".faq-question").forEach((item) => {
                    item.querySelector("span:last-child").textContent = "+";
                });

                // Buka jawaban yang diklik jika sebelumnya tertutup
                if (!isOpen) {
                    answer.classList.add("open");
                    question.querySelector("span:last-child").textContent = "-";
                }
            });
        });

        // Script untuk demo chatbot
        const chatMessages = document.getElementById('chat-messages');

        function askQuestion(type) {
            // Tambahkan pertanyaan pengguna
            let questionText = '';
            if (type === 'jadwal') {
                questionText = 'Bagaimana jadwal kuliah hari ini?';
            } else if (type === 'nilai') {
                questionText = 'Bagaimana cara cek nilai akademik?';
            } else if (type === 'wisuda') {
                questionText = 'Apa persyaratan untuk wisuda?';
            }

            addMessage(questionText, 'user');

            // Berikan jawaban setelah jeda singkat
            setTimeout(() => {
                let answerText = '';
                if (type === 'jadwal') {
                    answerText = 'Jadwal kuliah hari ini dapat Anda akses melalui portal student. Untuk informasi lebih detail, silakan login ke sistem akademik. Apakah Anda ingin informasi tentang jadwal khusus jurusan tertentu?';
                } else if (type === 'nilai') {
                    answerText = 'Anda dapat mengecek nilai akademik melalui portal student atau langsung bertanya kepada saya. Untuk mengakses melalui portal, login ke student.handayani.ac.id lalu pilih menu "Nilai Akademik".';
                } else if (type === 'wisuda') {
                    answerText = 'Persyaratan wisuda meliputi: menyelesaikan semua SKS yang ditentukan, tidak memiliki tunggakan administrasi, telah menyelesaikan skripsi/tugas akhir, dan mengikuti seluruh proses administrasi wisuda. Untuk detail lengkapnya, silakan kunjungi website resmi universitas.';
                }

                addMessage(answerText, 'bot');

                // Scroll ke bawah
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 1000);
        }

        function addMessage(text, sender) {
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('message-container');

            const avatar = document.createElement('div');
            avatar.classList.add('message-avatar');

            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');

            const senderDiv = document.createElement('div');
            senderDiv.classList.add('message-sender');

            if (sender === 'bot') {
                messageContainer.classList.add('bot-message-container');
                avatar.classList.add('bot-avatar');

                // Avatar Minha (ganti dengan path gambar Anda)
                const avatarImg = document.createElement('img');
                avatarImg.src = 'minha-avatar.webp';
                avatarImg.alt = 'Minha Avatar';
                avatarImg.onerror = function() {
                    this.src = 'img/icon-chat.webp';
                };
                avatar.appendChild(avatarImg);

                messageDiv.classList.add('bot-message');
                senderDiv.textContent = 'Minha';
            } else {
                messageContainer.classList.add('user-message-container');
                avatar.classList.add('user-avatar');

                // Avatar User (ganti dengan path gambar Anda)
                const avatarImg = document.createElement('img');
                avatarImg.src = 'user-avatar.webp';
                avatarImg.alt = 'User Avatar';
                avatarImg.onerror = function() {
                    this.src = 'img/avatar.webp';
                };
                avatar.appendChild(avatarImg);

                messageDiv.classList.add('user-message');
                senderDiv.textContent = 'Anda';
            }

            messageDiv.appendChild(senderDiv);
            messageDiv.appendChild(document.createTextNode(text));

            messageContainer.appendChild(avatar);
            messageContainer.appendChild(messageDiv);

            chatMessages.appendChild(messageContainer);

            // Scroll ke bawah
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>

    <!-- Spinner Loader Script -->
</body>

</html>