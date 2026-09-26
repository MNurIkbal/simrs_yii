<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\PurchaseRequisitionBarang;
use app\modules\v1\models\PurchaseRequisitionBarangDetail;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;
use Doco\components\DocoConstants;
use SirsCore\features\ListKonversiItem;

class UpdatePRAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $header = $request->post('header');
        $type = $request->post('type');
        $details = $request->post('details');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $new_ids = [];

        try {
            if($type == 'obat') {
                $model = $this->updateMedis($header, $details);
            } else if ($type == 'barang') {
                $model = $this->updateNonMedis($header, $details);
            }

            // update process
            $model->reference = $header['reference'];
            $model->save();

            foreach ($details as $key => $value) {
                if($type == 'obat') {
                    $model_detail = $this->updateDetailMedis($header, $value, $type, $connection);
                } else if ($type == 'barang') {
                    $model_detail = $this->updateDetailNonMedis($header, $value, $type, $connection);
                }

                $model_detail->save();
            }

            $transaction->commit();

            return ['message' => 'OK'];
        } catch (\yii\db\Exception $e) {
            $this->controller->logError($e);
            $transaction->rollBack();
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->controller->logError($e);
            $transaction->rollBack();
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    private function updateMedis($header, $details)
    {
        $new_ids = [];
        $model = PurchaseRequisition::find()
                ->where(['purchasereq_id' => $header['purchasereq_id']])
                ->one();
            
        if(is_null($header))
            throw new \Exception("Data dengan id:{$header['purchasereq_id']} tidak ditemukan", 1);

        // delete data at purchasereqdetail_t if not included in new payload
        foreach ($details as $key => $value) {
            if(isset($value['purchasereqdetail_id'])) {
                $new_ids[] = $value['purchasereqdetail_id'];
            }
        }

        if(!empty($new_ids)) {
            $rows = PurchaseRequisitionDetail::find()
                        ->where(['not in', 'purchasereqdetail_id', $new_ids])
                        ->andWhere(['purchasereq_id' => $header['purchasereq_id']])
                        ->all();

            foreach ($rows as $row) {
                $row->delete();
            }
        }

        return $model;
    }

    private function updateNonMedis($header, $details)
    {
        $new_ids = [];
        $model = PurchaseRequisitionBarang::find()
                ->where(['purchasereqbrg_id' => $header['purchasereqbrg_id']])
                ->one();
            
        if(is_null($header))
            throw new \Exception("Data dengan id:{$header['purchasereqbrg_id']} tidak ditemukan", 1);

        // delete data at purchasereqdetail_t if not included in new payload
        foreach ($details as $key => $value) {
            if(isset($value['purchasereqbrgdetail_id'])) {
                $new_ids[] = $value['purchasereqbrgdetail_id'];
            }
        }
        
        if(!empty($new_ids)) {
            $rows = PurchaseRequisitionBarangDetail::find()
                        ->where(['not in', 'purchasereqbrgdetail_id', $new_ids])
                        ->andWhere(['purchasereqbrg_id' => $header['purchasereqbrg_id']])
                        ->all();
            
            foreach ($rows as $row) {
                $row->delete();
            }
        }

        return $model;
    }

    private function updateDetailMedis($header, $value, $type, $connection)
    {
        if(!isset($value['purchasereqdetail_id'])) {
            // Create additional data
            $data_obat = $connection->createCommand("
                SELECT
                    CONCAT(om.obatalkes_kode, ' - ', om.obatalkes_nama) AS nma_obat,
                    CONCAT(1,' ', TRIM(sm3.satuanunit_nama), ' = ', sm.nilai_konversi, ' ' ,TRIM(sm2.satuanunit_nama)) AS satuan,
                    sr.qty_sisa AS stok
                FROM obatalkes_m AS om
                JOIN stokobatalkes_r sr ON om.obatalkes_id = sr.obatalkes_id
                JOIN satuankonversi_m sm ON om.obatalkes_id = sm.obatalkes_id
                JOIN satuanunit_m sm2 ON sm.satuankecil_id = sm2.satuanunit_id
                JOIN satuanunit_m sm3 ON sm.satuanbesar_id = sm3.satuanunit_id
                WHERE om.obatalkes_id = '" . $value['obatalkes_id'] . "'
                AND sm3.satuanunit_id = '" . $value['satuan_id'] . "'
            ")->queryOne();

            $additional_data = [
                'catatan' => $value['catatan'],
                'obatalkes_id' => $value['obatalkes_id'],
                'qty' => $value['qty'],
                'satuaninput_id' =>  $value['satuan_id']
            ];

            if($data_obat != false) {
                $additional_data = array_merge($additional_data, $data_obat);
            }

            $model_detail = new PurchaseRequisitionDetail;
            $model_detail->purchasereq_id   = $header['purchasereq_id'];
            $model_detail->additional_data  = json_encode($additional_data);
            $model_detail->status  = DocoConstants::VAR_BELUM_APPROVED; //default Belum Approved
            $model_detail->stok_gudang = isset($value['stok_gudang']) ? $value['stok_gudang'] : null;
            $model_detail->stok_farmasi = isset($value['stok_farmasi']) ? $value['stok_farmasi'] : null;
            $model_detail->stok_ruanganlain = isset($value['stok_lain']) ? $value['stok_lain'] : null;
        } else {
            $model_detail = PurchaseRequisitionDetail::find()
                        ->where(['purchasereqdetail_id' => $value['purchasereqdetail_id']])
                        ->one();
        }

        $satuan_konversi = $this->getSatuanKonversi(
            $type,
            ArrayHelper::getValue($value,'obatalkes_id'),
            ArrayHelper::getValue($value,'satuan_id'),
            ArrayHelper::getValue($value,'qty')
        );

        $model_detail->obatalkes_id = $value['obatalkes_id'];
        $model_detail->qty_input = $value['qty'];
        $model_detail->qty_pr = $value['qty'];
        $model_detail->qty_saatini = $value['stok'];
        $model_detail->satuan_id = $value['satuan_id'];
        $model_detail->qty_konversi = $satuan_konversi['qty_konversi'];
        $model_detail->satuankonversi_id = $satuan_konversi['satuankonversi_id'];
        $model_detail->catatan = $value['catatan'];

        return $model_detail;
    }

    private function updateDetailNonMedis($header, $value, $type, $connection)
    {
        if(!isset($value['purchasereqbrgdetail_id'])) {
            // Create additional data
            $data_obat = $connection->createCommand("
                SELECT
                    CONCAT(om.barang_kode, ' - ', om.barang_nama) AS nma_obat,
                    CONCAT(1,' ', TRIM(sm3.satuanunit_nama), ' = ', sm.nilai_konversi, ' ' ,TRIM(sm2.satuanunit_nama)) AS satuan,
                    sr.qty_sisa AS stok
                FROM barang_m AS om
                JOIN stokbarang_r sr ON om.barang_id = sr.barang_id
                JOIN satuankonversibrg_m sm ON om.barang_id = sm.barang_id
                JOIN satuanunit_m sm2 ON sm.satuankecil_id = sm2.satuanunit_id
                JOIN satuanunit_m sm3 ON sm.satuanbesar_id = sm3.satuanunit_id
                WHERE om.barang_id = '" . $value['barang_id'] . "'
                AND sm3.satuanunit_id = '" . $value['satuan_id'] . "'
            ")->queryOne();

            $additional_data = [
                'catatan' => $value['catatan'],
                'barang_id' => $value['barang_id'],
                'qty' => $value['qty'],
                'satuaninput_id' =>  $value['satuan_id']
            ];

            if($data_obat != false) {
                $additional_data = array_merge($additional_data, $data_obat);
            }

            $model_detail = new PurchaseRequisitionBarangDetail;
            $model_detail->purchasereqbrg_id   = $header['purchasereqbrg_id'];
            $model_detail->status  = DocoConstants::VAR_BELUM_APPROVED; //default Belum Approved
            $model_detail->additional_data  = json_encode($additional_data);
        } else {
            $model_detail = PurchaseRequisitionBarangDetail::find()
                        ->where(['purchasereqbrgdetail_id' => $value['purchasereqbrgdetail_id']])
                        ->one();
        }

        $satuan_konversi = $this->getSatuanKonversi(
            $type,
            ArrayHelper::getValue($value,'barang_id'),
            ArrayHelper::getValue($value,'satuan_id'),
            ArrayHelper::getValue($value,'qty')
        );
        
        $model_detail->barang_id = $value['barang_id'];
        $model_detail->qty_input = $value['qty'];
        $model_detail->qty_pr = $value['qty'];
        $model_detail->qty_saatini = $value['stok'];
        $model_detail->satuan_id = $value['satuan_id'];
        $model_detail->qty_konversi = $satuan_konversi['qty_konversi'];
        $model_detail->satuankonversi_id = $satuan_konversi['satuankonversi_id'];
        $model_detail->catatan = $value['catatan'];

        return $model_detail;
    }

    private function getSatuanKonversi(
        $type, 
        $item_id, 
        $satuan_id, 
        $qty_input
    ) {
        $cacheDuration = 60 * 60 * 12; // set for 12 hours
        $listKonversi = ListKonversiItem::getData($type, $cacheDuration, true);

        if(!isset($listKonversi[$item_id][$satuan_id])) {
            if ($type == "obat") {
                $itemData = ObatAlkes::find()->where(['obatalkes_id' => $item_id])->one();
                $itemName = $itemData->obatalkes_nama;
            } else {
                $itemData = Barang::find()->where(['barang_id' => $item_id])->one();
                $itemName = $itemData->barang_nama;
            }

            throw new \Exception("Satuan Konversi Item " . $itemName . " tidak ada", 1);
        }

        $qty_konversi = 0;
        $satuankonversi_id = 0;

        if($listKonversi[$item_id][$satuan_id]['satuankonversi_id'] == $satuan_id) {
            $qty_konversi = $qty_input;
            $satuankonversi_id = $satuan_id;
        } else if($listKonversi[$item_id][$satuan_id]['satuanbesar_id'] == $satuan_id) {
            $nilai_konversi = $listKonversi[$item_id][$satuan_id]['nilai_konversi'];
            $qty_konversi = $qty_input * $nilai_konversi;
            $satuankonversi_id =  $listKonversi[$item_id][$satuan_id]['satuankonversi_id'];
        }

        return [
            'qty_konversi' => $qty_konversi,
            'satuankonversi_id' => $satuankonversi_id
        ];
    }
}
