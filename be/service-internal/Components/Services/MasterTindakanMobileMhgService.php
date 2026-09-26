<?php

namespace Integrasi\Components\Services;

class MasterTindakanMobileMhgService extends SerconnBaseService
{
    public function createTindakanMobileMhg($payload, $blocking = true)
    {
        return $this->executeApi('/tindakanMobileMhg/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function deleteTindakanMobileMhg($payload, $blocking = true)
    {
        return $this->executeApi('/tindakanMobileMhg/delete', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function updateTindakanMobileMhg($payload, $blocking = true)
    {
        return $this->executeApi('/tindakanMobileMhg/update', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function mappResponse($response)
    {
        $data = isset($response['Results']['data']) 
                    ? $response['Results']['data'] : (isset($response['Results'][0]) && isset($response['Results'][0]['data']) 
                                ? $response['Results'][0]['data'] : []);
        
        return [
            'uid' => isset($response['ProcessUID']) ? $response['ProcessUID'] : null,
            'response' => $data,
        ];
    }
}
