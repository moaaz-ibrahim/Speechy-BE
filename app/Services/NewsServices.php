<?php

namespace App\Services;

use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\Storage;

class NewsServices
{
    // Your service logic goes here
    public function getOneNews($request){
        try {
            $request->validate([
                'url' => 'required'
            ]);
            $url = $request->get('url');
            $article = Article::where('original_url', $url)->first();
            if($article->path){
                // Basically when I scrape the main page of news company (the page that contains the article name, image and url to article)
                // I save the article name, url, and image to the database, so here I check if I generated an audio file for this article before.
                return ['success' => true,  'title'=>$article->title,'audio_path'=>$article->path,'parts'=>$article->parts];
            }

            if(!$article->path){

                $scrapper = new ScrapperServices();
                $newsData = $scrapper->scrapePage($url);
                $voice = new VoiceServices();
                // dd($newsData);
                $articelTitle =$newsData['title'];
                $part = 1;
                $fullFolderPath =public_path('audioFiles/'.$newsData['title']); 

                $folderPath = 'audioFiles/'.$newsData['title'];
                
                if (!file_exists($fullFolderPath)) {
                    foreach($newsData['text'] as $news){
                        $voiceData = $voice->generateVoice($articelTitle,$part, $news);
                        $part++;
                    }
                }
                
                else {
                    $files = array_diff(scandir($fullFolderPath), ['.', '..']);
                    $part= count($files);
                }
                $article->path = $folderPath;
                $article->parts = $part;
                $article->save();
            }
            // if($voiceData['success'])
            return ['success' => true,  'title'=>$newsData['title'],'audio_path'=>$folderPath,'parts'=>$part];
        return throw new Exception($voiceData['message']);
        } catch (Exception $e) {
            //throw $th;
            return ['success' => false,'message' => $e->getMessage()];
        }
    }
    
    public function storeToDb($request){
        try {
            //code...
            $audioFiles = Storage::disk('public_dir')->allDirectories('/audioFiles');
            $successCreated = [];
            foreach ($audioFiles as $file){
                $articleName =explode('/' , $file)[1];
                // dd();
                $article = [
                    'title'=>$articleName,
                    'path'=>$file,
                    'news_company_id' =>1
                ];
                $createdArticle  = Article::create($article);
                if($createdArticle){
                    $successCreated[] = $articleName;
                }
            }
            
            return ['success' => true, 'data' => $successCreated];
        } catch (Exception $e) {
            //throw $th;
            return ['success' => false,'message' => $e->getMessage()];
        }
    }
}
