<?php

namespace App\Domain\Product\Services;
class ProductSearchService
{   
    //FUNÇÃO DE PESQUISA COM ELETRIC SEARCH
    public function search(string $query): Collection
    {
        $client = ClientBuilder::create()
            ->setHosts(['localhost:9200'])
            ->build();
        
        $params = [
            'index' => 'products',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['product_name^3', 'brands^2', 'categories']
                    ]
                ]
            ]
        ];
        
        $response = $client->search($params);
        
        return collect($response['hits']['hits'])
            ->pluck('_source')
            ->mapInto(Product::class);
    }
}