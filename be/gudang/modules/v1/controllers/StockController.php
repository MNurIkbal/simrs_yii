<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use GuzzleHttp\Exception\RequestException;
use SirsCore\features\IntegrasiAkunting;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;

use app\modules\v1\models\StokObatAlkes;

use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\PenerimaanObatDetail;
use app\modules\v1\models\SatuanKonversiView;

use Doco\Services\StockService;

class StockController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\StokObatAlkes';

    public function actions(){
        return [
            'stock-out' => 'app\modules\v1\actions\Stock\StockOutAction'
        ];
    }

    public function actionStockIn(){
        $details = Yii::$app->request->post('details', []);

        try {
            $payload = $this->mappingStokObatAlkes($details);
            if(count($payload) <= 0){
                \Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => 'failed',
                    'text' => 'Detail obat harus diisi'
                ];
            }

            if(is_array($payload) && isset($payload['message'])){
                return $payload;
            }

            StokObatAlkes::batchInsert($payload, true);

            return [
                'status' => 200,
                'message' => 'success',
                'text' => 'Stok berhasil di update'
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function mappingStokObatAlkes($data)
    {
        // validasi kelengkapan attributes
        $validateData = $this->validateData($data);
        if(count($validateData) > 0){
            \Yii::$app->response->statusCode = 422;
            return [
                'status' => 422,
                'message' => 'failed',
                'text' => 'error validasi',
                'data' => $validateData
            ];
        }

        // get field name sumber stok in
        $stockInKey = $this->getStockInKey($data[0]);
        if($stockInKey == null){
            \Yii::$app->response->statusCode = 422;
            return [
                'status' => 422,
                'message' => 'failed',
                'text' => 'ID stok in tidak ditemukan'
            ];
        }

        $arr_harga_netto_avg = $this->findHargaNettoAvg($data);
        $dateNow = date('Y-m-d H:i:s');

        if($stockInKey == 'penerimaanobatdetail_id'){
            $total_persediaan = $this->getTotalPersediaan($data);
        }

        foreach($data as $key => $value) {
            $harga_netto_avg = $arr_harga_netto_avg[$value['obatalkes_id']];

            $payload[] = [
                $stockInKey     => $value[$stockInKey],
                'tglstok_in'    => isset($value['tglstok_in']) ? $value['tglstok_in'] : $dateNow,
                'tglterima'     => isset($value['tglterima']) ? $value['tglterima'] : null,
                'stokoa_aktif'  => true,
                'ruangan_id'    => $value['ruangan_id'],
                'obatalkes_id'  => $value['obatalkes_id'],
                'satuankecil_id'=> $value['satuankecil_id'],
                'tglkadaluarsa' => $value['tglkadaluarsa'],
                'qtystok_in'    => $value['qty_kecil'],
                'qtystok_out'   => 0,
                'persendiscount'=> isset($value['persendiscount']) ? $value['persendiscount'] : 0,
                'jmldiscount'   => isset($value['jmldiscount']) ? $value['jmldiscount'] : 0,
                'jmlmargin'     => isset($value['jmlmargin']) ? $value['jmlmargin'] : 0,
                'jmlppn'        => isset($value['jmlppn']) ? $value['jmlppn'] : 0,
                'persenppn'     => isset($value['persenppn']) ? $value['persenppn'] : 0,
                'persenpph'     => isset($value['persenpph']) ? $value['persenpph'] : 0,
                'persenmargin'  => isset($value['persenmargin']) ? $value['persenmargin'] : 0,
                'harganetto'    => isset($value['harganetto']) ? $value['harganetto'] : 0,
                'harga_netto_avg' => isset($harga_netto_avg) ? $harga_netto_avg : 0,
                'nobatch'       => isset($value['nobatch']) ? $value['nobatch'] : 0,
                'total_persediaan' => isset($total_persediaan[$value['obatalkes_id']]) ? $total_persediaan[$value['obatalkes_id']] : 0,
                'obatalkespasien_id' => isset($value['obatalkespasien_id']) ? $value['obatalkespasien_id'] : null
            ];
        }

        return $payload;
    }

    private function validateData($data) {
        foreach ($data as $key => $value) {
            $model = new StokObatAlkes;
            $model->attributes = $value;
            if(!$model->validate()){
                return $model->errors;
            }
        }
    }

    private function getStockInKey($data) {
        $stockInKeys = [
            'penerimaansuppdetail_id',
            'adjusmenobatmasuk_id',
            'stokopnamedetail_id',
            'penerimaanobatdetail_id',
            'terimamutasidetail_id',
            'returpenerimaanobatdetail_id',
            'returresepdetail_id',
            'pembatalanresep_id'
        ];

        $key = array_intersect($stockInKeys, array_keys($data));
        if(count($key) > 0) {
            return array_pop($key);
        } else {
            return null;
        }
    }

    private function getTotalPersediaan($data) {
        $arr_qty_diterima = $arr_nilai_konversi = $arr_total_persediaan = [];
        $arr_penerimaanobatdetail_id = array_column($data, 'penerimaanobatdetail_id');
        $penerimaanDetail = PenerimaanObatDetail::find()->where([
                                'IN', 'penerimaanobatdetail_id', $arr_penerimaanobatdetail_id
                            ])->asArray()->all();
        $arr_validasipoobatdetail_id = array_column($penerimaanDetail, 'validasipoobatdetail_id');

        foreach ($penerimaanDetail as $key => $value) {
            $arr_qty_diterima[$value['obatalkes_id']] = $value['qty_diterima'];
        }

        $validasiPoDetail = ValidasiPoObatDetail::find()->where([
                                'IN', 'validasipoobatdetail_id', $arr_validasipoobatdetail_id
                            ])->asArray()->all();
        $arr_s_konversiobt_id = array_column($validasiPoDetail, 's_konversiobt_id');
        $satuanKonversi = SatuanKonversiView::find()->where([
                                'IN', 'satuankonversi_id', $arr_s_konversiobt_id
                            ])->asArray()->all();

        foreach ($satuanKonversi as $key => $value) {
            $arr_nilai_konversi[$value['obatalkes_id']] = $value['nilai_konversi'];
        }

        foreach ($validasiPoDetail as $key => $value) {
            $obatalkes_id = $value['obatalkes_id'];
            $harga_satuan = ($value['jumlah'] + $value['discount_rp']) / $value['qty_po'];
            $konversi_penerimaan = $arr_nilai_konversi[$obatalkes_id] * $arr_qty_diterima[$obatalkes_id];
            $arr_total_persediaan[$obatalkes_id] = $konversi_penerimaan * $harga_satuan;
        }

        return $arr_total_persediaan;
    }

    private function findHargaNettoAvg($data){
        $dataMasterObatAlkes=[];
        if(is_array($data) && count($data)>0){
            $inCondition = "(" . implode(",", array_column($data, 'obatalkes_id')) . ")";
            $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
            $dataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
        }
        if(is_array($dataMasterObatAlkes) && count($dataMasterObatAlkes)>0) $dataMasterObatAlkes = array_column($dataMasterObatAlkes, 'hargaratarata','obatalkes_id');

        return $dataMasterObatAlkes;
    }

    public function actionTestBaseService($type){
        $data = Yii::$app->request->post('data', '{}');
        try{
            $header = Yii::$app->request->post('header_transaksi', '{}');
            if($type == "out") {
                return (new StockService)->out($header, $data);
            } else {
                return (new StockService)->in($header, $data);
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}