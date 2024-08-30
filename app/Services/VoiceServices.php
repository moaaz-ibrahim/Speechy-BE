<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class VoiceServices
{
    public function generateVoice($folderTitle = 'test', $fileTitle, $text)
{
    try {
        // Specify the path for the folder and file in the public directory
        $folderPath = public_path('audioFiles/' . $folderTitle);
        $filePath = $folderPath . '/' . $fileTitle . '.mp3';

        // Make the HTTP request using Laravel's HTTP client
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'xi-api-key' => 'sk_801d298ed22bf05cc2853df9d1746183088740e8d6970a1c',
            // nPczCjzI2devNBz1zQrb : original voice
            // tavIIPLplRB883FzWU0V : Mona
        ])->post('https://api.elevenlabs.io/v1/text-to-speech/nPczCjzI2devNBz1zQrb', [
            'model_id' => 'eleven_turbo_v2_5',
            'language_code' => 'ar',
            'text' => $text,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Create the folder if it doesn't exist
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            // Save the response body to a file
            file_put_contents($filePath, $response->body());

            // Return the public URL of the file
            return ['success' => true, 'message' => 'Voice generated and saved successfully', 'file_path' => asset('audioFiles/' . $folderTitle . '/' . $fileTitle . '.mp3')];
        } else {
            return ['success' => false, 'message' => 'Failed to generate voice. HTTP Status: ' . $response->status(), 'error' => $response->body()];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
    }
}
}