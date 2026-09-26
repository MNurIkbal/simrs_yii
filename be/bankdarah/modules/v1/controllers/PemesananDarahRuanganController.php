<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanDarah;
use app\modules\v1\models\PesanDarahDetail;
use app\modules\v1\models\JenisDarah;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoPemesananDarahView;
use app\modules\v1\models\InfoPemesananDarahDetailView;
use app\modules\v1\models\InfoPasienMasihDirawatView;
use app\modules\v1\models\DokterView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class PemesananDarahRuanganController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\PesanDarah';
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        return $actions;
    }

    public function actionGetDataRequest($ruangan_id)
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $dataJenisDarah = [];
        $dokter = DokterView::find()
            ->where(['is_active' => true/*, 'ruangan_id' => $ruangan_id*/])
            ->asArray()->all();
        
        $jenisDarah = JenisDarah::find()
            ->where(['is_active' => true])->asArray()->all();
        
        $metodePengambilan = Lookup::find()
            ->where(['is_active' => true, 'lookup_type' => 'pengambilan_darah'])
            ->asArray()->all();
        
        return [
            'dokter' => $dokter,
            'jenis_darah' => $jenisDarah,
            'metode_pengambilan' => $metodePengambilan,
            'dataJenisDarah' => $dataJenisDarah
        ];
    }

    public function actionGetJenisDarah($jenisdarah_id)
    {
        $data = JenisDarah::findOne($jenisdarah_id);

        return $data;
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = $no_pesandarah = null;
            $request = Yii::$app->request;
            $dataJson = $request->post('data',"{}");
            $data = json_decode($dataJson,true);

            // Insert Ke teransakasi pemesanan darah
            $model = new PesanDarah;
            $model->tgl_pesandarah = date('Y-m-d H:i:s');
            $model->ruanganpemesan_id = $request->post('ruangan_id');
            $model->pasien_id = $request->post('pasien_id');
            $model->pendaftaran_id = $request->post('pendaftaran_id');
            $model->pasienadmisi_id = $request->post('pasienadmisi_id');
            $model->golongandarah_id = $request->post('golongandarah_id');
            $model->total_harga = $request->post('total_harga');
            $model->total_kantongdarah = $request->post('total_kantongdarah');
            $model->metode_pengambilan = $request->post('metode_pengambilan');
            $model->indikasi_transfusi = $request->post('indikasi_transfusi');
            $model->riwayat_transfusi = $request->post('riwayat_transfusi');
            $model->riwayat_kehamilan = $request->post('riwayat_kehamilan');
            $model->keterangan = $request->post('keterangan');

            if ($model->validate() && $model->save(false)) {
                $dataInsert = [];
                $idParent = $model->pesandarah_id;
                $no_pesandarah = $model->no_pesandarah;
                foreach ($data as $key => $value) {
                    $dataInsert[] = [
                        'pesandarah_id' => $idParent,
                        'jenisdarah_id' => $value['jenisdarah_id'],
                        'tgl_mintakirim' => $value['tgl_mintakirim'],
                        'wkt_mintakirim' => $value['wkt_mintakirim'],
                        'jumlah' => $value['jumlah'],
                        'harga_satuan' => $value['harga'],
                        'sub_total' => $value['subTotal'],
                        'status_pesan' => DocoConstants::SPD_BELUM_DIPROSES,
                    ];
                }

                // Insert Ke teransakasi pemesanan darah detail
                PesanDarahDetail::batchInsert($dataInsert, false);
                $modelPmi = PesanDarah::findOne($idParent);
                $transaction->commit();
                return [
                    'message' => 'sukses',
                    'id_parent' => $idParent,
                    'no_pesandarah' => $modelPmi->no_pesandarah
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #nama_pmi# => nama pmi
    * @attribute #no_pesandarah# => nomor pesan
    * @attribute #title# => title
    * @attribute #tgl_pesandarah# => tgl_pesandarah
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #diagnosa# => diagnosa
    * @attribute #umur# => umur
    * @attribute #jenis_kelamin# => jenis_kelamin
    * @attribute #golongandarah_nama# => golongandarah_nama
    * @attribute #metode_pengambilan# => metode_pengambilan
    * @attribute #indikasi_transfusi# => indikasi_transfusi
    * @attribute #kadar_hb# => kadar_hb
    * @attribute #riwayat_transfusi# => riwayat_transfusi
    * @attribute #riwayat_kehamilan# => riwayat_kehamilan
    * @attribute #total_harga# => total_harga
    * @attribute #keterangan# => keterangan
    */
   
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $no_pesandarah = $request->get('no_pesandarah');
        $namaRuangan = $request->get('ruangan_nama');
        $title = 'Pemesanan Darah Ruangan '.$namaRuangan;
        $model = new InfoPemesananDarahView;
        $header = $model::find()->where(['no_pesandarah' => $no_pesandarah])->one();
        $detail = InfoPemesananDarahDetailView::find()
            ->where(['pesandarah_id' => $header->pesandarah_id])
            ->asArray()->all();

        $dataPasien = InfoPasienMasihDirawatView::find()
            ->where(['pasien_id' => $header->pasien_id, 'pendaftaran_id' => $header->pendaftaran_id])
            ->one();
        
        $diagnosa = isset($dataPasien->diagnosa) ? json_decode($dataPasien->diagnosa, true) : "";
        $diagnosa = !empty($diagnosa) ? $diagnosa['text'] : "-";
        
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#no_pesandarah#' => $no_pesandarah,
            '#tgl_pesandarah#' => date('d-M-Y', strtotime($header->tgl_pesandarah)),
            '#nama_pasien#' => $dataPasien->nama_pasien,
            '#no_rekam_medik#' => $dataPasien->no_rekam_medik,
            '#diagnosa#' => $diagnosa,
            '#umur#' => $dataPasien->umur,
            '#jenis_kelamin#' => $dataPasien->jenis_kelamin,
            '#golongandarah_nama#' => $dataPasien->golongandarah_nama,
            '#metode_pengambilan#' => $header->metode_pengambilan_nama,
            '#indikasi_transfusi#' => $header->indikasi_transfusi,
            '#riwayat_transfusi#' => $header->riwayat_transfusi,
            '#riwayat_kehamilan#' => $header->riwayat_kehamilan,
            '#keterangan#' => $header->keterangan,
            '#kadar_hb#' => $dataPasien->kadar_hb,
            '#total_harga#' => DocoHelpers::formatNumber($header->total_harga),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $detail,
            ]),
        ];

        $print->Output();
    }

    public function actionGetDataPasien()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $dokter_id = $get['dokter_id'];
        $ruangan_id = $get['ruangan_id'];
        $term = $get['term'];
        $query = InfoPasienMasihDirawatView::find()
            ->where(['pegawai_id' => $dokter_id, 'ruangan_id' => $ruangan_id]);
        
        if($term) {
            $query->andFilterWhere(['or',
            ['like','LOWER(no_rekam_medik)',strtolower($term)],
            ['like','LOWER(nama_pasien)',strtolower($term)]]);
        }

        return $query->limit(10)->asArray()->all();
    }
}