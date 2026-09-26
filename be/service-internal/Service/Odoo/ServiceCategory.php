<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\ServiceCategory as Model;
use Integrasi\Service\Odoo\Models\IntServiceCategoryView;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class ServiceCategory extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;

        $qCateg = IntServiceCategoryView::find();
        if (!empty($id)) {
            $qCateg->andWhere([
                'servicecategory_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qCateg->orderBy([
                'servicecategory_id' => SORT_DESC
            ])->limit(1);
        }

        $resultCateg = $qCateg->asArray()->all();
        foreach ($resultCateg as $value) {
            $idCateg = $value['servicecategory_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/servicecategory/update';
            } else {
                $url = '/servicecategory/create';
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
                    'servicecategory_id' => $idCateg
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-ServiceCategory',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function replaceString($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

}