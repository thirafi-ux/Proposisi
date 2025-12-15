// =============================================================================
// LogiCalc Pro - Kalkulator Logika Proposisi
// File: calculator.js (VERSI FINAL KOMPREHENSIF)
// =============================================================================

console.log('✅ Calculator JS loaded - Comprehensive Final Version');

// =============================================================================
// VARIABEL GLOBAL
// =============================================================================
let currentExpression = '';
let calculationHistory = [];
let currentVariables = [];
let currentMode = 'truth_table';
let treeData = null;
let lastOperatorTime = 0;
let isCalculating = false;

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// =============================================================================
// FUNGSI INISIALISASI
// =============================================================================

/**
 * Inisialisasi Particles.js background
 */
function initParticles() {
    if (typeof particlesJS !== 'undefined') {
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
    }
}

// =============================================================================
// FUNGSI PEMBERSIHAN & VALIDASI EKSPRESI - DIPERBAIKI TOTAL
// =============================================================================

/**
 * Membersihkan ekspresi logika - VERSI OPTIMIZED UNTUK SEMUA OPERATOR
 */
function cleanExpression(expression) {
    if (!expression || expression.trim() === '') {
        return { display: '', parsed: '' };
    }
    
    let cleaned = expression.trim();
    
    // Mapping simbol alternatif
    const replacementMap = {
        '~': '¬', '!': '¬',
        '&': '∧', '&&': '∧',
        '|': '∨', '||': '∨',
        '->': '→', '=>': '→',
        '<->': '↔', '<=>': '↔',
        '^': '⊕', 'xor': '⊕',
        'AND': '∧', 'OR': '∨', 'NOT': '¬',
        'IMPLIES': '→', 'IFF': '↔', 'XOR': '⊕'
    };
    
    // Ganti simbol dengan iterasi
    Object.keys(replacementMap).forEach(key => {
        const regex = new RegExp(key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
        cleaned = cleaned.replace(regex, replacementMap[key]);
    });
    
    // Hapus spasi berlebih, pertahankan keterbacaan
    cleaned = cleaned.replace(/\s+/g, ' ').trim();
    
    // Buat versi parsed tanpa spasi untuk validasi
    let parsedExpr = '';
    for (let i = 0; i < cleaned.length; i++) {
        const char = cleaned[i];
        if (char !== ' ') {
            parsedExpr += char;
        }
    }
    
    // Khusus untuk tampilan, beri spasi yang sesuai
    let displayExpr = '';
    for (let i = 0; i < parsedExpr.length; i++) {
        const char = parsedExpr[i];
        const prevChar = parsedExpr[i - 1];
        const nextChar = parsedExpr[i + 1];
        
        if (['∧', '∨', '→', '↔', '⊕'].includes(char)) {
            // Operator biner - tambah spasi sebelum dan sesudah
            if (displayExpr.length > 0 && displayExpr[displayExpr.length - 1] !== ' ') {
                displayExpr += ' ';
            }
            displayExpr += char;
            if (nextChar && nextChar !== ' ') {
                displayExpr += ' ';
            }
        } else if (char === '¬') {
            // Negasi - tidak ada spasi setelahnya
            displayExpr += char;
        } else if (char === '(' || char === ')') {
            // Kurung - tanpa spasi
            displayExpr += char;
        } else if (/[a-zA-Z]/.test(char)) {
            // Variabel - langsung
            displayExpr += char;
        }
    }
    
    displayExpr = displayExpr.replace(/\s+/g, ' ').trim();
    
    console.log('CleanExpression - Original:', expression);
    console.log('CleanExpression - Display:', displayExpr);
    console.log('CleanExpression - Parsed:', parsedExpr);
    
    return {
        display: displayExpr,
        parsed: parsedExpr
    };
}

/**
 * Parse ekspresi menjadi token untuk validasi yang lebih akurat
 */
function parseExpressionTokens(expression) {
    const tokens = [];
    let currentToken = '';
    
    for (let i = 0; i < expression.length; i++) {
        const char = expression[i];
        
        if (['¬', '∧', '∨', '→', '↔', '⊕', '(', ')'].includes(char)) {
            if (currentToken) {
                tokens.push(currentToken);
                currentToken = '';
            }
            tokens.push(char);
        } else if (/[a-zA-Z]/.test(char)) {
            currentToken += char;
        }
    }
    
    if (currentToken) {
        tokens.push(currentToken);
    }
    
    return tokens;
}

/**
 * Validasi ekspresi logika - VERSI LEBIH FLEKSIBEL UNTUK OPERATOR BERTURUTAN
 */
function validateExpression() {
    const expression = document.getElementById('expressionInput').value.trim();
    const validationMessage = document.getElementById('validationMessage');
    
    if (!expression) {
        validationMessage.innerHTML = '';
        return true;
    }
    
    // Clean dulu
    const cleanedResult = cleanExpression(expression);
    const parsedExpr = cleanedResult.parsed;
    
    console.log('Validating expression:', parsedExpr);
    
    // 1. Cek parentheses balance
    let balance = 0;
    for (let char of parsedExpr) {
        if (char === '(') balance++;
        if (char === ')') balance--;
        if (balance < 0) break;
    }
    
    if (balance !== 0) {
        validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Tanda kurung tidak seimbang</span>';
        return false;
    }
    
    // 2. Cek karakter valid
    const validChars = /^[a-zA-Z¬∧∨→↔⊕()]+$/;
    if (!validChars.test(parsedExpr)) {
        validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Karakter tidak valid</span>';
        return false;
    }
    
    // 3. Cek setidaknya ada satu variabel
    if (!/[a-zA-Z]/.test(parsedExpr)) {
        validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Ekspresi harus mengandung variabel</span>';
        return false;
    }
    
    // 4. Parsing token untuk validasi yang lebih akurat
    const tokens = parseExpressionTokens(parsedExpr);
    console.log('Validation tokens:', tokens);
    
    if (tokens.length === 0) {
        validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Tidak ada token yang valid</span>';
        return false;
    }
    
    // 5. Validasi sequence token
    const binaryOps = ['∧', '∨', '→', '↔', '⊕'];
    
    for (let i = 0; i < tokens.length; i++) {
        const token = tokens[i];
        const nextToken = tokens[i + 1];
        const prevToken = tokens[i - 1];
        
        console.log(`Token[${i}]: ${token}, Next: ${nextToken}, Prev: ${prevToken}`);
        
        // Token adalah operator biner
        if (binaryOps.includes(token)) {
            // Operator biner tidak boleh di awal
            if (i === 0) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Operator biner tidak boleh di awal ekspresi</span>';
                return false;
            }
            
            // Operator biner tidak boleh di akhir
            if (i === tokens.length - 1) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Operator biner tidak boleh di akhir ekspresi</span>';
                return false;
            }
            
            // Sebelum operator biner harus: variabel, kurung tutup, atau negasi+variabel
            const beforeValid = 
                (prevToken && /^[a-zA-Z]+$/.test(prevToken)) || 
                prevToken === ')' ||
                (prevToken === '¬' && tokens[i - 2] && /^[a-zA-Z]+$/.test(tokens[i - 2]));
            
            if (!beforeValid) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Sebelum operator biner harus variabel atau ekspresi dalam kurung</span>';
                return false;
            }
            
            // Setelah operator biner boleh: variabel, negasi, atau kurung buka
            // PERUBAHAN PENTING: Izinkan operator biner diikuti negasi (∧ ¬, ∨ ¬, dll)
            const afterValid = 
                (nextToken && /^[a-zA-Z]+$/.test(nextToken)) ||
                nextToken === '¬' ||
                nextToken === '(';
            
            if (!afterValid) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Setelah operator biner harus variabel, negasi, atau kurung buka</span>';
                return false;
            }
        }
        
        // Token adalah negasi
        else if (token === '¬') {
            // Negasi tidak boleh di akhir
            if (i === tokens.length - 1) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Negasi tidak boleh di akhir ekspresi</span>';
                return false;
            }
            
            // Setelah negasi harus: variabel atau kurung buka
            if (nextToken && !(/^[a-zA-Z]+$/.test(nextToken) || nextToken === '(')) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Setelah negasi harus variabel atau kurung buka</span>';
                return false;
            }
        }
        
        // Token adalah kurung buka
        else if (token === '(') {
            // Setelah ( harus: variabel, negasi, atau kurung buka lain
            if (nextToken && !(/^[a-zA-Z]+$/.test(nextToken) || nextToken === '¬' || nextToken === '(')) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Setelah kurung buka harus variabel, negasi, atau kurung buka</span>';
                return false;
            }
        }
        
        // Token adalah kurung tutup
        else if (token === ')') {
            // Setelah ) harus: operator biner, kurung tutup, atau akhir
            if (nextToken && !(binaryOps.includes(nextToken) || nextToken === ')' || i === tokens.length - 1)) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Setelah kurung tutup harus operator biner atau kurung tutup</span>';
                return false;
            }
        }
        
        // Token adalah variabel
        else if (/^[a-zA-Z]+$/.test(token)) {
            // Setelah variabel boleh: operator biner, kurung tutup, atau akhir
            if (nextToken && !(binaryOps.includes(nextToken) || nextToken === ')' || i === tokens.length - 1)) {
                validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Setelah variabel harus operator biner atau kurung tutup</span>';
                return false;
            }
        }
    }
    
    // 6. Cek pola-pola khusus yang tetap tidak valid
    // Dua operator biner berurutan masih tidak boleh
    if (/[∧∨→↔⊕]{2,}/.test(parsedExpr)) {
        validationMessage.innerHTML = '<span style="color: #ff00ff">❌ Dua operator biner tidak boleh berurutan</span>';
        return false;
    }
    
    // Update input dengan versi display yang bersih
    document.getElementById('expressionInput').value = cleanedResult.display;
    
    // Validasi berhasil
    validationMessage.innerHTML = '<span style="color: #00ffcc">✅ Ekspresi valid</span>';
    return true;
}

// =============================================================================
// FUNGSI UNTUK MEMBUAT CONTOH CEPAT
// =============================================================================

/**
 * Buat tombol contoh cepat secara dinamis
 */
function createQuickExampleButtons() {
    const examples = [
        { name: 'p ∧ q', expr: 'p ∧ q' },
        { name: 'p → q', expr: 'p → q' },
        { name: 'p ∨ q', expr: 'p ∨ q' },
        { name: '¬p', expr: '¬p' },
        { name: 'p ↔ q', expr: 'p ↔ q' },
        { name: 'p ⊕ q', expr: 'p ⊕ q' },
        { name: 'p ∧ ¬q', expr: 'p ∧ ¬q' },
        { name: 'p ∨ ¬q', expr: 'p ∨ ¬q' },
        { name: 'p → ¬q', expr: 'p → ¬q' },
        { name: 'p ↔ ¬q', expr: 'p ↔ ¬q' },
        { name: 'De Morgan 1', expr: '¬(p ∧ q) ↔ (¬p ∨ ¬q)' },
        { name: 'De Morgan 2', expr: '¬(p ∨ q) ↔ (¬p ∧ ¬q)' },
        { name: 'Tautologi', expr: 'p → (q → p)' },
        { name: 'Distributif', expr: 'p ∧ (q ∨ r) ↔ (p ∧ q) ∨ (p ∧ r)' },
        { name: 'Kompleks', expr: '(p → q) ∧ (q → r) → (p → r)' }
    ];
    
    const container = document.getElementById('quickExamplesContainer');
    if (!container) return;
    
    container.innerHTML = '';
    examples.forEach(example => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-sm btn-outline-neon mb-1 me-1';
        btn.textContent = example.name;
        btn.style.cssText = 'transition: all 0.3s ease !important;';
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            loadExample(example.expr);
        });
        
        container.appendChild(btn);
    });
}

// =============================================================================
// FUNGSI UTAMA KALKULATOR
// =============================================================================

/**
 * Masukkan operator ke input field - DIPERBAIKI
 */
function insertOperator(operator) {
    const now = Date.now();
    if (now - lastOperatorTime < 300) {
        console.log('🛑 Mencegah double click operator');
        return;
    }
    lastOperatorTime = now;
    
    const input = document.getElementById('expressionInput');
    if (!input) return;
    
    const start = input.selectionStart;
    const end = input.selectionEnd;
    const text = input.value;
    
    let insertText = operator;
    
    // Formatting untuk keterbacaan
    if (operator === '¬') {
        // Negasi - langsung tanpa spasi
        insertText = '¬';
    } else if (['∧', '∨', '→', '↔', '⊕'].includes(operator)) {
        // Operator biner - tambah spasi
        insertText = ' ' + operator + ' ';
    } else {
        // Kurung - tanpa spasi
        insertText = operator;
    }
    
    input.value = text.substring(0, start) + insertText + text.substring(end);
    
    // Update cursor position
    if (operator === '¬') {
        input.selectionStart = input.selectionEnd = start + 1;
    } else if (['∧', '∨', '→', '↔', '⊕'].includes(operator)) {
        input.selectionStart = input.selectionEnd = start + insertText.length;
    } else {
        input.selectionStart = input.selectionEnd = start + 1;
    }
    
    input.focus();
    
    // Validasi setelah insert
    setTimeout(validateExpression, 10);
}

/**
 * Muat contoh ekspresi
 */
function loadExample(expression) {
    console.log('Loading example:', expression);
    
    const cleanedExpression = cleanExpression(expression).display;
    const input = document.getElementById('expressionInput');
    
    if (input) {
        input.value = cleanedExpression;
        input.focus();
        
        // Trigger validation dan update UI
        setTimeout(() => {
            validateExpression();
            if (currentMode === 'custom_values') {
                generateVariableInputs();
            }
        }, 10);
        
        showAlert(`Contoh "${expression}" dimuat!`, 'info');
    }
}

// =============================================================================
// SETUP EVENT HANDLERS
// =============================================================================

/**
 * Setup operator buttons
 */
function setupOperatorButtons() {
    const operatorPad = document.getElementById('operatorPad');
    if (!operatorPad) return;
    
    // Setup event listeners
    const buttons = operatorPad.querySelectorAll('.logic-operator');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const operator = this.getAttribute('data-operator');
            if (operator) {
                insertOperator(operator);
            }
        });
    });
}

/**
 * Setup mode selection
 */
function setupModeSelection() {
    document.querySelectorAll('input[name="calculationMode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentMode = this.value;
            const customValuesSection = document.getElementById('customValuesSection');
            
            if (currentMode === 'custom_values') {
                customValuesSection.style.display = 'block';
                generateVariableInputs();
            } else {
                customValuesSection.style.display = 'none';
            }
        });
    });
}

/**
 * Generate variable inputs for custom values mode
 */
function generateVariableInputs() {
    const expression = document.getElementById('expressionInput').value.trim();
    if (!expression) return;
    
    const variableInputs = document.getElementById('variableInputs');
    variableInputs.innerHTML = '';
    
    // Extract variables
    const variables = expression.match(/[a-zA-Z]/g) || [];
    const uniqueVariables = [...new Set(variables)];
    currentVariables = uniqueVariables;
    
    uniqueVariables.forEach(variable => {
        const col = document.createElement('div');
        col.className = 'col-md-6 mb-2';
        col.innerHTML = `
            <div class="input-group">
                <span class="input-group-text" style="background: rgba(0, 245, 255, 0.1); color: var(--primary-neon); border-color: rgba(255, 255, 255, 0.1);">${variable}</span>
                <select class="form-select variable-value" data-variable="${variable}" style="background: rgba(255, 255, 255, 0.05); color: #fff; border-color: rgba(255, 255, 255, 0.1);">
                    <option value="true">Benar</option>
                    <option value="false" selected>Salah</option>
                </select>
            </div>
        `;
        variableInputs.appendChild(col);
    });
}

// =============================================================================
// FUNGSI PERHITUNGAN UTAMA
// =============================================================================

/**
 * Main calculation function - DIPERBAIKI DAN DIPERSINGKAT
 */
async function calculate() {
    if (isCalculating) return;
    
    const expression = document.getElementById('expressionInput').value.trim();
    
    if (!expression) {
        showAlert('Masukkan ekspresi terlebih dahulu!', 'warning');
        return;
    }
    
    if (!validateExpression()) {
        showAlert('Ekspresi tidak valid! Periksa kembali.', 'warning');
        return;
    }
    
    // Set flag mencegah multiple clicks
    isCalculating = true;
    
    const cleanedResult = cleanExpression(expression);
    const parsedExpression = cleanedResult.parsed;
    const displayExpression = cleanedResult.display;
    
    currentExpression = displayExpression;
    const mode = document.querySelector('input[name="calculationMode"]:checked').value;
    currentMode = mode;
    
    // Prepare data
    const data = {
        expression: parsedExpression,
        mode: mode,
        _token: csrfToken
    };
    
    // Add custom values if mode is custom_values
    if (mode === 'custom_values') {
        const customValues = {};
        document.querySelectorAll('.variable-value').forEach(select => {
            customValues[select.dataset.variable] = select.value === 'true';
        });
        data.custom_values = customValues;
    }
    
    // Add options
    const simplify = document.getElementById('simplifyCheck')?.checked || false;
    const showTree = document.getElementById('showTreeCheck')?.checked || false;
    const showSteps = document.getElementById('showStepsCheck')?.checked || false;
    
    if (simplify) data.simplify = simplify;
    if (showTree) data.show_tree = showTree;
    if (showSteps) data.show_steps = showSteps;
    
    // Show loading
    const loadingIndicator = document.getElementById('loadingIndicator');
    const calculateBtn = document.getElementById('calculateBtn');
    const resultsSection = document.getElementById('resultsSection');
    
    loadingIndicator.style.display = 'block';
    resultsSection.style.display = 'none';
    calculateBtn.disabled = true;
    
    try {
        console.log('📤 Mengirim ke server:', data);
        const response = await axios.post('/calculate', data, {
            timeout: 30000,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        console.log('📥 Response dari server:', response.data);
        
        if (response.data.success) {
            // Simpan ke history
            addToHistory(displayExpression, response.data.data);
            
            // Generate tree structure
            treeData = generateCleanTreeStructure(parsedExpression);
            
            // Tampilkan hasil berdasarkan tipe
            if (response.data.type === 'truth_table') {
                displayTruthTable(response.data.data);
            } else if (response.data.type === 'step_by_step') {
                displayStepByStep(response.data.data);
            } else if (response.data.type === 'custom_evaluation') {
                displayCustomEvaluation(response.data.data);
            } else {
                displayGenericResult(response.data.data);
            }
            
            resultsSection.style.display = 'block';
            resultsSection.scrollIntoView({ behavior: 'smooth' });
            
        } else {
            throw new Error(response.data.message || 'Terjadi kesalahan pada server');
        }
    } catch (error) {
        console.error('Calculation error:', error);
        
        let errorMessage = 'Error: ';
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage += error.response.data.message;
        } else if (error.message) {
            errorMessage += error.message;
        } else {
            errorMessage += 'Tidak dapat terhubung ke server';
        }
        
        showAlert(errorMessage, 'error');
        
        // Fallback: Tampilkan hasil lokal sederhana
        displayFallbackResult(parsedExpression, mode);
    } finally {
        loadingIndicator.style.display = 'none';
        calculateBtn.disabled = false;
        isCalculating = false;
    }
}

/**
 * Fallback result jika server error
 */
function displayFallbackResult(expression, mode) {
    const resultsContent = document.getElementById('resultsContent');
    
    const variables = [...new Set(expression.match(/[a-zA-Z]/g) || [])];
    const numVars = variables.length;
    const numRows = Math.pow(2, numVars);
    
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-table me-2"></i>Hasil Perhitungan (Fallback Mode)
        </h5>
        
        <div class="alert alert-warning mb-4">
            <i class="mdi mdi-alert-circle me-2"></i>
            Server sedang sibuk. Menampilkan hasil perhitungan lokal sederhana.
        </div>
        
        <div class="result-alert mb-4">
            <strong>Ekspresi:</strong> ${expression}<br>
            <strong>Variabel:</strong> ${variables.join(', ')}<br>
            <strong>Mode:</strong> ${mode}<br>
            <strong>Kombinasi:</strong> ${numRows} baris
        </div>
    `;
    
    // Simple tree visualization
    html += generateLogicTreeHTML();
    
    resultsContent.innerHTML = html;
    
    // Generate and render simple tree
    setTimeout(() => {
        treeData = generateSimpleTree(expression);
        if (treeData) {
            renderCleanLogicTree(treeData);
        }
    }, 100);
}

// =============================================================================
// FUNGSI DISPLAY HASIL
// =============================================================================

/**
 * Display truth table
 */
function displayTruthTable(data) {
    console.log('Displaying truth table with data:', data);
    
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-table me-2"></i>Tabel Kebenaran
        </h5>
        
        <div class="result-alert mb-4">
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: var(--primary-neon);">Ekspresi:</strong><br>
                    <span style="color: var(--accent-cyan); font-family: 'JetBrains Mono', monospace;">${data.main_expression || currentExpression}</span>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: var(--primary-neon);">Variabel:</strong><br>
                    <span style="color: #fff;">${(data.variables || []).join(', ')}</span>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: var(--primary-neon);">Sifat:</strong><br>
                    <span style="color: ${data.is_tautology ? 'var(--accent-cyan)' : data.is_contradiction ? 'var(--secondary-neon)' : '#ff9900'}; font-weight: bold;">
                        ${data.is_tautology ? 'Tautologi' : data.is_contradiction ? 'Kontradiksi' : 'Kontingen'}
                    </span>
                </div>
            </div>
        </div>
    `;
    
    // Tampilkan tabel jika ada data
    if (data.table && data.headers) {
        html += `
        <div class="table-responsive mb-4">
            <table class="table truth-table">
                <thead>
                    <tr>
        `;
        
        data.headers.forEach(header => {
            const isMain = header === (data.main_expression || currentExpression);
            const headerStyle = isMain ? 'background: rgba(0, 245, 255, 0.2) !important; color: var(--accent-cyan) !important;' : '';
            html += `<th style="${headerStyle}">${header}</th>`;
        });
        
        html += '</tr></thead><tbody>';
        
        data.table.forEach(row => {
            html += '<tr>';
            data.headers.forEach(header => {
                const value = row[header] || '';
                const isMain = header === (data.main_expression || currentExpression);
                const cellClass = value === 'B' || value === true || value === '1' ? 'result-true' : 'result-false';
                const boldClass = isMain ? 'fw-bold' : '';
                const displayValue = value === true || value === '1' ? 'B' : (value === false || value === '0' ? 'S' : value);
                
                html += `<td class="${cellClass} ${boldClass}">${displayValue}</td>`;
            });
            html += '</tr>';
        });
        
        html += `
                </tbody>
            </table>
        </div>
        `;
    } else {
        html += '<div class="alert alert-info">Tabel tidak tersedia untuk ekspresi ini</div>';
    }
    
    // Add logic tree if showTreeCheck is checked
    const showTreeCheck = document.getElementById('showTreeCheck');
    if (showTreeCheck && showTreeCheck.checked) {
        html += generateLogicTreeHTML();
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    
    if (showTreeCheck && showTreeCheck.checked && treeData) {
        setTimeout(() => {
            renderCleanLogicTree(treeData);
        }, 100);
    }
}

/**
 * Display step by step
 */
function displayStepByStep(data) {
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-format-list-numbers me-2"></i>Langkah Demi Langkah
        </h5>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Ringkasan</h6>
                    <p><strong>Tautologi:</strong> ${data.properties?.is_tautology ? 'Ya' : 'Tidak'}</p>
                    <p><strong>Kontradiksi:</strong> ${data.properties?.is_contradiction ? 'Ya' : 'Tidak'}</p>
                    <p><strong>Benar:</strong> ${data.properties?.true_count || 0}/${data.table?.length || 0}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Tabel Singkat</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
    `;
    
    (data.headers || []).forEach(header => {
        html += `<th>${header}</th>`;
    });
    
    html += '</tr></thead><tbody>';
    
    (data.table || []).forEach(row => {
        html += '<tr>';
        (data.headers || []).forEach(header => {
            const value = row[header];
            const cellClass = value === true || value === 'B' || value === '1' ? 'result-true' : 'result-false';
            const displayValue = value === true || value === '1' ? 'B' : (value === false || value === '0' ? 'S' : value);
            html += `<td class="${cellClass}">${displayValue}</td>`;
        });
        html += '</tr>';
    });
    
    html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <h6>Detail Langkah:</h6>
    `;
    
    (data.all_steps || []).forEach((stepSet, index) => {
        html += `
            <div class="glass-card-sm mb-3">
                <h6>Baris ${stepSet.row || index + 1}: ${stepSet.result || 'N/A'}</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Langkah</th>
                            <th>Ekspresi</th>
                            <th>Hasil</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        (stepSet.steps || []).forEach(step => {
            const resultClass = step.result === 'True' || step.result === true ? 'result-true' : 'result-false';
            const displayResult = step.result === true || step.result === 'True' ? 'B' : 'S';
            
            html += `
                <tr>
                    <td>${step.step || 'N/A'}</td>
                    <td><code>${step.expression || 'N/A'}</code></td>
                    <td class="${resultClass}">${displayResult}</td>
                    <td>${step.details || 'N/A'}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
    });
    
    const showTreeCheck = document.getElementById('showTreeCheck');
    if (showTreeCheck && showTreeCheck.checked) {
        html += generateLogicTreeHTML();
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    
    if (showTreeCheck && showTreeCheck.checked && treeData) {
        setTimeout(() => {
            renderCleanLogicTree(treeData);
        }, 100);
    }
}

/**
 * Display custom evaluation
 */
function displayCustomEvaluation(data) {
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-calculator-variant me-2"></i>Evaluasi dengan Nilai Kustom
        </h5>
        
        <div class="result-alert mb-4">
            <strong>Hasil Akhir:</strong> 
            <span class="${data.final_result_text === 'True' || data.final_result === true ? 'result-true' : 'result-false'}">
                ${data.final_result_text || (data.final_result ? 'True' : 'False')}
            </span>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Nilai Variabel:</h6>
    `;
    
    if (data.values) {
        for (const [variable, value] of Object.entries(data.values)) {
            html += `<p><strong>${variable}:</strong> ${value ? 'B (Benar)' : 'S (Salah)'}</p>`;
        }
    } else {
        html += '<p>Tidak ada data nilai</p>';
    }
    
    html += `
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Ringkasan:</h6>
                    <p><strong>Jumlah Langkah:</strong> ${data.steps?.length || 0}</p>
                </div>
            </div>
        </div>
    `;
    
    if (data.steps && data.steps.length > 0) {
        html += `
            <h6>Langkah Perhitungan:</h6>
            <table class="table">
                <thead>
                    <tr>
                        <th>Langkah</th>
                        <th>Ekspresi</th>
                        <th>Hasil</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        data.steps.forEach(step => {
            const resultClass = step.result === 'True' || step.result === true ? 'result-true' : 'result-false';
            const displayResult = step.result === true || step.result === 'True' ? 'B' : 'S';
            
            html += `
                <tr>
                    <td>${step.step || 'N/A'}</td>
                    <td><code>${step.expression || 'N/A'}</code></td>
                    <td class="${resultClass}">${displayResult}</td>
                    <td>${step.details || 'N/A'}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
    }
    
    const showTreeCheck = document.getElementById('showTreeCheck');
    if (showTreeCheck && showTreeCheck.checked) {
        html += generateLogicTreeHTML();
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    
    if (showTreeCheck && showTreeCheck.checked && treeData) {
        setTimeout(() => {
            renderCleanLogicTree(treeData);
        }, 100);
    }
}

/**
 * Generate HTML for logic tree container
 */
/**
 * Generate HTML for logic tree container - DIPERBAIKI (tanpa kotak dalam)
 */
function generateLogicTreeHTML() {
    return `
        <div class="mt-5">
            <h5 class="text-center mb-4" style="color: var(--primary-neon);">
                <i class="mdi mdi-chart-tree me-2"></i>Visualisasi Pohon Logika
            </h5>
            <div class="logic-tree-diagram" style="padding: 20px; background: rgba(10, 10, 31, 0.7); border: 2px solid rgba(0, 245, 255, 0.2); border-radius: 15px;">
                <div id="logicTreeVisualization" style="overflow: auto; position: relative;">
                    <div id="treeDrawing" class="tree-diagram-container" style="position: relative; padding: 20px; min-height: 400px;">
                        <div class="text-center" style="color: rgba(255, 255, 255, 0.5); padding: 60px;">
                            <i class="mdi mdi-chart-tree" style="font-size: 4rem; opacity: 0.5;"></i>
                            <p class="mt-3">Visualisasi pohon logika akan ditampilkan di sini...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Display generic result
 */
function displayGenericResult(data) {
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-information-outline me-2"></i>Hasil Perhitungan
        </h5>
        
        <div class="result-alert mb-4">
            <strong>Mode:</strong> ${currentMode === 'truth_table' ? 'Tabel Kebenaran' : 
                                   currentMode === 'step_by_step' ? 'Langkah Demi Langkah' : 'Nilai Kustom'}<br>
            <strong>Ekspresi:</strong> ${currentExpression}
        </div>
        
        <pre style="color: rgba(255, 255, 255, 0.8); background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 10px;">
            ${JSON.stringify(data, null, 2)}
        </pre>
    `;
    
    document.getElementById('resultsContent').innerHTML = html;
}

// =============================================================================
// FUNGSI TREE STRUCTURE
// =============================================================================

/**
 * Generate clean tree structure
 */
function generateCleanTreeStructure(expression) {
    try {
        const cleanExpr = expression.replace(/\s+/g, '');
        
        const precedence = {
            '↔': 1, '→': 2, '⊕': 3, '∨': 4, '∧': 5, '¬': 6
        };
        
        let nodeCounter = 0;
        function generateNodeId() {
            return `node_${nodeCounter++}`;
        }
        
        function parseExpression(expr, parentId = null, isLeft = true, level = 0) {
            if (!expr || expr === '') return null;
            
            let currentExpr = expr;
            
            // Remove outer parentheses
            while (currentExpr[0] === '(' && currentExpr[currentExpr.length - 1] === ')') {
                let balance = 0;
                let isOuter = true;
                
                for (let i = 0; i < currentExpr.length; i++) {
                    if (currentExpr[i] === '(') balance++;
                    if (currentExpr[i] === ')') balance--;
                    
                    if (balance === 0 && i < currentExpr.length - 1) {
                        isOuter = false;
                        break;
                    }
                }
                
                if (isOuter) {
                    currentExpr = currentExpr.substring(1, currentExpr.length - 1);
                } else {
                    break;
                }
            }
            
            // Find operator with lowest precedence
            let balance = 0;
            let minPrecedence = Infinity;
            let splitIndex = -1;
            let splitOperator = '';
            
            for (let i = 0; i < currentExpr.length; i++) {
                const char = currentExpr[i];
                
                if (char === '(') balance++;
                else if (char === ')') balance--;
                else if (balance === 0 && precedence[char] !== undefined) {
                    if (precedence[char] <= minPrecedence) {
                        minPrecedence = precedence[char];
                        splitIndex = i;
                        splitOperator = char;
                    }
                }
            }
            
            // If no operator found at this level
            if (splitIndex === -1) {
                if (currentExpr[0] === '¬') {
                    const nodeId = generateNodeId();
                    const operand = currentExpr.substring(1);
                    
                    return {
                        id: nodeId,
                        type: 'operator',
                        operator: '¬',
                        expression: currentExpr,
                        operand: parseExpression(operand, nodeId, true, level + 1),
                        level: level,
                        parentId: parentId,
                        isLeft: isLeft
                    };
                } else if (currentExpr.match(/^[a-zA-Z]$/)) {
                    return {
                        id: generateNodeId(),
                        type: 'variable',
                        value: currentExpr,
                        expression: currentExpr,
                        level: level,
                        parentId: parentId,
                        isLeft: isLeft
                    };
                } else if (currentExpr.match(/^¬[a-zA-Z]$/)) {
                    const nodeId = generateNodeId();
                    
                    return {
                        id: nodeId,
                        type: 'operator',
                        operator: '¬',
                        expression: currentExpr,
                        operand: {
                            id: generateNodeId(),
                            type: 'variable',
                            value: currentExpr.substring(1),
                            expression: currentExpr.substring(1),
                            level: level + 1,
                            parentId: nodeId,
                            isLeft: true
                        },
                        level: level,
                        parentId: parentId,
                        isLeft: isLeft
                    };
                } else {
                    return {
                        id: generateNodeId(),
                        type: 'expression',
                        value: currentExpr,
                        expression: currentExpr,
                        level: level,
                        parentId: parentId,
                        isLeft: isLeft
                    };
                }
            }
            
            // Split based on found operator
            const leftExpr = currentExpr.substring(0, splitIndex);
            const rightExpr = currentExpr.substring(splitIndex + 1);
            
            const nodeId = generateNodeId();
            
            return {
                id: nodeId,
                type: 'operator',
                operator: splitOperator,
                expression: currentExpr,
                left: parseExpression(leftExpr, nodeId, true, level + 1),
                right: parseExpression(rightExpr, nodeId, false, level + 1),
                level: level,
                parentId: parentId,
                isLeft: isLeft
            };
        }
        
        const root = parseExpression(cleanExpr);
        
        if (!root) {
            throw new Error('Failed to parse expression');
        }
        
        // Build levels and connections
        const levels = [];
        const connections = [];
        const nodeMap = new Map();
        
        function traverse(node) {
            if (!node) return;
            
            if (!levels[node.level]) levels[node.level] = [];
            levels[node.level].push(node);
            nodeMap.set(node.id, node);
            
            if (node.parentId) {
                connections.push({
                    from: node.parentId,
                    to: node.id,
                    isLeft: node.isLeft
                });
            }
            
            if (node.operand) traverse(node.operand);
            if (node.left) traverse(node.left);
            if (node.right) traverse(node.right);
        }
        
        traverse(root);
        
        // Sort levels for neat display
        levels.forEach(level => {
            if (level) {
                level.sort((a, b) => {
                    return a.expression.length - b.expression.length;
                });
            }
        });
        
        return {
            type: 'clean_tree',
            root: root,
            levels: levels.filter(level => level && level.length > 0),
            connections,
            nodeMap,
            totalNodes: nodeCounter,
            maxDepth: levels.filter(level => level).length,
            expression: expression
        };
        
    } catch (error) {
        console.error('Error generating clean tree structure:', error);
        return generateSimpleTree(expression);
    }
}

/**
 * Generate simple tree for fallback
 */
function generateSimpleTree(expression) {
    const cleanExpr = expression.replace(/\s+/g, '');
    
    const levels = [];
    const connections = [];
    const nodeMap = new Map();
    let nodeId = 0;
    
    // Root node
    const rootId = `node_${nodeId++}`;
    const rootNode = {
        id: rootId,
        type: 'expression',
        expression: expression,
        level: 0
    };
    nodeMap.set(rootId, rootNode);
    levels[0] = [rootNode];
    
    // Extract variables
    const variables = cleanExpr.match(/[a-zA-Z]/g) || [];
    if (variables.length > 0) {
        levels[1] = [];
        variables.forEach((variable, index) => {
            const varId = `node_${nodeId++}`;
            const varNode = {
                id: varId,
                type: 'variable',
                value: variable,
                expression: variable,
                level: 1,
                parentId: rootId,
                isLeft: index < variables.length / 2
            };
            nodeMap.set(varId, varNode);
            levels[1].push(varNode);
            connections.push({
                from: rootId,
                to: varId,
                isLeft: varNode.isLeft
            });
        });
    }
    
    return {
        type: 'simple_tree',
        root: rootNode,
        levels: levels.filter(level => level && level.length > 0),
        connections,
        nodeMap,
        totalNodes: nodeId,
        maxDepth: levels.filter(level => level).length,
        expression: expression
    };
}

/**
 * Render clean logic tree visualization
 */
/**
 * Render clean logic tree visualization
 */
function renderCleanLogicTree(treeData) {
    const container = document.getElementById('treeDrawing');
    if (!container || !treeData) {
        console.error('Tree container not found or no tree data');
        return;
    }
    
    container.innerHTML = '';
    
    if (!treeData.levels || treeData.levels.length === 0) {
        container.innerHTML = '<div class="text-center p-5" style="color: rgba(255,255,255,0.5)">Tidak dapat membuat visualisasi pohon untuk ekspresi ini</div>';
        return;
    }
    
    try {
        const treeHTML = generateCleanTreeHTML(treeData);
        container.innerHTML = treeHTML;
        
        // Add interactive features
        setTimeout(() => {
            const treeNodes = container.querySelectorAll('.tree-node-box');
            
            treeNodes.forEach(node => {
                node.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.12) translateZ(0)';
                    this.style.boxShadow = '0 12px 25px rgba(0, 245, 255, 0.45)';
                    this.style.zIndex = '1000';
                });
                
                node.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) translateZ(0)';
                    this.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.25)';
                    this.style.zIndex = '';
                });
            });
        }, 100);
    } catch (error) {
        console.error('Error rendering tree:', error);
        container.innerHTML = '<div class="text-center p-5" style="color: rgba(255,255,255,0.5)">Error rendering tree visualization</div>';
    }
}

/**
 * Generate clean tree HTML - DIPERBAIKI (legend di tengah bawah pohon)
 */
function generateCleanTreeHTML(treeData) {
    const { levels, connections } = treeData;
    
    if (!levels || levels.length === 0) {
        return '<div class="text-center p-5" style="color: rgba(255,255,255,0.5)">Data pohon logika tidak tersedia</div>';
    }
    
    // Calculate dimensions
    const maxNodesInLevel = Math.max(...levels.map(level => level.length));
    const totalLevels = levels.length;
    
    const baseNodeWidth = 140;
    const baseNodeHeight = 60;
    const verticalSpacing = 120;
    const horizontalSpacing = 60;
    const containerPadding = 30;
    
    const containerWidth = Math.max(800, maxNodesInLevel * (baseNodeWidth + horizontalSpacing) + containerPadding * 2);
    const containerHeight = totalLevels * verticalSpacing + containerPadding * 2;
    
    // Position nodes
    const positionedNodes = [];
    const nodePositions = new Map();
    
    levels.forEach((levelNodes, levelIndex) => {
        const nodesInLevel = levelNodes.length;
        const levelWidth = nodesInLevel * (baseNodeWidth + horizontalSpacing) - horizontalSpacing;
        const startX = (containerWidth - levelWidth) / 2 + baseNodeWidth / 2;
        
        levelNodes.forEach((node, nodeIndex) => {
            const x = startX + nodeIndex * (baseNodeWidth + horizontalSpacing);
            const y = containerPadding + levelIndex * verticalSpacing;
            
            const positionedNode = {
                ...node,
                x,
                y,
                width: baseNodeWidth,
                height: baseNodeHeight,
                isRoot: levelIndex === 0
            };
            
            positionedNodes.push(positionedNode);
            nodePositions.set(node.id, positionedNode);
        });
    });
    
    // Generate SVG connections
    let svgHTML = `<svg width="${containerWidth}" height="${containerHeight}" style="position: absolute; top: 0; left: 0; z-index: 1; pointer-events: none;">`;
    
    connections.forEach(conn => {
        const fromNode = nodePositions.get(conn.from);
        const toNode = nodePositions.get(conn.to);
        
        if (fromNode && toNode) {
            const fromX = fromNode.x;
            const fromY = fromNode.y + baseNodeHeight;
            const toX = toNode.x;
            const toY = toNode.y;
            
            const midY = fromY + (toY - fromY) / 2;
            
            svgHTML += `
                <path d="M ${fromX} ${fromY} 
                        C ${fromX} ${midY}, ${toX} ${midY}, ${toX} ${toY}"
                      stroke="rgba(0, 245, 255, 0.6)"
                      stroke-width="2"
                      fill="none"
                      stroke-linecap="round" />
            `;
        }
    });
    
    svgHTML += '</svg>';
    
    // Generate nodes HTML
    let nodesHTML = '';
    
    positionedNodes.forEach(node => {
        const isOperator = node.type === 'operator';
        const isRoot = node.isRoot;
        
        let nodeClass = 'tree-node-box';
        let nodeStyle = '';
        let contentHTML = '';
        
        if (isOperator) {
            nodeClass += ' operator-node';
            nodeStyle = `
                background: linear-gradient(145deg, 
                    rgba(0, 245, 255, 0.2), 
                    rgba(0, 245, 255, 0.1));
                border: 2px solid var(--primary-neon);
                color: var(--primary-neon);
            `;
            
            const operatorName = getOperatorName(node.operator);
            const displayExpr = node.expression.length > 12 ? 
                node.expression.substring(0, 10) + '...' : node.expression;
            
            contentHTML = `
                <div class="node-expression" style="font-size: 0.85rem; font-weight: bold; line-height: 1.2;">
                    ${displayExpr}
                </div>
                <div class="node-subtitle" style="font-size: 0.65rem; opacity: 0.9; margin-top: 2px;">
                    ${operatorName}
                </div>
            `;
        } else {
            nodeClass += ' variable-node';
            nodeStyle = `
                background: linear-gradient(145deg, 
                    rgba(0, 255, 204, 0.2), 
                    rgba(0, 255, 204, 0.1));
                border: 2px solid var(--accent-cyan);
                color: var(--accent-cyan);
            `;
            
            contentHTML = `
                <div class="node-expression" style="font-size: 1rem; font-weight: bold;">
                    ${node.value || node.expression}
                </div>
                <div class="node-subtitle" style="font-size: 0.65rem; opacity: 0.9; margin-top: 2px;">
                    Variabel
                </div>
            `;
        }
        
        if (isRoot) {
            nodeStyle += `
                box-shadow: 0 0 25px rgba(0, 245, 255, 0.4) !important;
                border-width: 3px !important;
            `;
        }
        
        nodesHTML += `
            <div class="${nodeClass}" 
                 id="${node.id}"
                 data-level="${node.level}"
                 data-type="${node.type}"
                 style="position: absolute;
                        left: ${node.x - baseNodeWidth/2}px;
                        top: ${node.y}px;
                        ${nodeStyle}
                        width: ${baseNodeWidth}px;
                        height: ${baseNodeHeight}px;
                        padding: 8px 5px;
                        border-radius: 10px;
                        cursor: pointer;
                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                        text-align: center;
                        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                        backdrop-filter: blur(8px);
                        z-index: ${100 - node.level};
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        overflow: hidden;
                        user-select: none;">
                
                ${contentHTML}
                
                <div class="node-level-indicator" style="position: absolute; top: 4px; right: 4px; 
                      background: ${isRoot ? 'var(--primary-neon)' : 'rgba(255,255,255,0.15)'};
                      color: ${isRoot ? '#0a0a1f' : 'rgba(255,255,255,0.8)'};
                      font-size: 0.55rem;
                      padding: 1px 6px;
                      border-radius: 10px;
                      font-weight: bold;">
                    L${node.level}
                </div>
            </div>
        `;
    });
    
    // Stats
    const operatorCount = positionedNodes.filter(n => n.type === 'operator').length;
    const variableCount = positionedNodes.filter(n => n.type === 'variable').length;
    
    const legendHTML = `
        <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 10;">
            <div class="tree-legend" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; padding: 12px 20px; background: rgba(10, 10, 31, 0.9); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.15); max-width: 600px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                <div class="legend-item" style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.95); font-size: 0.8rem; padding: 4px 12px; border-radius: 6px; background: rgba(0,245,255,0.1);">
                    <div class="legend-color" style="width: 12px; height: 12px; border-radius: 3px; background: var(--primary-neon);"></div>
                    <span>Operator (${operatorCount})</span>
                </div>
                <div class="legend-item" style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.95); font-size: 0.8rem; padding: 4px 12px; border-radius: 6px; background: rgba(0,255,204,0.1);">
                    <div class="legend-color" style="width: 12px; height: 12px; border-radius: 3px; background: var(--accent-cyan);"></div>
                    <span>Variabel (${variableCount})</span>
                </div>
            </div>
        </div>
    `;
    
    // PERUBAHAN PENTING: Legend ditempatkan di tengah bawah container pohon
    return `
        <div class="clean-tree-diagram" style="position: relative; 
                        width: ${containerWidth}px; 
                        min-height: ${containerHeight + 80}px;  <!-- Tambah tinggi untuk legend -->
                        margin: 0 auto;
                        padding-bottom: 20px;">
            <div style="position: relative; width: 100%; height: ${containerHeight}px;">
                ${svgHTML}
                <div class="clean-tree-nodes" style="position: relative; z-index: 2; width: 100%; height: 100%;">
                    ${nodesHTML}
                </div>
            </div>
            ${legendHTML}
        </div>
    `;
}
/**
 * Helper function to get operator name
 */
function getOperatorName(operator) {
    const operatorNames = {
        '¬': 'Negasi',
        '∧': 'Konjungsi',
        '∨': 'Disjungsi',
        '→': 'Implikasi',
        '↔': 'Bikondisional',
        '⊕': 'XOR'
    };
    return operatorNames[operator] || operator;
}

// =============================================================================
// FUNGSI HISTORY & STATISTIK
// =============================================================================

/**
 * Add to history
 */
function addToHistory(expression, data) {
    let resultText = 'Kontingen';
    if (data.is_tautology) resultText = 'Tautologi';
    else if (data.is_contradiction) resultText = 'Kontradiksi';
    
    const historyItem = {
        expression: expression,
        timestamp: new Date().toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        }),
        mode: currentMode,
        variables: data.variables || [],
        result: resultText
    };
    
    calculationHistory.unshift(historyItem);
    
    if (calculationHistory.length > 10) {
        calculationHistory = calculationHistory.slice(0, 10);
    }
    
    updateHistoryDisplay();
    updateStats();
    saveHistoryToLocalStorage();
}

/**
 * Update history display
 */
function updateHistoryDisplay() {
    const historyList = document.getElementById('historyList');
    if (!historyList) return;
    
    if (calculationHistory.length === 0) {
        historyList.innerHTML = `
            <div class="text-center py-3">
                <small style="color: rgba(255, 255, 255, 0.5);">Belum ada riwayat</small>
            </div>
        `;
        return;
    }
    
    let html = '';
    calculationHistory.forEach(item => {
        const safeExpression = item.expression.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        html += `
            <div class="list-group-item list-group-item-action" 
                onclick="loadExample('${safeExpression}')" 
                style="background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.8); cursor: pointer; transition: all 0.2s;">
                <div class="d-flex w-100 justify-content-between">
                    <code class="mb-1" style="color: var(--accent-cyan); max-width: 70%; overflow: hidden; text-overflow: ellipsis;">
                        ${item.expression}
                    </code>
                    <small style="color: rgba(255, 255, 255, 0.5);">${item.timestamp}</small>
                </div>
                <small style="color: rgba(255, 255, 255, 0.6);">
                    ${item.mode === 'truth_table' ? 'Tabel' : 
                      item.mode === 'step_by_step' ? 'Langkah' : 'Kustom'} • 
                    ${item.variables.length} var • ${item.result}
                </small>
            </div>
        `;
    });
    
    historyList.innerHTML = html;
}

/**
 * Save history to localStorage
 */
function saveHistoryToLocalStorage() {
    try {
        localStorage.setItem('propositionHistory', JSON.stringify(calculationHistory));
    } catch (e) {
        console.error('Error saving history to localStorage:', e);
    }
}

/**
 * Load history from localStorage
 */
function loadHistoryFromLocalStorage() {
    try {
        const savedHistory = localStorage.getItem('propositionHistory');
        if (savedHistory) {
            calculationHistory = JSON.parse(savedHistory);
            updateHistoryDisplay();
        }
    } catch (e) {
        console.error('Error loading history from localStorage:', e);
    }
}

/**
 * Update stats
 */
function updateStats() {
    const totalCalculations = document.getElementById('totalCalculations');
    const tautologyCount = document.getElementById('tautologyCount');
    const variableCount = document.getElementById('variableCount');
    const complexityScore = document.getElementById('complexityScore');
    
    if (totalCalculations) {
        totalCalculations.textContent = calculationHistory.length;
    }
    
    const tautologies = calculationHistory.filter(item => item.result === 'Tautologi').length;
    if (tautologyCount) {
        tautologyCount.textContent = tautologies;
    }
    
    const avgVariables = calculationHistory.length > 0 
        ? (calculationHistory.reduce((sum, item) => sum + (item.variables?.length || 0), 0) / calculationHistory.length).toFixed(1)
        : '0.0';
    if (variableCount) {
        variableCount.textContent = avgVariables;
    }
    
    if (complexityScore) {
        if (currentExpression) {
            let complexity = 0;
            const operators = ['¬', '∧', '∨', '→', '↔', '⊕'];
            operators.forEach(op => {
                complexity += (currentExpression.split(op).length - 1) * 2;
            });
            complexity += (currentVariables?.length || 0) * 3;
            complexity = Math.min(Math.round(complexity), 99);
            complexityScore.textContent = complexity;
        } else {
            complexityScore.textContent = 0;
        }
    }
}

// =============================================================================
// FUNGSI UTILITAS
// =============================================================================

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const colors = {
        error: '#ff00ff',
        warning: '#ff9900',
        info: '#00f5ff',
        success: '#00ffcc'
    };
    
    // Hapus alert sebelumnya
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert alert alert-dismissible fade show';
    alertDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 9999;
        background: rgba(10, 10, 31, 0.95);
        border: 1px solid ${colors[type]};
        color: #fff;
        max-width: 400px;
        backdrop-filter: blur(10px);
        animation: slideIn 0.3s ease;
    `;
    
    alertDiv.innerHTML = `
        <div style="color: ${colors[type]}; display: flex; align-items: center;">
            <i class="mdi mdi-${type === 'error' ? 'alert-circle' : type === 'warning' ? 'alert' : 'information'} me-2"></i>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.remove()" style="filter: invert(1);"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

/**
 * Export functions
 */
function exportCSV() {
    showAlert('Fitur ekspor CSV akan segera tersedia!', 'info');
}

function exportJSON() {
    showAlert('Fitur ekspor JSON akan segera tersedia!', 'info');
}

function printResults() {
    window.print();
}


function wrapNegationsWithParentheses(expression) {
    console.log('Original for wrapping:', expression);
    
    // Regex untuk menemukan pola ¬[variabel] tanpa kurung
    const negationPattern = /¬([a-zA-Z])/g;
    
    // Ganti ¬p dengan (¬p)
    let wrapped = expression.replace(negationPattern, '(¬$1)');
    
    // Handle kasus nested negations: ¬¬p menjadi (¬(¬p))
    const nestedNegationPattern = /¬\(([^)]+)\)/g;
    let previous;
    do {
        previous = wrapped;
        wrapped = wrapped.replace(nestedNegationPattern, '(¬($1))');
    } while (wrapped !== previous);
    
    console.log('After wrapping negations:', wrapped);
    return wrapped;
}

/**
 * Main calculation function - VERSI OPTIMIZED UNTUK SERVER YANG STRICT
 */
async function calculate() {
    if (isCalculating) return;
    
    const expression = document.getElementById('expressionInput').value.trim();
    
    if (!expression) {
        showAlert('Masukkan ekspresi terlebih dahulu!', 'warning');
        return;
    }
    
    if (!validateExpression()) {
        showAlert('Ekspresi tidak valid! Periksa kembali.', 'warning');
        return;
    }
    
    // Set flag mencegah multiple clicks
    isCalculating = true;
    
    const cleanedResult = cleanExpression(expression);
    const parsedExpression = cleanedResult.parsed;
    const displayExpression = cleanedResult.display;
    
    currentExpression = displayExpression;
    const mode = document.querySelector('input[name="calculationMode"]:checked').value;
    currentMode = mode;
    
    // PREPARE DATA UNTUK SERVER - KEY FIX UNTUK OPERATOR BERTURUTAN
    // ==============================================================
    // 1. Parse ekspresi ke tokens
    const tokens = parseExpressionTokens(parsedExpression);
    console.log('Tokens for server preparation:', tokens);
    
    // 2. Buat versi yang aman untuk server dengan menambahkan kurung
    let serverSafeExpression = '';
    let i = 0;
    
    while (i < tokens.length) {
        const token = tokens[i];
        const nextToken = tokens[i + 1];
        
        // Jika pattern: [operator biner] [¬] [variabel/kurung]
        // Contoh: ∧ ¬ q → ∧ (¬q)
        const binaryOps = ['∧', '∨', '→', '↔', '⊕'];
        
        if (binaryOps.includes(token) && nextToken === '¬') {
            // Pattern: operator biner diikuti negasi
            serverSafeExpression += token + ' ';
            
            // Temukan seluruh negasi chain
            let negatedPart = '';
            let j = i + 1; // Mulai dari ¬
            
            while (j < tokens.length && tokens[j] === '¬') {
                negatedPart += '¬';
                j++;
            }
            
            // Tambahkan operand setelah negasi
            if (j < tokens.length) {
                // Jika operand adalah variabel
                if (/^[a-zA-Z]+$/.test(tokens[j])) {
                    negatedPart += tokens[j];
                    serverSafeExpression += '(' + negatedPart + ')';
                } 
                // Jika operand adalah ekspresi dalam kurung
                else if (tokens[j] === '(') {
                    // Cari kurung tutup yang sesuai
                    let balance = 1;
                    let k = j + 1;
                    negatedPart += '(';
                    
                    while (k < tokens.length && balance > 0) {
                        if (tokens[k] === '(') balance++;
                        if (tokens[k] === ')') balance--;
                        negatedPart += tokens[k];
                        k++;
                    }
                    
                    serverSafeExpression += '(' + negatedPart + ')';
                    j = k - 1; // Update j ke posisi terakhir
                }
                
                i = j; // Skip bagian yang sudah diproses
            } else {
                // Fallback: jika tidak ada operand setelah negasi
                serverSafeExpression += token + ' ' + nextToken;
                i++;
            }
        } else {
            // Token biasa, tambahkan saja
            serverSafeExpression += token + ' ';
        }
        
        i++;
    }
    
    // Bersihkan spasi berlebih
    serverSafeExpression = serverSafeExpression.replace(/\s+/g, ' ').trim();
    
    // Jika tidak ada perubahan, gunakan parsedExpression biasa
    if (!serverSafeExpression || serverSafeExpression === parsedExpression) {
        serverSafeExpression = parsedExpression;
    }
    
    console.log('Original parsed:', parsedExpression);
    console.log('Server-safe expression:', serverSafeExpression);
    
    // Prepare data
    const data = {
        expression: serverSafeExpression, // Gunakan versi yang aman untuk server
        mode: mode,
        _token: csrfToken
    };
    
    // Add custom values if mode is custom_values
    if (mode === 'custom_values') {
        const customValues = {};
        document.querySelectorAll('.variable-value').forEach(select => {
            customValues[select.dataset.variable] = select.value === 'true';
        });
        data.custom_values = customValues;
    }
    
    // Add options
    const simplify = document.getElementById('simplifyCheck')?.checked || false;
    const showTree = document.getElementById('showTreeCheck')?.checked || false;
    const showSteps = document.getElementById('showStepsCheck')?.checked || false;
    
    if (simplify) data.simplify = simplify;
    if (showTree) data.show_tree = showTree;
    if (showSteps) data.show_steps = showSteps;
    
    // Show loading
    const loadingIndicator = document.getElementById('loadingIndicator');
    const calculateBtn = document.getElementById('calculateBtn');
    const resultsSection = document.getElementById('resultsSection');
    
    loadingIndicator.style.display = 'block';
    resultsSection.style.display = 'none';
    calculateBtn.disabled = true;
    
    try {
        console.log('📤 Mengirim ke server (SAFE VERSION):', data);
        const response = await axios.post('/calculate', data, {
            timeout: 30000,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        console.log('📥 Response dari server:', response.data);
        
        if (response.data.success) {
            // Simpan ke history
            addToHistory(displayExpression, response.data.data);
            
            // Generate tree structure
            treeData = generateCleanTreeStructure(parsedExpression);
            
            // Tampilkan hasil berdasarkan tipe
            if (response.data.type === 'truth_table') {
                displayTruthTable(response.data.data);
            } else if (response.data.type === 'step_by_step') {
                displayStepByStep(response.data.data);
            } else if (response.data.type === 'custom_evaluation') {
                displayCustomEvaluation(response.data.data);
            } else {
                displayGenericResult(response.data.data);
            }
            
            resultsSection.style.display = 'block';
            resultsSection.scrollIntoView({ behavior: 'smooth' });
            
        } else {
            throw new Error(response.data.message || 'Terjadi kesalahan pada server');
        }
    } catch (error) {
        console.error('Calculation error:', error);
        
        let errorMessage = 'Error: ';
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage += error.response.data.message;
        } else if (error.message) {
            errorMessage += error.message;
        } else {
            errorMessage += 'Tidak dapat terhubung ke server';
        }
        
        showAlert(errorMessage, 'error');
        
        // Fallback: Tampilkan hasil lokal untuk operator berurutan
        if (parsedExpression.includes('¬')) {
            displayLocalFallbackForNegation(parsedExpression, mode, displayExpression);
        } else {
            displayFallbackResult(parsedExpression, mode);
        }
    } finally {
        loadingIndicator.style.display = 'none';
        calculateBtn.disabled = false;
        isCalculating = false;
    }
}

function displayLocalFallbackForNegation(parsedExpr, mode, displayExpr) {
    const resultsContent = document.getElementById('resultsContent');
    
    const variables = [...new Set(parsedExpr.match(/[a-zA-Z]/g) || [])];
    const numVars = variables.length;
    const numRows = Math.pow(2, numVars);
    
    // Buat tabel kebenaran sederhana
    let tableHTML = '';
    
    if (numVars <= 3) {
        // Generate simple truth table untuk hingga 3 variabel
        tableHTML = generateSimpleTruthTable(parsedExpr, variables);
    }
    
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-table me-2"></i>Hasil Perhitungan (Local Mode)
        </h5>
        
        <div class="alert alert-info mb-4">
            <i class="mdi mdi-information-outline me-2"></i>
            Menggunakan perhitungan lokal karena server menolak operator berurutan.
        </div>
        
        <div class="result-alert mb-4">
            <strong>Ekspresi:</strong> ${displayExpr}<br>
            <strong>Variabel:</strong> ${variables.join(', ')}<br>
            <strong>Mode:</strong> ${mode}<br>
            <strong>Kombinasi:</strong> ${numRows} baris<br>
            <strong>Parsed:</strong> ${parsedExpr}
        </div>
        
        ${tableHTML}
    `;
    
    // Tambahkan visualisasi pohon
    html += generateLogicTreeHTML();
    
    resultsContent.innerHTML = html;
    
    // Generate and render tree
    setTimeout(() => {
        treeData = generateSimpleTree(parsedExpr);
        if (treeData) {
            renderCleanLogicTree(treeData);
        }
    }, 100);
}

/**
 * Generate simple truth table untuk fallback
 */
function generateSimpleTruthTable(expression, variables) {
    const precedence = {
        '¬': 6,
        '∧': 5,
        '∨': 4,
        '⊕': 3,
        '→': 2,
        '↔': 1
    };
    
    // Helper: evaluasi ekspresi logika sederhana
    function evaluate(expr, values) {
        // Implementasi evaluasi sederhana
        // Ini adalah implementasi dasar - Anda mungkin ingin memperbaikinya
        let evalExpr = expr;
        
        // Ganti variabel dengan nilai
        variables.forEach(v => {
            const regex = new RegExp(v, 'g');
            evalExpr = evalExpr.replace(regex, values[v] ? '1' : '0');
        });
        
        // Ganti operator dengan simbol JavaScript
        evalExpr = evalExpr.replace(/¬/g, '!')
                          .replace(/∧/g, '&&')
                          .replace(/∨/g, '||')
                          .replace(/⊕/g, '!==')
                          .replace(/→/g, '<=')
                          .replace(/↔/g, '===');
        
        try {
            // Gunakan Function constructor untuk evaluasi aman
            const result = Function('"use strict"; return (' + evalExpr + ')')();
            return result;
        } catch (e) {
            console.error('Evaluation error:', e);
            return false;
        }
    }
    
    // Generate semua kombinasi nilai
    const combinations = [];
    for (let i = 0; i < Math.pow(2, variables.length); i++) {
        const combo = {};
        variables.forEach((v, idx) => {
            combo[v] = !!(i & (1 << (variables.length - 1 - idx)));
        });
        combinations.push(combo);
    }
    
    // Header tabel
    let html = `
    <div class="table-responsive mb-4">
        <table class="table truth-table">
            <thead>
                <tr>
    `;
    
    variables.forEach(v => {
        html += `<th>${v}</th>`;
    });
    html += `<th>${expression}</th></tr></thead><tbody>`;
    
    // Baris tabel
    combinations.forEach(combo => {
        const result = evaluate(expression, combo);
        html += '<tr>';
        
        variables.forEach(v => {
            html += `<td class="${combo[v] ? 'result-true' : 'result-false'}">${combo[v] ? 'B' : 'S'}</td>`;
        });
        
        html += `<td class="${result ? 'result-true' : 'result-false'}">${result ? 'B' : 'S'}</td>`;
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    
    return html;
}

// =============================================================================
// INISIALISASI UTAMA
// =============================================================================

/**
 * Initialize calculator
 */
function initCalculator() {
    console.log('🚀 Menginisialisasi kalkulator...');
    
    // 1. Inisialisasi particles
    initParticles();
    
    // 2. Setup calculate button
    const calculateBtn = document.getElementById('calculateBtn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            calculate();
        });
    }
    
    // 3. Setup operator buttons
    setupOperatorButtons();
    
    // 4. Setup contoh cepat buttons
    createQuickExampleButtons();
    
    // 5. Setup expression input
    const expressionInput = document.getElementById('expressionInput');
    if (expressionInput) {
        // Enter key untuk kalkulasi
        expressionInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                calculate();
            }
        });
        
        // Validasi real-time
        expressionInput.addEventListener('input', function() {
            validateExpression();
            if (currentMode === 'custom_values') {
                generateVariableInputs();
            }
        });
        
        // Set default example jika kosong
        if (!expressionInput.value.trim()) {
            expressionInput.value = 'p ∧ q';
            validateExpression();
        }
    }
    
    // 6. Setup mode selection
    setupModeSelection();
    
    // 7. Load history from localStorage
    loadHistoryFromLocalStorage();
    
    // 8. Update stats
    updateStats();
    
    // 9. Expose functions to global scope
    window.insertOperator = insertOperator;
    window.loadExample = loadExample;
    window.calculate = calculate;
    window.exportCSV = exportCSV;
    window.exportJSON = exportJSON;
    window.printResults = printResults;
    
    console.log('✅ Kalkulator siap digunakan!');
}

/**
 * Add tree styles dynamically
 */
function addTreeStyles() {
    const styleId = 'tree-styles-fixed';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .clean-tree-diagram {
                position: relative;
                overflow: visible !important;
                width: 100%;
            }
            
            .tree-node-box {
                position: absolute;
                transform-origin: center;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                font-family: 'JetBrains Mono', 'Consolas', monospace;
                will-change: transform, box-shadow, z-index;
                backface-visibility: hidden;
            }
            
            .tree-node-box:hover {
                z-index: 1000 !important;
                transform: scale(1.15) translateZ(0) !important;
            }
            
            .operator-node:hover {
                box-shadow: 0 15px 35px rgba(0, 245, 255, 0.5) !important;
            }
            
            .variable-node:hover {
                box-shadow: 0 15px 35px rgba(0, 255, 204, 0.4) !important;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            .cursor-pointer {
                cursor: pointer !important;
            }
        `;
        document.head.appendChild(style);
    }
}

// =============================================================================
// MAIN INITIALIZATION
// =============================================================================

/**
 * Initialize everything when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Starting initialization');
    
    // 1. Add tree styles
    addTreeStyles();
    
    // 2. Initialize calculator
    setTimeout(() => {
        initCalculator();
        console.log('🎉 Kalkulator berhasil diinisialisasi!');
    }, 300);
});

/**
 * Fallback initialization
 */
window.addEventListener('load', function() {
    console.log('🔄 Window loaded - Final checks');
    
    // Re-initialize if needed
    if (!document.getElementById('calculateBtn').onclick) {
        console.log('⚠️ Re-initializing calculator...');
        setTimeout(initCalculator, 500);
    }
});