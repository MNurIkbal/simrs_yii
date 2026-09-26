<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\KomponenTarif;

class AccountingSyncController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
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

    public function actionJenisObat()
    {
        try {
            $data_jenis_obat = $list_id_jenis = [];
            $data_sync = JenisObatAlkes::find()->where(['is_sync' => false])->asArray()->all();
            $journalTypes = ['pendapatan', 'beban', 'persediaan'];

            for( $x = 0; $x<count($journalTypes); $x++){
                if (is_array($data_sync) && count($data_sync) > 0) {
                    foreach ($data_sync as $val_data_sync) {
                        $data_jenis_obat[] = [
                            'kode' => $val_data_sync['jenisobatalkes_kode'],
                            'name' => (($journalTypes[$x] == 'pendapatan') ? '' : ucfirst($journalTypes[$x])) . " ". $val_data_sync['jenisobatalkes_nama'],
                            'company_id' => 1,
                            'type' => 'jenis_obat',
                        ];
                        $list_id_jenis[] = $val_data_sync['jenisobatalkes_id'];
                    }
                }
            }

            $restSync = Yii::$app->docoRest->sinkronisasi;

            if (count($data_jenis_obat) < 1) {
                return 'gagal';
            }
            $request = $restSync->post('sync-accounting/jenis-obat-bulk', [
                'json' => $data_jenis_obat
            ]);

            $response = json_decode($request->getBody(), true);
            if ($response) {
                $numberAffectedRows = JenisObatAlkes::updateAll(['is_sync' => true], ['IN', 'jenisobatalkes_id', $list_id_jenis]);
                $isSuccessSync = ($numberAffectedRows === count($data_jenis_obat));
            }
            return $isSuccessSync == true ? 'success' : 'failed';
        } catch (RequestException $e) {
            return 'gagal';
        } catch (\Exception $e) {
            return 'gagal';
        }
    }

    public function actionInstalasi()
    {
        $transaction = Instalasi::getDb()->beginTransaction();
        try {
            $isInstalasiSuccess = false;
            $data_instalasi = $list_instalasi = [];
            $data = Instalasi::find()->where(["is_sync" => null])->asArray()->all();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $key => $val) {
                    $data_instalasi[] = [
                        'instalasi_id' => $val['instalasi_id'],
                        'instalasi_nama' => $val['instalasi_nama'],
                        'instalasi_namalainnya' => $val['instalasi_namalainnya'],
                        'instalasi_singkatan' => $val['instalasi_singkatan'],
                    ];
                    $list_instalasi[] = $val['instalasi_id'];
                }
            }

            $restSync = Yii::$app->docoRest->sinkronisasi;

            if (count($data_instalasi) < 1) {
                return "Synchronize Has Done before";
            }

            $request = $restSync->post('sync-accounting/instalasi-bulk', [
                'json' => $data_instalasi
            ]);

            $response = $request->getBody();
            if ($response) {
                $numberAffectedRowsInstalasi = Instalasi::updateAll(['is_sync' => true], ['IN', 'instalasi_id', $list_instalasi]);

                $isInstalasiSuccess = ($numberAffectedRowsInstalasi === count($data_instalasi));
            }
            $transaction->commit();
            return $isInstalasiSuccess == true ? 'success' : 'failed';
        } catch (RequestException $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actionRuangan()
    {
        $transaction = Ruangan::getDb()->beginTransaction();
        try {
            $data_ruangan = $list_ruangan = [];

            $ruangan = Ruangan::find()->where(['is_sync' => null])->asArray()->all();

            if (is_array($ruangan) && count($ruangan) > 0) {
                foreach ($ruangan as $index => $value) {
                    $data_ruangan[] = [
                        'ruangan_id' => $value['ruangan_id'],
                        'instalasi_id' => $value['instalasi_id'],
                        'ruangan_nama' => $value['ruangan_nama'],
                        'ruangan_namalainnya' => $value['ruangan_namalainnya'],
                        'ruangan_jenispelayanan' => $value['ruangan_jenispelayanan'],
                        'ruangan_singkatan' => $value['ruangan_singkatan'],
                    ];
                    $list_ruangan[] = $value['ruangan_id'];
                }
            }

            $restSync = Yii::$app->docoRest->sinkronisasi;

            if (count($data_ruangan) < 1) {
                return "Gagal Sinkronisasi";
            }

            $request = $restSync->post('sync-accounting/ruangan-bulk', [
                'json' => $data_ruangan
            ]);
            $response = json_decode($request->getBody(), true);

            if ($response) {
                $numberAffectedRowsRuangan = Ruangan::updateAll(['is_sync' => true], ['IN', 'ruangan_id', $list_ruangan]);
                $isInstalasiSuccess = ($numberAffectedRowsRuangan === count($data_ruangan));
            }
            $transaction->commit();
            return $isInstalasiSuccess == true ? 'success' : 'failed';
        } catch (RequestException $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actionKomponenTarif()
    {
        try {
            $data_komponen_tarif = $list_id_komponen = [];
            $data_sync = KomponenTarif::find()->where(['is_sync' => false])->asArray()->all();

            if (is_array($data_sync) && count($data_sync) > 0) {
                foreach ($data_sync as $val_data_sync) {
                    $data_komponen_tarif[] = [
                        'kode' => $val_data_sync['komponentarif_kode'],
                        'name' => $val_data_sync['komponentarif_nama'],
                        'company_id' => 1,
                        'type' => 'komponen_tarif',
                    ];
                    $list_id_komponen[] = $val_data_sync['komponentarif_id'];
                }
            }

            $restSync = Yii::$app->docoRest->sinkronisasi;

            if (count($data_komponen_tarif) < 1) {
                return 'gagal';
            }
            $request = $restSync->post('sync-accounting/jenis-obat-bulk', [
                'json' => $data_komponen_tarif
            ]);

            $response = json_decode($request->getBody(), true);
            if ($response) {
                $numberAffectedRows = KomponenTarif::updateAll(['is_sync' => true], ['IN', 'komponentarif_id', $list_id_komponen]);
                $isSuccessSync = ($numberAffectedRows === count($data_komponen_tarif));
            }
            return $isSuccessSync == true ? 'success' : 'failed';
        } catch (RequestException $e) {
            return 'gagal';
        } catch (\Exception $e) {
            return 'gagal';
        }
    }
}