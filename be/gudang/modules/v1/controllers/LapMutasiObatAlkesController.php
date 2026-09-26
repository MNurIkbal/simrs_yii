<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoSpout;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\modules\v1\models\LaporanMutasiObatView;
use app\modules\v1\models\Ruangan;

use Doco\rabbitmq\RabbitBgProcess;

class LapMutasiObatAlkesController extends DocoActiveController
{
	public $modelClass = '';

    public function actions()
    {
        return [
        	'get-list-data' => 'app\modules\v1\actions\LapMutasiObatAlkes\GetListDataAction',
            'get-list-ruangan' => 'app\modules\v1\actions\LapMutasiObatAlkes\GetListRuanganAction'
        ];
    }

    public function filterQuery($advancedFilter, $query)
    {
        if (isset($advancedFilter['tgl_pengiriman_awal']) && isset($advancedFilter['tgl_pengiriman_akhir'])) {
            $query->andWhere(
                ['between', 'tgl_pengiriman', $advancedFilter['tgl_pengiriman_awal'], $advancedFilter['tgl_pengiriman_akhir']]
            );
        }

        return $query;
    }

    protected $_title = 'LAPORAN MUTASI OBAT ALKES';
    public function actionExportExcel()
    {
        $model = new LaporanMutasiObatView;
        $query = $model::find();

        if (isset($_GET['advanced-filter'])) {
            $this->filterQuery($_GET['advanced-filter'], $query);
            $filter = $_GET['advanced-filter'];
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy([
            'tgl_pemesanan' => SORT_ASC,
            'no_pemesanan' => SORT_ASC,
            'nama_obat' => SORT_ASC
        ]);

        $result = $model->toExcel($query);

        $daftar_ruangan = ArrayHelper::map(Ruangan::find()->all(), 'ruangan_id', 'ruangan_nama');

        $header = array(
            Yii::t('app', $model->attributeLabels()['tgl_pengiriman']) => (@$filter['filter_tgl_pengiriman']),
            Yii::t('app', $model->attributeLabels()['ruangan_pengirim']) => (@$daftar_ruangan[$filter['ruanganpengirim_id']]),
            Yii::t('app', $model->attributeLabels()['ruangan_penerima']) => (@$daftar_ruangan[$filter['ruanganpenerima_id']])
        );

        $options = [
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'L',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'N',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'O',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'P',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'Q',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'R',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'S',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'T',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'U',
                    'formatCode' => 'number',
                ],
            ],
        ];
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
//        $filePath = DocoSpout::exportExcel($this->_title, $result, $header, [],[],[],true);
//        $filePath->close();
//        die;
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $total_data = $this->getDataExcel($getData)->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $total_data,
            'countData' => $total_data,
            'sendToUrl' => 'lap-mutasi-obat-alkes/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('gudang'),
        ], 'laporan_mutasi_obat_alkes_excel',  'import_data');

        return [
            'totalPerPage' => $total_data,
            'unique_str' => $randString,
            'countData' => $total_data,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

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
                    'message' => 'upload file berhasil!'
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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }

    private function getKepalaRuangan()
    {
        try {
            $getKepalaRuangan = PegawaiView::find()->where([
                'ruangan_id'=> Yii::$app->jwt->ruangan_id,
                'jabatan_id'=>DocoConstants::VAR_J_K_R])->one();
            return [
                'kepalaruangan' => $getKepalaRuangan->nama_pegawai,
                'kepalaruangannip' => $getKepalaRuangan->nomorindukpegawai
            ];
        } catch (\Exception $e) {
            return [
                'kepalaruangan' => '',
                'kepalaruangannip' => '',
            ];
        } catch (\yii\db\Exception $e){
            return [
                'kepalaruangan' => '',
                'kepalaruangannip' => '',
            ];
        }
    }

    private function getDataExcel()
    {
        $date = date('Y-m-d');
        $model = new LaporanMutasiObatView;
        $query = $model::find();
        
        
        if (isset($_GET['advanced-filter'])) {
            $query = $this->filterQuery($_GET['advanced-filter'], $query);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy([
            'tgl_pemesanan' => SORT_ASC,
            'no_pemesanan' => SORT_ASC,
            'nama_obat' => SORT_ASC
        ]);

    //     $start = date('Y-m-d 00:00:00');
    //     $end = date('Y-m-d 23:59:59');

    //    if(isset($_GET['advanced-filter'])) {
    //         if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
    //             $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
    //             if(count($explode) == 2) {
    //                 $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
    //                 $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
    //             }
    //             unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
    //         }

    //         if(isset($_GET['advanced-filter']['no_induk_kependudukan'])) {
    //             $no_induk_kependudukan = $_GET['advanced-filter']['no_induk_kependudukan'];
    //             $query->andWhere(['no_induk_kependudukan' => $no_induk_kependudukan]);
    //             unset($_GET['advanced-filter']['no_induk_kependudukan']);
    //         }

    //         if(isset($_GET['advanced-filter']['carabayar_id'])) {
    //             $carabayar_id = $_GET['advanced-filter']['carabayar_id'];
    //             $query->andWhere(['carabayar_id' => $carabayar_id]);
    //             unset($_GET['advanced-filter']['carabayar_id']);
    //         }

    //         if(isset($_GET['advanced-filter']['penjamin_id'])) {
    //             $penjamin_id = $_GET['advanced-filter']['penjamin_id'];
    //             $query->andWhere(['penjamin_id' => $penjamin_id]);
    //             unset($_GET['advanced-filter']['penjamin_id']);
    //         }

    //         if(isset($_GET['advanced-filter']['ruangan_id'])) {
    //             $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
    //             $query->andWhere(['ruangan_id' => $ruangan_id]);
    //             unset($_GET['advanced-filter']['ruangan_id']);
    //         }

    //         if(isset($_GET['advanced-filter']['pegawai_id'])) {
    //             $pegawai_id = $_GET['advanced-filter']['pegawai_id'];
    //             $query->andWhere(['pegawai_id' => $pegawai_id]);
    //             unset($_GET['advanced-filter']['pegawai_id']);
    //         }

    //         if(isset($_GET['advanced-filter']['status_periksa'])) {
    //             $status_periksa_id = $_GET['advanced-filter']['status_periksa'];
    //             $query->andWhere(['status_periksa_id' => $status_periksa_id]);
    //             unset($_GET['advanced-filter']['status_periksa']);
    //         }

    //         if(isset($_GET['advanced-filter']['status_skrining'])) {
    //             $status_skrining = $_GET['advanced-filter']['status_skrining'];
    //             $query->andWhere(['status_skrining' => $status_skrining]);
    //             unset($_GET['advanced-filter']['status_periksa']);
    //         }

    //         if(isset($_GET['advanced-filter']['carakeluar_nama'])) {
    //             $carakeluar_id = $_GET['advanced-filter']['carakeluar_nama'];
    //             $query->andWhere(['carakeluar_id' => $carakeluar_id]);
    //             unset($_GET['advanced-filter']['carakeluar_nama']);
    //         }

    //         if(isset($_GET['advanced-filter']['toggle'])){
    //             $toggle = $_GET['advanced-filter']['toggle'];
    //             unset($_GET['advanced-filter']['toggle']);
    //         }
    //     }
        
    //     $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
    //     $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        Yii::error($query->createCommand()->getRawSql());
        return $query;
    }
}
