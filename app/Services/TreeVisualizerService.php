<?php

namespace App\Services;

class TreeVisualizerService
{
    public function generate(array $ast): array
    {
        $nodeId = 0;
        $treeData = $this->buildTreeData($ast, 0, $nodeId);
        
        // Add some metadata to tree
        return [
            'type' => 'logic_tree',
            'root' => $treeData,
            'total_nodes' => $nodeId,
            'max_depth' => $this->calculateMaxDepth($treeData),
            'expression' => $this->getExpressionFromAST($ast)
        ];
    }
    
    private function buildTreeData(array $node, int $depth = 0, int &$nodeId = 0): array
    {
        $currentId = ++$nodeId;
        
        if ($node['type'] === 'variable') {
            return [
                'id' => $currentId,
                'label' => $node['value'],
                'type' => 'variable',
                'depth' => $depth,
                'children' => []
            ];
        }
        
        $operatorLabels = [
            '¬' => 'Negasi',
            '∧' => 'Konjungsi',
            '∨' => 'Disjungsi',
            '→' => 'Implikasi',
            '↔' => 'Bikondisional',
            '⊕' => 'XOR'
        ];
        
        $operator = $node['operator'];
        $label = isset($operatorLabels[$operator]) ? 
                 $operatorLabels[$operator] : 
                 $operator;
        
        $children = [];
        foreach ($node['operands'] as $operand) {
            $children[] = $this->buildTreeData($operand, $depth + 1, $nodeId);
        }
        
        return [
            'id' => $currentId,
            'label' => $label,
            'type' => 'operator',
            'operator' => $operator,
            'depth' => $depth,
            'children' => $children
        ];
    }
    
    private function calculateMaxDepth(array $node): int
    {
        if (empty($node['children'])) {
            return $node['depth'];
        }
        
        $maxDepth = $node['depth'];
        foreach ($node['children'] as $child) {
            $childDepth = $this->calculateMaxDepth($child);
            if ($childDepth > $maxDepth) {
                $maxDepth = $childDepth;
            }
        }
        
        return $maxDepth;
    }
    
    private function getExpressionFromAST(array $ast): string
    {
        if ($ast['type'] === 'variable') {
            return $ast['value'];
        }
        
        $operator = $ast['operator'];
        $operands = $ast['operands'];
        
        if ($operator === '¬') {
            return '¬' . $this->getExpressionFromAST($operands[0]);
        } else {
            $result = '(';
            foreach ($operands as $index => $operand) {
                if ($index > 0) {
                    $result .= ' ' . $operator . ' ';
                }
                $result .= $this->getExpressionFromAST($operand);
            }
            $result .= ')';
            return $result;
        }
    }
    
    public function generateGraphData(array $ast): array
    {
        $treeData = $this->generate($ast);
        return $this->flattenTree($treeData['root']);
    }
    
    private function flattenTree(array $node): array
    {
        $nodes = [];
        $edges = [];
        
        $stack = [$node];
        
        while (!empty($stack)) {
            $current = array_pop($stack);
            
            $nodes[] = [
                'id' => $current['id'],
                'label' => $current['label'],
                'type' => $current['type'],
                'depth' => $current['depth'],
                'operator' => $current['operator'] ?? null
            ];
            
            foreach ($current['children'] as $child) {
                $edges[] = [
                    'from' => $current['id'],
                    'to' => $child['id']
                ];
                $stack[] = $child;
            }
        }
        
        return [
            'nodes' => $nodes,
            'edges' => $edges
        ];
    }
    
    public function generateHtmlTree(array $ast): string
    {
        $treeData = $this->generate($ast);
        return $this->buildHtmlTree($treeData['root']);
    }
    
    private function buildHtmlTree(array $node): string
    {
        if (empty($node['children'])) {
            return '<div class="tree-node-variable">' . htmlspecialchars($node['label']) . '</div>';
        }
        
        $html = '<div class="tree-node-operator">';
        $html .= '<div class="tree-operator-main">' . htmlspecialchars($node['label']) . '</div>';
        
        if (!empty($node['children'])) {
            $html .= '<div class="tree-children">';
            foreach ($node['children'] as $child) {
                $html .= $this->buildHtmlTree($child);
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}