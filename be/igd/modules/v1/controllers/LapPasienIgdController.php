<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Iqbal
 * @Date:   2018-07-27 16:21:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\LapPasienIgdView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\web\UploadedFile;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\Lookup;


class LapPasienIgdController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienRdV';
    protected $_title = 'Informasi Pasien Rawat Darurat';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        // $verbs["update"] = ["POST", "PUT"];
        // $verbs["komponen-tarif"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    private function objectData()
    {
        try {
            $request = Yii::$app->request;
            $model = new LapPasienIgdView;
            $query = $model::find();
            $query->orderby('tgl_pendaftaran DESC');

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if(isset($_GET['advanced-filter'])){
                $between = false;
                $filter = $_GET['advanced-filter'];
                if(isset($filter['tgl_pendaftaran'])){
                    $explode = explode(" - ", $filter['tgl_pendaftaran']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                }

                if (isset($filter['dokter_id'])) {
                    $dokter_id = $filter['dokter_id'];
                    $query->andWhere('(dokter_id = ' . $dokter_id. '
                        OR dokter_jaga_id = ' . $dokter_id. ')');

                    unset($_GET['advanced-filter']['dokter_id']); // Unset Advanced Filter pegawai id / dokter id
                }

                if (isset($filter['no_rekam_medik'])) {
                    $no_rekam_medik = $filter['no_rekam_medik'];
                    $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
                    unset($_GET['advanced-filter']['no_rekam_medik']);
                }

                if (isset($filter['no_pendaftaran'])) {
                    $no_pendaftaran = $filter['no_pendaftaran'];
                    $query->andWhere(['no_pendaftaran' => $no_pendaftaran]);
                    unset($_GET['advanced-filter']['no_pendaftaran']);
                }

                if (isset($filter['status_periksa'])) {
                    $listStatus = explode(",", $filter['status_periksa']);
                    $query->andWhere(['IN', 'status_periksa', array_filter($listStatus)]);
                    unset($_GET['advanced-filter']['status_periksa']);
                }else{
                    $query->andWhere(['<>', 'status_periksa', DocoConstants::STATUS_PERIKSA_BTL_KUNJ]);
                }
            }
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            // $query->andWhere(['ruangan_id' => Yii::$app->jwt->ruangan_id]);
            
            return DocoRestActiveFilter::advancedFilter($model, $query);
            // $filter = $query->createCommand()->getRawSql();
            // return $filter;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => $this->objectData(),
        ]);
    }

    public function actionExportExcel()
    {
        try{
            $request = Yii::$app->request;
            $title = Yii::t('app', 'Laporan Daftar Pasien Rawat Darurat');
            
            $model = new LapPasienIgdView;
            $query = $model::find();
            $query->orderby('tgl_pendaftaran DESC');

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if(isset($_GET['advanced-filter'])){
                $between = false;
                $filter = $_GET['advanced-filter'];
                if(isset($filter['tgl_pendaftaran'])){
                    $explode = explode(" - ", $filter['tgl_pendaftaran']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                }

                if (isset($filter['dokter_id'])) {
                    $dokter_id = $filter['dokter_id'];
                    $query->andWhere('(dokter_id = ' . $dokter_id. '
                        OR dokter_jaga_id = ' . $dokter_id. ')');

                    unset($_GET['advanced-filter']['dokter_id']); // Unset Advanced Filter pegawai id / dokter id
                }

                if (isset($filter['status_periksa'])) {
                    $query->andWhere(['status_periksa'=>$filter['status_periksa']]);
                    unset($_GET['advanced-filter']['status_periksa']);
                }
            }

            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            // $query->andWhere(['ruangan_id' => Yii::$app->jwt->ruangan_id]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $value['tgl_pendaftaran'] = date("j F Y", strtotime($value['tgl_pendaftaran']));
                
                // $value['instalasi'] = $value['instalasi_nama'];
                unset($value['instalasi_nama']);
                unset($value['jeniskelamin']);
                // $value['ruangan'] = $value['ruangan_nama'];
                unset($value['ruangan_nama']);
                $tempdokterjaga = $value['dokter_jaga'];
                unset($value['dokter_jaga']);

                $value['cara_bayar'] = $value['carabayar_nama'];
                unset($value['carabayar_nama']);
                $value['penjamin'] = $value['penjamin_nama'];
                unset($value['penjamin_nama']);
                $value['jenis_kasus_penyakit'] = $value['jeniskasuspenyakit_nama'];
                unset($value['jeniskasuspenyakit_nama']);
                $value['dokter_jaga'] = $tempdokterjaga;
                $value['dokter_penanggungjawab'] = $value['dokter'];
                unset($value['dokter']);
                $value['status'] = $value['status_periksa_nama'];
                unset($value['status_periksa_nama']);
                unset($value['status_periksa']);
                
                $result[] = $value;
            }

            $header = 'Periode : ' . date('d F Y', strtotime($start))." - ".date('d F Y', strtotime($end));

            $filePath = DocoHelpers::exportExcel('Laporan Daftar Pasien Rawat Darurat', $result, [], array(
                "subTitle" => $header,
                "uploadPath" => "./uploads",
            ),[],[],true);

            $filePath->save('php://output');
            die;
            
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_report# => table

    **/
    public function actionExportPdf()
    {
        $model = new LapPasienIgdView;
        $query = $model::find();
        $query->orderby('tgl_pendaftaran DESC');

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])){
            $between = false;
            $filter = $_GET['advanced-filter'];
            if(isset($filter['tgl_pendaftaran'])){
                $explode = explode(" - ", $filter['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }

            if (isset($filter['dokter_id'])) {
                $dokter_id = $filter['dokter_id'];
                $query->andWhere('(dokter_id = ' . $dokter_id. '
                    OR dokter_jaga_id = ' . $dokter_id. ')');

                unset($_GET['advanced-filter']['dokter_id']); // Unset Advanced Filter pegawai id / dokter id
            }

            if (isset($filter['status_periksa'])) {
                $status_periksa = $filter['status_periksa'];
                $query->andWhere(['status_periksa' => $status_periksa]);

                unset($_GET['advanced-filter']['status_periksa']);
            }

            if (isset($filter['status_periksa'])) {
                $query->andWhere(['status_periksa'=>$filter['status_periksa']]);
                unset($_GET['advanced-filter']['status_periksa']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        // $query->andWhere(['ruangan_id'=>Yii::$app->jwt->ruangan_id]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = $query->asArray()->all();
        // Directory Creation
        $header = array(
            Yii::t('app', "Periode") => ((date('d F Y', strtotime($start))." - ".date('d F Y', strtotime($end)))),
        );
        $res = ['filter'=>$header, 'detail'=>$result];
        $print = new DocoPrint();
        $print->attributes = [
            '#table_report#' => $this->renderPartial('pdf',$res),
        ];
        $print->Output();
    }

    public function actionExportExcelNew()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->objectData();
        $fetchLimit = 50;
        $countData = $data->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData / $fetchLimit);

        $uri = Yii::$app->docoRest->getBaseUri('igd');

        $params = [
            'base_uri' => $uri,
        ];

        (new InternalService)->sendTo([
            'Sirs' => [
                'Igd\LapPasienIgd\Excel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Igd\LapPasienIgd\ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Igd\LapPasienIgd\UploadExcelFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionExportPdfNew()
    {
        $request = Yii::$app->request;
        $filter = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        if (isset($filter['page'])) unset($filter['page']);
        if (isset($filter['per-page'])) unset($filter['per-page']);

        $data = $this->objectData();
        $fetchLimit = 50;
        $countData = $data->count();
        $randString = isset($filter['randString']) ? $filter['randString'] : null;
        $totalPerPage = ceil($countData / $fetchLimit);

        $uri = Yii::$app->docoRest->getBaseUri('igd');
        $params = [
            'base_uri' => $uri,
        ];

        (new InternalService)->sendTo([
            'Sirs' => [
                'Igd\LapPasienIgd\Pdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $filter,
                    'params' => $params
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Igd\LapPasienIgd\ExportPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $filter,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Igd\LapPasienIgd\UploadPdfFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;

            $path = "uploads/" . $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $nameFile = $path . '/' . $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }

        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath . '/' . $no_request;
        $fileName = $dir . '/LAPORAN PASIEN RAWAT DARURAT.xlsx';
        return DocoHelpers::downloadFileExcel($fileName);
    }

    /**
     * @controller actionRenderAttributes
     * @attribute #table_report# => table
     **/
    public function actionRenderAttributes()
    {
        $request = Yii::$app->request;
        $postData = $request->post();
        $model = ArrayHelper::getValue($postData, 'model', []);
        $filter = ArrayHelper::getValue($postData, 'filter', []);
        
        unset($filter['order']);
        unset($filter['randString']);
        unset($filter['unique_str']);
        if(empty($filter['filters'])) {
            unset($filter['filters']);
        }

        if(isset($filter['advanced-filter'])){
            if(isset($filter['advanced-filter']['status_periksa'])){
                $listStatus = explode(",", $filter['advanced-filter']['status_periksa']);
                $status = Lookup::find()->Where([
                    'IN', 'lookup_id', array_filter($listStatus),
                    'is_active' => true,
                    'is_deleted' => false
                ])->asArray()->all();
                $filter['advanced-filter']['status_periksa'] = implode(", ", ArrayHelper::getColumn($status, 'lookup_name'));
            }
        }

        $res = ['filter'=>$filter, 'detail'=>$model];
        $attributes = [
            '#table_report#' => $this->renderPartial('pdf',$res),
        ];
        return $attributes;
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $path = 'LAPORAN PASIEN RAWAT DARURAT';
        $file = $rootPath.'/'.$fileName.'/'.$path.'.pdf';

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