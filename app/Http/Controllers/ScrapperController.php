<?php

namespace App\Http\Controllers;

use App\Services\ScrapperServices;
use Illuminate\Http\Request;

class ScrapperController extends Controller
{
    protected $service;
    public function __construct(ScrapperServices $service)
    {
        $this->service = $service;
    }
    public function scrapePage(Request $request ){
        $response = $this->service->scrapePageRequest($request);
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);
    }
    public function scrapeMainPage(Request $request ){
        $response = $this->service->scrapeMainPage($request);
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);
    }
}
