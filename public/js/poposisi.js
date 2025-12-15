// =============================================================================
// LogiCalc Pro - Analisis Tautologi & Kontradiksi (Versi Baru)
// File: poposisi.js
// =============================================================================

console.log('🚀 Proposition JS loaded - Tautology & Contradiction Focus');

// =============================================================================
// VARIABEL GLOBAL & INISIALISASI
// =============================================================================
let analysisHistory = [];
let currentMode = 'single';
let truthP = true;
let truthQ = true;
let currentOperator = 'and';

// Basis pengetahuan yang diperluas untuk mencakup tautologi dan kontradiksi
const knowledgeBase = {
    // TAUTOLOGI: Pernyataan yang selalu benar
    tautologies: {
        // Pola logika formal
        'p atau tidak p': { value: true, confidence: 100, explanation: 'Bentuk "A ∨ ¬A" selalu benar (hukum excluded middle)' },
        'tidak (p dan tidak p)': { value: true, confidence: 100, explanation: 'Bentuk "¬(A ∧ ¬A)" selalu benar (hukum non-kontradiksi)' },
        'jika p maka p': { value: true, confidence: 100, explanation: 'Bentuk "A → A" selalu benar (implikasi identitas)' },
        'p jika dan hanya jika p': { value: true, confidence: 100, explanation: 'Bentuk "A ↔ A" selalu benar (ekuivalensi identitas)' },
        
        // Tautologi kehidupan sehari-hari
        'semua manusia akan mati': { value: true, confidence: 100, explanation: 'Kebenaran absolut tentang keberadaan manusia' },
        'air mendidih pada suhu 100 derajat celcius di tekanan 1 atm': { value: true, confidence: 100, explanation: 'Kebenaran ilmiah absolut' },
        'manusia butuh oksigen untuk bernapas': { value: true, confidence: 100, explanation: 'Kebenaran biologis absolut' },
        'matahari terbit dari timur': { value: true, confidence: 100, explanation: 'Kebenaran astronomis absolut' },
        'gajah lebih besar daripada tikus': { value: true, confidence: 100, explanation: 'Kebenaran perbandingan absolut' },
        '1 + 1 = 2': { value: true, confidence: 100, explanation: 'Kebenaran matematis absolut' },
        '2 * 2 = 4': { value: true, confidence: 100, explanation: 'Kebenaran matematis absolut' },
        '0 < 1': { value: true, confidence: 100, explanation: 'Kebenaran matematis absolut' },
        '5 < 10': { value: true, confidence: 100, explanation: 'Kebenaran matematis absolut' },
        '10 > 5': { value: true, confidence: 100, explanation: 'Kebenaran matematis absolut' },
        '100 = 100': { value: true, confidence: 100, explanation: 'Kebenaran identitas absolut' },
        
        // Tautologi definisional
        'bujangan adalah pria yang belum menikah': { value: true, confidence: 100, explanation: 'Kebenaran berdasarkan definisi' },
        'segitiga memiliki tiga sisi': { value: true, confidence: 100, explanation: 'Kebenaran berdasarkan definisi' },
        'persegi memiliki empat sisi yang sama': { value: true, confidence: 100, explanation: 'Kebenaran berdasarkan definisi' },
        'jika seseorang adalah ayah, maka ia memiliki anak': { value: true, confidence: 100, explanation: 'Kebenaran berdasarkan definisi' },
        'jika hari ini hujan, maka hari ini hujan': { value: true, confidence: 100, explanation: 'Tautologi identitas' },
        'saya akan datang atau saya tidak akan datang': { value: true, confidence: 100, explanation: 'Tautologi excluded middle' },
        'besok hujan atau tidak hujan': { value: true, confidence: 100, explanation: 'Tautologi excluded middle' },
        'jika hari hujan maka jalan basah atau tidak basah': { value: true, confidence: 100, explanation: 'Tautologi kompleks' },
    },
    
    // KONTRADIKSI: Pernyataan yang selalu salah
    contradictions: {
        // Pola logika formal
        'p dan tidak p': { value: false, confidence: 100, explanation: 'Bentuk "A ∧ ¬A" selalu salah (kontradiksi formal)' },
        'tidak (p atau tidak p)': { value: false, confidence: 100, explanation: 'Bentuk "¬(A ∨ ¬A)" selalu salah' },
        'jika p maka tidak p dan p': { value: false, confidence: 100, explanation: 'Kontradiksi implikatif' },
        
        // Kontradiksi matematis
        '520 < 111': { value: false, confidence: 100, explanation: 'Kontradiksi matematis: 520 lebih besar dari 111' },
        '99 < 5': { value: false, confidence: 100, explanation: 'Kontradiksi matematis: 99 lebih besar dari 5' },
        '0 > 1': { value: false, confidence: 100, explanation: 'Kontradiksi matematis: 0 lebih kecil dari 1' },
        '2 + 2 = 5': { value: false, confidence: 100, explanation: 'Kontradiksi matematis' },
        '1 = 0': { value: false, confidence: 100, explanation: 'Kontradiksi matematis' },
        
        // Kontradiksi fisika/biologi
        'manusia bisa hidup tanpa oksigen': { value: false, confidence: 100, explanation: 'Kontradiksi biologis' },
        'air mendidih pada suhu 0 derajat celcius': { value: false, confidence: 100, explanation: 'Kontradiksi fisika' },
        'semut lebih besar daripada gajah': { value: false, confidence: 100, explanation: 'Kontradiksi perbandingan' },
        'matahari mengelilingi bumi': { value: false, confidence: 100, explanation: 'Kontradiksi astronomis' },
        'bumi itu datar': { value: false, confidence: 100, explanation: 'Kontradiksi geografis' },
        'es tidak dingin': { value: false, confidence: 100, explanation: 'Kontradiksi fisika' },
        'api tidak panas': { value: false, confidence: 100, explanation: 'Kontradiksi fisika' },
        'air tidak basah': { value: false, confidence: 100, explanation: 'Kontradiksi properti' },
        
        // Kontradiksi temporal
        'sekarang tahun 2003': { value: false, confidence: 100, explanation: 'Kontradiksi temporal' },
        'besok akan hujan dan tidak hujan pada saat yang sama': { value: false, confidence: 100, explanation: 'Kontradiksi simultan' },
        'saya sedang tidur dan tidak tidur pada waktu yang sama': { value: false, confidence: 100, explanation: 'Kontradiksi keadaan' },
        
        // Kontradiksi definisional
        'ada segitiga yang memiliki empat sisi': { value: false, confidence: 100, explanation: 'Kontradiksi definisi' },
        'persegi memiliki lima sisi': { value: false, confidence: 100, explanation: 'Kontradiksi definisi' },
        'lingkaran memiliki sudut': { value: false, confidence: 100, explanation: 'Kontradiksi definisi' },
        'pria hamil': { value: false, confidence: 100, explanation: 'Kontradiksi biologis' },
        'kucing berkokok': { value: false, confidence: 100, explanation: 'Kontradiksi zoologis' },
    },
    
    // FAKTA KONTINGEN: Bisa benar atau salah tergantung konteks
    contingentFacts: {
        'jakarta adalah ibu kota indonesia': { value: true, confidence: 100, explanation: 'Fakta geopolitik yang benar saat ini' },
        'indonesia merdeka pada tahun 1945': { value: true, confidence: 100, explanation: 'Fakta sejarah' },
        'emas lebih mahal daripada perak': { value: true, confidence: 90, explanation: 'Fakta ekonomi yang bisa berubah' },
        'sapi makan rumput': { value: true, confidence: 85, explanation: 'Umumnya benar, tapi ada pengecualian' },
        'kucing adalah hewan karnivora': { value: true, confidence: 88, explanation: 'Fakta biologis dengan variasi' },
        'manusia tidur 8 jam per hari': { value: false, confidence: 70, explanation: 'Rata-rata, bukan absolut' },
        'semua burung bisa terbang': { value: false, confidence: 80, explanation: 'Ada pengecualian (penguin, burung unta)' },
        'semua ikan hidup di air tawar': { value: false, confidence: 100, explanation: 'Ada ikan air laut' },
    },
    
    currentYear: new Date().getFullYear()
};

// =============================================================================
// FUNGSI UTAMA ANALISIS (Versi Baru - Fokus Tautologi & Kontradiksi)
// =============================================================================

/**
 * Fungsi utama untuk menganalisis proposisi (Versi Baru)
 * Fokus pada identifikasi tautologi dan kontradiksi
 */
function analyzeProposition(text) {
    const result = {
        isStatement: false,
        isProposition: false,
        truthValue: null,
        isTautology: false,
        isContradiction: false,
        isContingent: false,
        explanation: '',
        confidence: 0,
        propositionType: '',
        detailedAnalysis: '',
        semanticAnalysis: '',
        logicPattern: '',
        validationMethod: ''
    };
    
    const cleanText = text.trim();
    const lowerText = cleanText.toLowerCase();
    
    if (!cleanText) {
        result.explanation = 'Teks kosong tidak dapat dianalisis';
        return result;
    }
    
    // Langsung cek apakah ini pernyataan
    result.isStatement = instantIsStatement(cleanText);
    
    if (result.isStatement) {
        result.isProposition = instantIsProposition(cleanText);
        
        if (result.isProposition) {
            // Analisis utama: cek apakah tautologi, kontradiksi, atau kontingen
            const analysisResult = analyzeTruthNature(cleanText);
            
            result.truthValue = analysisResult.truthValue;
            result.confidence = analysisResult.confidence;
            result.explanation = analysisResult.explanation;
            result.isTautology = analysisResult.isTautology;
            result.isContradiction = analysisResult.isContradiction;
            result.isContingent = analysisResult.isContingent;
            result.semanticAnalysis = analysisResult.semanticAnalysis;
            result.logicPattern = analysisResult.logicPattern;
            result.validationMethod = analysisResult.validationMethod;
            
            // Tentukan jenis proposisi
            if (result.isTautology) {
                result.propositionType = 'TAUTOLOGI';
            } else if (result.isContradiction) {
                result.propositionType = 'KONTRADIKSI';
            } else {
                result.propositionType = 'KONTINGEN';
            }
            
            result.detailedAnalysis = generateDetailedAnalysis(cleanText, result);
        } else {
            result.explanation = 'Pernyataan ini mengandung unsur subjektif atau ketidakjelasan yang membuatnya bukan proposisi.';
        }
    } else {
        result.explanation = 'Ini bukan pernyataan deklaratif. Hanya pernyataan yang dapat menjadi proposisi.';
    }
    
    return result;
}

/**
 * Analisis mendalam tentang sifat kebenaran pernyataan
 */
function analyzeTruthNature(text) {
    const lowerText = text.toLowerCase();
    let result = {
        truthValue: "TIDAK DAPAT DITENTUKAN",
        confidence: 0,
        explanation: '',
        isTautology: false,
        isContradiction: false,
        isContingent: false,
        semanticAnalysis: '',
        logicPattern: '',
        validationMethod: ''
    };
    
    // 1. Cek apakah termasuk tautologi
    const tautologyCheck = checkTautology(text);
    if (tautologyCheck.isTautology) {
        result.truthValue = "BENAR";
        result.confidence = 100;
        result.isTautology = true;
        result.explanation = tautologyCheck.explanation;
        result.logicPattern = tautologyCheck.pattern;
        result.validationMethod = tautologyCheck.method;
        result.semanticAnalysis = analyzeSemantics(text, true);
        return result;
    }
    
    // 2. Cek apakah termasuk kontradiksi
    const contradictionCheck = checkContradiction(text);
    if (contradictionCheck.isContradiction) {
        result.truthValue = "SALAH";
        result.confidence = 100;
        result.isContradiction = true;
        result.explanation = contradictionCheck.explanation;
        result.logicPattern = contradictionCheck.pattern;
        result.validationMethod = contradictionCheck.method;
        result.semanticAnalysis = analyzeSemantics(text, false);
        return result;
    }
    
    // 3. Cek di knowledge base
    const kbCheck = checkKnowledgeBase(text);
    if (kbCheck.found) {
        result.truthValue = kbCheck.value ? "BENAR" : "SALAH";
        result.confidence = kbCheck.confidence;
        result.explanation = kbCheck.explanation;
        result.isContingent = !kbCheck.isAbsolute;
        result.semanticAnalysis = analyzeSemantics(text, kbCheck.value);
        result.validationMethod = kbCheck.method;
        
        if (kbCheck.isAbsolute) {
            if (kbCheck.value) {
                result.isTautology = true;
                result.propositionType = 'TAUTOLOGI';
            } else {
                result.isContradiction = true;
                result.propositionType = 'KONTRADIKSI';
            }
        } else {
            result.isContingent = true;
            result.propositionType = 'KONTINGEN';
        }
        return result;
    }
    
    // 4. Cek perbandingan matematika
    const mathCheck = checkMathematicalStatement(text);
    if (mathCheck.valid) {
        result.truthValue = mathCheck.value ? "BENAR" : "SALAH";
        result.confidence = 100;
        result.explanation = mathCheck.explanation;
        result.isContingent = true;
        result.semanticAnalysis = 'Pernyataan matematika yang dapat dievaluasi secara objektif.';
        result.validationMethod = 'Evaluasi matematika';
        return result;
    }
    
    // 5. Default: kontingen dengan nilai tidak dapat ditentukan
    result.truthValue = "TIDAK DAPAT DITENTUKAN";
    result.confidence = 0;
    result.isContingent = true;
    result.explanation = 'Pernyataan ini memerlukan konteks tambahan untuk dievaluasi.';
    result.semanticAnalysis = 'Tidak cukup informasi untuk menentukan sifat kebenaran pernyataan ini.';
    result.validationMethod = 'Analisis kontekstual diperlukan';
    
    return result;
}

// =============================================================================
// FUNGSI DETEKSI TAUTOLOGI & KONTRADIKSI
// =============================================================================

/**
 * Deteksi tautologi berdasarkan pola logika dan semantik
 */
function checkTautology(text) {
    const lowerText = text.toLowerCase().trim();
    const result = {
        isTautology: false,
        explanation: '',
        pattern: '',
        method: ''
    };
    
    // Pola-pola tautologi formal
    const tautologyPatterns = [
        // Pola excluded middle: A atau tidak A
        { 
            pattern: /^(.*)\s+(atau|or)\s+(tidak|bukan|not)\s+\1$/i,
            test: (match) => true,
            explanation: (match) => `"${match[1]} atau tidak ${match[1]}" adalah tautologi (hukum excluded middle).`,
            method: 'Pola logika formal: A ∨ ¬A'
        },
        
        // Pola identitas: jika A maka A
        { 
            pattern: /^(jika|if)\s+(.*)\s+(maka|then)\s+\2$/i,
            test: (match) => true,
            explanation: (match) => `"jika ${match[2]} maka ${match[2]}" adalah tautologi (implikasi identitas).`,
            method: 'Pola logika formal: A → A'
        },
        
        // Pola non-kontradiksi: tidak (A dan tidak A)
        { 
            pattern: /^tidak\s+\((.*)\s+(dan|and)\s+(tidak|bukan|not)\s+\1\)$/i,
            test: (match) => true,
            explanation: (match) => `"tidak (${match[1]} dan tidak ${match[1]})" adalah tautologi (hukum non-kontradiksi).`,
            method: 'Pola logika formal: ¬(A ∧ ¬A)'
        },
        
        // Pola ekuivalensi: A jika dan hanya jika A
        { 
            pattern: /^(.*)\s+(jika\s+dan\s+hanya\s+jika|iff)\s+\1$/i,
            test: (match) => true,
            explanation: (match) => `"${match[1]} jika dan hanya jika ${match[1]}" adalah tautologi (ekuivalensi identitas).`,
            method: 'Pola logika formal: A ↔ A'
        },
        
        // Pola disjungsi dengan tautologi
        { 
            pattern: /^(.*)\s+(atau|or)\s+(.*\s+atau\s+tidak\s+.*)$/i,
            test: (match) => match[3].includes('atau tidak') || match[3].includes('or not'),
            explanation: (match) => `Disjungsi dengan tautologi menghasilkan tautologi.`,
            method: 'Sifat logika: (A ∨ T) ≡ T'
        },
        
        // Pola implikasi dengan kontradiksi sebagai antecedent
        { 
            pattern: /^(jika|if)\s+(.*\s+dan\s+tidak\s+.*)\s+(maka|then)/i,
            test: (match) => match[2].includes('dan tidak'),
            explanation: (match) => `Implikasi dengan kontradiksi sebagai anteseden selalu benar.`,
            method: 'Sifat logika: (⊥ → A) ≡ T'
        }
    ];
    
    // Cek pola formal
    for (const pattern of tautologyPatterns) {
        const match = lowerText.match(pattern.pattern);
        if (match && pattern.test(match)) {
            result.isTautology = true;
            result.explanation = pattern.explanation(match);
            result.pattern = pattern.method;
            result.method = 'Deteksi pola logika formal';
            return result;
        }
    }
    
    // Cek di knowledge base tautologies
    for (const [tautology, data] of Object.entries(knowledgeBase.tautologies)) {
        if (lowerText.includes(tautology) || tautology.includes(lowerText) || 
            areStatementsSimilar(lowerText, tautology)) {
            result.isTautology = true;
            result.explanation = data.explanation;
            result.pattern = 'Tautologi berdasarkan pengetahuan';
            result.method = 'Basis pengetahuan tautologi';
            return result;
        }
    }
    
    // Analisis semantik untuk tautologi
    const semanticTautology = checkSemanticTautology(text);
    if (semanticTautology.isTautology) {
        result.isTautology = true;
        result.explanation = semanticTautology.explanation;
        result.pattern = semanticTautology.pattern;
        result.method = 'Analisis semantik';
        return result;
    }
    
    return result;
}

/**
 * Deteksi kontradiksi berdasarkan pola logika dan semantik
 */
function checkContradiction(text) {
    const lowerText = text.toLowerCase().trim();
    const result = {
        isContradiction: false,
        explanation: '',
        pattern: '',
        method: ''
    };
    
    // Pola-pola kontradiksi formal
    const contradictionPatterns = [
        // Pola kontradiksi langsung: A dan tidak A
        { 
            pattern: /^(.*)\s+(dan|and)\s+(tidak|bukan|not)\s+\1$/i,
            test: (match) => true,
            explanation: (match) => `"${match[1]} dan tidak ${match[1]}" adalah kontradiksi (hukum kontradiksi).`,
            method: 'Pola logika formal: A ∧ ¬A'
        },
        
        // Pola negasi excluded middle: tidak (A atau tidak A)
        { 
            pattern: /^tidak\s+\((.*)\s+(atau|or)\s+(tidak|bukan|not)\s+\1\)$/i,
            test: (match) => true,
            explanation: (match) => `"tidak (${match[1]} atau tidak ${match[1]})" adalah kontradiksi.`,
            method: 'Pola logika formal: ¬(A ∨ ¬A)'
        },
        
        // Pola implikasi yang mustahil: jika A maka tidak A, dan A
        { 
            pattern: /^jika\s+(.*)\s+maka\s+tidak\s+\1\s+dan\s+\1$/i,
            test: (match) => true,
            explanation: (match) => `"jika ${match[1]} maka tidak ${match[1]}, dan ${match[1]}" adalah kontradiksi.`,
            method: 'Kombinasi logika yang mustahil'
        },
        
        // Pola konjungsi dengan kontradiksi
        { 
            pattern: /^(.*)\s+(dan|and)\s+(.*\s+dan\s+tidak\s+.*)$/i,
            test: (match) => match[3].includes('dan tidak'),
            explanation: (match) => `Konjungsi dengan kontradiksi menghasilkan kontradiksi.`,
            method: 'Sifat logika: (A ∧ ⊥) ≡ ⊥'
        }
    ];
    
    // Cek pola formal
    for (const pattern of contradictionPatterns) {
        const match = lowerText.match(pattern.pattern);
        if (match && pattern.test(match)) {
            result.isContradiction = true;
            result.explanation = pattern.explanation(match);
            result.pattern = pattern.method;
            result.method = 'Deteksi pola logika formal';
            return result;
        }
    }
    
    // Cek di knowledge base contradictions
    for (const [contradiction, data] of Object.entries(knowledgeBase.contradictions)) {
        if (lowerText.includes(contradiction) || contradiction.includes(lowerText) || 
            areStatementsSimilar(lowerText, contradiction)) {
            result.isContradiction = true;
            result.explanation = data.explanation;
            result.pattern = 'Kontradiksi berdasarkan pengetahuan';
            result.method = 'Basis pengetahuan kontradiksi';
            return result;
        }
    }
    
    // Analisis semantik untuk kontradiksi
    const semanticContradiction = checkSemanticContradiction(text);
    if (semanticContradiction.isContradiction) {
        result.isContradiction = true;
        result.explanation = semanticContradiction.explanation;
        result.pattern = semanticContradiction.pattern;
        result.method = 'Analisis semantik';
        return result;
    }
    
    return result;
}

/**
 * Analisis semantik untuk tautologi
 */
function checkSemanticTautology(text) {
    const lowerText = text.toLowerCase();
    const result = {
        isTautology: false,
        explanation: '',
        pattern: ''
    };
    
    // Ciri-ciri tautologi berdasarkan makna:
    
    // 1. Pernyataan tentang semua kemungkinan
    const allPossibilityPatterns = [
        /(.*)\s+(atau|or)\s+(.*)\s+(pasti|selalu|always)\s+(terjadi|benar|true)/i,
        /(tidak\s+ada|no)\s+(kemungkinan|possibility)\s+(lain|other)/i,
        /(pasti|selalu|always)\s+(.*)\s+(atau|or)\s+(.*)/i
    ];
    
    for (const pattern of allPossibilityPatterns) {
        if (pattern.test(lowerText)) {
            result.isTautology = true;
            result.explanation = 'Pernyataan ini mencakup semua kemungkinan, sehingga selalu benar.';
            result.pattern = 'Ekshausif semua kemungkinan';
            return result;
        }
    }
    
    // 2. Pernyataan berdasarkan definisi
    const definitionPatterns = [
        /(.*)\s+(adalah|is)\s+(definisi|definition)\s+dari/i,
        /(menurut|according to)\s+(definisi|definition)/i,
        /(secara|by)\s+(definisi|definition)/i
    ];
    
    for (const pattern of definitionPatterns) {
        if (pattern.test(lowerText)) {
            result.isTautology = true;
            result.explanation = 'Pernyataan berdasarkan definisi selalu benar.';
            result.pattern = 'Kebenaran definisional';
            return result;
        }
    }
    
    // 3. Pernyataan identitas
    if (lowerText.includes('sama dengan') || 
        lowerText.includes('identik dengan') || 
        lowerText.includes('=') && !lowerText.includes('≠')) {
        const parts = lowerText.split(/(sama dengan|=|identik dengan)/);
        if (parts.length >= 3) {
            const left = parts[0].trim();
            const right = parts[2].trim();
            if (left === right) {
                result.isTautology = true;
                result.explanation = 'Pernyataan identitas selalu benar.';
                result.pattern = 'Identitas: A = A';
                return result;
            }
        }
    }
    
    return result;
}

/**
 * Analisis semantik untuk kontradiksi
 */
function checkSemanticContradiction(text) {
    const lowerText = text.toLowerCase();
    const result = {
        isContradiction: false,
        explanation: '',
        pattern: ''
    };
    
    // Ciri-ciri kontradiksi berdasarkan makna:
    
    // 1. Pernyataan yang mustahil secara fisika
    const physicalImpossible = [
        'lebih kecil dari dirinya sendiri',
        'lebih besar dan lebih kecil',
        'sebelum dan sesudah bersamaan',
        'hidup dan mati bersamaan',
        'ada dan tidak ada bersamaan',
        'di dalam dan di luar bersamaan'
    ];
    
    for (const phrase of physicalImpossible) {
        if (lowerText.includes(phrase)) {
            result.isContradiction = true;
            result.explanation = `"${phrase}" adalah kontradiksi fisika.`;
            result.pattern = 'Kontradiksi fisika';
            return result;
        }
    }
    
    // 2. Pernyataan yang melanggar definisi
    const definitionViolation = [
        'segitiga bersisi empat',
        'persegi bersisi tiga',
        'lingkaran bersudut',
        'pria hamil',
        'kucing berkokok',
        'sapi bertelur',
        'ikan terbang di udara'
    ];
    
    for (const phrase of definitionViolation) {
        if (lowerText.includes(phrase)) {
            result.isContradiction = true;
            result.explanation = `"${phrase}" melanggar definisi.`;
            result.pattern = 'Pelanggaran definisi';
            return result;
        }
    }
    
    // 3. Pernyataan yang saling meniadakan
    const negatingPhrases = [
        ['selalu', 'tidak pernah'],
        ['semua', 'tidak ada'],
        ['pasti', 'mungkin tidak'],
        ['harus', 'tidak boleh'],
        ['wajib', 'dilarang']
    ];
    
    for (const [pos, neg] of negatingPhrases) {
        if (lowerText.includes(pos) && lowerText.includes(neg)) {
            result.isContradiction = true;
            result.explanation = `Kata "${pos}" dan "${neg}" saling meniadakan.`;
            result.pattern = 'Kata yang saling meniadakan';
            return result;
        }
    }
    
    return result;
}

/**
 * Analisis semantik umum
 */
function analyzeSemantics(text, isTrue) {
    const lowerText = text.toLowerCase();
    let analysis = '';
    
    if (isTrue) {
        analysis += "✅ **Analisis Semantik:**\n";
        analysis += "Pernyataan ini memiliki makna yang konsisten dengan realitas.\n";
        
        if (lowerText.includes('semua') || lowerText.includes('selalu') || lowerText.includes('pasti')) {
            analysis += "• Menggunakan kata kuantor universal yang menunjukkan kepastian.\n";
        }
        
        if (lowerText.includes('definisi') || lowerText.includes('artinya') || lowerText.includes('berarti')) {
            analysis += "• Berdasarkan definisi atau makna istilah.\n";
        }
        
        if (/\d+\s*[=<>]+\s*\d+/.test(text)) {
            analysis += "• Memuat perbandingan matematika yang terverifikasi.\n";
        }
    } else {
        analysis += "❌ **Analisis Semantik:**\n";
        analysis += "Pernyataan ini mengandung makna yang bertentangan dengan realitas.\n";
        
        if (lowerText.includes('dan tidak') || lowerText.includes('tetapi tidak')) {
            analysis += "• Mengandung klaim yang saling bertentangan.\n";
        }
        
        if (lowerText.includes('mustahil') || lowerText.includes('tidak mungkin')) {
            analysis += "• Mengakui ketidakmungkinan dalam pernyataannya sendiri.\n";
        }
        
        if (/\d+\s*[<>]\s*\d+/.test(text)) {
            const mathCheck = checkMathematicalStatement(text);
            if (!mathCheck.value) {
                analysis += "• Memuat perbandingan matematika yang salah.\n";
            }
        }
    }
    
    return analysis;
}

// =============================================================================
// FUNGSI BANTUAN
// =============================================================================

function checkKnowledgeBase(text) {
    const lowerText = text.toLowerCase();
    
    // Cek tautologies
    for (const [fact, data] of Object.entries(knowledgeBase.tautologies)) {
        if (lowerText.includes(fact) || fact.includes(lowerText) || areStatementsSimilar(lowerText, fact)) {
            return {
                found: true,
                value: data.value,
                confidence: data.confidence,
                explanation: data.explanation,
                isAbsolute: true,
                method: 'Basis pengetahuan tautologi'
            };
        }
    }
    
    // Cek contradictions
    for (const [fact, data] of Object.entries(knowledgeBase.contradictions)) {
        if (lowerText.includes(fact) || fact.includes(lowerText) || areStatementsSimilar(lowerText, fact)) {
            return {
                found: true,
                value: data.value,
                confidence: data.confidence,
                explanation: data.explanation,
                isAbsolute: true,
                method: 'Basis pengetahuan kontradiksi'
            };
        }
    }
    
    // Cek contingent facts
    for (const [fact, data] of Object.entries(knowledgeBase.contingentFacts)) {
        if (lowerText.includes(fact) || fact.includes(lowerText) || areStatementsSimilar(lowerText, fact)) {
            return {
                found: true,
                value: data.value,
                confidence: data.confidence,
                explanation: data.explanation,
                isAbsolute: false,
                method: 'Basis pengetahuan fakta kontingen'
            };
        }
    }
    
    return { found: false };
}

function checkMathematicalStatement(text) {
    const result = {
        valid: false,
        value: null,
        explanation: ''
    };
    
    try {
        // Deteksi perbandingan matematika
        const comparisonPattern = /(\d+\.?\d*)\s*([<>]=?|=)\s*(\d+\.?\d*)/;
        const match = text.match(comparisonPattern);
        
        if (match) {
            const num1 = parseFloat(match[1]);
            const operator = match[2];
            const num2 = parseFloat(match[3]);
            
            let isTrue;
            switch (operator) {
                case '<': isTrue = num1 < num2; break;
                case '>': isTrue = num1 > num2; break;
                case '<=': isTrue = num1 <= num2; break;
                case '>=': isTrue = num1 >= num2; break;
                case '=': 
                case '==': isTrue = num1 === num2; break;
                default: isTrue = false;
            }
            
            result.valid = true;
            result.value = isTrue;
            result.explanation = `${num1} ${operator} ${num2} adalah ${isTrue ? 'benar' : 'salah'}.`;
            return result;
        }
        
        // Deteksi operasi matematika sederhana
        const operationPattern = /(\d+)\s*([+\-*/])\s*(\d+)\s*=\s*(\d+)/;
        const opMatch = text.match(operationPattern);
        
        if (opMatch) {
            const num1 = parseInt(opMatch[1]);
            const operator = opMatch[2];
            const num2 = parseInt(opMatch[3]);
            const expected = parseInt(opMatch[4]);
            
            let actual;
            switch (operator) {
                case '+': actual = num1 + num2; break;
                case '-': actual = num1 - num2; break;
                case '*': actual = num1 * num2; break;
                case '/': actual = num1 / num2; break;
                default: actual = null;
            }
            
            if (actual !== null) {
                result.valid = true;
                result.value = (actual === expected);
                result.explanation = `${num1} ${operator} ${num2} = ${actual}, ${result.value ? 'benar' : 'salah'} (diklaim ${expected}).`;
                return result;
            }
        }
    } catch (e) {
        console.error('Error in mathematical check:', e);
    }
    
    return result;
}

function instantIsStatement(text) {
    const lowerText = text.toLowerCase();
    
    // Cek apakah pertanyaan
    if (text.trim().endsWith('?') || 
        lowerText.startsWith('apakah') ||
        lowerText.startsWith('siapa') ||
        lowerText.startsWith('mengapa') ||
        lowerText.startsWith('bagaimana')) {
        return false;
    }
    
    // Cek apakah perintah
    if (text.trim().endsWith('!') || 
        /^(tolong|silakan|mohon|harap)/i.test(text)) {
        return false;
    }
    
    // Cek apakah kalimat deklaratif minimal
    return text.split(' ').length >= 1;
}

function instantIsProposition(text) {
    const lowerText = text.toLowerCase();
    
    // Kata subjektif yang membuat bukan proposisi
    const subjectiveWords = [
        'cantik', 'jelek', 'indah', 'buruk', 'bagus', 'baik',
        'senang', 'sedih', 'bahagia', 'kesal', 'marah',
        'enak', 'tidak enak', 'lezat', 'pahit', 'manis'
    ];
    
    const hasSubjective = subjectiveWords.some(word => lowerText.includes(word));
    if (hasSubjective) return false;
    
    // Pola ambigu
    const ambiguousPatterns = [
        /\b(mungkin|bisa jadi|barangkali|sepertinya|tampaknya)\b/i,
        /\b(sedikit|agak|cukup|lumayan)\b/i
    ];
    
    if (ambiguousPatterns.some(pattern => pattern.test(text))) {
        return false;
    }
    
    return true;
}

function areStatementsSimilar(text1, text2) {
    const words1 = text1.toLowerCase().split(/\s+/);
    const words2 = text2.toLowerCase().split(/\s+/);
    
    let matches = 0;
    words1.forEach(word1 => {
        if (words2.includes(word1)) {
            matches++;
        }
    });
    
    return matches >= Math.min(words1.length, words2.length) * 0.6;
}

function generateDetailedAnalysis(text, result) {
    let analysis = '';
    
    if (result.isTautology) {
        analysis += "🔍 **Analisis Mendalam Tautologi:**\n";
        analysis += "1. Struktur logika membuat pernyataan ini selalu benar\n";
        analysis += "2. Tidak bergantung pada fakta dunia nyata\n";
        analysis += "3. Valid dalam semua interpretasi dan kondisi\n";
        analysis += "4. Contoh pola: A ∨ ¬A, A → A, ¬(A ∧ ¬A)\n";
    } else if (result.isContradiction) {
        analysis += "🔍 **Analisis Mendalam Kontradiksi:**\n";
        analysis += "1. Struktur logika membuat pernyataan ini selalu salah\n";
        analysis += "2. Mengandung unsur yang saling meniadakan\n";
        analysis += "3. Tidak mungkin benar dalam kondisi apapun\n";
        analysis += "4. Contoh pola: A ∧ ¬A, ¬(A ∨ ¬A)\n";
    } else {
        analysis += "🔍 **Analisis Mendalam Kontingen:**\n";
        analysis += "1. Nilai kebenaran bergantung pada kondisi\n";
        analysis += "2. Dapat diverifikasi dengan fakta dunia nyata\n";
        analysis += "3. Memerlukan konteks untuk evaluasi\n";
        analysis += "4. Sebagian besar pernyataan sehari-hari adalah kontingen\n";
    }
    
    return analysis;
}

// =============================================================================
// FUNGSI UNTUK MODE MAJEMUK
// =============================================================================

/**
 * Set operator untuk proposisi majemuk
 */
function setOperator(operator) {
    currentOperator = operator;
    
    // Update tampilan operator
    const operatorBtns = document.querySelectorAll('.operator-btn');
    operatorBtns.forEach(btn => {
        btn.classList.remove('selected');
        btn.classList.remove('active');
        if (btn.dataset.operator === operator) {
            btn.classList.add('selected');
            btn.classList.add('active');
        }
    });
    
    // Update preview operator di halaman jika ada
    updateCompoundPreview();
}

/**
 * Update nilai kebenaran P atau Q
 */
function updateTruthValue(variable, value) {
    if (variable === 'p') {
        truthP = value;
        // Update tombol truth P
        const truthPButtons = document.querySelectorAll('.truth-btn[data-prop="p"]');
        truthPButtons.forEach(btn => {
            btn.classList.remove('selected');
            if (btn.dataset.value === value.toString()) {
                btn.classList.add('selected');
            }
        });
    } else if (variable === 'q') {
        truthQ = value;
        // Update tombol truth Q
        const truthQButtons = document.querySelectorAll('.truth-btn[data-prop="q"]');
        truthQButtons.forEach(btn => {
            btn.classList.remove('selected');
            if (btn.dataset.value === value.toString()) {
                btn.classList.add('selected');
            }
        });
    }
}

/**
 * Update preview proposisi majemuk
 */
function updateCompoundPreview() {
    const pInput = document.getElementById('inputP')?.value || 'P';
    const qInput = document.getElementById('inputQ')?.value || 'Q';
    
    // Di halaman ini tidak ada preview khusus, tapi kita bisa update di console
    console.log(`Preview: ${pInput} ${getOperatorSymbol(currentOperator)} ${qInput}`);
}

/**
 * Set contoh cepat untuk mode majemuk
 */
function setCompoundExample(p, q, operator) {
    document.getElementById('inputP').value = p;
    document.getElementById('inputQ').value = q;
    setOperator(operator);
    
    // Analisis masing-masing proposisi untuk nilai kebenaran
    if (p) {
        const pResult = analyzeProposition(p);
        updateTruthValue('p', pResult.truthValue === "BENAR");
    }
    
    if (q) {
        const qResult = analyzeProposition(q);
        updateTruthValue('q', qResult.truthValue === "BENAR");
    }
    
    // Pindah ke mode compound jika belum
    if (currentMode !== 'compound') {
        switchMode('compound');
    }
    
    showNotification('Contoh berhasil dimuat!', 'success');
}

/**
 * Get operator symbol untuk ditampilkan
 */
function getOperatorSymbol(operator) {
    const symbols = {
        'and': '∧',
        'or': '∨',
        'not': '¬',
        'imply': '→',
        'biconditional': '↔'
    };
    return symbols[operator] || operator;
}

/**
 * Get operator name untuk ditampilkan
 */
function getOperatorName(operator) {
    const names = {
        'and': 'Konjungsi (DAN)',
        'or': 'Disjungsi (ATAU)',
        'not': 'Negasi (BUKAN)',
        'imply': 'Implikasi (JIKA-MAKA)',
        'biconditional': 'Biimplikasi (JIKA DAN HANYA JIKA)'
    };
    return names[operator] || operator;
}

// =============================================================================
// FUNGSI TAMPILAN HASIL
// =============================================================================

function displaySingleResult(result, originalText) {
    const resultSection = document.getElementById('resultSection');
    const singleResultSection = document.getElementById('singleResultSection');
    
    if (!resultSection || !singleResultSection) return;
    
    // Tentukan warna dan ikon berdasarkan jenis proposisi
    let propositionTypeColor = '';
    let propositionTypeIcon = '';
    let propositionTypeText = '';
    let statusBadge = '';
    
    if (result.isTautology) {
        propositionTypeColor = '#00ff88';
        propositionTypeIcon = 'mdi-checkbox-marked-circle';
        propositionTypeText = 'TAUTOLOGI';
        statusBadge = `<span class="badge" style="background: linear-gradient(45deg, #00ff88, #00cc66);">✅ TAUTOLOGI</span>`;
    } else if (result.isContradiction) {
        propositionTypeColor = '#ff0066';
        propositionTypeIcon = 'mdi-close-circle';
        propositionTypeText = 'KONTRADIKSI';
        statusBadge = `<span class="badge" style="background: linear-gradient(45deg, #ff0066, #cc0052);">❌ KONTRADIKSI</span>`;
    } else if (result.isContingent) {
        propositionTypeColor = '#ffcc00';
        propositionTypeIcon = 'mdi-alert-circle';
        propositionTypeText = 'KONTINGEN';
        statusBadge = `<span class="badge" style="background: linear-gradient(45deg, #ffcc00, #cc9900);">⚠️ KONTINGEN</span>`;
    }
    
    const truthIcon = result.truthValue === "BENAR" ? '✅' : 
                     result.truthValue === "SALAH" ? '❌' : '❓';
    const truthColor = result.truthValue === "BENAR" ? '#00ff88' : 
                      result.truthValue === "SALAH" ? '#ff0066' : '#ffcc00';
    
    let html = `
        <div class="glass-card animate__animated animate__fadeInUp">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-file-document-edit-outline me-2"></i>
                        Hasil Analisis Logika
                    </h5>
                    <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem;">
                        <i class="mdi mdi-clock-outline me-1"></i>
                        ${new Date().toLocaleTimeString('id-ID')}
                    </div>
                </div>
                ${statusBadge}
            </div>
            
            <div class="result-card animate__animated animate__fadeIn" style="animation-delay: 0.1s; border-left: 4px solid ${propositionTypeColor};">
                <!-- Input Pengguna -->
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 10px; margin-bottom: 1.5rem;">
                    <div style="color: var(--accent-cyan); font-weight: 600; margin-bottom: 0.5rem;">
                        <i class="mdi mdi-format-quote-open me-1"></i>
                        Pernyataan yang Dianalisis
                    </div>
                    <div style="color: #fff; font-size: 1.1rem; font-weight: 600; font-style: italic;">
                        "${originalText}"
                    </div>
                </div>
                
                <!-- Status Proposisi -->
                <div class="mb-4">
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;">
                        <span style="color: var(--primary-neon);">1.</span> Status Proposisi
                    </div>
                    <div style="display: flex; align-items: center; padding: 12px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 2px solid ${propositionTypeColor}40;">
                        <i class="mdi ${propositionTypeIcon}" style="font-size: 1.5rem; margin-right: 10px; color: ${propositionTypeColor};"></i>
                        <div>
                            <div style="font-size: 1.2rem; font-weight: bold; color: ${propositionTypeColor};">
                                ${propositionTypeText}
                            </div>
                            <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-top: 2px;">
                                ${result.isTautology ? 'Selalu benar' : result.isContradiction ? 'Selalu salah' : 'Bisa benar atau salah'}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nilai Kebenaran -->
                <div class="mb-4">
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;">
                        <span style="color: var(--primary-neon);">2.</span> Nilai Kebenaran
                    </div>
                    <div style="display: flex; align-items: center; padding: 12px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 2px solid ${truthColor}40;">
                        <span style="font-size: 1.5rem; margin-right: 10px; color: ${truthColor};">${truthIcon}</span>
                        <div>
                            <div style="font-size: 1.2rem; font-weight: bold; color: ${truthColor};">
                                ${result.truthValue || 'TIDAK DAPAT DITENTUKAN'}
                            </div>
                            <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-top: 2px;">
                                Keyakinan: ${result.confidence}%
                            </div>
                        </div>
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-top: 5px; padding-left: 10px;">
                        ${result.explanation || 'Penjelasan tidak tersedia.'}
                    </div>
                </div>
                
                <!-- Pola Logika -->
                ${result.logicPattern ? `
                <div class="mb-4">
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;">
                        <span style="color: var(--primary-neon);">3.</span> Pola Logika
                    </div>
                    <div style="padding: 12px; background: rgba(0, 245, 255, 0.05); border-radius: 8px; border: 1px solid rgba(0, 245, 255, 0.2);">
                        <div style="color: var(--accent-cyan); font-weight: 600; margin-bottom: 5px;">
                            <i class="mdi mdi-logic-gate me-1"></i>
                            ${result.logicPattern}
                        </div>
                        <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">
                            Metode validasi: ${result.validationMethod}
                        </div>
                    </div>
                </div>
                ` : ''}
                
                <!-- Analisis Semantik -->
                ${result.semanticAnalysis ? `
                <div class="mb-4">
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;">
                        <span style="color: var(--primary-neon);">4.</span> Analisis Makna
                    </div>
                    <div style="padding: 12px; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div style="color: rgba(255, 255, 255, 0.9); font-size: 0.95rem; line-height: 1.6; white-space: pre-line;">
                            ${result.semanticAnalysis}
                        </div>
                    </div>
                </div>
                ` : ''}
                
                <!-- Ringkasan -->
                <div style="border-top: 2px dashed rgba(255, 255, 255, 0.1); margin: 1.5rem 0; padding-top: 1.5rem;">
                    <div style="color: var(--accent-cyan); font-weight: 600; margin-bottom: 10px;">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Kesimpulan Logika
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.95rem; line-height: 1.6; padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
                        ${generateLogicConclusion(result, originalText)}
                    </div>
                </div>
                
                <!-- Contoh Lain -->
                <div style="background: rgba(0, 245, 255, 0.05); border-radius: 10px; padding: 1rem; margin-top: 1rem;">
                    <div style="color: var(--primary-neon); font-weight: 600; margin-bottom: 10px;">
                        <i class="mdi mdi-lightbulb-on-outline me-2"></i>
                        Coba Juga:
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <button class="btn btn-sm btn-outline-neon" onclick="setExample('hari ini hujan atau tidak hujan', 'single')">
                            Tautologi: "hari ini hujan atau tidak hujan"
                        </button>
                        <button class="btn btn-sm btn-outline-neon" onclick="setExample('520 < 111', 'single')">
                            Kontradiksi: "520 < 111"
                        </button>
                        <button class="btn btn-sm btn-outline-neon" onclick="setExample('jakarta adalah ibu kota indonesia', 'single')">
                            Kontingen: "jakarta adalah ibu kota indonesia"
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    singleResultSection.innerHTML = html;
    singleResultSection.style.display = 'block';
    resultSection.style.display = 'block';
    
    // Scroll ke hasil
    setTimeout(() => {
        resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
    
    // Simpan ke history
    addToHistory(originalText, result);
}

function displayCompoundResult(compoundSentence, compoundValue, explanation, pValue, qValue, pAnalysis, qAnalysis) {
    const resultSection = document.getElementById('resultSection');
    const compoundResultSection = document.getElementById('compoundResultSection');
    
    if (!resultSection || !compoundResultSection) return;
    
    const truthClass = compoundValue ? 'result-true' : 'result-false';
    const truthIcon = compoundValue ? 'mdi-check-bold' : 'mdi-close-thick';
    const truthText = compoundValue ? 'BENAR' : 'SALAH';
    const truthColor = compoundValue ? '#00ff88' : '#ff0066';
    
    // Tentukan jenis proposisi majemuk
    let compoundType = 'KONTINGEN';
    let compoundTypeColor = '#ffcc00';
    
    if (pAnalysis && qAnalysis) {
        if (pAnalysis.isTautology && qAnalysis.isTautology) {
            if (currentOperator === 'and' || currentOperator === 'biconditional') {
                compoundType = 'TAUTOLOGI';
                compoundTypeColor = '#00ff88';
            }
        } else if (pAnalysis.isContradiction || qAnalysis.isContradiction) {
            if (currentOperator === 'and') {
                compoundType = 'KONTRADIKSI';
                compoundTypeColor = '#ff0066';
            }
        }
    }
    
    let html = `
        <div class="glass-card animate__animated animate__fadeInUp">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 style="color: var(--primary-neon);">
                        <i class="mdi mdi-gate-or me-2"></i>
                        Hasil Analisis Proposisi Majemuk
                    </h5>
                    <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem;">
                        <i class="mdi mdi-clock-outline me-1"></i>
                        ${new Date().toLocaleTimeString('id-ID')}
                    </div>
                </div>
                <span class="badge" style="background: linear-gradient(45deg, ${compoundTypeColor}, ${compoundTypeColor}80);">
                    ${compoundType}
                </span>
            </div>
            
            <div class="result-card animate__animated animate__fadeIn" style="animation-delay: 0.1s">
                <!-- Proposisi Majemuk -->
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 10px; margin-bottom: 1.5rem;">
                    <div style="color: var(--accent-cyan); font-weight: 600; margin-bottom: 0.5rem;">
                        <i class="mdi mdi-logic-gate me-1"></i>
                        Proposisi Majemuk
                    </div>
                    <div style="color: #fff; font-size: 1.1rem; font-weight: 600; font-family: 'JetBrains Mono', monospace;">
                        "${compoundSentence}"
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-top: 0.5rem;">
                        Operator: ${getOperatorName(currentOperator)}
                    </div>
                </div>
                
                <!-- Komponen Proposisi -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="result-card" style="padding: 1rem; background: rgba(0, 245, 255, 0.05);">
                            <div style="color: var(--primary-neon); font-weight: 600; margin-bottom: 0.5rem;">
                                <i class="mdi mdi-alpha-p-circle me-1"></i>
                                Proposisi P
                            </div>
                            <div style="color: #fff; font-size: 0.9rem; margin-bottom: 0.5rem; font-style: italic;">
                                "${document.getElementById('inputP').value}"
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="result-indicator ${pValue === 'BENAR' ? 'result-true' : 'result-false'}" style="font-size: 0.9rem;">
                                    ${pValue}
                                </div>
                                ${pAnalysis ? `<span class="badge" style="font-size: 0.7rem; background: ${getPropositionColor(pAnalysis)};">
                                    ${pAnalysis.propositionType || 'PROPOSISI'}
                                </span>` : ''}
                            </div>
                        </div>
                    </div>
                    
                    ${currentOperator !== 'not' ? `
                    <div class="col-md-6">
                        <div class="result-card" style="padding: 1rem; background: rgba(0, 245, 255, 0.05);">
                            <div style="color: var(--primary-neon); font-weight: 600; margin-bottom: 0.5rem;">
                                <i class="mdi mdi-alpha-q-circle me-1"></i>
                                Proposisi Q
                            </div>
                            <div style="color: #fff; font-size: 0.9rem; margin-bottom: 0.5rem; font-style: italic;">
                                "${document.getElementById('inputQ').value}"
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="result-indicator ${qValue === 'BENAR' ? 'result-true' : 'result-false'}" style="font-size: 0.9rem;">
                                    ${qValue}
                                </div>
                                ${qAnalysis ? `<span class="badge" style="font-size: 0.7rem; background: ${getPropositionColor(qAnalysis)};">
                                    ${qAnalysis.propositionType || 'PROPOSISI'}
                                </span>` : ''}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
                
                <!-- Hasil Evaluasi -->
                <div class="result-card animate__animated animate__fadeIn" style="animation-delay: 0.2s; background: rgba(${compoundValue ? '0,255,136' : '255,0,102'}, 0.1); border: 2px solid ${truthColor}40;">
                    <div class="d-flex align-items-center mb-3">
                        <div style="font-size: 1.1rem; color: ${truthColor};">
                            <i class="mdi ${truthIcon} me-2"></i>
                            <strong>HASIL: ${truthText}</strong>
                        </div>
                    </div>
                    
                    <div style="padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 10px; margin-top: 1rem;">
                        <div style="color: var(--accent-cyan); font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Analisis Logika
                        </div>
                        <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.95rem;">
                            ${explanation}
                        </div>
                    </div>
                </div>
                
                <!-- Tabel Kebenaran Mini -->
                <div class="analysis-section animate__animated animate__fadeIn" style="animation-delay: 0.3s; margin-top: 1.5rem;">
                    <div style="color: var(--primary-neon); font-weight: 600; margin-bottom: 0.5rem;">
                        <i class="mdi mdi-table me-2"></i>
                        Tabel Kebenaran Operator ${getOperatorName(currentOperator)}
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">
                        ${generateTruthTableSnippet(currentOperator)}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    compoundResultSection.innerHTML = html;
    compoundResultSection.style.display = 'block';
    resultSection.style.display = 'block';
    
    setTimeout(() => {
        resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
    
    addToHistory(compoundSentence, {
        isStatement: true,
        isProposition: true,
        truthValue: truthText,
        confidence: 100,
        propositionType: compoundType,
        isTautology: compoundType === 'TAUTOLOGI',
        isContradiction: compoundType === 'KONTRADIKSI',
        isContingent: compoundType === 'KONTINGEN'
    });
}

function getPropositionColor(analysis) {
    if (analysis.isTautology) return 'linear-gradient(45deg, #00ff88, #00cc66)';
    if (analysis.isContradiction) return 'linear-gradient(45deg, #ff0066, #cc0052)';
    if (analysis.isContingent) return 'linear-gradient(45deg, #ffcc00, #cc9900)';
    return 'var(--primary-neon)';
}

function generateTruthTableSnippet(operator) {
    const tables = {
        'and': 'P ∧ Q benar hanya jika P benar DAN Q benar',
        'or': 'P ∨ Q salah hanya jika P salah ATAU Q salah',
        'not': '¬P membalik nilai kebenaran P',
        'imply': 'P → Q salah hanya jika P benar DAN Q salah',
        'biconditional': 'P ↔ Q benar jika P dan Q memiliki nilai yang sama'
    };
    return tables[operator] || 'Tabel kebenaran untuk operator ini.';
}

function generateLogicConclusion(result, originalText) {
    let conclusion = '';
    
    if (result.isTautology) {
        conclusion += `"${originalText}" adalah <strong>tautologi</strong>. `;
        conclusion += `Ini berarti pernyataan ini <strong>selalu benar</strong> dalam semua kondisi dan interpretasi. `;
        conclusion += `Tautologi tidak bergantung pada fakta dunia nyata, tetapi pada struktur logika itu sendiri. `;
        conclusion += `Contoh tautologi klasik: "P ∨ ¬P" (hukum excluded middle).`;
    } else if (result.isContradiction) {
        conclusion += `"${originalText}" adalah <strong>kontradiksi</strong>. `;
        conclusion += `Ini berarti pernyataan ini <strong>selalu salah</strong> dalam semua kondisi dan interpretasi. `;
        conclusion += `Kontradiksi mengandung unsur yang saling meniadakan secara logis. `;
        conclusion += `Contoh kontradiksi klasik: "P ∧ ¬P" (hukum kontradiksi).`;
    } else if (result.isContingent) {
        conclusion += `"${originalText}" adalah <strong>kontingen</strong>. `;
        conclusion += `Ini berarti pernyataan ini <strong>bisa benar atau salah</strong> tergantung pada kondisi dunia nyata. `;
        conclusion += `Kebenarannya bergantung pada fakta, konteks, atau interpretasi tertentu. `;
        conclusion += `Sebagian besar pernyataan dalam kehidupan sehari-hari adalah kontingen.`;
    }
    
    conclusion += `\n\n<b>Keyakinan analisis:</b> ${result.confidence}%`;
    
    return conclusion;
}

// =============================================================================
// FUNGSI EVENT HANDLERS
// =============================================================================

function handleSingleAnalysis() {
    const inputText = document.getElementById('inputText').value.trim();
    
    if (!inputText) {
        showNotification('Masukkan pernyataan terlebih dahulu!', 'warning');
        return;
    }
    
    // Proses analisis langsung
    const result = analyzeProposition(inputText);
    displaySingleResult(result, inputText);
    
    // Update statistik
    updateStats();
}

function handleCompoundAnalysis() {
    const p = document.getElementById('inputP').value.trim();
    const q = document.getElementById('inputQ').value.trim();
    
    if (!p) {
        showNotification('Proposisi P harus diisi!', 'warning');
        return;
    }
    
    if (currentOperator !== 'not' && !q) {
        showNotification('Proposisi Q harus diisi untuk operator ini!', 'warning');
        return;
    }
    
    // Analisis masing-masing proposisi
    const pAnalysis = analyzeProposition(p);
    const qAnalysis = q ? analyzeProposition(q) : null;
    
    // Bangun kalimat majemuk
    let compoundSentence = '';
    let compoundValue = false;
    let explanation = '';
    const pValue = truthP ? 'BENAR' : 'SALAH';
    const qValue = truthQ ? 'BENAR' : 'SALAH';
    
    switch(currentOperator) {
        case 'and':
            compoundSentence = `${p} DAN ${q}`;
            compoundValue = truthP && truthQ;
            explanation = `Konjungsi (∧): P = ${pValue}, Q = ${qValue}. `;
            explanation += `Hasil: ${compoundValue ? 'BENAR (tautologi jika P dan Q tautologi)' : 'SALAH (kontradiksi jika P atau Q kontradiksi)'}`;
            break;
        case 'or':
            compoundSentence = `${p} ATAU ${q}`;
            compoundValue = truthP || truthQ;
            explanation = `Disjungsi (∨): P = ${pValue}, Q = ${qValue}. `;
            explanation += `Hasil: ${compoundValue ? 'BENAR' : 'SALAH (hanya jika P dan Q keduanya salah)'}`;
            break;
        case 'not':
            compoundSentence = `BUKAN (${p})`;
            compoundValue = !truthP;
            explanation = `Negasi (¬): P = ${pValue}. `;
            explanation += `Hasil: ${compoundValue ? 'BENAR' : 'SALAH'} (membalik nilai P)`;
            break;
        case 'imply':
            compoundSentence = `JIKA ${p} MAKA ${q}`;
            compoundValue = !truthP || truthQ;
            explanation = `Implikasi (→): P = ${pValue}, Q = ${qValue}. `;
            explanation += `Hasil: ${compoundValue ? 'BENAR' : 'SALAH (hanya jika P benar dan Q salah)'}`;
            break;
        case 'biconditional':
            compoundSentence = `${p} JIKA DAN HANYA JIKA ${q}`;
            compoundValue = truthP === truthQ;
            explanation = `Biimplikasi (↔): P = ${pValue}, Q = ${qValue}. `;
            explanation += `Hasil: ${compoundValue ? 'BENAR (tautologi jika P dan Q sama)' : 'SALAH (kontradiksi jika P dan Q berbeda)'}`;
            break;
    }
    
    // Tampilkan hasil majemuk
    displayCompoundResult(compoundSentence, compoundValue, explanation, pValue, qValue, pAnalysis, qAnalysis);
    
    // Update statistik
    updateStats();
}

// =============================================================================
// FUNGSI UTILITAS
// =============================================================================

function showNotification(message, type = 'info') {
    // Cek apakah ada container notifikasi
    let notificationContainer = document.getElementById('notificationContainer');
    
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notificationContainer';
        notificationContainer.style.cssText = `
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        `;
        document.body.appendChild(notificationContainer);
    }
    
    const notification = document.createElement('div');
    notification.className = `glass-card animate__animated animate__fadeInRight`;
    notification.style.cssText = `
        margin-bottom: 10px;
        border-left: 4px solid ${type === 'info' ? 'var(--primary-neon)' : 
                               type === 'success' ? '#00ff88' : 
                               type === 'warning' ? '#ffcc00' : '#ff0066'};
    `;
    
    const icon = type === 'info' ? 'mdi-information-outline' :
                 type === 'success' ? 'mdi-check-circle-outline' :
                 type === 'warning' ? 'mdi-alert-outline' : 'mdi-close-circle-outline';
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="mdi ${icon}" style="font-size: 1.5rem; margin-right: 10px; 
                color: ${type === 'info' ? 'var(--primary-neon)' : 
                        type === 'success' ? '#00ff88' : 
                        type === 'warning' ? '#ffcc00' : '#ff0066'};"></i>
            <div>
                <div style="font-weight: 600;">${message}</div>
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto" 
                    onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    notificationContainer.appendChild(notification);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('animate__fadeInRight');
            notification.classList.add('animate__fadeOutRight');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }
    }, 5000);
}

function addToHistory(expression, data) {
    const historyItem = {
        expression: expression,
        result: data,
        timestamp: new Date().toISOString()
    };
    
    analysisHistory.unshift(historyItem);
    
    // Simpan ke localStorage (maksimal 50 item)
    if (analysisHistory.length > 50) {
        analysisHistory = analysisHistory.slice(0, 50);
    }
    
    localStorage.setItem('analysisHistory', JSON.stringify(analysisHistory));
}

function updateStats() {
    const totalAnalyses = analysisHistory.length;
    const validStatements = analysisHistory.filter(item => item.result.isProposition).length;
    const avgConfidence = analysisHistory.length > 0 ? 
        Math.round(analysisHistory.reduce((sum, item) => sum + item.result.confidence, 0) / analysisHistory.length) : 0;
    const propositionCount = analysisHistory.filter(item => 
        item.result.isTautology || item.result.isContradiction || item.result.isContingent).length;
    
    // Update elemen statistik
    document.getElementById('totalAnalyses').textContent = totalAnalyses;
    document.getElementById('validStatements').textContent = validStatements;
    document.getElementById('avgConfidence').textContent = avgConfidence + '%';
    document.getElementById('propositionCount').textContent = propositionCount;
}

// =============================================================================
// FUNGSI INISIALISASI
// =============================================================================

function switchMode(mode) {
    currentMode = mode;
    
    // Update tombol mode
    const modeBtns = document.querySelectorAll('.mode-btn');
    const modeSections = document.querySelectorAll('.mode-section');
    
    modeBtns.forEach(btn => {
        if (btn.dataset.mode === mode) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Tampilkan/sembunyikan section yang sesuai
    modeSections.forEach(section => {
        if (section.id === mode + 'Mode' || section.id === 'single' + 'Mode') {
            section.style.display = 'block';
            section.classList.add('active');
        } else {
            section.style.display = 'none';
            section.classList.remove('active');
        }
    });
    
    // Untuk result sections
    const singleResultSection = document.getElementById('singleResultSection');
    const compoundResultSection = document.getElementById('compoundResultSection');
    
    if (mode === 'single') {
        if (singleResultSection) singleResultSection.style.display = 'block';
        if (compoundResultSection) compoundResultSection.style.display = 'none';
    } else {
        if (singleResultSection) singleResultSection.style.display = 'none';
        if (compoundResultSection) compoundResultSection.style.display = 'block';
    }
    
    showNotification(`Mode ${mode === 'single' ? 'Tunggal' : 'Majemuk'} diaktifkan`, 'info');
}

function setExample(text, mode) {
    if (mode === 'single') {
        document.getElementById('inputText').value = text;
        // Switch ke mode single jika belum
        if (currentMode !== 'single') {
            switchMode('single');
        }
        setTimeout(() => handleSingleAnalysis(), 100);
    }
}

function clearSingleInput() {
    document.getElementById('inputText').value = '';
    document.getElementById('singleResultSection').innerHTML = '';
    document.getElementById('resultSection').style.display = 'none';
}

function clearCompoundInput() {
    document.getElementById('inputP').value = '';
    document.getElementById('inputQ').value = '';
    document.getElementById('compoundResultSection').innerHTML = '';
    document.getElementById('resultSection').style.display = 'none';
    setOperator('and');
    updateTruthValue('p', true);
    updateTruthValue('q', true);
}

function initParticles() {
    if (typeof particlesJS !== 'undefined') {
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#00f5ff" },
                shape: { type: "circle" },
                opacity: { value: 0.5, random: true },
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
                    speed: 2,
                    direction: "none",
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" }
                }
            },
            retina_detect: true
        });
    }
}

function initApplication() {
    console.log('🚀 Menginisialisasi aplikasi analisis logika...');
    
    // Setup event listeners
    document.getElementById('checkBtn')?.addEventListener('click', handleSingleAnalysis);
    document.getElementById('checkCompoundBtn')?.addEventListener('click', handleCompoundAnalysis);
    
    // Clear buttons
    document.getElementById('clearBtn')?.addEventListener('click', clearSingleInput);
    document.getElementById('clearCompoundBtn')?.addEventListener('click', clearCompoundInput);
    
    // Mode selector
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchMode(this.dataset.mode);
        });
    });
    
    // Operator buttons
    document.querySelectorAll('.operator-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            setOperator(this.dataset.operator);
        });
    });
    
    // Truth selector buttons
    document.querySelectorAll('.truth-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const prop = this.dataset.prop;
            const value = this.dataset.value === 'true';
            updateTruthValue(prop, value);
        });
    });
    
    // Enter key untuk textarea
    document.getElementById('inputText')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            handleSingleAnalysis();
        }
    });
    
    // Load history dari localStorage
    const savedHistory = localStorage.getItem('analysisHistory');
    if (savedHistory) {
        analysisHistory = JSON.parse(savedHistory);
        updateStats();
    }
    
    // Setup contoh cepat
    window.setExample = setExample;
    window.setCompoundExample = setCompoundExample;
    
    // Inisialisasi particles
    initParticles();
    
    console.log('✅ Aplikasi analisis logika siap!');
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', initApplication);