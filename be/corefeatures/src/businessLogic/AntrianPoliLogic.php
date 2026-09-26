<?php

namespace SirsCore\businessLogic;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

use app\modules\v1\models\PendaftaranOnline;
use SirsCore\models\Antrian;
use SirsCore\models\KonfigSystem;
use SirsCore\models\KuotaDokterR;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\antrian\KonfigAntrian;
use Doco\models\Pendaftaran;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Business logic ini untuk melakukan handle flow dari RSUD Sinjai.
 *
 * @author Maulana Muhammad Rizky
 */
class AntrianPoliLogic
{
    /**
     * Checking konfig.
     * Konfigurasi harus mempunyai value false agar tidak tabrakan dengan antrian global.
     *
     * @author <Maulana Muhammad Rizky>
     * @return bool
     */
    public static function isKonfig()
    {
        $konfigSystemK = KonfigSystem::find()
            ->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])
            ->asArray()->one();


        if (!empty($konfigSystemK)) {
            if ($konfigSystemK['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                $konfigAntrianGlobal = Yii::$app->db->createCommand("
                    SELECT
                        additional_value
                    FROM lookuptransaksi_m
                    WHERE kode_transaksi = 'konfig_antrian_prefix_dokter'
                ")->queryOne();

                if (!empty($konfigAntrianGlobal) && $konfigAntrianGlobal['additional_value'] == 'false') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Function ini berfungsi untuk melakukan pengembalian kuota pendaftaran.
     * jangan lupa pastikan konfignya harus true.
     *
     * @author <Maulana Muhammad Rizky>
     * @param int $jadwalbukapoli_id
     * @return bool
     */
    public static function revertKuotaPendaftaran($jadwalbukapoli_id, $is_online = false)
    {
        $konfig = self::isKonfig();
        if ($konfig == true) {
            $kuotaAntrian = KuotaDokterR::find()
                ->where([
                    'jadwalbukapoli_id' => $jadwalbukapoli_id
                ]);

            if ($is_online == true) {
                $kuotaAntrian->andWhere(['is_online' => true]);
            } else {
                $kuotaAntrian->andWhere(['is_online' => false]);
            }

            $result = $kuotaAntrian->one();

            if (!empty($result)) {
                if ($result->kuota_masuk != $result->kuota_tersedia) {
                    $result->kuota_keluar -= 1;
                    $result->kuota_tersedia += 1;
                    $result->save();
                }
            }
        }
    }

    /**
     * Function ini berfungsi untuk melakukan pengembalian kuota pendaftaran.
     * jangan lupa pastikan konfignya harus false.
     *
     * @author <Maulana Muhammad Rizky>
     * @param int $jadwaldokter_id
     * @return bool
     */
    public static function revertKuotaPoli($jadwaldokter_id, $is_online = false)
    {
        $kuotaPoli = KuotaDokterR::find()
            ->where([
                'jadwaldokter_id' => $jadwaldokter_id
            ]);

        if ($is_online) {
            $kuotaPoli->andWhere(['is_online' => true]);
        } else {
            $kuotaPoli->andWhere(['is_online' => false]);
        }

        $result = $kuotaPoli->one();

        if (!empty($result)) {
            if ($result->kuota_masuk != $result->kuota_tersedia) {
                $result->kuota_keluar -= 1;
                $result->kuota_tersedia += 1;
                $result->save();
            }
        }
    }

    /**
     * Function ini berfungsi untuk mengruangi kuota dokter dari pendaftaran.
     * jangan lupa pastikan konfigjadwalbukapoli_idnya harus false.
     *
     * @author <Maulana Muhammad Rizky>
     */
    public function kurangiKuotaDokter()
    {
        return true;
    }

    /**
     * Function ini berfungsi melakukan validasi kuota dokter apakah masih tersedia atau tidak.
     *
     * @author <Maulana Muhammad Rizky>
     */
    public static function validateKuotaDokter($dokter_id, $antrianId)
    {
        try {
            $kuotaDokter = self::checkKuotaDokter($dokter_id);

            if (!empty($kuotaDokter)) {
                $jadwaldokter = ArrayHelper::getValue($kuotaDokter, 'jadwaldokter_id');
                $isEsiantri = true;
                /**
                 *  Check antrian online atau bukan.
                 **/
                $dataAntrian = Antrian::find()->select([
                    'is_online',
                    'temp_urutan'
                ])->where(['antrian_id' => $antrianId])->one();

                /**
                 * Generate nomer antrian apabila dari reservasi.
                 */
                self::createAntrianPoli($antrianId, $dataAntrian, $dokter_id);

                $lastKuota = KuotaDokterR::find()
                    ->select([
                        'kuotadokter_id',
                        'kuota_keluar',
                        'kuota_tersedia'
                    ])
                    ->where([
                        'jadwaldokter_id' => $jadwaldokter,
                    ]);

                if ($dataAntrian->is_online == true) {
                    $lastKuota->andWhere(['is_online' => true]);        
                    $pendaftaranOnline = PendaftaranOnline::find()
                        ->select([
                            'jenis_reservasi'
                        ])
                        ->where(['antrian_id' => $antrianId])
                        ->one();

                    if(! empty($pendaftaranOnline) && $pendaftaranOnline['jenis_reservasi'] == DocoConstants::JENIS_RSV_JKN) {
                        $isEsiantri = false;
                    }
                } else {
                    $lastKuota->andWhere(['is_online' => false]);
                }

                $lastKuota = $lastKuota->andWhere(['<>', 'kuota_tersedia', 0])
                    ->asArray()
                    ->one();

                if (empty($lastKuota)) {
                    return [
                        'status' => 422,
                        'data' => 'Kuota dokter habis !'
                    ];
                }

                if($isEsiantri) {
                    $kuotaDokterId = ArrayHelper::getValue($lastKuota, 'kuotadokter_id');
                    $kuotaKeluar = ArrayHelper::getValue($lastKuota, 'kuota_keluar');
                    $kuotaTersedia = ArrayHelper::getValue($lastKuota, 'kuota_tersedia');
                    $newKuotaKeluar = (int) $kuotaKeluar + 1;
                    $newKuotaTersedia = (int) $kuotaTersedia - 1;
    
                    $updateAntrian = Antrian::updateAll(['jadwaldokter_id' => $jadwaldokter], ['antrian_id' => $antrianId]);
                    $updateAntrianAsal = Antrian::updateAll(['jadwaldokter_id' => $jadwaldokter], ['antrianasal_id' => $antrianId]);
    
                    $updateKuotaDokter = KuotaDokterR::find()->where([
                        'kuotadokter_id' => $kuotaDokterId
                    ]);
    
                    if ($dataAntrian->is_online == true) {
                        $updateKuotaDokter->andWhere(['is_online' => true]);
                    } else {
                        $updateKuotaDokter->andWhere(['is_online' => false]);
                    }
    
                    $updateKuotaDokter = $updateKuotaDokter->one();
                    $updateKuotaDokter->kuota_keluar = $newKuotaKeluar;
                    $updateKuotaDokter->kuota_tersedia = $newKuotaTersedia;
                    $updateKuotaDokter->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            throw new Exception(json_encode([
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]));
        }
    }

    /**
     * Function ini berfungsi melakukan pengurangan kuota dokter dari reservasi.
     * jangan lupa pastikan konfignya harus false.
     *
     * @author <Maulana Muhammad Rizky>
     * @param int $antrianId
     */
    public static function kurangiKuotaDokterFromReservasi($antrianId)
    {
        $dataAntrian = Antrian::find()
            ->select(['jadwalbukapoli_id'])
            ->where([
                'antrian_id' => $antrianId
            ])
            ->asArray()
            ->one();

        if (!empty($dataAntrian)) {
              self::revertKuotaPendaftaran($dataAntrian['jadwalbukapoli_id']);
        }
    }

    /**
     * Function ini berfungsi melakukan pengurangan kuota dokter dari reservasi.
     * jangan lupa pastikan konfignya harus true.
     *
     * @author <Maulana Muhammad Rizky>
     * @param int $pendaftaran_id
     */
    public static function validateBatalKunjungan($pendaftaran_id)
    {
        $konfig = self::isKonfig();
        if ($konfig == true) {
            $antrian = Antrian::find()
                ->select([
                    'jadwalbukapoli_id',
                    'jadwaldokter_id',
                    'is_online',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'jenisantrian_id' => DocoConstants::VAR_JA_P
                ])->one();

            if (!empty($antrian)) {
                $revertKuotaPendaftaran = self::revertKuotaPendaftaran($antrian->jadwalbukapoli_id, $antrian->is_online);

                if (!empty($antrian->jadwaldokter_id)) {
                    $revertKuotaPoli = self::revertKuotaPoli($antrian->jadwaldokter_id, $antrian->is_online);
                }
            }
        }
        return true;
    }

    /**
     * Function untuk validasi pengurangan kuota dokter dan kuota antrian dari batal periksa.
     *
     * @author Maulana Muhammad Rizky
     * @param int $pendaftaran_id.
     * @return bool
     */
    public static function validateBatalPeriksa($pendaftaran_id)
    {
        $konfig = self::isKonfig();
        if ($konfig == true) {
            $antrian = Antrian::find()
                ->select([
                    'jadwalbukapoli_id',
                    'jadwaldokter_id',
                    'is_online'
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'jenisantrian_id' => DocoConstants::VAR_JA_P
                ])->one();

            if (!empty($antrian)) {
                $revertKuotaPendaftaran = self::revertKuotaPendaftaran($antrian->jadwalbukapoli_id, $antrian->is_online);

                if (!empty($antrian->jadwaldokter_id)) {
                    $revertKuotaPoli = self::revertKuotaPoli($antrian->jadwaldokter_id, $antrian->is_online);
                }
            }
        }
        return true;
    }

    /**
     * Function check kuota dokter terakhir.
     *
     * @author <Maulana Muhammad Rizky>
     * @param int $dokter_id
     * @return bool
     */
    private static function checkKuotaDokter($dokter_id)
    {
        $hariIni = DocoHelpers::getIdHariIni();

        return Yii::$app->db->createCommand("
            SELECT jadwaldokter_id from jadwaldokter_m
            INNER JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
            WHERE pegawai_id = {$dokter_id}
            AND jadwalbukapoli_m.is_deleted = FALSE
            AND jadwalbukapoli_m.is_active = true
            AND jadwalbukapoli_m.hari = {$hariIni}
        ")->queryOne();
    }

    /**
     * Generate nomer antrian poli dan lakukan save data.
     *
     * @author Maulana Muhammad Rizky
     */
    public static function createAntrianPoli($antrianId, $dataAntrian = [], $dokter_id = null)
    {
        $connection = Yii::$app->db;
        $modelPoli = Antrian::find()
        ->where([
            'antrianasal_id' => $antrianId,
            'jenisantrian_id' => DocoConstants::VAR_JA_P
        ])->one();

        if(! empty($modelPoli)) {
            if($modelPoli->no_antrian == '---' || empty($modelPoli->no_antrian)) {
                $konfigAntrianM = KonfigAntrian::find()
                ->select([
                    'kode_antrian',
                    'ruangan_id',
                    'konfigantrian_id'
                ])
                ->where([
                    'konfigantrian_id' => $modelPoli->konfigantrian_id
                ])
                ->asArray()->one();

                if($konfigAntrianM['ruangan_id'] != $modelPoli->ruangan_id){
                    $konfigAntrianM = KonfigAntrian::find()
                    ->select([
                        'kode_antrian',
                        'ruangan_id',
                        'konfigantrian_id'
                    ])
                    ->where([
                        'jenisantrian_id' => DocoConstants::VAR_JA_P,
                        'ruangan_id' => $modelPoli->ruangan_id
                    ])
                    ->asArray()->one();
                }

                $date = date('Y-m-d');
                $antrianpoli = DocoConstants::VAR_JA_P;

                $generateNoAntrian = $connection->createCommand("
                    SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) AS no_antrian
                    FROM antrian_t
                    WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = '{$konfigAntrianM['kode_antrian']}'
                    AND tgl_antrian::DATE = '{$date}'
                    AND jenisantrian_id = '{$antrianpoli}';
                ")->queryOne();

                $modelPoli->no_antrian = $konfigAntrianM['kode_antrian'].$generateNoAntrian['no_antrian'];
                $modelPoli->temp_urutan = ArrayHelper::getValue($dataAntrian, 'temp_urutan');
                $modelPoli->pegawai_id = $dokter_id;
                $modelPoli->konfigantrian_id = $konfigAntrianM['konfigantrian_id'];
                $modelPoli->save();
            }
        }
    }
}
