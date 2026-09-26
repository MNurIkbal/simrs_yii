<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanDarahPmi;
use app\modules\v1\models\PesanDarahPmiDetail;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\JenisDarah;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoPesanDarahPmiView;
use app\modules\v1\models\InfoPesanDarahPmiDetailView;
use app\modules\v1\models\PegawaiView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class PemesananDarahController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\PesanDarahPmi';
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

    public function actionGetDataRequest($jenisdarah_id = null, $golongandarah_id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $namaPmi = Supplier::find()->where(['is_active' => true, 'is_pmi' => true])->asArray()->all();
        $jenisDarah = JenisDarah::find()->where(['is_active' => true])->asArray()->all();
        $golDarah = Lookup::find()->where(['is_active' => true, 'lookup_type' => 'golongan_darah'])->asArray()->all();
        
        $dataJenisDarah = '';
        $dataGolDar = '';
        if(!empty($jenisdarah_id) && !empty($golongandarah_id)) {
            $dataJenisDarah = Jenisdarah::findOne($jenisdarah_id);
            $dataGolDar = Lookup::findOne($golongandarah_id);
        }   
             
        return [
            'pmi' => $namaPmi,
            'jenis_darah' => $jenisDarah,
            'gol_darah' => $golDarah,
            'dataJenisDarah' => $dataJenisDarah,
            'dataGolDar' => $dataGolDar,
        ];
    }

    public function actionGetDataPmi($supplier_id)
    {
        $data = Supplier::findOne($supplier_id);

        return $data;
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = $no_pesandarahpmi = null;
            $request = Yii::$app->request;
            $dataJson = $request->post('data',"{}");
            $data = json_decode($dataJson,true);
            // Insert Ke teransakasi pemesanan darah
            $model = new PesanDarahPmi;
            $model->tgl_pesandarahpmi = date('Y-m-d H:i:s');
            $model->ruanganpemesan_id = $request->post('ruangan_id');
            $model->supplier_id = $request->post('supplier_id');
            $model->total_harga = $request->post('total_harga');
            $model->total_kantongdarah = $request->post('total_kantongdarah');
            if ($model->validate() && $model->save(false)) {
                $dataInsert = [];
                $idParent = $model->pesandarahpmi_id;
                $no_pesandarahpmi = $model->no_pesandarahpmi;
                foreach ($data as $key => $value) {
                    $dataInsert[] = [
                        'pesandarahpmi_id' => $idParent,
                        'jenisdarah_id' => $value['jenisdarah_id'],
                        'golongandarah_id' => $value['golongandarah_id'],
                        'rhesus' => ($value['rhesus'] == "Positif") ? 1 : 0,
                        'tgl_mintakirim' => $value['tgl_mintakirim'],
                        'wkt_mintakirim' => $value['wkt_mintakirim'],
                        'qty_pesan' => $value['jumlah'],
                        'qty_sisa' => $value['jumlah'],
                        'harga_satuan' => $value['harga'],
                        'sub_total' => $value['subTotal'],
                    ];
                }

                // Insert Ke teransakasi pemesanan darah detail
                PesanDarahPmiDetail::batchInsert($dataInsert, false);
                $modelPmi = PesanDarahPmi::findOne($idParent);
                $transaction->commit();
                return [
                    'message' => 'sukses',
                    'id_parent' => $idParent,
                    'no_pesandarahpmi' => $modelPmi->no_pesandarahpmi
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
    * @attribute #no_pesandarahpmi# => nomor pesan
    * @attribute #title# => title
    * @attribute #alamat# => alamat
    * @attribute #no_tlp# => no_tlp
    * @attribute #tanggal# => tanggal
    * @attribute #jabatan# => jabatan
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #namaRs# => namaRs
    * @attribute #alamatRs# => alamatRs
    */
   
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $no_pesandarahpmi = $request->get('no_pesandarahpmi');
        $namaRs = $request->get('nama_rs');
        $alamatRs = $request->get('alamat_rs');
        $title = 'Pemesanan Darah '.$namaRs;
        $model = new InfoPesanDarahPmiView;
        $header = $model::find()->where(['no_pesandarahpmi' => $no_pesandarahpmi])->one();
        $detail = InfoPesanDarahPmiDetailView::find()
            ->where(['pesandarahpmi_id' => $header->pesandarahpmi_id])
            ->asArray()->all();

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $pegawai = PegawaiView::find()->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])->one();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#no_pesandarahpmi#' => $no_pesandarahpmi,
            '#nama_pmi#' => $header->supplier_nama,
            '#alamat#' => $header->supplier_alamat,
            '#no_tlp#' => $header->no_tlp,
            '#title#' => $title,
            '#tanggal#' => date('d-M-Y'),
            '#jabatan#' => isset($pegawai) ? $pegawai->jabatan_nama : "",
            '#nama_pegawai#' => isset($pegawai) ? $pegawai->nama_pegawai : "",
            '#namaRs#' => $namaRs,
            '#alamatRs#' => $alamatRs,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $detail,
                'total_harga' => $header->total_harga,
                'total_kantongdarah' => $header->total_kantongdarah,
            ]),
        ];

        $print->Output();
    }
}