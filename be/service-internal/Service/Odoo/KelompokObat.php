<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\JenisObatAlkes as Model;
use Integrasi\Service\Odoo\Models\IntKelompokObatView;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class KelompokObat extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;

        $qPaket = IntKelompokObatView::find();

        if (!empty($id)) {
            $qPaket->andWhere([
                'jenisobatalkes_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qPaket->orderBy([
                'jenisobatalkes_id' => SORT_DESC
            ])->limit(1);
        }

        $resultCateg = $qPaket->asArray()->all();
        foreach ($resultCateg as $value) {
            $idPaket = $value['jenisobatalkes_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/jenisobat/update';
            } else {
                $url = '/jenisobat/create';
            }

            $sendOdoo = (new OdooService)->sendOdoo($url, $value, function ($data) {
                return $data;
            });

            if (!empty($sendOdoo)) {
                $dataRest = isset($sendOdoo['Results'][0]) ? $sendOdoo['Results'][0] : [];
                $data = isset($dataRest['data']) ? $dataRest['data'] : [];
                $model->uid = isset($sendOdoo['ProcessUID']) ? $sendOdoo['ProcessUID'] : null;
                $model->is_error = isset($data['is_error']) ? $data['is_error'] : false;
                $model->is_sending = $model->is_error && empty($model->is_sending) ? false : true;
                $model->response = isset($data['response']) ? $data['response'] : false;
                $model->payload = isset($data['payload']) ? $data['payload'] : false;

                Model::updateAll([
                    'additional_data' => json_encode($model->buildArray())
                ], [
                    'jenisobatalkes_id' => $idPaket
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-KelompokObat',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}