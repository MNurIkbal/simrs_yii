<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-25 11:10:06
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-21 14:33:15
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\Expertise;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\ExpertiseView;
use app\modules\v1\models\PemeriksaanRad;

class ExpertiseController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Expertise';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs['update'] = ['POST','PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        // unset($actions['update']);
        return $actions;
    }
    public function actionIndex()
    {
        $model = new ExpertiseView;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionView($id)
    {
        $model = new ExpertiseView;
        $query = $model::find()->where(['expertise_id' => $id])->asArray()->one();

        return $query;
    }
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $title = "Daftar master expertise radiologi";
            $header = array();
            $footer = array();
            $model = new ExpertiseView;
            $query = $model::find();

            if (isset($_GET['advanced-filter']['nama_expertise'])) {
                $header[Yii::t('app', 'Nama Expertise')] = $_GET['advanced-filter']['nama_expertise'];
            }

            if (isset($_GET['advanced-filter']['pemeriksaanrad_id'])) {
                $header[Yii::t('app', 'Nama Pemeriksaan')] = $_GET['advanced-filter']['pemeriksaanrad_id'];
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();
            $result = [];
            foreach ($data as $key => $value) {
                $newData = [];
                $newData[\Yii::t('app', 'Nama expertise')] = $value['nama_expertise'];
                $newData[\Yii::t('app', 'Nama pemeriksaan')] = $value['pemeriksaanrad_nama'];
                $newData[\Yii::t('app', 'Hasil expertise')] = preg_replace("/&#?[a-z0-9]+;/i","",strip_tags($value['hasil_expertise']));
                $newData[\Yii::t('app', 'Kesan')] = preg_replace("/&#?[a-z0-9]+;/i","",strip_tags($value['kesan']));
                $newData[\Yii::t('app', 'Kesimpulan')] = preg_replace("/&#?[a-z0-9]+;/i","",strip_tags($value['kesimpulan']));
                $result[] = $newData;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
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
    * @controller actionExportPdf
    * @attribute #table_expertise# => menampilkan data expertise
    * @attribute #kepala_ruangan# => menampilkan nama kepala ruangan
    * @attribute #tgl_skrg# => menampilkan tgl hari ini
    * @attribute #username# => menampilkan nama pengguna yg mencetak
    * @attribute #tgl_cetak# => menampilkan tgl cetak dokumen
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new ExpertiseView;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();
            $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : '';
            $getKepalaRuangan = PegawaiView::find()->where(['jabatan_id'=>DocoConstants::VAR_J_K_R, 'ruangan_id'=>$ruangan_id])->one();
            $kepala_ruangan = isset($getKepalaRuangan['nama_pegawai']) ? : 'Kepala Ruangan Radiologi';
            $print = new DocoPrint();
            $print->attributes = [
                '#table_expertise#' => $this->renderPartial('index',['data'=>$data]),
                '#kepala_ruangan#' => $kepala_ruangan,
                '#tgl_skrg#' => date('d M Y'),
                '#username#' => 'superadmin',
                '#tgl_cetak#' => date('Y-m-d H:i:s'),
            ];
            $print->Output();
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
    public function actionGetPemeriksaanRad()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new PemeriksaanRad();
        $query = $model::find();
        if(isset($get['pemeriksaanrad_nama'])){
            $query->andWhere(['ILIKE', 'LOWER(pemeriksaanrad_nama)', $get['pemeriksaanrad_nama']]);
        }
        return $query->asArray()->all();
    }
}