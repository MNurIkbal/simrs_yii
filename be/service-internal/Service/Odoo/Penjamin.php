<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\Penjamin as Model;
use Integrasi\Service\Odoo\Models\IntPenjaminView;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Penjamin extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = (int) $this->id;
        $lastInsert = $this->last_insert;
        $count = $this->count;

        $qPaket = IntPenjaminView::find();

        if (!empty($id)) {
            $qPaket->andWhere([
                'penjamin_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qPaket->orderBy([
                'penjamin_id' => SORT_DESC
            ])->limit(empty($count) ? 1 : $count);
        }

        $resultData = $qPaket->asArray()->all();

        foreach ($resultData as $value) {
            $idPaket = $value['penjamin_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/penjamin/update';
            } else {
                $url = '/penjamin/create';
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

                $additonalMerge = array_merge($additional, $model->buildArray());
                Model::updateAll([
                    'additional_data' => json_encode($additonalMerge)
                ], [
                    'penjamin_id' => $idPaket
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Penjamin',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}