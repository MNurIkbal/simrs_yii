<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\Supplier as Model;
use Integrasi\Service\Odoo\Models\IntPartner;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Supplier extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;
        $qPartner = IntPartner::find();

        if (!empty($id)) {
            $qPartner->andWhere([
                'sync_id_api' => 'SUP'.$id,
                'jenis' => 'SUPPLIER'
            ]);
        }

        if (!empty($lastInsert)) {
            $qPartner->andWhere([
                'jenis' => 'SUPPLIER'
            ]);

            $qPartner->orderBy([
                'partner_id' => SORT_DESC
            ])->limit(1);
        }

        $resultPartner = $qPartner->asArray()->all();
        foreach ($resultPartner as $value) {
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);

            if (!empty($model->is_sending)) {
                $url = '/vendor/edit';
            } else {
                $model->is_sending = true;
                $url = '/vendor/create';
            }

            $sendOdoo = (new OdooService)->sendOdoo($url, ['vendor' => $value], function ($data) {
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
                    'supplier_id' => $value['partner_id']
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Supplier',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}