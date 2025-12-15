<?php

namespace App\Services;

class StepByStepService
{
    private $parser;
    
    public function __construct()
    {
        $this->parser = new PropositionParserService();
    }
    
    public function evaluateWithSteps(string $expression, ?array $customValues = null): array
    {
        $parsed = $this->parser->parse($expression);
        $variables = $parsed['variables'];
        $ast = $parsed['ast'];
        
        // Jika ada custom values, gunakan itu
        if ($customValues && !empty($customValues)) {
            return $this->evaluateSingleRow($ast, $variables, $customValues, $expression);
        }
        
        // Jika tidak, buat tabel kebenaran dengan langkah-langkah
        return $this->generateTruthTableWithSteps($ast, $variables, $expression);
    }
    
    private function evaluateSingleRow(array $ast, array $variables, array $values, string $originalExpression): array
    {
        $steps = [];
        $stepNumber = 1;
        
        // Langkah 1: Tampilkan nilai variabel
        foreach ($variables as $var) {
            $value = $values[$var] ?? false;
            $steps[] = [
                'step' => $stepNumber++,
                'expression' => $var,
                'result' => $value ? 'True' : 'False',
                'type' => 'variable_assignment',
                'details' => "Nilai variabel {$var} = " . ($value ? 'True' : 'False')
            ];
        }
        
        // Langkah 2: Evaluasi AST dengan langkah-langkah
        $finalResult = $this->evaluateASTWithSteps($ast, $values, $steps, $stepNumber);
        
        return [
            'type' => 'single_evaluation',
            'original_expression' => $originalExpression,
            'variables' => $variables,
            'custom_values' => $values,
            'steps' => $steps,
            'final_result' => $finalResult,
            'final_result_bool' => $finalResult === 'True'
        ];
    }
    
    private function evaluateASTWithSteps(array $node, array $values, array &$steps, int &$stepNumber, int $depth = 0): string
    {
        if ($node['type'] === 'variable') {
            $value = $values[$node['value']] ?? false;
            return $value ? 'True' : 'False';
        }
        
        $operator = $node['operator'];
        $operands = $node['operands'];
        
        if ($operator === '¬') {
            // Negasi
            $operandResult = $this->evaluateASTWithSteps($operands[0], $values, $steps, $stepNumber, $depth + 1);
            $operandBool = $operandResult === 'True';
            $result = !$operandBool;
            
            $steps[] = [
                'step' => $stepNumber++,
                'expression' => '¬' . $this->nodeToString($operands[0]),
                'result' => $result ? 'True' : 'False',
                'type' => 'negation',
                'details' => "Negasi: ¬({$operandResult}) = " . ($result ? 'True' : 'False')
            ];
            
            return $result ? 'True' : 'False';
        } else {
            // Binary operators
            $leftResult = $this->evaluateASTWithSteps($operands[0], $values, $steps, $stepNumber, $depth + 1);
            $rightResult = $this->evaluateASTWithSteps($operands[1], $values, $steps, $stepNumber, $depth + 1);
            
            $leftBool = $leftResult === 'True';
            $rightBool = $rightResult === 'True';
            
            switch ($operator) {
                case '∧':
                    $result = $leftBool && $rightBool;
                    $operatorName = 'Konjungsi (AND)';
                    $details = "{$leftResult} ∧ {$rightResult} = " . ($result ? 'True' : 'False');
                    break;
                case '∨':
                    $result = $leftBool || $rightBool;
                    $operatorName = 'Disjungsi (OR)';
                    $details = "{$leftResult} ∨ {$rightResult} = " . ($result ? 'True' : 'False');
                    break;
                case '→':
                    $result = !$leftBool || $rightBool;
                    $operatorName = 'Implikasi';
                    $details = "{$leftResult} → {$rightResult} = " . ($result ? 'True' : 'False') . 
                               " (¬{$leftResult} ∨ {$rightResult})";
                    break;
                case '↔':
                    $result = $leftBool === $rightBool;
                    $operatorName = 'Bi-implikasi';
                    $details = "{$leftResult} ↔ {$rightResult} = " . ($result ? 'True' : 'False');
                    break;
                case '⊕':
                    $result = $leftBool !== $rightBool;
                    $operatorName = 'XOR';
                    $details = "{$leftResult} ⊕ {$rightResult} = " . ($result ? 'True' : 'False');
                    break;
                default:
                    $result = false;
                    $details = "Operator tidak dikenali";
            }
            
            $steps[] = [
                'step' => $stepNumber++,
                'expression' => $this->nodeToString($operands[0]) . " {$operator} " . $this->nodeToString($operands[1]),
                'result' => $result ? 'True' : 'False',
                'type' => strtolower($operator),
                'details' => "{$operatorName}: {$details}"
            ];
            
            return $result ? 'True' : 'False';
        }
    }
    
    private function nodeToString(array $node): string
    {
        if ($node['type'] === 'variable') {
            return $node['value'];
        }
        
        $operator = $node['operator'];
        $operands = $node['operands'];
        
        if ($operator === '¬') {
            return '¬' . $this->nodeToString($operands[0]);
        } else {
            return '(' . $this->nodeToString($operands[0]) . ' ' . $operator . ' ' . $this->nodeToString($operands[1]) . ')';
        }
    }
    
    private function generateTruthTableWithSteps(array $ast, array $variables, string $expression): array
    {
        $totalRows = pow(2, count($variables));
        $table = [];
        $allSteps = [];
        
        for ($i = 0; $i < $totalRows; $i++) {
            $row = [];
            $values = [];
            $rowSteps = [];
            $stepNumber = 1;
            
            // Assign binary values to variables
            foreach ($variables as $index => $var) {
                $value = ($i >> (count($variables) - $index - 1)) & 1;
                $row[$var] = (bool)$value;
                $values[$var] = (bool)$value;
                
                $rowSteps[] = [
                    'step' => $stepNumber++,
                    'expression' => $var,
                    'result' => $value ? 'True' : 'False',
                    'type' => 'variable_assignment'
                ];
            }
            
            // Evaluate expression with steps
            $result = $this->evaluateASTWithSteps($ast, $values, $rowSteps, $stepNumber);
            
            $row['result'] = $result === 'True';
            $row['steps'] = $rowSteps;
            $table[] = $row;
            
            $allSteps[] = [
                'row' => $i + 1,
                'values' => $values,
                'result' => $result,
                'steps' => $rowSteps
            ];
        }
        
        return [
            'type' => 'truth_table',
            'original_expression' => $expression,
            'variables' => $variables,
            'table' => $table,
            'all_steps' => $allSteps,
            'headers' => array_merge($variables, ['Result']),
            'is_tautology' => array_reduce($table, fn($carry, $item) => $carry && $item['result'], true),
            'is_contradiction' => array_reduce($table, fn($carry, $item) => $carry && !$item['result'], true)
        ];
    }
}