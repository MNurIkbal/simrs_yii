<?php

namespace Integrasi\Components\Services;

class MhgService extends SerconnBaseService
{
    public function doctorScheduleCreate($payload, $blocking = true)
    {
        return $this->executeApi('/doctorschedule/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function doctorScheduleDelete($payload, $blocking = true)
    {
        return $this->executeApi('/doctorschedule/delete', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function appointmentUpdate($payload, $blocking = true)
    {
        return $this->executeApi('/appointment/update', $payload, [
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

    public function patientCreate($payload, $blocking = true)
    {
        return $this->executeApi('/syncmasterdata/patient', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function doctorLeaveCreate($payload, $blocking = true)
    {
        return $this->executeApi('/doctorleave/create', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }

    public function doctorLeaveDelete($payload, $blocking = true)
    {
        return $this->executeApi('/doctorleave/delete', $payload, [
            'method' => 'POST',
            'callbackSuccess' => function ($response) {
                return $this->mappResponse($response);
            },
            'is_blocking' => $blocking
        ]);
    }
}
