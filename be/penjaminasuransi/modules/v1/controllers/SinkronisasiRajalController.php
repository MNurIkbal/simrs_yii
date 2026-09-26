<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoSyp;
use app\modules\v1\models\Cron;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\LogError;

/**
 * @function : Set Limit exec
 */
ini_set('max_execution_time', '0');
ini_set("memory_limit", "-1");
class SinkronisasiRajalController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['sinkron-rajal'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['sinkron-rajal'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Function sinkronisasi kunjungan rajal
     * @return message
     * @author Erlangga <erlangga@docotel.com>
     */
    public function actionSinkronRajal($command = false)
    {
        $sync = false;
        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $transaction = $connection->beginTransaction();
        try {
            $kunjungan = array();
            $diagnosa = array();
            $tagihan = array();
            $syncKunjungan = false;
            $syncDiagnosa = false;
            $syncTagihan = false;
            $service = 'sinkron-rajal';
            $message = '';
            $cache_sync = $cache->get($service);
            $cron = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_KUNJUNGAN_RJ])->one();
            $sync = $cron->is_sync;
            $last_sync = $cron->cron_tgl_mulai;
            $nextSync = date('Y-m-d 00:00:00', strtotime('+1 days'));
            $tmpNoDaftar = array();
            $tmpDiagnosa = array();
            $tmpTagihan = array();
            $dataKunjungan = array();
            $hapusNoPendaftaran = array();

            if (!$cache_sync) {
                $sync = true;
                $cache->set($service, $sync);
                $tgl_pendaftaran = date('Y-m-d', strtotime($cron->cron_tgl_mulai));
                $jam_pendaftaran = date('H:i:s', strtotime($cron->cron_tgl_mulai));

                $results = DocoSyp::getDataKunjunganRajal([
                    'tgl_pendaftaran' => $tgl_pendaftaran,
                    'jam_pendaftaran' => $jam_pendaftaran
                ], 'GET');

                $dataKunjungan = SyKunjunganPasien::find()->select(['no_pendaftaran', 'status_kunjungan'])->where(" tgl_pulang::date='$tgl_pendaftaran'")->andWhere(['<', 'status_kunjungan', '2'])->andWhere(['<>','instalasi_kode',DocoConstants::RINP])->groupBy(['no_pendaftaran', 'status_kunjungan'])->asArray()->all();

                if (!empty($dataKunjungan)) {
                    foreach ($dataKunjungan as $val) {
                        $tmpNoDaftar[] = $val['no_pendaftaran'];
                    }
                }

                if (isset($results['kunjunganRajal']) && !empty($results['kunjunganRajal'])) {
                    foreach ($results['kunjunganRajal'] as $key => $value) {
                        $tglDaftarRjal = (isset($value['tgl_pendaftaran']) ? $value['tgl_pendaftaran'] : $value['TGL_PENDAFTARAN']);
                        $jamDaftarRjal = (isset($value['jam_pendaftaran']) ? $value['jam_pendaftaran'] : $value['JAM_PENDAFTARAN']);
                        $jamDaftarAkhirRjal = $tglDaftarRjal;
                        $noPendaftaranRjal = (isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : $value['NO_PENDAFTARAN']);

                        if (in_array($noPendaftaranRjal, $tmpNoDaftar)) {
                            $hapusNoPendaftaran[] = $noPendaftaranRjal;
                        }

                        $item = [
                            'no_pendaftaran' => $noPendaftaranRjal,
                            'no_rekammedik' => (isset($value['no_rm'])) ? $value['no_rm'] : $value['NO_RM'],
                            'nama_pasien' => (isset($value['nama_pasien'])) ? $value['nama_pasien'] : $value['NAMA_PASIEN'],
                            'jenis_kelamin' => array_key_exists('jns_kelamin', $value) ? $value['jns_kelamin'] : $value['JNS_KELAMIN'],
                            'tgl_lahir' => array_key_exists('tgl_lahir', $value) ? $value['tgl_lahir'] : $value['TGL_LAHIR'],
                            'umur' => array_key_exists('umur', $value) ? $value['umur'] : $value['UMUR'],
                            'tgl_pendaftaran' => $jamDaftarAkhirRjal,
                            'tgl_pulang' => array_key_exists('tgl_pulang', $value) ? $value['tgl_pulang'] : $value['TGL_PULANG'],
                            'instalasi_kode' => array_key_exists('instalasi_kode', $value) ? $value['instalasi_kode'] : $value['INSTALASI_KODE'],
                            'instalasi_nama' =>  array_key_exists('instalasi_nama', $value) ? $value['instalasi_nama'] : $value['INSTALASI_NAMA'],
                            'ruangan_kode' => array_key_exists('ruangan_kode', $value) ? $value['ruangan_kode'] : $value['RUANGAN_KODE'],
                            'ruangan_nama' => array_key_exists('ruangan_nama', $value) ? $value['ruangan_nama'] : $value['RUANGAN_NAMA'],
                            'carabayar_kode' => array_key_exists('carabayar_kode', $value) ? $value['carabayar_kode'] : $value['CARABAYAR_KODE'],
                            'carabayar_nama' => array_key_exists('carabayar_nama', $value) ? $value['carabayar_nama'] : $value['CARABAYAR_NAMA'],
                            'penjamin_kode' => (isset($value['penjamin_kode'])) ? $value['penjamin_kode'] : $value['PENJAMIN_KODE'],
                            'penjamin_nama' => array_key_exists('penjamin_nama', $value) ? $value['penjamin_nama'] : $value['PENJAMIN_NAMA'],
                            'kelas_kode' => array_key_exists('kelas_kode', $value) ? $value['kelas_kode'] : $value['KELAS_KODE'],
                            'kelas_nama' => array_key_exists('kelas_nama', $value) ? $value['kelas_nama'] : $value['KELAS_NAMA'],
                            'dokter_kode' =>  array_key_exists('dokter_kode', $value) ? $value['dokter_kode'] : $value['DOKTER_KODE'],
                            'dokter_nama' =>  array_key_exists('dokter_nama', $value) ? $value['dokter_nama'] : $value['DOKTER_NAMA'],
                            'status_kunjungan' => DocoConstants::STATUS_BELUM_KOREKSI
                        ];

                        $kunjungan[] = $item;
                    }
                }

                if (!empty($hapusNoPendaftaran)) {
                    $hapusNoPendaftaran = implode("','", $hapusNoPendaftaran);
                    $sql = "delete from sy_kunjungan where no_pendaftaran IN ('$hapusNoPendaftaran')";
                    $deleteKunjungan = Yii::$app->db->createCommand($sql)->execute();
                }

                if (!empty($kunjungan)) {
                    $syncKunjungan = SyKunjunganPasien::batchInsert($kunjungan);
                }

                /* ganti proses agar cron tetap berjalan */
                $modelCronKunjungan = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_KUNJUNGAN_RJ])->one();
                $modelCronKunjungan->cron_tgl_mulai = $nextSync;
                $modelCronKunjungan->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
                $modelCronKunjungan->save();

                if (isset($results['diagnosaRajal']) && !empty($results['diagnosaRajal'])) {
                    foreach ($results['diagnosaRajal'] as $key => $value) {
                        $noDaftarRajal = (isset($value['no_pendaftaran'])) ? $value['no_pendaftaran'] : $value['NO_PENDAFTARAN'];

                        if ($noDaftarRajal) {
                            $modelDiagnosa = SyKunjungan::find()->where(['no_pendaftaran' => $noDaftarRajal])->andWhere(['<', 'status_kunjungan', '2'])->one();

                            if ($modelDiagnosa != '') {
                                if (!isset($tmpDiagnosa[$noDaftarRajal])) {
                                    $tmpDiagnosa[$noDaftarRajal] = $noDaftarRajal;
                                }
                                $item = [
                                    'kunjungan_id' => $modelDiagnosa->kunjungan_id,
                                    'no_pendaftaran' => $noDaftarRajal,
                                    'no_rekammedik' => (isset($value['no_rm'])) ? $value['no_rm'] : $value['NO_RM'],
                                    'kelompok_diagnosa' => array_key_exists('kelompok_diagnosa', $value) ? $value['kelompok_diagnosa'] : $value['KELOMPOK_DIAGNOSA'],
                                    'diagnosa_kode' => array_key_exists('diagnosa_kode', $value) ? $value['diagnosa_kode'] : $value['DIAGNOSA_KODE'],
                                    'diagnosa_nama' => array_key_exists('diagnosa_nama', $value) ? $value['diagnosa_nama'] : $value['DIAGNOSA_NAMA']
                                ];

                                $diagnosa[] = $item;
                            }
                        }
                    }
                }

                if (!empty($tmpDiagnosa)) {
                    $tmpDiagnosaDouble = array();
                    foreach ($tmpDiagnosa as $key) {
                        $tmpDiagnosaDouble[] = $key;
                    }
                    $tmpDiagnosaDouble = implode("','", $tmpDiagnosaDouble);
                    $sql = "delete from sy_kunjungandetail where no_pendaftaran IN ('$tmpDiagnosaDouble')";
                    $hapusDiagnosa = Yii::$app->db->createCommand($sql)->execute();
                }

                if (!empty($diagnosa)) {
                    $syncDiagnosa = SyKunjunganDetail::batchInsert($diagnosa);
                }

                $modelCronDiagnosa = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_DIAGNOSA_RJ])->one();
                $modelCronDiagnosa->cron_tgl_mulai = $nextSync;
                $modelCronDiagnosa->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
                $modelCronDiagnosa->save();

                if ($syncKunjungan) {
                    if (isset($results['tagihanRajal']) && !empty($results['tagihanRajal'])) {
                        foreach ($results['tagihanRajal'] as $key => $value) {
                            $noTagihRajal = (isset($value['no_pendaftaran'])) ? $value['no_pendaftaran'] : $value['NO_PENDAFTARAN'];
                            $modelTagihan = SyKunjungan::find()->where(['no_pendaftaran' => $noTagihRajal])->andWhere(['<', 'status_kunjungan', '2'])->one();

                            if ($modelTagihan != '') {
                                if (!isset($tmpTagihan[$noTagihRajal])) {
                                    $tmpTagihan[$noTagihRajal] = $noTagihRajal;
                                }

                                $item = [
                                    'kunjungan_id' => $modelTagihan->kunjungan_id,
                                    'no_pendaftaran' => $noTagihRajal,
                                    'no_rekammedik' => array_key_exists('no_rm', $value) ? $value['no_rm'] : $value['NO_RM'],
                                    'layanan_kode' => array_key_exists('layanan_kode', $value) ? $value['layanan_kode'] : $value['LAYANAN_KODE'],
                                    'layanan_nama' => array_key_exists('layanan_nama', $value) ? $value['layanan_nama'] : $value['LAYANAN_NAMA'],
                                    'layanan_qty' => (float) array_key_exists('layanan_qty', $value) ? $value['layanan_qty'] : $value['LAYANAN_QTY'],
                                    'layanan_tarif' => (float) array_key_exists('layanan_tarif', $value) ? $value['layanan_tarif'] : $value['LAYANAN_TARIF'],
                                    'tindakan_kode' => array_key_exists('tindakan_kode', $value) ? $value['tindakan_kode'] : $value['TINDAKAN_KODE'],
                                    'bagian_kode' => array_key_exists('bagian_kode', $value) ? $value['bagian_kode'] : $value['BAGIAN_KODE'],
                                    'bagian_nama' => array_key_exists('bagian_nama', $value) ? $value['bagian_nama'] : $value['BAGIAN_NAMA'],
                                    'jasa_rs' => array_key_exists('jasa_rs', $value) ? $value['jasa_rs'] : $value['JASA_RS'],
                                    'jasa_dokter' => array_key_exists('jasa_dokter', $value) ? $value['jasa_dokter'] : $value['JASA_DOKTER'],
                                    'dokter_kode' => array_key_exists('dokter_kode', $value) ? $value['dokter_kode'] : $value['DOKTER_KODE'],
                                    'dokter_nama' => array_key_exists('dokter_nama', $value) ? $value['dokter_nama'] : $value['DOKTER_NAMA'],
                                    'kode_nota' => array_key_exists('kode_nota', $value) ? $value['kode_nota'] : $value['KODE_NOTA'],
                                    'kel_report' => array_key_exists('kel_report', $value) ? $value['kel_report'] : $value['KEL_REPORT'],
                                    'tarifrs_akt' => array_key_exists('tarifrs_akt', $value) ? $value['tarifrs_akt'] : $value['TARIFRS_AKT'],
                                    'no_buktitrans' => array_key_exists('no_buktitrans', $value) ? $value['no_buktitrans'] : $value['NO_BUKTITRANS'],
                                    'kelas_kode' => array_key_exists('kelas_kode', $value) ? $value['kelas_kode'] : $value['KELAS_KODE'],
                                    'tgl_pendaftaran' => array_key_exists('tgl_pendaftaran', $value) ? $value['tgl_pendaftaran'] : $value['TGL_PENDAFTARAN'],
                                    'total_adjust' => array_key_exists('total_adjust', $value) ? $value['total_adjust'] : $value['TOTAL_ADJUST'],
                                    'kode_adjust' => array_key_exists('kode_adjust', $value) ? $value['kode_adjust'] : $value['KODE_ADJUST']
                                ];

                                $tagihan[] = $item;
                            }
                        }
                    }

                    if (!empty($tmpTagihan)) {
                        $tmpTagihanDouble = array();
                        foreach ($tmpTagihan as $key) {
                            $tmpTagihanDouble[] = $key;
                        }
                        $tmpTagihanDouble = implode("','", $tmpTagihanDouble);
                        $sql = "delete from sy_kunjungantagihan where no_pendaftaran IN ('$tmpTagihanDouble')";
                        $hapusTagihan = Yii::$app->db->createCommand($sql)->execute();
                    }

                    if (!empty($tagihan)) {
                        $syncTagihan = SyKunjunganTagihan::batchInsert($tagihan);
                    }

                    $modelCronTagihan = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_TAGIHAN_RJ])->one();
                    $modelCronTagihan->cron_tgl_mulai = $nextSync;
                    $modelCronTagihan->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
                    $modelCronTagihan->save();
                }

                $transaction->commit();
                $cron->is_sync = false;
                $cron->save();
                $cache->delete($service);
                return [
                    'status' => 200,
                    'message' => Yii::t('app', 'Data Rawat Jalan Berhasil Tersinkronisasi.')
                ];
            } else {
                $message = Yii::t('app', 'Sinkronisasi Rawat Jalan Sedang Berjalan.');
                $this->appendError($service, 500, json_encode($message));
                return [
                    'status' => 500,
                    'message' => $message
                ];
            }
        } catch (\yii\db\Exception $e) {
            $cache->delete($service);
            $transaction->rollBack();
            if (!$command) {
                \Yii::$app->response->statusCode = 500;
            }
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $cache->delete($service);
            $transaction->rollBack();
            if (!$command) {
                \Yii::$app->response->statusCode = 500;
            }
            $this->appendError($service, 500, json_encode($e->getMessage()));
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function appendError($service, $status, $msg)
    {
        $model = new LogError;
        $model->nama = $service;
        $model->pesan = $status . " - " . $msg;
        $model->tgl_error = date('Y-m-d H:i:s', strtotime('NOW'));
        $model->save(false);

        return true;
    }
}