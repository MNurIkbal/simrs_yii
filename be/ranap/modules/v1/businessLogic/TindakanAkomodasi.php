<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\businessLogic;

use Yii;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\InfoTarifRsKamar;
use app\modules\v1\models\TindakanPelayanan;

use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\DaftarTindakan; 
use app\modules\v1\models\LookupTransaksi;
use app\components\object\AkomodasiAttributes;

class TindakanAkomodasi
{

    const PERCENTACE = 0.5;
    const MAX_HOURS = 6;

    /**
     * Untuk perhitungan tarif akomodasi kamar
     * @param  string $type      #ref DocoConstants
     * @param  integer $admisiId  pasienAdmisi
     * @param  timestamp $regisDate tanggal stop akomodasi atau mutasi
     * @return boolean
     */

    public static function execute($type, $admisiId, $endDate, $view = false, $stopAkomodasi = false, $newKelaspelayananId = null, $is_cron = false, $is_pindahkamar = false)
    {
        $qPasien = Pendaftaran::find(true)
            ->select([
                'pendaftaran_t.pendaftaran_id',
                'pendaftaran_t.pasienadmisi_id',
                'pendaftaran_t.is_stopakomodasi',
                'pendaftaran_t.tgl_stopakomodasi',
                'pendaftaran_t.jeniskasuspenyakit_id',
                'pendaftaran_t.no_pendaftaran',
                'pasienadmisi_t.penjamin_id',
                'pasienadmisi_t.carabayar_id',
                'pasienadmisi_t.is_pasientitipan',
                'pasienadmisi_t.is_aps',
                'pasienadmisi_t.bpjs_id',
                'pasienadmisi_t.pegawai_id',
                'pasienadmisi_t.pasien_id',
                'carabayar_m.groupcarabayar_id',
            ])
            ->joinWith([
                'admisi' => function ($query) {
                    $query->select([
                        'pasienadmisi_t.pasienadmisi_id',
                        'pasienadmisi_t.carabayar_id',
                        'pasienadmisi_t.is_pasientitipan',
                        'pasienadmisi_t.ruangan_titipan_id',
                        'pasienadmisi_t.kamar_titipan_id',
                        'pasienadmisi_t.kelas_ditagihkan_id',
                        'pasienadmisi_t.is_stoptitipan',
                    ])->joinWith([
                        'carabayar' => function ($query) {
                            $query->select([
                                'carabayar_m.carabayar_id',
                            ]);
                        }
                    ]);
                }
            ])
            ->andWhere(['pendaftaran_t.pasienadmisi_id' => $admisiId])
            ->asArray()->one();

        if (empty($qPasien)) {
            throw new \Exception("Pendaftaran tidak ditemukan ".$admisiId);
        }

        $qMasukKamar = MasukKamar::find()->select([
            'masukkamar_t.masukkamar_id',
            'masukkamar_t.pindahkamar_id',
            'masukkamar_t.kelaspelayanan_id',
            'COALESCE(CASE WHEN pindahkamar_t.is_stoptitipan THEN masukkamar_t.tgl_last_akomodasi ELSE NULL END, masukkamar_t.tgl_masukkamar) as tgl_masukkamar',
            'COALESCE(CASE WHEN pindahkamar_t.is_stoptitipan THEN masukkamar_t.tgl_last_akomodasi::TIME ELSE NULL END, masukkamar_t.jam_masukkamar) as jam_masukkamar',
            'COALESCE(CASE WHEN pindahkamar_t.is_stoptitipan THEN NULL ELSE pindahkamar_t.ruangan_titipan_id END, masukkamar_t.ruangan_id) as ruangan_id',
            'COALESCE(CASE WHEN pindahkamar_t.is_stoptitipan THEN NULL ELSE pindahkamar_t.kamar_titipan_id END, masukkamar_t.kamarruangan_id) as kamarruangan_id',
            'COALESCE(CASE WHEN pindahkamar_t.is_stoptitipan THEN NULL ELSE pindahkamar_t.tempattidur_titipan_id END, masukkamar_t.kamartempattidur_id) as kamartempattidur_id',
            'masukkamar_t.kamartempattidur_id',
            'masukkamar_t.carabayar_id',
            'masukkamar_t.tgl_keluarkamar',
            'masukkamar_t.jam_keluarkamar',
            'masukkamar_t.tgl_last_akomodasi',
        ])
            ->leftJoin("pindahkamar_t", "masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id")
            ->andWhere(['masukkamar_t.pasienadmisi_id' => $admisiId]);

        // if ($type === DocoConstants::AKOMODASI_MUTASI) {
        //     $qMasukKamar->andWhere(['NOT', ['pindahkamar_id' => null]]);
        // } 

        $qMasukKamarList = $qMasukKamar->orderBy(['masukkamar_t.masukkamar_id' => SORT_DESC])->asArray()->all();
        if (empty($qMasukKamarList)) {
            throw new \Exception("Tidak ada riwayat masuk kamar");
        }

        $qMasukKamarList[0]['tgl_keluarkamar'] = date('Y-m-d', strtotime($endDate));
        $qMasukKamarList[0]['jam_keluarkamar'] = date('H:i:s', strtotime($endDate));

        $tempQMasukKamar = [$qMasukKamarList[0]];
        $tempKelasPelayanan = $qMasukKamarList[0]['kelaspelayanan_id'];

        for ($i=1; $i < sizeof($qMasukKamarList); $i++) { 
            if($qMasukKamarList[$i]['kelaspelayanan_id'] == $tempKelasPelayanan) {
                $tempQMasukKamar[] = $qMasukKamarList[$i];
            } else {
                break;
            }
        }

        $qMasukKamarList = array_reverse($tempQMasukKamar);
        $qMasukKamarList = self::intersectJamMasuk($qMasukKamarList);
        $dataInsertAkomodasiAll = [];

        $paramsType = [
            'is_cron' => $is_cron,
            'type' => $type,
            'is_pindahkamar' => $is_pindahkamar,
        ];
        
        $masukKamarId = null;
        foreach ($qMasukKamarList as $qMasukKamar) {
            $endDate = $qMasukKamar['tgl_keluarkamar'] . ' ' . $qMasukKamar['jam_keluarkamar'];
            $populateData = self::populateData($qMasukKamar, $qPasien, $endDate, $type);
            $penjamin = $populateData['penjamin'];
            $kamarRuanganId = $populateData['kamarruanganId'];
            $dataPasien = [
                'jeniskasuspenyakit_id' => $populateData['jenisPenyakit'],
                'dokter_id' => $populateData['pegawaiId'],
                'pasien_id' => $populateData['pasienId'],
                'pendaftaran_id' => $populateData['pendaftaranId'],
                'pasienadmisi_id' => $admisiId,
                'penjamin_id' => $populateData['penjamin'],
                'ruangan_id' => $populateData['ruanganId'],
                'instalasi_id' => $populateData['instalasi_id'],
                'carabayar_id' => $populateData['carabayar_id'],
                'is_stopakomodasi' => $stopAkomodasi
            ];

            if (!empty($qMasukKamar) && ($type === DocoConstants::AKOMODASI_MUTASI || $type === DocoConstants::STOP_TITIPAN)) {
                $stopAkomodasi = false;
            }

            // if ($type === DocoConstants::AKOMODASI_MUTASI) {
            //     $cekNaikKelas = self::getIsNaikKelas($admisiId, $populateData);
            //     $stopAkomodasi = false;
            //     if($cekNaikKelas['isNaikKelas']) {
            //         $dataTindakan = self::getTindakanExisting($admisiId);

            //         $kelasBaru = $cekNaikKelas['kelasBaru'];
            //         $arrPelayananId = [];

            //         if(!empty($dataTindakan)) {
            //             $tindakanId = [];
            //             foreach ($dataTindakan as $key => $value) {
            //                 $tindakanId[] = $value['daftartindakan_id'];
            //                 $arrPelayananId[$value['daftartindakan_id']] = $value['pelayanan_id'];
            //             }

            //             $paramsTindakan = [
            //                 'penjamin_id' => $penjamin,
            //                 'kelaspelayanan_id' => $kelasBaru,
            //                 'tindakanId' => $tindakanId,
            //                 'arrPelayananId' => $arrPelayananId,
            //                 'dataTindakan' => $dataTindakan,
            //             ];

            //             $dataTindakanBaru = self::getTarifNaikKelas($paramsTindakan);
            //             $komponentTarif = self::getKomponent($paramsTindakan);

            //             $pelayanan_id = !empty($dataTindakanBaru['pelayanan_id']) ? $dataTindakanBaru['pelayanan_id'] : [];

            //             if(!empty($pelayanan_id)) {
            //                 $inCondition = "(" . implode(",", $pelayanan_id) . ")";
            //                 Yii::$app->db->createCommand("DELETE FROM tindakanpelayanan_t WHERE tindakanpelayanan_id IN {$inCondition}")->execute();

            //                 $params = [
            //                     'tindakanBaru' => $dataTindakanBaru,
            //                     'komponent' => $komponentTarif,
            //                     'type' => 'pelayanan',
            //                 ];

            //                 if(!empty($dataTindakanBaru)) {
            //                     $insertData = self::generateTindakanNaikKelas($params);
            //                     TindakanPelayanan::batchInsert($insertData);
            //                 }
            //             }
            //         }
            //     }
            // } 

            $dataInsertAkomodasi = self::generateAkomodasiKamar($populateData, $dataPasien, $stopAkomodasi, $endDate, $paramsType);

            $pendaftaranId = $populateData['pendaftaranId'];
            $totalHari = $populateData['totalHari'];
            $startDate = $populateData['startDate'];
            $jamMasukKamar = $populateData['jamMasukKamar'];

            if (!empty($dataInsertAkomodasi)) {
                $tagihanAkomodasi = array();
                for ($x = 0; $x < count($dataInsertAkomodasi); $x++) {
                    $tanggal = null;
                    if ($x == count($dataInsertAkomodasi) && count($dataInsertAkomodasi) != 1) {
                        $validStartDate = date('Y-m-d', strtotime($startDate));
                        $validEndDate = date('Y-m-d', strtotime($endDate));
                        if ($validStartDate !== $validEndDate) $endDate = $startDate;
                        $tanggal = $endDate;
                    } else {
                        if($is_cron || $type == DocoConstants::STOP_AKOMODASI || $type == DocoConstants::STOP_TITIPAN || $is_pindahkamar){
                            $data_konfig = self::getKonfigStopAkomodasi();
                            $cutOff = $data_konfig[0];
                            //strtotime($cutOff.'-'.$data_konfig[1].'hours')
                            if(strtotime($cutOff) < strtotime($populateData['jamMasukKamar'])){
                                $tanggal = date('Y-m-d', strtotime($startDate . ' +1 day')). ' '.$cutOff;
                            }else{
                                $tanggal = date('Y-m-d', strtotime($startDate)). ' '.$cutOff;
                            }

                            if(($type == DocoConstants::STOP_AKOMODASI || $type == DocoConstants::STOP_TITIPAN || $is_pindahkamar)  && (count($dataInsertAkomodasi) - $x) == 1){
                                $tanggal = $endDate;
                            }
                        }else {
                            $tanggal = $startDate;
                        }
                    }

                    $date = strtotime("+1 day", strtotime($startDate));

                    if ($type == DocoConstants::STOP_TITIPAN) {
                        $startDate = date('Y-m-d', $date) . ' ' . date('H:i:s', strtotime($startDate));
                    } else {
                        $startDate = date('Y-m-d', $date) . ' ' . $jamMasukKamar;
                    }

                    $tagihanAkomodasi[] = $tanggal;
                }

                foreach ($dataInsertAkomodasi as $key => $value) {
                    if (array_key_exists($key, $tagihanAkomodasi)) {
                        $dataInsertAkomodasi[$key]['tgl_tindakan'] = $tagihanAkomodasi[$key];
                    }
                    $dataInsertAkomodasi[$key]['kamartempattidur_id'] = $populateData['kamartempattidurId'];
                }

                if ($view) {
                    // if (isset($qPasien['is_stopakomodasi']) && !empty($qPasien['tgl_stopakomodasi']) && !$qPasien['is_stopakomodasi']) {
                    //     $checkLastAkomodasi = TindakanPelayanan::find()->where(['pendaftaran_id' => $pendaftaranId, 'daftartindakan_id' => $dataInsertAkomodasi[0]['daftartindakan_id']])->orderBy(['tgl_tindakan' => SORT_DESC])->one();
                    //     if(!empty($checkLastAkomodasi)) {
                    //         $dataInsertAkomodasi[0]['tarif_tindakan'] = $dataInsertAkomodasi[0]['tarif_tindakan'] - $checkLastAkomodasi->tarif_tindakan;
                    //     }

                    //     if($dataInsertAkomodasi[0]['tarif_tindakan'] > 0) {
                    //         $dataInsertAkomodasi[0]['qty_tindakan'] = $dataInsertAkomodasi[0]['tarif_tindakan'] / $dataInsertAkomodasi[0]['tarif_satuan'];
                    //     } else {
                    //         unset($dataInsertAkomodasi[0]);
                    //         $dataInsertAkomodasi = array_values($dataInsertAkomodasi);
                    //     }
                    // } else {
                    // }
                    $dataInsertAkomodasi = self::checkIsExist($dataInsertAkomodasi, $admisiId,$populateData['startDate'],$populateData['is_reset']);
                } else {
                    // if (isset($qPasien['is_stopakomodasi']) && !empty($qPasien['tgl_stopakomodasi']) && !$qPasien['is_stopakomodasi']) {
                    //     $lastAkomodasi = TindakanPelayanan::find()->where(['pendaftaran_id' => $pendaftaranId, 'daftartindakan_id' => $dataInsertAkomodasi[0]['daftartindakan_id']])->orderBy(['tgl_tindakan' => SORT_DESC])->one();
                    //     if(!empty($lastAkomodasi)) {
                    //         $lastAkomodasi->is_deleted = true;
                    //         $lastAkomodasi->update();
                    //     }
                    // } else {
                    //     $dataInsertAkomodasi = self::checkIsExist($dataInsertAkomodasi, $admisiId);
                    // }
                    $dataInsertAkomodasi = self::checkIsExist($dataInsertAkomodasi, $admisiId,$populateData['startDate'],$populateData['is_reset']);

                    TindakanPelayanan::batchInsert($dataInsertAkomodasi);
                    
                    $statusLunas = DocoConstants::BELUM_LUNAS;
                    Yii::$app->db->createCommand("
                        UPDATE pendaftaran_t SET status_bayar = {$statusLunas} 
                        WHERE pendaftaran_id = {$pendaftaranId}
                    ")->execute();

                }
                $masukKamarId = $qMasukKamar['masukkamar_id'];
            }
            $dataInsertAkomodasiAll = array_merge($dataInsertAkomodasiAll, $dataInsertAkomodasi);
        }

        if ($type == DocoConstants::STOP_TITIPAN && !empty($masukKamarId)) {
            $masukKamar = MasukKamar::findOne($masukKamarId);
            $masukKamar->tgl_last_akomodasi = date('Y-m-d H:i:s', strtotime('NOW'));
            $masukKamar->save(false);
        }

        if ($view) {
            $totalAkomodasi = 0;
            if (!empty($dataInsertAkomodasiAll)) {
                foreach ($dataInsertAkomodasiAll as $key => $value) {
                    $totalAkomodasi += $value['tarif_tindakan'];
                }
            }

            return [
                'tindakan_akomodasi' => $dataInsertAkomodasiAll,
                'total_akomodasi' => $totalAkomodasi
            ];
        } else {
            return true;
        }
    }

    private static function generateTindakan($dataArray, $dataPasien/*, $konfigPercen*/)
    {
        if (!empty($dataArray)) {
            $komponenTotal = (new DocoConstansId)->actionGetId('komponen_total');
            $tmp = $komponen = [];
            foreach ($dataArray as $value) {

                $tarifSatuan = $value['harga_tariftindakan'] * $konfigPercen;
                $tindakanId = $value['daftartindakan_id'];
                $komponenId = $value['komponentarif_id'];
                /** Parent Tindakan */
                if ($komponenId == $komponenTotal) {
                    $tmp[$tindakanId] = [
                        'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'pasien_id' => !empty($dataPasien['pasien_id']) ? $dataPasien['pasien_id'] : null,
                        'daftartindakan_id' => $tindakanId,
                        'tipepaket_id' => null,
                        'carabayar_id' => $value['carabayar_id'],
                        'pendaftaran_id' => !empty($dataPasien['pendaftaran_id']) ? $dataPasien['pendaftaran_id'] : null,
                        'pasienadmisi_id' => !empty($dataPasien['pasienadmisi_id']) ? $dataPasien['pasienadmisi_id'] : null,
                        'jeniskasuspenyakit_id' => !empty($dataPasien['jeniskasuspenyakit_id']) ? $dataPasien['jeniskasuspenyakit_id'] : null,
                        'instalasi_id' => $value['instalasi_id'],
                        'kamarruangan_id' => $value['kamarruangan_id'],
                        'ruangan_id' => $value['ruangan_id'],
                        'penjamin_id' => $value['penjamin_id'],
                        'tgl_tindakan' => date('Y-m-d H:i:s'),
                        'dokterpenanggungjawab_id' => !empty($dataPasien['dokter_id']) ? $dataPasien['dokter_id'] : null,
                        'tarif_satuan' => (float) $tarifSatuan,
                        'qty_tindakan' => 1,
                        'tarif_tindakan' => (float) $tarifSatuan,
                        'tarifcyto_tindakan' => 0,
                        'cyto_tindakan' => false,
                        'discount_tindakan' => 0,
                        'additional_data' => [
                            'list_komponen' => []
                        ]
                    ];
                } else {
                    /** Generate Komponen Tindakan */
                    $komponen[$tindakanId][] = [
                        'komponentarif_id' => $komponenId,
                        'tindakanpelayanan_id' => null,
                        'tarif_kompsatuan' => (float) $tarifSatuan,
                        'tarif_tindakankomp' => (float) $tarifSatuan,
                        'tarifcyto_tindakankomp' => 0,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                    ];
                }
            }
            $listTagihan = [];
            foreach ($tmp as $key => $value) {
                $tmp[$key]['additional_data']['list_komponen'] = isset($komponen[$key]) ? $komponen[$key] : null;
                $tmp[$key]['additional_data'] = json_encode($tmp[$key]['additional_data']);
                $listTagihan = $tmp[$key];
                break;
            }
            return $listTagihan;
        }
        return [];
    }

    private static function getUrutanKelas($kelaspelayanan_id)
    {
        $urutan = null;
        $data = KelasPelayanan::findOne($kelaspelayanan_id);
        if ($data) {
            $urutan = $data['urutankelas'];
        }

        return $urutan;
    }

    private static function getTarif($ruangan, $penjamin, $kelas, $type, $tindakan = null)
    {
        $where = '';
        if ($tindakan) {
            $where = "WHERE daftartindakan_id = $tindakan ";
        }
        $data = Yii::$app->db->createCommand('SELECT daftartindakan_id,
                daftartindakan_nama, harga_tariftindakan, is_akomodasi,
                persencyto_tindakan, tariftindakan_id, komponentarif_id, kelaspelayanan_id,
                carabayar_id, instalasi_id, kamarruangan_id, ruangan_id, penjamin_id
                FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id,:type) 
                ' . $where . '
                ORDER BY daftartindakan_nama ASC')
            ->bindParam(':ruangan_id', $ruangan)
            ->bindParam(':penjamin_id', $penjamin)
            ->bindParam(':kelaspelayanan_id', $kelas)
            ->bindParam(':type', $type);

        $result = $data->queryAll();

        if ($tindakan) {
            $result = $data->queryOne();
        }

        return $result;
    }

    private static function populateData($qMasukKamar, $qPasien, $endDate, $type = null)
    {
        $pindahKamar = array();
        $penjamin = !empty($qPasien['penjamin_id']) ? $qPasien['penjamin_id'] : null;
        $ruanganId = !empty($qMasukKamar['ruangan_id']) ? $qMasukKamar['ruangan_id'] : null;
        $hakKelas = !empty($qMasukKamar['kelaspelayanan_id']) ? $qMasukKamar['kelaspelayanan_id'] : null;
        $pasienId = !empty($qPasien['pasien_id']) ? $qPasien['pasien_id'] : null;
        $pendaftaranId = !empty($qPasien['pendaftaran_id']) ? $qPasien['pendaftaran_id'] : null;
        $jenisPenyakit = !empty($qPasien['jeniskasuspenyakit_id']) ? $qPasien['jeniskasuspenyakit_id'] : null;
        $pegawaiId = !empty($qPasien['pegawai_id']) ? $qPasien['pegawai_id'] : null;
        $kamarruanganId = !empty($qMasukKamar['kamarruangan_id']) ? $qMasukKamar['kamarruangan_id'] : null;
        $kamartempattidurId = !empty($qMasukKamar['kamartempattidur_id']) ? $qMasukKamar['kamartempattidur_id'] : null;
        $carabayar_id = !empty($qMasukKamar['carabayar_id']) ? $qMasukKamar['carabayar_id'] : null;
        $grCarBayar = !empty($qPasien['groupcarabayar_id']) ? $qPasien['groupcarabayar_id'] : null;
        $jamMasukKamar = !empty($qMasukKamar['jam_masukkamar']) ? date('H:i:s', strtotime($qMasukKamar['jam_masukkamar'])) : '00:00:00';
        $tanggalMasuk = !empty($qMasukKamar['tgl_masukkamar']) ? date('Y-m-d', strtotime($qMasukKamar['tgl_masukkamar'])) : null;
        $startDate = !empty($qMasukKamar['tgl_masukkamar']) && !empty($qMasukKamar['jam_masukkamar'])
            ? $tanggalMasuk . ' ' . $jamMasukKamar : null;
        $tanggalKeluar = !empty($qMasukKamar['tgl_keluarkamar']) ? date('Y-m-d H:i:s', strtotime($qMasukKamar['tgl_keluarkamar'])) : null;
        $hasBatalStopAkomodasi = isset($qPasien['is_stopakomodasi']) && !empty($qPasien['tgl_stopakomodasi']) && !$qPasien['is_stopakomodasi'] ? true : false;

        $totalJam = 0;
        $totalHari = 0;
        $is_reset = false;
        if ($qMasukKamar['tgl_last_akomodasi']) {
            $startDate = $qMasukKamar['tgl_last_akomodasi'];
            $explode_last_akomodasi = explode(" ", $qMasukKamar['tgl_last_akomodasi']);
            if (count($explode_last_akomodasi) == 2) {
                $date_last_akomodasi = date('Y-m-d 00:00:00', strtotime($explode_last_akomodasi[0]));
                $jam_last_akomodasi = date('H:i:s', strtotime($explode_last_akomodasi[1]));
            }
            $jamMasukKamar = $jam_last_akomodasi;
            $is_reset = true;
        }

        if ($hasBatalStopAkomodasi) {
            $startDate = date('Y-m-d', strtotime($qPasien['tgl_stopakomodasi'])) . ' ' . $jamMasukKamar;
        }

        if ($startDate) {
            $dateDiff = DocoHelpers::getDiffDateTime($startDate, $endDate);
            $totalJam = $dateDiff['jam'];
            $totalHari = $totalJam <= 0 ? 0 : ceil($totalJam / 24);
            $totalHari = $totalHari > 1 ? $totalHari : $totalHari;
        }

        $konfigPercen = 1;
        if ($totalJam <= self::MAX_HOURS && $totalJam != 0) {
            $totalHari = 1;
            $konfigPercen = self::PERCENTACE;
        }

        if ($qPasien['admisi']['is_stoptitipan'] == false) {
            if ($qPasien['admisi']['is_pasientitipan']) {
                $hakKelas = $qPasien['admisi']['kelas_ditagihkan_id'];
                $kamarruanganId = $qPasien['admisi']['kamar_titipan_id'];
                $ruanganId = $qPasien['admisi']['ruangan_titipan_id'];
            }
        }

        $pindahKamar = PindahKamar::find()
            ->andWhere([
                'pasienadmisi_id' => $qPasien['pasienadmisi_id'],
                'pindahkamar_id' => $qMasukKamar['pindahkamar_id']
            ])
            ->orderBy(['pindahkamar_id' => SORT_DESC])
            ->one();

        if (!empty($pindahKamar)) {
            if ($pindahKamar->is_stoptitipan == false) {
                if ($pindahKamar->is_pasientitipan) {
                    $hakKelas = $pindahKamar->kelas_ditagihkan_id;
                    $kamarruanganId = $pindahKamar->kamar_titipan_id;
                    $ruanganId = $pindahKamar->ruangan_titipan_id;
                } else {
                    // $hakKelas = $pindahKamar->kelaspelayanan_id;
                    // $kamarruanganId = $pindahKamar->kamarruangan_id;
                    // $ruanganId = $pindahKamar->ruangan_id;
                }
            } else {
                $hakKelas = $pindahKamar->kelaspelayanan_id;
                $kamarruanganId = $pindahKamar->kamarruangan_id;
                $ruanganId = $pindahKamar->ruangan_id;
            }
        }

        // if ($grCarBayar === DocoConstants::GROUP_BPJS) {
        //     $isTitipan = !empty($qPasien['is_pasientitipan']) ? $qPasien['is_pasientitipan'] : false;
        //     $isBpjs = !empty($qPasien['bpjs_id']) ? $qPasien['bpjs_id'] : false;
        //     if ($isBpjs) {
        //         $qBpjs = Bpjs::find()->select([
        //             'klsrawat'
        //         ])
        //         ->andWhere(['bpjs_id' => $isBpjs])
        //         ->asArray()->one();
        //         /** Kondisi pasien titipan yang di pake hak rawat dari bpjs */
        //         if ($isTitipan) {
        //             $hakKelas = !empty($qBpjs['klsrawat']) ? $qBpjs['klsrawat'] : null;
        //         }
        //     }
        // }

        return [
            'penjamin' => $penjamin,
            'ruanganId' => $ruanganId,
            'hakKelas' => $hakKelas,
            'pasienId' => $pasienId,
            'pendaftaranId' => $pendaftaranId,
            'jenisPenyakit' => $jenisPenyakit,
            'pegawaiId' => $pegawaiId,
            'kamarruanganId' => $kamarruanganId,
            'kamartempattidurId' => $kamartempattidurId,
            'grCarBayar' => $grCarBayar,
            'konfigPercen' => $konfigPercen,
            'totalHari' => $totalHari,
            'startDate' => $startDate,
            'jamMasukKamar' => $jamMasukKamar,
            'carabayar_id' => $carabayar_id,
            'instalasi_id' => DocoConstants::INST_ID_RI,
            'tanggalKeluar' => $tanggalKeluar,
            'is_reset' => $is_reset,
        ];
    }

    private static function getTarifNaikKelas($params)
    {
        $dataTindakanBaru = $arrPelayananIdNew = [];
        $tindakanId = isset($params['tindakanId']) ? $params['tindakanId'] : [];
        $penjamin_id = isset($params['penjamin_id']) ? $params['penjamin_id'] : [];
        $kelasPelayananId = isset($params['kelaspelayanan_id']) ? $params['kelaspelayanan_id'] : [];
        $arrPelayananId = isset($params['arrPelayananId']) ? $params['arrPelayananId'] : [];
        $tindakanExisting = isset($params['dataTindakan']) ? $params['dataTindakan'] : [];
        $type = isset($params['type']) ? $params['type'] : 'pelayanan';
        $dataPasien = isset($params['dataPasien']) ? $params['dataPasien'] : [];
        $kamarRuanganId = isset($params['kamarruanganId']) ? $params['kamarruanganId'] : [];
        $tgl_pelayanan = isset($params['tgl_pelayanan']) ? $params['tgl_pelayanan'] : [];
        $ruangan_id = isset($params['ruangan_id']) ? $params['ruangan_id'] : [];
        $where = '';

        if ($type == 'kamar') {
            if ($kamarRuanganId) {
                $where = "WHERE kamarruangan_id = {$kamarRuanganId} ";
            }
        } else {
            if (!empty($tindakanId)) {
                $inCondition = "(" . implode(",", $tindakanId) . ")";
                $where = "WHERE daftartindakan_id IN {$inCondition} ";
            }
        }

        $data = Yii::$app->db->createCommand('SELECT daftartindakan_id,
                daftartindakan_nama, harga_tariftindakan, is_akomodasi,
                persencyto_tindakan, tariftindakan_id, komponentarif_id, kelaspelayanan_id,
                carabayar_id, instalasi_id, kamarruangan_id, ruangan_id, penjamin_id
                FROM tariftotalkamarrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id) 
                ' . $where . '
                ORDER BY daftartindakan_nama ASC')
            ->bindParam(':ruangan_id', $ruangan_id)
            ->bindParam(':penjamin_id', $penjamin_id)
            ->bindParam(':kelaspelayanan_id', $kelasPelayananId);

        $result = $data->queryAll();

        if (empty($result)) {
            if ($type == 'kamar') {
                $param = [
                    'kamarruanganId' => $kamarRuanganId,
                    'kelaspelayanan_id' => $kelasPelayananId,
                    'carabayar_id' => $dataPasien['carabayar_id'],
                    'instalasi_id' => $dataPasien['instalasi_id'],
                    'penjamin_id' => $penjamin_id,
                    'ruangan_id' => $dataPasien['ruangan_id'],
                ];
                $result[] = self::defaultTindakan($param);
            }
        }

        foreach ($result as $key => $value) {
            $daftartindakan_id = $value['daftartindakan_id'];
            $harga_tariftindakan = $value['harga_tariftindakan'];
            $params = [
                'data' => $value,
                'pelayanan_id' => $arrPelayananId,
                'tindakan_existing' => $tindakanExisting,
                'daftartindakan_id' => $daftartindakan_id,
                'dataPasien' => $dataPasien,
            ];

            if ($type == 'kamar') {
                $items = self::generateItemAkomodasi($params);
            } else {
                if (!empty($arrPelayananId)) {
                    if (isset($arrPelayananId[$daftartindakan_id])) {
                        $value['pelayanan_id'] = $arrPelayananId[$daftartindakan_id];
                        $arrPelayananIdNew[] = $arrPelayananId[$daftartindakan_id];
                    }
                }

                $items = self::generateItem($params);
            }

            $value['harga_tariftindakan'] = $harga_tariftindakan;
            $value['pasien_id'] = $items['pasienId'];
            $value['pendaftaran_id'] = $items['pendaftaranId'];
            $value['pasienadmisi_id'] = $items['pasienAdmisiId'];
            $value['jeniskasuspenyakit_id'] = $items['jenisKasusPenyakitId'];
            $value['dokterpenanggungjawab_id'] = $items['dokterPjId'];
            $value['qty'] = $items['qty'];
            $value['tarif_cyto'] = $items['tarif_cyto'];
            $value['is_cyto'] = $items['is_cyto'];
            $value['discount'] = $items['discount'];
            $value['tgl_pelayanan'] = !empty($tgl_pelayanan) ? $tgl_pelayanan : $items['tgl_pelayanan'];
            $value['instalasi_id'] = $items['instalasiId'];
            $value['ruangan_id'] = $items['ruanganId'];
            $value['carabayar_id'] = $items['caraBayarId'];
            $value['penjamin_id'] = $items['penjaminId'];
            $value['persencyto_tindakan'] = $items['persenCyto'];
            $value['tipepaket_id'] = $items['tipePaketId'];

            if ($type != 'kamar') {
                $dataTindakanBaru[] = $value;
            } else {
                $dataTindakanBaru = $value;
            }
        }

        return [
            'tindakan' => $dataTindakanBaru,
            'pelayanan_id' => $arrPelayananIdNew,
        ];
    }

    private static function generateTindakanNaikKelas($params)
    {
        $data = isset($params['tindakanBaru']) ? $params['tindakanBaru'] : [];
        $komponent = isset($params['komponent']) ? $params['komponent'] : [];
        $type = isset($params['type']) ? $params['type'] : [];
        $tanggalMasuk = isset($params['tanggalMasuk']) ? $params['tanggalMasuk'] : null;
        $tanggalKeluar = isset($params['tanggalKeluar']) ? $params['tanggalKeluar'] : null;
        $gracePeriode = isset($params['gracePeriode']) ? $params['gracePeriode'] : false;
        $paramsType = isset($params['paramsType']) ? $params['paramsType'] : [];
        if ($type == 'kamar') {
            return self::generateTarifKamar($data, $komponent, $tanggalMasuk, $tanggalKeluar, $gracePeriode, $paramsType);
        } else {
            return self::generateTarif($data, $komponent, $type);
        }
    }

    private static function getKomponent($params)
    {
        $tindakanId = isset($params['tindakanId']) ? $params['tindakanId'] : [];
        $penjamin_id = isset($params['penjamin_id']) ? $params['penjamin_id'] : [];
        $kelaspelayanan_id = isset($params['kelaspelayanan_id']) ? $params['kelaspelayanan_id'] : [];
        $type = isset($params['type']) ? $params['type'] : 'pelayanan';
        $dataPasien = isset($params['dataPasien']) ? $params['dataPasien'] : [];
        $kamarRuanganId = isset($params['kamarruanganId']) ? $params['kamarruanganId'] : [];

        $where = '';
        if ($type == 'kamar') {
            if ($kamarRuanganId) {
                $where = "WHERE kamarruangan_id = {$kamarRuanganId} ";
            }
        } else {
            if (!empty($tindakanId)) {
                $inCondition = "(" . implode(",", $tindakanId) . ")";
                $where = "WHERE daftartindakan_id IN {$inCondition} ";
            }
        }

        $data = Yii::$app->db->createCommand('SELECT daftartindakan_id,
                daftartindakan_nama, harga_tariftindakan, is_akomodasi,
                persencyto_tindakan, tariftindakan_id, komponentarif_id, kelaspelayanan_id,
                carabayar_id, instalasi_id, kamarruangan_id, ruangan_id, penjamin_id
                FROM komponentarifnaikkelas_fn(:penjamin_id,:kelaspelayanan_id,:type) 
                ' . $where . '
                ORDER BY daftartindakan_nama ASC')
            ->bindParam(':penjamin_id', $penjamin_id)
            ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
            ->bindParam(':type', $type);

        $result = $data->queryAll();

        if (empty($result)) {
            $komponenTotal = (new DocoConstansId)->actionGetId('komponen_total');
            $tindakanAkomodasi = DaftarTindakan::find()->where(['is_akomodasi' => true, 'is_active' => true])->one();
            $result = [];
            $result[] = [
                'daftartindakan_id' => ($tindakanAkomodasi) ? $tindakanAkomodasi['daftartindakan_id'] : null,
                'daftartindakan_nama' => ($tindakanAkomodasi) ? $tindakanAkomodasi['daftartindakan_nama'] : null,
                'harga_tariftindakan' => 0,
                'is_akomodasi' => true,
                'persencyto_tindakan' => 0,
                'tariftindakan_id' => null,
                'kamarruangan_id' => $kamarRuanganId,
                'komponentarif_id' => $komponenTotal,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'carabayar_id' => isset($dataPasien['carabayar_id']) ? $dataPasien['carabayar_id'] : null,
                'instalasi_id' => isset($dataPasien['instalasi_id']) ? $dataPasien['instalasi_id'] : null,
                'ruangan_id' => isset($dataPasien['ruangan_id']) ? $dataPasien['ruangan_id'] : null,
                'penjamin_id' => $penjamin_id,
            ];
        }

        $dataArr = [];
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $dataArr[$value['daftartindakan_id']][] = $value;
            }
        }

        return $dataArr;
    }

    private static function getIsNaikKelas($admisiId, $populateData)
    {
        //cek pindah kamar
        $dataPindahKamar = PindahKamar::find()
            ->where(['pasienadmisi_id' => $admisiId])
            ->orderBy(['pindahkamar_id' => SORT_DESC])
            ->asArray()->one();

        $kelasAwal = $populateData['hakKelas'];
        $kelasBaru = ($dataPindahKamar) ? $dataPindahKamar['kelaspelayanan_id'] : null;

        $urutanKelasAwal = self::getUrutanKelas($kelasAwal);
        $urutanKelasBaru = self::getUrutanKelas($kelasBaru);
        $isNaikKelas = false;
        if ($urutanKelasBaru != null && $urutanKelasAwal != null) {
            if ($urutanKelasBaru > $urutanKelasAwal) {
                $isNaikKelas = true;
            }
        }

        return [
            'isNaikKelas' => $isNaikKelas,
            'kelasBaru' => $kelasBaru,
        ];
    }

    private static function getTindakanExisting($admisiId)
    {
        $hariIni = date('Y-m-d 23:59:59');
        $selisihTgl = date('Y-m-d 00:00:00', strtotime("- 2 day"));

        $kelompokTindakan = [
            DocoConstants::KT_BEDAH_SENTRAL, DocoConstants::KT_KONSULTASI,
            DocoConstants::KT_VISITE, DocoConstants::KT_LABORATORIUM,
            DocoConstants::KT_RADIOLOGI
        ];

        $data = InfoTagihanPasien::find()
            ->select([
                'infotagihanpasien_v.pendaftaran_id',
                'infotagihanpasien_v.pelayanan_id',
                'infotagihanpasien_v.tindakan_obat_id as daftartindakan_id',
                'infotagihanpasien_v.tindakan_obat_nama as daftartindakan_nama',
                'infotagihanpasien_v.tarif_satuan as harga_tariftindakan',
                'infotagihanpasien_v.qty',
                'infotagihanpasien_v.pasienadmisi_id',
                'infotagihanpasien_v.kelompoktindakan_id',
                'infotagihanpasien_v.tgl_pelayanan',
                'infotagihanpasien_v.kelaspelayanan_id',
                'infotagihanpasien_v.carabayar_pelayanan_id as carabayar_id',
                'infotagihanpasien_v.penjamin_pelayanan_id as penjamin_id',
                'infotagihanpasien_v.instalasi_id',
                'infotagihanpasien_v.ruangan_id',
                'infotagihanpasien_v.pasien_id',
                'infotagihanpasien_v.jeniskasuspenyakit_id',
                'infotagihanpasien_v.dokterpenanggungjawab_id',
                'infotagihanpasien_v.tarif_cyto',
                'infotagihanpasien_v.is_cyto',
                'infotagihanpasien_v.discount',
                'infotagihanpasien_v.tipepaket_id',
                'infotagihanpasien_v.kamarruangan_id',
            ])
            ->where([
                'infotagihanpasien_v.pasienadmisi_id' => $admisiId,
                'infotagihanpasien_v.is_obat' => false,
                'infotagihanpasien_v.is_akomodasi' => false,
            ])
            ->andWhere(['NOT IN', 'infotagihanpasien_v.kelompoktindakan_id', $kelompokTindakan])
            ->andWhere(['between', 'infotagihanpasien_v.tgl_pelayanan', $selisihTgl, $hariIni])
            ->asArray()->all();

        $dataArr = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $dataArr[$value['daftartindakan_id']] = $value;
            }
        }

        return $dataArr;
    }

    private static function getTarifAkomodasi($ruanganId, $penjamin, $kelas, $dataPasien)
    {
        $type = 'kamar';
        $qTarif = self::getTarif($ruanganId, $penjamin, $kelas, $type);

        if ($qTarif) {
            $tarifAkomodasi = self::generateTindakan($qTarif, $dataPasien/*, $konfigPercen*/);

            return $tarifAkomodasi;
        }

        return [];
    }

    private static function generateKomponent($params)
    {
        $dataKomponent = [];
        $komponent = isset($params['komponent']) ? $params['komponent'] : [];
        $daftartindakanId = isset($params['daftartindakan_id']) ? $params['daftartindakan_id'] : null;
        $is_cyto = isset($params['is_cyto']) ? $params['is_cyto'] : null;
        $type = isset($params['type']) ? $params['type'] : 'pelayanan';
        $persentase = isset($params['persentase']) ? $params['persentase'] : 0;
        $detail_akomodasi = isset($params['detail_akomodasi']) ? $params['detail_akomodasi'] : [];
        $qty_tindakan = isset($params['qty_tindakan']) ? $params['qty_tindakan'] : 0;

        if (isset($komponent[$daftartindakanId])) {
            if (!empty($komponent[$daftartindakanId])) {
                foreach ($komponent[$daftartindakanId] as $value) {

                    $tarifSatuan = $value['harga_tariftindakan'];
                    $persen_cyto = ($type == 'kamar') ? 0 : $value['persencyto_tindakan'];
                    $qty = ($type == 'kamar') ? 1 : $qty_tindakan;
                    $komponen_cyto = 0;

                    if ($type == 'kamar') {
                        $harga_komponent = ($persentase / 100) * $tarifSatuan;
                        $komponen_cyto = 0;
                    } else {
                        if ($is_cyto) {
                            $komponen_cyto = ($persen_cyto / 100) * $tarifSatuan;
                            $harga_komponent = $tarifSatuan + $komponen_cyto;
                        } else {
                            $harga_komponent = $tarifSatuan;
                        }
                    }

                    $dataKomponent[] = [
                        'komponentarif_id' => $value['komponentarif_id'],
                        'tindakanpelayanan_id' => null,
                        'tarif_kompsatuan' => (float) $harga_komponent,
                        'tarif_tindakankomp' => (float) ($qty * $harga_komponent),
                        'tarifcyto_tindakankomp' => $komponen_cyto,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                    ];
                }
            }

            $additional_data = [
                'list_komponen' => $dataKomponent,
            ];
        } else {
            $additional_data = [
                'list_komponen' => [],
            ];
        }

        $additional_data['detail_akomodasi'] = $detail_akomodasi;

        return $additional_data;
    }

    private static function generateAkomodasi($tanggalMasuk, $tanggalKeluar, $gracePeriode = false, $paramsType = [])
    {
        $konfig_akomodasi = LookupTransaksi::find()->select(['additional_value'])->where(['kode_transaksi' => 'konfig_stop_akomodasi'])->asArray()->one();
        $data_konfig = json_decode($konfig_akomodasi['additional_value']);;
        $cutOff = $data_konfig[0];
        $gracePeriode = $data_konfig[1];
        $halfCharge = $data_konfig[2];
        AkomodasiAttributes::getInstance($tanggalMasuk, $tanggalKeluar, $cutOff, $gracePeriode, $halfCharge, true, $paramsType);
        return Yii::$app->docoPlugin->execute('generate_akomodasi');
    }

    private static function intersectJamMasuk($qMasukKamarList) {
        $konfig_akomodasi = LookupTransaksi::find()->select(['additional_value'])->where(['kode_transaksi' => 'konfig_stop_akomodasi'])->asArray()->one();
        $data_konfig = json_decode($konfig_akomodasi['additional_value']);;
        $cutOff = $data_konfig[0];

        $qMasukKamarListTemp = [];
        $lastTanggalKeluar = null;
        foreach ($qMasukKamarList as $i => $qMasukKamar) {
            $tanggalMasuk = strtotime(date('Y-m-d', strtotime($qMasukKamar['tgl_masukkamar'])) . ' ' .
                date('H:i:s', strtotime($qMasukKamar['jam_masukkamar'])));
            $tanggalKeluar = strtotime(date('Y-m-d', strtotime($qMasukKamar['tgl_keluarkamar'])) . ' ' .
                date('H:i:s', strtotime($qMasukKamar['jam_keluarkamar'])));

            if($i != 0) {
                $tanggalMasukCutoff = strtotime(date('Y-m-d ' . $cutOff, $tanggalMasuk));
                $tanggalMasukCutoff = $tanggalMasukCutoff > $tanggalMasuk ? strtotime('-1 day', $tanggalMasukCutoff) : $tanggalMasukCutoff;
                if($lastTanggalKeluar !== null) {
                    $tanggalMasukCutoff = $tanggalMasukCutoff < $lastTanggalKeluar ? $lastTanggalKeluar : $tanggalMasukCutoff;
                }
                $tanggalMasuk = $tanggalMasukCutoff;
            }


            if($i != (sizeof($qMasukKamarList) - 1)) {
                $tanggalKeluarCutoff = strtotime(date('Y-m-d ' . $cutOff, $tanggalKeluar));
                $tanggalKeluarCutoff = $tanggalKeluarCutoff > $tanggalKeluar ? strtotime('-1 day', $tanggalKeluarCutoff) : $tanggalKeluarCutoff;
                $tanggalKeluarCutoff = $tanggalKeluarCutoff < $tanggalMasuk ? $tanggalMasuk : $tanggalKeluarCutoff;
                $tanggalKeluar = $tanggalKeluarCutoff;
            }

            if($tanggalMasuk < $tanggalKeluar) {
                $qMasukKamar['tgl_masukkamar'] = date('Y-m-d', $tanggalMasuk);
                $qMasukKamar['jam_masukkamar'] = date('H:i:s', $tanggalMasuk);
                $qMasukKamar['tgl_keluarkamar'] = date('Y-m-d', $tanggalKeluar);
                $qMasukKamar['jam_keluarkamar'] = date('H:i:s', $tanggalKeluar);
                $qMasukKamarListTemp[] = $qMasukKamar;
            }
            $lastTanggalKeluar = $tanggalKeluar;
        }

        return $qMasukKamarListTemp;
    }

    private static function generateTarifKamar($data, $komponent, $tanggalMasuk, $tanggalKeluar, $gracePeriode = false , $paramsType = [])
    {
        $dataResult = [];
        $data = isset($data['tindakan']) ? $data['tindakan'] : [];
        $type = 'kamar';
        $temp_no = 0;
        $generateAkomodasi = self::generateAkomodasi($tanggalMasuk, $tanggalKeluar, $gracePeriode, $paramsType);
        if (!empty($generateAkomodasi)) {
            foreach ($generateAkomodasi as $key => $value) {
                $tindakanId = isset($data['daftartindakan_id']) ? $data['daftartindakan_id'] : null;
                $tarifSatuan = isset($data['harga_tariftindakan']) ? $data['harga_tariftindakan'] : null;
                $totalTarif = ($value['total_tarif'] / 100) * $tarifSatuan;

                $paramsKomponent = [
                    'komponent' => $komponent,
                    'daftartindakan_id' => $tindakanId,
                    'is_cyto' => false,
                    'type' => $type,
                    'persentase' => $value['total_tarif'],
                    'detail_akomodasi' => [
                        'tanggal' => $value['tanggal'],
                        'persentase' => $value['total_tarif'],
                    ]
                ];
                $additional_data = self::generateKomponent($paramsKomponent);
                $dataResult[] = [
                    'temp_no' => $temp_no,
                    'kelaspelayanan_id' => $data['kelaspelayanan_id'],
                    'pasien_id' => $data['pasien_id'],
                    'daftartindakan_id' => $data['daftartindakan_id'],
                    'tipepaket_id' => $data['tipepaket_id'],
                    'carabayar_id' => $data['carabayar_id'],
                    'pendaftaran_id' => $data['pendaftaran_id'],
                    'pasienadmisi_id' => $data['pasienadmisi_id'],
                    'jeniskasuspenyakit_id' => $data['jeniskasuspenyakit_id'],
                    'instalasi_id' => $data['instalasi_id'],
                    'kamarruangan_id' => $data['kamarruangan_id'],
                    'ruangan_id' => $data['ruangan_id'],
                    'penjamin_id' => $data['penjamin_id'],
                    'tgl_tindakan' => $data['tgl_pelayanan'],
                    'dokterpenanggungjawab_id' => $data['dokterpenanggungjawab_id'],
                    'tarif_satuan' => (float) $totalTarif,
                    'qty_tindakan' => $value['total_tarif'] / 100,
                    'tarif_tindakan' => (float) ($data['qty'] * $totalTarif),
                    'tarifcyto_tindakan' => 0,
                    'cyto_tindakan' => false,
                    'discount_tindakan' => 0,
                    'additional_data' => json_encode($additional_data),
                ];
                $temp_no++;
            }
        }

        return $dataResult;
    }

    private static function generateTarif($data, $komponent, $type)
    {
        $dataResult = [];
        $result = $data['tindakan'];

        if ($type) {
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $tindakanId = $value['daftartindakan_id'];
                    $tarifSatuan = $value['harga_tariftindakan'];
                    $is_cyto = $value['is_cyto'];
                    if ($is_cyto) {
                        $persencyto_tindakan = $value['persencyto_tindakan'];
                        $tarif_cyto = ($persencyto_tindakan / 100) * $value['harga_tariftindakan'];
                        $tarifSatuan = $value['harga_tariftindakan'] + $tarif_cyto;
                    } else {
                        $tarif_cyto = $value['tarif_cyto'];
                    }

                    $paramsKomponent = [
                        'komponent' => $komponent,
                        'daftartindakan_id' => $tindakanId,
                        'is_cyto' => $is_cyto,
                        'qty_tindakan' => $value['qty'],
                    ];

                    $additional_data = self::generateKomponent($paramsKomponent);
                    $dataResult[$tindakanId] = [
                        'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'pasien_id' => $value['pasien_id'],
                        'daftartindakan_id' => $tindakanId,
                        'tipepaket_id' => $value['tipepaket_id'],
                        'carabayar_id' => $value['carabayar_id'],
                        'pendaftaran_id' => $value['pendaftaran_id'],
                        'pasienadmisi_id' => $value['pasienadmisi_id'],
                        'jeniskasuspenyakit_id' => $value['jeniskasuspenyakit_id'],
                        'instalasi_id' => $value['instalasi_id'],
                        'kamarruangan_id' => $value['kamarruangan_id'],
                        'ruangan_id' => $value['ruangan_id'],
                        'penjamin_id' => $value['penjamin_id'],
                        'tgl_tindakan' => $value['tgl_pelayanan'],
                        'dokterpenanggungjawab_id' => $value['dokterpenanggungjawab_id'],
                        'tarif_satuan' => (float) $tarifSatuan,
                        'qty_tindakan' => $value['qty'],
                        'tarif_tindakan' => (float) ($value['qty'] * $tarifSatuan),
                        'tarifcyto_tindakan' => $tarif_cyto,
                        'cyto_tindakan' => $is_cyto,
                        'discount_tindakan' => $value['discount'],
                        'additional_data' => json_encode($additional_data),
                    ];
                }
            }
        }

        return $dataResult;
    }

    private static function generateItemAkomodasi($params)
    {
        $dataPasien = isset($params['dataPasien']) ? $params['dataPasien'] : [];

        $pasienId = $dataPasien['pasien_id'];
        $pendaftaranId = $dataPasien['pendaftaran_id'];
        $pasienAdmisiId = $dataPasien['pasienadmisi_id'];
        $jenisKasusPenyakitId = $dataPasien['jeniskasuspenyakit_id'];
        $dokterPjId = $dataPasien['dokter_id'];
        $qty = 1;
        $tarif_cyto = 0;
        $is_cyto = false;
        $discount = 0;
        $tgl_pelayanan = date('Y-m-d H:i:s');
        $instalasiId = $dataPasien['instalasi_id'];
        $ruanganId = $dataPasien['ruangan_id'];
        $caraBayarId = $dataPasien['carabayar_id'];
        $penjaminId = $dataPasien['penjamin_id'];
        $tipePaketId = null;
        $persenCyto = 0;

        return [
            'pasienId' => $pasienId,
            'pendaftaranId' => $pendaftaranId,
            'pasienAdmisiId' => $pasienAdmisiId,
            'jenisKasusPenyakitId' => $jenisKasusPenyakitId,
            'dokterPjId' => $dokterPjId,
            'qty' => $qty,
            'tarif_cyto' => $tarif_cyto,
            'is_cyto' => $is_cyto,
            'discount' => $discount,
            'tgl_pelayanan' => $tgl_pelayanan,
            'instalasiId' => $instalasiId,
            'ruanganId' => $ruanganId,
            'caraBayarId' => $caraBayarId,
            'penjaminId' => $penjaminId,
            'tipePaketId' => $tipePaketId,
            'persenCyto' => $persenCyto,
        ];
    }

    private static function generateItem($params)
    {
        $dataPasien = isset($params['dataPasien']) ? $params['dataPasien'] : [];
        $tindakanExisting = isset($params['tindakan_existing']) ? $params['tindakan_existing'] : [];
        $daftartindakan_id = isset($params['daftartindakan_id']) ? $params['daftartindakan_id'] : null;
        $pelayanan_id = isset($params['pelayanan_id']) ? $params['pelayanan_id'] : null;
        $data = isset($params['data']) ? $params['data'] : [];
        $existing = isset($tindakanExisting[$daftartindakan_id]) ? $tindakanExisting[$daftartindakan_id] : [];

        $pasienId = ($existing) ? $existing['pasien_id'] : $dataPasien['pasien_id'];
        $pendaftaranId = ($existing) ? $existing['pendaftaran_id'] : $dataPasien['pendaftaran_id'];
        $pasienAdmisiId = ($existing) ? $existing['pasienadmisi_id'] : $dataPasien['pasienadmisi_id'];
        $jenisKasusPenyakitId = ($existing) ? $existing['jeniskasuspenyakit_id'] : $dataPasien['jeniskasuspenyakit_id'];
        $dokterPjId = ($existing) ? $existing['dokterpenanggungjawab_id'] : $dataPasien['dokter_id'];
        $qty = ($existing) ? $existing['qty'] : 1;
        $tarif_cyto = ($existing) ? $existing['tarif_cyto'] : 0;
        $is_cyto = ($existing) ? $existing['is_cyto'] : false;
        $discount = ($existing) ? $existing['discount'] : 0;
        $tgl_pelayanan = ($existing) ? $existing['tgl_pelayanan'] : date('Y-m-d H:i:s');
        $instalasiId = ($existing) ? $existing['instalasi_id'] : $dataPasien['instalasi_id'];
        $ruanganId = ($existing) ? $existing['ruangan_id'] : $dataPasien['ruangan_id'];
        $caraBayarId = ($existing) ? $existing['carabayar_id'] : $dataPasien['carabayar_id'];
        $penjaminId = ($existing) ? $existing['penjamin_id'] : $dataPasien['penjamin_id'];
        $tipePaketId = ($existing) ? $existing['tipepaket_id'] : null;
        $persenCyto = ($existing) ? $data['persencyto_tindakan'] : 0;

        return [
            'pasienId' => $pasienId,
            'pendaftaranId' => $pendaftaranId,
            'pasienAdmisiId' => $pasienAdmisiId,
            'jenisKasusPenyakitId' => $jenisKasusPenyakitId,
            'dokterPjId' => $dokterPjId,
            'qty' => $qty,
            'tarif_cyto' => $tarif_cyto,
            'is_cyto' => $is_cyto,
            'discount' => $discount,
            'tgl_pelayanan' => $tgl_pelayanan,
            'instalasiId' => $instalasiId,
            'ruanganId' => $ruanganId,
            'caraBayarId' => $caraBayarId,
            'penjaminId' => $penjaminId,
            'tipePaketId' => $tipePaketId,
            'persenCyto' => $persenCyto,
        ];
    }

    private static function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new \DateInterval('P1D');
        $realEnd = new \DateTime($end);
        $realEnd->add($interval);
        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd);
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    private static function generateAkomodasiKamar($populateData, $dataPasien, $gracePeriode = false, $endDate , $paramsType = [])
    {
        $penjamin = isset($populateData['penjamin']) ? $populateData['penjamin'] : null;
        $kelas = isset($populateData['hakKelas']) ? $populateData['hakKelas'] : null;
        $kamarruangan_id = isset($populateData['kamarruanganId']) ? $populateData['kamarruanganId'] : null;
        $startDate = isset($populateData['startDate']) ? $populateData['startDate'] : null;
        $tanggalKeluar = isset($populateData['tanggalKeluar']) ? $populateData['tanggalKeluar'] : null;
        $ruanganId = isset($populateData['ruanganId']) ? $populateData['ruanganId'] : null;

        // penyesuaian akomodasi
        $paramsAkomodasi = [
            'penjamin_id' => $penjamin,
            'kelaspelayanan_id' => $kelas,
            'type' => 'kamar',
            'dataPasien' => $dataPasien,
            'kamarruanganId' => $kamarruangan_id,
            'tgl_pelayanan' => date('Y-m-d H:i:00', strtotime($endDate)),
            'ruangan_id'=> $ruanganId,
        ];

        $tarifAkomodasi = self::getTarifNaikKelas($paramsAkomodasi);
        $komponentAkomodasi = self::getKomponent($paramsAkomodasi);

        $paramsAkm = [
            'tindakanBaru' => $tarifAkomodasi,
            'komponent' => $komponentAkomodasi,
            'tanggalMasuk' => $startDate,
            'tanggalKeluar' => $endDate,
            'type' => 'kamar',
            'gracePeriode' => $gracePeriode,
            'paramsType' => $paramsType,
        ];

        $dataInsertAkomodasi = self::generateTindakanNaikKelas($paramsAkm);

        return $dataInsertAkomodasi;
    }

    private static function defaultTindakan($param)
    {
        $kamarRuanganId = isset($param['kamarruanganId']) ? $param['kamarruanganId'] : null;
        $kelasPelayananId = isset($param['kelaspelayanan_id']) ? $param['kelaspelayanan_id'] : null;
        $carabayar_id = isset($param['carabayar_id']) ? $param['carabayar_id'] : null;
        $instalasi_id = isset($param['instalasi_id']) ? $param['instalasi_id'] : null;
        $penjamin_id = isset($param['penjamin_id']) ? $param['penjamin_id'] : null;
        $ruangan_id = isset($param['ruangan_id']) ? $param['ruangan_id'] : null;

        $komponenTotal = (new DocoConstansId)->actionGetId('komponen_total');
        $tindakanAkomodasi = DaftarTindakan::find()->where(['is_akomodasi' => true, 'is_active' => true])->one();

        return [
            'daftartindakan_id' => ($tindakanAkomodasi) ? $tindakanAkomodasi['daftartindakan_id'] : null,
            'daftartindakan_nama' => ($tindakanAkomodasi) ? $tindakanAkomodasi['daftartindakan_nama'] : null,
            'harga_tariftindakan' => 0,
            'is_akomodasi' => true,
            'persencyto_tindakan' => 0,
            'tariftindakan_id' => null,
            'kamarruangan_id' => $kamarRuanganId,
            'komponentarif_id' => $komponenTotal,
            'kelaspelayanan_id' => $kelasPelayananId,
            'carabayar_id' => $carabayar_id,
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
        ];
    }

    private static function getKonfigStopAkomodasi()
    {
        $konfig_akomodasi = LookupTransaksi::find()->select(['additional_value'])->where(['kode_transaksi' => 'konfig_stop_akomodasi'])->asArray()->one();
        $data_konfig = json_decode($konfig_akomodasi['additional_value']);;

        return $data_konfig;
    }

    private static function getInfoTagihanPasien($pasienAdmisiId, $startDate = null, $is_reset = false)
    {
        $listInfoTagihan = InfoTagihanPasien::find()
            ->select(['kelaspelayanan_id','tgl_pelayanan','kelaspelayanan_id','additional_data'])
            ->andWhere(['is_akomodasi' => true])
            ->andWhere(['pasienadmisi_id' => $pasienAdmisiId])
            ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI]);
        if($is_reset && !empty($startDate)){
            $listInfoTagihan->andWhere("tgl_pelayanan > '$startDate'");
        }
        return $listInfoTagihan->orderBy(['tgl_pelayanan' => SORT_DESC])->asArray()->all();
    }

    private static function checkIsExist($dataInsertAkomodasi, $pasienAdmisiId, $startDate = null, $is_reset = false) {
        $data_konfig = self::getKonfigStopAkomodasi();
        $cutOff = $data_konfig[0];
        $listInfoTagihan = self::getInfoTagihanPasien($pasienAdmisiId,$startDate,$is_reset);
        $lastTagihan = reset($listInfoTagihan);
        $dataInsertAkomodasiResult = [];
        $countInsert = 0;

        foreach ($dataInsertAkomodasi as $currAkomodasi) {
            $isExist = false;
            foreach ($listInfoTagihan as $infoTagihan) {

                if($infoTagihan['kelaspelayanan_id'] == $lastTagihan['kelaspelayanan_id']) {
                    $tagihanDate = self::getPeriodeCutoff($infoTagihan['tgl_pelayanan'], $cutOff);
                    $akomodasiDate = self::getPeriodeCutoff($currAkomodasi['tgl_tindakan'], $cutOff);
                    if($tagihanDate == $akomodasiDate) {
                        $additional_data = json_decode($infoTagihan['additional_data'], true);
                        $qty = $currAkomodasi['qty_tindakan'] - ($additional_data['detail_akomodasi']['persentase'] / 100);

                        if($qty > 0) {
                            $isExist = false;
                            $currAkomodasi['qty_tindakan'] = $qty;
                            // tarif satuan sudah hasil dari  perhitungan dengan qty, jadi tarif tindakan tidak perlu lagi di hitung bersama qty
                            $currAkomodasi['tarif_tindakan'] = $currAkomodasi['tarif_satuan'];
                        } else {
                            $isExist = true;
                            break;
                        }

                    }
                } else {
                    continue;
                }
            }

            if(!$isExist) {
                $countInsert++;
                $dataInsertAkomodasiResult[] = $currAkomodasi;
            }
        }

        if (count($dataInsertAkomodasi) > count($listInfoTagihan) + $countInsert && !empty($currAkomodasi)) {
            $total_data = count($dataInsertAkomodasi) - (count($listInfoTagihan) + $countInsert);
            $reverse_dataInsertAkomodasi = array_reverse($dataInsertAkomodasi);
            foreach($reverse_dataInsertAkomodasi as $currAkomodasi){
                if($total_data <= 0){
                    break;
                }
                if(!in_array($currAkomodasi['temp_no'], array_column($dataInsertAkomodasiResult, 'temp_no'))){
                    $dataInsertAkomodasiResult[] = $currAkomodasi;
                    $total_data--;
                }
            }
        }

        return $dataInsertAkomodasiResult;
    }

    private static function getPeriodeCutoff($date, $cutoff) {
        $dateCutoff = explode(' ', $date)[0] . ' ' .$cutoff;
        $timeDate = strtotime($date);
        $timeCutoff = strtotime($dateCutoff);
        if($timeDate < $timeCutoff) {
            $timeCutoff = date('Y-m-d H:i:s', strtotime($timeCutoff . ' +1 day'));
        }
        return $timeCutoff;
    }

    public function getTodayAkomodasi($pasienadmisiId, $date)
    {
        $tindakanAkomodasi = DaftarTindakan::find()->select([
            'daftartindakan_id'
        ])->where(['is_akomodasi' => true, 'is_active' => true])->one();

        $result = TindakanPelayanan::find()->select([
            'tindakanpelayanan_id',
            'daftartindakan_id',
            'created_date',
            'tgl_tindakan',
        ])->where([
            'daftartindakan_id' => $tindakanAkomodasi->daftartindakan_id,
            'pasienadmisi_id' => $pasienadmisiId,
            'DATE(tgl_tindakan)' => date('Y-m-d', strtotime($date)),
        ])->orderBy([
            'tgl_tindakan' => SORT_DESC,
        ]);

        $queryResult = $result->asArray()->one();
        return $queryResult;
    }
}
