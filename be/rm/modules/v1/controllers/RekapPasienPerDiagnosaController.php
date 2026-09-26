<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;

class RekapPasienPerDiagnosaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaprekappasienperdiagnosadetV';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
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
        $getRequest = $request->get();

        $instalasi = Instalasi::find();
        $instalasi = $instalasi->where([
            'is_active' => true,
            'instalasi_id' => [1, 2, 3]
        ])->all();

        $ruangan = Ruangan::find();
        $ruangan = $ruangan->where([
            'is_active' => true,
            'instalasi_id' => [1, 2, 3]
        ])->all();

        return [
            'instalasi' => ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
            'ruangan' => ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'),
        ];
    }

    public function actionGetDataRekap()
    {
        return $this->generateDataRekap($this->objectData()->all());
    }

    public function actionGetDataDetail()
    {
        return new ActiveDataProvider([
            'query' => $this->objectData(),
        ]);
    }

    private function objectData()
    {
        try {
            $request = Yii::$app->request;
            $model = new $this->modelClass;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pendaftaran']);
                }
            }
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

            return DocoRestActiveFilter::advancedFilter($model, $query);
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

    private function generateDataRekap($tmpData)
    {
        $request = Yii::$app->request;
        $data = $tmp = [];

        if (!empty($tmpData)) {
            foreach ($tmpData as $key => $value) {
                $kodeDiagnosa = $value['diagnosa_utama_kode'];
                $pasienId = $value['pasien_id'];

                if (!isset($tmp[$kodeDiagnosa][$pasienId])) {
                    $tmp[$kodeDiagnosa][$pasienId] = [
                        'diagnosa_utama_kode' => $kodeDiagnosa,
                        'diagnosa_utama' => $value['diagnosa_utama']
                    ];
                }
            }

            foreach ($tmp as $key => $value) {
                foreach ($value as $k => $v) {
                    $kodeDiagnosa = $v['diagnosa_utama_kode'];
                    if (!isset($data[$kodeDiagnosa])) {
                        $data[$kodeDiagnosa] = [
                            'diagnosa_utama_kode' => $kodeDiagnosa,
                            'diagnosa_utama' => $v['diagnosa_utama'],
                            'jumlah_pasien' => 1,
                            'tgl_pendaftaran' => '',
                            'dokterdpjp_nama' => '',
                            'diagnosa_utama_id' => '',
                            'nama_pasien' => '',
                            'no_rekam_medik' => '',
                            'instalasi_id' => '',
                            'ruangan_id' => '',
                            'dokterdpjp_id' => '',
                        ];
                    } else {
                        $data[$kodeDiagnosa]['jumlah_pasien'] += 1;
                    }
                }
            }
        }

        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => $request->get('per-page'),
            ],
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $type = isset($getData['type']) ? $getData['type'] : 'rekap';
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->objectData();
        $fetchLimit = 50;
        $countData = $data->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData / $fetchLimit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Rm\RekapPasienPerDiagnosa\Excel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'type' => $type,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Rm\RekapPasienPerDiagnosa\ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'type' => $type,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'Rm\RekapPasienPerDiagnosa\UploadExcelFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'type' => $type,
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
        $fileName = $dir . '/Laporan Rekap Pasien per Diagnosa.xlsx';
        return DocoHelpers::downloadFileExcel($fileName);
    }
}
