<?php
/**
 * 
 * @description: master untuk CRUD Klasifikasi Pasien
**/

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Klasifikasipasien;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class KlasifikasipasienController extends \Doco\components\DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\Klasifikasipasien';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        $verbs["list-klasifikasipasien"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        // $verbs["list-function-screen"] = ["POST", "GET"];

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $modelklasifikasipasien = new Klasifikasipasien;
            $result = $modelklasifikasipasien::find()->orderBy(['klasifikasipasien_id' => SORT_DESC]);

            $query = DocoRestActiveFilter::advancedFilter($modelklasifikasipasien, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

            return [
                'data' => $result->all(),
                'count' => $result->count()
            ];
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


    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Klasifikasipasien;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KlasifikasipasienForm');
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
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

    public function actionDelete()
    {
        try {
            $request=Yii::$app->request;
            $post=$request->get();
            $id=$post['id'];
            try {
            $delete = (new Klasifikasipasien)->delete($id);
            if($delete){
                return "done";
            }
            } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
            }   
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

    public function actionView($id)
    {
        return $this->getData($id)->one();
        // return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                'klasifikasipasien_id',
                                'klasifikasipasien_nama',
                                'klasifikasipasien_kode' => 'klasifikasipasien_kode',
                                'is_active',
                                'is_deleted' 
                            ])->from('klasifikasipasien_m')
                                ->where(['is_deleted' => false]);
                        //->join('JOIN', 'lookup_m lookupjenis','lookupjenis.lookup_id = t.jenisantrian_id')
                        // ->orderBy([ 't.klasifikasipasien_id' => SORT_ASC ]);

        if ($id) {
            $returnData->andWhere([
                'klasifikasipasien_id' => $id]);
        }

        return $returnData;
    }

    public function actionListKlasifikasipasien() {
        $items = ArrayHelper::map(Klasifikasipasien::find()->where(['is_active' => true])->all(), 'klasifikasipasien_id', 'klasifikasipasien_nama');

        return $items;
    }

    public function actionListTypeScreen() {
        $items = ArrayHelper::map(Klasifikasipasien::find()->where(['is_deleted' => false])->all(), 'klasifikasipasien_id', 'klasifikasipasien_nama');

        return $items;
    }

    // public function actionListTypeScreen() {
    //     $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'jenis_antrian'])->all(), 'lookup_id', 'lookup_name');

    //     return $items;
    // }

    // public function actionListFunctionScreen() {
    //     $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'fungsi_antrian'])->all(), 'lookup_id', 'lookup_name');

    //     return $items;
    // }

   public function actionUpdate($id)
    {
         try {
            $request = Yii::$app->request;
            $model = Klasifikasipasien::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->update()) {
                    return [
                        'message' => 'Data Berhasil di ubah',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KlasifikasipasienForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            // throw new Exception("Data Tidak Di Temukan");
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

    protected $_title = 'Klasifikasi Pasien';
    public function actionExportExcel($klasifikasipasien_id=null)
    {
        $request = Yii::$app->request;
        $result = $this->getData();
        $model = new Klasifikasipasien;
        // $result = $model::find()->select([
        //     'klasifikasipasien_id',
        //     'klasifikasipasien_nama',
        //     'kode_klasifikasi_pasien' => 'klasifikasipasien_kode',
        //     '(case when is_active = TRUE THEN \'Aktif\' ELSE \'Tidak Aktif\' END) as status'
        // ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $result);
        // $header=[];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // $data = $query->all();

        // foreach ($data as $key => $value) {
        //     if ($value['is_active']== true){
        //         $data[$key]['is_active']='Aktif';
        //     } else {
        //         $data[$key]['is_active']='Tidak Aktif';     
        //     }
        // }
        
        $dataklasifikasi = [];
        foreach ($dataProvider->getModels() as $key => $value){
            $newValue = [];
            $newValue[\Yii::t('app', 'Nama Klasifikasi Pasien')] = $value['klasifikasipasien_nama'];
            $newValue[\Yii::t('app', 'Kode Klasifikasi Pasien')] = $value['klasifikasipasien_kode'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';

            $dataklasifikasi[$key] = $newValue;
        }

        $klasifikasipasien = new Klasifikasipasien;
        $klasifikasipasien_nama = '';
        $klasifikasipasien_kode = '';
        $is_active = '';

        if (isset($_GET['advanced-filter']['klasifikasipasien_id'])) {
            $queryKlasifikasipasien = $klasifikasipasien->findOne($_GET['advanced-filter']['klasifikasipasien_id']);
            $klasifikasipasien_nama = ($$queryKlasifikasipasien) ? $queryKlasifikasipasien->klasifikasipasien_nama : '';
        }

        // $header = array(
        //     Yii::t('app', "klasifikasipasien_nama") => (@$_GET['advanced-filter']['klasifikasipasien_nama']),
        //     Yii::t('app', "kode_klasifikasi_pasien") => ($klasifikasipasien_kode),
        //     Yii::t('app', "is_active") => ($is_active),
        // );
        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Nama Klasifikasi Pasien' => 'Tanggal Unduh : ' . date('d M Y'),
            ]
        ];

        $filePath = DocoHelpers::exportExcel($this->_title, $dataklasifikasi, [], [],$footer,[],true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionCetakPdf 
    * @attribute #klasifikasipasien# => table 
    **/

    public function actionCetakPdf()
    {
        $model = new Klasifikasipasien;
        $query = $model::find(true)->where(['is_deleted' => false]);


        if(isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['klasifikasipasien_nama'])) {
                $_GET['advanced-filter']['klasifikasipasien_id'] = $_GET['advanced-filter']['klasifikasipasien_nama'];
            }
        }




        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $result = ['data'=>$data];
        $print = new DocoPrint();            
        $print->attributes = [
            '#klasifikasipasien#' => $this->renderPartial('index',$result),
        ];
        $print->Output();

        // $request = Yii::$app->request;
        // $result = $this->getData()
        //                     ->limit($request->post('length',10))
        //                     ->offset($request->post('start',0));

        //     if ($indexing = $request->post('klasifikasipasien')) {
        //         $result->andFilterWhere(['ILIKE', 't.klasifikasipasien_nama', $indexing]);
        //     }

        //     $status = $request->post('is_active');
        //     if($status) {
        //         $status = $status ? true : false;
        //         $result->andWhere(['t.is_active' => $status]);
        //     }
        //     $result->andWhere(['t.is_deleted' => 'false']);
        //     if ($order = $request->post('orderby')) {
        //         $dir = (int) $request->post('dir');
        //         $result->orderby([$order => $dir]);
        //     }
        //     $data=$result->all();
        //     $header=[];
        // // $filter = [
        // //     'Ruangan nama' => $ruangan ? $ruangan->ruangan_nama : '-',
        //    // 'Status' => isset($_GET['advanced-filter']['is_active']) ? ($_GET['advanced-filter']['is_active'] == 0) ? 'Aktif' : 'Tidak Aktif' : '-' ,
        // // ];
        // $print = new DocoPrint();    
        // $print->attributes = [
        //     '#klasifikasipasien#' => $this->renderPartial('index', [
        //         'filter'=> $header,                
        //         'detail' => $data,
        // 'title' => 'Klasifikasi Pasien'
        //     ]),            
        // ];
        // $print->Output();
    }
}