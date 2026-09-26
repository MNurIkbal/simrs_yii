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

use app\modules\v1\models\InstalasiView;
use app\modules\v1\models\WarnaTempatTidur;
use app\modules\v1\models\WarnaTempatTidurView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;

use Doco\components\DocoMessages;

class WarnaTempatTidurController extends DocoActiveController
{
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
        return $actions;
    }

    private function Model(){
        $model = new WarnaTempatTidurView;
        return $model::find();
    }


    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new WarnaTempatTidurView;
            $modelLookup = new Lookup;
            
            $query = $this->model();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['kettempattidur_nama' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            \Yii::$app->response->statusCode = 500;
        }
    }

    public function actionCreateWtt()
    {
        try {
            $request = Yii::$app->request;
            $model = new WarnaTempatTidur;
            $post = $request->post();
            $model->attributes = $post;
            if($model->validate()){
                if ($post) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'WarnaTempatTidurForm');
                        return ['data' => $errors,'status' => 422];
                    }
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors,'WarnaTempatTidurForm');
                        return ['data' => $errors,'status' => 422];
            }
        } catch (\yii\db\Exception $e) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE]);
        } catch (\Exception $e) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE]);
        }
    }


    public function actionViewWtt()
    {
        try {
            $request = Yii::$app->request;
            $model = WarnaTempatTidur::findOne($request->get('id'));
            if (!empty($model)) {
                return $model->attributes;
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

    public function actionUpdateWtt()
    {
        try {
            $request = Yii::$app->request;
            $model = WarnaTempatTidur::findOne($request->get('id'));
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'WarnaTempatTidurForm');
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

    public function actionDeleteWtt()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $model = WarnaTempatTidur::findOne($id);
            $modelKamarTempatTidur = new KamarTempatTidur;
            $getDataKamarTempatTidur = $modelKamarTempatTidur::find()->where(['kettempattidur_id'=>$id])->count();
            if($getDataKamarTempatTidur > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Warna Tempat Tidur ini sedang dipakai',
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

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Warna Tempat Tidur';
            $get = $request->get();

            $model = new WarnaTempatTidurView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if(isset($advancedFilters['kettempattidur_nama'])){
                    $query->andWhere(['ILIKE', 'LOWER(kettempattidur_nama)', strtolower($advancedFilters['kettempattidur_nama'])]);
                }
                if(isset($advancedFilters['kettempattidur_warna'])){
                    $query->andWhere(['ILIKE', 'LOWER(kettempattidur_warna)', strtolower($advancedFilters['kettempattidur_warna'])]);
                }
                if(isset($advancedFilters['is_kosong'])){
                    $query->andWhere(['ILIKE', 'LOWER(is_kosong)', strtolower($advancedFilters['is_kosong'])]);
                }
                if(isset($advancedFilters['is_active'])){
                    $query->andWhere(['ILIKE', 'LOWER(is_active)', strtolower($advancedFilters['is_active'])]);
                }
            }
            $query->orderby(['kettempattidur_nama' => SORT_ASC]);        
            
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
            $title = 'Laporan Master Warna Tempat Tidur';
            $result = [];
            $get = $request->get();
            
            $model = new WarnaTempatTidurView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if(isset($advancedFilters['kettempattidur_nama'])){
                    $query->andWhere(['ILIKE', 'LOWER(kettempattidur_nama)', strtolower($advancedFilters['kettempattidur_nama'])]);
                }
                if(isset($advancedFilters['kettempattidur_warna'])){
                    $query->andWhere(['ILIKE', 'LOWER(kettempattidur_warna)', strtolower($advancedFilters['kettempattidur_warna'])]);
                }
                if(isset($advancedFilters['is_kosong'])){
                    $query->andWhere(['ILIKE', 'LOWER(is_kosong)', strtolower($advancedFilters['is_kosong'])]);
                }
                if(isset($advancedFilters['is_active'])){
                    $query->andWhere(['ILIKE', 'LOWER(is_active)', strtolower($advancedFilters['is_active'])]);
                }
            }
            $query->orderby(['kettempattidur_nama' => SORT_ASC]);

            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $data['Keterangan Tempat Tidur'] = $value['kettempattidur_nama'];
                $data['Jenis Kamar'] = ($value['jenis_kamar'] != NULL) ? $value['jenis_kamar'] : '-';
                $data['Warna Tempat Tidur'] = ($value['kettempattidur_warna'] != NULL) ? $value['kettempattidur_warna'] : '-';
                $data['Status Kamar'] = $value['is_kosong'];
                $data['Status'] = $value['is_active'];
                $result[] = $data;
            }

            /* $header = ['Periode'=> date('d F Y H:i:s', strtotime($start)) . ' - '.date('d F Y H:i:s', strtotime($end))
                        ];*/
            $header = [];
            $filePath = DocoHelpers::exportExcel('Master Warna Tempat Tidur', $result, $header, array(
                "uploadPath" => "./uploads",
            ));
            
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
            
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /*public function actionListKabupaten($propinsi) {
        $data = Kabupaten::find()->where(['is_active' => 't', 'is_deleted' => 'f', 'propinsi_id' => $propinsi ]);
        $items = ArrayHelper::map($data->all(), 'kabupaten_id', 'kabupaten_nama');
        return $items;
    }*/

     public function actionListWarnaTempatTidur()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $kamarruangan_id = $get['kamarruangan_id'];
        try {
            $res = [];
            $getDataKamarTempatTidur = KamarRuangan::findOne($kamarruangan_id);
            if(empty($getDataKamarTempatTidur)){
                return $res;
            }else{
                // $arr = json_encode([$getDataKamarTempatTidur->kamarruangan_jenis]);
                $arr = $getDataKamarTempatTidur->kamarruangan_jenis;
                $getWarnaTempatTidur = WarnaTempatTidur::find()
                                        ->where(['kamarruangan_jenis'=>$arr])
                                        ->orderby(['kettempattidur_nama' => SORT_ASC])
                                        ->andWhere(['is_active'=>'t',
                                                    'is_deleted'=>'f',
                                                    'is_kosong'=>'t', 
                                                    ])
                                        ->all();
                                        // ->asArray()->all();
                $res = ArrayHelper::map($getWarnaTempatTidur, 'kettempattidur_id', 'kettempattidur_nama');
                return $res;
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

}