<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiCalc Pro | Dashboard Futuristik</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Custom -->
    <style>
        :root {
            --primary-neon: #00f5ff;
            --primary-dark: #0a0a1f;
            --secondary-neon: #ff00ff;
            --accent-cyan: #00ffcc;
            --accent-purple: #9d4edd;
            --glass-white: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-dark: rgba(10, 10, 31, 0.8);
            --glow-primary: 0 0 20px rgba(0, 245, 255, 0.5);
            --glow-secondary: 0 0 20px rgba(255, 0, 255, 0.3);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0a1f 0%, #1a1a2e 50%, #16213e 100%);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Particle Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
        }
        
        /* Navigation */
        .navbar-glass {
            background: var(--glass-dark) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.2rem 0;
        }
        
        .navbar-brand {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.7) !important;
            font-weight: 500;
            padding: 0.5rem 1.2rem !important;
            border-radius: 50px;
            transition: all 0.3s ease;
            margin: 0 0.2rem;
        }
        
        .nav-link:hover {
            color: #fff !important;
            background: rgba(0, 245, 255, 0.1);
        }
        
        .nav-link.active {
            color: #fff !important;
            background: rgba(0, 245, 255, 0.15);
            box-shadow: var(--glow-primary);
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
            position: relative;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #fff, var(--primary-neon));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        /* Glass Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 245, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 245, 255, 0.2);
        }
        
        /* Feature Icons */
        .feature-icon {
            width: 70px;
            height: 70px;
            background: rgba(0, 245, 255, 0.1);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: var(--primary-neon);
        }
        
        /* Buttons */
        .btn-neon {
            background: transparent;
            color: var(--primary-neon);
            border: 2px solid var(--primary-neon);
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-neon:hover {
            background: rgba(0, 245, 255, 0.1);
            box-shadow: var(--glow-primary);
            color: #fff;
        }
        
        .btn-neon-primary {
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            color: #0a0a1f;
            border: none;
        }
        
        .btn-neon-primary:hover {
            background: linear-gradient(45deg, var(--accent-cyan), var(--primary-neon));
            color: #0a0a1f;
        }
        
        .btn-neon-secondary {
            background: linear-gradient(45deg, var(--secondary-neon), var(--accent-purple));
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-neon-secondary:hover {
            background: linear-gradient(45deg, var(--accent-purple), var(--secondary-neon));
            color: white;
            box-shadow: var(--glow-secondary);
        }
        
        /* Stats */
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        
        /* Examples */
        .example-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .example-card:hover {
            background: rgba(0, 245, 255, 0.05);
            border-color: rgba(0, 245, 255, 0.3);
            transform: translateY(-3px);
        }
        
        .example-expression {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.2rem;
            color: var(--accent-cyan);
            margin-bottom: 1rem;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            border-left: 3px solid var(--primary-neon);
        }
        
        /* Profile Section */
        .profile-section {
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            display: none;
            opacity: 0;
            transition: opacity 0.8s ease;
        }
        
        .profile-container.active {
            display: block;
            opacity: 1;
        }
        
        .profile-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s ease;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-neon), var(--secondary-neon));
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1.5rem;
            border: 3px solid rgba(0, 245, 255, 0.3);
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.2);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: var(--primary-neon);
            box-shadow: 0 0 30px rgba(0, 245, 255, 0.4);
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-avatar.fallback {
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #0a0a1f;
            font-weight: bold;
        }
        
        .profile-info h3 {
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .profile-info p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.25rem;
        }
        
        .profile-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .skill-tag {
            background: rgba(0, 245, 255, 0.15);
            color: var(--primary-neon);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Profile Reveal Button */
        .profile-reveal-btn {
            position: relative;
            display: inline-block;
            padding: 1rem 2.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(45deg, var(--accent-purple), var(--secondary-neon));
            border: none;
            border-radius: 50px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s ease;
            margin: 2rem auto;
            display: block;
        }
        
        .profile-reveal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .profile-reveal-btn:hover::before {
            left: 100%;
        }
        
        .profile-reveal-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 0, 255, 0.3);
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .slide-in {
            animation: slideIn 0.8s ease-out forwards;
        }
        
        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }
        
        .glitch-effect {
            animation: glitch 0.5s ease-in-out;
        }
        
        @keyframes photoReveal {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            100% {
                transform: scale(1) rotate(0);
                opacity: 1;
            }
        }
        
        .photo-reveal {
            animation: photoReveal 0.8s ease-out forwards;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(10, 10, 31, 0.8);
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            border-radius: 4px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Particle Background -->
    <div id="particles-js"></div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-glass fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="mdi mdi-logic-gate me-2"></i>LOGICALC
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon text-white">
                    <i class="mdi mdi-menu"></i>
                </span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#examples">Contoh</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stats">Statistik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('calculator') }}">
                            <i class="mdi mdi-calculator-variant-outline me-1"></i>Kalkulator Logika
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('proposition.checker') }}" style="color: var(--secondary-neon) !important;">
                            <i class="mdi mdi-check-decagram-outline me-1"></i>Analisis Proposisi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="hero-title">
                        Analisis Logika Proposisi dengan Teknologi Futuristik
                    </h1>
                    <p class="hero-subtitle">
                        LogiCalc Pro memberikan pengalaman analisis logika yang belum pernah ada sebelumnya. 
                        Dengan antarmuka futuristik dan algoritma canggih, pahami logika proposisi dengan cara yang menyenangkan.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('calculator') }}" class="btn-neon btn-neon-primary pulse-animation">
                            <i class="mdi mdi-rocket-launch-outline"></i>
                            Mulai Kalkulator Logika
                        </a>
                        <a href="{{ route('proposition.checker') }}" class="btn-neon-secondary">
                            <i class="mdi mdi-check-decagram-outline"></i>
                            Analisis Proposisi Lengkap
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="glass-card float-animation">
                        <div class="d-flex align-items-center mb-4">
                            <div class="feature-icon">
                                <i class="mdi mdi-brain"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1" style="color: var(--primary-neon);">Coba Sekarang</h4>
                                <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">
                                    Ekspresi logika yang menarik untuk dicoba:
                                </p>
                            </div>
                        </div>
                        <div class="example-expression">(p ∧ q) → (r ∨ ¬s)</div>
                        <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1.5rem;">
                            Dapatkan analisis lengkap termasuk tabel kebenaran, pohon logika, dan langkah penyelesaian.
                        </p>
                        <button onclick="loadExample('(p ∧ q) → (r ∨ ¬s)')" class="btn-neon w-100 justify-content-center">
                            <i class="mdi mdi-play-circle-outline"></i>
                            Jalankan Ekspresi Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3" style="color: var(--primary-neon);">Fitur Unggulan</h2>
                <p class="lead" style="color: rgba(255, 255, 255, 0.7);">
                    Dilengkapi dengan teknologi terbaru untuk analisis logika yang komprehensif
                </p>
            </div>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card">
                        <div class="feature-icon">
                            <i class="mdi mdi-table"></i>
                        </div>
                        <h4 class="mb-3" style="color: #fff;">Tabel Kebenaran Dinamis</h4>
                        <p style="color: rgba(255, 255, 255, 0.8);">
                            Hasilkan tabel kebenaran lengkap dengan visualisasi interaktif. 
                            Dukungan hingga 8 variabel dengan performa optimal.
                        </p>
                        <ul class="list-unstyled mt-3" style="color: rgba(255, 255, 255, 0.7);">
                            <li class="mb-2"><i class="mdi mdi-check text-success me-2"></i>Auto-generate semua kombinasi</li>
                            <li class="mb-2"><i class="mdi mdi-check text-success me-2"></i>Highlight hasil akhir</li>
                            <li><i class="mdi mdi-check text-success me-2"></i>Ekspor ke CSV/JSON</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card">
                        <div class="feature-icon">
                            <i class="mdi mdi-graph-outline"></i>
                        </div>
                        <h4 class="mb-3" style="color: #fff;">Visualisasi Pohon Logika</h4>
                        <p style="color: rgba(255, 255, 255, 0.8);">
                            Representasi hierarki ekspresi dengan diagram interaktif. 
                            Pahami struktur logika dengan visual yang intuitif.
                        </p>
                        <ul class="list-unstyled mt-3" style="color: rgba(255, 255, 255, 0.7);">
                            <li class="mb-2"><i class="mdi mdi-check text-success me-2"></i>Diagram hierarki lengkap</li>
                            <li class="mb-2"><i class="mdi mdi-check text-success me-2"></i>Zoom & pan interaktif</li>
                            <li><i class="mdi mdi-check text-success me-2"></i>Warna berdasarkan operator</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card" onclick="window.location.href='{{ route('proposition.checker') }}'" style="cursor: pointer; border-color: rgba(255, 0, 255, 0.3);">
                        <div class="feature-icon" style="background: rgba(255, 0, 255, 0.1); color: var(--secondary-neon);">
                            <i class="mdi mdi-check-decagram-outline"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--secondary-neon);">Analisis Proposisi</h4>
                        <p style="color: rgba(255, 255, 255, 0.8);">
                            Identifikasi dan evaluasi pernyataan logika dengan AI. 
                            Analisis validitas, struktur, dan nilai kebenaran setiap kalimat.
                        </p>
                        <ul class="list-unstyled mt-3" style="color: rgba(255, 255, 255, 0.7);">
                            <li class="mb-2"><i class="mdi mdi-check" style="color: var(--secondary-neon);"></i> Analisis kalimat tunggal & majemuk</li>
                            <li class="mb-2"><i class="mdi mdi-check" style="color: var(--secondary-neon);"></i> Validasi dengan teknologi AI</li>
                            <li><i class="mdi mdi-check" style="color: var(--secondary-neon);"></i> Tabel kebenaran otomatis</li>
                        </ul>
                        <div class="text-center mt-4">
                            <span class="btn-neon" style="border-color: var(--secondary-neon); color: var(--secondary-neon);">
                                Coba Sekarang <i class="mdi mdi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Examples Section -->
    <section id="examples" class="py-5" style="background: rgba(0, 245, 255, 0.03);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3" style="color: var(--primary-neon);">Contoh Ekspresi</h2>
                <p class="lead" style="color: rgba(255, 255, 255, 0.7);">
                    Coba ekspresi-ekspresi menarik berikut untuk memahami kekuatan kalkulator
                </p>
            </div>
            
            <div class="row g-4">
                <!-- Example 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="example-card" onclick="loadExample('p ∧ q')">
                        <div class="example-expression">p ∧ q</div>
                        <p style="color: rgba(255, 255, 255, 0.8);">
                            Konjungsi sederhana dengan 2 variabel
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small style="color: var(--accent-cyan);">
                                <i class="mdi mdi-variable"></i> 2 Variabel
                            </small>
                            <span class="badge bg-primary">Konjungsi</span>
                        </div>
                    </div>
                </div>
                
                <!-- Example 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="example-card" onclick="loadExample('¬(p ∧ q) ↔ (¬p ∨ ¬q)')">
                        <div class="example-expression">¬(p ∧ q) ↔ (¬p ∨ ¬q)</div>
                        <p style="color: rgba(255, 255, 255, 0.8);">
                            Hukum De Morgan - Konjungsi
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small style="color: var(--accent-cyan);">
                                <i class="mdi mdi-variable"></i> 2 Variabel
                            </small>
                            <span class="badge bg-success">Biimplikasi</span>
                        </div>
                    </div>
                </div>
                
                <!-- Example 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="example-card" onclick="loadExample('(p → q) ∧ p → q')">
                        <div class="example-expression">(p → q) ∧ p → q</div>
                        <p style="color: rgba(255, 255, 255, 0.8);">
                            Modus Ponens - Tautologi klasik
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small style="color: var(--accent-cyan);">
                                <i class="mdi mdi-variable"></i> 2 Variabel
                            </small>
                            <span class="badge bg-warning">Implikasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3" style="color: var(--primary-neon);">Statistik</h2>
                <p class="lead" style="color: rgba(255, 255, 255, 0.7);">
                    Data real-time dari platform LogiCalc Pro
                </p>
            </div>
            
            <div class="row g-4">
                <!-- Stat 1 -->
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card text-center">
                        <div class="stat-number" id="statCalculations">0</div>
                        <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">
                            <i class="mdi mdi-calculator me-1"></i>Perhitungan
                        </p>
                    </div>
                </div>
                
                <!-- Stat 2 -->
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card text-center">
                        <div class="stat-number" id="statUsers">0</div>
                        <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">
                            <i class="mdi mdi-account-group me-1"></i>Pengguna
                        </p>
                    </div>
                </div>
                
                <!-- Stat 3 -->
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card text-center">
                        <div class="stat-number" id="statExpressions">0</div>
                        <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">
                            <i class="mdi mdi-code-braces me-1"></i>Ekspresi
                        </p>
                    </div>
                </div>
                
                <!-- Stat 4 -->
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card text-center">
                        <div class="stat-number" id="statAccuracy">99.8%</div>
                        <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">
                            <i class="mdi mdi-check-circle me-1"></i>Akurasi
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="mt-5">
                <h3 class="mb-4" style="color: var(--primary-neon);">
                    <i class="mdi mdi-history me-2"></i>Aktivitas Terbaru
                </h3>
                <div class="row" id="recentActivity">
                    <!-- Activity will be loaded here -->
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Section -->
    <section class="profile-section" id="team">
        <div class="container text-center">
            <h2 class="display-4 mb-3" style="color: var(--primary-neon);">Tim Pengembang</h2>
            <p class="lead mb-5" style="color: rgba(255, 255, 255, 0.7);">
                Berikut adalah tim kreatif di balik LogiCalc Pro
            </p>
            
            <!-- Reveal Button -->
            <button class="profile-reveal-btn pulse-animation" id="revealTeamBtn">
                <i class="mdi mdi-account-group me-2"></i>
                Tampilkan Tim Pengembang
            </button>
            
            <!-- Profile Container -->
            <div class="profile-container" id="profileContainer">
                <!-- Profile 1 -->
                <div class="profile-card slide-in" style="animation-delay: 0.1s;">
                    <div class="profile-header">
                        <div class="profile-avatar photo-reveal">
                            <!-- Ganti dengan foto dari folder public/images -->
                            <img src="{{ asset('image/fabian (2).jpeg') }}" alt="Aldi Ramdani" onerror="this.onerror=null; this.parentElement.classList.add('fallback'); this.parentElement.innerHTML='AR';">
                        </div>
                        <div class="profile-info">
                            <h3>Fabian Fakhru Thirafi</h3>
                            <p>250411100035</p>
                            <p><i class="mdi mdi-school me-1"></i>Full Stack Developer</p>
                        </div>
                    </div>
                    <div class="profile-skills">
                        <span class="skill-tag">Algoritma</span>
                    </div>
                </div>
                
                <!-- Profile 2 -->
                <div class="profile-card slide-in" style="animation-delay: 0.2s;">
                    <div class="profile-header">
                        <div class="profile-avatar photo-reveal">
                            <!-- Ganti dengan foto dari folder public/images -->
                            <img src="{{ asset('image/Topek.jpeg') }}" alt="Bima Satria" onerror="this.onerror=null; this.parentElement.classList.add('fallback'); this.parentElement.innerHTML='BS';">
                        </div>
                        <div class="profile-info">
                            <h3>Ahmad Taufikur Rohman</h3>
                            <p>250411100039</p>
                            <p><i class="mdi mdi-school me-1"></i>Pemateri</p>
                        </div>
                    </div>
                    <div class="profile-skills">
                        <span class="skill-tag">JavaScript</span>
                    </div>
                </div>
                
                <!-- Profile 3 -->
                <div class="profile-card slide-in" style="animation-delay: 0.3s;">
                    <div class="profile-header">
                        <div class="profile-avatar photo-reveal">
                            <!-- Ganti dengan foto dari folder public/images -->
                            <img src="{{ asset('image/imell.png') }}" alt="Cindy Putri" onerror="this.onerror=null; this.parentElement.classList.add('fallback'); this.parentElement.innerHTML='CP';">
                        </div>
                        <div class="profile-info">
                            <h3>Imel Fiana Safira</h3>
                            <p>250411100206</p>
                            <p><i class="mdi mdi-school me-1"></i>Pemateri</p>
                        </div>
                    </div>
                    <div class="profile-skills">
                        <span class="skill-tag">Laravel</span>
                    </div>
                </div>
                
                <!-- Profile 4 -->
                <div class="profile-card slide-in" style="animation-delay: 0.4s;">
                    <div class="profile-header">
                        <div class="profile-avatar photo-reveal">
                            <!-- Ganti dengan foto dari folder public/images -->
                            <img src="{{ asset('image/lut.jpeg') }}" alt="Dwiki Wibowo" onerror="this.onerror=null; this.parentElement.classList.add('fallback'); this.parentElement.innerHTML='DW';">
                        </div>
                        <div class="profile-info">
                            <h3>Lutfi</h3>
                            <p>250411100186</p>
                            <p><i class="mdi mdi-school me-1"></i>Pemateri</p>
                        </div>
                    </div>
                    <div class="profile-skills">
                        <span class="skill-tag">UI/UX</span>
                    </div>
                </div>
                
                <!-- Profile 5 -->
                <div class="profile-card slide-in" style="animation-delay: 0.5s;">
                    <div class="profile-header">
                        <div class="profile-avatar photo-reveal">
                            <!-- Ganti dengan foto dari folder public/images -->
                            <img src="{{ asset('images/eka.jpg') }}" alt="Eka Rianti" onerror="this.onerror=null; this.parentElement.classList.add('fallback'); this.parentElement.innerHTML='ER';">
                        </div>
                        <div class="profile-info">
                            <h3>M. Haikal Dwi Januar</h3>
                            <p>250411100037</p>
                            <p><i class="mdi mdi-school me-1"></i>Pemateri</p>
                        </div>
                    </div>
                    <div class="profile-skills">
                        <span class="skill-tag">Database</span>
                    </div>
                </div>
                <div class="profile-card slide-in" style="animation-delay: 0.5s;">
                    <div class="profile-header">
                        <div class="profile-avatar photo-reveal">
                            <!-- Ganti dengan foto dari folder public/images -->
                            <img src="{{ asset('images/eka.jpg') }}" alt="Eka Rianti" onerror="this.onerror=null; this.parentElement.classList.add('fallback'); this.parentElement.innerHTML='ER';">
                        </div>
                        <div class="profile-info">
                            <h3>Ahmad Bagus Indra Permadi</h3>
                            <p>250411100123</p>
                            <p><i class="mdi mdi-school me-1"></i>Pemateri</p>
                        </div>
                    </div>
                    <div class="profile-skills">
                        <span class="skill-tag">Struktur</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="mb-3" style="color: var(--primary-neon);">
                        <i class="mdi mdi-logic-gate me-2"></i>LOGICALC PRO
                    </h4>
                    <p style="color: rgba(255, 255, 255, 0.7);">
                        Platform analisis logika proposisi terdepan dengan teknologi futuristik.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="mb-3" style="color: #fff;">Navigasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Fitur</a></li>
                        <li class="mb-2"><a href="#examples" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Contoh</a></li>
                        <li class="mb-2"><a href="#stats" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Statistik</a></li>
                        <li><a href="#team" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Tim Pengembang</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="mb-3" style="color: #fff;">Sumber Daya</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Dokumentasi</a></li>
                        <li class="mb-2"><a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Tutorial</a></li>
                        <li class="mb-2"><a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Blog</a></li>
                        <li><a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">API</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-4 mb-4">
                    <h5 class="mb-3" style="color: #fff;">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="mdi mdi-email-outline me-2" style="color: var(--accent-cyan);"></i>
                            <span style="color: rgba(255, 255, 255, 0.7);">support@logicalc.pro</span>
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-github me-2" style="color: var(--accent-cyan);"></i>
                            <span style="color: rgba(255, 255, 255, 0.7);">GitHub</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-4 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <p style="color: rgba(255, 255, 255, 0.5);">
                    © 2024 LogiCalc Pro. Dibuat dengan <i class="mdi mdi-heart" style="color: var(--secondary-neon);"></i> untuk komunitas logika.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
    <script>
        // Initialize Particles.js
        particlesJS('particles-js', {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: ["#00f5ff", "#ff00ff", "#00ffcc"] },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 3, random: true },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#00f5ff",
                    opacity: 0.2,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 1,
                    direction: "none",
                    random: true,
                    straight: false
                }
            },
            interactivity: {
                events: {
                    onhover: { enable: true, mode: "repulse" }
                }
            }
        });
        
        // Load example function
        function loadExample(expression) {
            // Save to localStorage
            localStorage.setItem('exampleExpression', expression);
            
            // Redirect to calculator
            window.location.href = "{{ route('calculator') }}";
        }
        
        // Animate counter
        function animateCounter(elementId, finalValue, duration = 1500) {
            const element = document.getElementById(elementId);
            const start = 0;
            const increment = finalValue / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    clearInterval(timer);
                    element.textContent = finalValue + '+';
                } else {
                    element.textContent = Math.floor(current) + '+';
                }
            }, 16);
        }
        
        // Load dashboard stats
        async function loadDashboardStats() {
            try {
                const response = await axios.get('/stats');
                if (response.data.success) {
                    const stats = response.data.data;
                    
                    // Animate counters
                    animateCounter('statCalculations', stats.total_calculations);
                    animateCounter('statUsers', stats.total_users);
                    animateCounter('statExpressions', stats.total_expressions);
                    
                    // Accuracy doesn't need animation
                    document.getElementById('statAccuracy').textContent = stats.accuracy_rate + '%';
                }
            } catch (error) {
                console.error('Failed to load stats:', error);
                
                // Fallback values
                animateCounter('statCalculations', 1250);
                animateCounter('statUsers', 850);
                animateCounter('statExpressions', 5700);
            }
        }
        
        // Load recent activity
        async function loadRecentActivity() {
            try {
                const response = await axios.get('/recent-activity');
                if (response.data.success) {
                    const activities = response.data.data;
                    const container = document.getElementById('recentActivity');
                    
                    let html = '';
                    activities.forEach(activity => {
                        html += `
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="example-card">
                                    <div class="example-expression">${activity.expression}</div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small style="color: var(--accent-cyan);">
                                            <i class="mdi mdi-clock-outline me-1"></i>${activity.time_ago}
                                        </small>
                                        <span class="badge bg-info">${activity.type}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    container.innerHTML = html;
                }
            } catch (error) {
                console.error('Failed to load activity:', error);
            }
        }
        
        // Profile reveal functionality
        function setupProfileReveal() {
            const revealBtn = document.getElementById('revealTeamBtn');
            const profileContainer = document.getElementById('profileContainer');
            let revealed = false;
            
            revealBtn.addEventListener('click', function() {
                if (!revealed) {
                    // Add glitch effect
                    revealBtn.classList.add('glitch-effect');
                    
                    // Change button text
                    setTimeout(() => {
                        revealBtn.innerHTML = '<i class="mdi mdi-eye-off me-2"></i>Sembunyikan Tim Pengembang';
                        revealBtn.style.background = 'linear-gradient(45deg, #ff9800, #ff5722)';
                        
                        // Show profile container
                        profileContainer.classList.add('active');
                        
                        // Scroll to profile section
                        document.getElementById('team').scrollIntoView({ behavior: 'smooth' });
                        
                        revealed = true;
                        
                        // Remove glitch effect
                        setTimeout(() => {
                            revealBtn.classList.remove('glitch-effect');
                        }, 500);
                    }, 300);
                } else {
                    // Hide profile container
                    profileContainer.classList.remove('active');
                    
                    // Change button text back
                    revealBtn.innerHTML = '<i class="mdi mdi-account-group me-2"></i>Tampilkan Tim Pengembang';
                    revealBtn.style.background = 'linear-gradient(45deg, var(--accent-purple), var(--secondary-neon))';
                    
                    revealed = false;
                    
                    // Scroll back to profile section start
                    document.getElementById('team').scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
        
        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            loadRecentActivity();
            setupProfileReveal();
            
            // Add animation on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('slide-in');
                    }
                });
            }, observerOptions);
            
            // Observe profile cards
            document.querySelectorAll('.profile-card').forEach(card => {
                observer.observe(card);
            });
            
            // Set active nav link based on scroll position
            window.addEventListener('scroll', function() {
                const sections = document.querySelectorAll('section[id]');
                const scrollPos = window.scrollY + 100;
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    const sectionId = section.getAttribute('id');
                    
                    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                        document.querySelectorAll('.nav-link').forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === `#${sectionId}`) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>