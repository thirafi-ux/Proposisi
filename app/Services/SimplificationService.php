<?php

namespace App\Services;

class SimplificationService
{
    public function simplify(string $expression): array
    {
        return [
            'original' => $expression,
            'dnf' => $this->toDNF($expression),
            'cnf' => $this->toCNF($expression),
            'simplified' => $this->applyLogicLaws($expression)
        ];
    }
    
    private function toDNF(string $expression): string
    {
        return $this->convertNormalForm($expression, 'dnf');
    }
    
    private function toCNF(string $expression): string
    {
        return $this->convertNormalForm($expression, 'cnf');
    }
    
    private function convertNormalForm(string $expression, string $type): string
    {
        $expression = $this->applyLogicLaws($expression);
        
        if ($type === 'dnf') {
            return $expression . ' (DNF)';
        } else {
            return $expression . ' (CNF)';
        }
    }
    
    private function applyLogicLaws(string $expression): string
    {
        $laws = [
            // Identity Laws
            '/p ∨ F/' => 'p',
            '/p ∧ T/' => 'p',
            
            // Domination Laws
            '/p ∨ T/' => 'T',
            '/p ∧ F/' => 'F',
            
            // Idempotent Laws
            '/([a-z]) ∨ \1/' => '$1',
            '/([a-z]) ∧ \1/' => '$1',
            
            // Double Negation
            '/¬¬([a-z])/' => '$1',
            
            // De Morgan's Laws
            '/¬\(([a-z]) ∧ ([a-z])\)/' => '¬$1 ∨ ¬$2',
            '/¬\(([a-z]) ∨ ([a-z])\)/' => '¬$1 ∧ ¬$2',
            
            // Absorption Laws
            '/([a-z]) ∨ \(([a-z]) ∧ ([a-z])\)/' => '$1',
            '/([a-z]) ∧ \(([a-z]) ∨ ([a-z])\)/' => '$1'
        ];
        
        $simplified = $expression;
        do {
            $previous = $simplified;
            foreach ($laws as $pattern => $replacement) {
                $simplified = preg_replace($pattern, $replacement, $simplified);
            }
        } while ($previous !== $simplified);
        
        return $simplified;
    }
}