<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-07 16:08:42
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-08 13:59:53
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Persalinan;
use app\modules\v1\models\PersalinanDetail;
use app\modules\v1\models\LookupKeperawatan;

class KalaController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Rizqi Fitrianto <rizqi@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\Persalinan';

    /**
     * @todo Verbs function
     * @author Rizqi Fitrianto <rizqi@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Rizqi Fitrianto <rizqi@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionGetDataKala($id, $admisi, $kala = 1)
    {
        try {
            $getData = Persalinan::find()->where(['pendaftaran_id' => $id, 'pasienadmisi_id' => $admisi]);
            if($kala == 1){
                $getData->select(['pendaftaran_id', 'pasienadmisi_id', 'persalinan_id', 'k1_masalah', 'k1_gariswaspada', 'k1_pelaksanaanmasalah', 'k1_hasil']);

                return $getData->asArray()->one();
            } else if ($kala == 2) {
                $getData->select([
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'persalinan_id',
                    'k2_pendamping',
                    'k2_episitomi',
                    'k2_gawatjanin',
                    'k2_distosiabahu',
                    'k2_tindakanjanin',
                    'k2_tindakandistosia',
                    'k2_masalah',
                    'k2_indikasi',
                    'k2_hasil',
                ]);

                $pendamping = $this->getDataLookupKeperawatan('pendamping');

                return [
                    'kala' => $getData->asArray()->one(),
                    'pendamping' => $pendamping
                ];
            } else if ($kala == 4) {
                $getData->select([
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'persalinan_id',
                    'k4_keadaanumum',
                    'k4_td_systolic',
                    'k4_td_diastolic',
                    'k4_detaknadi',
                    'k4_pernapasan',
                    'k4_masalah',
                ]);

                return [
                    'kala' => $getData->asArray()->one()
                ];
            }
            
        } catch (\yii\db\Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionGetDetailPersalinan($id)
    {
        $model = new PersalinanDetail;
        $query = $model::find()->where([
            'pendaftaran_id' => $id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSimpanPemantauan($id)
    {
        $request = Yii::$app->request;
        $attributes = [
            'pendaftaran_id' => $id,
            'jam_ke' => $request->post('jam_ke'),
            'waktu' => $request->post('waktu'),
            'td_systolic' => $request->post('td_systolic'),
            'td_diastolic' => $request->post('td_diastolic'),
            'detak_nadi' => $request->post('detak_nadi'),
            'suhu' => $request->post('suhu'),
            'tinggi_fundus' => $request->post('tinggi_fundus'),
            'kontraksi_uterus' => $request->post('kontraksi_uterus'),
            'kandung_kemih' => $request->post('kandung_kemih'),
            'darah_keluar' => $request->post('darah_keluar'),
        ];
        $model = new PersalinanDetail;
        $model->attributes = $attributes;
        if ($model->save()) {
            return [
                'title' => 'Proses Berhasil!',
                'text' => 'Data Pemantauan Kala IV Berhasil disimpan.'
            ];
        } else {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }
    }

    public function actionHapusPemantauan($id)
    {
        try {
            $result = (new PersalinanDetail)->delete([
                'persalinandetail_id' => $id
            ]);
            return [
                'title' => 'Proses Hapus Berhasil!',
                'text' => 'Data pemantauan kala IV berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data lookup
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getDataLookupKeperawatan($lookupType)
    {
        $model = LookupKeperawatan::find()->where([
            'like', 'lookup_type', $lookupType
        ])->andWhere([
            'is_active' => true,
            'is_deleted' => false
        ])->all();

        return $model;
    }
}