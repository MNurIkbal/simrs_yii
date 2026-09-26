<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\DokterV;
use app\modules\v1\models\HasilUsg;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\HasilPemeriksaan;
use Doco\components\DocoRestActiveFilter;

class UsgController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\HasilUsg';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["ajax"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', 0);
            $ruangan_id = $request->get('ruangan_id', null);
            $instalasi_id = $request->get('instalasi_id', null);
            $model = new HasilUsg;
            $query = $model::find(true)->with(['pegawai']);
            $query->where(['pendaftaran_id' => $pendaftaran_id]);
            if (!empty($instalasi_id)) {
                $query->leftJoin('ruangan_m', 'ruangan_m.ruangan_id = hasilusg_t.ruangan_id');
                $query->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
            }
            $query->orderBy(['pendaftaran_id' => SORT_DESC]);
            $data = $query->all();
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

    public function actionCreate()
    {
        $request = Yii::$app->request;

        try {
            $model = new HasilUsg;
            $model->attributes = $request->post();

            if (isset($model->pendaftaran_id)) {
                $pendaftaran = Pendaftaran::find()->select(['pasien_id', 'pasienadmisi_id'])->where(['pendaftaran_id' => $model->pendaftaran_id])->asArray()->one();
                $model->pasien_id = ArrayHelper::getValue($pendaftaran, 'pasien_id');
                $model->pasienadmisi_id = ArrayHelper::getValue($pendaftaran, 'pasienadmisi_id');
            }
            if (!$model->validate() || !$model->save()) {
                return [
                    'status' => 422,
                    'message' => $model->errors
                ];
            } else {
                return [
                    'status' => 200,
                    'message' => "Hasil USG berhasil disimpan !"
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetBundle($pendaftaran_id = null)
    {
        $data_dokter = DokterV::find()->select(['pegawai_id', 'nama_pegawai']);
        $dokter = ArrayHelper::map($data_dokter->all(), 'pegawai_id', 'nama_pegawai');

        $pendaftaran = !is_null($pendaftaran_id) ? Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->select(['tgl_pendaftaran', 'pasienadmisi_id'])->asArray()->one() : null;
        $tgl_pendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
        $dpjp_id = null;
        if (!empty($pendaftaran)) {
            $infopendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id]);
            if (!empty(ArrayHelper::getValue($pendaftaran, 'pasienadmisi_id'))) {
                $dpjp_id = $infopendaftaran->select(['dokterri_id as dpjp_id'])->scalar();
            } else {
                $dpjp_id = $infopendaftaran->select(['dokterrj_id as dpjp_id'])->scalar();
            }
        }

        $data_pemeriksaan = HasilPemeriksaan::find()->select(['hasilpemeriksaan_id', 'hasilpemeriksaan_nama']);
        $pemeriksaan = ArrayHelper::map($data_pemeriksaan->all(), 'hasilpemeriksaan_id', 'hasilpemeriksaan_nama');

        return [
            'dokter' => $dokter,
            'tgl_pendaftaran' => $tgl_pendaftaran,
            'dpjp_id' => $dpjp_id,
            'pemeriksaan' => $pemeriksaan,
        ];
    }
}
