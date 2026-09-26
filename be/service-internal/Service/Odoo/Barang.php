<?php

namespace Integrasi\Service\Odoo;

use Yii;
use yii\db\Expression as DbExpression;
use Integrasi\Service\Odoo\Models\Barang as Model;
use Integrasi\Service\Odoo\Models\IntBarang;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Barang extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;
        $isInjected = $this->is_injected;

        $qBarang = IntBarang::find();

        if (!empty($id)) {
            $qBarang->andWhere([
                'barang_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qBarang->orderBy([
                'barang_id' => SORT_DESC
            ]);
        }

        if (!empty($isInjected)) {
            $qBarang->andWhere([
                'IS', 'additional_data', new DbExpression('null')
            ]);
        }

        $resultBarang = $qBarang->asArray()->all();
        foreach ($resultBarang as $value) {
            $idBarang = $value['barang_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/producttemplate/barangedit';
            } else {
                $url = '/producttemplate/barang';
            }
            $sendOdoo = (new OdooService)->sendOdoo($url, ['barang'=>$value], function ($data) {
                return $data;
            });

            if (!empty($sendOdoo)) {
                $dataRest = isset($sendOdoo['Results'][0]) ? $sendOdoo['Results'][0] : [];
                $data = isset($dataRest['data']) ? $dataRest['data'] : [];
                $model->uid = isset($sendOdoo['ProcessUID']) ? $sendOdoo['ProcessUID'] : null;
                $model->is_error = isset($data['is_error']) ? $data['is_error'] : false;
                $model->is_sending = $model->is_error && empty($model->is_sending) ? false : true;
                $model->response = isset($data['response']) ? $data['response'] : $sendOdoo;
                $model->payload = isset($data['payload']) ? $data['payload'] : false;

                $additonalMerge = array_merge($additional, $model->buildArray());
                Model::updateAll([
                    'additional_data' => json_encode($additonalMerge)
                ], [
                    'barang_id' => $idBarang
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Barang',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

}