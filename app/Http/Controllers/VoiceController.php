<?php

namespace App\Http\Controllers;

use App\Services\VoiceServices;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    //
    protected $service;
    public function __construct(VoiceServices $service)
    {
        $this->service = $service;
    }
    public function generateVoice(Request $request ){
        $response = $this->service->generateVoice($request, '');
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);
    }
}
