<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\DokRekamMedis;
use app\modules\v1\models\MonitoringRekamMedikView;
use app\modules\v1\models\PermintaanDokRm;
use Doco\components\DocoConstants;
use Doco\Notifications\RmNotification;

class MonitoringDokumenController extends DocoActiveController 
{
    public $modelClass = 'app\modules\v1\models\MonitoringRekamMedikView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $from = Yii::$app->request->get('from', null);
        return (new Lookup)->getMonitoringStatus(Yii::$app->jwt->instalasi_id);
    }

    public function actionGetDocumentList()
    {
        return (new MonitoringRekamMedikView)->getDocumentData( Yii::$app->request->get() );
    }

    public function actionUpdateDocumentStatus()
    {
        $post = Yii::$app->request->post();
        $permintaandokrm_id = Yii::$app->request->post('permintaandokrm_id', []);
        $statusdokrm = Yii::$app->request->post('status', null);

        $pendaftaran = Yii::$app->request->post('pendaftaran_id', []);
        $admisi = Yii::$app->request->post('pasienadmisi_id', []);
        if(empty($permintaandokrm_id)) {
            if(empty($pendaftaran)) {
                return $this->responseJson(422, 'Pendaftaran tidak boleh kosong!');
            } else {
                $query = PermintaanDokRm::find()->
                    select(['permintaandokrekammedik_id']);
                if(sizeof($pendaftaran) == sizeof($admisi)) {
                    for($i = 0; $i < sizeof($pendaftaran); $i++) {
                        $query = $query->orWhere([
                            'pendaftaran_id' => $pendaftaran[$i],
                            'pasienadmisi_id' => $admisi[$i],
                        ]);
                    }
                } else {
                    for($i = 0; $i < sizeof($pendaftaran); $i++) {
                        $query = $query->orWhere([
                            'pendaftaran_id' => $pendaftaran[$i],
                            'pasienadmisi_id' => null,
                        ]);
                    }
                }
                $permintaandokrm_id = $query->asArray()->column();
            }
        } 

        if( empty($permintaandokrm_id) || empty($statusdokrm) ) {
            return $this->responseJson(422, 'permintaandokrm_id atau statusdokrm tidak boleh kosong!');
        }

        $lokasirak_id = Yii::$app->request->post('lokasirak_id', null);
        $subrak_id = Yii::$app->request->post('subrak_id', null);
        if($statusdokrm == DocoConstants::DOKRM_RETURN &&  (empty($lokasirak_id) || empty($subrak_id)) ) {
            return $this->responseJson(422, 'Rak atau Subrak tidak boleh kosong!');            
        }

        if($statusdokrm == DocoConstants::DOKRM_REMIND) {
            $dataPermintaanRemind = PermintaanDokRm::find()
                ->select(['permintaandokrekammedik_t.ruangan_id','status_rekam_medik','ruangan_m.ruangan_nama','pasien_m.nama_pasien','pasien_m.no_rekam_medik','pendaftaran_t.no_pendaftaran'])
                ->leftJoin('ruangan_m','ruangan_m.ruangan_id = permintaandokrekammedik_t.ruangan_id')
                ->leftJoin('pendaftaran_t','pendaftaran_t.pendaftaran_id = permintaandokrekammedik_t.pendaftaran_id')
                ->leftJoin('pasien_m','pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->where(['permintaandokrekammedik_id'=>$permintaandokrm_id])
                ->distinct()->asArray()->all();
            RmNotification::documentReminder($dataPermintaanRemind);
        } else {
            $update = PermintaanDokRm::updateAll(['status_rekam_medik' => $statusdokrm], ['in', 'permintaandokrekammedik_id', $permintaandokrm_id]);
            if(!$update) {
                Yii::error([
                    'msg' => $update->getErrors(),
                    'payload' => $post
                ]);
                return $this->responseJson(500, 'Terjadi Kesalahan');
            }

            if($statusdokrm == DocoConstants::DOKRM_RETURN) {
                $pasien_id = PermintaanDokRm::find()->
                    select([
                        'pasien_id'
                    ])->where(['in', 'permintaandokrekammedik_id', $permintaandokrm_id])
                    ->asArray()->all();
                    
                DokRekamMedis::updateAll(['lokasirak_id'=>$lokasirak_id, 'subrak_id'=>$subrak_id], ['in', 'pasien_id', $pasien_id]);
            }
        }
        return $this->responseJson(200, 'Update Status berhasil!');
    }
}