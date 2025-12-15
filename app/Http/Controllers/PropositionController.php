<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PropositionParserService;
use App\Services\TruthTableService;
use App\Services\TreeVisualizerService;
use App\Utilities\PropositionValidator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PropositionController extends Controller
{
    protected $parser;
    protected $validator;
    protected $truthTableService;
    protected $treeVisualizer;

    public function __construct()
    {
        $this->parser = new PropositionParserService();
        $this->validator = new PropositionValidator();
        $this->truthTableService = new TruthTableService();
        $this->treeVisualizer = new TreeVisualizerService();
    }

    /**
     * Menampilkan halaman kalkulator
     */
    public function index()
    {
        return view('calculator.index');
    }

    /**
     * Melakukan perhitungan
     */


    public function propositionCalculator()
    {
        // Data statistik untuk ditampilkan di halaman
        $stats = [
            'total_analyses' => 1250,
            'valid_statements' => 1100,
            'avg_confidence' => '94.5',
            'proposition_count' => 850
        ];
        
        return view('calculator.proposisi', compact('stats'));
    }

     public function getStats()
    {
        // Data statistik statis (bisa diganti dengan query database)
        $stats = [
            'total_calculations' => 1250,
            'total_users' => 850,
            'total_expressions' => 5700,
            'accuracy_rate' => '99.8',
            'recent_activity' => [
                [
                    'expression' => 'p ∧ (q → r)',
                    'time_ago' => '5 menit yang lalu',
                    'type' => 'Tabel Kebenaran'
                ],
                [
                    'expression' => '¬(p ∨ q) ↔ (¬p ∧ ¬q)',
                    'time_ago' => '12 menit yang lalu',
                    'type' => 'Hukum De Morgan'
                ],
                [
                    'expression' => '(p → q) ∧ p → q',
                    'time_ago' => '25 menit yang lalu',
                    'type' => 'Modus Ponens'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Mendapatkan aktivitas terbaru
     */
    public function recentActivity()
    {
        $activities = [
            [
                'expression' => 'p ∧ (q → r)',
                'time_ago' => '5 menit yang lalu',
                'type' => 'Tabel Kebenaran'
            ],
            [
                'expression' => '¬(p ∨ q) ↔ (¬p ∧ ¬q)',
                'time_ago' => '12 menit yang lalu',
                'type' => 'Hukum De Morgan'
            ],
            [
                'expression' => '(p → q) ∧ p → q',
                'time_ago' => '25 menit yang lalu',
                'type' => 'Modus Ponens'
            ],
            [
                'expression' => 'p ∨ ¬p',
                'time_ago' => '40 menit yang lalu',
                'type' => 'Tautologi'
            ],
            [
                'expression' => '(p → q) ↔ (¬q → ¬p)',
                'time_ago' => '1 jam yang lalu',
                'type' => 'Kontrapositif'
            ],
            [
                'expression' => 'p ∧ (q ∨ r) ↔ (p ∧ q) ∨ (p ∧ r)',
                'time_ago' => '2 jam yang lalu',
                'type' => 'Distributif'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }
    
    public function calculate(Request $request)
    {
        $request->validate([
            'expression' => 'required|string',
            'mode' => 'in:truth_table,step_by_step,custom_values',
            'custom_values' => 'array'
        ]);
        
        $expression = $request->input('expression');
        $mode = $request->input('mode', 'truth_table');
        $customValues = $request->input('custom_values', []);
        
        try {
            // Parse expression untuk mendapatkan AST (digunakan untuk semua mode)
            $parsed = $this->parser->parse($expression);
            $ast = $parsed['ast'];
            $variables = $parsed['variables'];
            
            // Generate tree data dari AST (untuk semua mode)
            $treeData = $this->treeVisualizer->generate($ast);
            
            if ($mode === 'custom_values') {
                // Fill missing variables with false
                foreach ($variables as $var) {
                    if (!isset($customValues[$var])) {
                        $customValues[$var] = false;
                    }
                }
                
                $result = $this->truthTableService->evaluateWithCustomValues($ast, $customValues);
                
                // Tambahkan tree data ke result
                $result['tree_data'] = $treeData;
                
                return response()->json([
                    'success' => true,
                    'type' => 'custom_evaluation',
                    'data' => $result
                ]);
            } else if ($mode === 'step_by_step') {
                $result = $this->truthTableService->generateWithSteps($ast, $variables);
                
                // Tambahkan tree data ke result
                $result['tree_data'] = $treeData;
                
                return response()->json([
                    'success' => true,
                    'type' => 'step_by_step',
                    'data' => $result
                ]);
            } else {
                // Gunakan method baru untuk truth table dengan sub-ekspresi
                $result = $this->truthTableService->generateDetailedTable($expression);
                
                // Tambahkan tree data ke result
                $result['tree_data'] = $treeData;
                
                return response()->json([
                    'success' => true,
                    'type' => 'truth_table',
                    'data' => $result
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Parsing ekspresi
     */
    public function parseExpression(Request $request)
    {
        try {
            $validated = $request->validate([
                'expression' => 'required|string'
            ]);

            $validation = $this->validator->validate($validated['expression']);
            
            if ($validation['valid']) {
                $parsed = $this->parser->parse($validated['expression']);
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'valid' => true,
                        'variables' => $parsed['variables'] ?? [],
                        'structure' => $parsed['structure'] ?? null
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Ekspresi tidak valid',
                    'errors' => $validation['errors']
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memparsing ekspresi'
            ], 500);
        }
    }

    /**
     * Validasi ekspresi
     */
    public function validateExpression(Request $request)
    {
        try {
            $validated = $request->validate([
                'expression' => 'required|string'
            ]);

            $result = $this->validator->validate($validated['expression']);
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal validasi ekspresi'
            ], 500);
        }
    }

    /**
     * Mendapatkan history
     */
    public function history(Request $request)
    {
        try {
            $history = DB::table('calculation_histories')
                ->where(function($query) {
                    if (auth()->check()) {
                        $query->where('user_id', auth()->id());
                    } else {
                        $query->whereNull('user_id');
                    }
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'expression' => $item->expression,
                        'created_at' => $item->created_at,
                        'mode' => $item->mode,
                        'variables' => json_decode($item->variables, true) ?? [],
                        'is_tautology' => false,
                        'is_contradiction' => false
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat history'
            ], 500);
        }
    }

    /**
     * Mendapatkan hukum logika
     */
    public function getLogicLaws()
    {
        $laws = [
            [
                'name' => 'Hukum Identitas',
                'formula' => 'p ∧ T ↔ p',
                'example' => 'p AND True adalah p'
            ],
            [
                'name' => 'Hukum Negasi',
                'formula' => 'p ∨ ¬p ↔ T',
                'example' => 'p OR NOT p adalah True'
            ],
            [
                'name' => 'Hukum De Morgan',
                'formula' => '¬(p ∧ q) ↔ (¬p ∨ ¬q)',
                'example' => 'NOT (p AND q) = (NOT p) OR (NOT q)'
            ],
            [
                'name' => 'Hukum Distributif',
                'formula' => 'p ∧ (q ∨ r) ↔ (p ∧ q) ∨ (p ∧ r)',
                'example' => 'p AND (q OR r) = (p AND q) OR (p AND r)'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $laws
        ]);
    }

    /**
     * Mendapatkan contoh proposisi
     */
    public function getPropositionExamples()
    {
        $examples = [
            [
                'name' => 'Konjungsi Sederhana',
                'expression' => 'p ∧ q',
                'description' => 'Dua proposisi yang harus keduanya benar'
            ],
            [
                'name' => 'Disjungsi',
                'expression' => 'p ∨ q',
                'description' => 'Setidaknya satu dari dua proposisi benar'
            ],
            [
                'name' => 'Implikasi',
                'expression' => 'p → q',
                'description' => 'Jika p maka q'
            ],
            [
                'name' => 'Tautologi',
                'expression' => 'p ∨ ¬p',
                'description' => 'Selalu benar untuk semua nilai'
            ],
            [
                'name' => 'Kontradiksi',
                'expression' => 'p ∧ ¬p',
                'description' => 'Selalu salah untuk semua nilai'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $examples
        ]);
    }

    /**
     * Membersihkan cache
     */
    public function clearCache(Request $request)
    {
        Cache::flush();
        
        return response()->json([
            'success' => true,
            'message' => 'Cache berhasil dibersihkan'
        ]);
    }

    /**
     * Test parser untuk debugging
     */
    public function testParser()
    {
        try {
            $expressions = ['p ∧ q', '(p → q) ∧ (¬p ∧ q)', '¬(p ∧ q) ↔ (¬p ∨ ¬q)', 'p → (q → p)'];
            $results = [];
            
            foreach ($expressions as $expression) {
                $validation = $this->validator->validate($expression);
                $parsed = $this->parser->parse($expression);
                
                $results[] = [
                    'expression' => $expression,
                    'validation' => $validation,
                    'parsed' => $parsed
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test truth table generation
     */
    public function testTruthTable(Request $request)
    {
        try {
            $expression = $request->get('expression', 'p ∧ q');
            
            $parsed = $this->parser->parse($expression);
            $result = $this->truthTableService->generate($parsed['ast'], $parsed['variables']);
            
            // Also test with steps
            $resultWithSteps = $this->truthTableService->generateWithSteps($parsed['ast'], $parsed['variables']);
            
            // Test custom values evaluation
            $customValues = ['p' => true, 'q' => false];
            $customResult = $this->truthTableService->evaluateWithCustomValues($parsed['ast'], $customValues);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'expression' => $expression,
                    'parsed' => $parsed,
                    'truth_table' => $result,
                    'truth_table_with_steps' => $resultWithSteps,
                    'custom_evaluation' => $customResult,
                    'is_tautology' => $this->truthTableService->isTautology($parsed['ast'], $parsed['variables']),
                    'is_contradiction' => $this->truthTableService->isContradiction($parsed['ast'], $parsed['variables'])
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Mendapatkan contoh tabel kebenaran
     */
    public function getTruthTableExample(Request $request)
    {
        $example = $request->get('example', 'basic');
        
        $examples = [
            'basic' => [
                'expression' => 'p ∧ q',
                'description' => 'Konjungsi (DAN) - p dan q harus keduanya benar',
                'variables' => ['p', 'q'],
                'table' => [
                    ['p' => true, 'q' => true, 'result' => true],
                    ['p' => true, 'q' => false, 'result' => false],
                    ['p' => false, 'q' => true, 'result' => false],
                    ['p' => false, 'q' => false, 'result' => false]
                ]
            ],
            'implication' => [
                'expression' => 'p → q',
                'description' => 'Implikasi (JIKA-MAKA) - Hanya salah jika p benar dan q salah',
                'variables' => ['p', 'q'],
                'table' => [
                    ['p' => true, 'q' => true, 'result' => true],
                    ['p' => true, 'q' => false, 'result' => false],
                    ['p' => false, 'q' => true, 'result' => true],
                    ['p' => false, 'q' => false, 'result' => true]
                ]
            ],
            'negation' => [
                'expression' => '¬p',
                'description' => 'Negasi (BUKAN) - Membalikkan nilai kebenaran',
                'variables' => ['p'],
                'table' => [
                    ['p' => true, 'result' => false],
                    ['p' => false, 'result' => true]
                ]
            ]
        ];
        
        $selectedExample = $examples[$example] ?? $examples['basic'];
        
        return response()->json([
            'success' => true,
            'data' => $selectedExample
        ]);
    }
}