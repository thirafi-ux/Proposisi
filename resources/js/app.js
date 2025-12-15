// public/js/calculator.js - VERSI DIPERBAIKI DENGAN POHON LOGIKA
console.log('Calculator JS loaded - Enhanced Version with Tree Visualization');

// Global variables
let currentExpression = '';
let calculationHistory = [];
let currentVariables = [];
let currentMode = 'truth_table';
let treeData = null;

// Set CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Initialize Particles.js
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

// Insert operator into input field
function insertOperator(operator) {
    const input = document.getElementById('expressionInput');
    const start = input.selectionStart;
    const end = input.selectionEnd;
    const text = input.value;
    
    input.value = text.substring(0, start) + operator + text.substring(end);
    input.selectionStart = input.selectionEnd = start + operator.length;
    input.focus();
    
    // Validate expression
    validateExpression();
}

// Load example expression
function loadExample(expression) {
    document.getElementById('expressionInput').value = expression;
    validateExpression();
    if (currentMode === 'custom_values') {
        generateVariableInputs();
    }
}

// Validate expression
function validateExpression() {
    const expression = document.getElementById('expressionInput').value.trim();
    const validationMessage = document.getElementById('validationMessage');
    
    if (expression === '') {
        validationMessage.innerHTML = '';
        return true;
    }
    
    let isValid = true;
    let message = '';
    
    // Check for balanced parentheses
    let balance = 0;
    for (let char of expression) {
        if (char === '(') balance++;
        if (char === ')') balance--;
        if (balance < 0) break;
    }
    
    if (balance !== 0) {
        isValid = false;
        message = '❌ Tanda kurung tidak seimbang';
    } else if (expression.length > 100) {
        isValid = false;
        message = '❌ Ekspresi terlalu panjang';
    } else {
        // Check for consecutive operators (excluding double negation)
        const operatorPattern = /[¬∧∨→↔⊕]{2,}/;
        if (operatorPattern.test(expression) && !/¬¬/.test(expression)) {
            isValid = false;
            message = '❌ Operator tidak boleh berurutan';
        } else {
            message = '✅ Ekspresi valid';
        }
    }
    
    validationMessage.innerHTML = `<span style="color: ${isValid ? '#00ffcc' : '#ff00ff'}">${message}</span>`;
    return isValid;
}

// Handle mode selection
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

// Generate variable inputs for custom values mode
function generateVariableInputs() {
    const expression = document.getElementById('expressionInput').value.trim();
    if (!expression) return;
    
    const variableInputs = document.getElementById('variableInputs');
    variableInputs.innerHTML = '';
    
    // Extract variables from expression
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

// Main calculation function
async function calculate() {
    const expression = document.getElementById('expressionInput').value.trim();
    
    if (!expression) {
        showAlert('Masukkan ekspresi terlebih dahulu!', 'warning');
        return;
    }
    
    if (!validateExpression()) {
        showAlert('Ekspresi tidak valid! Periksa kembali.', 'warning');
        return;
    }
    
    currentExpression = expression;
    const mode = document.querySelector('input[name="calculationMode"]:checked').value;
    currentMode = mode;
    
    // Prepare data
    const data = {
        expression: expression,
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
        const response = await axios.post('/calculate', data);
        console.log('Server response:', response.data);
        
        if (response.data.success) {
            // Simpan ke history
            addToHistory(expression, response.data.data);
            
            // Simpan treeData dari response jika ada
            if (response.data.data && response.data.data.tree_data) {
                treeData = response.data.data.tree_data;
                console.log('Tree data received:', treeData);
            } else {
                treeData = null;
                console.log('No tree data in response');
            }
            
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
            throw new Error(response.data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        console.error('Calculation error:', error);
        showAlert('Error: ' + (error.response?.data?.message || error.message), 'error');
    } finally {
        loadingIndicator.style.display = 'none';
        calculateBtn.disabled = false;
    }
}

// Display truth table with tree
function displayTruthTable(data) {
    console.log('Displaying truth table with data:', data);
    
    // Simpan treeData dari data jika ada
    if (data.tree_data) {
        treeData = data.tree_data;
    }
    
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-table me-2"></i>Tabel Kebenaran
        </h5>
        
        <div class="alert alert-dark mb-4" style="background: rgba(0, 245, 255, 0.1); border-color: var(--primary-neon);">
            <strong>Ekspresi:</strong> ${data.main_expression}<br>
            <strong>Variabel:</strong> ${data.variables.join(', ')}<br>
            <strong>Jumlah Baris:</strong> ${data.row_count}<br>
            <strong>Sifat:</strong> ${data.is_tautology ? 'Tautologi' : data.is_contradiction ? 'Kontradiksi' : 'Kontingen'}
        </div>
        
        <div class="table-responsive mb-4">
            <table class="table truth-table">
                <thead>
                    <tr>
    `;
    
    // Table headers
    data.headers.forEach(header => {
        const isMain = header === data.main_expression;
        const headerStyle = isMain ? 'background: rgba(0, 245, 255, 0.2) !important; color: var(--accent-cyan) !important;' : '';
        html += `<th style="${headerStyle}">${header}</th>`;
    });
    
    html += '</tr></thead><tbody>';
    
    // Table rows
    data.table.forEach(row => {
        html += '<tr>';
        data.headers.forEach(header => {
            const value = row[header] || '';
            const isMain = header === data.main_expression;
            const cellClass = value === 'B' ? 'result-true' : 'result-false';
            const boldClass = isMain ? 'fw-bold' : '';
            
            html += `<td class="${cellClass} ${boldClass}">${value}</td>`;
        });
        html += '</tr>';
    });
    
    html += `
            </tbody>
            </table>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="glass-card-sm text-center">
                    <div class="stat-number">${data.true_count}</div>
                    <small>Benar</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="glass-card-sm text-center">
                    <div class="stat-number">${data.false_count}</div>
                    <small>Salah</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="glass-card-sm text-center">
                    <div class="stat-number">${Math.round((data.true_count / data.row_count) * 100)}%</div>
                    <small>Persentase Benar</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="glass-card-sm text-center">
                    <div class="stat-number">${data.row_count}</div>
                    <small>Total Baris</small>
                </div>
            </div>
        </div>
    `;
    
    // Add logic tree if showTreeCheck is checked
    const showTreeCheck = document.getElementById('showTreeCheck');
    if (showTreeCheck && showTreeCheck.checked) {
        html += generateLogicTreeHTML();
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    
    // Render tree after HTML is loaded
    if (showTreeCheck && showTreeCheck.checked) {
        setTimeout(() => {
            if (treeData) {
                renderLogicTree(treeData);
            } else {
                // Generate basic tree from expression
                generateTreeFromExpression(currentExpression);
            }
        }, 100);
    }
}

// Display step by step with tree
function displayStepByStep(data) {
    // Simpan treeData dari data jika ada
    if (data.tree_data) {
        treeData = data.tree_data;
    }
    
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-format-list-numbers me-2"></i>Langkah Demi Langkah
        </h5>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Ringkasan</h6>
                    <p><strong>Tautologi:</strong> ${data.properties.is_tautology ? 'Ya' : 'Tidak'}</p>
                    <p><strong>Kontradiksi:</strong> ${data.properties.is_contradiction ? 'Ya' : 'Tidak'}</p>
                    <p><strong>Benar:</strong> ${data.properties.true_count}/${data.table.length}</p>
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
    
    data.headers.forEach(header => {
        html += `<th>${header}</th>`;
    });
    
    html += '</tr></thead><tbody>';
    
    data.table.forEach(row => {
        html += '<tr>';
        data.headers.forEach(header => {
            const value = row[header];
            html += `<td class="${value === true || value === 'B' ? 'result-true' : 'result-false'}">${value === true ? 'B' : value === false ? 'S' : value}</td>`;
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
    
    data.all_steps.forEach((stepSet, index) => {
        html += `
            <div class="glass-card-sm mb-3">
                <h6>Baris ${stepSet.row}: ${stepSet.result}</h6>
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
        
        stepSet.steps.forEach(step => {
            html += `
                <tr>
                    <td>${step.step}</td>
                    <td><code>${step.expression}</code></td>
                    <td class="${step.result === 'True' || step.result === true ? 'result-true' : 'result-false'}">${step.result}</td>
                    <td>${step.details}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
    });
    
    // Add logic tree if showTreeCheck is checked
    const showTreeCheck = document.getElementById('showTreeCheck');
    if (showTreeCheck && showTreeCheck.checked) {
        html += generateLogicTreeHTML();
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    
    // Render tree after HTML is loaded
    if (showTreeCheck && showTreeCheck.checked) {
        setTimeout(() => {
            if (treeData) {
                renderLogicTree(treeData);
            } else {
                generateTreeFromExpression(currentExpression);
            }
        }, 100);
    }
}

// Display custom evaluation with tree
function displayCustomEvaluation(data) {
    // Simpan treeData dari data jika ada
    if (data.tree_data) {
        treeData = data.tree_data;
    }
    
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-calculator-variant me-2"></i>Evaluasi dengan Nilai Kustom
        </h5>
        
        <div class="alert alert-dark mb-4" style="background: rgba(0, 245, 255, 0.1); border-color: var(--primary-neon);">
            <strong>Hasil Akhir:</strong> 
            <span class="${data.final_result_text === 'True' ? 'result-true' : 'result-false'}">
                ${data.final_result_text}
            </span>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Nilai Variabel:</h6>
    `;
    
    for (const [variable, value] of Object.entries(data.values)) {
        html += `<p><strong>${variable}:</strong> ${value ? 'B (Benar)' : 'S (Salah)'}</p>`;
    }
    
    html += `
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card-sm">
                    <h6>Ringkasan:</h6>
                    <p><strong>Jumlah Langkah:</strong> ${data.steps.length}</p>
                    <p><strong>Status:</strong> ${data.final_result_text === 'True' ? 'Ekspresi Benar' : 'Ekspresi Salah'}</p>
                </div>
            </div>
        </div>
        
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
        html += `
            <tr>
                <td>${step.step}</td>
                <td><code>${step.expression}</code></td>
                <td class="${step.result === 'True' ? 'result-true' : 'result-false'}">${step.result}</td>
                <td>${step.details}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    
    // Add logic tree if showTreeCheck is checked
    const showTreeCheck = document.getElementById('showTreeCheck');
    if (showTreeCheck && showTreeCheck.checked) {
        html += generateLogicTreeHTML();
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    
    // Render tree after HTML is loaded
    if (showTreeCheck && showTreeCheck.checked) {
        setTimeout(() => {
            if (treeData) {
                renderLogicTree(treeData);
            } else {
                generateTreeFromExpression(currentExpression);
            }
        }, 100);
    }
}

// Generate HTML for logic tree container
function generateLogicTreeHTML() {
    return `
        <div class="mt-5">
            <h5 style="color: var(--primary-neon);">
                <i class="mdi mdi-chart-tree me-2"></i>Pohon Logika
            </h5>
            <div id="logicTreeVisualization" class="tree-container" 
                 style="min-height: 400px; border: 1px solid rgba(255, 255, 255, 0.1); 
                        border-radius: 10px; padding: 20px; background: rgba(255, 255, 255, 0.03);">
                <div id="treeDrawing" style="min-height: 350px; display: flex; justify-content: center; align-items: center;">
                    <div class="text-center" style="color: rgba(255, 255, 255, 0.5);">
                        <i class="mdi mdi-chart-tree" style="font-size: 3rem;"></i>
                        <p class="mt-2">Memuat visualisasi pohon logika...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Generate basic tree from expression (fallback)
function generateTreeFromExpression(expression) {
    if (!expression) return;
    
    // Simple tree generation from expression
    const operators = {
        '¬': 'Negasi',
        '∧': 'Konjungsi',
        '∨': 'Disjungsi',
        '→': 'Implikasi',
        '↔': 'Bikondisional',
        '⊕': 'XOR'
    };
    
    // Extract variables
    const variables = [...new Set(expression.match(/[a-zA-Z]/g) || [])];
    
    // Simple AST generation
    const tree = {
        id: 1,
        label: expression,
        type: 'expression',
        depth: 0,
        children: []
    };
    
    // Parse expression and build tree
    let nodeId = 2;
    
    function parseSubExpression(expr, parent, depth) {
        // Remove outer parentheses
        expr = expr.trim();
        if (expr.startsWith('(') && expr.endsWith(')')) {
            expr = expr.slice(1, -1);
        }
        
        // Find main operator
        let operator = null;
        let operatorIndex = -1;
        
        // Check for unary operators first
        if (expr.startsWith('¬')) {
            operator = '¬';
            operatorIndex = 0;
        } else {
            // Check for binary operators
            const opPattern = /[∧∨→↔⊕]/;
            let parenCount = 0;
            
            for (let i = 0; i < expr.length; i++) {
                const char = expr[i];
                if (char === '(') parenCount++;
                else if (char === ')') parenCount--;
                else if (parenCount === 0 && opPattern.test(char)) {
                    operator = char;
                    operatorIndex = i;
                    break;
                }
            }
        }
        
        if (operator) {
            const node = {
                id: nodeId++,
                label: operators[operator] || operator,
                type: 'operator',
                operator: operator,
                depth: depth,
                children: []
            };
            
            if (operator === '¬') {
                // Unary operator
                const operand = expr.slice(1);
                node.children.push(parseSubExpression(operand, node, depth + 1));
            } else {
                // Binary operator
                const left = expr.slice(0, operatorIndex);
                const right = expr.slice(operatorIndex + 1);
                node.children.push(parseSubExpression(left, node, depth + 1));
                node.children.push(parseSubExpression(right, node, depth + 1));
            }
            
            return node;
        } else {
            // Variable or constant
            return {
                id: nodeId++,
                label: expr,
                type: 'variable',
                depth: depth,
                children: []
            };
        }
    }
    
    tree.children.push(parseSubExpression(expression, tree, 1));
    treeData = {
        type: 'logic_tree',
        root: tree,
        total_nodes: nodeId - 1,
        max_depth: 5
    };
    
    renderLogicTree(treeData);
}

// Render logic tree visualization
function renderLogicTree(treeData) {
    const container = document.getElementById('treeDrawing');
    if (!container) {
        console.error('Tree container not found');
        return;
    }
    
    // Clear previous content
    container.innerHTML = '';
    
    // Create a simple HTML tree visualization as fallback
    const treeHTML = generateSimpleTreeHTML(treeData);
    container.innerHTML = treeHTML;
}

function generateSimpleTreeHTML(treeData) {
    if (!treeData || !treeData.root) {
        return '<div class="text-center p-5" style="color: rgba(255,255,255,0.5)">Data pohon tidak tersedia</div>';
    }
    
    function buildNodeHTML(node, depth = 0) {
        const indent = depth * 30;
        const nodeColor = node.type === 'operator' ? 'var(--primary-neon)' : 
                         node.type === 'variable' ? 'var(--accent-cyan)' : 'var(--accent-purple)';
        
        let html = `
            <div style="margin-left: ${indent}px; margin-bottom: 10px;">
                <div class="d-flex align-items-center" 
                     style="background: ${node.type === 'operator' ? 'rgba(0,245,255,0.1)' : 'rgba(0,255,204,0.1)'}; 
                            padding: 8px 12px; 
                            border-radius: 8px;
                            border: 1px solid ${nodeColor};
                            color: #fff;">
                    <div style="width: 30px; height: 30px; 
                                background: ${nodeColor}; 
                                border-radius: 50%; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center;
                                margin-right: 10px;
                                font-weight: bold;">
                        ${node.type === 'operator' ? 'O' : node.type === 'variable' ? 'V' : '?'}
                    </div>
                    <div>
                        <strong>${node.label || node.value || 'Node'}</strong>
                        ${node.operator ? `<br><small style="color: rgba(255,255,255,0.7)">Operator: ${node.operator}</small>` : ''}
                    </div>
                </div>
        `;
        
        if (node.children && node.children.length > 0) {
            html += '<div style="border-left: 2px solid rgba(0,245,255,0.3); margin-left: 15px; padding-left: 15px;">';
            node.children.forEach(child => {
                html += buildNodeHTML(child, depth + 1);
            });
            html += '</div>';
        }
        
        html += '</div>';
        return html;
    }
    
    return `
        <div style="padding: 20px; max-height: 400px; overflow-y: auto;">
            <div class="mb-3" style="color: var(--primary-neon);">
                <i class="mdi mdi-chart-tree me-2"></i>Struktur Pohon Logika
            </div>
            ${buildNodeHTML(treeData.root)}
        </div>
    `;
}

// Display generic result (fallback)
function displayGenericResult(data) {
    let html = `
        <h5 class="mb-3" style="color: var(--primary-neon);">
            <i class="mdi mdi-information-outline me-2"></i>Hasil Perhitungan
        </h5>
        
        <div class="alert alert-dark mb-4" style="background: rgba(0, 245, 255, 0.1); border-color: var(--primary-neon);">
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

// Add to history
function addToHistory(expression, data) {
    let resultText = 'Kontingen';
    if (data.is_tautology) resultText = 'Tautologi';
    else if (data.is_contradiction) resultText = 'Kontradiksi';
    
    calculationHistory.unshift({
        expression,
        timestamp: new Date().toLocaleString('id-ID'),
        mode: currentMode,
        variables: data.variables || [],
        result: resultText
    });
    
    if (calculationHistory.length > 10) {
        calculationHistory.pop();
    }
    
    updateHistoryDisplay();
    updateStats();
    saveHistoryToLocalStorage();
}

// Update history display
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
        html += `
            <div class="list-group-item list-group-item-action" 
                onclick="loadExample('${item.expression.replace(/'/g, "\\'")}')" 
                style="background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.8); cursor: pointer;">
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

// Save history to localStorage
function saveHistoryToLocalStorage() {
    try {
        localStorage.setItem('propositionHistory', JSON.stringify(calculationHistory));
    } catch (e) {
        console.error('Error saving history to localStorage:', e);
    }
}

// Load history from localStorage
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

// Update stats
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
            complexity += currentVariables.length * 3;
            complexity = Math.min(Math.round(complexity), 99);
            complexityScore.textContent = complexity;
        } else {
            complexityScore.textContent = 0;
        }
    }
}

// Show alert message
function showAlert(message, type = 'info') {
    const colors = {
        error: '#ff00ff',
        warning: '#ff9900',
        info: '#00f5ff',
        success: '#00ffcc'
    };
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-dismissible fade show';
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
    `;
    alertDiv.innerHTML = `
        <div style="color: ${colors[type]}; display: flex; align-items: center;">
            <i class="mdi mdi-${type === 'error' ? 'alert-circle' : type === 'warning' ? 'alert' : 'information'} me-2"></i>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Export functions
function exportCSV() {
    showAlert('Fitur ekspor CSV akan segera tersedia!', 'info');
}

function exportJSON() {
    showAlert('Fitur ekspor JSON akan segera tersedia!', 'info');
}

function printResults() {
    window.print();
}

// Initialize calculator
function initCalculator() {
    console.log('Initializing calculator...');
    
    // Initialize particles
    initParticles();
    
    // Add tree styles
    addTreeStyles(); // PASTIKAN INI DIPANGGIL
    
    // Setup calculate button
    const calculateBtn = document.getElementById('calculateBtn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculate);
    }
    
    // Setup expression input
    const expressionInput = document.getElementById('expressionInput');
    if (expressionInput) {
        expressionInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') calculate();
        });
        
        expressionInput.addEventListener('input', function() {
            validateExpression();
            if (currentMode === 'custom_values') {
                generateVariableInputs();
            }
        });
        
        // Set default example
        if (!expressionInput.value) {
            expressionInput.value = 'p ∧ q';
            validateExpression();
        }
    }
    
    // Setup mode selection
    setupModeSelection();
    
    // Load history from localStorage
    loadHistoryFromLocalStorage();
    
    // Update stats
    updateStats();
    
    // Make functions globally available
    window.insertOperator = insertOperator;
    window.loadExample = loadExample;
    window.calculate = calculate;
    window.exportCSV = exportCSV;
    window.exportJSON = exportJSON;
    window.printResults = printResults;
    
    console.log('Calculator initialized successfully');
}

function generateCleanTreeHTML(treeData) {
    const { levels, connections, root } = treeData;
    
    if (!levels || levels.length === 0) {
        return '<div class="text-center p-5" style="color: rgba(255,255,255,0.5)">Data pohon logika tidak tersedia</div>';
    }
    
    // Calculate tree dimensions - lebih rapi dan terstruktur
    const totalLevels = levels.length;
    const maxNodesInLevel = Math.max(...levels.map(level => level.length));
    
    // Tentukan ukuran container berdasarkan kompleksitas
    const baseNodeWidth = 110; // Lebar node sedikit lebih besar
    const baseNodeHeight = 55; // Tinggi node
    const verticalSpacing = 85; // Jarak vertikal antar level
    const horizontalSpacing = 70; // Jarak horizontal antar node dalam level yang sama
    const containerPadding = 40; // Padding container
    
    // Hitung ukuran container yang tepat
    const containerWidth = Math.max(800, maxNodesInLevel * (baseNodeWidth + horizontalSpacing) + containerPadding * 2);
    const containerHeight = totalLevels * verticalSpacing + containerPadding * 2;
    
    // Setup untuk menghitung posisi node dengan struktur pohon yang benar
    const positionedNodes = [];
    const nodePositions = new Map();
    
    // Hitung posisi untuk setiap node dengan struktur pohon yang simetris
    levels.forEach((levelNodes, levelIndex) => {
        const nodesInLevel = levelNodes.length;
        
        // Untuk level dengan banyak node, sebarkan secara merata
        const levelCenter = containerWidth / 2;
        let startX;
        
        if (nodesInLevel === 1) {
            startX = levelCenter - (baseNodeWidth / 2);
        } else {
            const totalLevelWidth = (nodesInLevel * baseNodeWidth) + ((nodesInLevel - 1) * horizontalSpacing);
            startX = levelCenter - (totalLevelWidth / 2);
        }
        
        // Atur posisi untuk setiap node dalam level
        levelNodes.forEach((node, nodeIndex) => {
            let x;
            
            if (nodesInLevel === 1) {
                x = startX;
            } else {
                x = startX + (nodeIndex * (baseNodeWidth + horizontalSpacing));
            }
            
            // Posisi Y berdasarkan level
            const y = containerPadding + (levelIndex * verticalSpacing);
            
            const positionedNode = {
                ...node,
                x: x + (baseNodeWidth / 2), // Pusatkan node
                y,
                width: baseNodeWidth,
                height: baseNodeHeight,
                isRoot: levelIndex === 0
            };
            
            positionedNodes.push(positionedNode);
            nodePositions.set(node.id, positionedNode);
        });
    });
    
    // Generate SVG connections dengan garis lurus dan rapi
    let svgHTML = `<svg width="${containerWidth}" height="${containerHeight}" style="position: absolute; top: 0; left: 0; z-index: 1; overflow: visible;">`;
    
    // Tambahkan background grid untuk referensi visual (opsional)
    svgHTML += `
        <defs>
            <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                <path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" opacity="0.3"/>
    `;
    
    // Gambar garis penghubung dengan pola seperti pohon
    connections.forEach(conn => {
        const fromNode = nodePositions.get(conn.from);
        const toNode = nodePositions.get(conn.to);
        
        if (fromNode && toNode) {
            const fromX = fromNode.x;
            const fromY = fromNode.y + baseNodeHeight; // Dari bawah node parent
            const toX = toNode.x;
            const toY = toNode.y; // Ke atas node child
            
            // Hitung titik kontrol untuk membuat garis lurus dengan sedikit offset horizontal
            const midY = fromY + ((toY - fromY) / 2);
            
            // Buat garis dengan sudut yang lebih tajam seperti pohon
            svgHTML += `
                <path d="M ${fromX} ${fromY} 
                        L ${fromX} ${midY - 10} 
                        L ${toX} ${midY - 10} 
                        L ${toX} ${toY}"
                      stroke="rgba(0, 245, 255, 0.7)"
                      stroke-width="2"
                      fill="none"
                      stroke-linecap="round"
                      class="tree-connector connector-${conn.from} connector-${conn.to}" />
                
                <circle cx="${toX}" cy="${toY}" r="4" 
                        fill="var(--accent-cyan)" 
                        class="connector-end connector-${conn.to}" />
            `;
        }
    });
    
    svgHTML += '</svg>';
    
    // Generate nodes HTML dengan styling yang lebih rapi dan terstruktur
    let nodesHTML = '';
    
    positionedNodes.forEach(node => {
        const isOperator = node.type === 'operator';
        const isVariable = node.type === 'variable';
        const isRootNode = node.isRoot;
        
        let nodeClass = 'tree-node-box';
        let nodeStyle = '';
        let contentHTML = '';
        
        if (isOperator) {
            nodeClass += ' operator-node';
            nodeStyle = `
                background: linear-gradient(145deg, 
                    rgba(0, 245, 255, 0.18), 
                    rgba(0, 245, 255, 0.08));
                border: 2px solid var(--primary-neon);
                color: var(--primary-neon);
            `;
            
            // Tampilkan operator dengan lebih jelas
            const operatorDisplay = getOperatorSymbol(node.operator);
            const operatorName = getOperatorName(node.operator);
            
            contentHTML = `
                <div class="node-main" style="font-size: 0.95rem; font-weight: bold; color: var(--primary-neon);">
                    ${operatorDisplay}
                </div>
                <div class="node-subtitle" style="font-size: 0.65rem; opacity: 0.9; margin-top: 2px;">
                    ${operatorName}
                </div>
                <div class="node-expression" style="font-size: 0.7rem; opacity: 0.8; margin-top: 3px; color: rgba(255,255,255,0.9);">
                    ${node.expression.length > 15 ? node.expression.substring(0, 12) + '...' : node.expression}
                </div>
            `;
        } else {
            nodeClass += ' variable-node';
            nodeStyle = `
                background: linear-gradient(145deg, 
                    rgba(0, 255, 204, 0.18), 
                    rgba(0, 255, 204, 0.08));
                border: 2px solid var(--accent-cyan);
                color: var(--accent-cyan);
            `;
            
            contentHTML = `
                <div class="node-main" style="font-size: 1.1rem; font-weight: bold; color: var(--accent-cyan);">
                    ${node.value}
                </div>
                <div class="node-subtitle" style="font-size: 0.7rem; opacity: 0.9; margin-top: 2px;">
                    Variabel
                </div>
                <div class="node-type" style="font-size: 0.6rem; opacity: 0.7; margin-top: 1px;">
                    Proposisi
                </div>
            `;
        }
        
        // Styling tambahan untuk root node
        if (isRootNode) {
            nodeStyle += `
                box-shadow: 0 0 20px rgba(0, 245, 255, 0.5) !important;
                border-width: 3px !important;
            `;
        }
        
        nodesHTML += `
            <div class="${nodeClass}" 
                 id="${node.id}"
                 data-level="${node.level}"
                 data-type="${node.type}"
                 style="position: absolute;
                        left: ${node.x - (baseNodeWidth / 2)}px;
                        top: ${node.y}px;
                        ${nodeStyle}
                        width: ${baseNodeWidth}px;
                        height: ${baseNodeHeight}px;
                        padding: 8px 5px;
                        border-radius: 8px;
                        cursor: pointer;
                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                        text-align: center;
                        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
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
                      background: ${isRootNode ? 'var(--primary-neon)' : 'rgba(255,255,255,0.2)'};
                      color: ${isRootNode ? '#0a0a1f' : 'rgba(255,255,255,0.8)'};
                      font-size: 0.55rem;
                      padding: 1px 4px;
                      border-radius: 3px;
                      font-weight: bold;">
                    L${node.level}
                </div>
            </div>
        `;
    });
    
    // Generate legend dan stats yang lebih informatif
    const legendHTML = `
        <div class="row justify-content-center mt-4">
            <div class="col-auto">
                <div class="tree-legend" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; padding: 12px 20px; background: rgba(10, 10, 31, 0.9); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.15); max-width: 600px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                    <div class="legend-item" style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.95); font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; background: rgba(0,245,255,0.1);">
                        <div class="legend-color" style="width: 14px; height: 14px; border-radius: 4px; background: linear-gradient(135deg, var(--primary-neon), rgba(0, 245, 255, 0.7)); border: 1px solid var(--primary-neon);"></div>
                        <span>Operator Logika</span>
                    </div>
                    <div class="legend-item" style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.95); font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; background: rgba(0,255,204,0.1);">
                        <div class="legend-color" style="width: 14px; height: 14px; border-radius: 4px; background: linear-gradient(135deg, var(--accent-cyan), rgba(0, 255, 204, 0.7)); border: 1px solid var(--accent-cyan);"></div>
                        <span>Variabel Proposisi</span>
                    </div>
                    <div class="legend-item" style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.95); font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; background: rgba(255,255,255,0.05);">
                        <svg width="20" height="20">
                            <line x1="2" y1="2" x2="18" y2="18" stroke="rgba(0, 245, 255, 0.7)" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Hubungan Parent → Child</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mt-3">
            <div class="col-auto">
                <div class="tree-stats" style="display: flex; gap: 20px; padding: 10px 20px; background: rgba(255, 255, 255, 0.06); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);">
                    <div style="text-align: center; min-width: 70px;">
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.75); margin-bottom: 3px;">Kedalaman</div>
                        <div style="color: var(--primary-neon); font-weight: bold; font-size: 1.1rem; text-shadow: 0 0 8px rgba(0,245,255,0.5);">${levels.length}</div>
                    </div>
                    <div style="text-align: center; min-width: 70px;">
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.75); margin-bottom: 3px;">Total Node</div>
                        <div style="color: var(--accent-cyan); font-weight: bold; font-size: 1.1rem; text-shadow: 0 0 8px rgba(0,255,204,0.5);">${positionedNodes.length}</div>
                    </div>
                    <div style="text-align: center; min-width: 70px;">
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.75); margin-bottom: 3px;">Operator</div>
                        <div style="color: #ff00ff; font-weight: bold; font-size: 1.1rem; text-shadow: 0 0 8px rgba(255,0,255,0.5);">
                            ${positionedNodes.filter(n => n.type === 'operator').length}
                        </div>
                    </div>
                    <div style="text-align: center; min-width: 70px;">
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.75); margin-bottom: 3px;">Variabel</div>
                        <div style="color: #ff9900; font-weight: bold; font-size: 1.1rem; text-shadow: 0 0 8px rgba(255,153,0,0.5);">
                            ${positionedNodes.filter(n => n.type === 'variable').length}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3" style="color: rgba(255,255,255,0.65); font-size: 0.75rem; padding: 8px 15px; background: rgba(255,255,255,0.04); border-radius: 6px; border: 1px solid rgba(255,255,255,0.08); max-width: 700px; margin: 0 auto;">
            <i class="mdi mdi-information-outline me-1" style="color: var(--primary-neon);"></i>
            <strong>Tips:</strong> Klik pada node untuk melihat detail • Arah garis menunjukkan aliran evaluasi dari atas ke bawah
        </div>
    `;
    
    return `
        <div class="clean-tree-diagram" style="position: relative; width: 100%;">
            <div style="position: relative; 
                        width: 100%;
                        min-height: ${containerHeight}px; 
                        background: rgba(10, 10, 31, 0.5); 
                        border-radius: 12px; 
                        padding: ${containerPadding}px 0; 
                        border: 2px solid rgba(0, 245, 255, 0.2);
                        margin-bottom: 25px;
                        overflow: visible;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
                <div style="width: ${containerWidth}px; height: ${containerHeight}px; margin: 0 auto; position: relative;">
                    ${svgHTML}
                    <div class="clean-tree-nodes" style="position: relative; z-index: 2; width: 100%; height: 100%;">
                        ${nodesHTML}
                    </div>
                </div>
            </div>
            ${legendHTML}
        </div>
    `;
}

function getOperatorSymbol(operator) {
    const operatorSymbols = {
        '¬': '¬ (NOT)',
        '∧': '∧ (AND)',
        '∨': '∨ (OR)',
        '→': '→ (IMP)',
        '↔': '↔ (IFF)',
        '⊕': '⊕ (XOR)'
    };
    return operatorSymbols[operator] || operator;
}

// Add tree styles dynamically
function addTreeStyles() {
    const styleId = 'tree-styles';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .clean-tree-diagram {
                position: relative;
                overflow: visible !important;
                width: 100%;
            }
            
            .clean-tree-nodes {
                position: relative;
                width: 100%;
                height: 100%;
            }
            
            .tree-node-box {
                position: absolute;
                transform-origin: center;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-family: 'JetBrains Mono', monospace, sans-serif;
            }
            
            .tree-node-box:hover {
                z-index: 1000 !important;
                transform: scale(1.08) !important;
            }
            
            .operator-node {
                box-shadow: 0 0 12px rgba(0, 245, 255, 0.25) !important;
            }
            
            .variable-node {
                box-shadow: 0 0 8px rgba(0, 255, 204, 0.25) !important;
            }
            
            .tree-connector {
                stroke-linecap: round;
                transition: all 0.3s ease;
            }
            
            @keyframes dash {
                to {
                    stroke-dashoffset: 20;
                }
            }
            
            .tree-diagram-container {
                position: relative;
                width: 100%;
                overflow: visible !important; /* HAPUS SCROLL */
                padding: 10px;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .tree-node-box {
                    width: 70px !important;
                    height: 40px !important;
                    font-size: 0.7rem !important;
                    padding: 4px 6px !important;
                }
                
                .node-expression {
                    font-size: 0.65rem !important;
                }
                
                .node-operator {
                    font-size: 0.5rem !important;
                }
                
                .tree-legend {
                    flex-direction: column;
                    align-items: center;
                    gap: 8px !important;
                }
                
                .tree-stats {
                    flex-wrap: wrap;
                    justify-content: center;
                    gap: 10px !important;
                }
            }
            
            .cursor-pointer {
                cursor: pointer !important;
            }
            
            .node-type-badge {
                font-weight: bold;
                letter-spacing: 0.5px;
            }
            
            /* Untuk ekspresi yang sangat panjang */
            .long-expression {
                font-size: 0.7rem !important;
                line-height: 1 !important;
            }
            
            /* Untuk root node yang penting */
            .root-node {
                border-width: 3px !important;
            }
        `;
        document.head.appendChild(style);
    }
}


// Start when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add tree styles
    addTreeStyles();
    
    // Initialize calculator
    initCalculator();
    
    // Load initial history from server
    loadServerHistory();
});

// Load history from server
function loadServerHistory() {
    axios.get('/history')
        .then(response => {
            if (response.data.success && response.data.data.length > 0) {
                const historyList = document.getElementById('historyList');
                if (historyList && response.data.data.length > 0) {
                    let html = '';
                    response.data.data.forEach(item => {
                        html += `
                            <div class="list-group-item" style="background: transparent; border-color: rgba(255, 255, 255, 0.1);">
                                <div class="d-flex w-100 justify-content-between">
                                    <small style="color: var(--accent-cyan);">${item.expression}</small>
                                </div>
                                <small style="color: rgba(255, 255, 255, 0.5);">
                                    ${new Date(item.created_at).toLocaleString()}
                                </small>
                            </div>
                        `;
                    });
                    historyList.innerHTML = html;
                }
            }
        })
        .catch(error => {
            console.error('Error loading history:', error);
        });
}