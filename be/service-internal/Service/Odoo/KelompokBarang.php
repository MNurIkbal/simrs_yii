<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\KelompokBarang as Model;
use Integrasi\Service\Odoo\Models\IntKelompokBarangView;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class KelompokBarang extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = (int) $this->id;
        $lastInsert = $this->last_insert;

        $qPaket = IntKelompokBarangView::find();

        if (!empty($id)) {
            $qPaket->andWhere([
                'kelompokbarang_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qPaket->orderBy([
                'kelompokbarang_id' => SORT_DESC
            ])->limit(1);
        }

        $resultCateg = $qPaket->asArray()->all();
        foreach ($resultCateg as $value) {
            $idPaket = $value['kelompokbarang_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/jenisobat/update';
            } else {
                $model->is_sending = true;
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
                $model->response = isset($data['response']) ? $data['response'] : false;
                $model->payload = isset($data['payload']) ? $data['payload'] : false;

                Model::updateAll([
                    'additional_data' => json_encode($model->buildArray())
                ], [
                    'kelompokbarang_id' => $idPaket
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-KelompokBarang',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}