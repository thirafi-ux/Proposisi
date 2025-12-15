<?php

namespace App\Services;

class TruthTableService
{
    private $parser;
    
    public function __construct()
    {
        $this->parser = new PropositionParserService();
    }

    /**
     * Generate detailed truth table with all sub-expressions
     */
    public function generateDetailedTable($expression)
    {
        // Parse expression to get AST and variables
        $parsed = $this->parser->parse($expression);
        $ast = $parsed['ast'];
        $variables = $parsed['variables'];
        
        // Extract all sub-expressions from AST with proper ordering
        $subExpressions = $this->extractAllSubExpressions($ast);
        
        // Sort sub-expressions by complexity (simple to complex)
        usort($subExpressions, function($a, $b) {
            return $this->getExpressionComplexity($a) - $this->getExpressionComplexity($b);
        });
        
        // Add main expression at the end if not already present
        $mainExpr = $this->astToString($ast);
        if (!in_array($mainExpr, $subExpressions)) {
            $subExpressions[] = $mainExpr;
        }
        
        // Get all expressions to display
        $allExpressions = array_merge($variables, $subExpressions);
        
        $totalRows = pow(2, count($variables));
        $table = [];
        
        // Generate all possible combinations
        for ($i = 0; $i < $totalRows; $i++) {
            $row = [];
            $values = [];
            
            // Assign binary values to variables
            foreach ($variables as $index => $var) {
                $value = ($i >> (count($variables) - $index - 1)) & 1;
                $row[$var] = (bool)$value;
                $values[$var] = (bool)$value;
            }
            
            // Evaluate all sub-expressions
            foreach ($subExpressions as $expr) {
                // Parse the sub-expression
                try {
                    $parsedExpr = $this->parser->parse($expr);
                    $result = $this->evaluateAST($parsedExpr['ast'], $values);
                    $row[$expr] = $result;
                } catch (\Exception $e) {
                    $row[$expr] = false;
                }
            }
            
            $table[] = $row;
        }
        
        // Convert boolean to B/S (Benar/Salah) and format table
        $formattedTable = [];
        foreach ($table as $row) {
            $formattedRow = [];
            foreach ($row as $key => $value) {
                $formattedRow[$key] = $value ? 'B' : 'S';
            }
            $formattedTable[] = $formattedRow;
        }
        
        // Count results for main expression
        $mainExpr = $this->astToString($ast);
        $trueCount = 0;
        foreach ($formattedTable as $row) {
            if (isset($row[$mainExpr]) && $row[$mainExpr] === 'B') {
                $trueCount++;
            }
        }
        
        return [
            'table' => $formattedTable,
            'headers' => $allExpressions,
            'variables' => $variables,
            'sub_expressions' => $subExpressions,
            'is_tautology' => $trueCount === count($formattedTable),
            'is_contradiction' => $trueCount === 0,
            'true_count' => $trueCount,
            'false_count' => count($formattedTable) - $trueCount,
            'row_count' => count($formattedTable),
            'main_expression' => $mainExpr
        ];
    }
    
    /**
     * Extract all sub-expressions from AST in post-order
     */
    private function extractAllSubExpressions($ast, &$expressions = [])
    {
        if ($ast['type'] === 'variable') {
            return $expressions;
        }
        
        // First process operands
        foreach ($ast['operands'] as $operand) {
            $this->extractAllSubExpressions($operand, $expressions);
        }
        
        // Then process current node
        $exprString = $this->astToString($ast);
        
        // Add to expressions if not already present
        if (!in_array($exprString, $expressions)) {
            $expressions[] = $exprString;
        }
        
        return $expressions;
    }
    
    /**
     * Get complexity score of expression (number of operators)
     */
    private function getExpressionComplexity($expression)
    {
        $operators = ['¬', '∧', '∨', '→', '↔', '⊕'];
        $complexity = 0;
        
        foreach ($operators as $op) {
            $complexity += substr_count($expression, $op);
        }
        
        return $complexity;
    }
    
    /**
     * Convert AST to string representation
     */
    private function astToString($ast)
    {
        if ($ast['type'] === 'variable') {
            return $ast['value'];
        }
        
        $operator = $ast['operator'];
        $operands = $ast['operands'];
        
        if ($operator === '¬') {
            return '¬' . $this->astToString($operands[0]);
        }
        
        $left = $this->astToString($operands[0]);
        $right = $this->astToString($operands[1]);
        
        // Remove unnecessary outer parentheses for cleaner display
        return $left . ' ' . $operator . ' ' . $right;
    }
    
    /**
     * Generate basic truth table
     */
    public function generate($ast, $variables)
    {
        // Jika parameter pertama adalah string, parse dulu
        if (is_string($ast)) {
            $parsed = $this->parser->parse($ast);
            $ast = $parsed['ast'];
            $variables = $parsed['variables'];
        }
        
        $totalRows = pow(2, count($variables));
        $table = [];
        
        // Generate all possible combinations
        for ($i = 0; $i < $totalRows; $i++) {
            $row = [];
            $values = [];
            
            // Assign binary values to variables
            foreach ($variables as $index => $var) {
                $value = ($i >> (count($variables) - $index - 1)) & 1;
                $row[$var] = (bool)$value;
                $values[$var] = (bool)$value;
            }
            
            // Evaluate the expression
            $row['Hasil'] = $this->evaluateAST($ast, $values);
            $table[] = $row;
        }
        
        $trueCount = $this->countTrue($table);
        $falseCount = count($table) - $trueCount;
        
        return [
            'table' => $table,
            'headers' => array_merge($variables, ['Hasil']),
            'variables' => $variables,
            'is_tautology' => $trueCount === count($table),
            'is_contradiction' => $trueCount === 0,
            'is_contingent' => $trueCount > 0 && $trueCount < count($table),
            'true_count' => $trueCount,
            'false_count' => $falseCount,
            'truth_percentage' => ($trueCount / count($table)) * 100,
            'row_count' => count($table)
        ];
    }
    
    /**
     * Generate truth table with step-by-step evaluation
     */
    public function generateWithSteps($ast, $variables)
    {
        $tableData = $this->generate($ast, $variables);
        $allSteps = [];
        
        foreach ($tableData['table'] as $rowIndex => $row) {
            list($steps, $result) = $this->evaluateASTWithSteps($ast, $row);
            $allSteps[] = [
                'row' => $rowIndex + 1,
                'values' => $row,
                'result' => $result ? 'True' : 'False',
                'steps' => $steps
            ];
        }
        
        return [
            'table' => $tableData['table'],
            'all_steps' => $allSteps,
            'headers' => $tableData['headers'],
            'properties' => [
                'is_tautology' => $tableData['is_tautology'],
                'is_contradiction' => $tableData['is_contradiction'],
                'true_count' => $tableData['true_count'],
                'truth_percentage' => $tableData['truth_percentage'],
                'variable_count' => count($variables)
            ]
        ];
    }
    
    /**
     * Evaluate expression with custom values
     */
    public function evaluateWithCustomValues($ast, $customValues)
    {
        // Prepare values array for evaluation
        $values = [];
        
        // Extract variables from AST
        $variables = $this->extractVariablesFromAST($ast);
        foreach ($variables as $var) {
            $values[$var] = $customValues[$var] ?? false;
        }
        
        // Evaluate the expression
        list($steps, $result) = $this->evaluateASTWithSteps($ast, $values);
        
        return [
            'steps' => $steps,
            'final_result_text' => $result ? 'True' : 'False',
            'final_result_bool' => $result,
            'values' => $values
        ];
    }
    
    /**
     * Evaluate AST with given values
     */
    private function evaluateAST($ast, $values)
    {
        if ($ast['type'] === 'variable') {
            return $values[$ast['value']] ?? false;
        }
        
        $operator = $ast['operator'];
        $operands = $ast['operands'];
        
        switch ($operator) {
            case '¬':
                return !$this->evaluateAST($operands[0], $values);
            case '∧':
                return $this->evaluateAST($operands[0], $values) && 
                       $this->evaluateAST($operands[1], $values);
            case '∨':
                return $this->evaluateAST($operands[0], $values) || 
                       $this->evaluateAST($operands[1], $values);
            case '→':
                return !$this->evaluateAST($operands[0], $values) || 
                       $this->evaluateAST($operands[1], $values);
            case '↔':
                return $this->evaluateAST($operands[0], $values) === 
                       $this->evaluateAST($operands[1], $values);
            case '⊕':
                return $this->evaluateAST($operands[0], $values) !== 
                       $this->evaluateAST($operands[1], $values);
            default:
                return false;
        }
    }
    
    /**
     * Evaluate AST with step-by-step tracking
     */
    private function evaluateASTWithSteps($ast, $values, $depth = 1)
    {
        $steps = [];
        
        if ($ast['type'] === 'variable') {
            $value = $values[$ast['value']] ?? false;
            $steps[] = [
                'step' => $depth,
                'expression' => $ast['value'],
                'result' => $value ? 'True' : 'False',
                'details' => 'Nilai variabel ' . $ast['value']
            ];
            return [$steps, $value];
        }
        
        $operator = $ast['operator'];
        $operands = $ast['operands'];
        
        // Evaluate operands
        $operandResults = [];
        $currentStep = $depth;
        
        foreach ($operands as $operand) {
            list($operandSteps, $operandValue) = $this->evaluateASTWithSteps($operand, $values, $currentStep);
            $steps = array_merge($steps, $operandSteps);
            $operandResults[] = $operandValue;
            $currentStep = $depth + count($steps);
        }
        
        // Apply operator
        $operatorNames = [
            '¬' => 'Negasi (BUKAN)',
            '∧' => 'Konjungsi (DAN)',
            '∨' => 'Disjungsi (ATAU)',
            '→' => 'Implikasi (JIKA-MAKA)',
            '↔' => 'Bikondisional (JIKA-HANYA-JIKA)',
            '⊕' => 'XOR (Exclusive OR)'
        ];
        
        $operatorName = $operatorNames[$operator] ?? $operator;
        
        // Format values for display
        $formatValue = function($val) { return $val ? 'B' : 'S'; };
        
        switch ($operator) {
            case '¬':
                $result = !$operandResults[0];
                $expression = '¬' . $this->astToString($operands[0]);
                $details = $operatorName . ': ' . $formatValue($operandResults[0]) . ' → ' . $formatValue($result);
                break;
            case '∧':
                $result = $operandResults[0] && $operandResults[1];
                $expression = $this->astToString($operands[0]) . ' ∧ ' . $this->astToString($operands[1]);
                $details = $operatorName . ': ' . $formatValue($operandResults[0]) . ' ∧ ' . $formatValue($operandResults[1]) . ' = ' . $formatValue($result);
                break;
            case '∨':
                $result = $operandResults[0] || $operandResults[1];
                $expression = $this->astToString($operands[0]) . ' ∨ ' . $this->astToString($operands[1]);
                $details = $operatorName . ': ' . $formatValue($operandResults[0]) . ' ∨ ' . $formatValue($operandResults[1]) . ' = ' . $formatValue($result);
                break;
            case '→':
                $result = !$operandResults[0] || $operandResults[1];
                $expression = $this->astToString($operands[0]) . ' → ' . $this->astToString($operands[1]);
                $details = $operatorName . ': ' . $formatValue($operandResults[0]) . ' → ' . $formatValue($operandResults[1]) . ' = ' . $formatValue($result);
                break;
            case '↔':
                $result = $operandResults[0] === $operandResults[1];
                $expression = $this->astToString($operands[0]) . ' ↔ ' . $this->astToString($operands[1]);
                $details = $operatorName . ': ' . $formatValue($operandResults[0]) . ' ↔ ' . $formatValue($operandResults[1]) . ' = ' . $formatValue($result);
                break;
            case '⊕':
                $result = $operandResults[0] !== $operandResults[1];
                $expression = $this->astToString($operands[0]) . ' ⊕ ' . $this->astToString($operands[1]);
                $details = $operatorName . ': ' . $formatValue($operandResults[0]) . ' ⊕ ' . $formatValue($operandResults[1]) . ' = ' . $formatValue($result);
                break;
            default:
                $result = false;
                $expression = $operator;
                $details = 'Operator tidak dikenal';
        }
        
        $steps[] = [
            'step' => $currentStep,
            'expression' => $expression,
            'result' => $result ? 'True' : 'False',
            'details' => $details
        ];
        
        return [$steps, $result];
    }
    
    /**
     * Extract variables from AST
     */
    private function extractVariablesFromAST($ast)
    {
        $variables = [];
        
        if ($ast['type'] === 'variable') {
            return [$ast['value']];
        }
        
        foreach ($ast['operands'] as $operand) {
            $variables = array_merge($variables, $this->extractVariablesFromAST($operand));
        }
        
        return array_unique($variables);
    }
    
    /**
     * Count true results in table
     */
    private function countTrue($table)
    {
        $count = 0;
        foreach ($table as $row) {
            if ($row['Hasil']) {
                $count++;
            }
        }
        return $count;
    }
    
    /**
     * Generate complete truth table with all basic operators for 2 variables
     */
    public function generateCompleteTable($variables)
    {
        if (count($variables) != 2) {
            return ['error' => 'Complete table only available for 2 variables'];
        }
        
        $p = $variables[0];
        $q = $variables[1];
        
        $table = [];
        $combinations = [
            [true, true],
            [true, false],
            [false, true],
            [false, false]
        ];
        
        foreach ($combinations as $combination) {
            $pVal = $combination[0];
            $qVal = $combination[1];
            
            $row = [
                $p => $pVal,
                $q => $qVal,
                '¬' . $p => !$pVal,
                $p . ' ∧ ' . $q => $pVal && $qVal,
                $p . ' ∨ ' . $q => $pVal || $qVal,
                $p . ' → ' . $q => !$pVal || $qVal,
                $p . ' ↔ ' . $q => $pVal === $qVal,
                $p . ' ⊕ ' . $q => $pVal !== $qVal
            ];
            
            $table[] = $row;
        }
        
        return [
            'table' => $table,
            'headers' => [
                $p, $q,
                '¬' . $p,
                $p . ' ∧ ' . $q,
                $p . ' ∨ ' . $q,
                $p . ' → ' . $q,
                $p . ' ↔ ' . $q,
                $p . ' ⊕ ' . $q
            ]
        ];
    }
    
    /**
     * Simplify expression (placeholder)
     */
    public function simplifyExpression($ast)
    {
        $original = $this->astToString($ast);
        
        return [
            'original' => $original,
            'simplified' => $original,
            'dnf' => 'Tidak tersedia (fitur dalam pengembangan)',
            'cnf' => 'Tidak tersedia (fitur dalam pengembangan)'
        ];
    }
    
    /**
     * Check if expression is tautology
     */
    public function isTautology($ast, $variables)
    {
        $table = $this->generate($ast, $variables);
        return $table['is_tautology'];
    }
    
    /**
     * Check if expression is contradiction
     */
    public function isContradiction($ast, $variables)
    {
        $table = $this->generate($ast, $variables);
        return $table['is_contradiction'];
    }
}