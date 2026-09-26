<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\businessLogic\TindakanAkomodasi;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\TindakanPelayanan;

class ApiController extends DocoActiveController
{
    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Model class
     */
    public $modelClass = '';

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi custom verbs
     * @return array $results
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi custom actions
     * @return array $results
     */
    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get list master
     * @return array $results
     */
    public function actionGetDataPasienRanap()
    {
        $results = array();
        $request = Yii::$app->request->get();
        $model = new InfoPasienRiView;
        $data = $model::find();

        if (!empty($request)) {
            foreach ($request as $key => $value) {
                if ($value == '') {
                    $value = null;
                }
                
                $data->andWhere([$key => $value]);
            }
        }

        if (empty($data->one())) {
            return [];
        }

        return $data->one();
    }

    public function actionGetAkomodasiSementara($admisiId, $endDate = null)
    {
        $request = Yii::$app->request;
        $type = $request->get("type", DocoConstants::AKOMODASI_MUTASI);
        if(empty($endDate)) {
            $endDate = date('Y-m-d H:i:s');
        }
        $view = $request->get("view", true);
        $stopAkomodasi = false;
        $statusAkomodasi = Pendaftaran::find()->select(['is_stopakomodasi', 'tgl_stopakomodasi'])->where(['pasienadmisi_id' => $admisiId])->asArray()->one();

        if (!empty($statusAkomodasi) && $statusAkomodasi['is_stopakomodasi']) {
            return [
                'total_akomodasi' => null,
                'tindakan_akomodasi' => null,
                'kelompok_tindakan' => null
            ];
        }

        $viewAkomodasi = TindakanAkomodasi::execute(
            $type, 
            $admisiId,
            $endDate,
            $view,
            $stopAkomodasi
        );
        
        if (!empty($viewAkomodasi)) {
            return $viewAkomodasi;
        } else {
            return false;
        }
    }

    // create  cron akomodasi dan stop akomodasi untuk kebutuhan testing
    public function actionCreateAkomodasi($admisiId = null ,$tgl_cron = null, $tgl_pendaftaran = null, $is_stopakomodasi= false, $tgl_stopakomodasi =  null)
    {
        $configAkomodasi = (new DocoConstansId)->actionGetAdditional('konfig_stop_akomodasi');
        $transaksi = Yii::$app->db->beginTransaction();

        if($is_stopakomodasi){
            $dataPendaftaran = Pendaftaran::find()
            ->where(['pasienadmisi_id' => $admisiId])
            ->one();
            if(empty($tgl_stopakomodasi)){
                $tgl_stopakomodasi = date('Y-m-d H:i:s');
            }
            if ($dataPendaftaran) {
                $idAdmisi = !empty($dataPendaftaran->pasienadmisi_id) ? $dataPendaftaran->pasienadmisi_id : null;
                $dataPendaftaran->is_stopakomodasi = true;
                $dataPendaftaran->tgl_stopakomodasi = $tgl_stopakomodasi;
                if (!$dataPendaftaran->validate()) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $dataPendaftaran->errors
                    ]);
                }
                // kondisi ketika stop akomodasi ditagihkan atau tidak
                // merujuk dari perubahan ketika batal stop akomodasi ada pilihan ditagihkan atau tidak, **default true
                if($dataPendaftaran->is_ditagihkan){
                    $gracePeriod = 0;
                    if(!empty($configAkomodasi)) {
                        $configAkomodasi = json_decode($configAkomodasi, true);
                        $gracePeriod = isset($configAkomodasi[1]) ? $configAkomodasi[1] : 0;
                    }

                    // get latest generate accomodation
                    $getLatestTindakan = (new TindakanAkomodasi)->getTodayAkomodasi($idAdmisi, date('Y-m-d', strtotime($tgl_stopakomodasi)));
                    $executeTindakanAkomodasi = true;

                    /* 
                    * Check if the interval from latest generate accomodation is lower than the grace period range
                    */
                    if (is_null($getLatestTindakan)) {
                        $executeTindakanAkomodasi = true; // execute tindakan akomodasi when there is no accomodation today
                    } else {
                        $dateNow = date('Y-m-d H:i:00', strtotime($tgl_stopakomodasi));
                        $tglTindakan = date('Y-m-d H:i:00', strtotime($getLatestTindakan['tgl_tindakan']));
                        $interval = round((strtotime($dateNow) - strtotime($tglTindakan)) / 3600, 1);

                        if ($gracePeriod != 0 && $interval < $gracePeriod) {
                            // dont execute tindakan akomodasi when hour interval between today last accomodation and current date is lower than grace periode
                            $executeTindakanAkomodasi = false; 
                        }
                    }

                    if ($executeTindakanAkomodasi) {
                        TindakanAkomodasi::execute(DocoConstants::STOP_AKOMODASI, $idAdmisi, $tgl_stopakomodasi);
                    }
                }
                $dataPendaftaran->save();
                $transaksi->commit();
                return DocoHelpers::callBack(DocoMessages::KEY_UPDATED, ['text' => 'Akomodasi Berhasil di Stop']);
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE_PENDAFTARAN_NOT_EXIST]);
            }
        }

        if(!empty($tgl_pendaftaran)){

            $pasien_admisi = PasienAdmisi::find()
            ->where(['pasienadmisi_id' => $admisiId])
            ->one();
            $pasien_admisi->tgl_admisi = $tgl_pendaftaran;
            $pasien_admisi->save(false);

            $masuk_kamar = MasukKamar::find()
            ->where(['pasienadmisi_id' => $admisiId])
            ->one();

            $explode = explode(" ", $tgl_pendaftaran);
            if (count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('H:i:s', strtotime($explode[1]));
            }

            $masuk_kamar->tgl_masukkamar = $start;
            $masuk_kamar->jam_masukkamar = $end;
            $masuk_kamar->save(false);
            $transaksi->commit();
        }

        if(!empty($configAkomodasi)) {
            $configAkomodasi = json_decode($configAkomodasi, true);
            $cutOff = isset($configAkomodasi[0]) ? $configAkomodasi[0] : "";
        }

        if(!empty($tgl_cron)) {
            $now = strtotime("now");
            $tgl_cron = strtotime($tgl_cron.' '.$cutOff);
            $tgl_cron = date('Y-m-d H:i', $tgl_cron);
        }
        $listAkomodasi = [];

        $params = [
            [
                'pasienadmisi_id' => $admisiId,
                'tgl_pendaftaran' => $tgl_pendaftaran,
            ]
        ];
        if(!empty($params)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($params as $key => $value) {
                    $admisiId = ArrayHelper::getValue($value, 'pasienadmisi_id');
                    if(!empty($admisiId)) {
                        $type = DocoConstants::AKOMODASI_MUTASI;
                        $dataAkomodasi = TindakanAkomodasi::execute(
                            $type, 
                            $admisiId,
                            $tgl_cron,
                            true, // $view
                            false, // $stopakomodasi
                            null, // $newKelasPelayananId
                            true // $is_cron
                        );

                        if(!empty($dataAkomodasi)) {
                            $data = ArrayHelper::getValue($dataAkomodasi, 'tindakan_akomodasi', []);
                            if(!empty($data)) {
                                foreach ($data as $key => $value) {
                                    $listAkomodasi[] = [
                                        'kelaspelayanan_id' => ArrayHelper::getValue($value, 'kelaspelayanan_id'),
                                        'pasien_id' => ArrayHelper::getValue($value, 'pasien_id'),
                                        'daftartindakan_id' => ArrayHelper::getValue($value, 'daftartindakan_id'),
                                        'tipepaket_id' => ArrayHelper::getValue($value, 'tipepaket_id'),
                                        'carabayar_id' => ArrayHelper::getValue($value, 'carabayar_id'),
                                        'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                                        'pasienadmisi_id' => ArrayHelper::getValue($value, 'pasienadmisi_id'),
                                        'jeniskasuspenyakit_id' => ArrayHelper::getValue($value, 'jeniskasuspenyakit_id'),
                                        'instalasi_id' => ArrayHelper::getValue($value, 'instalasi_id'),
                                        'kamarruangan_id' => ArrayHelper::getValue($value, 'kamarruangan_id'),
                                        'kamartempattidur_id' => ArrayHelper::getValue($value, 'kamartempattidur_id'),
                                        'ruangan_id' => ArrayHelper::getValue($value, 'ruangan_id'),
                                        'penjamin_id' => ArrayHelper::getValue($value, 'penjamin_id'),
                                        'tgl_tindakan' => ArrayHelper::getValue($value, 'tgl_tindakan'),
                                        'dokterpenanggungjawab_id' => ArrayHelper::getValue($value, 'dokterpenanggungjawab_id'),
                                        'tarif_satuan' => ArrayHelper::getValue($value, 'tarif_satuan', 0),
                                        'qty_tindakan' => 1,
                                        //'qty_tindakan' => isset($value['qty_tindakan']) ? (int) $value['qty_tindakan'] : 1,
                                        'tarif_tindakan' => ArrayHelper::getValue($value, 'tarif_tindakan', 0),
                                        'tarifcyto_tindakan' => ArrayHelper::getValue($value, 'tarifcyto_tindakan', 0),
                                        'cyto_tindakan' => ArrayHelper::getValue($value, 'cyto_tindakan', false),
                                        'discount_tindakan' => ArrayHelper::getValue($value, 'discount_tindakan', 0),
                                        'additional_data' => ArrayHelper::getValue($value, 'additional_data'),
                                        'created_date' => date('Y-m-d H:i:s'),
                                        'is_deleted' => false,
                                        'created_by' => 1,
                                        'is_active' => true,
                                    ];
                                }
                            }
                        }else{
                            Yii::error([
                                'msg' => 'nah kalo masuk sini, berarti data akomodasinya kosong',
                                'admisi_id' => isset($admisiId) ? $admisiId : 0,
                            ]);
                        }
                    }
                }
                if(!empty($listAkomodasi)) {
                    Yii::error([
                        'msg' => 'nah kalo masuk sini, berarti datanya ada siap untuk di insert',
                        'pendaftaran_id' => isset($listAkomodasi[0]['pendaftaran_id']) ? $listAkomodasi[0]['pendaftaran_id'] : 0,
                    ]);

                    TindakanPelayanan::batchInsert($listAkomodasi);
                    $transaction->commit();
                }
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage());
                return $e->getMessage();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage());
                return $e->getMessage();
            }
        }
    }
}
