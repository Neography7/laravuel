<?php

namespace App\Services;

use GuzzleHttp\Client;

class NewsApiService {
    protected $client;
    protected $apiKey;
    protected $pageSize = 5;

    public function __construct() {
        $this->client = new Client();
        $this->apiKey = env('NEWS_API_KEY');
    }

    public function getArticles($country = 'us', $preferences = [], $page = 1) {

        $params = [
            'apiKey' => $this->apiKey,
            'country' => $country,
            'page' => $page,
            'pageSize' => $this->pageSize
        ];

        if (!empty($preferences['category'])) {
            $params['category'] = $preferences['category'];
        }

        if (!empty($preferences['authors'])) {
            $params['q'] = implode(' OR ', $preferences['authors']);
        }

        $response = $this->client->get('https://newsapi.org/v2/top-headlines', [
            'query' => $params
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function formatArticles(array $newsApiArticlesRaw)
    {
        if (isset($newsApiArticlesRaw['articles'])) {
            return array_map(function ($article) {
                return [
                    'title' => $article['title'] ?? 'No title',
                    'description' => $article['description'] ?? '',
                    'published_at' => $article['publishedAt'] ?? '',
                    'url' => $article['url'] ?? '',
                    'author' => $article['author'] ?? 'Unknown author',
                    'source' => $article['source']['name'] ?? 'Unknown source',
                    'image' => $article['urlToImage'] ?? null,
                    'api' => 'newsapi'
                ];
            }, $newsApiArticlesRaw['articles']);
        }

        return [];
    }
}
