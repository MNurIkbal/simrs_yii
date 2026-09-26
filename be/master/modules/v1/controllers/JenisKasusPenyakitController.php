<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 13:50:49
 * @Last Modified by:   iqbal@docotel.com
 * @Last Modified time: 2018-10-23 17:49:09
 * @Description: controller untuk master jenis kasus penyakit rajal
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Pendaftaran;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;

class JenisKasusPenyakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisKasusPenyakit';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        // $verbs["listdata"] = ["POST", "GET"];
        // $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisKasusPenyakit;
            $query = $model::find();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
      
                if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($advancedFilters['jeniskasuspenyakit_nama']) ]);
                }

                if (isset($advancedFilters['jeniskasuspenyakit_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_namalainnya)', strtolower($advancedFilters['jeniskasuspenyakit_namalainnya']) ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['jeniskasuspenyakit_m.is_active' =>  $advancedFilters['status'] ]);
                }

            }
            $query->orderby(['jeniskasuspenyakit_m.jeniskasuspenyakit_nama'=> SORT_DESC]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            
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

    /**
    * @author Rizal
    * @since 2018-01-04 10:42:03
    * @return array list data jenis kasus penyakit
    */
    public function actionListData() {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($nama = $request->post('jeniskasuspenyakit_nama')) {
                $result->andFilterWhere(['ILIKE','jeniskasuspenyakit_nama',$nama]);
            }
            if ($namaLainnya = $request->post('jeniskasuspenyakit_namalainnya')) {
                $result->andFilterWhere(['ILIKE','jeniskasuspenyakit_namalainnya',$namaLainnya]);
            }

            $status = $request->post('status');
            $is_active = $status === '0' ? false : true;
            $result->andWhere(['is_active' => $is_active]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'filter'=>$request->post(),
                'data' => $result->asArray()->all(),
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
            $model = new JenisKasusPenyakit;
            $post = $request->post();            
            if(empty($post)){
                $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tidak Di Temukan'
                        ];                        
                return $result;
            }else{
                $model->attributes = $post;
                if (!$model->validate()) {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }else{
                    if (!$model->save()){
                        return [
                                'data' =>  $model->errors,
                                'status' => 422
                            ];
                    } 
                    else{
                        $result = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                        return $result;
                    }
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = JenisKasusPenyakit::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                } else {
                    return [
                        'data' => $model->errors,
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

    /**
    * @author Rizal
    * @since 
    * @param integer id
    * @update : iqbal@docotel.com 
    * @return 
    * @desc 
    */

    public function actionDeleteJenisPenyakit()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $request->get('id');
        try {
            $request = Yii::$app->request;
            $model = JenisKasusPenyakit::findOne($id);
            $modelKasusPenyakitRuangan = new KasusPenyakitRuangan;
            $getKasusPenyakitRuangan = $modelKasusPenyakitRuangan::find()->where(['jeniskasuspenyakit_id'=>$id])->count();
            if($getKasusPenyakitRuangan > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Jenis Kasus Penyakit ini sedang dipakai',
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

    private function getData($id = null)
    {
        $jenisKasusPenyakit = JenisKasusPenyakit::find()
                ->select([
                    'jeniskasuspenyakit_id',
                    'jeniskasuspenyakit_nama',
                    'jeniskasuspenyakit_namalainnya',
                    'jeniskasuspenyakit_urutan',
                    'is_active',
                ]);
        if ($id) {
            $jenisKasusPenyakit->where(['jeniskasuspenyakit_id' => $id]);
        }

        return $jenisKasusPenyakit;
    } 

    /**
    * @author Rizal
    * @since 2018-01-29 11:50:50 
    * @param 
    * @return array map list data penyakit
    * @desc 
    */
    public function actionAllowListPenyakit() {
        $data = JenisKasusPenyakit::find()
        ->where(['is_active' => 't'])
        ->orderBy('jeniskasuspenyakit_nama');
        $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

        return $items;
    }

    /**
     * @author: arief saputra
     * @description: list jenis kasus penyakit
    **/

    public function actionListJenisKasusPenyakit()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getJenisKasusPenyakit();
        $result->select(['jeniskasuspenyakit_id','jeniskasuspenyakit_nama']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(jeniskasuspenyakit_nama)', $term]);
        }
        return $result->asArray()->all();
    }

    public function getJenisKasusPenyakit()
    {
        $data = JenisKasusPenyakit::find();
        return $data;
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $title = 'Master Jenis Kasus Penyakit';
        $get = $request->get();

        $model = new JenisKasusPenyakit;
        $query = $model::find();
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
  
            if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($advancedFilters['jeniskasuspenyakit_nama']) ]);
            }

            if (isset($advancedFilters['jeniskasuspenyakit_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_namalainnya)', strtolower($advancedFilters['jeniskasuspenyakit_namalainnya']) ]);
            }

            if (isset($advancedFilters['status']) ) {
                $query->andFilterWhere(['jeniskasuspenyakit_m.is_active' =>  $advancedFilters['status'] ]);
            }

        }
        $query->orderby(['jeniskasuspenyakit_m.jeniskasuspenyakit_nama'=> SORT_DESC]);
        $resQuery = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataQuery = $resQuery->asArray()->all();
        $result = [];

        $no = 0;
        foreach ($dataQuery as $key => $value) {
            $no ++;
            $data[\Yii::t('app', 'Nama')] = $value['jeniskasuspenyakit_nama'];
            $data[\Yii::t('app', 'Nama Lainnya')] = $value['jeniskasuspenyakit_namalainnya'];
            $data[\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
            
            $result[] = $data;
        }

        $header = array();

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

     /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Jenis Kasus Penyakit';
            $get = $request->get();

            $model = new JenisKasusPenyakit;
            $query = $model::find();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
      
                if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($advancedFilters['jeniskasuspenyakit_nama']) ]);
                }

                if (isset($advancedFilters['jeniskasuspenyakit_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_namalainnya)', strtolower($advancedFilters['jeniskasuspenyakit_namalainnya']) ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['jeniskasuspenyakit_m.is_active' =>  $advancedFilters['status'] ]);
                }

            }
            $query->orderby(['jeniskasuspenyakit_m.jeniskasuspenyakit_nama'=> SORT_DESC]);
            $resQuery = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $resQuery->asArray()->all();
            $result = [];
            foreach ($data as $key => $value) {
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('cetak_jenis_kasus_penyakit', [
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