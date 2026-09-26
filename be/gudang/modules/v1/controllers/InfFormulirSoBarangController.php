<?php

/**
* @author yaya
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\LaporanFormSoBarang;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoFormSoBarang;
use app\modules\v1\models\InfoFormSoBarangDetail;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;

use app\modules\v1\businessLogic\StokOpname as LogicSO;
use app\modules\v1\models\InfoStokOpnameBarangDetailView;
use app\modules\v1\models\TambahStokOpnameBarangFn;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoStokOpnameBarangView;
use yii\base\DynamicModel;
use app\modules\v1\businessLogic\VerifikasiStokOpnameProses as VERIF_BSL_SO;
use app\modules\v1\models\KonfigGudang;
use app\modules\v1\entities\StokOpnameBarangDetail as EntSoDetail;
use app\modules\v1\models\StokOpnameBarang;
use Doco\components\DocoMessages;
use Doco\Services\InternalService;
use app\modules\v1\models\InfoFormSoBarangView;
use app\modules\v1\models\InfoFormSoBarangDetailView;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadPayload;
use Doco\Repositories\KonfigRepositories;
use app\modules\v1\models\Pegawai;

class InfFormulirSoBarangController extends DocoActiveController
{
    public $modelClass = InfoFormSoBarang::class;
    protected $_title = "Laporan Formulir Stok Opname Barang";

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "DELETE"];
        $verbs['list-barang-baru'] = ["GET"];
        $verbs['cek-barang-baru-so'] = ["POST","GET"];
        $verbs['detail-header'] = ["GET"];
        $verbs['get-data-detail-so'] = ["GET"];
        $verbs['export-pdf-detail'] = ["GET"];
        $verbs['verifikasi'] = ["PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            return new ActiveDataProvider([
                'query' => $this->dataProvider(),
            ]);

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }


    private function dataProvider()
    {
        $model = new LaporanFormSoBarang;

        $query = $model::find();

        $start = date('d-M-Y 00:00:00');
        $end = date('d-M-Y 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglformulir'])) {
                $tanggal_so = DocoHelpers::parsingRangeDate($_GET['advanced-filter']['tglformulir']);
                $start = $tanggal_so['startDate'];
                $end = $tanggal_so['endDate'];                
            }

            if(isset($_GET['advanced-filter']['status_so'])) {
                if($_GET['advanced-filter']['status_so'] == DocoConstants::BELUM_INPUT_HASIL){
                    $query->andWhere(['stokopnamebarang_id' => null]);
                }
                else if($_GET['advanced-filter']['status_so'] == DocoConstants::BELUM_VERIFIKASI){
                    $query->andWhere(['not',['stokopnamebarang_id' => null]]);
                    $query->andWhere(['pegawaiverifikasi' => null]);
                }
                else if($_GET['advanced-filter']['status_so'] == DocoConstants::SUDAH_VERIFIKASI){
                    $query->andWhere(['not',['pegawaiverifikasi' => null]]);
                }
            }

        }

        $query->andWhere(['between', 'tglformulir', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionView($id)
    {
        $header = InfoFormSoBarang::find()->where([
                    'formsobarang_id' => $id
                ])->asArray()->one();

        $detail = InfoFormSoBarangDetail::find()->where([
                    'formsobarang_id' => $id
                ])
                ->orderBy(['barang_nama' => SORT_ASC])
                ->asArray()
                ->all();

        $option = Lookup::find()->where([
            'lookup_type' => ['jenis_stokopname','kondisi_barang']
        ])->asArray()->all();

        $jenis_so = $kondisi_barang = [];

        foreach ($option as $value) {
            switch ($value['lookup_type']) {
                case 'jenis_stokopname':
                    $jenis_so[$value['lookup_id']] = $value['lookup_value'];
                    break;
                default:
                    $kondisi_barang[$value['lookup_name']] = $value['lookup_value'];
                    break;
            }
        }

        $data = [
            'header' => $header,
            'detail' => $detail,
            'opt_jenis_so' => $jenis_so,
            'opt_kondisi_barang' => $kondisi_barang,
        ];
        return $data;
    }

    public function actionSave($id) {
        $infoStok = InfoFormSoBarang::find()->where([
            'formsobarang_id' => $id
        ])->asArray()->one();

        if (!empty($infoStok) && !empty($infoStok['stokopnamebarang_id'])) {
            $result = (new LogicSO)->update($id);
        } else {
            $result = (new LogicSO)->execute($id, $infoStok);
        }
        return $result;
    }

    public function actionDelete($id)
    {
        $result = LogicSO::delete($id);
        return $result;
    }

    public function actionBeforePrint($id)
    {
        $formso = LaporanFormSoBarang::find()->where([
            'formsobarang_id' => $id
        ])->one();
        $detail = InfoFormSoBarangDetail::find()->where([
                    'formsobarang_id' => $id
                ])->asArray()->all();
        return [
            'header' => $formso,
            'detail' => $detail
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #ruangan# => Untuk Menampilkan Ruangan
    * @attribute #periode_stok# => Untuk Menampilkan periode Stok
    * @attribute #tanggal_stok_opname# => Untuk menampilkan tanggal Stok opname
    * @attribute #nomer_stok_opname# => Untuk menampilkan Nomer Stok Opname
    * @attribute #jenis_stok_opname# => Untuk menampilkan Jenis Stok Opame
    * @attribute #nomor_formulir# => Untuk menampilkan Jenis Stok Opame
    * @attribute #tabel_stok_opname# => Untuk menampilkan Detail Tabel Stok opname
    **/
    public function actionExportPdf($id)
    {
        $request = Yii::$app->request;
        $print = new DocoPrint;
        $header = LaporanFormSoBarang::find()
            ->where(['formsobarang_id' => $id])
            ->orderBy(['barang_nama' => SORT_ASC])
            ->asArray()
            ->one();
        
        if (!empty($header)) {
            $detail = InfoFormSoBarangDetail::find()->where([
                        'formsobarang_id' => $id
                    ])->asArray()->all();

            $periode = date('d-M-Y',strtotime($header['periode_awal'])) . ' s/d ' . date('d-M-Y',strtotime($header['periode_akhir']));
            $print->attributes = [
                '#ruangan#' => $header['ruangan_nama'],
                '#periode_stok#' => $periode,
                '#tanggal_stok_opname#' => date('d-M-Y',strtotime($header['tglstokopname'])),
                '#nomer_stok_opname#' => $header['nostokopname'],
                '#jenis_stok_opname#' => $header['jenis_so'],
                '#nomor_formulir#' => $header['noformulir'],
                '#tabel_stok_opname#' => $this->renderPartial('index',[
                    'header' => $header,
                    'detail' => $detail
                ]),
            ];
        }

        $print->Output();
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->dataProvider()->all();
            $ruangan = null;
            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $modelRuangan = Ruangan::find()->where([
                    'ruangan_id' => $_GET['advanced-filter']['ruangan_id']
                ])->one();
                $ruangan = !empty($modelRuangan->ruangan_nama) ? $modelRuangan->ruangan_nama : null;
            }
            $this->_title .= " {$ruangan}";
            // Directory Creation
            $header = array(
                Yii::t('app', "Periode") => $request->get('periode'),
            );
            

            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
                "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app            
            ));

            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDetailHeader($id) 
    {
        $model = new InfoStokOpnameBarangView;
        $query = $model->find()->where(['stokopnamebarang_id' => $id])->asArray()->one();
        return $this->responseJson(200, 'success', $query);
    }

    public function actionGetDataDetailSo($stokopnamebarang_id) 
    {
        $model = new InfoStokOpnameBarangDetailView;
        $query = $model::find()
            ->where(['stokopnamebarang_id' => $stokopnamebarang_id])
            ->orderBy(['barang_nama' => SORT_ASC]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @controller actionExportPdfDetail
     * @attribute #ruangan# => nama ruangan
     * @attribute #tgl_formulir# => tanggal formulir
     * @attribute #tgl_stokopname# => tanggal stokopname
     * @attribute #no_stokopname# => nomor stokopname
     * @attribute #no_formulir# => nomor formulir
     * @attribute #total_harga_fisik# => total harga fisik
     * @attribute #total_harga_sistem# => total harga system
     * @attribute #total_selisih# => total selisih
     * @attribute #table_formulir# => list data detail
     **/
    public function actionExportPdfDetail($id)
    {
        $request = Yii::$app->request;
        $defaultOrder = ['barang_nama' => SORT_ASC];
        $order = $request->get('order', '');

        if($order == '') {
            $order = $defaultOrder;
        }

        $modelHeader = new InfoStokOpnameBarangView;
        $header = $modelHeader->find()->where(['stokopnamebarang_id' => $id])->asArray()->one();

        $modelDetail = new InfoStokOpnameBarangDetailView;
        $detail = $modelDetail::find()->where(['stokopnamebarang_id' => $id])
        ->orderBy($order)->asArray()->all();
         
        $ruangan_nama = ArrayHelper::getValue($header, 'ruangan_nama', '');
		$tgl_formulir = isset($header['tglformulir']) ? date('d-M-Y H:i:s', strtotime($header['tglformulir'])) : '-';
        $tgl_stokopname = isset($header['tglstokopname']) ? date('d-M-Y H:i:s', strtotime($header['tglstokopname'])) : '-';
		$no_stokopname = ArrayHelper::getValue($header, 'nostokopname', '-');
		$no_formulir = ArrayHelper::getValue($header, 'noformulir', '-');
		$jenis_stokopname = ArrayHelper::getValue($header, 'jenis_stokopname', '-');
		$total_harga_fisik = DocoHelpers::rupiahDisplay(ArrayHelper::getValue($header, 'harga_netto_fisik', 0));
        $total_harga_sistem = DocoHelpers::rupiahDisplay(ArrayHelper::getValue($header, 'total_harga_sistem', 0));
        $total_selisih = DocoHelpers::rupiahDisplay(ArrayHelper::getValue($header, 'total_selisih', 0));

        $print = new DocoPrint;
        $print->attributes = [
            '#ruangan#' => $ruangan_nama,
            '#tgl_formulir#' => $tgl_formulir,
            '#tgl_stokopname#' => $tgl_stokopname,
            '#no_stokopname#' => $no_stokopname,
            '#no_formulir#' => $no_formulir,
            '#jenis_stokopname#' => $jenis_stokopname,
            '#total_harga_fisik#' => $total_harga_fisik,
            '#total_harga_sistem#' => $total_harga_sistem,
            '#total_selisih#' => $total_selisih,
            '#table_formulir#' => $this->renderPartial('index-list',[
                'data' => $detail,
            ]),
        ];
        $print->Output();
    }

    public function actionVerifikasi(){
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $request = Yii::$app->request;
            $id = $request->get('id',null);

            $validate = DynamicModel::validateData(compact('id'), [
                [['id'], 'integer'],
                [['id'], 'required'],
            ]);
            if ($validate->hasErrors()) return $this->responseJson(422, $validate->getFirstError('id'), $validate->getErrors());

            $stokOpname = StokOpnameBarang::find()->where(['stokopnamebarang_id'=>$id])->one();
            if(is_null($stokOpname)) throw new \Exception("Stok Opname ID Tidak Ditemukan", 1);

            if($stokOpname->is_verifikasi) throw new \Exception("Stok opname sudah diverifikasi", 1);

            $stokOpnameDetail = (new EntSoDetail)->loadViewById($id);
            $tglImplSesuaiVerif = $this->getTglImplSesuaiVerif($id);
            $tgl_implementasi = ArrayHelper::getValue($tglImplSesuaiVerif, 'config', false) ? date('Y-m-d H:i:s') : ArrayHelper::getValue($tglImplSesuaiVerif, 'tgl_implementasi');
            
            $stokOpname->is_verifikasi = TRUE;
            $stokOpname->tglverifikasi = date('Y-m-d H:i:s');
            $stokOpname->pegawaiverifikasi_id = Yii::$app->jwt->user->pegawai_id;
            $stokOpname->tgl_implementasi = $tgl_implementasi;
            $stokOpname->save();
            $stokOpnameDetail->updateStokAkhir();

            $params = [
                'stokopnamebarang_id' => $id,
                'ruangan_id' => $stokOpname->ruangan_id,
                'tgl_implementasi' => ArrayHelper::getValue($tglImplSesuaiVerif, 'tgl_implementasi'),
            ];
            $hitungStok = (new VERIF_BSL_SO($params))->execute();

            $transaction->commit();
            return $this->responseJson(200,DocoMessages::SUC_MESSAGE_UPDATED);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage()]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$e->getMessage()]);
        }
    }

    private function getTglImplSesuaiVerif($id)
    {
        $config = KonfigGudang::find()->select(['is_tgl_implementasi_sesuai_verif'])->asArray()->one();
        $infoSOBarang = InfoStokOpnameBarangView::find()->where(['stokopnamebarang_id' => $id])->asArray()->one();
        
        if (!empty($infoSOBarang) && date('Y-m-d') == date('Y-m-d', strtotime(ArrayHelper::getValue($infoSOBarang, 'tglformulir', null)))) {
            $tgl_implementasi = date('Y-m-d H:i:s');
        } else {
            $tgl_implementasi = date('Y-m-d 23:59:59', strtotime(ArrayHelper::getValue($infoSOBarang, 'tglformulir')));
        }

        return [
            'tgl_implementasi' => $tgl_implementasi,
            'config' => isset($config['is_tgl_implementasi_sesuai_verif']) ? $config['is_tgl_implementasi_sesuai_verif'] : false,
        ];
    }

    public function actionGetObjectData() {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $render = ArrayHelper::getValue($this->getKonfigCetakFormSo(), 'layout', 'cetak-form');
            $head = InfoFormSoBarangView::find()->where(["formsobarang_id" => $id])->one();
            $detail = InfoFormSoBarangDetailView::find()->where(["formsobarang_id" => $id])->orderBy(['barang_nama' => SORT_ASC])->all();
            $pegawai_cetak = Pegawai::find()->select(['pegawai_id', 'nama_pegawai'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->one();
            $attributes = [
                "#nomor_formulir#" => ArrayHelper::getValue($head, 'noformulir'),
                "#ruangan#" => ArrayHelper::getValue($head, 'ruangan_nama'),
                "#priode_stok#" => date("d-M-Y H:i:s", strtotime(ArrayHelper::getValue($head, 'tglformulir'))),
                "#detail_formulir#" => $this->renderPartial($render,[
                    'head' => $head,
                    'detail' => $detail
                ]),
                '#pegawai_cetak#' => $pegawai_cetak['nama_pegawai']
            ];
            return [
                'attributes' => $attributes
            ];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function actionSyncPdf() {
        $request = Yii::$app->request;
        $params = $request->get('params', []);
        $randString = $request->get('randString');
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        $docTercetak = ArrayHelper::getValue($this->getKonfigCetakFormSo(), 'kode_doc', 'transaksi-formulir-barang');
        $fetchLimit = 20;
        $countData = InfoFormSoBarangDetailView::find()->where(['formsobarang_id' => ArrayHelper::getValue($params, 'id')])->count();
        $totalPerPage = ceil($countData / $fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportFormSoBarangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => [
                        'id' => ArrayHelper::getValue($params, 'id')
                    ],
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'CetakFormSoBarangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'docTercetak' => $docTercetak
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadFormSoBarangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function getKonfigCetakFormSo()
    {
        $konfigCetak = KonfigRepositories::getKonfigGudang();
        return isset($konfigCetak['cetak_form_so']) ? $konfigCetak['cetak_form_so'] : [];
    }

    public function actionSendFile() {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;
            $path = 'uploads/' . $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path . '/' . $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDownloadPdf() {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    public function actionTransaksiPrintFormulir($id) {
        $docTercetak = ArrayHelper::getValue($this->getKonfigCetakFormSo(), 'kode_doc', 'transaksi-formulir-barang');
        $print = new DocoPrint($docTercetak);

        $print->attributes = $this->actionGetObjectData()['attributes'];
        return $print->Output();
    }
}
