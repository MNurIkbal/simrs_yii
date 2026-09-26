<?php
/**
 * 
 * @description: master untuk CRUD Display Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Displayantrian;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class DisplayantrianController extends \Doco\components\DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\Displayantrian';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["index2"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["update2"] = ["POST", "PUT"];
        $verbs["list-displayantrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];

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
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('displayantrian')) {
                $result->andFilterWhere(['ILIKE', 't.layarantrian_nama', $indexing]);
            }

            $status = $request->post('is_active');
            if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            }
            $result->andWhere(['t.is_deleted' => 'false']);
            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

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
            $model = new Displayantrian;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'displayantrianForm');
                    return [
                        'data' => $errors,
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

    public function actionDeleteDisplay()
    {
        try {
            $request=Yii::$app->request;
            $post=$request->get();
            $id=$post['id'];
            try {
            $delete = (new Displayantrian)->delete($id);
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
                                't.layarantrian_nama',
                                't.jenisantrian_id',
                                't.layarantrian_latarbelakang',
                                't.is_active',
                                't.is_deleted',
                                'lookupjenis.lookup_name as layarantrian_jenis'])->from('layarantrian_m t')
                        ->join('JOIN', 'lookup_m lookupjenis','lookupjenis.lookup_id = t.jenisantrian_id')
                        ->orderBy([ 't.jenisantrian_id' => SORT_ASC ]);

        if ($id) {
            $returnData->where(['t.layarantrian_id' => $id]);
        }

        return $returnData;
    }

    public function actionListDisplayantrian() {
        $items = ArrayHelper::map(Layarantrian::find()->where(['is_active' => true])->all(), 'layarantrian_id', 'layarantrian_judul');

        return $items;
    }

    public function actionListTypeScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'jenis_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    public function actionListFunctionScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'fungsi_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

   public function actionUpdate($id)
    {
         try {
            $request = Yii::$app->request;
            $model = Displayantrian::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->update()) {
                    return [
                        'message' => 'Data Berhasil di ubah',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'DisplayantrianForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
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

    protected $_title = 'Display Antrian';
    public function actionExportExcel($layarantrian_id=null)
    {
        $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('displayantrian')) {
                $result->andFilterWhere(['ILIKE', 't.layarantrian_nama', $indexing]);
            }

            $status = $request->post('is_active');
            if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            }
            $result->andWhere(['t.is_deleted' => 'false']);
            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }
            $data=$result->all();
            $header=[];
        
        // $header = array(
        //     Yii::t('app', "Instalasi") => $instalasi ? $instalasi->instalasi_nama : '',
        //     Yii::t('app', "Poliklinik") => $ruangan ? $ruangan->ruangan_nama : '',
        //     Yii::t('app', "Dokter") => $pegawai ? $pegawai->nama_pegawai : '',
        // );

        $filePath = DocoHelpers::exportExcel($this->_title, $data, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    /**
    * @controller actionCetakPdf 
    * @attribute #layarantrian# => table 
    **/

    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('displayantrian')) {
                $result->andFilterWhere(['ILIKE', 't.layarantrian_nama', $indexing]);
            }

            $status = $request->post('is_active');
            if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            }
            $result->andWhere(['t.is_deleted' => 'false']);
            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }
            $data=$result->all();
            $header=[];
        // $filter = [
        //     'Ruangan nama' => $ruangan ? $ruangan->ruangan_nama : '-',
        //     'Status' => isset($_GET['advanced-filter']['is_active']) ? ($_GET['advanced-filter']['is_active'] == 0) ? 'Aktif' : 'Tidak Aktif' : '-' ,
        // ];
        $print = new DocoPrint();    
        $print->attributes = [
            '#displayantrian#' => $this->renderPartial('index', [
                'filter'=> $header,                
                'detail' => $data,
        'title' => 'Display Antrian'
            ]),            
        ];
        $print->Output();
    }
}