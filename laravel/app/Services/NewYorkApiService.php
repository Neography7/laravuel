<?php

namespace App\Services;

use GuzzleHttp\Client;

class NewYorkApiService {
    protected $client;
    protected $apiKey;
    protected $pageSize = 5;

    public function __construct() {
        $this->client = new Client();
        $this->apiKey = env('NYT_API_KEY');
    }

    public function getArticles($query = null, $preferences = [], $page = 1)
    {
        $params = [
            'api-key' => $this->apiKey,
            'page' => $page,
            'page-size' => $this->pageSize
        ];

        if (!empty($query)) {
            $params['q'] = $query;
        }

        $filters = [];

        if (!empty($preferences['categories'])) {
            $categoriesFilter = 'news_desk:(' . implode(' ', $preferences['categories']) . ')';
            $filters[] = $categoriesFilter;
        }

        if (!empty($preferences['sources'])) {
            $sourcesFilter = 'source:(' . implode(' ', $preferences['sources']) . ')';
            $filters[] = $sourcesFilter;
        }

        if (!empty($preferences['authors'])) {
            $authorsFilter = 'byline:(' . implode(' ', $preferences['authors']) . ')';
            $filters[] = $authorsFilter;
        }

        if (!empty($filters)) {
            $params['fq'] = implode(' AND ', $filters);
        }

        $response = $this->client->get('https://api.nytimes.com/svc/search/v2/articlesearch.json', [
            'query' => $params
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function formatArticles(array $newYorkArticlesRaw)
    {
        if (isset($newYorkArticlesRaw['response']['docs'])) {
            return array_map(function ($article) {
                return [
                    'title' => $article['headline']['main'] ?? 'No title',
                    'description' => $article['snippet'] ?? '',
                    'published_at' => $article['pub_date'] ?? '',
                    'url' => $article['web_url'] ?? '',
                    'author' => $article['byline']['original'] ?? 'Unknown author',
                    'source' => 'New York Times',
                    'image' => $article['multimedia'][0]['url'] ?? null,
                    'api' => 'newyork'
                ];
            }, $newYorkArticlesRaw['response']['docs']);
        }

        return [];
    }
}
