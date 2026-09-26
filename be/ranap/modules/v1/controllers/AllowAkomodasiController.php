<?php

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\TindakanPelayanan;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;

class AllowAkomodasiController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $getListTarif = $getListTarifHeader = $getListPasien = [];
        /** untuk Menampung saat integrasi akunting */
        $listPasien = [];
        /** untuk menampung semua tindakan pasien yang masih di rawat */
        $listTindakanPasien = [];
        $getListPasienRs = InfoPasienRiView::find()
                    ->andWhere(['!=','status_ranap', DocoConstants::STATUS_RANAP_PULANG])
                    ->asArray()->all();
        
        $listTarif = InfoTarifRs::find()->where([
            'is_akomodasi' => true
        ])->asArray()->all();


        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($listTarif as $key => $value) {
                $komponenId = $value['komponentarif_id'];
                $tindakanId = $value['daftartindakan_id'];
                $ruanganId = $value['ruangan_id'];
                $kelasId = $value['kelaspelayanan_id'];
                $penjaminId = $value['penjamin_id'];
                if ($komponenId == 6) {
                    $getListTarifHeader[$ruanganId][$kelasId][$penjaminId][] = $value;
                    continue;
                } 

                $getListTarif[$tindakanId][$ruanganId][$kelasId][$penjaminId][] = $value;
            }

            foreach ($getListPasienRs as $dataPasien) {
                $ruanganPas = $dataPasien['ruangan_id'];
                $kelasPas = $dataPasien['kelaspelayanan_id'];
                $penjaminPas = $dataPasien['penjamin_id'];
                $listPasien[] = $dataPasien['no_pendaftaran'];
                if (isset($getListTarifHeader[$ruanganPas][$kelasPas][$penjaminPas])) {
                    $listTindakan = $getListTarifHeader[$ruanganPas][$kelasPas][$penjaminPas];
                    foreach ($listTindakan as $dataTindakan) {
                        /** Penampung untuk tindakan komponen */
                        $komponenTindakan = [];
                        $tindakanIdc = $dataTindakan['daftartindakan_id'];
                        if (isset($getListTarif[$tindakanIdc][$ruanganPas][$kelasPas][$penjaminPas])) {
                            /** Get Komponen Detail */
                            foreach ($getListTarif[$tindakanIdc][$ruanganPas][$kelasPas][$penjaminPas] as $komponen) {
                                $komponenTindakan[] = [
                                    'komponentarif_id' => $komponen['komponentarif_id'],
                                    'tindakanpelayanan_id' => null,
                                    'tarif_kompsatuan' => $komponen['harga_tariftindakan'],
                                    'tarif_tindakankomp' => $komponen['harga_tariftindakan'],
                                    'tarifcyto_tindakankomp' => 0,
                                    'subsidiasuransikomp' => 0,
                                    'subsidipemerintahkomp' => 0,
                                    'subsidirumahsakitkomp' => 0,
                                    'iurbiayakomp' => 0,
                                ];
                            }
                        }
                        $listKomponen = [
                            'list_komponen' => $komponenTindakan
                        ];
                        /** Create Tagihan pasien */
                        $listTindakanPasien[] = [
                            'kelaspelayanan_id' => $kelasPas,
                            'pasien_id' => $dataPasien['pasien_id'],
                            'instalasi_id' => DocoConstants::INST_ID_RI,
                            'daftartindakan_id' => $tindakanIdc,
                            'carabayar_id' => $dataPasien['carabayar_id'],
                            'pendaftaran_id' => $dataPasien['pendaftaran_id'],
                            'jeniskasuspenyakit_id' => $dataPasien['jeniskasuspenyakit_id'],
                            'ruangan_id' => $ruanganPas,
                            'penjamin_id' => $penjaminPas,
                            'pasienadmisi_id' => $dataPasien['pasienadmisi_id'],
                            'tgl_tindakan' => date('Y-m-d H:i:s'),
                            'tarif_satuan' => $dataTindakan['harga_tariftindakan'],
                            'tarif_tindakan' => $dataTindakan['harga_tariftindakan'],
                            'qty_tindakan' => 1,
                            'cyto_tindakan' => false,
                            'dokterpenanggungjawab_id' => $dataPasien['dokter_admisi_id'],
                            'additional_data' => json_encode($listKomponen),
                        ];
                    }
                }
            }
            if (!empty($listTindakanPasien)) {
                TindakanPelayanan::batchInsert($listTindakanPasien,true);
            }
            $transaction->commit();
            IntegrasiAkunting::integrateTindakanBmhp($listPasien, DocoConstants::INST_ID_RI);
            return [
                'message' => 'Cron Akomodasi Perawat berhasil'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}