<?php

namespace App\Services;

use App\Models\Article;
use DOMDocument;
use DOMXPath;
use Exception;
use GuzzleHttp\Client;
use simple_html_dom;
use Symfony\Component\DomCrawler\Crawler;

class ScrapperServices
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10.0,
        ]);
    }

    public function scrapePage($url)
    {
        $response = $this->client->request('GET', $url);
        $html = (string) $response->getBody();

        // Load the HTML into DOMDocument
        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        // Create a new XPath object
        $xpath = new DOMXPath($dom);

        // Find the h1 with class "post-title entry-title"
        $titleNode = $xpath->query('//h1[contains(@class, "post-title entry-title")]')->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : 'Title not found';

        // Find the div with class "entry-content entry clearfix"
        $nodeList = $xpath->query('//div[contains(@class, "entry-content entry clearfix")]');
        if ($nodeList->length === 0) {
            return ['success' => false, 'error' => 'Content div not found'];
        }

        $node = $nodeList[0];

        $textContent = [];

        // Extract the text content of all <p> tags within the div
        $paragraphs = $node->getElementsByTagName('p');
        foreach ($paragraphs as $paragraph) {
            $text = trim($paragraph->textContent);
            if (!empty($text)) {
                $textContent[] = $text;
            }
        }
        return ['text' => $textContent, 'title' => $title];
    }

    public function scrapePageRequest($request)
    {

        // $url = 'https://jawlah.co/36640';
        try {
            $request->validate([
                'url' => 'required',
            ]);
            $url = $request->url;
            $data = $this->scrapePage($url);
            // Return the extracted title and text content
            return ['success' => true, 'result' => ['title' => $data['title'], 'text' => $data['text']]];
        } catch (Exception $e) {
            // Handle exceptions
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    protected function scrapeJawlah()
    {
        // Load the HTML content from a URL or file
        $html = file_get_contents('https://jawlah.co/'); // Replace with your URL or file path

        // Initialize DOMDocument and load the HTML
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Handle errors due to malformed HTML
        $dom->loadHTML($html);
        libxml_clear_errors();

        // Initialize DOMXPath
        $xpath = new DOMXPath($dom);
        // $xpath->query()
        // Query to get all post items
        $nodes = $xpath->query('//ul[@class="posts-items posts-list-container"]/li');

        // Array to store the scraped data
        $posts = [];

        // Iterate over each post item
        foreach ($nodes as $node) {
            // Get the image URL
            $imgNode = $xpath->query('.//a[@class="post-thumb"]/img', $node)[0];
            $imgSrc = $imgNode ? $imgNode->getAttribute('src') : '';

            // Get the title
            $titleNode = $xpath->query('.//h2[@class="post-title"]/a', $node)[0];
            $title = $titleNode ? $titleNode->textContent : '';

            // Get the URL
            $urlNode = $xpath->query('.//h2[@class="post-title"]/a', $node)[0];
            $url = $urlNode ? $urlNode->getAttribute('href') : '';

            // Store the results in the array
            $posts[] = [
                'img'   => $imgSrc,
                'title' => $title,
                'url'   => $url,
                'platform' => 'Jawlah.co'
            ];
            $article = Article::where('title', $title)->first();
            if (!$article) {

                Article::create([
                    'title' => $title,
                    'original_url' => $url,
                    'image' => $imgSrc,
                    'news_company_id' => 1,
                ]);
            }
        }
        $allArticles = Article::select('articles.image as img', 'articles.title', 'articles.original_url as url', 'news_companies.name as platform')
        ->join('news_companies', 'articles.news_company_id', '=', 'news_companies.id')
        ->orderBy('articles.created_at', 'desc')
            ->get();
        return $allArticles;
        // dd($allArticles->toArray() , $posts);
        return $posts;
    }
    protected function scrapeAlquroosh()
    {
        $url = 'https://thmanyah.com/@alquroosh';

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        // Execute cURL request and get the response
        $html = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            return;
        }

        // Close cURL session
        curl_close($ch);

        // Save the HTML to a file for inspection
        file_put_contents('thmanyah_page.html', $html);

        echo "HTML content has been saved to 'thmanyah_page.html'. Please check this file.\n";

        // Now let's try to analyze the content
        $crawler = new Crawler($html);

        // Let's print out all the text content of the page
        $textContent = $crawler->filter('body')->text();
        echo "First 1000 characters of text content:\n";
        echo substr($textContent, 0, 1000) . "\n\n";

        // Let's try to find any div elements and print their classes
        $divs = $crawler->filter('div');
        echo "Number of div elements found: " . $divs->count() . "\n";
        $divs->each(function (Crawler $div, $i) {
            if ($i < 10) {  // Let's look at the first 10 divs
                $class = $div->attr('class');
                if ($i == 1)
                    echo "Div " . ($i + 1) . " class: " . ($class ? $class : 'No class') . $div->innerHTML() . "\n";
                echo "Div " . ($i + 1) . " class: " . ($class ? $class : 'No class') . "\n";
            }
        });
    }

    public function scrapeMainPage()
    {
        try {
            // Here add all the pages that we want to scrape.
            // $alqurooshPosts = $this->scrapeAlquroosh();
            // dd($alqurooshPosts);
            $jawlahPosts = $this->scrapeJawlah();


            $posts = $jawlahPosts;

            // Return success with the scraped data
            return ['success' => true, 'data' => $posts];
        } catch (Exception $e) {
            // Return error if something goes wrong
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
