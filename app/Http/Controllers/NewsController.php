<?php

namespace App\Http\Controllers;

use App\Services\NewsServices;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    //
    protected $service;
    public function __construct(NewsServices $service )
    {
        $this->service = $service;
    }
    public function getOneNews(Request $request ){
        $response = $this->service->getOneNews($request);
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);
    }
    public function getFeaturedNews(Request $request ){
        $response = $this->service->getFeaturedNews($request);
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);
    }

    public function getMainNews(){
        $response = $this->service->getMainNews();
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);   
    }
    public function storeToDb(Request $request ){
        $response = $this->service->storeToDb($request);
        if (!$response['success']) {
            return $this->sendError($response);
        }
        return $this->sendResponse($response);
    }
}
