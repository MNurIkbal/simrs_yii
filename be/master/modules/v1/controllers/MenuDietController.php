<?php
    /**
    * @author iqbal@docotel.com
    * @since 2018-08-30 10:11:20 
    * @desc 
    */
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\MenuDietView;
use app\modules\v1\models\MenuDietMP;

class MenuDietController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\MenuDiet';

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

    private function Model(){
        $model = new MenuDietView;
        return $model::find();
    }

    private function ModelMaster(){
        $model = new MenuDietMP;
        return $model::find();
    }


    private function queryRequest($query, $filter){

        $query->select(["menudiet_mp.jenisdiet_id", 
            "jenisdiet_m.jenisdiet_nama",
            "string_agg(distinct makanandiet_nama,' <br/> ') as makanandiet_nama" 
            ])
        ->from('menudiet_mp')
        ->leftJoin('jenisdiet_m', 'jenisdiet_m.jenisdiet_id = menudiet_mp.jenisdiet_id')
        ->leftJoin('makanandiet_m', 'makanandiet_m.makanandiet_id = menudiet_mp.makanandiet_id')
        ->orderby(['jenisdiet_nama' => SORT_ASC])
        ->groupBy(["menudiet_mp.jenisdiet_id","jenisdiet_nama"]);
        
        if( isset($filter['jenisdiet_id']) ){
            $query->andwhere(['jenisdiet_id' => $filter['jenisdiet_id'] ]);
        }
        if( isset($filter['makanandiet_nama']) ){
            $query->andFilterWhere(['ILIKE', 'LOWER(makanandiet_nama)', strtolower($filter['makanandiet_nama']) ]);
        }
        $result = $query;

        return $result;
    }

    public function actionIndex()
    {
        try {
            $models = $this->model();
            $request = Yii::$app->request;            
            $get = $request->get();
            $page = $request->get('page',0);
            $limit = 10;
            $perpage = $request->get('per-page',$limit);
            $offset = ($page - 1) * $perpage;

            $advanced = $request->get('advanced-filter');            
            $query = (new \yii\db\Query());                       
            $result = $this->queryRequest($query, $advanced);
            
            $totalCount = $result->count();
            $data = $result->limit($perpage)->offset($offset)->all();

            return [
                'data'=>$data,
                'totalCount'=> $totalCount,
            ];
            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            \Yii::$app->response->statusCode = 500;
        }
    }

    public function actionCreateMenuDiet()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
           
            $model = new MenuDietMP;
            $post = $request->post();
            if(empty($post)){
                $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tidak Di Temukan'
                        ];                        
                return $result;
            }else{
                $model->scenario = 'create';
                $model->jenisdiet_id = $post['jenisdiet_id'];
                $model->makanandiet_id = isset($post['makanandiet_id']) ? count($post['makanandiet_id']) : null ;
                
                if (!$model->validate()) {
                    return [
                        'data' => DocoHelpers::parseError($model->errors,'MenuDietForm'),
                        'status' => 422
                    ];
                }else{

                    $result = $this->savingData($post['makanandiet_id'], $model->jenisdiet_id);                    
                    return $result;
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

    private function savingData($data, $jenisdiet_id){

        $arrData = [];
        foreach ($data as $key => $value) {
            $arrData[] = ['jenisdiet_id'  => $jenisdiet_id,
                        'makanandiet_id'=>$value
                        ];
        }

        foreach ($arrData as $k => $v) {
            $modelMenuDiet = new MenuDietMP;
            $modelMenuDiet->attributes = $v;
            $modelMenuDiet->save(true);
        }                   

        $result = [
                'status' => 200,
                'title' => 'Proses Berhasil',
                'text' => 'Data Berhasil Tersimpan'
            ];

        return $result;

    }

    public function actionUbahMenuDiet()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
           
            $model = new MenuDietMP;
            $post = $request->post();
            if(empty($post)){
                $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tidak Di Temukan'
                        ];                        
                return $result;
            }else{
                $model->jenisdiet_id = $post['jenisdiet_id'];
                $model->makanandiet_id = isset($post['makanandiet_id']) ? count($post['makanandiet_id']) : null ;
                if (!$model->validate()) {
                    return [
                        'data' => DocoHelpers::parseError($model->errors,'MenuDietForm'),
                        'status' => 422
                    ];
                }else{

                    MenuDietMP::deleteAll(['jenisdiet_id' => $model->jenisdiet_id]);
                    $result = $this->savingData($post['makanandiet_id'], $model->jenisdiet_id);                    
                    return $result;
                    
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

    public function actionGetDataByJenisdiet()
    {
        try {
            $get = Yii::$app->request->get();
            $jenisdiet_id = $get['jenisdiet_id'];
            
            $model = new MenuDietMP;
            $query = $this->modelMaster()
                        ->select(['menudiet_mp.jenisdiet_id', 
                                'jenisdiet_m.jenisdiet_kode as kode_jenis',
                                'jenisdiet_m.jenisdiet_nama as nama_jenis',
                                'menudiet_mp.makanandiet_id',
                                'makanandiet_m.makanandiet_kode as kode_makanan',
                                'makanandiet_m.makanandiet_nama as nama_makanan'
                            ])
                        ->leftJoin('jenisdiet_m', 'jenisdiet_m.jenisdiet_id = menudiet_mp.jenisdiet_id')
                        ->leftJoin('makanandiet_m', 'makanandiet_m.makanandiet_id = menudiet_mp.makanandiet_id')
                        ->andWhere(['menudiet_mp.is_deleted' => false, 'menudiet_mp.is_active' => true]);
            if($jenisdiet_id){
                $query->andwhere(['menudiet_mp.jenisdiet_id' => $jenisdiet_id]);
            }

            $result = $query->asArray()->all();
            $res = ['data' => $result];

            return $res;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }    
    
    public function actionExportExcel()
    {
        $title = 'Menu Diet';
        
        $request = Yii::$app->request;
        $get = $request->get();
        
        $model = new MenuDietView;
        $query = $this->model();
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderby(['jenisdiet_nama' => SORT_ASC]);
        $resData = $query->all();

        $no = 0;
        $result = [];
        foreach ($resData as $key => $value) {
            $no ++;
            $data[\Yii::t('app', 'Nama Jenis Diet')] = $value->jenisdiet_nama;
            $data[\Yii::t('app', 'Nama Makanan')] = $value->makanandiet_nama;
            $modified = str_replace('<br/>', "\n", $value['makanandiet_nama']);
            
            $result[] = $data;
        }
        $header = array();
        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Nama Jenis Diet' => 'Tanggal Unduh : ' . date('d M Y'),
            ]
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],$footer,[],true);
        $filePath->save('php://output');
        die;
    }

     /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table menu diet
    */
    public function actionExportPdf()
    {
        try {
            $title = 'Menu Diet';

            $request = Yii::$app->request;
            $model = new MenuDietView;
            $query = $this->model();

            $advanced = $request->get('advanced-filter');            
            $query = (new \yii\db\Query());                       
            $result = $this->queryRequest($query, $advanced);
            $resData = $result->all();

            $result = [];
            $nomor = 0;
            foreach ($resData as $key => $value) {
                $nomor ++;
                $value['rowNum'] = $nomor ;
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

}