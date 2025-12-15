<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PropositionController;
use Illuminate\Http\Request;

class PropositionAPIController extends Controller
{
    private PropositionController $propositionController;
    
    public function __construct()
    {
        $this->propositionController = new PropositionController();
    }
    
    public function calculate(Request $request)
    {
        return $this->propositionController->calculate($request);
    }
    
    public function history(Request $request)
    {
        return $this->propositionController->history($request);
    }
    
    public function getLogicLaws()
    {
        return $this->propositionController->getLogicLaws();
    }
    
    public function validateExpression(Request $request)
    {
        $request->validate([
            'expression' => 'required|string'
        ]);
        
        $validator = new \App\Utilities\PropositionValidator();
        $result = $validator->validate($request->expression);
        
        return response()->json($result);
    }
}