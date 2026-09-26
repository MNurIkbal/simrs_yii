<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-13 11:28:34
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-19 16:22:14
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoTarifPenunjangView;
use app\modules\v1\models\TarifTindakanLabView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\JenisPemeriksaanLab;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KelompokPemeriksaanRad;
use app\modules\v1\models\TarifTindakanRadView;

use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadFormPdf;

use Doco\Services\InternalService;

class InformasiTarifPenunjangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TarifTindakanLabView';

    public function verbs()
    {
        $verbs = parent::verbs();
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
        $model = new InfoTarifPenunjangView;
        $query = $model::find()->where(['komponentarif_id' => 6]);

        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['ruangan_nama'])) {
                $ruangan_id = $advancedFilter['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
            if(isset($advancedFilter['penjamin_nama'])) {
                $penjamin_id = $advancedFilter['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
            if(isset($advancedFilter['nama_kelompok'])) {
                $kelompok_id = $advancedFilter['nama_kelompok'];
                $query->andWhere(['kelompokpemeriksaanlab_id' => $kelompok_id]);
                unset($_GET['advanced-filter']['nama_kelompok']);
            }
            if(isset($advancedFilter['jenispemeriksaanlab_nama'])) {
                $jenis_id = $advancedFilter['jenispemeriksaanlab_nama'];
                $query->andWhere(['jenispemeriksaanlab_id' => $jenis_id]);
                unset($_GET['advanced-filter']['jenispemeriksaanlab_nama']);
            }
            if(isset($advancedFilter['kelaspelayanan_nama'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_nama'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                unset($_GET['advanced-filter']['kelaspelayanan_nama']);
            }
        }

        if(isset($_GET['order'])) {
            $order = $_GET['order'];
            $query->orderBy($order);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionCetakPdf
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail pemesanan
    * @attribute #title# => Untuk Menampilkan title
    **/
    public function actionCetakPdf()
    {
        try {
            $instalasi_id = Yii::$app->jwt->instalasi_id;

            $titleRuangan = 'Laboratorium';
            if($instalasi_id == DocoConstants::INSTALASI_ID_RADIOLOGI){
                $titleRuangan = 'Radiologi';
            }

            $title = 'Informasi Tarif ' . $titleRuangan;
            $query = InfoTarifPenunjangView::find()->where(['komponentarif_id' => 6]);
            $request = Yii::$app->request;
            // return $request->get();
            if(isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if(isset($advancedFilter['ruangan_nama'])) {
                    $ruangan_id = $advancedFilter['ruangan_nama'];
                    $query->andWhere(['ruangan_id' => $ruangan_id]);
                }
                if(isset($advancedFilter['penjamin_nama'])) {
                    $penjamin_id = $advancedFilter['penjamin_nama'];
                    $query->andWhere(['penjamin_id' => $penjamin_id]);
                }
                if(isset($advancedFilter['nama_kelompok'])) {
                    $kelompok_id = $advancedFilter['nama_kelompok'];
                    $query->andWhere(['kelompokpemeriksaanlab_id' => $kelompok_id]);
                }
                if(isset($advancedFilter['jenispemeriksaanlab_nama'])) {
                    $jenis_id = $advancedFilter['jenispemeriksaanlab_nama'];
                    $query->andWhere(['jenispemeriksaanlab_id' => $jenis_id]);
                }
                if(isset($advancedFilter['kelaspelayanan_nama'])) {
                    $kelaspelayanan_id = $advancedFilter['kelaspelayanan_nama'];
                    $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                }
            }

            $query->orderBy($request->get('order'));
            $model = $query->all();

            if($model) {
                $print = new DocoPrint();
                $print->docName = $title;
                $print->attributes = [
                    '#title#' => $title,
                    '#tabel_detail#' => $this->renderPartial('index', [
                        'query' => $model
                    ]),
                ];

                $print->Output();
            }     
        } catch (\Exception $e) {
             \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDetail()
    {
        $instalasi_id = Yii::$app->jwt->instalasi_id;
        $modelTarifTindakan = new TarifTindakanLabView;
        $jenispemeriksaan_nama = 'jenispemeriksaanlab_nama';
        if($instalasi_id == DocoConstants::INSTALASI_ID_RADIOLOGI){
            $modelTarifTindakan = new TarifTindakanRadView;
            $jenispemeriksaan_nama = 'jenispemeriksaanrad_nama';
        }

        $request = Yii::$app->request;
        $get = $request->get();
        
        $query = $modelTarifTindakan::find()->where(['tariftindakan_id' => $get['id']])->one();
        // return $query->kelaspelayanan_id;
        $detail = $modelTarifTindakan::find()->where([
            'ruangan_id' => $query->ruangan_id,
            'penjamin_id' => $query->penjamin_id,
            'kelaspelayanan_id' => $query->kelaspelayanan_id,
            'daftartindakan_id' => $query->daftartindakan_id,
        ])
        ->andWhere(['<>', 'komponentarif_id', 6])
        ->all();

        $sum = 0;
        foreach ($detail as $key => $value) {
            $sum += $value['harga_tariftindakan'];
        }
        return [
            'query' => $query,
            'detail' => $detail,
            'total' => $sum,
            'jenispemeriksaan_nama' => $jenispemeriksaan_nama,
        ];
    }

    public function actionExportExcel()
    {
        $instalasi_id = Yii::$app->jwt->instalasi_id;

        $titleRuangan = 'Laboratorium';
        if($instalasi_id == DocoConstants::INSTALASI_ID_RADIOLOGI){
            $titleRuangan = 'Radiologi';
        }

        $title = 'Informasi Tarif ' . $titleRuangan;

        $request = Yii::$app->request;
        $model = new InfoTarifPenunjangView;
        $query = $model::find(true)->where(['komponentarif_id' => 6]);

        $ruangan = '';
        $penjamin = '';
        $kelompok = '';
        $jenis = '';
        $kp = '';

        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['ruangan_nama'])) {
                $ruangan_id = $advancedFilter['ruangan_nama'];
                $ruangan = Ruangan::findOne($ruangan_id);
                $ruangan = $ruangan->ruangan_nama;
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
            if(isset($advancedFilter['penjamin_nama'])) {
                $penjamin_id = $advancedFilter['penjamin_nama'];
                $penjamin = Penjamin::findOne($penjamin_id);
                $penjamin = $penjamin->penjamin_nama;
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
            if(isset($advancedFilter['nama_kelompok'])) {
                $kelompok_id = $advancedFilter['nama_kelompok'];
                $query->andWhere(['kelompokpemeriksaanlab_id' => $kelompok_id]);
                unset($_GET['advanced-filter']['nama_kelompok']);
            }
            if(isset($advancedFilter['jenispemeriksaanlab_nama'])) {
                $jenis_id = $advancedFilter['jenispemeriksaanlab_nama'];
                $query->andWhere(['jenispemeriksaanlab_id' => $jenis_id]);
                unset($_GET['advanced-filter']['jenispemeriksaanlab_nama']);
            }
            if(isset($advancedFilter['kelaspelayanan_nama'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_nama'];
                $kp = KelasPelayanan::findOne($kelaspelayanan_id);
                $kp = $kp->kelaspelayanan_nama;
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                unset($_GET['advanced-filter']['kelaspelayanan_nama']);
            }
        }
        
        $query->orderBy($request->get('order'));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $result = [];

        foreach ($query->all() as $key => $value) {
            // Data Selection
            $newValue = [];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Kelompok Pemeriksaan')] = $value['nama_kelompok'];
            $newValue[\Yii::t('app', 'Jenis Pemeriksaan')] = $value['jenispemeriksaanlab_nama'];
            $newValue[\Yii::t('app', 'Nama Pemeriksaan')] = $value['pemeriksaanlab_nama'];
            $newValue[\Yii::t('app', 'Kelas Pelayanan')] = $value['kelaspelayanan_nama'];
            $newValue[\Yii::t("app", "Tarif Total")] = "Rp." . number_format($value['harga_tariftindakan'],0,",",".");
            $newValue[\Yii::t("app", "Cyto Tindakan (%)")] = $value['persencyto_tindakan'];
            $newValue[\Yii::t("app", "Diskon Tindakan (%)")] = $value['persendiskon_tindakan'];
            $result[$key] = $newValue;
        }

        // Directory Creation
        $header = array(
            Yii::t("app", "Ruangan") => $ruangan,
            Yii::t("app", "Penjamin") => $penjamin,
            Yii::t("app", "Kelompok Pemeriksaan") => $kelompok,
            Yii::t("app", "Jenis Pemeriksaan") => $jenis,
            Yii::t("app", "Kelas Pelayanan") => $kp,
        );

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    public function actionGetRequest()
    {
        $request = Yii::$app->request;

        $instalasi_id = !empty($request->get('instalasi_id')) ? $request->get('instalasi_id') : '' ;
        $ruangan = new Ruangan;
        $ruangan = $ruangan->find();

        $ruangan = $ruangan->all();

        $penjamin = new Penjamin;
        $penjamin = $penjamin->find()->all();

        $kelompok = new KelompokPemeriksaanLab;
        $jenis = new JenisPemeriksaanLab;
        $kelompok_id = 'kelompokpemeriksaanlab_id';
        $jenispemeriksaan_id = 'jenispemeriksaanlab_id';
        $jenispemeriksaan_nama = 'jenispemeriksaanlab_nama';

        if($instalasi_id == DocoConstants::INSTALASI_ID_RADIOLOGI){
            $kelompok = new KelompokPemeriksaanRad;
            $jenis = new JenisPemeriksaanRad;
            $kelompok_id = 'kelompokpemeriksaanrad_id';
            $jenispemeriksaan_id = 'jenispemeriksaanrad_id';
            $jenispemeriksaan_nama = 'jenispemeriksaanrad_nama';
        }

        $kelompok = $kelompok->find()->all();
        $jenis = $jenis->find()->all();

        $kelas_pelayanan = new KelasPelayanan;
        $kelas_pelayanan = $kelas_pelayanan->find()->all();

        return [
            'ruangan' => $ruangan,
            'penjamin' => $penjamin,
            'kelompok' => $kelompok,
            'kelompok_id' => $kelompok_id,
            'jenispemeriksaan_id' => $jenispemeriksaan_id,
            'jenispemeriksaan_nama' => $jenispemeriksaan_nama,
            'jenis' => $jenis,
            'kelas_pelayanan' => $kelas_pelayanan,
        ];
    }

    public function getDataExcel($getData, $instalasi_id)
    {
        $date = date('Y-m-d');
        $model = new InfoTarifPenunjangView;
        $query = $model::find()->where(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);

        if( !empty($instalasi_id) ) {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
        }

        if(isset($getData['advanced-filter'])) {
            $advancedFilter = $getData['advanced-filter'];
            if(isset($advancedFilter['ruangan_nama'])) {
                $ruangan_id = $advancedFilter['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($getData['advanced-filter']['ruangan_nama']);
            }
            if(isset($advancedFilter['penjamin_nama'])) {
                $penjamin_id = $advancedFilter['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($getData['advanced-filter']['penjamin_nama']);
            }
            if(isset($advancedFilter['nama_kelompok'])) {
                $kelompok_id = $advancedFilter['nama_kelompok'];
                $query->andWhere(['kelompokpemeriksaanlab_id' => $kelompok_id]);
                unset($getData['advanced-filter']['nama_kelompok']);
            }
            if(isset($advancedFilter['jenispemeriksaanlab_nama'])) {
                $jenis_id = $advancedFilter['jenispemeriksaanlab_nama'];
                $query->andWhere(['jenispemeriksaanlab_id' => $jenis_id]);
                unset($getData['advanced-filter']['jenispemeriksaanlab_nama']);
            }
            if(isset($advancedFilter['pemeriksaanlab_nama'])) {
                $nama_pemeriksaan = $advancedFilter['pemeriksaanlab_nama'];
                $query->andWhere(['ILIKE', 'pemeriksaanlab_nama', $nama_pemeriksaan]);
                unset($getData['advanced-filter']['pemeriksaanlab_nama']);
            }
            if(isset($advancedFilter['kelaspelayanan_nama'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_nama'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                unset($getData['advanced-filter']['kelaspelayanan_nama']);
            }
        }

        if(isset($getData['order'])) {
            $order = $getData['order'];
            $query->orderBy($order);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $flashData = $getData['flash'];
        $instalasiId = $getData['instalasi_id'];
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($flashData['page'])) unset($getData['flash']['page']);
        if (isset($flashData['per-page'])) unset($getData['flash']['per-page']);

        $data = $this->getDataExcel($flashData, $instalasiId)->asArray()->all();
        $countData = count($data);
        $randString = isset($flashData['randString']) ? $flashData['randString'] : null;
        $totalPerPage = count($data);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanTarifPenunjang' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLaporanTarifPenunjang' => [
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
                'UploadLaporanTarifPenunjang' => [
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

    public function actionDownloadFile() {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $filename = $dir.'/Laporan Tarif Penunjang.xlsx';

        if(file_exists($filename)) {
            $file = basename($filename);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($filename);
            die();
        }
    }

    /* BEGIN ACTION EXPORT PDF BGPROCESS */
    public function actionProcessSyncPdfBgprocess() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $flashData = $getData['flash'];
        $instalasiId = $getData['instalasi_id'];
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($flashData['page'])) unset($getData['flash']['page']);
        if (isset($flashData['per-page'])) unset($getData['flash']['per-page']);

        $data = $this->getDataExcel($flashData, $instalasiId)->asArray()->all();
        $countData = count($data);
        $randString = isset($flashData['randString']) ? $flashData['randString'] : null;
        $limit = 100;
        $totalPerPage = ceil($countData/$limit);


        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanTarifPenunjangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLaporanTarifPenunjangPdf' => [
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
                'UploadLaporanTarifPenunjangPdf' => [
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

    public function actionDropFilePdfBgprocess() {
        $request = Yii::$app->request;
        $model = new UploadFormPdf;
        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;
            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);
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

    public function actionPopulateDataPdfBgprocess() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $flashData = $getData['flash'];
        $instalasiId = $getData['instalasi_id'];
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($flashData['page'])) unset($getData['flash']['page']);
        if (isset($flashData['per-page'])) unset($getData['flash']['per-page']);

        $data = $this->getDataExcel($flashData, $instalasiId)->asArray()->all();

        $attributes = [
            '#datatable#' => $this->renderPartial('cetak_pdf_bgprocess', [
                'data' => $data
            ])
        ];
        return [
            'attributes' => $attributes,
            'kode_doc' => 'master-lap-tarif-penunjang'
        ];
    }

    public function actionDownloadPdfBgprocess() {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath . '/' . $no_request;
        $fileName = $dir . '/tarif-penunjang.pdf';
        if (file_exists($fileName)) {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
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
    /* END ACTION EXPORT PDF BGPROCESS */
}