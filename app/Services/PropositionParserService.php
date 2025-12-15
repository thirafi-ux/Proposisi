<?php

namespace App\Services;

class PropositionParserService
{
    private $operators = ['¬', '∧', '∨', '→', '↔', '⊕'];
    private $precedence = ['¬' => 4, '∧' => 3, '∨' => 2, '⊕' => 2, '→' => 1, '↔' => 1];
    
    public function parse(string $expression): array
    {
        $expression = $this->normalizeExpression($expression);
        $tokens = $this->tokenize($expression);
        $this->validateSyntax($tokens);
        $rpn = $this->shuntingYard($tokens);
        $variables = $this->extractVariables($tokens);
        
        return [
            'tokens' => $tokens,
            'rpn' => $rpn,
            'variables' => $variables,
            'ast' => $this->buildAST($rpn),
            'normalized' => $expression
        ];
    }
    
    private function normalizeExpression(string $expression): string
    {
        $replacements = [
            '~' => '¬',
            '!' => '¬',
            'not' => '¬',
            'NOT' => '¬',
            '&&' => '∧',
            '&' => '∧',
            'and' => '∧',
            'AND' => '∧',
            '||' => '∨',
            '|' => '∨',
            'or' => '∨',
            'OR' => '∨',
            '->' => '→',
            '=>' => '→',
            'implies' => '→',
            'IMPLIES' => '→',
            '<->' => '↔',
            '<=>' => '↔',
            'iff' => '↔',
            'IFF' => '↔',
            'xor' => '⊕',
            'XOR' => '⊕'
        ];
        
        // Remove all whitespace
        $expression = preg_replace('/\s+/', '', $expression);
        
        // Replace alternative operators
        foreach ($replacements as $search => $replace) {
            $expression = str_replace($search, $replace, $expression);
        }
        
        return $expression;
    }
    
    private function tokenize(string $expression): array
    {
        $pattern = '/([a-zA-Z][a-zA-Z0-9]*|¬|∧|∨|→|↔|⊕|\(|\))/';
        preg_match_all($pattern, $expression, $matches);
        return $matches[0] ?? [];
    }
    
    private function validateSyntax(array $tokens): void
    {
        $balance = 0;
        $previous = null;
        
        foreach ($tokens as $token) {
            if ($token === '(') {
                $balance++;
            } elseif ($token === ')') {
                $balance--;
                if ($balance < 0) {
                    throw new \InvalidArgumentException('Tanda kurung tidak seimbang');
                }
            }
            
            // Check for consecutive operators (excluding negation at the beginning)
            if ($previous !== null && $this->isOperator($previous) && $this->isOperator($token)) {
                // Allow ¬ followed by another operator (like ¬¬p)
                if (!($previous === '¬' && $token === '¬')) {
                    throw new \InvalidArgumentException("Operator berurutan: {$previous} {$token}");
                }
            }
            
            // Check for invalid variable names
            if (preg_match('/^[a-zA-Z]/', $token)) {
                if (strlen($token) > 10) {
                    throw new \InvalidArgumentException("Nama variabel terlalu panjang: {$token}");
                }
            }
            
            $previous = $token; // PASTIKAN INI DI DALAM LOOP
        }
        
        if ($balance !== 0) {
            throw new \InvalidArgumentException('Tanda kurung tidak seimbang');
        }
        
        // Check if expression ends with operator
        if (!empty($tokens) && $this->isOperator(end($tokens)) && end($tokens) !== '¬') {
            throw new \InvalidArgumentException("Ekspresi tidak boleh diakhiri dengan operator: " . end($tokens));
        }
    }
    
    private function shuntingYard(array $tokens): array
    {
        $output = [];
        $stack = [];
        
        foreach ($tokens as $token) {
            if (preg_match('/^[a-zA-Z]/', $token)) {
                $output[] = $token;
            } elseif ($token === '(') {
                $stack[] = $token;
            } elseif ($token === ')') {
                while (!empty($stack) && end($stack) !== '(') {
                    $output[] = array_pop($stack);
                }
                if (empty($stack)) {
                    throw new \InvalidArgumentException('Tanda kurung tidak cocok');
                }
                array_pop($stack); // Remove '('
            } elseif ($this->isOperator($token)) {
                while (!empty($stack) && 
                       end($stack) !== '(' &&
                       isset($this->precedence[end($stack)]) && 
                       $this->precedence[end($stack)] >= $this->precedence[$token]) {
                    $output[] = array_pop($stack);
                }
                $stack[] = $token;
            }
        }
        
        while (!empty($stack)) {
            $operator = array_pop($stack);
            if ($operator === '(' || $operator === ')') {
                throw new \InvalidArgumentException('Tanda kurung tidak cocok');
            }
            $output[] = $operator;
        }
        
        return $output;
    }
    
    private function isOperator(?string $token): bool
    {
        if ($token === null) {
            return false;
        }
        return in_array($token, $this->operators, true);
    }
    
    private function extractVariables(array $tokens): array
    {
        $variables = [];
        foreach ($tokens as $token) {
            if (preg_match('/^[a-zA-Z]/', $token) && !in_array($token, $variables, true)) {
                $variables[] = $token;
            }
        }
        sort($variables);
        return $variables;
    }
    
    private function buildAST(array $rpn): array
    {
        $stack = [];
        
        foreach ($rpn as $token) {
            if (preg_match('/^[a-zA-Z]/', $token)) {
                $stack[] = ['type' => 'variable', 'value' => $token];
            } elseif ($token === '¬') {
                $operand = array_pop($stack);
                if ($operand === null) {
                    throw new \InvalidArgumentException('Operand tidak ditemukan untuk negasi');
                }
                $stack[] = [
                    'type' => 'operator',
                    'operator' => $token,
                    'operands' => [$operand]
                ];
            } else {
                $right = array_pop($stack);
                $left = array_pop($stack);
                
                if ($right === null || $left === null) {
                    throw new \InvalidArgumentException('Operand tidak ditemukan untuk operator: ' . $token);
                }
                
                $stack[] = [
                    'type' => 'operator',
                    'operator' => $token,
                    'operands' => [$left, $right]
                ];
            }
        }
        
        if (count($stack) !== 1) {
            throw new \InvalidArgumentException('Struktur ekspresi tidak valid');
        }
        
        return $stack[0] ?? [];
    }
}