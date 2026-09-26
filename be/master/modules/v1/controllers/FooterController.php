<?php

/**
 * @Author: johndoe
 * @Date:   2018-03-35 12:05:06
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-10-01 16:05:44
 * @Description: controller untuk master Header Kertas
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DocFooter;
use app\modules\v1\models\Kertas;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\ProfilRumahSakit;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;


class FooterController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DocFooter';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }


    /**
    * @author johndoe
    * @since 2018-03-35 12:05:06
    * @return array list data header kertas
    */
    public function actionIndex() {
        $request = Yii::$app->request;
        
        $model = new DocFooter;
        $query = $model::find()->joinWith(['kertas'])->select(['docfooter_k.*', 'kertas_k.kertas_nama']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        // jenis kertas
        $modelJenisKertas = new Kertas;
        $queryJenisKertas = $modelJenisKertas::find();
        $queryJenisKertas = DocoRestActiveFilter::advancedFilter($modelJenisKertas, $queryJenisKertas);
        $queryJenisKertas = new ActiveDataProvider([
            'query' => $queryJenisKertas,
        ]);

        // profil rs
        $modelProfilRs = new ProfilRumahSakit;
        $queryProfilRs = $modelProfilRs::find();
        $queryProfilRs = DocoRestActiveFilter::advancedFilter($modelProfilRs, $queryProfilRs);
        $queryProfilRs = new ActiveDataProvider([
            'query' => $queryProfilRs,
        ]);

        return [
            'jenis_kertas' => $queryJenisKertas->getModels(),
            'jenis_footer' => $queryProfilRs->getModels(),
        ];
    }

    public function actionGetFooter($profilrs_id)
    {
        $model = ProfilRumahSakit::find()->where(['profilrs_id' => $profilrs_id])->asArray()->one();
        
        $arrFields = array_keys($model);
        return $arrFields;
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new DocFooter;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post;
                if ($model->save()) {
                    
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    
                    \Yii::$app->response->statusCode = 422;
                    $errors_a = [];
                    foreach ($model->getErrors() as $key => $value) {
                        $errors_m=[];
                        $errors_m['field'] = $key;
                        $errors_m['message'] = $value[0];
                        $errors_a[] = $errors_m;
                    }
                    return $errors_a;
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

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = DocFooter::findOne($id);
            if ($request->post() && !empty($model)) {
                $post = $request->post();
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    \Yii::$app->response->statusCode = 422;
                    $errors_a = [];
                    foreach ($model->getErrors() as $key => $value) {
                        $errors_m=[];
                        $errors_m['field'] = $key;
                        $errors_m['message'] = $value[0];
                        $errors_a[] = $errors_m;
                    }
                    return $errors_a;
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = DocFooter::find()
                        ->select(['docfooter_k.*'])->joinWith([
                        'kertas' => function ($query) {
                            $query->select(['kertas_k.kertas_nama','kertas_k.kertas_id']);
                        }]);
        if ($id) {
            $model->where(['docfooter_id' => $id]);
        }

        return $model;
    }

    public function actionDelete($id)
    {
        try {
            $result = (new DocFooter)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    protected $_title = "Data Master Footer Kertas";
    public function actionExportExcel()
    {
        $model = new DocFooter;
        $query = $model::find()->joinWith(['kertas'])->select(['docfooter_k.*', 'kertas_k.kertas_nama']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->pagination = false;
        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kode Footer')] = $value['kode_footer'];
            $newValue[\Yii::t('app', 'Nama Footer')] = $value['nama_footer'];
            $newValue[\Yii::t('app', 'Gambar Kiri Atas')] = $value['gambar_kiri'];
            $newValue[\Yii::t('app', 'Gambar Kanan Atas')] = $value['gambar_kanan'];
            $result[$key] = $newValue;
        }

        $header = array();

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    /**
    * @controller actionExportPdf
    * @attribute #tgl_cetak# => tgl_cetak
    * @attribute #nama_user# => nama_user
    * @attribute #table_footer# => table
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $nama_usercetak = $request->get('nama_usercetak','');
        $id_usercetak = $request->get('id_usercetak',0);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id'=>$id_usercetak])->asArray()->one();
        
        if(is_null($mNamaPegawai)){
            $nama_user = $nama_usercetak;
        }else{
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $model = new DocFooter;
        $query = $model::find()->joinWith(['kertas'])->select(['docfooter_k.*', 'kertas_k.kertas_nama']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_footer#' => $this->renderPartial('cetakan',['data'=>$query->asArray()->all()]),
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }

}