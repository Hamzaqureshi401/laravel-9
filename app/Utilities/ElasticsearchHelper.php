<?php

namespace App\Utilities;

use App\Utilities\Contracts\ElasticsearchHelperInterface;

use Elasticsearch\ClientBuilder;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        $params = [
            'index' => 'emails',
            'body' => [
                'message_body' => $messageBody,
                'message_subject' => $messageSubject,
                'to_email_address' => $toEmailAddress,
            ],
        ];

        $response = $this->client->index($params);

        return $response['_id'];
    }

     public function listSentEmails(){
        
        $index = 'emails';
        $type = '_doc';
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ];
        $response = $this->client->search($params);
        $sentEmails = [];
        foreach ($response['hits']['hits'] as $hit) {
            $email = [
                'to' => $hit['_source']['to_email_address'],
                'subject' => $hit['_source']['message_subject'],
                'body' => $hit['_source']['message_body'],
            ];
            $sentEmails[] = $email;
        }
        return $sentEmails;
    }

}

