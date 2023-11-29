<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;

class SearchController extends Controller
{   
    
    // private $elasticsearch;
    // public function __construct()
    // {
    //     $this->elasticsearch = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
    // }

    // public function search(Request $request)
    // {
    //     $query = $request->input('query');
    //     $params = [
    //         'index' => 'users_index',
    //         'body' => [
    //             'query' => [
    //                 'multi_match' => [
    //                     'query' => $query,
    //                     'fields' => ['username', 'name'],   
    //                 ]
    //             ]
    //         ]
    //     ];
    //     $response = $this->elasticsearch->search($params);
    //     $hits = $response['hits']['hits'];
    //     return response()->json($hits);
    // }
}
