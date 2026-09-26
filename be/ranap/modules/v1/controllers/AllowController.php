<?php
/*
 * @Author: metafiliana
 * @Date: 2018-01-29 13:10:59
 * @Last Modified by: Anggoro <tri.anggoro@docotel.com>
 * @Last Modified time: 2019-06-21
 * @Description:
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstansId;
use yii\db\Expression;

use Doco\models\PegawaiView;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\KelompokDiagnosa;
use app\modules\v1\models\RujukanKeluar;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\PasienMorbiditas;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\DokterV;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\TarifTindakanRuangan;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\BodyMassIndexAnak;
use app\modules\v1\models\KlasifikasiTekananDarah;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\BagianTubuhDetail;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\InfoKunjunganRi;
use app\modules\v1\models\Sysdia;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\RekonsiliasiObat;
use app\modules\v1\models\RekonsiliasiObatView;
use app\modules\v1\models\TarifTindakanLabView;
use app\modules\v1\models\PaketTindakanLabView;
use app\modules\v1\models\InfoTarifPenunjangView;
use app\modules\v1\models\InfoTarifPaketPenunjangView;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\PermintaanKonsulView;
use app\modules\v1\models\KasusPenyakitRuanganView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\InfoPasienPenunjangView;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\KondisiKeluar;
use app\modules\v1\models\WarnaTempatTidur;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\InfoTarifKamarView;
use app\modules\v1\models\TindakanBmhpView;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\TarifTotalRs;
use app\modules\v1\models\KonfigPelayanan;
use app\modules\v1\payload\TarifPayload;
use app\modules\v1\payload\ParamModel;
use app\modules\v1\models\AsesmenAwal;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\businessLogic\TindakanAkomodasi;
use Doco\models\InfoStokObatAlkesFnr;
use app\modules\v1\actions\Allow\GetOrderFisioAction;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = 'app\modules\v1\models\PenjaminPendaftaran';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["allow-get-list-data"] = ["GET"];


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action


        return $actions;
    }

    /**
     *
     * @see Fungsi get list data
     * @return array
     *
     */
    public function actionAllowGetListData()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('id_ruangan');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id');
            $penjamin_id = $request->get('penjamin_id');
            $pendaftaran_id = $request->get('pendaftaran_id');

            /** Cek Pasien Titip */
            $isTitipan = false;
            $pasienTitipan = [];
            $cekData = [];
            if ($pendaftaran_id) {
                /** Case jika ada pindah kamar */
                $pasienTitipan = PindahKamar::getKelasTitipan($pendaftaran_id);
                /** Case jika tidak ada pindah kamar */
                $cekData = $this->cekData($pendaftaran_id);
                if (!empty($pasienTitipan)) {
                    if ($pasienTitipan[0]['is_pasientitipan'] == true) {
                        if ($pasienTitipan[0]['is_stoptitipan'] != true) {
                            $isTitipan = true;
                            $kelaspelayanan_id = !empty($pasienTitipan[0]['kelas_ditagihkan_id']) ? $pasienTitipan[0]['kelas_ditagihkan_id'] : $kelaspelayanan_id;
                        } else {
                            $kelaspelayanan_id = $pasienTitipan[0]['kelaspelayanan_id'];
                        }
                    } else {
                        $kelaspelayanan_id = $pasienTitipan[0]['kelaspelayanan_id'];
                    }
                } else if ($cekData['is_pasientitipan'] == true) {
                    if ($cekData['is_stoppasientitipan'] != true) {
                        $isTitipan = true;
                        $kelaspelayanan_id = $cekData['kelas_ditagihkan_id'];
                    } else {
                        $kelaspelayanan_id = $cekData['kelaspelayanan_id'];
                    }
                }
            }

            $modelPayload = new ParamModel;
            /** validation payload */
            $modelPayload->attributes = [
                'ruangan_id' => $ruangan_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'penjamin_id' => $penjamin_id,
                'pendaftaran_id' => $pendaftaran_id,
            ];

            /** Validate Type data payload */
            if (!$modelPayload->validate()) {
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'data' => $modelPayload->errors
                ];
            }
            // get data all obatalkes_m
            // $data_obatalkes = $this->getOrSetCache(
            //     DocoConstants::VAR_CACHE_OBATALKES,
            //     $this->getObatAlkes(DocoConstants::JENIS_OBATALKES_OBAT)
            // );

            // get data tindakan per ruangan
            $data_tindakanruangan = $this->getDataTindakanRuangan(
                $ruangan_id,
                $kelaspelayanan_id,
                $penjamin_id,
                DocoConstants::KOMPONEN_TARIF
            );


            // get data tindakan per paket
            $data_paketruangan = $this->getDataPaket(
                $ruangan_id,
                $kelaspelayanan_id,
                $penjamin_id,
                DocoConstants::KOMPONEN_TARIF
            );

            $listDaftarTindakan = $tmpListTindakan = [];
            /**  Untuk Paket */
            $listTipePaket = $listpaket = $qDetailPaket = [];
            /** List Tindakan Ruangan **/
            foreach ($data_tindakanruangan as $value) {
                $row = $value;
                $row['list_bmhp'] = [];
                $daftarTindakan = $value['daftartindakan_id'];
                $listDaftarTindakan[] = $value['daftartindakan_id'];
                $tmpListTindakan[$daftarTindakan] = $row;
            }

            if (!empty($data_paketruangan)) {
                foreach ($data_paketruangan as $value) {
                    $tipePaketId = $value['tipepaket_id'];
                    $row = $value;
                    $row['list_bmhp'] = [];
                    $row['paketDetail'] = [];
                    $listpaket[$tipePaketId] = $row;
                    $listTipePaket[] = $tipePaketId;
                }
                if (!empty($listTipePaket)) {
                    $qDetailPaket = PaketDetailView::find()->where([
                        'tipepaket_id',
                        'daftartindakan_id',
                        'daftartindakan_nama',
                    ])->where([
                        'tipepaket_id' => $listTipePaket
                    ])->asArray()->all();

                    foreach ($qDetailPaket as $value) {
                        $daftarTindakan = $value['daftartindakan_id'];
                        if (!isset($tmpListTindakan[$daftarTindakan])) {
                            $listDaftarTindakan[] = $daftarTindakan;
                        }
                    }
                }
            }

            if (!empty($listDaftarTindakan)) {
                $qBmhp = TindakanBmhpView::find()->select([
                    'daftartindakan_id',
                    'daftartindakan_nama',
                    'obatalkes_id',
                    'obatalkes_nama',
                    'qty_konversi',
                ])->where([
                    'daftartindakan_id' => $listDaftarTindakan
                ])->asArray()->all();
                $medicineIds = [];
                foreach ($qBmhp as $recordBmhp) {
                    $medicineIds[] = $recordBmhp['obatalkes_id'];
                }
                $qStok = (new InfoStokObatAlkesFnr(['extParam' => [$penjamin_id, $kelaspelayanan_id, $ruangan_id]]))->find()->select([
                    'obatalkes_id',
                    'qty_tersedia',
                    'jml_hargajual'
                ])->where(['obatalkes_id' => $medicineIds])->asArray()->all();
                $listStok = [];
                /** Mapping Obat Ke Stok */
                foreach ($qStok as $value) {
                    $listStok[$value['obatalkes_id']] = $value;
                }
                $mappTindakanObat = [];
                /** Mapping ke BMHP */
                foreach ($qBmhp as $value) {
                    $daftarTindakan = $value['daftartindakan_id'];
                    $row = $value;
                    $row['stok'] = 0;
                    $row['harga'] = 0;
                    if (isset($listStok[$row['obatalkes_id']])) {
                        $row['stok'] = $listStok[$row['obatalkes_id']]['qty_tersedia'];
                        $row['harga'] = $listStok[$row['obatalkes_id']]['jml_hargajual'];
                    }
                    if (isset($tmpListTindakan[$daftarTindakan]['list_bmhp'])) {
                        $tmpListTindakan[$daftarTindakan]['list_bmhp'][] = $row;
                    }
                    $mappTindakanObat[$daftarTindakan][] = $row;
                }
                /** Mapping Paket Ke tindakan */
                if (!empty($qDetailPaket)) {
                    $checkListMappTindakan = [];
                    foreach ($qDetailPaket as $value) {
                        $tipePaketId = $value['tipepaket_id'];
                        $daftarTindakan = $value['daftartindakan_id'];

                        if (isset($listpaket[$tipePaketId]['paketDetail'])) {
                            $listpaket[$tipePaketId]['paketDetail'][] = [
                                'daftartindakan_id' => $daftarTindakan,
                                'daftartindakan_nama' => $value['daftartindakan_nama'],
                            ];
                        }

                        if (isset($listpaket[$tipePaketId]['list_bmhp'])) {
                            if (isset($mappTindakanObat[$daftarTindakan])) {
                                $obatAlkes = $mappTindakanObat[$daftarTindakan];
                                foreach ($obatAlkes as $dataObat) {
                                    $idObat = $dataObat['obatalkes_id'];
                                    if (isset($checkListMappTindakan[$tipePaketId][$idObat])) {
                                        /** Re-Calculate Obat Alkes Yang Sama */
                                        foreach ($listpaket[$tipePaketId]['list_bmhp'] as $primary => $obatBmhp) {
                                            $row = $obatBmhp;
                                            $obatPaket = $row['obatalkes_id'];
                                            if ($obatPaket === $idObat) {
                                                $row['qty_konversi'] += $dataObat['qty_konversi'];
                                                $listpaket[$tipePaketId]['list_bmhp'][$primary] = $row;
                                            }
                                        }
                                    } else {
                                        $listpaket[$tipePaketId]['list_bmhp'][] = $dataObat;
                                        $checkListMappTindakan[$tipePaketId][$idObat] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }


            // get data dokter per ruangan
            $data_dokter = $this->getOrSetCache(
                DocoConstants::VAR_CACHE_DOKTER_RUANGAN,
                $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS),
                true,
                $ruangan_id
            );


            // get data perawat per ruangan
            $data_perawat = $this->getOrSetCache(
                DocoConstants::VAR_CACHE_PERAWAT_RUANGAN,
                $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAKEPERAWATAN),
                true,
                $ruangan_id
            );

            // get data satuan tindakan via lookup
            $data_satuantindakan = $this->getOrSetCache(
                DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE,
                $this->getLookupByType('satuan_tindakan'),
                true,
                'satuan_tindakan'
            );

            // konfig farmasi
            $konfig = $this->getOrSetCache(
                DocoConstants::VAR_CACHE_KONFIG_FARMASI,
                KonfigFarmasi::find(),
                false
            );

            // count data tindakan & bmhp for print
            // $count = RiwayatTindakanView::find()->where([
            //     'pendaftaran_id' => $pendaftaran_id
            // ])->count();

            $data_jenisinstruksi = $this->getOrSetCache(
                DocoConstants::VAR_CACHE_LOOKUP_JENISINSTRUKSI,
                $this->getLookupByType('jenis_instruksi'),
                true,
                'jenis_instruksi'
            );

            return [
                // 'data-obatalkes' => $data_obatalkes,
                'data-tindakanruangan' => $tmpListTindakan,
                'data-paket' => $listpaket,
                'data-dokter' => $data_dokter,
                'data-perawat' => $data_perawat,
                'data-satuantindakan' => $data_satuantindakan,
                'data-konfigfarmasi' => $konfig,
                // 'count-riwayat' => $count,
                'data-jenisinstruksi' => $data_jenisinstruksi
            ];
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get riwayat diagnosa pasien
     * @return array
     *
     */
    public function actionAllowGetDiagnosaPasien()
    {
        $request = Yii::$app->request;
        try {
            $pasien_id = $request->get('pasien_id');

            $data_pasien = PasienMorbiditas::find()
                ->select(['diagnosa_id'])
                ->where(['pasien_id' => $pasien_id])
                ->asArray()->all();

            $return = [];
            foreach ($data_pasien as $key => $value) {
                if (!in_array($value['diagnosa_id'], $return)) {
                    array_push($return, (string)$value['diagnosa_id']);
                }
            }

            return [
                'data-diagnosa' => $return
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

    /**
     *
     * @see Fungsi get data penjamin
     * @return array, activeQueryRecords
     *
     */
    private function getPenjamin()
    {
        $penjamin = Penjamin::find();

        return $penjamin;
    }

    /**
     *
     * @see Fungsi get data jabatan
     * @return array, activeQueryRecords
     *
     */
    private function getJabatan()
    {
        $result = Jabatan::find();

        return $result;
    }

    /**
     *
     * @see Fungsi get data pegawai ruangan
     * @return array, activeQueryRecords
     *
     */
    private function getPegawaiRuangan($ruangan_id = null, $kelompokpegawai = null)
    {
        $sql = "
            SELECT
                ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.*
            FROM ruanganpegawai_mp
            JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id
            JOIN kelompokpegawai_m ON kelompokpegawai_m.kelompokpegawai_id = pegawai_m.kelompokpegawai_id
            WHERE ruanganpegawai_mp.is_deleted = FALSE
        ";

        if ($ruangan_id) {
            $sql .= " AND ruanganpegawai_mp.ruangan_id =" . $ruangan_id;
        }

        if ($kelompokpegawai) {
            $sql .= " AND kelompokpegawai_m.kelompokpegawai_namalainnya ='" . $kelompokpegawai . "'";
        }

        $result = RuanganPegawai::findBySql($sql);

        return $result;
    }

    /**
     *
     * @see Fungsi get data diagnosa
     * @return array, activeQueryRecords
     *
     */
    private function getDiagnosa($is_imunisasi = false)
    {
        $data = Diagnosa::find();

        if ($is_imunisasi) {
            $data->andWhere(['diagnosa_imunisasi' => TRUE]);
        }

        return $data;
    }

    /**
     *
     * @see Fungsi get data diagnosa + klasifikasi
     * @return array, activeQueryRecords
     *
     */
    private function getDiagnosaKlasifikasi()
    {
        $sql = '
            SELECT
                t.*, kd.*
            FROM
                diagnosa_m t
            LEFT JOIN klasifikasidiagnosa_m kd ON kd.klasifikasidiagnosa_id = t.klasifikasidiagnosa_id';

        $result = Diagnosa::findBySql($sql);

        return $result;
    }

    /**
     *
     * @see Fungsi get data kelompokdiagnosa
     * @return array, activeQueryRecords
     *
     */
    private function getKelompokDiagnosa()
    {
        $data = KelompokDiagnosa::find();

        return $data;
    }

    /**
     * @see Fungsi get data rujukan keluar
     * @return array, activeQueryRecords
     *
     */
    private function getRujukanKeluar()
    {
        $result = RujukanKeluar::find();

        return $result;
    }

    /**
     * @see Fungsi get data poli dari jadwal poli
     * @return array, activeQueryRecords
     *
     */
    private function getJadwalPoli($ruangan_id = null)
    {
        $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $mapp_hari = DocoConstants::$look_hari;
        $jam = date('H:i:s');
        $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;

        $sql = "
            SELECT
                t.jadwalbukapoli_id, t.ruangan_id, t.hari, t.jam_mulai, t.jam_tutup, r.ruangan_nama
            FROM
                jadwalbukapoli_m t
            LEFT JOIN ruangan_m r ON r.ruangan_id = t.ruangan_id
            LEFT JOIN lookup_m lu ON lu.lookup_id = t.hari
            CROSS JOIN (SELECT '{$jam}'::TIME AS event) sub
            WHERE t.is_deleted = FALSE AND lu.lookup_id = {$hari} AND t.ruangan_id != {$ruangan_id} AND
                CASE WHEN jam_mulai <= jam_tutup THEN jam_mulai<= event AND jam_tutup >= event
                ELSE jam_mulai <= event OR jam_tutup >= event END";

        $result = JadwalPoliklinik::findBySql($sql);

        return $result;
    }

    public function actionGetDokterJadwal()
    {
        $request = Yii::$app->request;

        try {
            $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
            $mapp_hari = DocoConstants::$look_hari;
            $jam = date('H:i:s');
            $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;
            $ruangan_id = $request->get('ruangan_id');

            $sql = "
                SELECT
                    t.pegawai_id, d.nama_pegawai
                FROM
                    jadwaldokter_m t
                LEFT JOIN pegawai_m d ON d.pegawai_id = t.pegawai_id
                INNER JOIN jadwalbukapoli_m p ON t.jadwalbukapoli_id = p.jadwalbukapoli_id
                WHERE t.is_deleted = FALSE
                    AND p.hari = '{$hari}'
                    AND t.ruangan_id = {$ruangan_id}
            ";

            $result = JadwalDokter::findBySql($sql)->asArray()->all();

            return [
                'data-dokter' => $result
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

    /**
     * @see Fungsi get obatalkes
     * @return array, activeQueryRecords
     *
     */
    private function getObatAlkes($jenisobatalkes_id)
    {
        $result = ObatAlkes::find();

        if ($jenisobatalkes_id) {
            $result->where(['jenisobatalkes_id' => $jenisobatalkes_id]);
        }

        return $result;
    }
    /*
     * @see Fungsi get data pegawai dokter ruangan
     * @return array, activeQueryRecords
     *
     */

    public function actionListDokter($ruangan_id)
    {
        try {
            $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
            $mapp_hari = DocoConstants::$look_hari;
            $jam = date('H:i:s');
            $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;

            $condition = [];
            $query = "
                SELECT
                    *
                FROM
                    jadwaldokter_m
                JOIN pegawai_m ON pegawai_m.pegawai_id = jadwaldokter_m.pegawai_id
                JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
                LEFT JOIN kuotadokter_r ON kuotadokter_r.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
                LEFT JOIN lookup_m lu ON lu.lookup_id = jadwaldokter_m.jadwaldokter_hari
                CROSS JOIN (SELECT '{$jam}'::TIME AS event) sub
                WHERE lu.lookup_name = '{$hari}' AND jadwaldokter_m.is_deleted = FALSE AND
                    CASE WHEN jadwaldokter_mulai <= jadwaldokter_tutup THEN jadwaldokter_mulai<= event AND jadwaldokter_tutup >= event
                    ELSE jadwaldokter_mulai <= event OR jadwaldokter_tutup >= event END
            ";

            if ($ruangan_id) {
                $query .= " AND jadwaldokter_m.ruangan_id = :ruangan_id";
                $condition[':ruangan_id'] = $ruangan_id;
            }

            $data = JadwalDokter::findBySql($query, $condition)->asArray()->all();

            return [
                'data' => $data
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

    /**
     *
     * @see Fungsi get data diagnosa per ruangan
     * @var params integer id = ruangan_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataDiagnosaRuangan($ruangan_id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    d.*
                FROM
                    kasuspenyakitruangan_mp kpr
                LEFT JOIN kasuspenyakitdiagnosa_mp kpd ON kpd.jeniskasuspenyakit_id = kpr.jeniskasuspenyakit_id
                JOIN diagnosa_m d ON d.diagnosa_id = kpd.diagnosa_id
                WHERE
                    kpr.is_deleted = FALSE
            ";

        if ($ruangan_id) {
            $sql .= " AND kpr.ruangan_id = :ruangan_id";
            $condition[':ruangan_id'] = $ruangan_id;
        }

        $result = KasusPenyakitRuangan::findBySql($sql, $condition);

        return $result;
    }

    public function actionGetLookupByType($type = null)
    {
        return $this->getLookupByType($type)->asArray()->all();
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->where(['lookup_type' => $type, 'is_active' => 't', 'is_deleted' => 'f']);
        }

        return $result;
    }

    /**
     * @see Fungsi get data info tarif rs
     * @return array, activeQueryRecords
     *
     */
    private function getDataTindakanRuangan($ruangan_id = null, $kelaspelayanan_id = null, $penjamin_id = null, $komponentarif_id = DocoConstants::KOMPONEN_TARIF)
    {
        $request = Yii::$app->request;
        $keyword = $request->get('keyword');
        $keyword = !empty($keyword) ? strtolower($keyword) : '';
        $result = [];

        try {
            $where = '';
            if (!empty($keyword)) {
                $where = "AND LOWER(daftartindakan_nama) LIKE '%$keyword%' ";
            }

            $result = Yii::$app->db->createCommand('SELECT daftartindakan_id,
                    daftartindakan_nama, harga_tariftindakan, is_akomodasi,
                    persencyto_tindakan, tariftindakan_id, komponentarif_id
                    FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id)
                    WHERE tipepaket_id IS NULL
                    ' . $where . '
                    ORDER BY daftartindakan_nama ASC')
                ->bindParam(':ruangan_id', $ruangan_id)
                ->bindParam(':penjamin_id', $penjamin_id)
                ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                ->queryAll();

            return $result;
        } catch (Exception $e) {
            return $result;
        }

        //    $result = InfoTarifRs::find()->select([
        //         'daftartindakan_id',
        //         'daftartindakan_nama',
        //         'harga_tariftindakan',
        //         'is_akomodasi',
        //         'persencyto_tindakan',
        //         'tariftindakan_id',
        //         'komponentarif_id',
        //     ]);


        //     if ($ruangan_id){
        //         $result->andWhere(['ruangan_id' => $ruangan_id]);
        //     }

        //     if ($kelaspelayanan_id){
        //         $result->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
        //     }

        //     if ($penjamin_id){
        //         $result->andWhere(['penjamin_id' => $penjamin_id]);
        //     }

        //     if ($komponentarif_id){
        //         $result->andWhere(['komponentarif_id' => $komponentarif_id]);
        //     }

        //     $result->andWhere('tipepaket_id IS NULL');

        //     return $result;
    }

    /**
     * @see Fungsi get data paket
     * @return array
     *
     */
    private function getDataPaket(
        $ruangan_id = null,
        $kelaspelayanan_id = null,
        $penjamin_id = null,
        $komponentarif_id = DocoConstants::KOMPONEN_TARIF
    ) {
        $request = Yii::$app->request;
        $keyword = $request->get('keyword');
        $keyword = !empty($keyword) ? strtolower($keyword) : '';
        $result = [];

        try {
            $where = '';
            if (!empty($keyword)) {
                $where = "AND LOWER(tipepaket_nama) LIKE '%$keyword%' ";
            }
            $result = Yii::$app->db->createCommand('SELECT tipepaket_id,
                    tipepaket_nama, harga_tariftindakan, is_akomodasi,
                    persencyto_tindakan, tariftindakan_id, komponentarif_id
                    FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id)
                    WHERE tipepaket_id IS NOT NULL
                    ' . $where . '
                    ORDER BY tipepaket_nama ASC')
                ->bindParam(':ruangan_id', $ruangan_id)
                ->bindParam(':penjamin_id', $penjamin_id)
                ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                ->queryAll();

            return $result;
        } catch (Exception $e) {
            return $result;
        }

        // $result = InfoTarifRs::find()->select([
        //     'infotarifrs_v.tipepaket_id',
        //     'infotarifrs_v.tipepaket_nama',
        //     'daftartindakan_id',
        //     'daftartindakan_nama',
        //     'harga_tariftindakan',
        //     'is_akomodasi',
        //     'persencyto_tindakan',
        //     'tariftindakan_id',
        //     'komponentarif_id',
        // ]);


        // if ($ruangan_id){
        //     $result->andWhere(['infotarifrs_v.ruangan_id' => $ruangan_id]);
        // }

        // if ($kelaspelayanan_id){
        //     $result->andWhere(['infotarifrs_v.kelaspelayanan_id' => $kelaspelayanan_id]);
        // }

        // if ($penjamin_id){
        //     $result->andWhere(['infotarifrs_v.penjamin_id' => $penjamin_id]);
        // }

        // if ($komponentarif_id){
        //     $result->andWhere(['infotarifrs_v.komponentarif_id' => $komponentarif_id]);
        // }

        // $result->andWhere('infotarifrs_v.tipepaket_id IS NOT NULL');

        // return $result->asArray()->all();
    }

    /**
     *
     * @see Fungsi get data gcs
     * @return array
     *
     */
    public function actionAllowGetDataFisik()
    {
        $request = Yii::$app->request;
        try {
            $cache = Yii::$app->cache;
            $data_gcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_MASTER, Gcs::find()->orderBy(['gcs_nilaimin' => SORT_ASC]));
            $data_metodegcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_METODE, MetodeGcs::find());
            $data_bmi = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI, BodyMassIndex::find());
            $data_tekanandarah = $this->getOrSetCache(DocoConstants::VAR_CACHE_KLASIFIKASI_TEKANANDARAH, KlasifikasiTekananDarah::find());
            $data_bagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_BAGIANTUBUH, BagianTubuh::find());
            $data_getdetailbagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_DETAILBAGIANTUBUH, BagianTubuhDetail::find());
            $data_bmi_anak = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI_ANAK, BodyMassIndexAnak::find());
            $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
            $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
            $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
            $data_listgcs = [];
            $data_detailbagiantubuh = [];

            foreach ($data_metodegcs as $key => $value) {
                if (!$value['metodegcs_id']) {
                    continue;
                }

                $value['nama_and_nilai'] = $value['metodegcs_nama'] . ' - ' . $value['metodegcs_nilai'];
                if ($value['metodegcs_singkatan'] == $gcsindicator_eye) {
                    $data_listgcs['eye'][] = $value;
                } elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal) {
                    $data_listgcs['verbal'][] = $value;
                } elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik) {
                    $data_listgcs['motorik'][] = $value;
                }
            }
            foreach ($data_getdetailbagiantubuh as $k => $v) {
                $data_detailbagiantubuh[$v['bagiantubuh_id']][$v['bagiantubuhdetail_id']] = $v['nama_bagiantubuh'];
            }
            return [
                'data-gcs' => $data_gcs,
                'data-metodegcs' => $data_metodegcs,
                'data-listgcs' => $data_listgcs,
                'data-bmi' => $data_bmi,
                'data-bmi-anak' => $data_bmi_anak,
                'data-tekanandarah' => $data_tekanandarah,
                'data-bagiantubuh' => $data_bagiantubuh,
                'data-detailbagiantubuh' => $data_detailbagiantubuh,
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

    /*
     * @see Fungsi get data pegawai
     * @return array, activeQueryRecords
     *
     */
    public function actionListPegawai()
    {
        try {
            $request = Yii::$app->request;
            $jabatan_id = $request->get('jabatan_id' . null);
            $kelompokpegawai = $request->get('kelompokpegawai_id', []);
            $data = Pegawai::find()->where(['is_deleted' => false]);
            if (!is_null($jabatan_id)) {
                $data->andWhere(['jabatan_id' => $jabatan_id]);
            }
            if (!empty($kelompokpegawai)) {
                $data->andWhere(['IN', 'kelompokpegawai_id', $kelompokpegawai]);
            }
            $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

            return $items;
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

    /**
     *
     * @see Fungsi get data ruangan berdasarkan instalasi
     * @return array, activeQueryRecords
     *
     */
    private function getRuanganInstalasi($instalasi_singkatan = null, $ruangan_id = null)
    {
        $sql = "
            SELECT DISTINCT
                *
            FROM
                ruangan_m
            LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
            WHERE ruangan_m.is_deleted = FALSE
        ";

        if ($instalasi_singkatan) {
            $sql .= " AND instalasi_m.instalasi_singkatan = '" . $instalasi_singkatan . "'";
        }
        if ($ruangan_id) {
            $sql .= " AND ruangan_m.ruangan_id = '" . $ruangan_id . "'";
        }

        $result = Ruangan::findBySql($sql);

        return $result;
    }

    /*
     * @see Fungsi get data obatalkes ruangan
     * @return array, activeQueryRecords
     *
     */
    public function actionListObatAlkes()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id', null);

            $data = InfoStokObatAlkesView::find()->where(['ruangan_id' => $ruangan_id]);
            $items = $data->all();

            return $items;
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

    /**
     * @author Arief
     * @since 2018-04-19 17:08:42
     * @return array list of dokter
     * @desc needs for dropdown
     */
    public function actionListDokterForFilter($ruangan_id)
    {
        $data = DokterV::find()->where(['ruangan_id' => $ruangan_id]);

        $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

        return $items;
    }

    public function actionSetPemulanganPasien()
    {
        $connection = Yii::$app->db;
        $tanggal = DocoHelpers::getTanggalIndonesia();
        $dateNow = date('Y-m-d H:i:s');

        // Filterig Pendaftaran
        $ambilDataPendaftaran = $connection->createCommand("
            SELECT pendaftaran_t.pendaftaran_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id
            FROM pendaftaran_t
            INNER JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
            INNER JOIN anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
            WHERE pendaftaran_t.is_deleted = FALSE
            AND pendaftaran_t.is_active = true
            AND anamnesa_t.keluhan_utama IS NOT NULL
            AND pendaftaran_t.status_periksa = '2'
            GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.ruangan_id
            ORDER BY pendaftaran_t.pendaftaran_id
        ")->queryAll();

        // print_r($ambilDataPendaftaran);die;
        foreach ($ambilDataPendaftaran as $key => $value) {
            //value id pendaftaran
            $id = $value['pendaftaran_id'];
            $ruangan = $value['ruangan_id'];
            $pasien = $value['pasien_id'];
            //save into pasienpulang_t
            $modelPasienPulang = new PasienPulang;
            $modelPasienPulang->pendaftaran_id = $id;
            $modelPasienPulang->carakeluar_id = 1;
            $modelPasienPulang->tglpasienpulang = $dateNow;
            $modelPasienPulang->ruanganakhir_id = $ruangan;
            $modelPasienPulang->pasien_id = $pasien;

            if ($modelPasienPulang->save()) {
                //update status_periksa pendaftaran_t menjadi pulang
                $modelPendaftaran = Pendaftaran::findOne($id);
                $modelPendaftaran['status_periksa'] = 4;
                $modelPendaftaran->save();
            }
        }

        return [
            'message' => 'Pasien Berhasil Dipulangkan'
        ];
    }

    /**
     * @author Rizal Faidin
     * @since 2018-08-22 13:40:02
     * @param
     * @return array $results :
     * @desc
     */
    private function getListLookup($types = null)
    {
        $results = [];
        $lookup = new Lookup;
        $q = $lookup->find()->where(['is_active' => true, 'is_deleted' => false]);

        $lookups = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_ALL, $q, true);

        $returns = [];
        foreach ($lookups as $key => $lookup) {
            if (in_array($lookup['lookup_type'], $types)) {
                $returns[$lookup['lookup_type']][] = $lookup;
            }
        }
        return $returns;
    }

    // DEPRECATED
    // private function getListMaster(array $listRequest)
    // {
    //     $results = [];
    //     foreach ($listRequest as $key=>$request) {
    //         $class = "app\modules\\v1\models\\" . (is_array($request) ? $request[0] : $request);
    //         $model = new $class;
    //         $q = $model->find();
    //         // $q->andWhere(['is_active' => true, 'is_deleted' => false]);
    //         if (is_array($request) && $request[1]) {
    //             $q->andWhere($request[1]);
    //         }
    //         $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
    //     }
    //     return $results;
    // }

    private function getListMaster(array $listRequest)
    {
        foreach ($listRequest as $key => $value) {
            $condition = false;
            if (is_array($value)) {
                $class = "app\modules\\v1\models\\" . $value[0];
                $condition = $value[1];
            } else {
                $class = "app\modules\\v1\models\\" . $value;
            }
            $model = new $class;
            $q = $model->find();
            if ($condition) {
                $q->andWhere($condition);
            }
            $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            // $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
            $results[$key] = $q->asArray()->all();
        }
        return $results;
    }

    //get kamar berdasar ruangan
    private function getKamarRuangan($ruangan_id)
    {
        $model = new KamarRuangan;
        $results = $model->find();
        $results->andWhere(['ruangan_id' => $ruangan_id, 'is_deleted' => false]);
        return $results;
    }

    //get jenis visite
    private function getdataJenisVisite($ruangan_id)
    {
        $model = new InfoTarifRs;
        $results = $model->find()->select(['daftartindakan_id', 'daftartindakan_nama']);
        $results->andWhere(['ruangan_id' => $ruangan_id, 'komponentarif_id' => '6', 'kelompoktindakan_id' => '32']);
        $results->groupBy(['daftartindakan_id', 'daftartindakan_nama']);
        return $results;
    }

    /**
     * @author sunarko
     * @since
     * @param
     * @return
     */
    public function actionGetApi()
    {
        $request = Yii::$app->request;
        try {
            $controller = $request->get('controller', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $instalasi = $request->get('instalasi_nama', null);
            $datatamp = [];
            $listRequest = [];
            $counter = 0;

            if ($controller) {
                if ($controller == 'traVisitDokter') {
                    $listRequest = [
                        'dokter' => [
                            'DokterView', ['ruangan_id' => $ruangan_id],
                        ]
                    ];
                }
                // get data dokter per ruangan
                $data_dokter = $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS)->asArray()->all();
                if ($instalasi == 'Rawat Inap') {
                    $instalasi = DocoConstants::VAR_I_RI;
                }
                $data_ruangan = $this->getRuanganInstalasi($instalasi, $ruangan_id)->asArray()->all();
                $data_kamar = $this->getKamarRuangan($ruangan_id)->asArray()->all();
                $jenis_visite = $this->getdataJenisVisite($ruangan_id)->asArray()->all();

                $master = $this->getListMaster($listRequest);
                $master['doktervisite'] = $data_dokter;
                $master['jenisvisite'] = $jenis_visite;
                $master['kamarruangan'] = $data_kamar;
                $master['ruanganinstalasi'] = $data_ruangan;
                $result = [
                    'master' => $master
                ];
            } else {
                // get all master by request
                $listRequestMaster = [
                    'carabayar' => 'CaraBayar',
                    'penjamin' => 'Penjamin',
                    'kelaspelayanan' => 'KelasPelayanan',
                    'ruangan' => ['Ruangan', ['instalasi_id' => DocoConstants::INST_ID_RI]],
                ];
                // get all lookup by request
                $listRequestLookup = [
                    'kelas_bpjs'
                ];
                $lookup = $this->getListLookup($listRequestLookup);
                $master = $this->getListMaster($listRequestMaster);

                $jenis_kasus_penyakit =  $this->getDataJenisPenyakit()->asArray()->all();
                $arrjenis_kasus_penyakit = ArrayHelper::map($jenis_kasus_penyakit, 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');
                $getListDokter =  $this->getListDokter();
                $arrListDokter = ArrayHelper::map($getListDokter, 'pegawai_id', 'nama_pegawai');

                $result = [
                    'lookup' => $lookup,
                    'master' => $master,
                    'jenis_kasus_penyakit' => $arrjenis_kasus_penyakit,
                    'list_dokter' => $arrListDokter,
                ];
            }

            return $result;
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

    public function getListDokter()
    {
        $request = Yii::$app->request;

        $model = new DokterView;
        $query = $model::find()
            ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI])
            ->orderBy(['nama_pegawai' => SORT_ASC]);

        $results = $query->asArray()->all();
        return $results;
    }

    public function getDataJenisPenyakit()
    {
        $data = JenisKasusPenyakit::find();
        $result = $data->where(['is_deleted' => false])
            ->orderBy(['jeniskasuspenyakit_nama' => SORT_ASC]);

        return $result;
    }

    public function actionListPackAsesmen(){
        //try{
            // get data all diagnosa
            $pendaftaranId = Yii::$app->request->post('pendaftaran_id');
            $gcs = $this->actionAllowGetDataFisik();
            $data_asesmennyeri = $this->getLookupKeperawatanByType('asmen_nyeri');
            $data_kunjungan = !empty($pendaftaranId) ? InfoKunjunganRi::find()->select(['pegawai_id', 'pasien_id', 'nama_pegawai', 'pendaftaran_id'])->where(['pendaftaran_id' => $pendaftaranId])->asArray()->one() : [];

            // $data_diagnosa = $this->getOrSetCache(DocoConstants::VAR_CACHE_DIAGNOSA, $this->getDiagnosa());
            $data_denyutjantung = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('denyut_jantung'), true, 'denyut_jantung');
            // get data diagnosa imunisasi
            $data_diagnosaimunisasi = $this->getOrSetCache(DocoConstants::VAR_CACHE_DIAGNOSA_IMUNISASI, $this->getDiagnosa(true));
            // $data_obatalkes = $this->getOrSetCache(DocoConstants::VAR_CACHE_OBATALKES, $this->getObatAlkes(DocoConstants::JENIS_OBATALKES_OBAT));
            $data_asesmen = Yii::$app->runAction(
                'v1/asesmen-medis/get-asesmen',
                [
                    'pendaftaranId' => $pendaftaranId,
                ]
            );
            // return json_encode($data_asesmen);
            $data_pendaftaran = Pendaftaran::findOne($pendaftaranId);
            $data_riwayat = Yii::$app->runAction(
                'v1/asesmen-medis/get-riwayat',
                [
                    'pasienId' => $data_pendaftaran['pasien_id'],
                    'pendaftaran_id' => $data_pendaftaran['pendaftaran_id'],
                ]
            );


            $data_askep = [];
            $data_askep = AsesmenAwal::find()->Select([
                'additional_data'
            ])->andWhere([
                'pendaftaran_id' => $pendaftaranId
            ])
            ->asArray()->one();
            $body_askep = !empty($data_askep) ? json_decode($data_askep['additional_data'],true) : [];

            $data_askep['tinggi_badan'] = isset($body_askep['tinggi_badan']) ?$body_askep['tinggi_badan'] : 0;
            $data_askep['berat_badan'] = isset($body_askep['berat_badan']) ?$body_askep['berat_badan'] : 0;
            $data_askep['bb_ideal'] = isset($body_askep['bb_ideal']) ?$body_askep['bb_ideal'] : 0;
            $data_askep['imt'] = isset($body_askep['imt']) ?$body_askep['imt'] : 0;
            $data_askep['ket_imt'] = isset($body_askep['ket_imt']) ?$body_askep['ket_imt'] : '';
            $data_askep['skala'] = isset($body_askep['pilih_skala']) && $body_askep['pilih_skala'] == 'dewasa' ? $body_askep['skala_nyeri'] :  isset($body_askep['pilih_skala']) && $body_askep['pilih_skala'] == 'anak' ? $body_askep['skala_nyeri_anak'] : '';
            if(isset($body_askep['tensi'])){
                $explode = explode('/', $body_askep['tensi']);
                $data_askep['td_systolic'] = isset($explode[0]) ? $explode[0] : $body_askep['tensi'];
                $data_askep['td_diastolic'] = isset($explode[1]) ?$explode[1]: '';
            }
            $data_askep['detak_nadi'] = isset($body_askep['detak_nadi']) ?$body_askep['detak_nadi'] : 0;
            $data_askep['suhu_tubuh'] = isset($body_askep['suhu_tubuh']) ?$body_askep['suhu_tubuh'] : 0;
            $data_askep['r_penyakitsekarang'] = isset($body_askep['r_penyakitsaatini']) ?$body_askep['r_penyakitsaatini'] : null;
            $data_askep['obat_diberikan'] = isset($body_askep['r_pengobatan']) ?$body_askep['r_pengobatan'] : null;
            $data_askep['gcs_eye'] = isset($body_askep['gcseye_id']) ? $body_askep['gcseye_id'] : null;
            $data_askep['gcs_verbal'] = isset($body_askep['gcsverbal_id']) ? $body_askep['gcsverbal_id'] : null;
            $data_askep['gcs_motorik'] = isset($body_askep['gcsmotorik_id']) ? $body_askep['gcsmotorik_id'] : null;
            $data_askep['jumlah_gcs'] = isset($body_askep['hasil_gcs']) ? $body_askep['hasil_gcs'] : null;
            $data_askep['keluhan_utama'] = isset($body_askep['keluhan']) ? $body_askep['keluhan'] : null;
            $data_askep['sumber_info'] = isset($body_askep['asesmen_auto']) ? $body_askep['asesmen_auto'] : null;
            $data_askep['sumber_info_lainnya'] = isset($body_askep['asesmen_allo']) ? $body_askep['asesmen_allo'] : null;
            $data_askep['sumber_hubungan'] = isset($body_askep['asesmen_allo_text']) ? $body_askep['asesmen_allo_text'] : null;
            
            $data_hasilpenunjang = [];
            $data_hasilpenunjang['masuklab_id'] = null;
            $data_hasilpenunjang['masukrad_id'] = null;

            $exceptStatus = [
                DocoConstants::ST_P_PEN_BTL,
                DocoConstants::ST_P_PEN_BLM_PRKS
            ];
            $masukPenunjang = InfoPasienPenunjangView::find()
                ->andWhere(['pendaftaran_id' => $pendaftaranId])
                ->andWhere(['NOT IN', 'status_periksa', $exceptStatus])
                ->asArray()->all();

            // return $masukPenunjang;
            if ($masukPenunjang) {
                foreach ($masukPenunjang as $each) {
                    if ($each['instalasi_id'] == DocoConstants::INST_ID_LAB) {
                        $data_hasilpenunjang['masuklab_id'][] = DocoHelpers::encrypt($each['pasienmasukpenunjang_id']);
                    } elseif ($each['instalasi_id'] == DocoConstants::INST_ID_RAD) {
                        $data_hasilpenunjang['masukrad_id'][] = DocoHelpers::encrypt($each['pasienmasukpenunjang_id']);
                    }
                }
            }

            $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');

            $result = [
                // 'data_diagnosa' => $data_diagnosa,
                'data_imunisasi' => $data_diagnosaimunisasi,
                'data_obatalkes' => [],
                'data_kunjungan' => $data_kunjungan,
                'data_asesmennyeri'=>$data_asesmennyeri->asArray()->all(),
                'data_askep'=> $data_askep,
                'data_asesmen' => $data_asesmen['response'],
                'data_riwayat' => $data_riwayat['response'],
                'data_denyutjantung' => $data_denyutjantung,
                'data_hasilpenunjang' => $data_hasilpenunjang,
                'enable_pulang' => $enable_pulang,
            ];
            $result = array_merge($result, $gcs);
        // } catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        //     $result = [];
        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        //     $result = [];
        // }
        return $result;
    }

    public function actionLoopAksi()
    {
        $request = Yii::$app->request;
        $arr = $request->post();
        $data = [];
        try {
            foreach ($arr as $key => $value) {
                if (count($value) > 1) {
                    $data[$key] = $this->$value[0]($value[1]);
                } else {
                    $data[$key] = $this->$value();
                }
            }
        } catch (\Exception $e) {
            $data = [];
        }

        return $data;
    }

    private function lookupKeperawatan($type = null)
    {
        $model = new LookupKeperawatan;
        $query = $model->find();

        if ($type) {
            $query->where(['lookup_type' => $type]);
        }

        return $query->all();
    }

    private function ruanganDokter($id)
    {
        $where = " where 1=1";
        if (!empty($id)) {
            $where = " AND rp.ruangan_id = {$id}";
        }

        $request = Yii::$app->request;
        $query = "SELECT p.pegawai_id,p.nama_pegawai from ruanganpegawai_mp rp
                  INNER JOIN pegawai_m p ON rp.pegawai_id = p.pegawai_id" . $where . " and rp.is_deleted = false order by p.nama_pegawai ASC";
        $data = Yii::$app->db->createCommand($query)->queryAll();
        return ArrayHelper::map($data, 'pegawai_id', 'nama_pegawai');
    }

    public function actionGetRequestDischargePlanning()
    {
        return [
            'assesment' => $this->lookupKeperawatan($type),
            'dokter_ruangan' => $this->ruanganDokter($id),
        ];
    }

    /**
     * author: Rizqi Febian
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupKeperawatanByType($type = null)
    {
        $result = LookupKeperawatan::find();

        if ($type) {
            $result->where(['lookup_type' => $type]);
        }

        return $result;
    }

    public function actionGetDataTd()
    {
        try {
            $result = $this->getOrSetCache(DocoConstants::VAR_H_TD, Sysdia::find());
        } catch (\yii\db\Exception $e) {
            $result = [];
        } catch (\Exception $e) {
            $result = [];
        }
        return $result;
    }

    /**
     * @todo Method untuk mendapatkan data klasifikasi tekanan darah
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKlasifikasiTekananDarah()
    {
        try {
            $data = KlasifikasiTekananDarah::find()->all();
            $result = ArrayHelper::index($data, 'urutan');
        } catch (\yii\db\Exception $e) {
            $result = [];
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * author: Rizqi Febian
     * @see Fungsi get data diagnosa
     * @return array, [id => value]
     *
     */
    public function actionGetDiagnosa()
    {
        return ArrayHelper::map($this->getOrSetCache(DocoConstants::VAR_CACHE_DIAGNOSA, $this->getDiagnosa()), 'diagnosa_id', 'diagnosa_nama');
    }

    /**
     */
    public function actionGetAllDiagnosa()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);


        $diagnosa = Diagnosa::find()
            ->select([
                'diagnosa_id',
                'diagnosa_nama',
                new Expression("CONCAT(diagnosa_kode,' - ',diagnosa_nama) AS nama_diagnosa")
            ]);
        if (!empty($get['keyword'])) {
            $term = $get['keyword'];
            $diagnosa->andWhere(['like', 'LOWER(diagnosa_nama)', $term]);
            $diagnosa->orWhere(['like', 'LOWER(diagnosa_kode)', $term]);
        }
        $diagnosa->andWhere(['is_active' => TRUE, 'is_deleted' => FALSE]);

        return $diagnosa->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionDataKondisiKeluar($carakeluar_id)
    {
        $sql = "select kondisikeluar_id, kondisikeluar_nama, carakeluar_id from kondisikeluar_m where carakeluar_id={$carakeluar_id} and is_deleted = false and is_active = true
            order by kondisikeluar_id asc
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

    private function getJenisVisite($id)
    {
        $where = " where 1=1";
        if (!empty($id)) {
            $where = " AND rp.ruangan_id = {$id}";
        }

        $request = Yii::$app->request;
        $query = "SELECT p.pegawai_id,p.nama_pegawai from ruanganpegawai_mp rp
                  INNER JOIN pegawai_m p ON rp.pegawai_id = p.pegawai_id" . $where . " and rp.is_deleted = false order by p.nama_pegawai ASC";
        $data = Yii::$app->db->createCommand($query)->queryAll();
        return ArrayHelper::map($data, 'pegawai_id', 'nama_pegawai');
    }

    private function getDokterVisite($id)
    {
        $where = " where 1=1";
        if (!empty($id)) {
            $where = " AND rp.ruangan_id = {$id}";
        }

        $request = Yii::$app->request;
        $query = "SELECT p.pegawai_id,p.nama_pegawai from ruanganpegawai_mp rp
                  INNER JOIN pegawai_m p ON rp.pegawai_id = p.pegawai_id" . $where . " and rp.is_deleted = false order by p.nama_pegawai ASC";
        $data = Yii::$app->db->createCommand($query)->queryAll();
        return ArrayHelper::map($data, 'pegawai_id', 'nama_pegawai');
    }

    /**
     * author: Rizal Faidin
     * @return array, [array of data]
     * @desc need for dropdown select2
     */
    public function actionGetListObatAlkes()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        // return json_encode($post['keyword']);
        $result = ObatAlkes::find();

        if (!empty($post['keyword'])) {
            $term = $post['keyword'];
            $result->andWhere(['like', 'LOWER(obatalkes_nama)', $term]);
            $result->orWhere(['like', 'LOWER(obatalkes_kode)', $term]);
        }

        return $result->asArray()->all();
    }

    public function actionGetOneObatAlkes()
    {
        $obatalkes_id = $_GET['id'];

        $result = ObatAlkes::find()
            ->where(['obatalkes_id' => $obatalkes_id])
            ->asArray()->one();
        return $result;
    }



    /**
     * @author Rizal F. <rizal@docotel.com>
     * @since
     * @param int pendaftaran_id
     * @param int pasienadmisi_id
     * @return list rekonsiliasi obat
     */
    public function actionGetListRekonObat()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id');

        $model = RekonsiliasiObatView::find()
            ->andWhere([
                'pendaftaran_id' => $pendaftaran_id,
                'pasienadmisi_id' => $pasienadmisi_id,
            ])
            ->asArray()->all();

        return $model;
    }


    // duplicate from master allow
    // rizal
    public function actionGetLastKodeOa()
    {
        try {
            $sql = "SELECT obatalkes_id as last_number from obatalkes_m order by obatalkes_id desc limit 1";
            $query = Yii::$app->db->createCommand($sql)->queryOne();
            $nomor = 'A001';
            if (isset($query['last_number'])) {
                $last_number = $query['last_number'] + 1;
                $prefix = 'A';
                if (strlen($last_number)  == 1) {
                    $last_number = '00' . $last_number;
                } else if (strlen($last_number) == 2) {
                    $last_number = '0' . $last_number;
                } else if (strlen($last_number) == 3) {
                    $last_number = $last_number;
                }
                $nomor = $prefix . $last_number;
            }
            $query = $nomor;
        } catch (\yii\db\Exception $e) {
            $query = '';
        }
        return $query;
    }



    /**
     * @author Rizal
     * @since 2018-07-25 11:37:16
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc
     */
    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = Ruangan::find();
        // $result->select(['ruangan_id','ruangan_nama']);
        if (isset($get['instalasi_id'])) {
            $result->andWhere(['instalasi_id' => $get['instalasi_id']]);
        }
        $result->andWhere([
            'is_deleted' => false,
            'is_active' => true,
        ]);

        return $result->asArray()->orderBy(['ruangan_urutan' => SORT_ASC])->all();
    }

    public function actionListDokterPerujuk() {
        $request = Yii::$app->request;
        $get = $request->get();

        $keyword = $request->get('q', '');
        $page = $request->get('page', 1);
        $attr = $request->get('attr', []);
        $perpage = 10;

        $query = PegawaiView::find()->select(['pegawai_id', 'nama_pegawai'])
            ->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($keyword)])
            ->andWhere(['kelompokpegawai_namalainnya' => DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS]);
        if(!empty($attr)) {
            $query->andWhere($attr);
        }

        $query->offset(($page-1)*$perpage)->limit($perpage);
        $data = ['data' => $query->distinct()->asArray()->all()];
        return $data;
    }

    /**
     * @author Rizal CLONE FROM PENDAFTARAN
     * @since 2018-07-26 11:37:16
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc
     */
    public function actionGetTarifTindakan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $result = [];
        try {
            // $model = new TarifTindakanLabView;
            $model = new InfoTarifPenunjangView;
            $query = $model::find();
            $query->where(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);
            if (isset($get['ruangan_id']) && !empty($get['ruangan_id'])) {
                $query->andWhere('ruangan_id = ' . $get['ruangan_id']);
            }
            if (isset($get['penjamin_id']) && (!empty($get['penjamin_id']) || $get['penjamin_id'] == 'null')) {
                $query->andWhere('penjamin_id = ' . $get['penjamin_id']);
            }
            if (isset($get['kelaspelayanan_id']) && !empty($get['kelaspelayanan_id'])) {
                $query->andWhere('kelaspelayanan_id = ' . $get['kelaspelayanan_id']);
            }
            if (isset($get['jenispemeriksaanlab_nama']) && !empty($get['jenispemeriksaanlab_nama'])) {
                $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', $get['jenispemeriksaanlab_nama']]);
            }
            $result = new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function actionGetTarifPaket()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $result = [];
        try {
            // $model = new PaketTindakanLabView;
            $model = new InfoTarifPaketPenunjangView;
            $query = $model::find()->with([
                'paketDetailView' => function ($data) {
                    $data->select(['tipepaket_id', 'daftartindakan_nama']);
                }
            ]);
            $query->where(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);
            if (isset($get['ruangan_id']) && !empty($get['ruangan_id'])) {
                $query->andWhere('ruangan_id = ' . $get['ruangan_id']);
            }
            if (isset($get['penjamin_id']) && (!empty($get['penjamin_id']) || $get['penjamin_id'] == 'null')) {
                $query->andWhere('penjamin_id = ' . $get['penjamin_id']);
            }
            if (isset($get['kelaspelayanan_id']) && !empty($get['kelaspelayanan_id'])) {
                $query->andWhere('kelaspelayanan_id = ' . $get['kelaspelayanan_id']);
            }
            if (isset($get['tipepaket_nama']) && !empty($get['tipepaket_nama'])) {
                $query->andWhere(['ILIKE', 'LOWER(tipepaket_nama)', $get['tipepaket_nama']]);
            }
            $result = new ActiveDataProvider([
                'query' => $query->asArray(),
            ]);
        } catch (\yii\db\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function actionListPermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $lookup_type = $request->post('lookup_type');
            $getListDokter = $this->getListDokterKonsul($pendaftaran_id);
            $getLookupByType = $this->GetLookupByType($lookup_type)->asArray()->all();
            $listDokter = ArrayHelper::map($getListDokter, 'pegawai_id', 'nama_pegawai');
            $listJenisKonsul = ArrayHelper::map($getLookupByType, 'lookup_id', 'lookup_value');

            return [
                'listDokter' => $listDokter,
                'listJenisKonsul' => $listJenisKonsul,
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

    private function getListDokterKonsul($pendaftaran_id)
    {
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $model = PermintaanKonsulView::find()
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            // ->andWhere(
            //     '(status_konsul = ' . DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT . ' AND '
            // )
            ->asArray()->all();

        $listDokterAktif = [];
        $listDokterAktif[] = $pegawai_id;
        foreach ($model as $each) {
            if (
                ($each['jenis_konsul'] == DocoConstants::JNS_KNSL_RB && $each['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU) ||
                ($each['jenis_konsul'] == DocoConstants::JNS_KNSL_1X && $each['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && !$each['jawaban_konsul']) ||
                ($each['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT)
            ) {
                $listDokterAktif[] = $each['dokter_id'];
            }
        }

        $data = DokterView::find()
            ->andWhere(['NOT IN', 'pegawai_id', $listDokterAktif])
            ->asArray()->all();


        return $data;
    }


    // public function actionTestGuzzle() {
    //     $restLab = Yii::$app->docoRest->laboratorium;

    //     $request = $restLab->get('allow/hellow');
    //     $response = json_decode($request->getBody(),true);

    //     return $response;
    // }


    /**
     * @author Iqbal Qurahman
     * @since 2018-07-30 11:50:50
     * @param
     * @return array map informasi pasien konsul
     * @desc
     */

    public function QueryInfoPasienKonsulV()
    {
        $model = new PermintaanKonsulView;
        return  $model::find();
    }

    public function actionListFilterPasienKonsul()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruanganId = Yii::$app->jwt->ruangan_id;
        $dokerId = $get['dokter_id'];
        try {
            // get all master by request
            $listRequestMaster = [
                'pegawai' => ['DokterView', ['instalasi_id' => DocoConstants::INST_ID_RI]],
                'carabayar' => 'CaraBayar',
                'kelaspelayanan' => 'KelasPelayanan',
                'ruangan' => ['Ruangan', ['instalasi_id' => DocoConstants::INST_ID_RI]],
            ];
            $master = $this->getListMaster($listRequestMaster);

            // get all lookup by request
            $listRequestLookup = [
                'jenis_konsul',
            ];
            $lookup = $this->getListLookup($listRequestLookup);

            $result = [
                'master' => $master,
                'lookup' => $lookup,
            ];
            return $result;
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

    /**
     * @author Rizal
     * @since
     * @param
     * @return array $results :
     * @desc ONLY DOKTER RANAP
     */
    public function actionGetListDokter()
    {
        $request = Yii::$app->request;

        $model = new DokterView;
        $query = $model::find()
            ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI]);

        if ($q = $request->get('q')) {
            $query = $query->andWhere(['ILIKE', 'nama_pegawai', $q]);
        }
        $results = $query->asArray()->all();

        $return = [];
        foreach ($results as $key => $result) {
            $return[$result['pegawai_id']] = $result;
        }
        return $return;
    }

    public function actionGetListKasusPenyakit()
    {
        $request = Yii::$app->request;

        $model = new KasusPenyakitRuanganView;
        $query = $model::find()
            ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI]);

        if ($q = $request->get('q')) {
            $query = $query->andWhere(['ILIKE', 'jeniskasuspenyakit_nama', $q]);
        }
        $results = $query->asArray()->all();

        $return = [];
        foreach ($results as $key => $result) {
            $return[$result['jeniskasuspenyakit_id']] = $result;
        }
        return $return;
    }

    /*
    public function ListNoPendaftaran($ruangan_id = null)
    {
        $data = DaftarTindakan::find();
        if ($ruangan_id) {
            $data->leftJoin('tindakanruangan_mp', 'tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id');
            $data->where(['tindakanruangan_mp.ruangan_id' => $ruangan_id]);
        }
        $data->orderBy('daftartindakan_nama');
        $items = ArrayHelper::map($data->all(), 'daftartindakan_id', 'daftartindakan_nama');

        return $items;
    }*/

    public function actionTest()
    {

        // $check = Yii::$app->jwt->user;
        // return json_encode($check->pegawai_id);
        return $this->getListDokterKonsul(671);
    }

    /*
    * start
    * Author : iqbal@docotel.com
    * Date : 16-08-2018
    */

    private function getListKelasPelayanan()
    {
        $model = KelasPelayanan::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['kelaspelayanan_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    private function getListCaraKeluar()
    {
        $model = CaraKeluar::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['carakeluar_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    private function getJenisKasusPenyakit()
    {
        $model = JenisKasusPenyakit::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['jeniskasuspenyakit_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    private function getCaraBayar()
    {
        $model = CaraBayar::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['carabayar_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    private function getKondisiKeluar()
    {
        $model = KondisiKeluar::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['kondisikeluar_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    private function getListRuangan($instalasi_id, $ruangan_id)
    {
        $model = Ruangan::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false]);
        if ($instalasi_id) {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
        }
        if ($ruangan_id) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        return $query->asArray()->all();
    }

    public function actionGetFilterPasienPulang()
    {
        try {
            $request = Yii::$app->request;
            $instalasi_id = $request->get('instalasi_id');
            $ruangan_id = $request->get('ruangan_id');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id');
            $penjamin_id = $request->get('penjamin_id');
            $pendaftaran_id = $request->get('pendaftaran_id');
            $pegawai_id = $request->get('pegawai_id');

            $getDataRuangan = $this->getListRuangan($instalasi_id, $ruangan_id);
            $dataRuangan = ArrayHelper::map($getDataRuangan, 'ruangan_nama', 'ruangan_nama'); //ruangan_id

            $getDataJenisKelamin = $this->getLookupByType('jenis_kelamin')->asArray()->all();
            $dataJenisKelamin = ArrayHelper::map($getDataJenisKelamin, 'lookup_name', 'lookup_name'); //lookup_id

            $getDataPenjamin = $this->getOrSetCache(DocoConstants::VAR_CACHE_PENJAMIN, $this->getPenjamin());
            $dataPenjamin = ArrayHelper::map($getDataPenjamin, 'penjamin_nama', 'penjamin_nama'); //penjamin_id

            $getDataDokter = $this->getOrSetCache(DocoConstants::VAR_CACHE_DOKTER_RUANGAN, $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS), true, $ruangan_id);
            $dataDokter = ArrayHelper::map($getDataDokter, 'nama_pegawai', 'nama_pegawai'); //pegawai_id
            asort($dataDokter);

            $getDataCaraKeluar = $this->getListCaraKeluar();
            $dataCaraKeluar = ArrayHelper::map($getDataCaraKeluar, 'carakeluar_nama', 'carakeluar_nama'); //carakeluar_id

            $getListKelasPelayanan = $this->getListKelasPelayanan();
            $dataKelasPelayanan = ArrayHelper::map($getListKelasPelayanan, 'kelaspelayanan_nama', 'kelaspelayanan_nama'); //kelaspelayanan_nama

            $getJenisKasusPenyakit = $this->getJenisKasusPenyakit();
            $dataJenisKasusPenyakit = ArrayHelper::map($getJenisKasusPenyakit, 'jeniskasuspenyakit_nama', 'jeniskasuspenyakit_nama');

            $getCaraBayar = $this->getCaraBayar();
            $dataCaraBayar = ArrayHelper::map($getCaraBayar, 'carabayar_nama', 'carabayar_nama');

            $getKondisiKeluar = $this->getKondisiKeluar();
            $dataKondisiKeluar = ArrayHelper::map($getKondisiKeluar, 'kondisikeluar_nama', 'kondisikeluar_nama');

            return [
                'dataRuangan' => $dataRuangan,
                'dataJenisKelamin' => $dataJenisKelamin,
                'dataPenjamin' => $dataPenjamin,
                'dataDokter' => $dataDokter,
                'dataCaraKeluar' => $dataCaraKeluar,
                'dataKelasPelayanan' => $dataKelasPelayanan,
                'dataJenisKasusPenyakit' => $dataJenisKasusPenyakit,
                'dataCaraBayar' => $dataCaraBayar,
                'dataKondisiKeluar' => $dataKondisiKeluar,
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
    /*
    * end
    * Author : iqbal@docotel.com
    * Date : 16-08-2018
    */


    public function actionGetAllDokter()
    {
        $model = DokterView::find()
            ->select('pegawai_id, nama_pegawai')
            ->orderBy(['nama_pegawai' => SORT_ASC])
            ->groupBy('pegawai_id, nama_pegawai')
            ->asArray()->all();
        return $model;
    }

    /**
     * @author Iqbal Qurahman
     * @since 2018-09-19 11:08:54
     * @param
     * @return
     * @desc Get Data Warna Tempat Tidur
     */
    public function actionGetWarnaTempatTidur()
    {

        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $getWarnaBed = $this->WarnaTempatTidur();

            return [
                'warna_tempat_tidur' => $getWarnaBed,

            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    private function WarnaTempatTidur()
    {
        $model = new WarnaTempatTidur;
        $query = $model->find()
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kamarruangan_jenis' => SORT_ASC]);
        $result = $query->asArray()->all();

        return $result;
    }

    public function actionGetApiPindahKamar()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');

        $data_pasien = InfoPasienRiView::find()
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()->one();

        // list required requested master
        $listRequestMaster = [
            'jeniskasuspenyakit' => 'JenisKasusPenyakit',
            'kelaspelayanan' => 'KelasPelayanan',
            // 'ruangan' => ['Ruangan', ['instalasi_id'=>DocoConstants::INST_ID_RI]],
        ];
        $data_master = $this->getListMaster($listRequestMaster);

        return [
            'data_pasien' => $data_pasien,
            'data_master' => $data_master
        ];
    }



    /**
     * @author Rizal
     * @since 2018-07-25 11:37:16
     * @param int jeniskasuspenyakit_id
     * @return array list of ruangan
     * @desc
     */
    public function actionGetListRuanganByKp()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = KasusPenyakitRuanganView::find();
        $result->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI]);
        if (isset($get['jeniskasuspenyakit_id'])) {
            $result->andWhere(['jeniskasuspenyakit_id' => $get['jeniskasuspenyakit_id']]);
        }
        if (isset($get['jeniskasuspenyakit_nama'])) {
            $result->andWhere(['jeniskasuspenyakit_nama' => $get['jeniskasuspenyakit_nama']]);
        }

        return $result->asArray()->all();
    }

    public function actionGetKamarRuanganByKp()
    {
        $idRuangan = 0;
        $idPenjamin = 0;
        $idKelasPelayanan = 0;
        $where = '';
        $tipeTarif='kamar';
        try {
            $listRuangan = Yii::$app->db->createCommand('
                SELECT DISTINCT
                    ruangan_id,
                    ruangan_nama,
                    kelaspelayanan_nama,
                    jeniskasuspenyakit_nama,
                    kamarruangan_nokamar,
                    kelaspelayanan_id,
                    kamarruangan_id,
                    harga_tariftindakan,
                    klasifikasikamar_id,
                    klasifikasikamar_nama
                FROM tariftotalkamarrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id,:tipe_tarif)
                '.$where.'
            ')
            ->bindParam(':ruangan_id',$idRuangan)
            ->bindParam(':penjamin_id', $idPenjamin)
            ->bindParam(':kelaspelayanan_id', $idKelasPelayanan)
            ->bindParam(':tipe_tarif', $tipeTarif)
            ->queryAll();
        } catch (\Throwable $th) {
            $this->logError($th);
            $listRuangan = [];
        }

        return $listRuangan;
    }

    public function actionDataKamar()
    {
        $data = Yii::$app->db->createCommand("
            SELECT
                ruangan_id,
                ruangan_nama,
                kelaspelayanan_id,
                kelaspelayanan_nama,
                jeniskasuspenyakit_id,
                jeniskasuspenyakit_nama,
                kamarruangan_id,
                kamarruangan_nokamar
            FROM kamar_v
        ")->queryAll();

        return [
            'data'=>$data
        ];
    }

    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        $db = Yii::$app->db;
        $jenis_fleksibel = DocoConstants::VAR_JKF;
        $status_approve = DocoConstants::VAR_STJ;
        $status_dipesan = DocoConstants::VAR_BK;
        $sql = "SELECT jeniskelamin
            FROM infopemesanankamar_v
            WHERE
                kamarruangan_id = {$kamarruangan_id}
            AND kamarruangan_jenis = {$jenis_fleksibel}
            AND (
                statusbooking = '{$status_approve}' OR statusbooking = '{$status_dipesan}'
            )
        ";
        $data = $db->createCommand($sql)->queryOne();

        return !empty($data) ? $data : false;
    }

    public function actionGetDataKamar()
    {
        $request = Yii::$app->request;
        if ($request->get('penjamin_id') && !empty($request->get('penjamin_id'))) {
            $model = new InfoTarifKamarView;
            $payload = $request->get();
            $query = $model::find();
            $query->andWhere(['penjamin_id' => $payload['penjamin_id']]);
            if (isset($payload['jeniskasuspenyakit_id']) && !empty($payload['jeniskasuspenyakit_id']) && strtolower($payload['jeniskasuspenyakit_id']) !== 'semua') {
                $query->andWhere(['jeniskasuspenyakit_id' => $payload['jeniskasuspenyakit_id']]);
            }
            if (isset($payload['kelaspelayanan_id']) && !empty($payload['kelaspelayanan_id']) && strtolower($payload['kelaspelayanan_id']) !== 'semua') {
                $query->andWhere(['kelaspelayanan_id' => $payload['kelaspelayanan_id']]);
            }
            if (isset($payload['ruangan_id']) && !empty($payload['ruangan_id']) && strtolower($payload['ruangan_id']) !== 'semua') {
                $query->andWhere(['ruangan_id' => $payload['ruangan_id']]);
            }
            if (isset($payload['kamar_id']) && !empty($payload['kamar_id']) && strtolower($payload['kamar_id']) !== 'semua') {
                $query->andWhere(['kamarruangan_id' => $payload['kamar_id']]);
            }
            switch ($payload['status_kamar']) {
                case '1':
                    // only empty
                    $query->andWhere(['status_isi' => false]);
                    break;
                case '2':
                    // only assigned
                    $query->andWhere(['status_isi' => true]);
                    break;
            }
            $page = $payload['page'] ? $payload['page'] : 1;

            return [
                'data' => $query->asArray()->all(),
                'list-ruangan' => $query
                    ->select(['ruangan_id', 'ruangan_nama', 'is_akomodasi', 'kelaspelayanan_nama', 'jeniskasuspenyakit_nama', 'kamarruangan_nokamar'])
                    ->distinct()
                    ->limit(11)
                    ->offset(($page - 1) * 10)
                    ->orderBy(['ruangan_nama' => SORT_ASC, 'kamarruangan_nokamar' => SORT_ASC])
                    ->all()
            ];
        } else {
            return [
                'data' => [],
                'list-ruangan' => []
            ];
        }
    }

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetNewDiagnosa()
    {
        try {
            $get = Yii::$app->request->get();
            $q = $get['q'];
            $type = $get['type'];
            $page = Yii::$app->request->get('page', 0);
            $limit = Yii::$app->request->get('limit', 5);
            $offset = Yii::$app->request->get('offset', 0);

            $is_perawat = !empty($get['is_perawat']) ? $get['is_perawat'] : 0;
            $kelompok_diagnosa = DocoConstants::$mapp_kel_diagnosa;

            if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK) {
                $tabularlist_versi = 'ICD X';
            } else if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA) {
                $tabularlist_versi = 'ICD X';
            } else if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                $tabularlist_versi = 'ICD X';
            } else if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA) {
                $tabularlist_versi = 'ICD X';
            } else {
                $tabularlist_versi = 'ICD IX';
            }

            if ($is_perawat) {
                $tabularlist_versi = 'ICD_KEP';
            }

            $model = DiagnosaView::find()->andWhere(['is_deleted' => false, 'is_active' => true]);

            if (isset($q) && $q != '') {
                $model->andWhere(['ilike', 'diagnosa_nama', $q]);
                $model->orWhere(['ilike', 'diagnosa_kode', $q]);
            }

            if (isset($tabularlist_versi) && $tabularlist_versi != '') {
                $model->andWhere(['tabularlist_versi' => $tabularlist_versi]);
            }

            return $model->offset($offset)->limit($limit)->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }


    /**
     * @author Rizal
     * @since 2018-01-24 14:01:16
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc needs for depdrop or dropdown
     */
    public function actionListPenjamin($carabayar_id = null)
    {
        $data = Penjamin::find();
        if ($carabayar_id) {
            $data->where(
                [
                    'is_deleted' => 'f',
                    'is_active' => 't',
                    'carabayar_id' => $carabayar_id
                ]
            );
        }
        $data->orderBy('penjamin_id');

        $items = ArrayHelper::map($data->all(), 'penjamin_id', 'penjamin_nama');
        return $items;
    }

    public function actionGetKalaTigaData()
    {
        $laserisasi = LookupKeperawatan::find()->where(["lookup_type" => "laserisasi"])->all();
        $laserisasi = ArrayHelper::map($laserisasi, "lookupkeperawatan_id", "lookup_name");
        $laserisasi[0] = "Tidak Ada";
        return [
            "laserisasi" => $laserisasi,
        ];
    }

    public function actionCronRekapTt()
    {
        try {
            $connection = Yii::$app->db;
            $sql = "
                SELECT * FROM rekap_kamar_tidur()
            ";

            $result = $connection->createCommand($sql)->queryOne();

            return [
                "msg" => "Rekap Tempat Tidur Berhasil",
                "data" => $result
            ];
        } catch (Exception $e) {
            return [
                "msg" => "Rekap Tempat Tidur Gagal"
            ];
        }
    }

    public function actionCronBorLosToi($tahun = null)
    {

        /**
         * 1. Mencari Seluruh pasien yang masih di rawat
         * 2. Cari Tanggal Awal dan Tanggal Akhir
         * 3. Setelah dapat cari tanggal awal dan tanggal akhir
         */
        try {
            $connection = Yii::$app->db;
            $tahun = !empty($tahun) ? $tahun : date('Y');
            $query = "
                SELECT
                  pasienadmisi_t.pasienadmisi_id,
                    masukkamar_t.tgl_masukkamar,
                    masukkamar_t.tgl_keluarkamar,
                    pasienadmisi_t.tgl_pulang,
                    masukkamar_t.kamarruangan_id,
                    pasienadmisi_t.pendaftaran_id
                FROM masukkamar_t
                JOIN pasienadmisi_t ON masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                WHERE date_part('year', tgl_admisi) = {$tahun}
                ORDER BY  pasienadmisi_t.pasienadmisi_id,masukkamar_t.tgl_masukkamar ASC
            ";

            $dataKamar = $connection->createCommand($query)->queryAll();
            $rekapKamar = [];
            foreach ($dataKamar as $key => $value) {
                $idKamar = $value['kamarruangan_id'];
                $admisi = $value['pasienadmisi_id'];
                $tglMasuk = $value['tgl_masukkamar'];
                $tglKeluar = $value['tgl_keluarkamar'];
                $tglPulang = $value['tgl_pulang'];
                $rekapKamar[$admisi][$idKamar][] = $value;
            }
            return $rekapKamar;
            $sql = "
                SELECT * FROM indikatorrs_r();
            ";

            $result = $connection->createCommand($sql)->queryOne();

            return [
                "msg" => "Cron BOR LOS TOI BTO Berhasil",
                "data" => $result
            ];
        } catch (Exception $e) {
            return [
                "msg" => "Cron BOR LOS TOI BTO Gagal"
            ];
        }
    }

    // Create soap without diag
    public static function actionCreateSoap($ruangan, $pendaftaranId, $pegawaiId, $pasienId, $admisiId = null)
    {
        $strip = '-';
        $valDiag = [];
        $valDiag['text'] = $strip;
        // Try
        try {
            $model = new Cppt;
            $model->scenario = 'soap';
            $model->subject = $strip;
            $model->object = $strip;
            $model->planning = $strip;
            $model->a_diag_utama = $valDiag;
            $model->a_diag_penyerta = $valDiag;
            $model->pendaftaran_id = $pendaftaranId;
            $model->pegawai_id = $pegawaiId;
            $model->pasien_id = $pasienId;
            $model->pasienadmisi_id = $admisiId;
            $explodeRuangan = explode('@#', $ruangan);
            if (count($explodeRuangan) > 1) {
                $model->ruangan_id = (int)$explodeRuangan[0];
                $model->kamarruangan_id = $explodeRuangan[1];
                $model->kamartempattidur_id = $explodeRuangan[2];
                $model->kamar_tempattidur  = $explodeRuangan[3];
            }

            $model->tgl_cppt = date('Y-m-d H:i:s');
            if ($model->validate()) {
                if ($model->save()) {
                    if (isset($payload['is_dokter']) && $payload['is_dokter']) {
                        (new Cppt)->addVisiteDokter($model->attributes);
                    }
                    return $model->cppt_id;
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'CpptForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
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

    private function cekData($pendaftaranId)
    {
        $data = [];
        $data = InfoPasienRiView::find()->select(['pendaftaran_id', 'pasienadmisi_id', 'kelas_ditagihkan_id', 'is_pasientitipan', 'no_pendaftaran', 'is_stoppasientitipan', 'kelaspelayanan_id'])->where(['pendaftaran_id' => $pendaftaranId])->asArray()->one();

        return $data;
    }

    public function actionJenisKonsulInfinity()
    {
        $q = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getKonsul = Lookup::find();
        $getKonsul->select([
            'lookup_id as id',
            "lookup_name as text",
        ]);
        if (!is_null($q)) {
            $getKonsul->where([
                'ILIKE', 'lookup_name', $q
            ]);
        }
        $getKonsul->andWhere(['lookup_type' => 'jenis_konsul', 'is_active' => 't', 'is_deleted' => 'f']);
        $getKonsul->limit($limit);
        $getKonsul->offset((($page - 1) * $limit));
        return $getKonsul->asArray()->all();
    }

    public function actionDokterKonsulInfinity()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $model = PermintaanKonsulView::find()
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()->all();

        $listDokterAktif = [];
        $listDokterAktif[] = $pegawai_id;
        foreach ($model as $each) {
            if (
                ($each['jenis_konsul'] == DocoConstants::JNS_KNSL_RB && $each['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU) ||
                ($each['jenis_konsul'] == DocoConstants::JNS_KNSL_1X && $each['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && !$each['jawaban_konsul']) ||
                ($each['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT)
            ) {
                $listDokterAktif[] = $each['dokter_id'];
            }
        }

        $q = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getDokter = DokterView::find();
        $getDokter->select([
            'distinct(pegawai_id) as id',
            "nama_pegawai as text",
        ]);
        if (!is_null($q)) {
            $getDokter->where([
                'ILIKE', 'nama_pegawai', $q
            ]);
        }
        $getDokter->andWhere(['NOT IN', 'pegawai_id', $listDokterAktif]);
        $getDokter->limit($limit);
        $getDokter->offset((($page - 1) * $limit));
        return $getDokter->asArray()->all();
    }

    public function actionGetKamarTempatTidur()
    {
        $q = Yii::$app->request->get('term', null);
        $statusIsi = Yii::$app->request->get('status_isi', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getKamar = KamarRuanganView::find();
        $getKamar->select([
            'kamartempattidur_id as id',
            "CONCAT(kamarruangan_nokamar, ' - ', no_tempattidur) as text",
            'ruangan_nama'
        ]);
        if (!is_null($q)) {
            $getKamar->where([
                'ILIKE', 'kamarruangan_nokamar', $q
            ]);
            $getKamar->orWhere([
                'ILIKE', 'no_tempattidur', $q
            ]);
        }
        if (!is_null($statusIsi)) {
            $getKamar->andWhere([
                'status_isi' => $statusIsi
            ]);
        }
        $getKamar->limit($limit);
        $getKamar->offset((($page - 1) * $limit));
        return $getKamar->asArray()->all();
    }

    public function actionGetTarifTindakanRi()
    {
        $request = Yii::$app->request;
        $namaJenis = $request->get('jenispemeriksaanlab_nama');
        $namaPemeriksaan = $request->get('daftartindakan_nama');
        $payload = new TarifPayload;
        $payload->attributes = $request->get();

        $isInstalasiFisio = $payload->instalasi_id == $this->constans->actionGetId('instalasi_fisio');

        if (!$payload->validate()) {
            return DocoHelpers::callback(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }

        switch ($payload->instalasi_id) {
            case $this->constans->actionGetId('LAB'):
                $type = 'penunjang';
                $kategori = 'lab';
                break;
            case $this->constans->actionGetId('RAD'):
                $type = 'penunjang';
                $kategori = 'rad';
                break;
            case $this->constans->actionGetId('IBS'):
                $type = 'penunjang';
                $kategori = 'operasi';
                break;
            case $this->constans->actionGetId('instalasi_fisio'):
                $type = 'penunjang';
                $kategori = 'fisio';
                break;
            default:
                $type = 'pelayanan';
                $kategori = null;
                break;
        }

        $model = (new TarifTotalRs([
            'extParam' => [
                $payload->ruangan_id,
                $payload->penjamin_id,
                $payload->kelaspelayanan_id,
                $type
            ]
        ]));
        $query = $model::find();

        if (!empty($kategori)) {
            $query->andWhere([
                'jenis' => $kategori
            ]);
        }

        if (!empty($namaJenis)) {
            $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaJenis)]);
        }

        if (!empty($namaPemeriksaan)) {
            $query->andFilterWhere(
                [
                    'or',
                    ['OR LIKE', 'LOWER(daftartindakan_nama)', strtolower($namaPemeriksaan)],
                    ['OR LIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaPemeriksaan)],
                    ['OR LIKE', 'LOWER(kode)', strtolower($namaPemeriksaan)]
                ]
            );
        }

        $groupingtindakan_penunjang = $this->constans->actionGetAdditional('groupingtindakan_penunjang', true);
        $dataOrder = $query->asArray()->all();
        if ($isInstalasiFisio) {
            $dataOrderFisioterapiNormalized = GetOrderFisioAction::getFromTarifTotalRsFunc($dataOrder, $payload->attributes);
            $dataOrder = $dataOrderFisioterapiNormalized;
        }

        $result = [
            'data' => $dataOrder,
            'groupingtindakan_penunjang' => $groupingtindakan_penunjang,
        ];

        return $result;
    }


    /**
     * This function will return default depo from constants function
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDefaultDepo()
    {
        return [
            'depoId' => $this->constans->actionGetId('apotek_rd')
        ];
    }

    public function actionDoctorList()
    {
        $q = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getDokter = DokterView::find();
        $getDokter->select([
            'distinct(pegawai_id) as id',
            "nama_pegawai as text",
        ]);
        if (!is_null($q)) {
            $getDokter->where([
                'ILIKE', 'nama_pegawai', $q
            ]);
        }
        $getDokter->limit($limit);
        $getDokter->offset((($page - 1) * $limit));
        return $getDokter->asArray()->all();
    }

    public function actionPelayananConfigButton() {
        $data = KonfigPelayanan::find()
            ->where(['instalasi_id' => DocoConstants::INST_ID_RI, 'is_active' => true, 'is_deleted' => false])
            ->orderBy(['konfigpelayanan_id'=>SORT_ASC])
            ->asArray()->all();
        return ['data' => $data];
    }

    /**
     * @Author: Aris (aris.m@docotel.com)
     * @Date:   2021-March-31 16:53
     * get data BMI
     */
    public function actionDataBmi()
    {
        $data_bmi = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI, BodyMassIndex::find());
        return [
            'data-bmi'               => $data_bmi,
        ];
    }

    public function actionGetListDokterRuangan()
    {
        $get = Yii::$app->request->get();
        $model = DokterView::find()
            ->where(['ruangan_id' => $get['ruangan_id']])
            ->asArray()->all();

        return $model;
    }

    public function actionTimeResetSuggestSoap($kode_lookup)
    {
        try {
            $result = LookupTransaksi::find()->select([
                'additional_value'
            ])
            ->where(['kode_transaksi' => $kode_lookup])
            ->asArray()->one();
            $time_reset_rj = json_decode($result['additional_value']);
            return [
                'data' => $time_reset_rj[2],
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

    public function actionGetFisioNonPaketMaksFrekuensi(){
        $maksFrek = LookUpTransaksi::find()
        ->select(['additional_value'])
        ->where(['kode_transaksi' => DocoConstants::LT_MAKS_FREKUENSI_NON_PAKET])
        ->asArray()
        ->one();
        return $maksFrek;
    }

    public function actionGetKonfigSystem()
    {
        $konfigSystem = KonfigSystem::find()->one();
        $orderBedahTanpaTindakan = isset($konfigSystem['order_bedah_tanpa_tindakan']) && !empty($konfigSystem['order_bedah_tanpa_tindakan']) ? $konfigSystem['order_bedah_tanpa_tindakan'] : false;
        $instruksiSoap = isset($konfigSystem['hide_instruksi_soap']) && !empty($konfigSystem['hide_instruksi_soap']) ? $konfigSystem['hide_instruksi_soap'] : false;

        return [
            'order_bedah_tanpa_tindakan' => $orderBedahTanpaTindakan,
            'hide_instruksi_soap' => $instruksiSoap
        ];
    }

    /** 
     * get data untuk di cppt ranap
     * pelayananConfigButton
     * timeResetSuggestSoap
     */
    public function actionGetDataToCppt()
    {
        $request = Yii::$app->request;
        $kodeLookup = $request->get('kode_lookup');
        
        $pelayananConfigButton = $this->actionTimeResetSuggestSoap($kodeLookup);
        return [
            'pelayananConfigButton' => $this->actionPelayananConfigButton(),
            'timeResetSuggestSoap' => ArrayHelper::getValue($pelayananConfigButton, 'data'),
            'konfigSystem' => $this->actionGetKonfigSystem(),
        ];
    }

    public function actionGetLookupTransaksi($kode_transaksi)
    {
        try {
            $result = LookupTransaksi::find()
                ->where(['kode_transaksi' => $kode_transaksi])
                ->asArray()->one();

            return [
                'data' => $result,
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
}
