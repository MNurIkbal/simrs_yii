<?php
    /**
    * @author iqbal@docotel.com
    * @since 2018-08-30 10:11:20 
    * @desc 
    */
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

use app\modules\v1\models\Instalasi;
use app\modules\v1\models\InstalasiView;
use app\modules\v1\models\Ruangan;
use Doco\Repositories\LookUpTransaksiRepositories;
use app\modules\v1\models\SatusehatInstalasi;

class InstalasiController extends DocoActiveController
{
    public $messageBroker = [
        'create-instalasi' => [
            'services' => [
                'Satusehat' => [
                    'SyncInstalasiSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
        'update-instalasi' => [
            'services' => [
                'Satusehat' => [
                    'SyncInstalasiSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                    'SyncUpdateInstalasiSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'update'
                    ],
                ],
            ]
        ],
    ];

    public $modelClass = 'app\modules\v1\models\Instalasi';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InstalasiView;
            $query = $model::find();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['nama_rumahsakit' => SORT_ASC,
                             'instalasi_nama' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    public function actionGetAllData() {
        try {
            $request = Yii::$app->request;
            $model = new InstalasiView;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby([
                'nama_rumahsakit' => SORT_ASC,
                'instalasi_nama' => SORT_ASC
            ]);

            return [
                'data' => $query->all()
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }

    }

    public function actionListInstalasi() {
        $data = Instalasi::find()->where(['is_active' => 't'])->orderBy('instalasi_id');
        $items = ArrayHelper::map($data->all(), 'instalasi_id', 'instalasi_nama');

        return $items;
    }

    public function actionCreateInstalasi()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new Instalasi;
            $post = $request->post();
            $satusehat_instalasi = $post['satusehat_instalasi'];
            unset($post['satusehat_instalasi']);
            $model->attributes = $post;
            if($model->validate()){
                if ($post) {
                    if ($model->save()) {
                        $instalasi_id = NULL;
                        if(empty($satusehat_instalasi)) {
                            // Kondisi generate SatuSehat Organization background process
                            $instalasi_id = $model->instalasi_id;
                        } else {
                            // Kondisi update SatuSehat Organization
                            $organizationSatusehat = SatusehatInstalasi::find()
                                                        ->where('instalasi_id = '.$model->instalasi_id.' 
                                                            and satusehat_instalasi_id IS NOT NULL
                                                            and is_active = true 
                                                            and is_deleted = false')->one();

                            if(!empty($organizationSatusehat)) {
                                $organizationSatusehat->satusehat_instalasi_id = $satusehat_instalasi;
                                $organizationSatusehat->save();
                            } else {
                                $organizationSatusehat = new SatusehatInstalasi;
                                $organizationSatusehat->instalasi_id = $model->instalasi_id;
                                $organizationSatusehat->satusehat_instalasi_id = $satusehat_instalasi;
                                $organizationSatusehat->save();
                            }
                        }

                        $transaction->commit();
                        $responseMessage =  [
                            'message' => 'Data Berhasil di simpan', 
                            'id' => !empty($instalasi_id) ? DocoHelpers::encrypt($instalasi_id) : NULL
                        ];
                        if (!$this->sinkronInstalasi($model->attributes)) {
                            $responseMessage['errorMessage'] = 'Gagal Menyimpan Data Sinkronisasi';
                            // if (!$this->saveTempSinkron($post)) {
                            // }
                        }

                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'InstalasiForm');
                        $responseMessage = ['data' => $errors,'status' => 422];
                    }

                    return $responseMessage;
                }
            }else{
                $result['status'] = 422;
                $result['data'] = $model->errors;
            }
            return $result;
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

    public function actionUpdateInstalasi()
    {
        try {
            $request = Yii::$app->request;
            $model = Instalasi::findOne($request->get('id'));
            if ($request->post() && !empty($model)) {
                $post = $request->post();

                $satusehat_instalasi = $post['satusehat_instalasi'];
                unset($post['satusehat_instalasi']);

                $model->attributes = $post;
                if ($model->save()) {
                    $organizationSatusehat = SatusehatInstalasi::find()
                                                ->where('instalasi_id = '.$model->instalasi_id.' 
                                                    and satusehat_instalasi_id IS NOT NULL
                                                    and is_active = true 
                                                    and is_deleted = false');

                    $instalasi_id = NULL;
                    if(empty($satusehat_instalasi)) {
                        // Kondisi generate SatuSehat Organization Background process
                        $instalasi_id = $model->instalasi_id;

                        $updateOrganizationSatusehat = $organizationSatusehat->one();

                        if( !empty($updateOrganizationSatusehat) ) {
                            $updateOrganizationSatusehat->is_deleted = true;
                            $updateOrganizationSatusehat->is_active = false;
                            $updateOrganizationSatusehat->save();
                        }

                        $state_input = 'create';
                    } else {
                        // Kondisi update SatuSehat Organization
                        $instalasi_id = $model->instalasi_id;
                        $organizationSatusehat = $organizationSatusehat->one();

                        if(!empty($organizationSatusehat)) {
                            $organizationSatusehat->satusehat_instalasi_id = $satusehat_instalasi;
                            $organizationSatusehat->save();

                            $state_input = 'update';
                        } else {
                            $organizationSatusehat = new SatusehatInstalasi;
                            $organizationSatusehat->instalasi_id = $model->instalasi_id;
                            $organizationSatusehat->satusehat_instalasi_id = $satusehat_instalasi;
                            $organizationSatusehat->save();

                            $state_input = 'update';
                        }
                    }

                    return [
                        'message' => 'Data Berhasil di simpan',
                        'id' => !empty($instalasi_id) ? DocoHelpers::encrypt($instalasi_id) : NULL,
                        'state_input' => $state_input
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'InstalasiForm');
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

    public function actionDeleteInstalasi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = Instalasi::findOne($id);
            $modelRuangan = new Ruangan;
            $getDataPendaftaran = $modelRuangan::find()->where(['instalasi_id'=>$id])->count();
            if($getDataPendaftaran > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Instalasi ini sedang dipakai',
                            'status' => 422
                       ];
            }else{
                if ($model->delete()) {
                    return $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Data berhasil dihapus'
                       ];
                } else {
                    return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Data Gagal di hapus',
                            'status' => 422
                       ];
                }                
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
        $model = new InstalasiView;
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
            $title = 'Master Instalasi Rumah Sakit';
            $get = $request->get();

            $model = new InstalasiView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if(isset($advancedFilters['nama_rumahsakit'])){
                    $query->andWhere(['ILIKE', 'LOWER(nama_rumahsakit)', strtolower($advancedFilters['nama_rumahsakit'])]);
                }
                if(isset($advancedFilters['instalasi_nama'])){
                    $query->andWhere(['ILIKE', 'LOWER(instalasi_nama)', strtolower($advancedFilters['instalasi_nama'])]);
                }
                if(isset($advancedFilters['instalasi_singkatan'])){
                    $query->andWhere(['ILIKE', 'LOWER(instalasi_singkatan)', strtolower($advancedFilters['instalasi_singkatan'])]);
                }
            }
            $query->orderby(['nama_rumahsakit' => SORT_ASC,
                             'instalasi_nama' => SORT_ASC]);               
            
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
            $title = Yii::t('app', 'Master Instalasi');
            $header = [];
            $footer = [];
            $result = [];
            $get = $request->get();
            
            $model = new InstalasiView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if(isset($advancedFilters['nama_rumahsakit'])){
                    $query->andWhere(['ILIKE', 'LOWER(nama_rumahsakit)', strtolower($advancedFilters['nama_rumahsakit'])]);

                    $header[Yii::t('app', 'Nama Rumah Sakit')] = $advancedFilters['nama_rumahsakit'];
                }
                if(isset($advancedFilters['instalasi_nama'])){
                    $query->andWhere(['ILIKE', 'LOWER(instalasi_nama)', strtolower($advancedFilters['instalasi_nama'])]);

                    $header[Yii::t('app', 'Nama Instalasi')] = $advancedFilters['instalasi_nama'];
                }
                if(isset($advancedFilters['instalasi_singkatan'])){
                    $query->andWhere(['ILIKE', 'LOWER(instalasi_singkatan)', strtolower($advancedFilters['instalasi_singkatan'])]);

                    $header[Yii::t('app', 'Nama Singkatan Instalasi')] = $advancedFilters['instalasi_singkatan'];
                }
            }
            $query->orderby(['nama_rumahsakit' => SORT_ASC,
                             'instalasi_nama' => SORT_ASC]);  
            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $data['Nama_Rumah_Sakit'] = $value['nama_rumahsakit'];
                $data['Nama_Instalasi'] = $value['instalasi_nama'];
                $data['Nama_Singkatan_Instalasi'] = $value['instalasi_singkatan'];
                $data['Satu_Sehat_Organization_ID'] = $value['satusehat_instalasi_id'];
                $result[] = $data;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function sinkronInstalasi($post)
    {
        try{
            $restSync = Yii::$app->docoRest->sinkronisasi;
            $request = $restSync->post('sync-accounting/instalasi', [
                'json' => $post
            ]);

            $response = json_decode($request->getBody(),true);
            return $response['response'];
        }catch(RequestException $e){
            return false;
        } catch(\Exception $e){
            return false;
        }
    }

    private function syncDeleteInstalasi($id)
    {
        try{
            $restSync = Yii::$app->docoRest->sinkronisasi;
            $request = $restSync->get('sync-accounting/instalasi-delete?id=' . $id);

            $response = json_decode($request->getBody(),true);
            return $response['response'];
        }catch(RequestException $e){
            return false;
        } catch(\Exception $e){
            return false;
        }
    }

     /**
     * @author Andri Amirul (andri.amirul@sirs.co.id)
     * @method getInstalasi (Mengambil Data Instalasi Tanpa Pagination)
     * @return Array
     */

    public function actionGetInstalasiDep()
    {
        $request = Yii::$app->request;
        $instalasiPilihan = $request->post();
        $model = Instalasi::find()
        ->where(['is_active'=>true]);
            if(count($instalasiPilihan) > 0){
            $instalasi = [];
            foreach($instalasiPilihan as $key => $value){
                if($value == 'rajal'){
                    $instalasi[] = (new LookUpTransaksiRepositories)->getInstalasiIdRj();
                }else if($value == 'ranap'){
                    $instalasi[] = (new LookUpTransaksiRepositories)->getInstalasiIdRi();
                }
                else if($value == 'fisioterapi'){
                    $instalasi[] = (new LookUpTransaksiRepositories)->getInstalasiIdFisio();
                }
            }
            $model->andWhere(['in', 'instalasi_id', $instalasi]);
        }
        return $model->asArray()->all();
    }

    public function actionView($id) {
        $model = Instalasi::find()->select([
                        'instalasi_m.instalasi_id', 
                        'instalasi_m.instalasi_nama', 
                        'instalasi_m.instalasi_namalainnya', 
                        'instalasi_m.instalasi_singkatan', 
                        'instalasi_m.instalasi_adakamar', 
                        'instalasi_m.is_penunjang', 
                        'instalasi_m.profilers_id',
                        'instalasi_m.is_pelayanan',
                        'instalasi_m.additional_data',
                        'instalasi_m.is_active',
                        'instalasi_m.is_sync',
                        'instalasi_m.lob_id',
                        'satusehat_instalasi.satusehat_instalasi_id as satusehat_instalasi'
                    ])->leftJoin('(
                        SELECT
                            id,
                            instalasi_id,
                            satusehat_instalasi_id,
                            is_active,
                            is_deleted
                        FROM
                            instalasi_satusehat_m
                        WHERE
                            satusehat_instalasi_id IS NOT NULL
                            AND
                            is_active = true
                            AND
                            is_deleted = false
                    ) satusehat_instalasi', 'satusehat_instalasi.instalasi_id = instalasi_m.instalasi_id')
                    ->where('instalasi_m.instalasi_id = '.$id)->asArray()->one();

        return $model;
    }
}