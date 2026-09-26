<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoAntrian;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\WarnaTempatTidur;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Loket;
use app\modules\v1\models\LoketJenisAntrian;

class AllowAntrianController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    // allow all method without authentication
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }


    /**
    * @author Rizal
    * @since 2018-03-20 16:05:34
    * ALL ABOUT ANTRIAN
    */

    public function actionLimitPanggil()
    {
        $lookup = Lookup::find()->where(['lookup_type'=>DocoConstants::LIMIT_ANTRIAN]);

        $results = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP, $lookup, false, DocoConstants::LIMIT_ANTRIAN);
        return $results;
    }

    public function actionListSisaAntrian($tgl_antrian=null)
    {
        $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));

        $model = $this->getData();
        $model->andWhere([
            'status_antrian'=>[
                DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL,
                DocoConstants::ANTRIAN_STATUS_PANGGIL,
            ]
        ]);
        $model->andWhere(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);
        $model->orderBy('tgl_antrian ASC');
        // $antrian = $model->all();
        return [
            'data' => $model->asArray()->all(),
            'count' => $model->count()
        ];
    }
    public function actionList($status_antrian=null, $tgl_antrian=null)
    {
        $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));

        $model = $this->getData();

        $model->where(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);
        if ($status_antrian) {
            $model->andWhere(['status_antrian'=>$status_antrian]);
        }
        return $model->asArray()->all();
    }

    public function actionNext($loket_id, $tgl_antrian=null, $jenisantrian_id = DocoConstants::VAR_JA_PD, $instalasi_id = null)
    {
        try {
            $jenisantrian_id = is_null($jenisantrian_id) ? 177 : $jenisantrian_id;

            // Get mapping loket ke lantai
            $jenisantriandetail_id = null;
            $loketLantai = LoketJenisAntrian::find()->where(['loket_id' => $loket_id])->one();
            if ($loketLantai != '' && (new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1) {
                $jenisantriandetail_id = $loketLantai->jenisantriandetail_id;
            }

            $data_loket = Loket::findOne(['loket_id'=>$loket_id,'is_active'=>true,'is_deleted'=>false]);
            if(!empty($data_loket)){
                $jenisantrian_id = $data_loket->jenisantrian_id;
            }
            $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));

            // $sql = "
            //     SELECT konfigantrian_id FROM infosisaantrian_v
            //     WHERE loket_id = {$loket_id}
            // ";
            $sql = "
                SELECT konfigantrian_id FROM loket_mp
                WHERE loket_id = {$loket_id} AND is_deleted = false
            ";
            $konfigs = Yii::$app->db->createCommand($sql)->queryColumn();

            $model = Antrian::find();
            $model->andWhere(['is_online'=>false]);
            $model->andWhere(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);
            $model->andWhere(['status_antrian'=>[DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL, DocoConstants::ANTRIAN_STATUS_PANGGIL]]);
            $model->andWhere(['loket_id'=>null]);
            $model->andWhere(['jenisantrian_id'=> $jenisantrian_id]);

            if ($jenisantrian_id == DocoConstants::VAR_JA_PD) {
                $model->andWhere(['or', ['konfigantrian_id'=>$konfigs], ['is_keteranganpasien' => false]]);
            }

            if ($jenisantriandetail_id) {
                $model->andWhere(['jenisantriandetail_id' => $jenisantriandetail_id]);
            }

            if ($jenisantriandetail_id) {
                $model->andWhere(['jenisantriandetail_id' => $jenisantriandetail_id]);
            }

            if ($jenisantriandetail_id) {
                $model->andWhere(['jenisantriandetail_id' => $jenisantriandetail_id]);
            }

            if (!empty($instalasi_id)) {
                if ($jenisantrian_id == DocoConstants::VAR_JA_PEN) {
                    $model->andWhere(['konfigantrian_id'=>$konfigs]);
                }
            }

            $model->orderBy([
                'tgl_antrian' => SORT_ASC,
                'antrian_id'=>SORT_ASC
            ]);
            $antrian = $model->one();
            // return $antrian;
            $results = [];
            if ($antrian) {
                $antrian->status_antrian = DocoConstants::ANTRIAN_STATUS_PANGGIL;
                $antrian->panggilan_ke = $antrian->panggilan_ke + 1;
                $antrian->loket_id = $loket_id;
                if ($antrian->save()) {
                    $results['data'] = $antrian;
                    $teks_panggil = DocoHelpers::convertAntrian($antrian->no_antrian);
                    $results['teks_panggil'] = $teks_panggil;
                    $data = DocoAntrian::getSisaAntrian($jenisantrian_id);
                    $dataSisaAntrian['list_sisa_antrian'] = $data;
                    $mode = Yii::$app->params['mode'];
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'panggil-antrian-'.$mode,
                        'message' => json_encode(['data' => $dataSisaAntrian])
                    ]);
                } else {
                }
            } else {
            }
            return $results;
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

    public function actionPanggilUlang($antrian_id)
    {
        $results = [];
        $antrian = Antrian::findOne($antrian_id);
        if ($antrian) {
            $antrian->panggilan_ke = $antrian->panggilan_ke + 1;
            $antrian->save();

            $results['data'] = $antrian;
            $teks_panggil = DocoHelpers::convertAntrian($antrian->no_antrian);
            $results['teks_panggil'] = $teks_panggil;

            $data = DocoAntrian::getSisaAntrian($antrian->jenisantrian_id);
            $dataSisaAntrian['list_sisa_antrian'] = $data;
            $mode = Yii::$app->params['mode'];
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'panggil-antrian-'.$mode,
                'message' => json_encode(['data' => $dataSisaAntrian])
            ]);

            // nama loket di set di frontend
        }
        return $results;
    }

    public function actionLewati($antrian_id)
    {
        $antrian = Antrian::findOne($antrian_id);
        if ($antrian) {
            $antrian->panggilan_ke = 1;
            $antrian->status_antrian = DocoConstants::ANTRIAN_STATUS_LEWATI;
            $antrian->save();
        }
        return $antrian;
    }

    public function actionBatal($antrian_id)
    {
        $antrian = Antrian::findOne($antrian_id);
        if ($antrian) {
            $antrian->status_antrian = DocoConstants::ANTRIAN_STATUS_BATAL;
            $antrian->save();
        }
        return $antrian;
    }

    public function actionCountSisaAntrian($status, $loket_id=null, $tgl_antrian=null)
    {
        // Get mapping loket ke lantai
        $jenisantriandetail_id = null;
        $loketLantai = LoketJenisAntrian::find()->where(['loket_id' => $loket_id])->one();
        if ($loketLantai != '' && (new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1) {
            $jenisantriandetail_id = $loketLantai->jenisantriandetail_id;
        }

        $model = $this->getListAntrian($status, $loket_id, $tgl_antrian, DocoConstants::VAR_JA_PD, $instalasi_id = null, $jenisantriandetail_id);
        return [
            'count'=>$model->count()
        ];
    }

    public function actionListAntrian($status, $loket_id=null, $tgl_antrian=null)
    {
        $model = $this->getListAntrian($status, $loket_id, $tgl_antrian);
        return [
            'count'=>$model->count(),
            'data'=>$model->asArray()->all()
        ];
    }

    private function getListAntrian($status, $loket_id, $tgl_antrian,$jenisantrian_id = DocoConstants::VAR_JA_PD, $instalasi_id=null, $jenisantriandetail_id = null)
    {
        $jenisantrian_id = is_null($jenisantrian_id) ? 177 : $jenisantrian_id;
        if ($status == 'belum_panggil') {
            $statusAntrian = [
                DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL,
                DocoConstants::ANTRIAN_STATUS_PANGGIL,
            ];
        } elseif ($status == 'pilih') {
            $statusAntrian = DocoConstants::ANTRIAN_STATUS_PILIH;
        } elseif ($status == 'lewati') {
            $statusAntrian = DocoConstants::ANTRIAN_STATUS_LEWATI;
        } elseif ($status == 'batal') {
            $statusAntrian = DocoConstants::ANTRIAN_STATUS_BATAL;
        }
        $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));

        $model = $this->getData();
        if ($statusAntrian) {
            $model->andWhere(['status_antrian'=>$statusAntrian]);
        }
        if($status == "lewati"){
            $model->andWhere(['loket_id' => $loket_id]);

            if ($jenisantriandetail_id) {
                $model->andWhere(['jenisantriandetail_id' => $jenisantriandetail_id]);
            }
        }
        if ($status == 'belum_panggil') {
            if ($jenisantriandetail_id) {
                $model->andWhere(['jenisantriandetail_id' => $jenisantriandetail_id]);
                $model->andWhere(['loket_id' => null]);
            } else {
                $model->andWhere(['loket_id' => null]);
            }
        }

        $data_loket = Loket::findOne(['loket_id' => $loket_id, 'is_active' => true, 'is_deleted' => false]);
        if (!empty($data_loket)) {
            $jenisantrian_id = $data_loket->jenisantrian_id;
        }

        if ($loket_id) {
            $sql = "
                SELECT konfigantrian_id FROM loket_mp
                WHERE loket_id = {$loket_id} AND is_deleted = false AND is_active = true
            ";
            $konfigs = Yii::$app->db->createCommand($sql)->queryColumn();
            if ($jenisantrian_id == DocoConstants::VAR_JA_PD) {
                $model->andWhere(['or', ['konfigantrian_id'=>$konfigs], ['is_keteranganpasien' => false]]);
            } else {
                $model->andWhere(['konfigantrian_id'=>$konfigs]);
            }
        }

        if (!empty($instalasi_id)) {
            if ($jenisantrian_id == DocoConstants::VAR_JA_PEN) {
                $model->andWhere(['instalasi_id'=>$instalasi_id]);
            }
        }

        $model->andWhere(['jenisantrian_id' => $jenisantrian_id]);
        $model->andWhere(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);

        $model->orderBy('tgl_antrian ASC');
        return $model;
        // return [
        //     'data' => $model->asArray()->all(),
        //     'count' => $model->count()
        // ];
    }

    private function getGroupCaraBayarLoket($loket_id)
    {
        $sql = "
            SELECT t.groupcarabayar_id
            FROM konfigantrian_m t
                JOIN loket_mp a ON a.konfigantrian_id = t.konfigantrian_id
                JOIN loket_m b ON b.loket_id = a.loket_id
            WHERE b.loket_id = {$loket_id}
                AND groupcarabayar_id is not NULL
            GROUP BY t.groupcarabayar_id
        ";
        $groupcarabayar = Yii::$app->db->createCommand($sql)->queryColumn();
        return $groupcarabayar;
    }

    private function getData()
    {
        $result = Antrian::find();
        return $result;
    }

    public function actionPilih($antrian_id)
    {
        $antrian = Antrian::findOne($antrian_id);
        if ($antrian) {
            $antrian->status_antrian = DocoConstants::ANTRIAN_STATUS_PILIH;
            $antrian->tglpilih_antrian = date('Y-m-d H:i:s');
            if ($antrian->save()) {
                return [
                    'title' => 'Proses berhasil!',
                    'text' => 'Data Berhasil di pilih',
                    'antrian_id' => $antrian->antrian_id,
                    'no_antrian' => $antrian->no_antrian,
                    'loket_id' => $antrian->loket_id,
                    'pasien_id' => $antrian->pasien_id
                ];
            }
        }


        return DocoHelpers::response($antrian);
    }


    public function actionGetAntrian($antrian_id)
    {
        $antrian = Antrian::findOne($antrian_id);
        return $antrian;
    }

    /**
    * @author Rizal
    * @since 2018-05-04 11:34:23
    * @param no_antrian
    * @param loket_id
    * @return object antrian
    * @desc pilih antrian manual
    */
    public function actionPilihManual($no_antrian, $loket_id)
    {
        try {
            $today = date('Y-m-d');
            $start = $today . ' 00:00:00';
            $end = $today . ' 23:59:59';
            $antrian = Antrian::find()
                ->andWhere(['no_antrian'=>$no_antrian])
                ->andWhere(['<>', 'status_antrian', DocoConstants::ANTRIAN_STATUS_BATAL])
                ->andWhere(['between', 'tgl_antrian', $start, $end])
                ->one();
            if ($antrian) {
                $antrian->loket_id = $loket_id;
                $antrian->status_antrian = DocoConstants::ANTRIAN_STATUS_PILIH;
                $antrian->save();
                return $antrian;
            }

            return ['status'=>404, 'message'=>Yii::t('app', 'Data antrian tidak ditemukan')];
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


    private function getCountSisaAntrian($status, $loket_id=null, $tgl_antrian=null, $instalasi_id=null, $jenisantriandetail_id = null)
    {
        $model = $this->getListAntrian($status, $loket_id, $tgl_antrian, null, $instalasi_id, $jenisantriandetail_id);

        return $model->count();
    }

    private function getLimitAntrian()
    {
        $lookup = Lookup::find()->where(['lookup_type' => DocoConstants::LIMIT_ANTRIAN]);

        $results = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP, $lookup, false, DocoConstants::LIMIT_ANTRIAN);
        return $results;
    }

    public function actionComponentAntrian($loket_id = null, $instalasi_id = null)
    {
        try {
            $jenisantriandetail_id = null;
            $list_antrian_terlewat = $this->getListAntrian('lewati', $loket_id, null, $instalasi_id);
            $data_antrian_terlewat = $list_antrian_terlewat->all();

            // Get mapping loket ke lantai
            $loketLantai = LoketJenisAntrian::find()->where(['loket_id' => $loket_id])->one();
            if ($loketLantai != '' && (new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1) {
                $jenisantriandetail_id = $loketLantai->jenisantriandetail_id;
            }

            $count_sisa_antrian = $this->getCountSisaAntrian('belum_panggil', $loket_id, null, $instalasi_id, $jenisantriandetail_id);
            $limit_antrian = $this->getLimitAntrian();

            return [
                'data_antrian_terlewat' => $data_antrian_terlewat,
                'count_sisa_antrian' => $count_sisa_antrian,
                'limit_antrian' => $limit_antrian
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

    private function WarnaTempatTidur(){
        $model = new WarnaTempatTidur;
        $query = $model->find()
                ->select([
                    'kode_warna',
                    'kettempattidur_nama'
                ])
                ->where(['is_active' => true, 'is_deleted' => false])
                ->orderBy(['kamarruangan_jenis'=> SORT_ASC ]);
        $result = $query->asArray()->all();

        return $result;
    }

    /**
    * @author Iqbal Qurahman
    * @since 2018-09-19 11:08:54
    * @param
    * @return
    * @desc Get Data Warna Tempat Tidur
    */
    public function actionGetWarnaTempatTidur(){

        $request = Yii::$app->request;
        $get = $request->get();
        try{
            $getWarnaBed = $this->WarnaTempatTidur();

            return [
                    'warna_tempat_tidur'=>$getWarnaBed,

                    ];

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

}