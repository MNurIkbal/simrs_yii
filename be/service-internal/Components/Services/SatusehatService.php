<?php

namespace Integrasi\Components\Services;

use Yii;
class SatusehatService extends SerconnBaseService
{
    public function mappResponse($response, $rawResponse = false)
    {
        $data = isset($response['Results']['data']) 
                    ? $response['Results']['data'] : (isset($response['Results'][0]) && isset($response['Results'][0]['data']) 
                                ? $response['Results'][0]['data'] : []);
        $error = isset($response['Results']['error']) 
                    ? $response['Results']['error'] : (isset($response['Results'][0]) && isset($response['Results'][0]['error']) 
                                ? $response['Results'][0]['error'] : NULL);
        return [
            'uid' => isset($response['ProcessUID']) ? $response['ProcessUID'] : null,
            'error' => $error,
            'response' => $data,
            'rawResponse' => $rawResponse ? $response : []
        ];
    }
    
    public function createEncounter($payload, $blocking = true)
    {
        return $this->executeApi('/encounter/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function updateEncounter($payload, $blocking = true) 
    {
        return $this->executeApi('/encounter/update', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createCondition($payload, $blocking = true)
    {
        return $this->executeApi('/condition/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createEncounterOutpatientResume($payload, $blocking = true)
    {
        return $this->executeApi('/encounter/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createObservation( $payload, $blocking = true )
    {
        return $this->executeApi('/observation/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response, true);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createComposition( $payload, $blocking = true )
    {
        return $this->executeApi('/composition/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response, true);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createProcedure($payload, $blocking = true)
    {
        return $this->executeApi('/procedure/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createMedication($payload, $blocking = true) {
        return $this->executeApi('/medication/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createMedicationDispense($payload, $blocking = true) {
        return $this->executeApi('/medicationDispense/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }
    
    public function createMedicationRequest($payload, $blocking = true) {
        return $this->executeApi('/medicationRequest/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createServiceRequest($payload, $blocking = true)
    {
        return $this->executeApi('/servicerequest/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createPractitioner($payload, $blocking = true)
    {
        return $this->executeApi('/practitioner/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response, true);
            },
            'is_blocking' => $blocking
        ]);
    }
    
    public function createOrganization($payload, $blocking = true)
    {
        return $this->executeApi('/organization/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function updateOrganization($payload, $blocking = true) 
    {
        return $this->executeApi('/organization/update', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createLocation($payload, $blocking = true)
    {
        return $this->executeApi('/location/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }
    
    public function updateLocation($payload, $blocking = true)
    {
        return $this->executeApi('/location/prosesupdate', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function create($url = '', $payload, $blocking = true)
    {
        return $this->executeApi($url.'/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function createPatient($payload, $blocking = true)
    {
        return $this->executeApi('/patient/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }
    
}
