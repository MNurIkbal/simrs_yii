<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoJwtHttpBearerAuth;
use app\modules\v1\models\InfoSliderM;

class InfoSliderRumahSakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoSliderM';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * @todo Behaviors untuk skip authenticator di beberapa actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    // public function behaviors()
    // {
    //     $behaviors = parent::behaviors();

    //     $behaviors['authenticator'] = [
    //         'class' => DocoJwtHttpBearerAuth::className(),
    //         'except' => ['show-slider'],
    //     ];

    //     $behaviors['access'] = [
    //         'class' => DocoAccessRule::className(),
    //         'except' => ['show-slider'],
    //     ];

    //     return $behaviors;
    // }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'propinsi_m, kabupaten_m, kecamatan_m, kelurahan_m');

        $model = new InfoSliderM;
        $query = $model::find();
        // return $_GET['advanced-filter'];
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
         **/
        $between = false;
        $start = date('Y-m-d');
        $end = date('Y-m-d');

        $startLahir = '';
        $endLahir = '';

        $tanggal_mulai = '';
        $tanggal_selesai = '';

        
        if (isset($_GET['advanced-filter'])) {

            if (isset($_GET['advanced-filter']['tgl_mulai'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_mulai']);
                $_GET['advanced-filter']['tgl_mulai'] = str_replace(',','',$_GET['advanced-filter']['tgl_mulai']);
                $tanggal_mulai = date('Y-m-d', strtotime($_GET['advanced-filter']['tgl_mulai']));

                unset($_GET['advanced-filter']['tgl_mulai']);
                // $between = true;

                $query->where('tgl_mulai >= :tgl_mulai', [':tgl_mulai' => $tanggal_mulai]);
                // $query->andWhere('tgl_selesai <= :tgl_selesai', [':tgl_selesai' => $tanggal_mulai]);
            }

            if (isset($_GET['advanced-filter']['tgl_selesai'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_selesai']);
                $_GET['advanced-filter']['tgl_selesai'] = str_replace(',','',$_GET['advanced-filter']['tgl_selesai']);
                $tanggal_selesai = date('Y-m-d', strtotime($_GET['advanced-filter']['tgl_selesai']));
                
                unset($_GET['advanced-filter']['tgl_selesai']);
                // $between = true;

                // $query->where('tgl_mulai >= :tgl_mulai', [':tgl_mulai' => $tanggal_selesai]);
                $query->andWhere('tgl_selesai <= :tgl_selesai', [':tgl_selesai' => $tanggal_selesai]);
            }

            /*if (isset($_GET['advanced-filter']['tgl_selesai'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_selesai']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_selesai']);
                $between = true;
                
                $query->where('tgl_mulai >= :tgl_mulai', [':tgl_mulai' => $start]);
                $query->andWhere('tgl_selesai <= :tgl_selesai', [':tgl_selesai' => $end]);
            }*/
            
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $is_mobile = Yii::$app->jwt->is_mobile;
        if ($is_mobile) {
            return $query->all();
        } else {
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }
    }

    public function actionShowSlider(){
        $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'propinsi_m, kabupaten_m, kecamatan_m, kelurahan_m');

        $model = new InfoSliderM;
        $query = $model::find();
        $start = date('Y-m-1');
        $end = date('Y-m-d');

        $query->andFilterWhere(['<=', 'tgl_mulai', $end])->andFilterWhere(['>=', 'tgl_selesai', $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        // /media/img/info-slider/
       

        $data = array();
        $response = $query->asArray()->all();
        if(!empty($response)){
            foreach ($response as $kv => $vv) {
                $data[$kv] = array(
                    "infoslider_id"=> $vv["infoslider_id"] ,
                    "profilrs_id"=> $vv["profilrs_id"] ,
                    "tgl_mulai"=> $vv["tgl_mulai"] ,
                    "tgl_selesai"=> $vv["tgl_selesai"] ,
                    "judul"=> $vv["judul"] ,
                    "file_gambar"=> Yii::$app->urlManagerFrontend->createUrl('')."media/img/info-slider/".$vv["file_gambar"] ,
                    "additional_data"=> $vv["additional_data"] ,
                    "created_date"=> $vv["created_date"] ,
                    "created_by"=> $vv["created_by"] ,
                    "modified_count"=> $vv["modified_count"] ,
                    "last_modified_date"=> $vv["last_modified_date"] ,
                    "last_modified_by"=> $vv["last_modified_by"] ,
                    "is_deleted"=> $vv["is_deleted"] ,
                    "is_active"=> $vv["is_active"] ,
                    "deleted_date"=> $vv["deleted_date"] ,
                    "deleted_by"=> $vv["deleted_by"]
                );
            }
        }

        return $data;
    }

    public function actionCreated()
    {
        $post = \Yii::$app->request->post();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        // return $post;
        try {
            $InfoSlider = new InfoSliderM;
            if (!empty($InfoSlider)) {
    
                $tgl_mulai = date('Y-m-d', strtotime($post["tgl_mulai"]));
                $tgl_selesai = date('Y-m-d', strtotime($post["tgl_selesai"]));
                // handle Stored XSS - temuan Pentest
                // $judul = \yii\helpers\HtmlPurifier::process($post["judul"]);


                $inputSlider = array(
                    "profilrs_id" => 1,
                    "tgl_mulai" => $tgl_mulai,
                    "tgl_selesai" => $tgl_selesai,
                    "judul" => $post["judul"],
                    // "judul" => $judul,
                    "file_gambar" => $post["file_gambar"],
                );
                // $InfoSlider_m = new ProfilRumahSakit;
                $InfoSlider->attributes = $inputSlider;

                // return $inputProfilRs;
                if ($InfoSlider->validate() && $InfoSlider->save()) {
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan'
                    ];
                } else {
                    $result['status'] = 422;
                    $result['data'] = $InfoSlider->errors;
                }

            } else {
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => ''
                ];
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = InfoSliderM::findOne($id);

            if (!empty($model)) {
                // $model->logo_rumahsakit = '/media/img/profil-rs/' . $model->logo_rumahsakit;
                return $result = [
                    'status' => 200,
                    'data' => $model
                ];
            } else {
                return $result = [
                    'status' => 500,
                    'data' => array()
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

    public function actionUpdated($id)
    {
        $post = \Yii::$app->request->post();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        // return $post;
        try {
            $InfoSlider = InfoSliderM::findOne(['infoslider_id'=>$id]);
            if (!empty($InfoSlider)) {

                $tgl_mulai = date('Y-m-d', strtotime($post["tgl_mulai"]));
                $tgl_selesai = date('Y-m-d', strtotime($post["tgl_selesai"]));
                // handle Stored XSS - temuan Pentest
                $judul = \yii\helpers\HtmlPurifier::process($post["judul"]);

                $gambar = $InfoSlider->file_gambar;
                if(!empty($post["file_gambar"])){
                    $gambar = $post["file_gambar"];
                }

                $inputSlider = array(
                    "profilrs_id" => 1,
                    "tgl_mulai" => $tgl_mulai,
                    "tgl_selesai" => $tgl_selesai,
                    "judul" => $judul,
                    "file_gambar" => $gambar,
                );
                // $InfoSlider_m = new ProfilRumahSakit;
                $InfoSlider->attributes = $inputSlider;

                // return $inputProfilRs;
                if ($InfoSlider->save()) {
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan'
                    ];
                } else {
                    $result['status'] = 500;
                    $result['title'] = 'Proses Gagal';
                    $result['text'] = $InfoSlider->getErrors();
                }

            } else {
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => ''
                ];
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDeleted($id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = InfoSliderM::findOne(['infoslider_id'=>$id]);

            if ($model->delete()) {
                return $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data Berhasil Dihapus'
                ];
            } else {
                return $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Data Gagal Dihapus',
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

        // $id = $get['id'];
        // $check = InfoSliderM::find()->where([
        //     'infoslider_id' => $id
        // ])->one();

        // if (empty($check)) {
        //     $delete = (new InfoSliderM)->delete($id);
        //     return [
        //         'title' => 'Proses Berhasil !',
        //         'text' => 'Data berhasil dihapus'
        //     ];
        // }
        // return [
        //     'status' => 422,
        //     'title' => 'Proses Gagal !',
        //     'text' => 'Data sudah di gunakan'
        // ];
    }
}