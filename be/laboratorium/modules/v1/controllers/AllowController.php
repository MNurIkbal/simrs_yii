<?php

/**
 * @author Randy Vianda Putra
 * @todo Allow all Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\ConfigTrait;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\JenisPemeriksaanLab;
use app\modules\v1\models\PemeriksaanLab;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\SampleLab;
use Doco\models\Notifikasi;
use Doco\Notifications\LaboratoriumNotification;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = 'app\modules\v1\models\CaraBayar';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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

    public function actionGetKelompokPemeriksaanLab()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new KelompokPemeriksaanLab;
        $query = $model::find();
        if(isset($get['nama_kelompok'])){
            $query->andWhere(['ILIKE', 'LOWER(nama_kelompok)', strtolower($get['nama_kelompok'])]);
        }
        return $query->asArray()->all();
    }
    public function actionGetJenisPemeriksaanLab()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new JenisPemeriksaanLab;
        $query = $model::find();
        if(isset($get['kelompokpemeriksaanlab_id'])){
            $query->andWhere('kelompokpemeriksaanlab_id = '.$get['kelompokpemeriksaanlab_id']);
        }
        return $query->asArray()->all();
    }
    public function actionGetPemeriksaanLab()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new PemeriksaanLab;
        $query = $model::find()->select([
            'pemeriksaanlab_m.pemeriksaanlab_id',
            'pemeriksaanlab_m.jenispemeriksaanlab_id',
            'pemeriksaanlab_m.daftartindakan_id',
            'pemeriksaanlab_m.pemeriksaanlab_kode',
            'pemeriksaanlab_m.pemeriksaanlab_nama',
            'daftartindakan_m.daftartindakan_nama'
        ])->joinWith([
            'daftarTindakan' => function ($query) {
                $query->select([
                    'daftartindakan_m.daftartindakan_id'
                ]);
            }
        ]);
        if(isset($get['jenispemeriksaanlab_id'])){
            $query->andWhere('jenispemeriksaanlab_id = '.$get['jenispemeriksaanlab_id']);
        }
        return $query->asArray()->all();
    }
    public function actionGetDokterLab()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new DokterView;
        $query = $model::find();
        if(isset($get['nama_pegawai'])){
            $query->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($get['nama_pegawai']) ]);
        }
        if(isset($get['ruangan_id'])){
            $query->andWhere('ruangan_id = '.$get['ruangan_id']);
        }
        return $query->asArray()->all();
    }

    public function actionGetPemeriksaan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new PemeriksaanLab;
        $query = $model::find();
        if (isset($get['pemeriksaanlab_nama'])) {
            $query->andWhere(['ILIKE', 'LOWER(pemeriksaanlab_nama)', strtolower($get['pemeriksaanlab_nama']) ]);
        }

        return $query->asArray()->all();
    }

    public function actionGetSample()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new SampleLab;
        $query = $model::find();
        if (isset($get['nama_sample'])) {
            $query->andWhere(['ILIKE', 'LOWER(nama_sample)', strtolower($get['nama_sample']) ]);
        }

        return $query->asArray()->all();
    }

    /**
     * This function will read notification laboratorium
     * 
     * @param String notifikasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionReadNotif()
    {
        $notifikasi_id = Yii::$app->request->get('notifikasi_id', null);
        if (!empty($notifikasi_id)) {
            Notifikasi::updateAll(['is_read' => true], compact('notifikasi_id'));
            LaboratoriumNotification::updateTotalUnread();
            return $this->responseJson(200, 'Status Notifikasi berhasil diperbarui');
        } else {
            return $this->responseJson(400, 'ID Notifikasi tidak boleh kosong');
        }
    }
    /**
     * This function will read notification laboratorium
     * 
     * @param String notifikasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionInitBucketNotification()
    {
        LaboratoriumNotification::updateTotalUnread();
        return $this->responseJson(200, 'Notifikasi laboratorium terinisiasi.');
    }

    public function actionCaraBayar()
    {
        try {
            return $this->getCaraBayar()->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    private function getCaraBayar()
    {
        $result = Carabayar::find();
        $result->andWhere(['is_active' => TRUE]);
        
        return $result;
    }

}
