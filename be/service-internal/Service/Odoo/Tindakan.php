<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\DaftarTindakan as Model;
use Integrasi\Service\Odoo\Models\IntTindakanView;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Tindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;

        $qTindakan = IntTindakanView::find();

        if (!empty($id)) {
            $qTindakan->andWhere([
                'daftartindakan_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qTindakan->orderBy([
                'daftartindakan_id' => SORT_DESC
            ])->limit(1)->offset(3);
        }

        $resultCateg = $qTindakan->asArray()->all();
        foreach ($resultCateg as $value) {
            $idPaket = $value['daftartindakan_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/tindakan/update';
            } else {
                $url = '/tindakan/create';
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
                    'daftartindakan_id' => $idPaket
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Tindakan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function replaceString($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

}