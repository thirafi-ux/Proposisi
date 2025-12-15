<?php

namespace App\Utilities;

class PropositionValidator
{
    private array $operators = ['¬', '∧', '∨', '→', '↔', '⊕'];
    
    public function validate(string $expression): array
    {
        $errors = [];
        
        if (empty($expression)) {
            $errors[] = 'Ekspresi tidak boleh kosong';
            return ['valid' => false, 'errors' => $errors];
        }
        
        if (strlen($expression) > 1000) {
            $errors[] = 'Ekspresi terlalu panjang (maksimal 1000 karakter)';
        }
        
        $balance = 0;
        $tokens = $this->tokenize($expression);
        
        foreach ($tokens as $index => $token) {
            if ($token === '(') {
                $balance++;
            } elseif ($token === ')') {
                $balance--;
                if ($balance < 0) {
                    $errors[] = 'Tanda kurung tidak seimbang';
                    break;
                }
            }
            
            if ($this->isOperator($token) && $index === 0 && $token !== '¬') {
                $errors[] = 'Ekspresi tidak boleh diawali dengan operator biner';
            }
        }
        
        if ($balance !== 0) {
            $errors[] = 'Tanda kurung tidak seimbang';
        }
        
        if (count($this->extractVariables($tokens)) > 8) {
            $errors[] = 'Terlalu banyak variabel (maksimal 8)';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    private function tokenize(string $expression): array
    {
        $pattern = '/([a-zA-Z][a-zA-Z0-9]*|¬|∧|∨|→|↔|⊕|\(|\))/';
        preg_match_all($pattern, $expression, $matches);
        return $matches[0] ?? [];
    }
    
    private function isOperator(string $token): bool
    {
        return in_array($token, $this->operators);
    }
    
    private function extractVariables(array $tokens): array
    {
        $variables = [];
        foreach ($tokens as $token) {
            if (preg_match('/^[a-zA-Z]/', $token) && !in_array($token, $variables)) {
                $variables[] = $token;
            }
        }
        return $variables;
    }
}