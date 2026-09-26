<?php
/*
 * @Author: metafiliana
 * @Date: 2018-01-29 13:10:59
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-04-03 12:41:21
 * @Last Modified time: 2018-09-04 11:20:29
 * @Description:
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use GuzzleHttp\Exception\RequestException;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\PelayananHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\ConfigTrait;

use Doco\models\PegawaiView;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\KelompokDiagnosa;
use app\modules\v1\models\RujukanKeluar;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\PasienMorbiditas;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\DokterV;
use app\modules\v1\models\TarifTindakanRuangan;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\KlasifikasiTekananDarah;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\ResepTemp;
use app\modules\v1\models\ResepTempDetailView;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\DiagnosaRuanganView;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\InfoTarifPenunjangView;
use app\modules\v1\models\InfoTarifPaketPenunjangView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\JadwalBukaPoliklinik;
use app\modules\v1\models\Infokonsulpoli;
use yii\db\Expression;
use Doco\components\DocoConstansId;
use app\modules\v1\models\TarifTotalRs;
use app\modules\v1\payload\TarifPayload;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\KonfigPelayanan;
use app\modules\v1\models\KonfigSystem;
use Doco\models\SatuanUnit;
use Doco\models\bpjs\Bpjs;
use Doco\models\AntrianKonsul;
use Doco\models\FgetKetersediaanobatFn;
use app\modules\v1\actions\Allow\GetOrderFisioAction;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Konsulpoli;
use Exception;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = 'app\modules\v1\models\PenjaminPendaftaran';

    protected $_restApotek;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["allow-get-list-data"] = ["GET"];
        $verbs["generate-templete"] = ["GET"];

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }


    public function actionGetDiagnosa($diagnosa_id)
    {

        try {

            $sql = '
            SELECT
                t.*, kd.*
            FROM
                diagnosa_m t
            LEFT JOIN klasifikasidiagnosa_m kd ON kd.klasifikasidiagnosa_id = t.klasifikasidiagnosa_id where t.diagnosa_id = ' . $diagnosa_id . ' ';

            $result = Diagnosa::findBySql($sql)->asArray()->one();

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
            $pegawai_id = $request->get('pegawai_id');

            // get data statusperiksa via lookup
            $data_statusperiksa = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('status_periksa'), true, 'status_periksa');

            // get data pegawai berdasarkan ruangan
            $data_pegawai = $this->getOrSetCache(DocoConstants::VAR_CACHE_PEGAWAIRUANGAN, $this->getPegawaiRuangan($ruangan_id), true, $ruangan_id);

            // get data all penjamin
            $data_penjamin = $this->getOrSetCache(DocoConstants::VAR_CACHE_PENJAMIN, $this->getPenjamin());

            // get data all jabatan
            $data_jabatan = $this->getOrSetCache(DocoConstants::VAR_CACHE_JABATAN, $this->getJabatan());

            // get data all diagnosa
            $data_diagnosa = [];
            // $data_diagnosa = $this->getOrSetCache(DocoConstants::VAR_CACHE_DIAGNOSA, $this->getDiagnosa());

            // get data diagnosa imunisasi
            // $data_diagnosaimunisasi = $this->getOrSetCache(DocoConstants::VAR_CACHE_DIAGNOSA_IMUNISASI, $this->getDiagnosa(true));
            $data_diagnosaimunisasi = $this->getDiagnosa('true')->all();

            $data_diagnosaklasifikasi = [];
            // get data diagnosa klasifikasi
            // $data_diagnosaklasifikasi = $this->getOrSetCache(DocoConstants::VAR_CACHE_DIAGNOSA_KLASIFIKASI, $this->getDiagnosaKlasifikasi());

            // get data all kelompok diagnosa
            $data_kelompokdiagnosa = $this->getOrSetCache(DocoConstants::VAR_CACHE_KELOMPOKDIAGNOSA, $this->getKelompokDiagnosa(['kelompokdiagnosa_id' => [DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA, DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA, DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI, DocoConstants::VAR_KELOMPOK_DIAGNOSA_KERJA]]));

            // get data jadwal poli berdasarkan ruangan
            $data_jadwalpoli = $this->getJadwalPoli($ruangan_id);
            $data_jadwalpoli = $data_jadwalpoli->asArray()->all();

            // get data rujukan keluar
            $data_rujukankeluar = $this->getOrSetCache(DocoConstants::VAR_CACHE_RUJUKANKELUAR, $this->getRujukanKeluar());

            // get data all obatalkes_m
            // $data_obatalkes = $this->getObatAlkes()->all();
            $data_obatalkes = [];
            // get data diagnosa per ruangan
            $data_diagnosaruangan = $this->getDataDiagnosaRuangan($ruangan_id)->asArray()->all();

            // get data tindakan per ruangan
            $data_tindakanruangan = $this->getDataTindakanRuangan($ruangan_id, $kelaspelayanan_id, $penjamin_id, DocoConstants::KOMPONEN_TARIF)->asArray()->all();
            // get data tindakan per paket
            $data_paket = $this->getDataPaket($ruangan_id, $kelaspelayanan_id, $penjamin_id, DocoConstants::KOMPONEN_TARIF)->asArray()->all();

            // get data dokter per ruangan
            $data_dokter = $this->getOrSetCache(DocoConstants::VAR_CACHE_DOKTER_RUANGAN, $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS), true, $ruangan_id);

            // get data perawat per ruangan
            $data_perawat = $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAKEPERAWATAN)->asArray()->all();
            $getDataPerawat =  ArrayHelper::map($data_perawat, "pegawai_id", "nama_pegawai");

            $data_satuantindakan = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('satuan_tindakan'), true, 'satuan_tindakan');

            // get data ruangan apotek
            $data_ruanganapotek = $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A);

            // get data master signa
            $data_signa = $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find());

            // konfig farmasi
            $konfig = $this->getOrSetCache(DocoConstants::VAR_CACHE_KONFIG_FARMASI, KonfigFarmasi::find(), false);

            // count data tindakan & bmhp for print
            $count = RiwayatTindakanView::find()->where(['pendaftaran_id' => $pendaftaran_id])->count();

            $data_permintaan_konsul = Infokonsulpoli::find()
                ->where(['pendaftaran_id' => $pendaftaran_id, 'ruangan_id' => $ruangan_id])
                ->asArray()
                ->one();

            $default_status_approve = $this->constans->actionGetId('status_approve');

            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
                'data-penjamin' => $data_penjamin,
                'data-jabatan' => $data_jabatan,
                // 'data-diagnosa' => $data_diagnosa,
                'data-diagnosa' => [],
                'data-diagnosaimunisasi' => $data_diagnosaimunisasi,
                'data-diagnosaklasifikasi' => $data_diagnosaklasifikasi,
                'data-kelompokdiagnosa' => $data_kelompokdiagnosa,
                'data-jadwalpoli' => $data_jadwalpoli,
                'data-rujukankeluar' => $data_rujukankeluar,
                'data-obatalkes' => $data_obatalkes,
                'data-diagnosaruangan' => $data_diagnosaruangan,
                'data-tindakanruangan' => $data_tindakanruangan,
                'data-paket' => $data_paket,
                'data-dokter' => $data_dokter,
                'data-perawat' => $data_perawat,
                'data-satuantindakan' => $data_satuantindakan,
                'data-ruanganapotek' => $data_ruanganapotek,
                'data-signa' => $data_signa,
                'data-konfigfarmasi' => $konfig,
                'count-riwayat' => $count,
                'data_permintaan_konsul' => $data_permintaan_konsul,
                'default_status_approve' => $default_status_approve
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

    public function actionGetDataPeriksaPasien()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('id_ruangan');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $konsulpoli_id = $request->get('konsulpoli_id');
        $pegawai_id = $request->get('pegawai_id');
        try {
            $data_permintaan_konsul = Infokonsulpoli::find();
            if (!empty($konsulpoli_id)) {
                $konsulpoli_id = PelayananHelpers::decryptId($konsulpoli_id);
                $data_permintaan_konsul = $data_permintaan_konsul->where(['konsulpoli_id' => $konsulpoli_id]);
            } else {
                // untuk case konsul rajal dihari berbeda (karena no pendaftaran baru) dan belum memiliki konsul lainnya dipendaftaran baru
                $data_permintaan_konsul = $data_permintaan_konsul->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $ruangan_id
                ])->orderBy(['konsulpoli_id' => SORT_ASC]);
                if (!empty($pegawai_id)) {
                    $data_permintaan_konsul->andwhere(['pegawai_id' => $pegawai_id]);
                }
            }
            $data_permintaan_konsul = $data_permintaan_konsul->asArray()->one();

            $default_status_approve = $this->constans->actionGetId('status_approve');

            /* get data all penjamin */
            // $data_penjamin = $this->getOrSetCache(DocoConstants::VAR_CACHE_PENJAMIN, $this->getPenjamin());

            /* get data pegawai berdasarkan ruangan */
            // $data_pegawai = $this->getOrSetCache(DocoConstants::VAR_CACHE_PEGAWAIRUANGAN, $this->getPegawaiRuangan($ruangan_id), true, $ruangan_id);

            /* get data statusperiksa via lookup */
            // $data_statusperiksa = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('status_periksa'), true, 'status_periksa');

            return [
                'data-statusperiksa' => [],
                'data-pegawai' => [],
                'data-penjamin' => [],
                'data-diagnosa' => [],
                'data_permintaan_konsul' => $data_permintaan_konsul,
                'default_status_approve' => $default_status_approve
            ];
        } catch (\yii\db\Exception $dbex) {
            Yii::error($dbex);
        } catch (Exception $ex) {
            Yii::error($ex);
        }
        return [
            'data-statusperiksa' => [],
            'data-pegawai' => [],
            'data-penjamin' => [],
            'data-diagnosa' => [],
            'data_permintaan_konsul' => [],
            'default_status_approve' => [],
        ];
    }

    /**
     *
     * @see Fungsi get riwayat diagnosa pasien
     * @return array
     *
     */
    public function actionAllowGetDiagnosaPasien($pasien_id = null)
    {
        $request = Yii::$app->request;
        try {
            $pasien_id = $request->get('pasien_id');

            $data_pasien = PasienMorbiditas::find()
                ->select(['diagnosa_id', 'diagnosa_pasien'])
                ->where(['pasien_id' => $pasien_id])
                ->asArray()->all();

            $return = [];
            foreach ($data_pasien as $key => $value) {
                $diagnosa = json_decode($value['diagnosa_pasien'], true);
                $text = explode(" - ", $diagnosa['text'] . " - ");
                if (!in_array($text[1], $return)) {
                    array_push($return, (string)$text[1]);
                }
                // return $diagnosa;

                // if (!in_array($value['diagnosa_id'], $return)){
                //     array_push($return, (string)$value['diagnosa_id']);
                // }
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
                ruanganpegawai_mp.pegawai_id as pegawai_id,
                pegawai_m.*
            FROM ruanganpegawai_mp
            JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id
            JOIN kelompokpegawai_m ON kelompokpegawai_m.kelompokpegawai_id = pegawai_m.kelompokpegawai_id
            WHERE ruanganpegawai_mp.is_deleted = FALSE
            AND ruanganpegawai_mp.is_active = TRUE
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
    private function getDiagnosa($is_imunisasi = false, $id = null)
    {
        $data = Diagnosa::find()
            ->where([
                'is_deleted' => false,
                'is_active' => true
            ]);

        if ($is_imunisasi) {
            $data->andWhere(['diagnosa_imunisasi' => TRUE]);
        }

        if ($id) {
            $data->andWhere(['diagnosa_id' => $id]);
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
        $diagnosa->andWhere(['is_active' => true, 'is_deleted' => false]);

        return $diagnosa->offset($offset)->limit($limit)->asArray()->all();
    }

    /**
     *
     * @see Fungsi get data kelompokdiagnosa
     * @return array, activeQueryRecords
     *
     */
    private function getKelompokDiagnosa($params = [])
    {
        $data = KelompokDiagnosa::find();
        if (isset($params['kelompokdiagnosa_id'])) {
            $data->andWhere(['kelompokdiagnosa_id' => $params['kelompokdiagnosa_id']]);
        }
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
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        try {
            $ruangan_id = $request->get('jadwalbukapoli_id', null);
            $date = $request->get('tanggal', null);
            $data = [];
            if ($ruangan_id) {
                $date_now = is_null($date) ? DocoHelpers::getTanggalIndonesia(date('Y-m-d')) : DocoHelpers::getTanggalIndonesia(date('Y-m-d', strtotime($date)));
                $mapp_hari = DocoConstants::$look_hari;
                $jam = date('H:i:s');
                $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;

                $result = JadwalDokter::find()
                    ->select([
                        'jadwaldokter_m.pegawai_id',
                        'pegawai_m.nama_pegawai',
                    ])
                    ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = jadwaldokter_m.pegawai_id AND pegawai_m.is_deleted = false AND pegawai_m.is_active = true')
                    ->innerJoin('jadwalbukapoli_m', 'jadwalbukapoli_m.jadwalbukapoli_id = jadwaldokter_m.jadwalbukapoli_id')
                    ->where(['jadwaldokter_m.is_deleted' => FALSE])
                    ->andWhere(['jadwaldokter_m.is_active' => TRUE])
                    ->andwhere(['jadwalbukapoli_m.hari' => $hari])
                    ->andWhere(['jadwalbukapoli_m.ruangan_id' => $ruangan_id]);

                    if ($request->get('with_self_pegawai') == false) {
                        $result = $result->andWhere(['NOT IN', 'pegawai_m.pegawai_id', $pegawai_id]);
                    }

                $result = $result->groupBy('jadwaldokter_m.pegawai_id, pegawai_m.nama_pegawai')
                    ->asArray()->all();
                $data = [
                    'data-dokter' => $result
                ];
            }

            return $data;
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


    public function actionGetJadwal()
    {
        $request = Yii::$app->request;
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        try {
            $params = [];
            $ruangan_id = $request->get('jadwalbukapoli_id', null);
            $startDate = date('Y-m-d 00:00:00');
            $endDate = date('Y-m-d 23:59:59');
            $date = $request->get('tanggal', null);
            $dokter_id = $request->get('dokter_id', null);
            if(isset($date)){
                $startDate = date('Y-m-d 00:00:00', strtotime($date));
                $endDate = date('Y-m-d 23:59:59', strtotime($date));
            }

            $params = [
                'pegawai_id' => $pegawai_id,
                'ruangan_id' => $ruangan_id,
                'date' => $date,
                'dokter_id' => $dokter_id,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'with_self_pegawai' => $request->get('with_self_pegawai'),
            ];
            $result = AntrianKonsul::checkJadwal($params);
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
     * @see Fungsi get obatalkes
     * @return array, activeQueryRecords
     *
     */
    private function getObatAlkes($jenisobatalkes_id = null)
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
                    pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai,
                    1::integer as kuota_tersedia,
                    NULL::text as jadwaldokter_id
                    --kuotadokter_r.kuota_tersedia,
                    --jadwaldokter_m.jadwaldokter_id
                FROM
                    jadwaldokter_m
                JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
                JOIN pegawai_m ON pegawai_m.pegawai_id = jadwaldokter_m.pegawai_id AND pegawai_m.is_active = true AND pegawai_m.is_deleted = false
                --LEFT JOIN kuotadokter_r ON kuotadokter_r.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
                LEFT JOIN lookup_m lu ON lu.lookup_id = jadwaldokter_m.jadwaldokter_hari
                --CROSS JOIN (SELECT '{$jam}'::TIME AS event) sub
                --WHERE jadwalbukapoli_m.hari = '{$hari}' AND jadwaldokter_m.is_deleted = FALSE AND
                --    CASE WHEN jadwaldokter_mulai <= jadwaldokter_tutup THEN jadwaldokter_mulai<= event AND jadwaldokter_tutup >= event
                --    ELSE jadwaldokter_mulai <= event OR jadwaldokter_tutup >= event END

            ";

            if ($ruangan_id) {
                $query .= " WHERE jadwaldokter_m.ruangan_id = :ruangan_id";
                $condition[':ruangan_id'] = $ruangan_id;
            }

            //$query .= " GROUP BY pegawai_m.pegawai_id, pegawai_m.nama_pegawai, kuotadokter_r.kuota_tersedia, jadwaldokter_m.jadwaldokter_id";

            $query .= " GROUP BY pegawai_m.pegawai_id, pegawai_m.nama_pegawai";

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
    private function getDataDiagnosaRuangan($ruangan_id = null, $icd = null)
    {
        $model = DiagnosaRuanganView::find();

        if ($ruangan_id) {
            $model->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($icd) {
            if ($icd == 10) {
                $model->andWhere(['tabularlist_versi' => 'ICD X']);
            } else if ($icd == 9) {
                $model->andWhere(['tabularlist_versi' => 'ICD IX']);
            }
        }

        return $model;
    }

    public function actionGetLookupType($type) {
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
            $result->where(['lookup_type' => $type]);
        }

        return $result;
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
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

    /**
     * @see Fungsi get data info tarif rs
     * @return array, activeQueryRecords
     *
     */
    private function getDataTindakanRuangan($ruangan_id = null, $kelaspelayanan_id = null, $penjamin_id = null, $komponentarif_id = DocoConstants::KOMPONEN_TARIF)
    {
        $result = InfoTarifRs::find();

        if ($ruangan_id) {
            $result->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($kelaspelayanan_id) {
            $result->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
        }

        if ($penjamin_id) {
            $result->andWhere(['penjamin_id' => $penjamin_id]);
        }

        if ($komponentarif_id) {
            $result->andWhere(['komponentarif_id' => $komponentarif_id]);
        }

        $result->andWhere('tipepaket_id IS NULL');

        return $result;
    }

    /**
     * @see Fungsi get data paket
     * @return array
     *
     */
    private function getDataPaket($ruangan_id = null, $kelaspelayanan_id = null, $penjamin_id = null, $komponentarif_id = DocoConstants::KOMPONEN_TARIF)
    {
        $result = InfoTarifRs::find();

        if ($ruangan_id) {
            $result->andWhere(['infotarifrs_v.ruangan_id' => $ruangan_id]);
        }

        if ($kelaspelayanan_id) {
            $result->andWhere(['infotarifrs_v.kelaspelayanan_id' => $kelaspelayanan_id]);
        }

        if ($penjamin_id) {
            $result->andWhere(['infotarifrs_v.penjamin_id' => $penjamin_id]);
        }

        if ($komponentarif_id) {
            $result->andWhere(['infotarifrs_v.komponentarif_id' => $komponentarif_id]);
        }

        $result->andWhere('infotarifrs_v.tipepaket_id IS NOT NULL');

        // Group
        return $result;
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
            $data_gcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_MASTER, Gcs::find());
            $data_metodegcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_METODE, MetodeGcs::find());
            $data_bmi = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI, BodyMassIndex::find());
            $data_tekanandarah = $this->getOrSetCache(DocoConstants::VAR_CACHE_KLASIFIKASI_TEKANANDARAH, KlasifikasiTekananDarah::find());
            $data_bagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_BAGIANTUBUH, BagianTubuh::find());

            $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
            $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
            $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
            $data_listgcs = [];
            if ($data_metodegcs) {
                foreach ($data_metodegcs as $key => $value) {
                    if (!$value['metodegcs_nilai']) {
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
            }

            return [
                'data-gcs' => $data_gcs,
                'data-metodegcs' => $data_metodegcs,
                'data-listgcs' => $data_listgcs,
                'data-bmi' => $data_bmi,
                'data-tekanandarah' => $data_tekanandarah,
                'data-bagiantubuh' => $data_bagiantubuh,
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
            $jabatan_id = $request->get('jabatan_id');

            $data = Pegawai::find()->where(['is_deleted' => 'f', 'jabatan_id' => $jabatan_id]);
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
    private function getRuanganInstalasi($instalasi_singkatan = null)
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
            $q = $request->get('keyword');
            $page = $request->get('page');

            $data = InfoStokObatAlkesFn::find()
                ->where(['ruangan_id' => $ruangan_id])
                ->andWhere(['LIKE', 'LOWER(obatalkes_nama)', strtolower($q)])
                ->offset(($page - 1) * 10)->limit(11)
                ->asArray()->all();

            return ['data' => $data, 'payload' => $request->get()];
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
                $modelPendaftaran['pasienpulang_id'] = $modelPasienPulang->pasienpulang_id;
                $modelPendaftaran->save();
            }
        }

        return [
            'message' => 'Pasien Berhasil Dipulangkan'
        ];
    }

    /**
     * @todo check stok
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer ruangan_id, obatalkes_id
     */
    public function actionCheckStok($ruangan_id = null, $obatalkes_id = null)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT obatalkes_id, qty_tersedia FROM stokobatalkes_r";

        if ($ruangan_id && $obatalkes_id) {
            $sql .=  " WHERE ruangan_id = {$ruangan_id} AND obatalkes_id = {$obatalkes_id}";
        }

        $stok = $connection->createCommand($sql)->queryOne();

        return !empty($stok) ? $stok : [];
    }

    public function actionAllowGetListDataReseptur()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('id_ruangan');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id');
            $penjamin_id = $request->get('penjamin_id');
            $pendaftaran_id = $request->get('pendaftaran_id');
            $pegawai_id = $request->get('pegawai_id');

            // get data all obatalkes_m
            $getDiagnosa = PasienMorbiditas::find()->select([
                'diagnosa_pasien'
            ])->where([
                'pendaftaran_id' => $pendaftaran_id,
                'kelompokdiagnosa_id' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA
            ])->asArray()->one();
            $diagnosa = isset($getDiagnosa['diagnosa_pasien']) ? $getDiagnosa['diagnosa_pasien'] : null;

            $getPendaftaran = Pendaftaran::find()->select([
                'status_periksa'
            ])->asArray()->one();
            // $data_obatalkes = $this->getObatAlkes()->all();

            // get data master signa
            $data_signa = $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find());

            // get data ruangan apotek
            $data_ruanganapotek = $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A);

            // konfig farmasi
            $konfig = $this->getOrSetCache(DocoConstants::VAR_CACHE_KONFIG_FARMASI, KonfigFarmasi::find(), false);

            // get data template
            $data_template = ResepTemp::find()->select([
                'reseptemp_id',
                'reseptemp_nama'
            ])->where(['dokter_id' => $pegawai_id])->asArray()->all();

            $default_depo = $this->constans->actionGetId('apotek_rj');

            return [
                'data-diagnosa' => $diagnosa,
                'data-ruanganapotek' => $data_ruanganapotek,
                'data-signa' => $data_signa,
                'data-konfigfarmasi' => $konfig,
                'data-template' => $data_template,
                'data-pendaftaran' => $getPendaftaran,
                'default_depo' => $default_depo,
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

    public function actionGenerateTemplate()
    {
        // try {
            $request = Yii::$app->request;
            $jwt = Yii::$app->jwt;
            $temp_id = $request->get('id');
            $ruangan_depo_id = $request->get('ruangan_depo_id');
            $penjamin_id = $request->get('penjamin_id');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id');

            $model = DynamicModel::validateData([
                'id' => $temp_id,
                'ruangan_depo_id' => $ruangan_depo_id,
                'penjamin_id' => $penjamin_id,
            ], [
                [['id', 'ruangan_depo_id', 'penjamin_id'], 'integer'],
                [['id', 'ruangan_depo_id', 'penjamin_id'], 'required']
            ]);

            if ($model->hasErrors()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }

            $data_template = ResepTempDetailView::find()
                ->where(['reseptemp_id' => $temp_id])->asArray()->all();
            
            $listOa = ArrayHelper::getColumn($data_template, 'obatalkes_id');
            $list_signa = ArrayHelper::getColumn($data_template, 'signa_id');

            $dataObat = [];
            $data_signa = SignaObat::find()->where(['in', 'signa_id', $list_signa])->asArray()->all();
            try {
                $response = Yii::$app->docoRest->apotek->get('allow/get-multiple-stok-obat', [
                    'query' => [
                        'listobat' => $listOa,
                        'ruangan_id' => $ruangan_depo_id,
                        'penjamin_id' => $penjamin_id,
                        'kelaspelayanan_id' => $kelaspelayanan_id,
                    ]
                ]);

                $response = json_decode($response->getBody(), true);
                
                $dataObat = isset($response['response']['data']) ? ArrayHelper::index($response['response']['data'], 'obatalkes_id') : [];
            } catch (RequestException $e) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Terjadi kesalahan pada Service'
                ]);
            }
            $temp_data = [];
            $ruangan = Yii::$app->jwt->ruangan_id;
            Yii::error($data_template);
            $get_stok = $this->getStokObatAlkesByDepoAndId($ruangan_depo_id, ArrayHelper::getColumn($data_template, 'obatalkes_id'));
            foreach ($data_template as $key => $value) {
                $dataInfoObatAlkes = isset($dataObat[$value['obatalkes_id']]) ? $dataObat[$value['obatalkes_id']] : [
                    'hargaygdipakai' => 0,
                    'harganetto' => 0,
                ];
                $signa = array_values(array_filter($data_signa, function ($var) use ($value) {
                        return ($var['signa_id'] == $value['signa_id']);
                    }));

                $hargasatuan = ($dataInfoObatAlkes['hargaygdipakai'] * $value['nilai_konversi']);
                $additional_data = json_decode($value['additional_data'], true);
                $get_stok_obatalkes_index = (isset($value['obatalkes_id']) ? $value['obatalkes_id'] : '').'.qty_tersedia';
                if($value['racikan_singkatan'] == 'NR') {
                    $temp_data[] = [
                        'additional_data' => json_encode([
                            'satuaninput_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                            'satuan_input' => isset($value['satuanunit_nama']) ? $value['satuan_input'] : '',
                            'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : null,
                            'satuan_konversi' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : '',
                            'harga_reseptur' => isset($hargasatuan) ? $hargasatuan : 0,
                            'nilai_konversi' => isset($value['qty_konversi']) ? $value['qty_konversi'] : 1,
                            'is_kronis' => isset($additional_data['is_kronis']) ? $additional_data['is_kronis'] : false,
                        ]),
                        'detail_type' => isset($additional_data['detail_type']) ? $additional_data['detail_type'] : ($value['racikan_singkatan'] == 'NR' ? 'non_racikan' : 'racikan_detail' ),
                        'etiket' => isset($additional_data['etiket']) ? $additional_data['etiket'] : '',
                        'harganetto_reseptur' => isset($dataInfoObatAlkes['harganetto']) ? $dataInfoObatAlkes['harganetto'] : 0,
                        'hargasatuan_reseptur' => $hargasatuan,
                        'obatalkes_id' => isset($value['obatalkes_id']) ? $value['obatalkes_id'] : null,
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'qty_konversi' => isset($value['qty_konversi']) ? $value['qty_konversi'] : 1,
                        'qty_reseptur' => $value['qty'],
                        'racikan_id' => $value['racikan_singkatan'],
                        'rke' =>  $value['rke'],
                        'satuaninput_id' => isset($value['satuaninput_id']) ? $value['satuaninput_id'] : null,
                        'satuaninput_text' => isset($value['satuan_input']) ? $value['satuan_input'] : $value['satuan_input'],
                        'satuankecil_id' => isset($additional_data['satuankecil_id']) ? $additional_data['satuankecil_id'] : $value['satuaninput_id'],
                        'satuankecil_text' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : $value['satuan_input'],
                        'signa'=> isset($value['signa']) ? $value['signa'] : '-',
                        'signa_id'=> isset($value['signa_id']) ? $value['signa_id'] : null,
                        'is_kronis' => isset($value['is_kronis']) ? $value['is_kronis'] : false,
                        'kebutuhan' => ArrayHelper::getValue($additional_data, 'kebutuhan'),
                        'hari' => ArrayHelper::getValue($additional_data, 'hari'),
                        'qty_obat' => ArrayHelper::getValue($signa, '0.qty_obat'),
                        'iterasi' => ArrayHelper::getValue($signa, '0.iterasi'),
                        'qty_tersedia' => ArrayHelper::getValue($get_stok, $get_stok_obatalkes_index, null) // get stok by array key obatalkes_id
                    ];
                } else if(isset($additional_data['detail_type']) && $additional_data['detail_type'] == 'racikan_freetext') {
                    $temp_data[] = [
                        'detail_type' => isset($additional_data['detail_type']) ? $additional_data['detail_type'] : ($value['racikan_singkatan'] == 'NR' ? 'non_racikan' : 'racikan_detail' ),
                        'racikan_id' => $value['racikan_singkatan'],
                        'racikan_text' => isset($additional_data['racikan_text']) ? $additional_data['racikan_text'] : 1,
                        'rke' =>  $value['rke'],
                    ];
                } else {
                    $temp_data[] = [
                        'additional_data' => json_encode([
                            'satuaninput_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                            'satuan_input' => isset($value['satuanunit_nama']) ? $value['satuanunit_nama'] : '',
                            'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : null,
                            'satuan_konversi' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : '',
                            'harga_reseptur' => isset($hargasatuan) ? $hargasatuan : 0,
                            'nilai_konversi' => isset($value['qty_konversi']) ? $value['qty_konversi'] : 1,
                            'harga_konversi' => (isset($dataInfoObatAlkes['harganetto']) ? $dataInfoObatAlkes['harganetto'] : 0) * $value['qty'],
                            'is_kronis' => isset($additional_data['is_kronis']) ? $additional_data['is_kronis'] : false,
                        ]),
                        'detail_type' => isset($additional_data['detail_type']) ? $additional_data['detail_type'] : ($value['racikan_singkatan'] == 'NR' ? 'non_racikan' : 'racikan_detail' ),
                        'etiket' => isset($additional_data['etiket']) ? $additional_data['etiket'] : '',
                        'hargajual_reseptur' => $hargasatuan,
                        'harganetto_reseptur' => isset($dataInfoObatAlkes['harganetto']) ? $dataInfoObatAlkes['harganetto'] : 0,
                        'hargasatuan_reseptur' => $hargasatuan,
                        'nama_racikan' => isset($additional_data['nama_racikan']) ? $additional_data['nama_racikan'] : '-',
                        'obatalkes_id' => isset($value['obatalkes_id']) ? $value['obatalkes_id'] : null,
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'qty_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 1,
                        'qty_racikan' => isset($additional_data['qty_racikan']) ? $additional_data['qty_racikan'] : 0,
                        'qty_reseptur' => $value['qty'],
                        'racikan_id' => $value['racikan_singkatan'],
                        'r' => isset($additional_data['r']) ? $additional_data['r'] : 'r',
                        'rke' =>  $value['rke'],
                        'satuan_besar' => isset($additional_data['satuan_besar']) ? $additional_data['satuan_besar'] : '-',
                        'satuan_nama' => isset($additional_data['satuan_nama']) ? $additional_data['satuan_nama'] : '-',
                        'satuan_racikan_id' => isset($additional_data['satuan_racikan_id']) ? $additional_data['satuan_racikan_id'] : $value['satuankecil_id'],
                        'satuan_racikan_nama' => isset($additional_data['satuan_racikan_nama']) ? $additional_data['satuan_racikan_nama'] : $value['satuanunit_nama'],
                        'satuanbesar_id' => isset($additional_data['satuanbesar_id']) ? $additional_data['satuanbesar_id'] : null,
                        'satuaninput_nama' => isset($additional_data['satuaninput_nama']) ? $additional_data['satuaninput_nama'] : '-',
                        'satuaninput_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                        'satuaninput_text' => isset($value['satuanunit_nama']) ? $value['satuanunit_nama'] : $value['satuan_input'],
                        'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                        'satuankecil_text' => isset($value['satuanunit_nama']) ? $value['satuanunit_nama'] : $value['satuan_input'],
                        'signa'=> isset($value['signa']) ? $value['signa'] : '-',
                        'signa_id'=> isset($value['signa_id']) ? $value['signa_id'] : null,
                        'is_kronis' => isset($value['is_kronis']) ? $value['is_kronis'] : false,
                        'kebutuhan' => ArrayHelper::getValue($additional_data, 'kebutuhan'),
                        'hari' => ArrayHelper::getValue($additional_data, 'hari'),
                        'qty_obat' => ArrayHelper::getValue($signa, '0.qty_obat'),
                        'iterasi' => ArrayHelper::getValue($signa, '0.iterasi'),
                        'qty_tersedia' => ArrayHelper::getValue($get_stok, $get_stok_obatalkes_index, null) // get stok by array key obatalkes_id
                    ];
                }

            }

            return $temp_data;
        // } catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    /*
    * start
    * Author : iqbal@docotel.com
    * Date : 16-08-2018
    */

    private function getListCaraKeluar()
    {
        $model = CaraKeluar::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false]);
        return $query->asArray()->all();
    }

    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $instalasi_id = $request->get('instalasi_id');
        return $this->getListRuangan($instalasi_id);
    }

    private function getListRuangan($instalasi_id, $ruangan_id = null)
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

        return $query->asArray()->orderBy(['ruangan_urutan' => SORT_ASC])->all();
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

            $getDataDokter = $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS);
            $dataDokter = ArrayHelper::map($getDataDokter->asArray()->all(), 'nama_pegawai', 'nama_pegawai');
            $getDataCaraKeluar = $this->getListCaraKeluar();
            $dataCaraKeluar = ArrayHelper::map($getDataCaraKeluar, 'carakeluar_nama', 'carakeluar_nama'); //carakeluar_id

            $getCaraBayar = $this->getCaraBayar();
            $dataCaraBayar = ArrayHelper::map($getCaraBayar, 'carabayar_nama', 'carabayar_nama');

            return [
                'dataRuangan' => $dataRuangan,
                'dataJenisKelamin' => $dataJenisKelamin,
                'dataPenjamin' => $dataPenjamin,
                'dataDokter' => $dataDokter,
                'dataCaraKeluar' => $dataCaraKeluar,
                'dataCaraBayar' => $dataCaraBayar,
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

    public function actionGetApiPeriksaPenunjang()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        // $instalasi_id = $request->get('instalasi_id');

        // $data_pasien = $this->getDataPasien($pendaftaran_id)->asArray()->one();
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $is_dokter = $this->getDokter($pegawai_id) ? true : false;
        if (!$is_dokter) {
            $getPegawaiId = Pendaftaran::find()->select(['pegawai_id'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            $pegawai_id = $getPegawaiId['pegawai_id'];
        }
        $data_pegawai = $this->getDataPegawai($pegawai_id)->asArray()->one();
        $listInstalasiPenunjang = $this->getListInstalasiPenunjang();

        return [
            'data_instalasi' => $listInstalasiPenunjang,
            'data_pegawai' => $data_pegawai,
            'is_dokter' => $is_dokter,
        ];
    }

    private function getDokter($pegawai_id)
    {
        return DokterV::find()->andWhere(['pegawai_id' => $pegawai_id])->one();
    }

    // rizal
    // Get list instalasi penunjang
    private function getListInstalasiPenunjang()
    {
        try {
            $model = Instalasi::find()
                ->andWhere(['is_penunjang' => true])
                // ->andWhere(['in', 'instalasi_id', [, 5]])
                ->orderBy(['instalasi_id' => SORT_ASC])
                ->asArray()->all();
            return $model;
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
     * @see Fungsi get data pasienpendaftaran pendaftaran_t
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     * MODIFIED BY Rizal
     */
    private function getDataPasien($id = null)
    {
        $condition = [];
        $model = InfoKunjunganRajal::find();
        // filter
        if ($id) {
            $model->andWhere(['pendaftaran_id' => $id]);
        }

        return $model;
    }

    private function getDataPegawai($id = null)
    {
        $condition = [];
        $model = Pegawai::find();
        // filter
        if ($id) {
            $model->andWhere(['pegawai_id' => $id]);
        }

        return $model;
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

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author ardi
     */
    public function actionGetNewDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $q = $request->get('q', '-');
            $type = $request->get('type', DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK);
            $page = $request->get('page', 0);
            $limit = $request->get('limit', 5);
            $offset = $request->get('offset', 0);
            // get kelompok pegawai id
            $employeeRecord = Pegawai::find(true)->select(['pegawai_id', 'kelompokpegawai_id'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->asArray()->one();
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
            if ($employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
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
     * @todo Method untuk mendapatkan data klasifikasi tekanan darah
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @update iqbal@docotel.com
     * @desc cloning dari allow ranap
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

    public function actionListPackAsesmen($pendaftaran_id = null)
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $ids = isset($pendaftaranId) ? $pendaftaranId : $pendaftaran_id;

            $data_pendaftaran = Pendaftaran::findOne($ids);
            $data_pasien = Pasien::findOne($data_pendaftaran['pasien_id']);
            $get_data_riwayat = Yii::$app->runAction(
                'v1/asesmen/get-riwayat',
                [
                    'no_rekam_medik' => $data_pasien['no_rekam_medik'],
                    'pendaftaran_id' => $data_pendaftaran['pendaftaran_id'],
                ]
            );
            $dataRiwayat = $get_data_riwayat['response'];
            $riwayatPenyakitDahulu = [];
            $riwayatPenyakitKeluarga = [];
            $riwayatImunisasi = [];
            $riwayatMakanan = [];
            $riwayatKelahiran = [];
            $riwayatAlergiObat = [];
            $riwayatStrImunisasi = [];
            if (!empty($dataRiwayat)) {
                foreach ($dataRiwayat as $key => $value) {
                    if (isset($value['riwayat_penyakitterdahulu']) && $value['riwayat_penyakitterdahulu'] != '') {
                        $rPT = json_decode($value['riwayat_penyakitterdahulu']);
                        if (!empty($rPT)) {
                            for ($i = 0; $i < count($rPT); $i++) {
                                if (!in_array($rPT[$i], $riwayatPenyakitDahulu)) {
                                    $riwayatPenyakitDahulu[] = $rPT[$i];
                                }
                            }
                        }
                    }

                    if (isset($value['riwayat_penyakitkeluarga']) && $value['riwayat_penyakitkeluarga'] != '') {
                        $rPK = json_decode($value['riwayat_penyakitkeluarga']);
                        if (!empty($rPK)) {
                            for ($i = 0; $i < count($rPK); $i++) {
                                if (!in_array($rPK[$i], $riwayatPenyakitKeluarga)) {
                                    $riwayatPenyakitKeluarga[] = $rPK[$i];
                                }
                            }
                        }
                    }




                    if (isset($value['riwayat_makanan']) && $value['riwayat_makanan'] != '') {
                        $rMakan = $value['riwayat_makanan'];
                        $exploderMakan = explode(',', $rMakan);
                        for ($i = 0; $i < count($exploderMakan); $i++) {
                            if (!in_array($exploderMakan[$i], $riwayatMakanan)) {
                                $riwayatMakanan[] = $exploderMakan[$i];
                            }
                        }
                    }

                    if (isset($value['riwayat_kelahiran']) && $value['riwayat_kelahiran'] != '') {
                        $rKelahiran = $value['riwayat_kelahiran'];
                        $riwayatKelahiran[] = $rKelahiran;
                    }

                    if (isset($value['riwayat_alergiobat']) && $value['riwayat_alergiobat'] != '') {
                        $explodeAlergiObat = explode(',', $value['riwayat_alergiobat']);
                        for ($i = 0; $i < count($explodeAlergiObat); $i++) {
                            if (!in_array($explodeAlergiObat[$i], $riwayatAlergiObat)) {
                                $valAlergiObat = str_replace('"', '', $explodeAlergiObat[$i]);
                                $riwayatAlergiObat[] = $valAlergiObat;
                            }
                        }
                    }

                    if (isset($value['riwayat_imunisasi']) && $value['riwayat_imunisasi'] != '') {
                        $intImunisasi = [];
                        foreach (json_decode($value['riwayat_imunisasi']) as $key => $value) {
                            if (!filter_var($value, FILTER_VALIDATE_INT) === false) {
                                $intImunisasi[] = $value;
                            } else {
                                $riwayatStrImunisasi[] = $value;
                            }
                        }
                        if (!empty($intImunisasi)) {
                            for ($i = 0; $i < count($intImunisasi); $i++) {
                                if (isset($intImunisasi[$i])) {
                                    $diagnosaImunisasi = Diagnosa::find()->where(['diagnosa_id' => $intImunisasi[$i]])->asArray()->one();
                                    $riwayatImunisasi[] = $diagnosaImunisasi['diagnosa_nama'];
                                    array_push($riwayatStrImunisasi, $diagnosaImunisasi['diagnosa_nama']);
                                }
                            }
                        }
                    }
                }
            }
            $data_riwayat = [
                'riwayat_penyakitDahulu' => $riwayatPenyakitDahulu,
                'riwayat_penyakitKeluarga' => $riwayatPenyakitKeluarga,
                'riwayat_imunisasi' => $riwayatStrImunisasi,
                'riwayat_makanan' => $riwayatMakanan,
                'riwayat_kelahiran' => $riwayatKelahiran,
                'riwayat_alergiobat' => $riwayatAlergiObat,
            ];
            $result = [
                'data_riwayat' => $data_riwayat,
            ];

            return $result;
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @Author: Budi (budi@docotel.com)
     * @Date:   2019-09-10 16:53
     * get data depdrop satuan besar
     */
    public function actionListSatuanBesar()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $data = SatuanKonversiView::find()->where(['jenis' => 'obat', 'obatalkes_id' => $obatalkes_id,'is_active'=>'t']);
            $items = $data->asArray()->all();

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
     * @Author: Budi (budi@docotel.com)
     * @Date:   2019-09-10 16:53
     * get data obat
     */
    public function actionGetDataObat()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id', null);
            $obatalkes_id = $request->get('obatalkes_id', null);

            $data = InfoObatAlkesView::find()->where(['obatalkes_id' => $obatalkes_id]);
            $items = $data->asArray()->one();

            $dataStok = StokObatAlkesR::find()->where([
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id
            ]);

            $stok = $dataStok->asArray()->one();

            return [
                'items' => $items,
                'stok' => $stok,
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

    public function actionGetKonversi()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $satuanbesar_id = $request->get('satuanbesar_id', null);
            $data = SatuanKonversiView::find()
                ->where([
                    'jenis' => 'obat',
                    'obatalkes_id' => $obatalkes_id,
                    'satuanbesar_id' => $satuanbesar_id
                ]);

            $items = $data->asArray()->one();

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

    public function actionGetJadwalPoli()
    {
        $request = Yii::$app->request;
        try {
            $ruangan_id = $request->get('ruangan_id', null);

            $term = $request->get('term');
            $page = $request->get('page', 0);
            $limit = $request->get('limit', 5);
            $offset = $request->get('offset', 0);
            $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
            $mapp_hari = DocoConstants::$look_hari;
            $jam = date('H:i:s');
            $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;

            $whereTerm = '';
            if (!empty($term)) {
                $whereTerm = " AND r.ruangan_nama ILIKE '%" . $term . "%' ";
            }

            $sql = "
            SELECT
                t.jadwalbukapoli_id, t.ruangan_id, t.hari, t.jam_mulai, t.jam_tutup, r.ruangan_nama
            FROM
                jadwalbukapoli_m t
            LEFT JOIN ruangan_m r ON r.ruangan_id = t.ruangan_id
            LEFT JOIN lookup_m lu ON lu.lookup_id = t.hari
            CROSS JOIN (SELECT '{$jam}'::TIME AS event) sub
            WHERE t.is_deleted = FALSE AND lu.lookup_id = {$hari} {$whereTerm} AND t.ruangan_id != {$ruangan_id} AND
                CASE WHEN jam_mulai <= jam_tutup THEN jam_mulai<= event AND jam_tutup >= event
                ELSE jam_mulai <= event OR jam_tutup >= event END OFFSET {$offset} LIMIT {$limit}";

            $result = JadwalPoliklinik::findBySql($sql);

            return $result->asArray()->all();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionGetTarifTindakanRj()
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

    public function actionGetKamarTempatTidur()
    {
        $q = Yii::$app->request->get('term', null);
        $statusIsi = Yii::$app->request->get('status_isi', null);
        $kamarruangan_jenis = Yii::$app->request->get('kamarruangan_jenis', null);
        $kamar_icu = Yii::$app->request->get('kamar_icu', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getKamar = KamarRuanganView::find();
        $getKamar->select([
            'kamartempattidur_id as id',
            "CONCAT(kamarruangan_nokamar, ' - ', no_tempattidur) as text",
            'ruangan_nama',
            'ruangan_id'
        ]);
        if (!empty($q)) {
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
        if (!empty($kamarruangan_jenis)) {
            $getKamar->andWhere([
                'kamarruangan_jenis' => $kamarruangan_jenis
            ]);
        }else if(!is_null($kamar_icu) && strtolower($kamar_icu) == 'true'){
            $getKamar->andWhere([
                'IS NOT', 'klasifikasi_ruangan_id', null
            ]);
        }

        $getKamar->limit($limit);
        $getKamar->offset((($page - 1) * $limit));
        return $getKamar->asArray()->all();
    }

    public function actionPelayananConfigButton()
    {
        $data = KonfigPelayanan::find()
            ->where(['instalasi_id' => DocoConstants::INST_ID_RJ, 'is_active' => true, 'is_deleted' => false])
            ->orderBy(['konfigpelayanan_id' => SORT_ASC])
            ->asArray()->all();
        return ['data' => $data];
    }

    public function actionGetListSigna()
    {
        $request = Yii::$app->request;
        $limit = $request->get('per-page', 30);
        $page = $request->get('page', 1);
        $q = $request->get('q', null);

        $model = (new SignaObat)->find()->select([
            'signa_id',
            'signa_nama',
            'signa_namalainnya',
            'qty_obat',
            'iterasi',
            'signa_kode',
            'alias'
        ]);

        if (!is_null($q) || !empty($q)) {
            $model->andWhere([
                'or',
                ['ILIKE', 'signa_nama', $q],
                ['ILIKE', 'signa_kode', $q],
                ['ILIKE', 'alias', $q],
            ]);
        }

        $model->orderBy('signa_nama', 'asc')->offset($page - 1)->limit($limit);

        return [
            'signa' => $page == 1 && !empty($q) ? array_merge([[
                'signa_id' => $q,
                'signa_nama' => $q,
                'signa_namalainnya' => $q,
                'qty_obat' => null,
                'iterasi' => null,
                'signa_kode' => null
            ]], $model->asArray()->all()) : $model->asArray()->all(),
            'limit' => $limit
        ];
    }

    public function actionGetMasterUnit()
    {
        $request = Yii::$app->request;
        $limit = $request->get('per-page', 30);
        $page = $request->get('page', 1);
        $q = $request->get('q', null);
        $allowEmpty = $request->get('allowEmpty', false);
        $allowEmpty = !is_bool($allowEmpty) ? ( $allowEmpty =='false' ? false : true ) : $allowEmpty;

        $model = (new SatuanUnit)->find()->select([
            'satuanunit_id',
            'satuanunit_nama',
            'satuan_jenis',
            'satuanunit_singkatan',
            'satuanunit_namalain'
        ]);

        if (!is_null($q) || !empty($q)) {
            $model->andWhere(
                ['ILIKE', 'satuanunit_nama', $q]
            );
        }

        $offset = ($page - 1) * $limit;
        $model->offset($offset)->limit($limit);

        return [
            'satuan' => $page == 1 && !empty($q) && ($allowEmpty == FALSE) ? array_merge([[
                'satuanunit_id' => $q,
                'satuanunit_nama' => $q,
                'satuanunit_namalain' => $q,
            ]], $model->asArray()->all()) : $model->asArray()->all(),
            'limit' => $limit
        ];
    }

    public function actionCronPemulanganPasien()
    {
        $minutesLimit = $this->constans->actionGetId('selisih_waktu_pemulangan_pasien'); // set di lookup_transaksi dalam satuan menit; jika ingin difilter per hari, set menjadi 24 * 60 (1440)
        $getPasien = Pendaftaran::find()->select([
            'pendaftaran_id',
            'pasien_id',
            'ruangan_id',
        ])->where([
            'instalasi_id' => DocoConstants::VAR_I_RJ
        ])->andWhere([
            'IN', 'status_periksa', [
                DocoConstants::STATUS_PERIKSA_ANTR_POLI, DocoConstants::STATUS_PERIKSA_ANTR_KASIR, DocoConstants::STATUS_PERIKSA_DIPERIKSA
            ]
        ]);
        if (isset($minutesLimit) && !empty($minutesLimit)) {
            $getTimeDiff = strtotime(date('Y-m-d H:i:s')) - ($minutesLimit * 60);
            $getPasien = $getPasien->andWhere([
                '>=', 'tgl_pendaftaran', date('Y-m-d H:i:s', $getTimeDiff)
            ]);
        } else {
            $getPasien = $getPasien->andWhere([
                '<=', 'tgl_pendaftaran', date('Y-m-d 23:59:59', strtotime('yesterday'))
            ]);
        }
        $getPasien = $getPasien->all();

        foreach ($getPasien as $key => $value) {
            // value id pendaftaran
            $id = $value['pendaftaran_id'];
            $ruangan = $value['ruangan_id'];
            $pasien = $value['pasien_id'];
            // save into pasienpulang_t
            $modelPasienPulang = new PasienPulang;
            $modelPasienPulang->pendaftaran_id = $id;
            $modelPasienPulang->carakeluar_id = 1;
            $modelPasienPulang->tglpasienpulang = date('Y-m-d H:i:00');
            $modelPasienPulang->ruanganakhir_id = $ruangan;
            $modelPasienPulang->pasien_id = $pasien;

            if ($modelPasienPulang->save()) {
                // update status_periksa pendaftaran_t menjadi pulang
                $modelPendaftaran = Pendaftaran::findOne($id);
                $modelPendaftaran['status_periksa'] = 4;
                $modelPendaftaran['pasienpulang_id'] = $modelPasienPulang->pasienpulang_id;
                $modelPendaftaran->save();
            }
        }

        return [
            'message' => 'Pasien Berhasil Dipulangkan'
        ];
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
                'data' => $time_reset_rj[0],
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

    public function actionGetJenisKamar()
    {
        $q = Yii::$app->request->get('term', null);
        $statusIsi = Yii::$app->request->get('status_isi', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;

        $lookup = new Lookup;
        $lookup = $lookup->find()->where(['lookup_type' => 'jenis_kamar', 'is_active' => true, 'is_deleted' => false]);
        if(!empty($q)){
            $lookup = $lookup->andWhere(['ILIKE', 'lookup_value', $q]);
        }
        $jenis_kamar = $lookup->all();

        $jenis_kamar = array_map(function($val){
                          return ['id' => $val['lookup_id'], 'text' => $val['lookup_value'], 'value' => $val['lookup_value']];
                      }, $jenis_kamar);

        return $jenis_kamar;
    }

    public function actionSep($nosep)
    {
        try {
            $bpjs = new Bpjs;
            $response = $bpjs->referensiCariSep($nosep);
            return $response;
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

    public function actionPrb($noprb, $nosep)
    {
        try {
            $bpjs = new Bpjs;
            $response = $bpjs->searchPRB($noprb, $nosep);
            return $response;
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


    public function actionGetTemplateResep()
    {
        try {
            $request = Yii::$app->request;
            $pegawai_id = $request->get('pegawai_id', null);

            if(empty($pegawai_id)) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Dokter tidak boleh kosong',
                ];
            } else {
                // get data template
                $data_template = ResepTemp::find()->select([
                    'reseptemp_id',
                    'reseptemp_nama'
                ])->where(['dokter_id' => $pegawai_id])->asArray()->all();
                return $data_template;
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

    public function actionGetFisioNonPaketMaksFrekuensi(){
        $maksFrek = LookUpTransaksi::find()
        ->select(['additional_value'])
        ->where(['kode_transaksi' => DocoConstants::LT_MAKS_FREKUENSI_NON_PAKET])
        ->asArray()
        ->one();
        return $maksFrek;
    }

    private function getCaraBayar()
    {
        $model = CaraBayar::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['carabayar_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    public function actionListHistoryResep()
    {
        $pasienId = Yii::$app->request->get('pasien_id', null);
        $limit = Yii::$app->request->get('length', 10);
        $offset = Yii::$app->request->get('start', 0);
        $configZeroStock = $this->actionGetLookupTransaksi('config_zero_stock');

        // $subQueryPendaftaran = Pendaftaran::find()
        //     ->select(['pendaftaran_id'])
        //     ->where([
        //         'pasien_id' => $pasienId
        //     ])
        //     ->andWhere([
        //         'IN', 'instalasi_id', [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RI, DocoConstants::INST_ID_RD]
        //     ])
        //     ->asArray()->all();
        
        $getResepturHeader = \Doco\models\HistoryResepView::find()->select([
            'reseptur_id',
            'resep_id',
            'pasien_id',
            'pendaftaran_id',
            'no_reseptur',
            'nomor',
            'tglreseptur',
            'tglresep',
            'COALESCE(tglreseptur, tglresep) as tgl_resep',
            'nama_pegawai',
            'instalasi_reseptur',
            'ruangan_reseptur',
            'instalasi_resep',
            'ruangan_tujuan',
            'kategori_resep',
            'kategori_resep_nama',
            'kategori_resep_kode' 
        ])->andWhere([
            'pasien_id' => $pasienId
        ])->orderBy([
            'tgl_resep' => SORT_DESC
        ]);

        $totalDataCount = $getResepturHeader->count();

        $data = $getResepturHeader->limit($limit)->offset($offset)->asArray()->all();

        if ( $data ) {
            $nomorResep = ArrayHelper::getColumn($data, 'nomor');
            $resepturIds = ArrayHelper::getColumn($data, 'reseptur_id');

            // $getDetailResep = \Doco\models\InfoResepDetailView::find()->andWhere([
            //     'IN', 'noresep', $nomorResep
            // ])->asArray()->all();

            $getDetailResep = \Doco\models\DetailResepObatalkesView::find();
            $getDetailResep = $getDetailResep->select([
                'detailresepobatalkes_v.noresep',
                'detailresepobatalkes_v.reseptur_id',
                'detailresepobatalkes_v.racikan_id',
                'detailresepobatalkes_v.obatalkes_id',
                'detailresepobatalkes_v.obatalkes_nama',
                'detailresepobatalkes_v.signa_nama',
                'detailresepobatalkes_v.satuankecil_id',
                'detailresepobatalkes_v.satuan_kecil',
                'detailresepobatalkes_v.satuan_input',
                'detailresepobatalkes_v.satuan_konversi',
                'detailresepobatalkes_v.qty_reseptur',
                'detailresepobatalkes_v.det_transaksi',
                'detailresepobatalkes_v.qty_konversi',
                'detailresepobatalkes_v.qty_transaksi',
                'detailresepobatalkes_v.qty_racikan',
                'detailresepobatalkes_v.signa_id',
                'detailresepobatalkes_v.signa_nama',
                'detailresepobatalkes_v.satuan_racikan_id',
                'detailresepobatalkes_v.satuan_racikan_nama',
                'detailresepobatalkes_v.racikan_nama',
                'detailresepobatalkes_v.etiket',
                'detailresepobatalkes_v.additional_data',
                'detailresepobatalkes_v.additional_reseptur',
                'detailresepobatalkes_v.tgl_update_resep',
                'detailresepobatalkes_v.user_update_resep',
                'detailresepobatalkes_v.kelompokpegawai_user_resep',
                'detailresepobatalkes_v.nama_racikan',
                'detailresepobatalkes_v.rke',
                'detailresepobatalkes_v.is_kronis',
                'detailresepobatalkes_v.signa_iterasi',
                'detailresepobatalkes_v.qty_obat_signa',
            ]);
            
            $getDetailResep = $getDetailResep->andWhere([
                'IN', 'noresep', $nomorResep
            ])->orderBy([
                'racikan_nama' => SORT_ASC
            ])->asArray()->all();
    
            $getReseptur = \Doco\models\ResepturDetail::find()->select([
                'reseptur_id',
                'obatalkes_id',
                'hari'
            ])->andWhere([
                'IN', 'reseptur_id', $resepturIds
            ])->andWhere(['racikan_id' => 1])->asArray()->all();

            $getResepturRacikan = \Doco\models\ResepturRacikan::find()
                ->andWhere(['reseptur_id' => $resepturIds])
                ->orderBy('rke ASC')
            ->asArray()->all();

            $newDataResep = [];
            foreach($getDetailResep as $key => $value){
                // foreach($getResepturRacikan as $keyReseptur => $racikan){
                //     if($value['reseptur_id'] == $racikan['reseptur_id'] ){
                //         $value['nama_racikan'] = !empty($racikan['racikan']) ? $racikan['racikan'] : null;
                //         $value['rke'] = !empty($racikan['rke']) ? $racikan['rke'] : null;
                //     }
                // }
                $newDataResep[] = $value;
            }

            $reindexingDetailResep = ArrayHelper::index($newDataResep, null, 'noresep');
            $reindexingRacikan = ArrayHelper::index($getResepturRacikan, null, 'reseptur_id');
            $reindexingResepturDetailRacikan = ArrayHelper::index($getReseptur, null, 'reseptur_id');
            foreach( $data as $key => $value) {
                $sort = ArrayHelper::getValue($reindexingDetailResep, $value['nomor'], []);
                $key_values = array_column($sort, 'tgl_update_resep');
                array_multisort($key_values, SORT_DESC, $sort);
                $data[$key]['detail_resep'] = ArrayHelper::getValue($reindexingDetailResep, $value['nomor'], []);
                $data[$key]['reseptur_racikan'] = ArrayHelper::getValue($reindexingRacikan, $value['reseptur_id'], []);
                $data[$key]['reseptur_detail_racikan'] = ArrayHelper::getValue($reindexingResepturDetailRacikan, $value['reseptur_id'], []);
                $data[$key]['last_update'] = (isset($sort[0]) && $sort[0]['kelompokpegawai_user_resep'] == DocoConstants::KELOMPOK_PEGAWAI_KEFARMASIAN) ? $sort[0]['tgl_update_resep'] : NULL;
            }
        }

        return [
            'configZeroStock' => $configZeroStock,
            'data' => $data,
            '_meta' => [
                'totalCount' => $totalDataCount
            ]
        ];
    }

    public function actionGetKonfigSystem()
    {
        $konfigSystem = Yii::$app->cache->get(DocoConstants::VAR_K_S);
        if (empty($konfigSystem)) {
            $konfigSystem = KonfigSystem::find()->asArray()->one();
        }
        $orderBedahTanpaTindakan = isset($konfigSystem['order_bedah_tanpa_tindakan']) && !empty($konfigSystem['order_bedah_tanpa_tindakan']) ? $konfigSystem['order_bedah_tanpa_tindakan'] : false;
        $instruksiSoap = isset($konfigSystem['hide_instruksi_soap']) && !empty($konfigSystem['hide_instruksi_soap']) ? $konfigSystem['hide_instruksi_soap'] : false;

        return [
            'order_bedah_tanpa_tindakan' => $orderBedahTanpaTindakan,
            'hide_instruksi_soap' => $instruksiSoap
        ];
    }

    public static function getPelayananConfigButton($instalasi_id)
    {
        return KonfigPelayanan::find()
            ->where(['instalasi_id' => $instalasi_id, 'is_active' => true, 'is_deleted' => false])
            ->orderBy(['konfigpelayanan_id' => SORT_ASC])
            ->asArray()->all();
    }

    public static function getLookupTransaksi($kode_transaksi, $column)
    {
        $column = !is_array($column) ? $column : [$column];
        return LookupTransaksi::find()
            ->select($column)
            ->where(['kode_transaksi' => $kode_transaksi])
            ->asArray()->one();
    }

    public static function getKonfigSystem($column = null)
    {
        $model = KonfigSystem::find();
        if ($column) {
            $column = !is_array($column) ? $column : [$column];
            $model->select($column);
        }
        return $model->asArray()->one();
    }

    public function actionGetKonfigPelayanan()
    {
        $request = Yii::$app->request;
        $kode_transaksi = $request->get('kode_transaksi', null);
        $instalasi_id = $request->get('instalasi_id', DocoConstants::INST_ID_RJ);
        $rest_time_reset = null;
        $konfig_soap = [];
        try {
            $lookupTransaksi = self::getLookupTransaksi($kode_transaksi, 'additional_value');
            if ($lookupTransaksi) {
                $addValue = ArrayHelper::getValue($lookupTransaksi, 'additional_value', '[]');
                $rest_time_reset = json_decode($addValue)[0];
            }
            $konfigSystem = self::getKonfigSystem();
            $konfig_soap['order_bedah_tanpa_tindakan'] = isset($konfigSystem['order_bedah_tanpa_tindakan']) && !empty($konfigSystem['order_bedah_tanpa_tindakan']) ? $konfigSystem['order_bedah_tanpa_tindakan'] : false;
            $konfig_soap['hide_instruksi_soap'] = isset($konfigSystem['hide_instruksi_soap']) && !empty($konfigSystem['hide_instruksi_soap']) ? $konfigSystem['hide_instruksi_soap'] : false;
            return [
                'pelayanan_config_button' => self::getPelayananConfigButton($instalasi_id),
                'rest_time_reset' => $rest_time_reset,
                'konfig_soap' => $konfig_soap,
            ];
        } catch(Exception $e) {
            Yii::error($e);
            return [
                'pelayanan_config_button' => [],
                'rest_time_reset' => null,
                'konfig_soap' => [],
            ];
        }
    }

    private function getStokObatAlkesByDepoAndId($depo_id, $obatalkes_ids)
    {
        if (is_array($obatalkes_ids) && !empty($obatalkes_ids) && !empty($depo_id)) {
            $filteredArray = array_filter($obatalkes_ids, function($value) {
                return $value !== null;
            });
            if (!empty($filteredArray)) {
                $implodeobatalkes_id = implode(',', $filteredArray);
                $get_stok = (new FgetKetersediaanobatFn(['extParam'=>[$depo_id, $implodeobatalkes_id]]))->find()->select(['obatalkes_id', 'qty_tersedia'])->asArray()->all();

                return ArrayHelper::index($get_stok, 'obatalkes_id');
            }
            return [];
        }

        return [];
    }
}
