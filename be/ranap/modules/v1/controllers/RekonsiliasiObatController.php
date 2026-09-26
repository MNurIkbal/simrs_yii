<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-06-12 09:43:30
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-06-26 16:50:29
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use app\modules\v1\models\RekonsiliasiObat;
use app\modules\v1\models\RekonsiliasiObatView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\StokObatPasien;
use app\modules\v1\models\PegawaiView;

class RekonsiliasiObatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\RekonsiliasiObat';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $listRekons = $request->post();
            $saved = false;
            if ($listRekons) {
                foreach ($listRekons as $key => $rekons) {
                    if ($rekons['rekonsiliasiobat_id']) continue;
                    
                    unset($rekons['rekonsiliasiobat_id']);
                    $model = new RekonsiliasiObat;
                    $model->attributes = $rekons;
                    // return $model->attributes;
                    if (!$model->save()) {

                        // // save to stokobatpasien_r
                        // $modelObatPasien = new StokObatPasien;
                        // $modelObatPasien->rekonsiliasiobat_id = $model->rekonsiliasiobat_id;
                        // $modelObatPasien->obatalkes_id = $model->obatalkes_id;
                        // $modelObatPasien->nama_obat = $model->nama_obat;
                        // $modelObatPasien->satuan_kecil = $model->satuan_kecil;
                        // $modelObatPasien->stok_layak = $model->qty

                        $transaction->rollback();
                        return [
                            'data' => $model->errors,
                            'status' => 422,
                        ];
                    }
                }
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                $transaction->rollback();
                return [
                    'data' => 'No data',
                    'status' => 422,
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }



    public function actionUpdateKeputusanRekon()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $listRekons = $request->post();
            $saved = false;
            if ($listRekons) {
                foreach ($listRekons as $id_rekon => $rekon) {
                    
                    $model = RekonsiliasiObat::findOne($id_rekon);
                    $model->is_lanjut = $rekon['is_lanjut'];
                    $model->catatan = $rekon['catatan'];
                    $model->tgl_keputusan = date('Y-m-d H:i:s');
                    $model->dokter_id = $_GET['pegawai_id'];
                    // return $model->attributes;
                    if (!$model->save(false)) {
                        $transaction->rollback();
                        return [
                            'data' => $model->errors,
                            'status' => 422,
                        ];
                    }
                }
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                $transaction->rollback();
                return [
                    'data' => 'No data',
                    'status' => 422,
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }


    public function actionUpdateKelayakanRekon()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $listRekons = $request->post();
            $saved = false;
            
            if ($listRekons) {
                foreach ($listRekons as $id_rekon => $rekon) {
                    $model = RekonsiliasiObat::findOne($id_rekon);
                    $model->qty_layak = $rekon['qty_layak'];
                    $model->qty_tidaklayak = $rekon['qty_tidaklayak'];
                    $model->terapi = $rekon['terapi'];
                    $model->signaterapi = $rekon['signaterapi'];
                    $model->rute_kelayakan = $rekon['rute_kelayakan'];
                    $model->tgl_kelayakan = date('Y-m-d H:i:s');
                    $model->apoteker_id = $_GET['pegawai_id'];
                    // return $model->attributes;
                    if (!$model->save(false)) {

                        $transaction->rollback();
                        return [
                            'data' => $model->errors,
                            'status' => 422,
                        ];
                    }

                    if ($model->qty_layak != 0) {
                        // save to stokobatpasien_r
                        $modelObatPasien = new StokObatPasien;
                        $modelObatPasien->rekonsiliasiobat_id = $model->rekonsiliasiobat_id;
                        $modelObatPasien->obatalkes_id = $model->obatalkes_id;
                        $modelObatPasien->nama_obat = $model->nama_obat;
                        $modelObatPasien->satuan_kecil = $model->satuan_kecil;
                        $modelObatPasien->stok_layak = $model->qty_layak;
                        $modelObatPasien->stok_sisa = $model->qty_layak;
                        
                        $modelObatPasien->save(false);
                    }
                }
                
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                $transaction->rollback();
                return [
                    'data' => 'No data',
                    'status' => 422,
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function actionSaveObatRekon()
    {
        $connection = Yii::$app->db;
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new ObatAlkes;
            $model->obatalkes_nama = trim($post['ObatAlkesForm']['obatalkes_nama']);
            $model->obatalkes_kode = trim($post['ObatAlkesForm']['obatalkes_kode']);

            // default
            $model->jenisobatalkes_id = 3; // obat
            $model->satuankecil_id = DocoConstants::SATUAN_BUAH;
            $model->satuansedang_id = DocoConstants::SATUAN_BUAH;
            $model->satuanbesar_id = DocoConstants::SATUAN_BUAH;
            $model->kemasan_sedang = 1;
            $model->kemasan_besar = 1;
            $model->is_generik = true;
            $date = strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . "+2 months");
            $kadaluarsa = date('Y-m-d', $date);
            $model->tglkadaluarsa = $kadaluarsa;
            $model->minimalstok = 0;
            $model->maksimalstok = 0;
            $model->harga_beli = 0;
            $model->discount = 0;
            $model->harganetto = 0;

            if ($model->validate()) {
                if (!$model->save(false)) {
                    return [
                        'data' => $model->errors,
                        'status' => 422,
                    ];
                }
                return [
                    'status' => 200,
                    'message' => 'sukses',
                    'obatalkes_id' => $model->obatalkes_id,
                    'obatalkes_nama' => $model->obatalkes_nama,
                ];
            } else {
                return [
                    'data' => '',
                    'status' => 422,
                ];
            }

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }



    /**
    * @controller actionExportPdf 
    * @attribute #data_obat# => table 
    * @attribute #tgl_rekon# => table 
    * @attribute #nama_pasien# => table 
    * @attribute #tgl_keputusan# => table 
    * @attribute #dpjp# => table 
    * @attribute #tgl_kelayakan# => table 
    * @attribute #apoteker# => table 
    * @attribute #nama_pegawai# => table 
    * @attribute #tgl_cetak# => table 
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;

        $modelHeader = new InfoPasienRanap;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id'=>$request->get('pendaftaran_id'),
                'pasienadmisi_id'=>$request->get('pasienadmisi_id'),
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $model = new RekonsiliasiObatView;
        $query = $model::find()
            ->andWhere([
                'pendaftaran_id'=>$request->get('pendaftaran_id'),
                'pasienadmisi_id'=>$request->get('pasienadmisi_id'),
            ]);
        $result = $query->asArray()->all();

        $pegawai = PegawaiView::find()->where(['pegawai_id'=>$request->get('pegawai_id')])->one();

        // Directory Creation
        $header1 = array(
            Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
            Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
            Yii::t('app', "Ruangan / kelas") => $resultHeader ? $resultHeader['ruangan_nama'] . ' / ' . $resultHeader['kelas_pelayanan'] : '',
            Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            Yii::t('app', "Penjamin") => $resultHeader ? $resultHeader['penjamin_nama'] : '',
        );
        
        $alergi = [
            '0' => 'Tidak tahu',
            '1' => 'Tidak',
            '2' => 'Ya',
        ];
        $hamil = [
            '0' => 'Tidak',
            '1' => 'Ya',
        ];
        $informasi = [
            '0' => 'Pasien',
            '1' => 'Keluarga',
            '2' => 'Lain-lain',
        ];
        $header2 = array(
            Yii::t('app', "Alergi") => $result ? $alergi[$result[0]['is_alergi']] : '',
            Yii::t('app', "Obat penyebab alergi") => $result ? $result[0]['obat_alergi'] : '',
            Yii::t('app', "Hamil / Menyusui") => $result ? $hamil[$result[0]['is_hamil']] : '',
            Yii::t('app', "Sumber informasi") => $result ? $informasi[$result[0]['sumber_informasi']] : '',
        );
        // return $this->renderPartial('pdf',['header1'=>$header1, 'header2'=>$header2, 'detail'=>$result]);
        
        $print = new DocoPrint();
        $print->attributes = [
            '#data_obat#' => $this->renderPartial('pdf',['header1'=>$header1, 'header2'=>$header2, 'detail'=>$result]),
            '#tgl_rekon#' => $result ? date('d F Y', strtotime($result[0]['created_date'])) : '',
            '#nama_pasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#dpjp#' => $result ? $result[0]['dokter_nama'] : '',
            '#tgl_keputusan#' => $result ? date('d F Y', strtotime($result[0]['tgl_keputusan'])) : '',
            '#apoteker#' => $result ? $result[0]['apoteker_nama'] : '',
            '#tgl_kelayakan#' => $result ? date('d F Y', strtotime($result[0]['tgl_kelayakan'])) : '',
            '#nama_pegawai#' => $pegawai ? $pegawai->nama_pegawai : '',
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }  


}

