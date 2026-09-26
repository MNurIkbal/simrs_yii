<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoAntrian;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\AntrianView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Loket;
use app\modules\v1\models\KonfigSystem;

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

    public function actionRefreshLayar()
    {
        $data['autirefresh_layarantrian'] = true;
        $mode = Yii::$app->params['mode'];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'display-antrian-'.$mode,
            'message' => json_encode(['data' => $data])
        ]);

        return [
            'title' => 'Berhasil',
            'message' => 'Berhasil',
            'text' => 'Berhasil'
        ];
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
            $data_loket = Loket::findOne(['loket_id'=>$loket_id,'is_active'=>true,'is_deleted'=>false]);
            if(!empty($data_loket)){
                $jenisantrian_id = $data_loket->jenisantrian_id;
            }
            $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));


            $model = Antrian::find();
            $model->andWhere(['is_online'=>false]);
            $model->andWhere(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);
            $model->andWhere(['status_antrian'=>[DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL, DocoConstants::ANTRIAN_STATUS_PANGGIL]]);
            $model->andWhere(['loket_id'=>null]);
            $model->andWhere(['jenisantrian_id'=> $jenisantrian_id]);

            if ($jenisantrian_id == DocoConstants::VAR_JA_PD) {
                $sql = "SELECT konfigantrian_id FROM loket_mp WHERE loket_id = {$loket_id} AND is_deleted = false AND is_active = true";
                $konfigs = Yii::$app->db->createCommand($sql)->queryColumn();
                $model->andWhere(['konfigantrian_id'=>$konfigs]);
            }

            if (!empty($instalasi_id)) {
                if ($jenisantrian_id == DocoConstants::VAR_JA_PEN) {
                    $model->andWhere(['instalasi_id'=>$instalasi_id]);
                }
            }
            
            $model->orderBy([
                'tgl_antrian' => SORT_ASC,
                'antrian_id'=>SORT_ASC
            ]); 
            $antrian = $model->one();
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
        
        $model = $this->getListAntrian($status, $loket_id, $tgl_antrian);
        return [
            'count'=>$model->count()
        ];
    }
    
    public function actionListAntrian($status, $loket_id=null, $tgl_antrian=null)
    {
        $model = $this->getListAntrian($status, $loket_id, $tgl_antrian);
        return $model;
        // return [
        //     'count'=>$model->count(),
        //     'data'=>$model->asArray()->all()
        // ];
    }

    private function getListAntrian($status, $loket_id, $tgl_antrian, $jenisantrian_id = DocoConstants::VAR_JA_PD, $instalasi_id=null) 
    {
        $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));
        
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


        $model = Antrian::find()->where(['is_online' => false]);
        if ($statusAntrian) {
            $model->andWhere(['status_antrian'=>$statusAntrian]);
        }

        if($status == "lewati"){
            $model->andWhere(['loket_id' => $loket_id]);
        }
        if ($status == 'belum_panggil') {
            $model->andWhere(['loket_id'=>null]);
        }

        $sql = "
            SELECT konfigantrian_id,jenisantrian_id FROM infosisaantrian_v
            WHERE loket_id = {$loket_id} order by antrian_id desc limit 1
        ";

        $konfigs = Yii::$app->db->createCommand($sql)->queryall();

        $sql2 = "
            SELECT konfigantrian_id FROM loket_mp
            WHERE loket_id = {$loket_id} and is_deleted = false and is_active = true order by loket_id desc limit 1
        ";

        $konfigs2 = Yii::$app->db->createCommand($sql2)->queryall();

        $jenisantrian_id = empty($konfigs[0]['jenisantrian_id']) ? $jenisantrian_id : $konfigs[0]['jenisantrian_id'];

        if ($loket_id) {
            if ($jenisantrian_id == DocoConstants::VAR_JA_PD) {
                $konfigantrian_id = empty($konfigs2[0]['konfigantrian_id']) ? null : $konfigs2[0]['konfigantrian_id'];
                $model->andWhere(['konfigantrian_id'=>$konfigantrian_id]);
            }
        }

        // $jenisantrian_id = empty($konfigs[0]['jenisantrian_id']) ? $jenisantrian_id : $konfigs[0]['jenisantrian_id'];

        // if ($loket_id) {
        //     if ($jenisantrian_id == DocoConstants::VAR_JA_PD) {
        //         $konfigantrian_id = empty($konfigs[0]['konfigantrian_id']) ? null : $konfigs[0]['konfigantrian_id'];
        //         $model->andWhere(['konfigantrian_id'=>$konfigantrian_id]);
        //     }
        // }

        if (!empty($instalasi_id)) {
            if ($jenisantrian_id == DocoConstants::VAR_JA_PEN) {
                $model->andWhere(['instalasi_id'=>$instalasi_id]);
            }
        }
        
        $model->andWhere(['jenisantrian_id' => $jenisantrian_id]);
        $model->andWhere(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);
        $model->orderBy('tgl_antrian ASC');
        
        return [
            'data' => $model->asArray()->all(),
            'count' => $model->count()
        ];
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
        $tgl_antrian = date('Y-m-d', strtotime(date('Y-m-d')));
        $result = Antrian::find();
        $result->andWhere(['is_online' => false]);
        $result->andWhere(['between', 'tgl_antrian', $tgl_antrian . ' 00:00:00', $tgl_antrian . ' 23:59:59']);
        $result->orderBy('tgl_antrian ASC');
        return $result;
    }

    public function actionPilih($antrian_id)
    {
        $antrian = Antrian::findOne($antrian_id);
        if ($antrian) {
            $antrian->status_antrian = DocoConstants::ANTRIAN_STATUS_PILIH;
            if ($antrian->save()) {
                return [
                    'title' => 'Proses berhasil!',
                    'text' => 'Data Berhasil di pilih',
                    'antrian_id' => $antrian->antrian_id,
                    'no_antrian' => $antrian->no_antrian,
                    'loket_id' => $antrian->loket_id,
                    'teks_panggil' => DocoHelpers::convertAntrian($antrian->no_antrian),
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
                ->andWhere(['is_online' => false])
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


    private function getCountSisaAntrian($status, $loket_id=null, $tgl_antrian=null, $instalasi_id=null)
    {
        $model = $this->getListAntrian($status, $loket_id, $tgl_antrian,null,$instalasi_id);

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

            $data_terlewat_antrian = $this->getListAntrian('lewati', $loket_id, null,null,$instalasi_id);
            $data_sisa_antrian =  $this->getListAntrian('belum_panggil', $loket_id, null,null, $instalasi_id);

            $list_antrian_terlewat = empty($data_terlewat_antrian['data']) ? [] : $data_terlewat_antrian['data'];
            $count_sisa_antrian = empty($data_sisa_antrian['count']) ? 0 : $data_sisa_antrian['count'];
            $limit_antrian = $this->getLimitAntrian();
    
            return [
                'data_antrian_terlewat' => $list_antrian_terlewat,
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

    public function actionComponentAntrianFarmasi($ruangan_id=null)
    {
        try {
            $data_antrian = $this->getListAntrianFarmasi($ruangan_id);

            $data_farmasi = $this->generateAntrianFarmasi($data_antrian);

            return [
                'racikan' => $data_farmasi['racikan'],
                'non_racikan' => $data_farmasi['non_racikan'],
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

    public function getListAntrianFarmasi($ruangan_id)
    {
        $tgl_antrian = date('Y-m-d', strtotime(date('Y-m-d')));

        $konfigSystem = $this->getOrSetCache(DocoConstants::VAR_K_S, KonfigSystem::find(), false);

        $model = AntrianView::find();
        // $model->join('JOIN','lookup_m l','l.lookup_id = t.antrian_farmasi');
        $model->Where(['is_online' => false]);
        $model->andWhere(['jenisantrian_id' => DocoConstants::VAR_JA_F]);
        $model->andWhere(['ruangan_id' => $ruangan_id]);
        $model->andWhere(['is_display' => true]);
        if(isset($konfigSystem['konfig_display_antrian_farmasi_etiket']) && $konfigSystem['konfig_display_antrian_farmasi_etiket'] == TRUE){
            $model->andWhere(['between', new \yii\db\Expression('(tgl_cetak_etiket::date)'), $tgl_antrian, $tgl_antrian]);
            $model->orderBy('tgl_cetak_etiket ASC');
        }else{
            $model->andWhere(['between', new \yii\db\Expression('(tgl_antrian::date)'), $tgl_antrian, $tgl_antrian]);
            $model->orderBy('tgl_antrian ASC');
        }
        return $model->asArray()->all();
    }

    public function actionProsesAntrianFarmasi($ruangan_id = null, $antrian_id = null, $loket_id = null)
    {
        try {

            $model = new Antrian;
            $model = $model->findOne(['antrian_id' => $antrian_id]);

            if ($model) {
                $model->antrian_farmasi = ($model->antrian_farmasi == null)
                                            ? DocoConstants::VAR_SF_2
                                            : (($model->antrian_farmasi == DocoConstants::VAR_SF_4)
                                                ? DocoConstants::VAR_SF_4
                                                : $model->antrian_farmasi + 1);
                $model->loket_id = $loket_id;

                if ($model->save()) {
                    $data_antrian = $this->getListAntrianFarmasi($ruangan_id);

                    $data_farmasi = $this->generateAntrianFarmasi($data_antrian);

                    // set ke display antrian
                    $data_display["proses_antrian_farmasi"] = [
                        'ruangan' => $ruangan_id,
                        'data' => [
                            'racikan' => $data_farmasi['racikan'],
                            'non_racikan' => $data_farmasi['non_racikan']
                        ]
                    ];
                    $mode = Yii::$app->params['mode'];
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'display-antrian-'.$mode,
                        'message' => json_encode(['data' => $data_display])
                    ]);
                    // end set display antrian


                    return [
                        'message'=>Yii::t('app', 'Data antrian telah di proses'),
                        'ruangan_id' => $ruangan_id,
                        'racikan' => $data_farmasi['racikan'],
                        'non_racikan' => $data_farmasi['non_racikan'],
                    ];
                } else {
                    return ['status'=>500, 'message'=>Yii::t('app', 'Data antrian gagal di proses')];
                }
            } else {
                    return ['status'=>500, 'message'=>Yii::t('app', 'Data antrian tidak di temukan')];                
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

    function generateAntrianFarmasi($data_antrian = null)
    {
        $racikan = [
            'sudah_proses' => [],
            'belum_proses' => []
        ];
        $nonracikan = [
            'sudah_proses' => [],
            'belum_proses' => []
        ];

        if ($data_antrian) {
            foreach ($data_antrian as $key => $val) {
                if ($val['fungsiantrian_id'] == 324) {
                    if ($val['antrian_farmasi'] == 586) {
                        $racikan['sudah_proses'][] = $val;
                    } else {
                        $racikan['belum_proses'][] = $val;
                    }
                } else {
                    if ($val['antrian_farmasi'] == 586) {
                        $nonracikan['sudah_proses'][] = $val;
                    } else {
                        $nonracikan['belum_proses'][] = $val;
                    }
                }
            }
        }

        return $data_farmasi = [
            'racikan' => $racikan,
            'non_racikan' => $nonracikan
        ];

    }

    function generateAntrianFarmasiV2($data_antrian = null)
    {
        $racikan = [];
        $nonracikan = [];

        if ($data_antrian) {
            foreach ($data_antrian as $key => $val) {
                if ($val['status_reseptur'] != "Diserahkan") {
                    if ($val['racikan_id'] == 1) {
                        $racikan[] = $val;
                    } else {
                        $nonracikan[] = $val;
                    }
                }
            }
        }

        return $data_farmasi = [
            'racikan' => $racikan,
            'non_racikan' => $nonracikan
        ];

    }


    public function actionPanggilAntrianFarmasi($antrian_id = null)
    {
        try {
            $model = Antrian::find();
            $model->where(['antrian_id'=>$antrian_id]);
            
            $antrian = $model->one();
            
            $results['data'] = $antrian;
            $teks_panggil = DocoHelpers::convertAntrian($antrian->no_antrian);
            $results['teks_panggil'] = $teks_panggil;

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

    public function actionGetDateTimeZone()
    {
        $tgl_antrian = null;
        $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));
        return $tgl_antrian;
    }



}