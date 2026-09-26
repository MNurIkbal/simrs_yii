<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\Supplier;
use Integrasi\Service\Odoo\Models\Penjamin;
use Integrasi\Service\Odoo\Models\Pegawai;
use Integrasi\Service\Odoo\Models\Perujuk;
use Integrasi\Service\Odoo\Models\IntPartner;
use Integrasi\Components\Object\AdditionalResponseObject;
use Integrasi\Components\Services\OdooService;

class Partner extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $id = $this->id;
        $lastInsert = $this->last_insert;
        $qPartner = IntPartner::find();

        if (!empty($id)) {
            $qPartner->andWhere([
                'sync_id_api' => $id
            ]);
        } else {
            if (isset($this->result['id']) && isset($this->result['model'])) {
                if ($this->result['model'] == 'PERUJUK') {
                    $qPartner->andWhere([
                        'sync_id_api' => 'REF' . $this->result['id']
                    ]);
                }
            }
        }

        $resultPartner = $qPartner->asArray()->all();
        foreach ($resultPartner as $value) {
            $idPartner = $value['sync_id_api'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);

            switch ($value['jenis']) {
                case 'SUPPLIER':
                    $modelPartner = new Supplier;
                    $primaryKey = 'supplier_id';
                    $paramOdooValue = ['vendor' => $value];

                    if (!empty($model->is_sending)) {
                        $url = '/vendor/edit';
                    } else {
                        $model->is_sending = true;
                        $url = '/vendor/create';
                    }
                    break;

                case 'PENJAMIN':
                    $modelPartner = new Penjamin;
                    $primaryKey = 'penjamin_id';
                    $paramOdooValue = $value;

                    if (!empty($model->is_sending)) {
                        $url = '/penjamin/update';
                    } else {
                        $model->is_sending = true;
                        $url = '/penjamin/create';
                    }
                    break;

                case 'PEGAWAI':
                    $modelPartner = new Pegawai;
                    $primaryKey = 'pegawai_id';
                    $paramOdooValue = $value;

                    if (!empty($model->is_sending)) {
                        $url = '/pegawai/update';
                    } else {
                        $model->is_sending = true;
                        $url = '/pegawai/create';
                    }
                    break;

                case 'PERUJUK':
                    $modelPartner = new Perujuk;
                    $primaryKey = 'perujuk_id';
                    $paramOdooValue = $value;

                    if (!empty($model->is_sending)) {
                        $url = '/pegawai/update';
                    } else {
                        $model->is_sending = true;
                        $url = '/pegawai/create';
                    }
                    break;

                default:
                    break;
            }

            $sendOdoo = (new OdooService)->sendOdoo($url, $paramOdooValue, function ($data) {
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

                $modelPartner::updateAll([
                    'additional_data' => json_encode($additonalMerge)
                ], [
                    $primaryKey => $value['partner_id']
                ]);
            }
        }

        return json_encode([
            'service' => 'Odoo-Partner',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}