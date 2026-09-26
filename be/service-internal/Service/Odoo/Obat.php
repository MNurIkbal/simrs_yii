<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\ObatAlkes as Model;
use Integrasi\Service\Odoo\Models\IntObat;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Obat extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;

        $qObat = IntObat::find();

        if (!empty($id)) {
            $qObat->andWhere([
                'obatalkes_id' => $id
            ]);
        }

        if (!empty($lastInsert)) {
            $qObat->orderBy([
                'obatalkes_id' => SORT_DESC
            ]);
        }

        $resultObat = $qObat->asArray()->all();
        foreach ($resultObat as $value) {
            $idObat = $value['obatalkes_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                $url = '/producttemplate/obatedit';
            } else {
                $url = '/producttemplate/obat';
            }
            $sendOdoo = (new OdooService)->sendOdoo($url, ['obat'=>$value], function ($data) {
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
                    'obatalkes_id' => $idObat
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Obat',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function replaceString($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

}