<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class SerconPayload extends \Doco\components\DocoBaseModel
{
    public $uid;
    public $is_error;
    public $data = [];
    public $payload = [];
    public $error_payload = [];
    public $id_rekap;
    public $tipe_rekap;

    public function rules()
    {
        return [
            [[
                'uid',
                'is_error',
                'data',
                'payload',
                'error_payload',
            ],'safe']
        ];
    }

    /**
     * set payload sercon
     * @return void
     */
    public function getPayloadSercon()
    {
        $result = isset($this->data['payload']) ? $this->data['payload'] : [];
        if ($this->is_error) {
            $result = $this->error_payload;
        }

        $this->id_rekap = isset($result['id_rekap']) ? $result['id_rekap'] : null;
        $this->tipe_rekap = isset($result['tipe_rekap']) ? $result['tipe_rekap'] : null;

        return $result;
    }
}