<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiCalc Pro | Kalkulator Logika Proposisi</title>
    
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
            box-shadow: var(--glow-primary);
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

        /* Input Styling */
        .expression-input {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 1rem;
            border-radius: 10px;
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

        /* Loading Indicator */
        #loadingIndicator {
            transition: opacity 0.3s ease;
        }

        /* Operator Buttons */
        .logic-operator {
            background: rgba(0, 245, 255, 0.1);
            color: var(--primary-neon);
            border: 2px solid rgba(0, 245, 255, 0.3);
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }

        .logic-operator:hover {
            background: rgba(0, 245, 255, 0.2);
            border-color: var(--primary-neon);
            transform: translateY(-2px);
            box-shadow: var(--glow-primary);
        }

        .logic-operator:active {
            transform: translateY(0);
        }

        /* Glass card for stats */
        .glass-card-sm {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        /* Truth Table */
        .truth-table {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .truth-table th {
            background: rgba(0, 245, 255, 0.15);
            color: var(--primary-neon);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid rgba(0, 245, 255, 0.3);
            padding: 12px 8px;
            text-align: center;
        }

        .truth-table td {
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 8px;
            transition: all 0.2s ease;
            text-align: center;
        }

        .truth-table tr:hover {
            background: rgba(0, 245, 255, 0.05);
        }

        /* Result Classes */
        .result-true {
            background: rgba(0, 255, 204, 0.15);
            color: var(--accent-cyan);
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .result-false {
            background: rgba(255, 0, 255, 0.15);
            color: var(--secondary-neon);
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .result-true:hover,
        .result-false:hover {
            transform: scale(1.05);
            z-index: 10;
        }

        /* Tree Container */
        .tree-container {
            min-height: 400px;
            position: relative;
            overflow: auto;
        }

        /* Legacy Tree Visualization */
        .logic-tree-container {
            background: rgba(10, 10, 31, 0.5);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(0, 245, 255, 0.2);
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
        }

        .structured-tree-diagram {
            position: relative;
            overflow: hidden;
        }

        .tree-node {
            position: absolute;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .tree-node:hover {
            z-index: 100;
        }

        .tree-node::before {
            content: '';
            position: absolute;
            left: -15px;
            top: 50%;
            width: 15px;
            height: 2px;
            background: rgba(0, 245, 255, 0.5);
        }

        .tree-children {
            margin-left: 30px;
            padding-left: 15px;
            border-left: 2px solid rgba(0, 245, 255, 0.3);
        }

        .tree-leaf {
            padding: 8px 12px;
            background: rgba(0, 255, 204, 0.1);
            border: 1px solid var(--accent-cyan);
            border-radius: 8px;
            color: var(--accent-cyan);
            margin: 5px 0;
        }

        .tree-operator {
            padding: 8px 12px;
            background: rgba(0, 245, 255, 0.1);
            border: 1px solid var(--primary-neon);
            border-radius: 8px;
            color: var(--primary-neon);
            margin: 5px 0;
        }

        /* Tree Node Styling */
        .tree-node-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .tree-node-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 245, 255, 0.2);
        }

        .tree-node-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        .tree-node-icon.operator {
            background: rgba(0, 245, 255, 0.2);
            color: var(--primary-neon);
            border: 2px solid var(--primary-neon);
        }

        .tree-node-icon.variable {
            background: rgba(0, 255, 204, 0.2);
            color: var(--accent-cyan);
            border: 2px solid var(--accent-cyan);
        }

        .tree-node-content {
            flex-grow: 1;
        }

        .tree-node-label {
            font-weight: bold;
            font-size: 0.9rem;
        }

        .tree-node-details {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Form Controls */
        .form-check-input:checked {
            background-color: var(--primary-neon);
            border-color: var(--primary-neon);
        }

        .form-check-input:focus {
            border-color: var(--primary-neon);
            box-shadow: 0 0 0 3px rgba(0, 245, 255, 0.1);
        }

        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-select:focus {
            border-color: var(--primary-neon);
            box-shadow: 0 0 0 3px rgba(0, 245, 255, 0.1);
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
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
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

        /* Enhanced Tree Visualization (Baru) */
        .logic-tree-diagram {
            position: relative;
            min-height: 500px;
            background: rgba(10, 10, 31, 0.7);
            border-radius: 12px;
            padding: 30px;
            border: 2px solid rgba(0, 245, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 245, 255, 0.15);
            overflow: auto;
        }

        .tree-diagram-container {
            position: relative;
            min-height: 450px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* Tree Levels */
        .tree-level {
            display: flex;
            justify-content: center;
            gap: 80px;
            margin-bottom: 60px;
            position: relative;
        }

        /* Tree Nodes */
        .tree-node {
            position: relative;
            z-index: 2;
        }

        .tree-node-operator {
            padding: 15px 25px;
            background: linear-gradient(135deg, rgba(0, 245, 255, 0.2), rgba(0, 245, 255, 0.3));
            border: 2px solid var(--primary-neon);
            border-radius: 10px;
            color: var(--primary-neon);
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
            min-width: 120px;
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.3);
            position: relative;
            z-index: 3;
            transition: all 0.3s ease;
        }

        .tree-node-operator:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 245, 255, 0.5);
        }

        .tree-node-variable {
            padding: 12px 20px;
            background: linear-gradient(135deg, rgba(0, 255, 204, 0.2), rgba(0, 255, 204, 0.3));
            border: 2px solid var(--accent-cyan);
            border-radius: 10px;
            color: var(--accent-cyan);
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
            min-width: 80px;
            box-shadow: 0 0 15px rgba(0, 255, 204, 0.3);
            position: relative;
            z-index: 3;
            transition: all 0.3s ease;
        }

        .tree-node-variable:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 255, 204, 0.5);
        }

        /* Tree Connections */
        .tree-connection {
            position: absolute;
            background: linear-gradient(45deg, var(--primary-neon), var(--accent-cyan));
            z-index: 1;
        }

        .tree-connection.vertical {
            width: 3px;
        }

        .tree-connection.horizontal {
            height: 3px;
        }

        .tree-connection.diagonal-left {
            width: 3px;
            transform: rotate(45deg);
        }

        .tree-connection.diagonal-right {
            width: 3px;
            transform: rotate(-45deg);
        }

        /* Tree Structure - Modern Layout */
        .tree-structure {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px;
            position: relative;
        }

        .tree-row {
            display: flex;
            justify-content: center;
            gap: 100px;
            position: relative;
        }

        /* Node Labels */
        .node-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(10, 10, 31, 0.8);
            padding: 2px 8px;
            border-radius: 4px;
            white-space: nowrap;
        }

        .operator-label {
            color: var(--primary-neon);
        }

        .variable-label {
            color: var(--accent-cyan);
        }

        /* Result Alert Styling */
        .result-alert {
            background: linear-gradient(135deg, rgba(0, 245, 255, 0.15), rgba(0, 245, 255, 0.25));
            border: 2px solid var(--primary-neon);
            border-radius: 12px;
            color: #fff;
            padding: 20px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .result-alert strong {
            color: var(--primary-neon);
            font-weight: 700;
        }

        /* Tree Legend */
        .tree-legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            padding: 15px;
            background: rgba(10, 10, 31, 0.8);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .legend-color.operator {
            background: linear-gradient(135deg, var(--primary-neon), rgba(0, 245, 255, 0.5));
        }

        .legend-color.variable {
            background: linear-gradient(135deg, var(--accent-cyan), rgba(0, 255, 204, 0.5));
        }

        /* Simplified Tree View (for complex expressions) */
        .simple-tree-view {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }

        .simple-tree-level {
            display: flex;
            justify-content: center;
            gap: 60px;
        }

        .simple-tree-node {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            position: relative;
            min-width: 100px;
            box-shadow: 0 5px 15px rgba(0, 245, 255, 0.2);
            transition: all 0.3s ease;
        }

        .simple-tree-node.operator {
            background: linear-gradient(135deg, rgba(0, 245, 255, 0.2), rgba(0, 245, 255, 0.3));
            border: 2px solid var(--primary-neon);
            color: var(--primary-neon);
        }

        .simple-tree-node.variable {
            background: linear-gradient(135deg, rgba(0, 255, 204, 0.2), rgba(0, 255, 204, 0.3));
            border: 2px solid var(--accent-cyan);
            color: var(--accent-cyan);
        }

        /* Connection Lines */
        .tree-connector {
            position: absolute;
            transition: all 0.3s ease;
            background: rgba(0, 245, 255, 0.6);
        }

        .tree-connector:hover {
            stroke: #ff00ff;
            stroke-width: 3px;
        }

        .node-type-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            white-space: nowrap;
        }

        .tree-stats {
            display: flex;
            gap: 20px;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .connector-vertical {
            width: 3px;
            height: 40px;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
        }

        .connector-horizontal {
            width: 60px;
            height: 3px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Hierarchical Tree Styles - UPDATE */
        .hierarchical-tree-diagram {
            position: relative;
            overflow: visible !important;
        }

        .hierarchical-tree-nodes {
            position: relative;
            min-height: 500px;
            width: 100%;
        }

        .tree-node.hierarchical-node {
            position: absolute;
            transform-origin: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tree-node.hierarchical-node:hover {
            z-index: 1000 !important;
        }

        .operator-node {
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.3) !important;
        }

        .variable-node {
            box-shadow: 0 0 15px rgba(0, 255, 204, 0.3) !important;
        }

        /* Connection lines */
        .tree-connector {
            stroke-linecap: round;
            transition: all 0.3s ease;
        }

        .tree-connector:hover {
            stroke: #ff00ff !important;
            stroke-width: 3px !important;
        }

        /* Level indicators */
        .tree-level-indicator {
            position: absolute;
            left: 10px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.8rem;
        }

        /* Tree container */
        .tree-diagram-container {
            position: relative;
            min-height: 500px;
            width: 100%;
            overflow: auto;
            padding: 20px;
        }

        /* Back to Dashboard Button - POSISI BARU KIRI BAWAH */
        .back-to-dashboard {
            position: fixed;
            bottom: 2rem;
            left: 2rem; /* Changed from right to left */
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

        /* Additional Hero Styles */
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #fff, var(--primary-neon));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Calculator Title */
        .calculator-title {
            margin-top: 80px;
            padding-top: 40px;
        }

        /* Additional Utility Classes */
        .cursor-pointer {
            cursor: pointer !important;
        }

        .text-neon {
            color: var(--primary-neon) !important;
        }

        .text-cyan {
            color: var(--accent-cyan) !important;
        }

        .bg-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        /* Transition Fixes */
        .glass-card,
        .logic-operator,
        .btn-neon,
        .form-control,
        .form-select,
        .tree-node-operator,
        .tree-node-variable,
        .simple-tree-node {
            transition: all 0.3s ease !important;
        }

        /* Fix for expression input focus */
        .expression-input:focus {
            border-color: var(--primary-neon) !important;
            box-shadow: 0 0 0 3px rgba(0, 245, 255, 0.1) !important;
            background: rgba(255, 255, 255, 0.08) !important;
            color: #fff !important;
            outline: none !important;
        }

        /* Fix for example buttons */
        .btn-outline-neon {
            background: transparent !important;
            color: var(--primary-neon) !important;
            border: 2px solid var(--primary-neon) !important;
            padding: 0.5rem 1rem !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }

        .btn-outline-neon:hover {
            background: rgba(0, 245, 255, 0.1) !important;
            color: var(--primary-neon) !important;
            border-color: var(--primary-neon) !important;
            transform: translateY(-2px) !important;
        }

        /* Responsive untuk Back to Dashboard Button */
        @media (max-width: 768px) {
            .back-to-dashboard {
                bottom: 1rem;
                left: 1rem;
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .back-to-dashboard {
                position: relative;
                bottom: auto;
                left: auto;
                margin-top: 1rem;
                display: inline-block;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
            
            .glass-card {
                padding: 1.5rem;
            }
            
            .calculator-title {
                margin-top: 40px;
                padding-top: 15px;
            }
            
            .tree-children {
                margin-left: 20px;
                padding-left: 10px;
            }
            
            /* Enhanced Tree Responsive */
            .logic-tree-diagram {
                padding: 15px;
                min-height: 400px;
            }
            
            .tree-level {
                gap: 30px;
                margin-bottom: 40px;
            }
            
            .tree-row {
                gap: 40px;
            }
            
            .tree-node-operator {
                padding: 10px 15px;
                min-width: 80px;
                font-size: 0.9rem;
            }
            
            .tree-node-variable {
                padding: 8px 12px;
                min-width: 60px;
                font-size: 0.9rem;
            }
            
            .simple-tree-level {
                gap: 30px;
            }
            
            .simple-tree-node {
                padding: 8px 16px;
                min-width: 70px;
                font-size: 0.9rem;
            }
            
            .tree-legend {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            /* Hierarchical Tree Responsive - UPDATE */
            .hierarchical-tree-nodes {
                transform: scale(0.8);
                transform-origin: top center;
            }
            
            .tree-node.hierarchical-node {
                min-width: 80px !important;
                padding: 8px 12px !important;
                font-size: 0.9rem !important;
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
                        <a class="nav-link active" href="{{ route('calculator') }}">
                            <i class="mdi mdi-calculator-variant me-1"></i>Kalkulator
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

    <!-- Main Content -->
    <div class="container py-5" style="padding-top: 140px;">
        <!-- Back to Dashboard Button (Floating) -->
        <a href="{{ route('dashboard') }}" class="btn-neon back-to-dashboard">
            <i class="mdi mdi-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
        <!-- Calculator Title -->
        <div class="text-center mb-5 calculator-title">
            <h1 class="display-4 mb-3 mt-10" style="color: var(--primary-neon);">
                <i class="mdi mdi-calculator-variant-outline me-2"></i>
                <h1 class="hero-title">Kalkulator Logika Proposisi</h1>
            </h1>
            <p class="lead" style="color: rgba(255, 255, 255, 0.7);">
                Analisis ekspresi logika dengan teknologi futuristik dan algoritma canggih
            </p>
        </div>
        
        <!-- Calculator Section -->
        <div class="row">
            <!-- Main Calculator Panel -->
            <div class="col-lg-8 mb-4">
                <div class="glass-card mb-4">
                    <h4 class="mb-4" style="color: var(--primary-neon);">
                        <i class="mdi mdi-pencil-outline me-2"></i>Kalkulator Logika Proposisi
                    </h4>
                    
                    <!-- CSRF Token -->
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    
                    <!-- Mode Selection -->
                    <div class="mb-4">
                        <label class="form-label" style="color: rgba(255, 255, 255, 0.9);">Mode Perhitungan:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="calculationMode" id="modeTruthTable" value="truth_table" checked>
                            <label class="btn btn-outline-neon" for="modeTruthTable">
                                <i class="mdi mdi-table me-1"></i> Tabel Kebenaran
                            </label>
                            
                            <input type="radio" class="btn-check" name="calculationMode" id="modeStepByStep" value="step_by_step">
                            <label class="btn btn-outline-neon" for="modeStepByStep">
                                <i class="mdi mdi-format-list-numbers me-1"></i> Langkah Demi Langkah
                            </label>
                            
                            <input type="radio" class="btn-check" name="calculationMode" id="modeCustomValues" value="custom_values">
                            <label class="btn btn-outline-neon" for="modeCustomValues">
                                <i class="mdi mdi-calculator-variant me-1"></i> Nilai Kustom
                            </label>
                        </div>
                    </div>
                    
                    <!-- Expression Input -->
                    <div class="mb-4">
                        <label for="expressionInput" class="form-label" style="color: rgba(255, 255, 255, 0.9);">
                            Masukkan Ekspresi Proposisi:
                        </label>
                        <input type="text" 
                               id="expressionInput" 
                               class="form-control expression-input" 
                               placeholder="Contoh: (p ∧ q) → (r ∨ ¬s)"
                               autocomplete="off">
                        <div id="validationMessage" class="mt-2 small" style="min-height: 24px;"></div>
                        <div class="form-text" style="color: rgba(255, 255, 255, 0.6);">
                            Gunakan variabel (p, q, r, ...), operator (¬ ∧ ∨ → ↔ ⊕), dan tanda kurung
                        </div>
                    </div>
                    
                    <!-- Custom Values Section (Initially Hidden) -->
                    <div id="customValuesSection" class="mb-4" style="display: none;">
                        <label class="form-label" style="color: rgba(255, 255, 255, 0.9);">Atur Nilai Variabel:</label>
                        <div id="variableInputs" class="row g-2">
                            <!-- Input variabel dinamis akan ditambahkan di sini -->
                        </div>
                        <div class="form-text" style="color: rgba(255, 255, 255, 0.6);">
                            Atur nilai kebenaran untuk setiap variabel (Benar/Salah)
                        </div>
                    </div>
                    
                    <!-- Operator Pad -->
                    <div class="mb-4">
                    <label class="form-label" style="color: rgba(255, 255, 255, 0.9);">Operator Cepat:</label>
                    <div class="d-flex flex-wrap gap-2" id="operatorPad">
                        <button type="button" class="logic-operator" data-operator="¬">¬ (BUKAN)</button>
                        <button type="button" class="logic-operator" data-operator="∧">∧ (DAN)</button>
                        <button type="button" class="logic-operator" data-operator="∨">∨ (ATAU)</button>
                        <button type="button" class="logic-operator" data-operator="→">→ (JIKA-MAKA)</button>
                        <button type="button" class="logic-operator" data-operator="↔">↔ (JIKA-HANYA-JIKA)</button>
                        <button type="button" class="logic-operator" data-operator="⊕">⊕ (XOR)</button>
                        <button type="button" class="logic-operator" data-operator="(">(</button>
                        <button type="button" class="logic-operator" data-operator=")">)</button>
                    </div>
                </div>
                    
                    <!-- Options -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="simplifyCheck">
                                <label class="form-check-label" style="color: rgba(255, 255, 255, 0.8);">
                                    <i class="mdi mdi-simplify me-1"></i> Tampilkan Bentuk Sederhana
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="showTreeCheck" checked>
                                <label class="form-check-label" style="color: rgba(255, 255, 255, 0.8);">
                                    <i class="mdi mdi-chart-tree me-1"></i> Tampilkan Pohon Logika
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="showStepsCheck">
                                <label class="form-check-label" style="color: rgba(255, 255, 255, 0.8);">
                                    <i class="mdi mdi-foot-print me-1"></i> Tampilkan Langkah Perhitungan
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="autoParseCheck" checked>
                                <label class="form-check-label" style="color: rgba(255, 255, 255, 0.8);">
                                    <i class="mdi mdi-auto-fix me-1"></i> Parsing Otomatis
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calculate Button -->
                    <div class="d-grid">
                        <button class="btn-neon btn-neon-primary btn-lg" id="calculateBtn">
                            <i class="mdi mdi-calculator me-2"></i>Hitung
                        </button>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="mt-4 text-center" style="display: none;">
                        <div class="spinner-border" style="color: var(--primary-neon);" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-3" style="color: rgba(255, 255, 255, 0.8);">Memproses ekspresi...</p>
                    </div>
                    
                    <!-- Quick Examples -->
                    <div class="mt-4">
                        <label class="form-label" style="color: rgba(255, 255, 255, 0.9);">Contoh Cepat:</label>
                        <div class="d-flex flex-wrap gap-2" id="quickExamplesContainer">
                            <!-- Tombol akan dibuat secara dinamis -->
                        </div>
                    </div>
                </div>
                
                <!-- Results Section -->
                <div class="glass-card mt-4" id="resultsSection" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 style="color: var(--primary-neon);">
                            <i class="mdi mdi-table me-2"></i>Hasil Perhitungan
                        </h4>
                        <div class="btn-group">
                            <button class="btn btn-outline-neon btn-sm" onclick="exportCSV()">
                                <i class="mdi mdi-download"></i> CSV
                            </button>
                            <button class="btn btn-outline-neon btn-sm" onclick="exportJSON()">
                                <i class="mdi mdi-code-json"></i> JSON
                            </button>
                            <button class="btn btn-outline-neon btn-sm" onclick="printResults()">
                                <i class="mdi mdi-printer"></i> Cetak
                            </button>
                        </div>
                    </div>
                    
                    <div id="resultsContent"></div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Guide -->
                <div class="glass-card mb-4">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-information-outline me-2"></i>Cara Penggunaan
                    </h5>
                    <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.8);">
                        <li class="mb-2"><strong>Variabel:</strong> p, q, r, s, ...</li>
                        <li class="mb-2"><strong>Operator:</strong>
                            <ul class="mb-2">
                                <li>¬ : BUKAN (Negasi)</li>
                                <li>∧ : DAN (Konjungsi)</li>
                                <li>∨ : ATAU (Disjungsi)</li>
                                <li>→ : JIKA-MAKA (Implikasi)</li>
                                <li>↔ : JIKA-HANYA-JIKA (Bi-kondisional)</li>
                                <li>⊕ : XOR (Exclusive OR)</li>
                            </ul>
                        </li>
                        <li><strong>Mode:</strong>
                            <ul>
                                <li><em>Tabel Kebenaran:</em> Tabel lengkap semua kombinasi</li>
                                <li><em>Langkah Demi Langkah:</em> Langkah perhitungan detail</li>
                                <li><em>Nilai Kustom:</em> Evaluasi dengan nilai spesifik</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                
                <!-- Variable Input Panel -->
                <div class="glass-card mb-4" id="variablePanel" style="display: none;">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-cog-outline me-2"></i>Pengaturan Variabel
                    </h5>
                    <div id="variableSettings">
                        <!-- Pengaturan variabel dinamis akan ditambahkan di sini -->
                    </div>
                </div>
                
                <!-- History -->
                <div class="glass-card mb-4">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-history me-2"></i>Perhitungan Terakhir
                    </h5>
                    <div id="historyList" class="list-group list-group-flush">
                        <div class="text-center py-3">
                            <small style="color: rgba(255, 255, 255, 0.5);">Belum ada riwayat</small>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="glass-card">
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-chart-bar me-2"></i>Statistik Cepat
                    </h5>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-number" id="totalCalculations">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Total Hitung</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-number" id="tautologyCount">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Tautologi</small>
                        </div>
                        <div class="col-6">
                            <div class="stat-number" id="variableCount">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Rata-rata Variabel</small>
                        </div>
                        <div class="col-6">
                            <div class="stat-number" id="complexityScore">0</div>
                            <small style="color: rgba(255, 255, 255, 0.7);">Tingkat Kompleksitas</small>
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
                        Platform analisis logika proposisi terdepan dengan teknologi futuristik.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="mb-3" style="color: #fff;">Navigasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('dashboard') }}#features" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Fitur</a></li>
                        <li class="mb-2"><a href="{{ route('dashboard') }}#examples" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Contoh</a></li>
                        <li class="mb-2"><a href="{{ route('dashboard') }}#stats" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Statistik</a></li>
                        <li><a href="{{ route('calculator') }}" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">Kalkulator</a></li>
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
    
    <script src="{{ asset('js/calculator.js') }}"></script>

    <!-- Simple initialization without conflicts -->
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup tombol contoh cepat secara dinamis
            const examples = [
                { name: 'p ∧ q', expr: 'p ∧ q' },
                { name: '(p → q) ∧ (¬p ∧ q)', expr: '(p → q) ∧ (¬p ∧ q)' },
                { name: 'De Morgan 1', expr: '¬(p ∧ q) ↔ (¬p ∨ ¬q)' },
                { name: 'De Morgan 2', expr: '¬(p ∨ q) ↔ (¬p ∧ ¬q)' },
                { name: 'Tautologi', expr: 'p → (q → p)' },
                { name: 'Modus Ponens', expr: '(p → q) ∧ p → q' },
                { name: 'p ∨ ¬p', expr: 'p ∨ ¬p' },
                { name: 'p ∧ ¬p', expr: 'p ∧ ¬p' }
            ];
            
            const container = document.getElementById('quickExamplesContainer');
            if (container) {
                container.innerHTML = '';
                examples.forEach(example => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-outline-neon';
                    btn.textContent = example.name;
                    btn.addEventListener('click', function() {
                        // Fokuskan ke input ekspresi
                        const expressionInput = document.getElementById('expressionInput');
                        if (expressionInput) {
                            expressionInput.value = example.expr;
                            expressionInput.focus();
                            
                            // Trigger event input untuk memicu validasi
                            expressionInput.dispatchEvent(new Event('input', { bubbles: true }));
                            
                            // Tampilkan pesan validasi
                            const validationMessage = document.getElementById('validationMessage');
                            if (validationMessage) {
                                validationMessage.innerHTML = `<span style="color: var(--accent-cyan);">
                                    <i class="mdi mdi-check-circle"></i> Contoh dimuat: ${example.expr}
                                </span>`;
                                setTimeout(() => {
                                    if (validationMessage) {
                                        validationMessage.innerHTML = '';
                                    }
                                }, 3000);
                            }
                        }
                    });
                    container.appendChild(btn);
                });
            }
        });
        
        // Fungsi untuk memasukkan operator ke input
        function insertOperator(operator) {
            const input = document.getElementById('expressionInput');
            if (!input) return;
            
            // Dapatkan posisi kursor saat ini
            const start = input.selectionStart;
            const end = input.selectionEnd;
            const text = input.value;
            
            // Sisipkan operator di posisi kursor
            input.value = text.substring(0, start) + operator + text.substring(end);
            
            // Pindahkan kursor setelah operator yang disisipkan
            const newPosition = start + operator.length;
            input.setSelectionRange(newPosition, newPosition);
            input.focus();
            
            // Trigger event input untuk validasi
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        
        // Setup operator buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Setup tombol operator
            document.querySelectorAll('.logic-operator').forEach(btn => {
                btn.addEventListener('click', function() {
                    const operatorText = this.textContent;
                    // Ambil operator dari teks tombol (karakter pertama sebelum spasi)
                    const operator = operatorText.split(' ')[0];
                    if (operator && ['¬', '∧', '∨', '→', '↔', '⊕', '(', ')'].includes(operator)) {
                        insertOperator(operator);
                    }
                });
            });
            
            // Setup example buttons yang sudah ada
            document.querySelectorAll('.btn-outline-neon').forEach(btn => {
                if (btn.textContent.includes('p') || 
                    btn.textContent.includes('De') || 
                    btn.textContent.includes('Tautologi') ||
                    btn.textContent.includes('Modus')) {
                    btn.addEventListener('click', function() {
                        const exampleMap = {
                            'p ∧ q': 'p ∧ q',
                            '(p → q) ∧ (¬p ∧ q)': '(p → q) ∧ (¬p ∧ q)',
                            'De Morgan 1': '¬(p ∧ q) ↔ (¬p ∨ ¬q)',
                            'De Morgan 2': '¬(p ∨ q) ↔ (¬p ∧ ¬q)',
                            'Tautologi': 'p → (q → p)',
                            'Modus Ponens': '(p → q) ∧ p → q',
                            'p ∨ ¬p': 'p ∨ ¬p',
                            'p ∧ ¬p': 'p ∧ ¬p'
                        };
                        
                        const expr = exampleMap[this.textContent];
                        if (expr) {
                            const input = document.getElementById('expressionInput');
                            if (input) {
                                input.value = expr;
                                input.focus();
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        }
                    });
                }
            });
        });
    </script> --}}
</body>
</html>