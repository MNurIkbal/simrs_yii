<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\Pasien as Model;
use Integrasi\Service\Odoo\Models\IntPasienView;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Pasien extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = (int) $this->id;

        $qPaket = IntPasienView::find();

        if (!empty($id)) {
            $qPaket->andWhere([
                'pasien_id' => $id
            ]);
            $qPaket->orderBy([
                'id' => SORT_DESC
            ])->limit(1);
        }

        $resultData = $qPaket->asArray()->all();

        foreach ($resultData as $value) {
            $idPaket = $value['pasien_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);

            if (!empty($value['no_identitas'])) {
                if ($value['no_identitas'] != "-") {
                    foreach (json_decode($value['no_identitas']) as $k => $v) {
                         if ($v->jenisidentitas == 94 || $v->jenisidentitas == "94") { // ktp
                              $value['ktp'] = (string)$v->no_identitas_pasien;
                         } else if ($v->jenisidentitas == 99 || $v->jenisidentitas == "99") { // passport
                              $value['passport'] = (string)$v->no_identitas_pasien;
                         }
                    }
                }
            }

            $url = '/pasien/update';

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

                $additonalMerge = array_merge($additional, $model->buildArray());
                Model::updateAll([
                    'additional_data' => json_encode($additonalMerge)
                ], [
                    'pasien_id' => $idPaket
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Pasien-update',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}