<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Object;

use Integrasi\Components\DocoConstants;

class AdditionalResponseObject extends DocoBaseObject
{

    public $is_sending;

    public $is_error;

    public $response;

    public $payload;

    public $uid;

    /**
     * @return mixed
     */
    public function getIsSending()
    {
        return $this->is_sending;
    }

    /**
     * @param mixed $is_sending
     *
     * @return self
     */
    public function setIsSending($is_sending)
    {
        $this->is_sending = $is_sending;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsError()
    {
        return $this->is_error;
    }

    /**
     * @param mixed $is_error
     *
     * @return self
     */
    public function setIsError($is_error)
    {
        $this->is_error = $is_error;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     *
     * @return self
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     *
     * @return self
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     *
     * @return self
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return array
     */
    public function buildArray()
    {
        return [
            'uid' => $this->uid,
            'payload' => $this->payload,
            'response' => $this->response,
            'is_error' => $this->is_error,
            'is_sending' => $this->is_sending,
        ];
    }
}