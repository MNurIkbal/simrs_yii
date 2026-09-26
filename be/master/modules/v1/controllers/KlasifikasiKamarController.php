<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use app\modules\v1\models\KlasifikasiKamarView;
use app\modules\v1\models\KlasifikasiKamar;
use app\modules\v1\models\SirsOnline;
use app\modules\v1\models\EisCovid;
use app\modules\v1\models\Applicare;
use app\modules\v1\models\Spgdt;
use app\modules\v1\models\KlasifikasiKamarV;
use Doco\components\DocoMessages;
use Doco\rabbitmq\RabbitBgProcess;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class KlasifikasiKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KlasifikasiKamar';

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
        try {
            $request = Yii::$app->request;
            $model = new KlasifikasiKamarView;
            $query = $model::find();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $dataProvider['query'] = $query;

            $query->orderby(['klasifikasikamar_nama' => SORT_ASC]);

            $isPagination = $request->get('isPagination', true);
            if ($isPagination == false) {
                $dataProvider['pagination'] = false;
            }
            
            return new ActiveDataProvider($dataProvider);
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

    public function actionCreateKlasifikasi()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new KlasifikasiKamar;
            $post = $request->post();
            $model->attributes = $post;
            if($model->validate()){
                if ($post) {
                    if ($model->save()) {
                        $transaction->commit();
                        $responseMessage =  ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'KlasifikasiKamarForm');
                        $responseMessage =  ['data' => $errors,'status' => 422];
                    }
                    return $responseMessage;
                }
            }else{
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422,$response);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateKlasifikasi()
    {
        try {
            $request = Yii::$app->request;
            $model = KlasifikasiKamar::findOne($request->get('id'));
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KlasifikasiKamarForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            else{
                throw new \Exception('Data Tidak Di Temukan');
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function Model(){
        $model = new KlasifikasiKamarView;
        return $model::find();
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Klasifikasi Kamar';
            $get = $request->get();

            $model = new KlasifikasiKamarView;
            $query = $this->model();

            if(isset($_GET['advanced-filter'])) {
                $advancedFilters = $_GET['advanced-filter'];
                if(isset($advancedFilters['is_active'])) {
                    $is_active = $advancedFilters['is_active'];
                    $query->andWhere(['is_active' => $is_active]);
                }
                if(isset($advancedFilters['sirsonline_nama'])) {
                    $sirsonline_id = $advancedFilters['sirsonline_nama'];
                    $query->andWhere(['sirsonline_id' => $sirsonline_id]);
                }
                if(isset($advancedFilters['eiscovid_nama'])) {
                    $eiscovid_id = $advancedFilters['eiscovid_nama'];
                    $query->andWhere(['eiscovid_id' => $eiscovid_id]);
                }
                if(isset($advancedFilters['applicare_nama'])) {
                    $applicare_id = $advancedFilters['applicare_nama'];
                    $query->andWhere(['applicare_id' => $applicare_id]);
                }
                if(isset($advancedFilters['spgdt_nama'])) {
                    $spgdt_id = $advancedFilters['spgdt_nama'];
                    $query->andWhere(['spgdt_id' => $spgdt_id]);
                }
            }

            $query->orderby(['klasifikasikamar_nama' => SORT_ASC]);

            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $result[] = $value;
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();

        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        try{
            $request = Yii::$app->request;
            $title = 'Master Klasifikasi Kamar';
            $result = [];
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $get = $request->get();

            $model = new KlasifikasiKamarView;
            $query = $this->model();
            
            if(isset($_GET['advanced-filter'])) {
                $advancedFilters = $_GET['advanced-filter'];
                if(isset($advancedFilters['is_active'])) {
                    $is_active = $advancedFilters['is_active'];
                    $query->andWhere(['is_active' => $is_active]);
                }
                if(isset($advancedFilters['sirsonline_nama'])) {
                    $sirsonline_id = $advancedFilters['sirsonline_nama'];
                    $query->andWhere(['sirsonline_id' => $sirsonline_id]);
                }
                if(isset($advancedFilters['eiscovid_nama'])) {
                    $eiscovid_id = $advancedFilters['eiscovid_nama'];
                    $query->andWhere(['eiscovid_id' => $eiscovid_id]);
                }
                if(isset($advancedFilters['applicare_nama'])) {
                    $applicare_id = $advancedFilters['applicare_nama'];
                    $query->andWhere(['applicare_id' => $applicare_id]);
                }
                if(isset($advancedFilters['spgdt_nama'])) {
                    $spgdt_id = $advancedFilters['spgdt_nama'];
                    $query->andWhere(['spgdt_id' => $spgdt_id]);
                }
            }

            $query->orderby(['klasifikasikamar_nama' => SORT_ASC]);
            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $data['klasifikasikamar_nama'] = $value['klasifikasikamar_nama'];
                $data['sirsonline_nama'] = $value['sirsonline_nama'];
                $data['eiscovid_nama'] = $value['eiscovid_nama'];
                $data['applicare_nama'] = $value['applicare_nama'];
                $data['spgdt_nama'] = $value['spgdt_nama'];
                $result[] = $data;
            }

            $header = [];
            $filePath = DocoHelpers::exportExcel('Master Klasifikasi Kamar', $result, $header, array("uploadPath" => "./uploads",), null, null, true);

            $filePath->save('php://output');
            die;

        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeleteKlasifikasi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $mapKlasifikasiKamar = KlasifikasiKamarV::find()->select(['klasifikasikamar_id', 'kamarruangan_nokamar'])->where(['klasifikasikamar_id' => $id])->all();
            if ($mapKlasifikasiKamar) {
                return [
                    'title' => 'Proses Gagal !',
                    'text' => 'Klasifikasi Sudah Dimappingkan dengan kamar',
                    'status' => 422
                ];
            }

            $request = Yii::$app->request;
            $model = KlasifikasiKamar::findOne($id);
            if ($model->delete()) {
                return $response['response'] = [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Data berhasil dihapus',
                    ];
            } else {
                return $response['response'] = [
                        'title' => 'Proses Gagal !',
                        'text' => 'Data Gagal di hapus',
                        'status' => 422
                    ];
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        return $this->getDataKlasifikasi($id)->asArray()->one();
    }

    private function getDataKlasifikasi($id = null)
    {
        $getKlasifikasi = KlasifikasiKamar::find()
                ->where(['is_deleted' => false]);
        if ($id) {
            $getKlasifikasi->where(['klasifikasikamar_id' => $id]);
        }
        
        return $getKlasifikasi;
    }
    

    public function actionGetDataSelect() {
        $data = SirsOnline::find()
            ->where(['is_active' => 't'])
            ->select(['sirsonline_id', 'sirsonline_nama'])
            ->orderBy(['sirsonline_nama' => SORT_ASC]);
            
        $sirs = ArrayHelper::map($data->all(), 'sirsonline_id', 'sirsonline_nama');
        
        $data = EisCovid::find()
            ->where(['is_active' => 't'])
            ->select(['eiscovid_id', 'eiscovid_nama'])
            ->orderBy(['eiscovid_nama' => SORT_ASC]);;
            
        $eis = ArrayHelper::map($data->all(), 'eiscovid_id', 'eiscovid_nama');

        $data = Applicare::find()
            ->where(['is_active' => 't'])
            ->select(['applicare_id', 'applicare_nama'])
            ->orderBy(['applicare_nama' => SORT_ASC]);;
            
        $applicare = ArrayHelper::map($data->all(), 'applicare_id', 'applicare_nama');

        $data = Spgdt::find()
            ->where(['is_active' => 't'])
            ->select(['spgdt_id', 'spgdt_nama'])
            ->orderBy(['spgdt_nama' => SORT_ASC]);;
            
        $spdgt = ArrayHelper::map($data->all(), 'spgdt_id', 'spgdt_nama');

        
        return [
            'sirs' => $sirs,
            'eis' => $eis,
            'applicare' => $applicare,
            'spdgt' => $spdgt
        ];
    }

    public function actionChangeStatus()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $mapKlasifikasiKamar = KlasifikasiKamarV::find()->select(['klasifikasikamar_id', 'kamarruangan_nokamar'])->where(['klasifikasikamar_id' => $id])->all();
            if ($mapKlasifikasiKamar) {
                return [
                    'title' => 'Proses Gagal !',
                    'text' => 'Klasifikasi Sudah Dimappingkan dengan kamar',
                    'status' => 422
                ];
            }

            $model = KlasifikasiKamar::findOne($id);
            if (empty($request->post()) || empty($model)) {
                return [
                    'title' => 'Proses Gagal !',
                    'text' => 'Data tidak ditemukan',
                    'status' => 422
                ];
            }
            
            $model->attributes = $request->post();
            if ($model->save()) {
                return ['message' => 'Status berhasil diubah', 'status' => 200];
            } else {
                $errors = DocoHelpers::parseError($model->errors,'KlasifikasiKamarForm');
                return ['data' => $errors,'status' => 422];
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcelBgproses()
    {
        $request = Yii::$app->request;
        $advanced_filter = $request->get('advanced-filter');
        $randString = $request->get('randString');

        $model = new KlasifikasiKamarView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $countData = $query->count();
        
        $headerExcel = [
            'Klasifikasi Kamar Nama' => ArrayHelper::getValue($advanced_filter, 'klasifikasikamar_nama'),
            'Sirsonline Nama' => ArrayHelper::getValue($advanced_filter, 'sirsonline_nama'),
            'Eiscovid Nama' => ArrayHelper::getValue($advanced_filter, 'eiscovid_nama'),
            'Nama Kelas Aplicare' => ArrayHelper::getValue($advanced_filter, 'namakelas_aplicare'),
            'Spgdt Nama' => ArrayHelper::getValue($advanced_filter, 'spgdt_nama'),
        ];

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $request->get(),
            'totalPerPage' => $countData,
            'headerExcel' => $headerExcel,
            'countData' => $countData, 
            'title' => 'Laporan Excel Klasifikasi Kamar',
            'sendToUrl' => 'klasifikasi-kamar/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('master'),
        ], 'laporan_klasifikasi_kamar');

        return [
            'totalPerPage' => $countData,
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
            $model->file = $fileName.'.'.$ext;
            $path = "uploads/";
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
}
