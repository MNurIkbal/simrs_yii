<?php

namespace app\components\Services\Laboratorium;

use Yii;

class GetPasienPenunjangService extends BaseCurrentService
{
    public function execute($id)
    {
        return $this->guzzleExec($this->restLab, [
            'url' => 'input-hasil/get-pasien-masuk-penunjang',
            'payload' => [
                'query' => [
                    'id' => $id,
                ]
            ]
        ]);
    }
}
