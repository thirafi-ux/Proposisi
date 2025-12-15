<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiCalc Pro | Analisis Pernyataan & Proposisi</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
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
            --success-gradient: linear-gradient(135deg, #00ff88 0%, #00ccff 100%);
            --error-gradient: linear-gradient(135deg, #ff0066 0%, #ff00ff 100%);
            --warning-gradient: linear-gradient(135deg, #ffcc00 0%, #ff8800 100%);
            --info-gradient: linear-gradient(135deg, #0099ff 0%, #00ccff 100%);
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

        /* Glass Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 245, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 245, 255, 0.2);
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
            transform: translateY(-2px);
        }

        .btn-neon-primary {
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            color: #0a0a1f;
            border: none;
        }

        .btn-neon-primary:hover {
            background: linear-gradient(45deg, var(--accent-cyan), var(--primary-neon));
            color: #0a0a1f;
            box-shadow: var(--glow-primary);
            transform: translateY(-2px);
        }

        .btn-outline-neon {
            background: transparent;
            color: var(--primary-neon);
            border: 2px solid var(--primary-neon);
        }

        .btn-outline-neon:hover {
            background: rgba(0, 245, 255, 0.1);
            color: var(--primary-neon);
            border-color: var(--primary-neon);
            transform: translateY(-2px);
        }

        /* Input Styling */
        .expression-input {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .expression-input:focus {
            border-color: var(--primary-neon);
            box-shadow: 0 0 0 3px rgba(0, 245, 255, 0.1);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .expression-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        /* Mode Selector */
        .mode-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mode-btn {
            flex: 1;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .mode-btn:hover {
            background: rgba(0, 245, 255, 0.1);
            border-color: rgba(0, 245, 255, 0.3);
            color: var(--primary-neon);
            transform: translateY(-3px);
        }

        .mode-btn.active {
            background: rgba(0, 245, 255, 0.15);
            border-color: var(--primary-neon);
            color: var(--primary-neon);
            box-shadow: var(--glow-primary);
        }

        /* Operator Buttons */
        .operator-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        @media (min-width: 768px) {
            .operator-buttons {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        .operator-btn {
            padding: 15px 10px;
            background: rgba(0, 245, 255, 0.1);
            border: 2px solid rgba(0, 245, 255, 0.3);
            border-radius: 12px;
            color: var(--primary-neon);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .operator-btn:hover {
            background: rgba(0, 245, 255, 0.2);
            border-color: var(--primary-neon);
            transform: translateY(-3px);
            box-shadow: var(--glow-primary);
        }

        .operator-btn.selected {
            background: rgba(0, 245, 255, 0.25);
            border-color: var(--primary-neon);
            box-shadow: var(--glow-primary);
        }

        /* Truth Selector */
        .truth-selector {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .truth-btn {
            flex: 1;
            padding: 12px 15px;
            background: rgba(0, 245, 255, 0.1);
            border: 2px solid rgba(0, 245, 255, 0.3);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .truth-btn:hover {
            background: rgba(0, 245, 255, 0.2);
            transform: translateY(-2px);
        }

        .truth-btn.selected {
            background: rgba(0, 245, 255, 0.25);
            border-color: var(--primary-neon);
            color: var(--primary-neon);
            box-shadow: 0 0 15px rgba(0, 245, 255, 0.3);
        }

        /* Result Cards */
        .result-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .result-card:hover {
            border-color: rgba(0, 245, 255, 0.3);
            transform: translateY(-3px);
        }

        .result-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .result-true {
            background: rgba(0, 255, 136, 0.15);
            color: #00ff88;
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .result-false {
            background: rgba(255, 0, 102, 0.15);
            color: #ff0066;
            border: 1px solid rgba(255, 0, 102, 0.3);
        }

        .result-unknown {
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            border: 1px solid rgba(255, 204, 0, 0.3);
        }

        .result-valid {
            background: rgba(0, 245, 255, 0.15);
            color: var(--primary-neon);
            border: 1px solid rgba(0, 245, 255, 0.3);
        }

        /* Confidence Meter */
        .confidence-meter {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin: 10px 0;
            overflow: hidden;
        }

        .confidence-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(90deg, #ff0066, #ff00ff, #00ccff, #00ff88);
        }

        /* Accuracy Badge */
        .accuracy-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: 10px;
        }

        .accuracy-high {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .accuracy-medium {
            background: rgba(255, 204, 0, 0.2);
            color: #ffcc00;
            border: 1px solid rgba(255, 204, 0, 0.3);
        }

        .accuracy-low {
            background: rgba(255, 102, 0, 0.2);
            color: #ff6600;
            border: 1px solid rgba(255, 102, 0, 0.3);
        }

        /* Examples Section */
        .example-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .example-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .example-card:hover {
            background: rgba(0, 245, 255, 0.05);
            border-color: rgba(0, 245, 255, 0.3);
            transform: translateY(-5px);
        }

        .example-card .example-title {
            color: var(--primary-neon);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .example-card .example-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Loading Animation */
        .loading-pulse {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .pulse-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-neon);
            animation: pulse 1.5s infinite ease-in-out;
        }

        .pulse-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .pulse-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.3);
                opacity: 0.5;
            }
        }

        /* Analysis Display */
        .analysis-section {
            background: rgba(10, 10, 31, 0.7);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2rem;
        }

        .analysis-title {
            color: var(--primary-neon);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .analysis-content {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Stat Cards */
        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: rgba(0, 245, 255, 0.3);
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        /* Footer */
        .footer {
            background: rgba(10, 10, 31, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }

        /* Animations */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes glowPulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(0, 245, 255, 0.3);
            }
            50% {
                box-shadow: 0 0 40px rgba(0, 245, 255, 0.5);
            }
        }

        .glow-pulse {
            animation: glowPulse 2s infinite ease-in-out;
        }

        /* Back to Dashboard */
        .back-to-dashboard {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            z-index: 1000;
            background: rgba(10, 10, 31, 0.9);
            border: 2px solid var(--primary-neon);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            color: var(--primary-neon);
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 245, 255, 0.3);
        }

        .back-to-dashboard:hover {
            background: rgba(0, 245, 255, 0.15);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 245, 255, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .back-to-dashboard {
                bottom: 1rem;
                left: 1rem;
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
            
            .mode-selector {
                flex-direction: column;
            }
            
            .operator-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .example-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Custom Scrollbar */
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
                        <a class="nav-link" href="{{ route('dashboard') }}#features">
                            <i class="mdi mdi-feature-search-outline me-1"></i>Fitur
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}#examples">
                            <i class="mdi mdi-code-braces me-1"></i>Contoh
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}#stats">
                            <i class="mdi mdi-chart-bar me-1"></i>Statistik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('calculator') }}">
                            <i class="mdi mdi-calculator-variant me-1"></i>Kalkulator Logika
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('proposition-calculator') }}">
                            <i class="mdi mdi-check-circle me-1"></i>Analisis Proposisi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5" style="padding-top: 140px;">
        <!-- Back to Dashboard Button -->
        <a href="{{ route('dashboard') }}" class="btn-neon back-to-dashboard">
            <i class="mdi mdi-arrow-left me-2"></i>Dashboard
        </a>

        <!-- Hero Section -->
        <div class="text-center mb-5 animate__animated animate__fadeIn">
            <h1 class="display-4 mb-3" style="
                background: linear-gradient(45deg, #fff, var(--primary-neon), var(--accent-cyan));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: 900;
            ">
                <i class="mdi mdi-check-decagram-outline me-3"></i>
                Analisis Pernyataan & Proposisi
            </h1>
            <p class="lead" style="color: rgba(255, 255, 255, 0.7); max-width: 800px; margin: 0 auto;">
                Identifikasi dan evaluasi pernyataan logika dengan teknologi AI dan analisis linguistik canggih.
                Dapatkan insight mendalam tentang validitas dan struktur logika dari setiap kalimat.
            </p>
        </div>

        <!-- Main Calculator Panel -->
        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card mb-4 animate__animated animate__fadeInUp">
                    <!-- Mode Selector -->
                    <div class="mb-4">
                        <label class="form-label mb-3" style="color: var(--primary-neon); font-size: 1.1rem; font-weight: 600;">
                            <i class="mdi mdi-swap-horizontal me-2"></i>Pilih Mode Analisis:
                        </label>
                        <div class="mode-selector">
                            <button class="mode-btn active" data-mode="single">
                                <i class="mdi mdi-text-box-check-outline me-2"></i>
                                Mode Tunggal
                            </button>
                            <button class="mode-btn" data-mode="compound">
                                <i class="mdi mdi-gate-or me-2"></i>
                                Mode Majemuk
                            </button>
                        </div>
                    </div>

                    <!-- Single Mode Section -->
                    <div id="singleMode" class="mode-section active">
                        <div class="mb-4">
                            <label for="inputText" class="form-label" style="color: rgba(255, 255, 255, 0.9);">
                                <i class="mdi mdi-pencil-outline me-2"></i>
                                Masukkan Kalimat/Pernyataan:
                            </label>
                            <textarea 
                                id="inputText" 
                                class="form-control expression-input" 
                                rows="4"
                                placeholder="Contoh: Gajah lebih besar daripada tikus."
                                style="resize: vertical; min-height: 120px;"
                            ></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button id="clearBtn" class="btn btn-outline-neon">
                                <i class="mdi mdi-delete-outline me-2"></i>Hapus
                            </button>
                            <button id="checkBtn" class="btn-neon btn-neon-primary">
                                <i class="mdi mdi-magnify me-2"></i>Analisis Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Compound Mode Section -->
                    <div id="compoundMode" class="mode-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="inputP" class="form-label" style="color: rgba(255, 255, 255, 0.9);">
                                    <i class="mdi mdi-alpha-p-circle me-2"></i>
                                    Proposisi P:
                                </label>
                                <textarea 
                                    id="inputP" 
                                    class="form-control expression-input" 
                                    rows="3"
                                    placeholder="Contoh: Hari ini hujan."
                                ></textarea>
                                
                                <label class="form-label mt-3" style="color: rgba(255, 255, 255, 0.9);">
                                    Nilai Kebenaran P:
                                </label>
                                <div class="truth-selector">
                                    <button class="truth-btn selected" data-prop="p" data-value="true">
                                        <i class="mdi mdi-check-circle-outline me-2"></i>Benar
                                    </button>
                                    <button class="truth-btn" data-prop="p" data-value="false">
                                        <i class="mdi mdi-close-circle-outline me-2"></i>Salah
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="inputQ" class="form-label" style="color: rgba(255, 255, 255, 0.9);">
                                    <i class="mdi mdi-alpha-q-circle me-2"></i>
                                    Proposisi Q:
                                </label>
                                <textarea 
                                    id="inputQ" 
                                    class="form-control expression-input" 
                                    rows="3"
                                    placeholder="Contoh: Ana berangkat ke sekolah."
                                ></textarea>
                                
                                <label class="form-label mt-3" style="color: rgba(255, 255, 255, 0.9);">
                                    Nilai Kebenaran Q:
                                </label>
                                <div class="truth-selector">
                                    <button class="truth-btn selected" data-prop="q" data-value="true">
                                        <i class="mdi mdi-check-circle-outline me-2"></i>Benar
                                    </button>
                                    <button class="truth-btn" data-prop="q" data-value="false">
                                        <i class="mdi mdi-close-circle-outline me-2"></i>Salah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="operator-section mb-4">
                            <label class="form-label" style="color: rgba(255, 255, 255, 0.9);">
                                <i class="mdi mdi-logic-gate me-2"></i>
                                Pilih Operator Logika:
                            </label>
                            <div class="operator-buttons">
                                <button class="operator-btn selected" data-operator="and">
                                    <i class="mdi mdi-and me-2"></i>P ∧ Q<br>
                                    <small>(Dan)</small>
                                </button>
                                <button class="operator-btn" data-operator="or">
                                    <i class="mdi mdi-or me-2"></i>P ∨ Q<br>
                                    <small>(Atau)</small>
                                </button>
                                <button class="operator-btn" data-operator="not">
                                    <i class="mdi mdi-not me-2"></i>¬P<br>
                                    <small>(Bukan)</small>
                                </button>
                                <button class="operator-btn" data-operator="imply">
                                    <i class="mdi mdi-arrow-right me-2"></i>P → Q<br>
                                    <small>(Jika-maka)</small>
                                </button>
                                <button class="operator-btn" data-operator="biconditional">
                                    <i class="mdi mdi-swap-horizontal me-2"></i>P ↔ Q<br>
                                    <small>(JIKA-HANYA-JIKA)</small>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button id="clearCompoundBtn" class="btn btn-outline-neon">
                                <i class="mdi mdi-delete-sweep me-2"></i>Hapus Semua
                            </button>
                            <button id="checkCompoundBtn" class="btn-neon btn-neon-primary">
                                <i class="mdi mdi-calculator-variant me-2"></i>Analisis Proposisi Majemuk
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="resultSection" style="display: none;">
                    <!-- Single Mode Results -->
                    <div id="singleResultSection" class="mode-section active animate__animated animate__fadeInUp">
                        <!-- Results will be injected here -->
                    </div>

                    <!-- Compound Mode Results -->
                    <div id="compoundResultSection" class="mode-section animate__animated animate__fadeInUp" style="display: none;">
                        <!-- Results will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Guide -->
                <div class="glass-card mb-4 animate__animated animate__fadeInRight">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-information-outline me-2"></i>Panduan Analisis
                    </h5>
                    <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.8);">
                        <li class="mb-3">
                            <strong style="color: var(--accent-cyan);">🔍 Pernyataan:</strong>
                            <div style="font-size: 0.9rem; margin-top: 5px;">
                                Kalimat deklaratif yang dapat dinilai benar/salah
                            </div>
                        </li>
                        <li class="mb-3">
                            <strong style="color: var(--accent-cyan);">🎯 Proposisi:</strong>
                            <div style="font-size: 0.9rem; margin-top: 5px;">
                                Pernyataan dengan nilai kebenaran yang jelas
                            </div>
                        </li>
                        <li class="mb-3">
                            <strong style="color: var(--accent-cyan);">📊 Validitas:</strong>
                            <div style="font-size: 0.9rem; margin-top: 5px;">
                                Tingkat kepercayaan hasil analisis
                            </div>
                        </li>
                        <li>
                            <strong style="color: var(--accent-cyan);">⚙️ Mode:</strong>
                            <div style="font-size: 0.9rem; margin-top: 5px;">
                                • Tunggal: Analisis kalimat tunggal<br>
                                • Majemuk: Analisis dengan operator logika
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Quick Stats -->
                <div class="glass-card mb-4 animate__animated animate__fadeInRight">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-chart-bar me-2"></i>Statistik Analisis
                    </h5>
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <div class="stat-number" id="totalAnalyses">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Total Analisis</small>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="stat-number" id="validStatements">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Pernyataan Valid</small>
                        </div>
                        <div class="col-6">
                            <div class="stat-number" id="avgConfidence">0%</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Rata-rata Validitas</small>
                        </div>
                        <div class="col-6">
                            <div class="stat-number" id="propositionCount">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Proposisi</small>
                        </div>
                    </div>
                </div>

                <!-- Quick Examples -->
                <div class="glass-card animate__animated animate__fadeInRight">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-lightbulb-on-outline me-2"></i>Contoh Cepat
                    </h5>
                    <div class="example-grid">
                        <div class="example-card" onclick="setExample('Gajah lebih besar daripada tikus.', 'single')">
                            <div class="example-title">
                                <i class="mdi mdi-check-circle" style="color: #00ff88;"></i>
                                Contoh 100% Valid
                            </div>
                            <div class="example-text">"Gajah lebih besar daripada tikus" - Fakta absolut</div>
                        </div>
                        
                        <div class="example-card" onclick="setExample('Pohon memiliki daun', 'single')">
                            <div class="example-title">
                                <i class="mdi mdi-check-circle" style="color: #00ccff;"></i>
                                Contoh 95% Valid
                            </div>
                            <div class="example-text">"Pohon memiliki daun" - Karakteristik umum</div>
                        </div>
                        
                        <div class="example-card" onclick="setExample('520 < 111', 'single')">
                            <div class="example-title">
                                <i class="mdi mdi-close-circle" style="color: #ff0066;"></i>
                                Contoh 100% Salah
                            </div>
                            <div class="example-text">"520 < 111" - Bertentangan dengan matematika</div>
                        </div>
                        
                        <div class="example-card" onclick="setCompoundExample('Hari ini hujan', 'Ana berangkat ke sekolah', 'and')">
                            <div class="example-title">
                                <i class="mdi mdi-logic-gate" style="color: var(--primary-neon);"></i>
                                Proposisi Majemuk
                            </div>
                            <div class="example-text">"Hari ini hujan DAN Ana berangkat ke sekolah"</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Examples Section -->
        <div class="mt-5">
            <div class="glass-card animate__animated animate__fadeInUp">
                <h4 style="color: var(--primary-neon);" class="mb-4">
                    <i class="mdi mdi-book-open-variant me-2"></i>
                    Contoh Analisis Lengkap
                </h4>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="result-card">
                            <div class="result-indicator result-true">
                                <i class="mdi mdi-check-bold me-2"></i>
                                100% VALID - Pernyataan & Proposisi
                            </div>
                            <h6 style="color: #fff;">"1 + 1 = 2"</h6>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span style="color: rgba(255, 255, 255, 0.7);">Validitas:</span>
                                    <span class="accuracy-badge accuracy-high">100% AKURASI TINGGI</span>
                                </div>
                                <div class="confidence-meter">
                                    <div class="confidence-fill" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="result-card">
                            <div class="result-indicator result-false">
                                <i class="mdi mdi-close-thick me-2"></i>
                                100% SALAH - Pernyataan & Proposisi
                            </div>
                            <h6 style="color: #fff;">"Manusia bisa hidup tanpa oksigen"</h6>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span style="color: rgba(255, 255, 255, 0.7);">Validitas:</span>
                                    <span class="accuracy-badge accuracy-high">100% AKURASI TINGGI</span>
                                </div>
                                <div class="confidence-meter">
                                    <div class="confidence-fill" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="mb-3" style="color: var(--primary-neon);">
                        <i class="mdi mdi-logic-gate me-2"></i>LOGICALC PRO
                    </h4>
                    <p style="color: rgba(255, 255, 255, 0.7);">
                        Platform analisis logika dan proposisi terdepan dengan teknologi futuristik dan algoritma canggih.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="mb-3" style="color: #fff;">Navigasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('dashboard') }}#features" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Fitur</a></li>
                        <li class="mb-2"><a href="{{ route('dashboard') }}#examples" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Contoh</a></li>
                        <li class="mb-2"><a href="{{ route('calculator') }}" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Kalkulator Logika</a></li>
                        <li><a href="{{ route('proposition-calculator') }}" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Analisis Proposisi</a></li>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
    <!-- JavaScript -->
    <script src="{{ asset('js/poposisi.js') }}"></script>
</body>
</html>