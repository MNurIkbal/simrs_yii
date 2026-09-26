<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\ClosingKasir;
use app\modules\v1\models\RincianClosing;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\PenerimaanUmum;
use app\modules\v1\models\PengeluaranUmum;
use app\modules\v1\models\Shift;
use Doco\components\DocoPrint;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayloadFile;
use Doco\Services\InternalService;
use app\modules\v1\models\ClosingKasirView;
use app\modules\v1\models\UploadForm;

class TraClosingKasirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ClosingKasir';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["view"] = ["GET"];
        $verbs["create"] = ["POST"];
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
        $result = [];
        $request = Yii::$app->request;
        $post = $request->post();
        if ($post) {

            $modelClosingKasir = new ClosingKasir;
            $modelClosingKasir->attributes = $post['ClosingKasir'];
            $modelClosingKasir->tgl_closingkasir = date("Y-m-d H:i:s");
            $modelClosingKasir->closing_dari = date("Y-m-d H:i:s");
            $modelClosingKasir->sampai_dengan = date("Y-m-d H:i:s");
            $modelClosingKasir->jumlah_transaksi = 0;
            $modelClosingKasir->shift_id = Shift::getCurrentShiftId();
            
            if($modelClosingKasir->save()) {
                $result[] = $modelClosingKasir;
                $closingkasir_id = $modelClosingKasir->closingkasir_id;

                if(isset($post['RincianClosing']))
                    foreach($post['RincianClosing'] as $row) {
                        $modelRincianClosing = new RincianClosing;
                        $nilaiuang = @$row['nilaiuang'] ? $row['nilaiuang'] : 0;
                        $banyakuang = @$row['banyakuang'] ? $row['banyakuang'] : 0;
                        $jumlahuang = $nilaiuang * $banyakuang;
                        $modelRincianClosing->nilaiuang = $nilaiuang;
                        $modelRincianClosing->banyakuang = $banyakuang;
                        $modelRincianClosing->jumlahuang = $jumlahuang;
                        $modelRincianClosing->closingkasir_id = $closingkasir_id;
                        $modelRincianClosing->save();
                        $result[] = $modelRincianClosing;
                    }

                if(isset($post['TandaBuktiBayar'])) {
                    foreach($post['TandaBuktiBayar'] as $row) {
                        $modelTandaBuktiBayars = TandaBuktiBayar::find(true)
                            ->where(['tandabuktibayar_id' => $row['tandabuktibayar_id']])
                            ->all();

                        foreach ($modelTandaBuktiBayars as $modelTandaBuktiBayar) {
                            $modelTandaBuktiBayar->closingkasir_id = $closingkasir_id;
                            $modelTandaBuktiBayar->update(false);
                            $result[] = $modelTandaBuktiBayar;
                        }
                    }

                    // foreach($post['TandaBuktiBayar'] as $row) {
                        // $modelPenerimaanUmums = PenerimaanUmum::find()
                            // ->where(['tandabuktibayar_id' => $row['tandabuktibayar_id']])
                            // ->all();

                        // foreach ($modelPenerimaanUmums as $modelPenerimaanUmum) {
                            // $modelPenerimaanUmum->closingkasir_id = $closingkasir_id;
                            // $modelPenerimaanUmum->update(false);
                            // $result[] = $modelPenerimaanUmum;
                        // }
                    // }

                    // foreach($post['TandaBuktiBayar'] as $row) {
                        // $modelPengeluaranUmums = PengeluaranUmum::find()
                            // ->where(['tandabuktibayar_id' => $row['tandabuktibayar_id']])
                            // ->all();

                        // foreach ($modelPengeluaranUmums as $modelPengeluaranUmum) {
                            // $modelPengeluaranUmum->closingkasir_id = $closingkasir_id;
                            // $modelPengeluaranUmum->update(false);
                            // $result[] = $modelPengeluaranUmum;
                        // }
                    // }
                }

            } else {
                return $modelClosingKasir->errors;
            }
        }
        return $result;
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_closingkasir# => table 
    **/
    public function actionExportPdf($closingkasir_id)
    {
        $model = new ClosingKasir;
        $query = $model::find()
            ->andWhere(['closingkasir_t.closingkasir_id'=>$closingkasir_id])
            ->joinWith('rincianClosing')
            ->joinWith([
                'tandaBuktiBayar', 
                'tandaBuktiBayar.pembayaranPelayanan',
                'tandaBuktiBayar.pembayaranPelayanan.pendaftaran',
                'tandaBuktiBayar.pembayaranPelayanan.pendaftaran.pasien',
            ])
            ->joinWith('shift')
            ->asArray()->one();
            // return $query['tandaBuktiBayar'];
       
        $header = array(
            Yii::t('app', "No closing kasir") => $query['no_closingkasir'],
            Yii::t('app', "Tgl closing kasir") => date('d-m-Y', strtotime($query['tgl_closingkasir'])),
            Yii::t('app', "Shift kasir") => $query['shift']['shift_nama'],
        );

        $detail = [];
        foreach ($query['tandaBuktiBayar'] as $key => $value) {
            $newValue = [];
            $newValue['tanggal_pembayaran'] = date('d-m-Y', strtotime($value['tglbuktibayar']));
            $newValue['no_pendaftaran'] = $value['pembayaranPelayanan']['pendaftaran']['no_pendaftaran'];
            $newValue['nama_pasien'] = $value['pembayaranPelayanan']['pendaftaran']['pasien']['no_rekam_medik'];
            $newValue['total_pembayaran'] = $value['uangditerima'];
            $detail[$key] = $newValue;
        }
        $detailPecahan = [];
        foreach ($query['rincianClosing'] as $key => $value) {
            $newValue = [];
            $newValue['uang_pecahan'] = $value['nilaiuang'];
            $newValue['qty'] = $value['banyakuang'];
            $newValue['jumlah'] = $value['jumlahuang'];
            $detailPecahan[$key] = $newValue;
        }
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_closingkasir#' => $this->renderPartial('print_pdf',[
                'header'=> $header,
                'detail' => $detail,
                'detailPecahan' => $detailPecahan,
            ]),
        ];
        $print->Output();
    }

    public function loadData()
    {
        $user = Yii::$app->jwt;
        $userId = !empty($user->user->pegawai_id) ? $user->user->pegawai_id : null;
        $model = new ClosingKasirView;
        $query = $model::find();

        $query->andWhere(['pegawai1_id' => $userId]);
        $query->andWhere(['closingkasir_id' => null]);
        
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionSyncExportExcel() 
    {
        $user = Yii::$app->jwt;
        $userId = !empty($user->user->pegawai_id) ? $user->user->pegawai_id : null;
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $nama_pemakai = Yii::$app->jwt->user->nama_pemakai;
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->loadData()->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanTransaksiClosingKasirExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'userId' => $userId,
                    'data' => $data,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportTransaksiClosingKasir' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'nama_pemakai' => $nama_pemakai,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanTransaksiClosingKasirExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadPayloadFile;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file Excel berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Transaksi Closing Kasir.xlsx';
        if (file_exists($fileName)) 
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }
}