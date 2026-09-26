<?php

/**
 * @author: yaya
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InfoStokObatDetail;
use app\modules\v1\models\InfoStokObatRakDetail;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\businessLogic\FormulirStokOpname as BLFormulirStokOpname;
use app\modules\v1\models\FormulirStokOpname;
use app\modules\v1\controllers\AllowController;
use app\modules\v1\models\InfoFormulirStokOpnameView;
use app\modules\v1\models\Pegawai;
use Doco\models\Lookup;
use Doco\models\LookupTransaksi;
use yii\web\UploadedFile;
use app\modules\v1\payloads\UploadPayload;
use Doco\Services\InternalService;

class FormulirStokOpnameController extends DocoActiveController
{
    public $modelClass = InfoStokObatAlkesView::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $konfig = $connection->createCommand("SELECT max_dataso FROM konfigfarmasi_k")->queryOne();
        $max_data_so = $konfig['max_dataso'];

        if($max_data_so < $request->get('per-page', 0)) {
            $page_size = $max_data_so;
        } else {
            $page_size = $request->get('per-page', 10);
        }

        $query = $this->dataProvider();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $page_size
            ]
        ]);

        if($dataProvider->getTotalCount() > $max_data_so){
            $dataProvider->setTotalCount($max_data_so);
        }

        return $dataProvider;
    }

    private function dataProvider()
    {
        $request = Yii::$app->request;
        $model = new InfoStokObatRakDetail;
        $query = $model::find();

        if (!empty($request->get('advanced-filter')['rakobat_id'])) {
            $filter_rak = $request->get('advanced-filter')['rakobat_id'];

            if($filter_rak == 'Tanpa Rak') {
                $query->andWhere(['IS','rakobat_id',null]);
            }else{
                $query->andWhere(['rakobat_id' => $filter_rak]);
            }
        }

        if(empty($request->get('advanced-filter')['is_consigment'])){
            $query->andWhere(['is_consigment'=>false]);
        }
        if(!empty($request->get('advanced-filter')['jenisobatalkes_nama'])){
            $filter_jenisobatalkes_nama = $request->get('advanced-filter')['jenisobatalkes_nama'];
            $query->andWhere(['jenisobatalkes_nama'=>$filter_jenisobatalkes_nama]);
        }
        $query->orderBy([
            'rakobat_nama' => SORT_ASC, 
            'laci' => SORT_ASC, 
            'obatalkes_nama' => SORT_ASC
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new InfoStokObatRakDetail;
        $post = $request->post();
        try {
            $connection = Yii::$app->db;
            $konfig = $connection->createCommand("SELECT max_dataso FROM konfigfarmasi_k")->queryOne();
            $max_data_so = $konfig['max_dataso'];

            if($max_data_so < $request->get('per-page', 0)) {
                $page_size = $max_data_so;
            } else {
                $page_size = $request->get('per-page', 10);
            }
            
            $query = $this->dataProvider();
            
            $dataDetail = $query->limit($max_data_so)->asArray()->all();
            $result = BLFormulirStokOpname::excecute($dataDetail);
            return $result;
        } catch (\Exception $e) {
            return [
                    'text' => 'Terjadi Kesalahan',
                    'status' => 422
                ];
        } catch (\yii\db\Exception $e) {
            return [
                    'text' => 'Terjadi Kesalahan',
                    'status' => 422
                ];
        }
    }

    /**
    * @controller actionPrintFormulirStokOpname
    * @attribute #priode_stok# => Untuk Menampilkan Data Periode stok
    * @attribute #nomor_formulir# => Untuk Menampiilkan Nomor formulir stok opname
    * @attribute #table_formulir# => Untuk menampilkan tabel formulir
    * @attribute #ruangan# => Untuk menapilkan ruangan
    **/
    public function actionPrintFormulirStokOpname($id)
    {
        ini_set('memory_limit', '-1'); 
        ini_set('max_execution_time', '300'); 
        ini_set("pcre.backtrack_limit", 5000000);
        $request = Yii::$app->request;
        $print = new DocoPrint;
        try {
            $formulir = FormulirStokOpname::find()->where(['formulirstokopname_id'=>$id])->one();

            $info_header = InfoFormulirStokOpnameView::find()
                ->select(['noformulir', 'ruangan_nama', 'tglformulir'])
                ->where(['formulirstokopname_id' => $id])
                ->asArray()->one();

            $data = DetailFormulirStokOpnameView::find();
            $data->where(['formulirstokopname_id' => $id]);
            $data->orderBy([
                'rakobat_nama' => SORT_ASC,
                'laci' => SORT_ASC,
                'obatalkes_nama'=>SORT_ASC,
                'tglkadaluarsa'=>SORT_ASC
            ]);
            
            $raw_data = $data->asArray()->all();
            $detail = [];
            $list_rak = ArrayHelper::map($raw_data, 'rakobat_nama', 'rak');

            // set file to be used, configurable in lookuptransaksi_m; kode_transaksi = cetak_form_so
            $lookup_trx = LookupTransaksi::find()
                    ->select(['kode_transaksi', 'kode_id'])
                    ->where(['kode_transaksi' => 'cetak_form_so'])->one();
            
            if(isset($lookup_trx)) {
                $get_file = Lookup::find()
                            ->select(['lookup_id', 'lookup_name', 'lookup_value'])
                            ->where(['lookup_id' => $lookup_trx['kode_id']])->one();
            }
            
            if(isset($get_file['lookup_value']) && $get_file['lookup_value'] != 'default') {
                ArrayHelper::multisort($raw_data, ['obatalkes_nama', 'tglkadaluarsa'], [SORT_ASC, SORT_ASC]);
                $detail = $raw_data;
            } else {
                $detail = ArrayHelper::index($raw_data, null, 'rak');
            }
            
            $html_file = isset($get_file['lookup_name']) ? $get_file['lookup_name'] : 'index';
            $pegawai_cetak = Pegawai::find()->select(['pegawai_id', 'nama_pegawai'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->one();

            $print->attributes = [
                '#priode_stok#' => date('d-M-Y H:i:s',strtotime($formulir->tglformulir)),
                '#nomor_formulir#' => isset($info_header['noformulir']) ? $info_header['noformulir'] : "",
                '#table_formulir#' => $this->renderPartial($html_file, [
                    'detail' => $detail,
                    'list_rak' => $list_rak
                ]),
                '#ruangan#' => isset($info_header['ruangan_nama']) ? $info_header['ruangan_nama'] : "",
                '#pegawai_cetak#' => $pegawai_cetak['nama_pegawai']
            ];

            $print->Output(false, '', 'D');
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $noformulir = $request->get('noformulir', null);
        $ruangan_nama = $request->get('ruangan_nama', null);

        try {
            $formulir = FormulirStokOpname::find()->where(['formulirstokopname_id' => $id])->one();

            $data = DetailFormulirStokOpnameView::find();
            $data->where(['formulirstokopname_id' => $id]);
            $data->orderBy([
                'rakobat_nama' => SORT_ASC,
                'laci' => SORT_ASC,
                'obatalkes_nama'=>SORT_ASC,
                'tglkadaluarsa'=>SORT_ASC
            ]);
            
            $raw_data = $data->asArray()->all();
            $detail = [];
            $list_rak = ArrayHelper::map($raw_data, 'rakobat_nama', 'rak');

            // set file to be used, configurable in lookuptransaksi_m; kode_transaksi = cetak_form_so
            $lookup_trx = LookupTransaksi::find()
                    ->select(['kode_transaksi', 'kode_id'])
                    ->where(['kode_transaksi' => 'cetak_form_so'])->one();
            
            if(isset($lookup_trx)) {
                $get_file = Lookup::find()
                            ->select(['lookup_id', 'lookup_name', 'lookup_value'])
                            ->where(['lookup_id' => $lookup_trx['kode_id']])->one();
            }
            
            if(isset($get_file['lookup_value']) && $get_file['lookup_value'] != 'default') {
                ArrayHelper::multisort($raw_data, ['obatalkes_nama', 'tglkadaluarsa'], [SORT_ASC, SORT_ASC]);
                $detail = $raw_data;
            } else {
                $detail = ArrayHelper::index($raw_data, null, 'rak');
            }
            
            $html_file = isset($get_file['lookup_name']) ? $get_file['lookup_name'] : 'index';
            $pegawai_cetak = Pegawai::find()->select(['pegawai_id', 'nama_pegawai'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->one();

            $attributes = [
                '#priode_stok#' => date('d-M-Y H:i:s',strtotime($formulir->tglformulir)),
                '#nomor_formulir#' => $noformulir,
                '#table_formulir#' => $this->renderPartial($html_file, [
                    'detail' => $detail,
                    'list_rak' => $list_rak
                ]),
                '#ruangan#' => $ruangan_nama,
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
        $getData = $request->get();
        $id = $getData['params']['id'];
        $noformulir = $getData['params']['noformulir'];
        $ruangan_nama = $getData['params']['ruangan_nama'];
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 20;
        $data = DetailFormulirStokOpnameView::find();
        $data->where(['formulirstokopname_id' => $id]);
        $data->orderBy([
            'rakobat_nama' => SORT_ASC,
            'laci' => SORT_ASC,
            'obatalkes_nama'=>SORT_ASC,
            'tglkadaluarsa'=>SORT_ASC
        ]);
        $data = $data->asArray()->all();
        $countData = count($data);

        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData / $fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportFormSoPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => [
                        'id' => $id,
                        'noformulir' => $noformulir,
                        'ruangan_nama' => $ruangan_nama
                    ]
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'CetakFormSoPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadFormSoPdf' => [
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
}
