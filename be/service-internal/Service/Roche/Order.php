<?php

namespace Integrasi\Service\Roche;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Roche\Models\BridgingOrderLabRocheView;
use Integrasi\Service\Roche\Models\PasienMasukPenunjang;
use Integrasi\Service\Roche\Models\IntegrasiRoche;
use Integrasi\Components\Services\RocheService;
use yii\helpers\ArrayHelper;

class Order extends \Integrasi\Contracts\DocoImplement
{

    protected $_pasienkirimkeunitlain_id;

    protected $_pasienmasukpenunjang_id;

    protected $_pendaftaran_id;

    public function init()
    {
        $this->_pasienkirimkeunitlain_id = $this->pasienkirimkeunitlain_id;
        $this->_pasienmasukpenunjang_id = $this->pasienmasukpenunjang_id;
        $this->_pendaftaran_id = $this->pendaftaran_id;
        $resultRegister = ArrayHelper::getValue($this->result,'id');
        if (!empty($resultRegister)) {
            $this->_pendaftaran_id = DocoHelpers::decrypt($resultRegister);
        }
    }

    public function execute()
    {
        $this->init();
        $payloadData = $this->getData();
        $response = $penunjangModel = '';
        if (!empty($payloadData)) {
            $insertLog = $listOrderId = [];
            foreach ($payloadData as $value) {
                $orderId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
                // Prevent double sending to servon 
                if (in_array($orderId, $listOrderId)) continue;

                $listOrderId[] = $orderId;
                // decode it first
                $clinical_info = json_decode($value['clinical_info']);
                $value['clinical_info'] = !empty($clinical_info->text) ? $clinical_info->text : '';
                $value['tests'] = json_decode($value['tests']);

                $is_sent = false;
                $id_sync_sercon = null;
                try {
                    $response = (new RocheService)->order($value, function ($data) {
                        return $data;
                    }, 'POST');
                    $result = ArrayHelper::getValue($response, 'Results.0.data.Data');
                    $id_sync_sercon = ArrayHelper::getValue($result, 'StatusId');
                    $is_sent = ArrayHelper::getValue($result, 'IsResponded', false);
                } catch (\Exception $e) {
                    $response = [
                        'Message' => $e->getMessage(),
                        'File' => $e->getFile(),
                        'Line' => $e->getLine(),
                    ];
                }

                $insertLog[] = [
                    'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                    'pasienmasukpenunjang_id' => $orderId,
                    'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                    'payload' => json_encode($value),
                    'is_sent' => $is_sent,
                    'is_sending' => true,
                    'id_sync_sercon' => $id_sync_sercon,
                    'sync_respon' => json_encode($response),
                ];
            }

            if (!empty($insertLog)) {
                IntegrasiRoche::batchInsert($insertLog);
            }
        }

        // save to log workers
        return json_encode([
            'service' => 'Roche-Order',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getData()
    {
        if (empty($this->_pasienkirimkeunitlain_id) 
                && empty($this->_pasienmasukpenunjang_id)
                && empty($this->_pendaftaran_id)) return [];

        $data = BridgingOrderLabRocheView::find()
            ->select([
                'patient_id',
                'patient_name',
                'date_of_birth',
                'gender',
                'address',
                'patient_class',
                'location_id',
                'location_name',
                'case_no',
                'order_no',
                'order_time',
                'ref_doctor_id',
                'ref_doctor_name',
                'priority',
                'clinical_info',
                'tests',
                'pendaftaran_id',
                'pasienmasukpenunjang_id',
                'pasienkirimkeunitlain_id',
            ])->andWhere(['is_bayar'=>true]);

        if ($this->_pasienkirimkeunitlain_id) {
            $data->andWhere(['pasienkirimkeunitlain_id' => $this->_pasienkirimkeunitlain_id]);
        }

        if ($this->_pasienmasukpenunjang_id) {
            $data->andWhere(['pasienmasukpenunjang_id' => $this->_pasienmasukpenunjang_id]);
        }

        if ($this->_pendaftaran_id) {
            $data->andWhere(['pendaftaran_id' => $this->_pendaftaran_id]);
        }

        return $data->asArray()->all();
    }
}