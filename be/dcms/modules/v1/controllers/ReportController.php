<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\models\DocMapping;
use Doco\models\Report;
use yii\helpers\ArrayHelper;

class ReportController extends DocoActiveController
{
    public $modelClass = Report::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["link"] = ["GET"];
        $verbs["code"] = ["GET"];
        $verbs["save"] = ["POST"];
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

    private function dashesToCamelCase($string, $capitalizeFirstCharacter = false) 
    {

        $str = str_replace('-', '', ucwords($string, '-'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public function actionLink()
    {
        try {
            $modules = Yii::$app->request->get('modules');
            $controller = Yii::$app->request->get('controller');
            $actionMethod = Yii::$app->request->get('action');

            $controller = $this->dashesToCamelCase($controller,true);
            $actionMethod = $this->dashesToCamelCase($actionMethod,true);

            $whereClause = [
                'doc_key' => $modules . '-' . $controller .'Controller-action'.$actionMethod
            ];

            $report = Report::find()->joinWith([
                'docmapping' => function($query) use ($whereClause){
                    $query->andWhere($whereClause);
                }
            ])->one();

            return [
                'reportData' => ArrayHelper::getValue($report,'content')
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(),'line'=>$e->getLine()];
        }
    }

    public function actionCode()
    {
        try {
            $model = Report::find()->where([
                'code' => Yii::$app->request->get('reportCode')
            ])->one();

            return [
                'reportData' => ArrayHelper::getValue($model,'content')
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSave()
    {
        try {
            $model = Report::find()->where([
                'code' => Yii::$app->request->post('kode',0)
            ])->one();

            $model->content = Yii::$app->request->post('stidata');
            
            $model->save();
            return [
                'message' => 'Updated' 
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionConfig()
    {
        try {
            $kode = Yii::$app->request->get('kode',0);
            $getCache = Yii::$app->cache->get("cache-report-designer-id");
            if ($getCache == false || !isset($getCache[$kode])) {
                $model = Report::find()->with('docmapping')->where([
                    'code' => $kode
                ])->one();
                $data = [
                    'reportCode' => ArrayHelper::getValue($model,'code'),
                    'reportConfig' => ArrayHelper::getValue($model,'config'),
                    'docMapping' => ArrayHelper::getValue($model,'docmapping')
                ];
                $tmp = [];
                $tmp[$kode] = $data;
                $getCache = $tmp;
                Yii::$app->cache->set("cache-report-designer-id",$tmp);
            }
            return isset($getCache[$kode]) ? $getCache[$kode] : null;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionConfigUrl()
    {
        try {
            $modules = Yii::$app->request->get('modules');
            $controller = Yii::$app->request->get('controller');
            $actionMethod = Yii::$app->request->get('action');

            $controller = $this->dashesToCamelCase($controller,true);
            $actionMethod = $this->dashesToCamelCase($actionMethod,true);

            $whereClause = [
                'doc_key' => $modules . '-' . $controller .'Controller-action'.$actionMethod
            ];

            $report = Report::find()->joinWith([
                'docmapping' => function($query) use ($whereClause){
                    $query->andWhere($whereClause);
                }
            ])->one();

            return [
                'reportCode' => ArrayHelper::getValue($report,'code'),
                'reportConfig' => ArrayHelper::getValue($report,'config'),
                'docMapping' => ArrayHelper::getValue($report,'docmapping')
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionIndex()
    {
        try {
            $model = new Report;
            $query = $model::find()->select([
                'id',
                'docmapping_id',
                'title',
                'config',
                'code',
                'is_file',
                'created_date',
                'created_by',
                'is_deleted',
                'is_active'
            ])->asArray();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSaveConfig($id = null)
    {
        try{
            $request = Yii::$app->request;
            
            $report = $request->post('report',[]);
            $configs = $request->post('config',[]);
            $configs['docmapping_enabled'] = isset($configs['docmapping_enabled']) && $configs['docmapping_enabled'] != false ? true : false;
            $configs['show_in_viewer'] = isset($configs['show_in_viewer']) && $configs['show_in_viewer'] != false ? true : false;
            $configs['render_mode'] = isset($configs['render_mode']) ? $configs['render_mode'] : 'full';

            if(!is_null($id) && !empty($id) && $id != ''){
                $modelReport = Report::findOne($id);
            }else{
                $modelReport = new Report;
            }
            $modelReport->attributes = $report;
            $modelReport->is_file = false;
            $modelReport->config = $configs;
            if ($modelReport->save()) {
                Yii::$app->cache->delete("cache-report-designer-id");
                return [
                    'data' => ['report' => $modelReport->attributes],
                    'message' => 'Data Berhasil di simpan'
                ];
            } else {
                return [
                    'data' => $modelReport->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateConfig($id)
    {
        try{
            $request = Yii::$app->request;
            
            $report = $request->post('report',[]);
            $configs = $request->post('config',[]);
            $configs['docmapping_enabled'] = isset($configs['docmapping_enabled']) && $configs['docmapping_enabled'] != false ? true : false;
            $configs['show_in_viewer'] = isset($configs['show_in_viewer']) && $configs['show_in_viewer'] != false ? true : false;
            $configs['render_mode'] = isset($configs['render_mode']) ? $configs['render_mode'] : 'full';

            $modelReport = Report::findOne($id);
            $modelReport->attributes = $report;
            $modelReport->is_file = false;
            $modelReport->config = $configs;
            if ($modelReport->save()) {
                Yii::$app->cache->delete("cache-report-designer-id");
                return [
                    'data' => ['report' => $modelReport->attributes],
                    'message' => 'Data Berhasil di simpan'
                ];
            } else {
                return [
                    'data' => $modelReport->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetConfig($id)
    {
        $request = Yii::$app->request;

        $report = Report::find()->select(['id','title','docmapping_id','code','filepath','config','is_file','is_active'])->where(['id'=>$id])->one();

        return ['report' => $report];
    }

    public function actionGetDokumen()
    {
        try {
            $model = new DocMapping;
            $query = $model::find()->select(['docmapping_id',"CONCAT(kode_doc,' :: ',nama_doc) as nama_dokumen"])->asArray();
            return new ActiveDataProvider([
                'query' => $query,
                'pagination' =>false
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionAllViewer()
    {
        $request = Yii::$app->request;
        $module_id = $request->get('module_id');
        $roles = $request->get('roles');

        try {
            $query = (new \yii\db\Query())
            ->select(['reports_id', 'code','title'])
            ->from('hakakseslaporan_v')
            ->where("config->>'show_in_viewer' = 'true'")
            ->andWhere([ 'module_id' => $module_id ])
            ->andWhere(['in', 'peranpenggunanama', $roles]);
            return new ActiveDataProvider([
                'query' => $query,
                'pagination' =>false
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}