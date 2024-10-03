<?php

namespace App\Services;

use GuzzleHttp\Client;

class GuardianApiService {
    protected $client;
    protected $apiKey;
    protected $pageSize = 5;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GUARDIAN_API_KEY');
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

        if (!empty($preferences['categories'])) {
            $params['section'] = implode(',', $preferences['categories']);
        }

        if (!empty($preferences['authors'])) {
            $params['q'] = (isset($params['q']) ? $params['q'] . ' ' : '') . implode(' OR ', $preferences['authors']);
        }

        $response = $this->client->get('https://content.guardianapis.com/search', [
            'query' => $params
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function formatArticles(array $guardianArticlesRaw)
    {
        if (isset($guardianArticlesRaw['response']['results'])) {
            return array_map(function ($article) {
                return [
                    'title' => $article['webTitle'] ?? 'No title',
                    'description' => $article['fields']['trailText'] ?? '',
                    'published_at' => $article['webPublicationDate'] ?? '',
                    'url' => $article['webUrl'] ?? '',
                    'author' => $article['fields']['byline'] ?? 'Unknown author',
                    'source' => 'The Guardian',
                    'image' => $article['fields']['thumbnail'] ?? null,
                    'api' => 'guardian'
                ];
            }, $guardianArticlesRaw['response']['results']);
        }

        return [];
    }
}
