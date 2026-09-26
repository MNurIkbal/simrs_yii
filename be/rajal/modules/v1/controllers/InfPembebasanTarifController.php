<?php
// Author: Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\PembebasanTarif;
use app\modules\v1\models\InfoPembebasanTarifView;
use app\modules\v1\models\DokterV;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\Pegawai;

class InfPembebasanTarifController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PembebasanTarif';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        unset($actions['update']);
        unset($actions['view']);
        

        return $actions;
    }

    /**
    *
    * @see Fungsi override action index
    * @return array, activeQueryRecords data pembebasan tarif
    *
    */

    public function actionIndex()
    {
        try{
            $request = Yii::$app->request;
            
            $model = new InfoPembebasanTarifView;
            $query = $model::find();

            $tgl_awal = date('Y-m-d') .' 00:00:00';
            $tgl_akhir = date('Y-m-d') .' 23:59:59';

            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            }
            // $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = InfoPembebasanTarifView::find();
        if ($id) {
            $model->where(['pembebasantarif_id' => $id]);
        }

        return $model;
    }

    /**
     *
     * Fungsi update pembebasan tarif pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = PembebasanTarif::findOne($id);

            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->update()) {
                    return ['message' => 'Data Berhasil di ubah'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PembebasanTarifForm');
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

    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id');

            $data_kelaspelayanan = KelasPelayanan::find()->all();

            $data_jeniskasuspenyakit = JenisKasusPenyakit::find()->all();

            $data_dokter = DokterV::find();
            if ($ruangan_id){
                $data_dokter->andWhere(['ruangan_id' => $ruangan_id]);
            }
            $data_dokter = $data_dokter->all();

            $data_statusbayar = Lookup::find();
            $data_statusbayar->andWhere(['lookup_type' => DocoConstants::VAR_LU_SB]);
            $data_statusbayar = $data_statusbayar->all();

            $data_jabatan = Jabatan::find()->all();

            $data_pegawai = Pegawai::find()->all();


            return [
                'data-kelaspelayanan' => $data_kelaspelayanan,
                'data-jeniskasuspenyakit' => $data_jeniskasuspenyakit,
                'data-dokter' => $data_dokter,
                'data-statusbayar' => $data_statusbayar,
                'data-jabatan' => $data_jabatan,
                'data-pegawai' => $data_pegawai,
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

    /**
    * @controller actionExportPdf 
    * @attribute #table# => table data
    **/
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $pembebasantarif_id = $request->get('id');
            $title = Yii::t('app', 'Bukti pembebasan tarif');

            $model = new InfoPembebasanTarifView;
            $query = $model::find();
            $query->andWhere(['pembebasantarif_id' => $pembebasantarif_id]);
            $query->with(['pasien', 'pendaftaran']);
            $data = $query->asArray()->one();

            $header = [];
            $print = new DocoPrint();
            $print->attributes = [
                '#table#' => $this->renderPartial('index',[
                    'title'=> $title,
                    'header'=> $header,
                    'data' => $data,
                ]),
            ];
            $print->Output();

            // return true;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

}