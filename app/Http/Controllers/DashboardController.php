<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Mendapatkan statistik dashboard
     */
    public function getStats(Request $request)
    {
        try {
            $stats = Cache::remember('dashboard_stats', 3600, function () {
                // Statistik contoh - dapat disesuaikan dengan data real
                $totalCalculations = DB::table('calculation_histories')->count() ?? 1250;
                $totalUsers = DB::table('users')->count() ?? 850;
                
                $uniqueExpressions = DB::table('calculation_histories')
                    ->select('expression')
                    ->distinct()
                    ->count();
                
                return [
                    'total_calculations' => $totalCalculations,
                    'total_users' => $totalUsers,
                    'total_expressions' => $uniqueExpressions ?: 5700,
                    'accuracy_rate' => 99.8,
                    'avg_variables' => 2.8,
                    'response_time' => 0.12,
                    'uptime' => 99.9
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat statistik',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan aktivitas terbaru
     */
    public function recentActivity(Request $request)
    {
        try {
            $activities = Cache::remember('recent_activities', 300, function () {
                $recentCalculations = DB::table('calculation_histories')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                
                if ($recentCalculations->isEmpty()) {
                    return [
                        [
                            'expression' => 'p ∧ q',
                            'time_ago' => '5 menit lalu',
                            'type' => 'konjungsi'
                        ],
                        [
                            'expression' => '(p → q) ∧ (¬p ∧ q)',
                            'time_ago' => '12 menit lalu',
                            'type' => 'implikasi'
                        ],
                        [
                            'expression' => '¬(p ∧ q) ↔ (¬p ∨ ¬q)',
                            'time_ago' => '25 menit lalu',
                            'type' => 'biimplikasi'
                        ],
                        [
                            'expression' => 'p → (q → p)',
                            'time_ago' => '1 jam lalu',
                            'type' => 'tautologi'
                        ],
                        [
                            'expression' => 'p ∧ ¬p',
                            'time_ago' => '2 jam lalu',
                            'type' => 'kontradiksi'
                        ]
                    ];
                }
                
                return $recentCalculations->map(function ($calc) {
                    return [
                        'expression' => $calc->expression,
                        'time_ago' => $this->timeAgo($calc->created_at),
                        'type' => $calc->is_tautology ? 'tautologi' : 
                                 ($calc->is_contradiction ? 'kontradiksi' : 'kontingen')
                    ];
                })->toArray();
            });
            
            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat aktivitas'
            ], 500);
        }
    }

    /**
     * Helper untuk format waktu
     */
    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari lalu';
        return date('d M Y', $time);
    }
}