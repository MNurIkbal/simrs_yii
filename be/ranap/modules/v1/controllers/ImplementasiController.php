<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use app\modules\v1\models\CpptView;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoAkunting;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use yii\db\Expression;
use Doco\Services\KasirService;
use Doco\models\TarifTotalFn;
use Doco\models\TarifKomponenRsFn;

// Using model
use app\modules\v1\models\Cppt;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\InfoImplementasiView;
use app\modules\v1\models\Instruksi;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InstruksiTindakanBmhp;
use app\modules\v1\models\Implementasi;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\ObatAlkesFn;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\InfoKunjunganRi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PasienAdmisi;

use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\FGetinstruksi;
use app\modules\v1\models\PindahKamar;
use SirsCore\features\FeatureTindakanBmhp;

// integrate akunting
use SirsCore\features\IntegrasiAkunting;

// Class
class ImplementasiController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\RiwayatInstruksiTindakanView';

    // Verbs
    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);

        // Return
        return $actions;
    }

    public function actionIndex()
    {
        // Try catch
        try {
            // Request      
            $connection = Yii::$app->db;
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', 0);
            $ruangan_id = $request->get('ruangan_id', 0);
            $pasienadmisi_id = $request->get('pasienadmisi_id', 0);
            $page = $request->get('page', 0);
            $perpage = $request->get('per-page', 0);
            $offset = ($page - 1) * $perpage;
            $newList = $listAllInstruksi = [];
            $infoinstruksi = (new InfoInstruksiView)->getInstructionData([
                'infoinstruksi_v.instruksi_id',
                'infoinstruksi_v.cppt_id',
                'infoinstruksi_v.pendaftaran_id',
                'infoinstruksi_v.instruksi_deleted',
                'infoinstruksi_v.tanggal_terapi',
                'MAX(infoinstruksi_v.tgl_instruksi) AS tgl_instruksi',
                'infoinstruksi_v.grouping_tipe',
                'infoinstruksi_v.is_verifikasi_dpjp',
                'infoinstruksi_v.is_penatajasa'
            ])->where([
                'infoinstruksi_v.pendaftaran_id' => $pendaftaran_id,
            ])
             ->andWhere(['not', ['infoinstruksi_v.pasienadmisi_id' => null]])
            ->groupBy([
                'infoinstruksi_v.instruksi_id',
                'infoinstruksi_v.cppt_id',
                'infoinstruksi_v.pendaftaran_id',
                'infoinstruksi_v.grouping_tipe',
                'infoinstruksi_v.instruksi_deleted',
                'infoinstruksi_v.tanggal_terapi',
                'infoinstruksi_v.is_verifikasi_dpjp',
                'infoinstruksi_v.is_penatajasa'
            ])
            ->orderBy(['tgl_instruksi' => SORT_DESC]);

            $grandTotal = Instruksi::find()->select([
                'intruksi_t.instruksi_id',
                'cppt_t.cppt_id',
                'cppt_t.pendaftaran_id',
                'cppt_t.pasienadmisi_id'
            ])
                ->rightJoin('cppt_t', 'cppt_t.cppt_id = instruksi_t.cppt_id')
                ->where([
                    'cppt_t.pendaftaran_id' => $pendaftaran_id,
                    'cppt_t.pasienadmisi_id' => $pasienadmisi_id
                ])
                ->count();

            $listGroupInstruksi = $infoinstruksi
                ->limit($perpage)->offset($offset)
                ->all();

            if (count($listGroupInstruksi) > 0) {
                $instruksiId = $is_penatajasa = $getPenataJasa = $grouping_tipe = [];
                foreach ($listGroupInstruksi as $key =>  $groupInstruksi) {
                    $is_penatajasa[] = $groupInstruksi['is_penatajasa'];
                    if (!in_array($groupInstruksi['instruksi_id'], $instruksiId)) {
                        $instruksiId[] = $groupInstruksi['instruksi_id'];
                        $grouping_tipe[] = $groupInstruksi['grouping_tipe'];
                    }
                    $listAllInstruksi[$key]['pendaftaran_id'] = $groupInstruksi['pendaftaran_id'];
                    $listAllInstruksi[$key]['instruksi_id'] = ($groupInstruksi['is_penatajasa'] && empty($groupInstruksi['instruksi_id'])) ? (int) substr(strtotime($groupInstruksi['tgl_instruksi']), -5) : $groupInstruksi['instruksi_id'];
                    $listAllInstruksi[$key]['cppt_id'] = $groupInstruksi['cppt_id'];
                    $listAllInstruksi[$key]['grouping_tipe'] = $groupInstruksi['grouping_tipe'];
                    $listAllInstruksi[$key]['tgl_instruksi'] = $groupInstruksi['tgl_instruksi'];
                    $listAllInstruksi[$key]['instruksi_deleted'] = $groupInstruksi['instruksi_deleted'];
                    $listAllInstruksi[$key]['tanggal_terapi'] = $groupInstruksi['tanggal_terapi'];
                    $listAllInstruksi[$key]['is_verifikasi_dpjp'] = $groupInstruksi['is_verifikasi_dpjp'];
                    $listAllInstruksi[$key]['data_instruksi'] = [];
                    $listAllInstruksi[$key]['data_implementasi'] = [];
                }

                $modelInstruksi = InfoInstruksiView::find()->select([
                    'instruksi_id',
                    'cppt_id',
                    'tipe_instruksi',
                    'catatan_instruksi',
                    'cpptpegawai_id',
                    'is_verifikasi_dpjp',
                    'instruksi_deleted',
                    'tindakaninstruksi_nama',
                    'qty',
                    'tgl_instruksi',
                    'ket_cyto',
                    'ket_racik',
                    'tindakan_deleted',
                    'daftar_paket',
                    'status',
                    'grouping_tipe',
                    'ruangan_pertindakan',
                    'instruksi',
                    'is_telah_implementasi',
                    'dokter',
                    'bmhp_tindakandetail',
                    'status_implementasi',
                    'is_penatajasa',
                ]);
                $getByInstruksiId = $modelInstruksi->where([
                    'instruksi_id' => $instruksiId,
                    'pendaftaran_id' => $pendaftaran_id
                ])
                    ->andWhere(['not', ['infoinstruksi_v.pasienadmisi_id' => null]])
                    ->orderBy(['tgl_instruksi' => SORT_DESC])
                    ->asArray()
                    ->all();

                // Cek inputan penata jasa
                if (in_array(true, $is_penatajasa)) {
                    $getPenataJasa = $modelInstruksi->where([
                        'pendaftaran_id' => $pendaftaran_id,
                        'is_penatajasa' => true
                    ])
                        ->andWhere(['not', ['infoinstruksi_v.pasienadmisi_id' => null]])
                        ->orderBy(['tgl_instruksi' => SORT_DESC])
                        ->asArray()
                        ->all();
                    foreach ($getPenataJasa as $key => $val) {
                        $getPenataJasa[$key]['instruksi_id'] = (int) substr(strtotime($val['tgl_instruksi']), -5);
                        $getPenataJasa[$key]['status'] = 'Sudah Implementasi';
                    }
                }

                $instruksiData = ArrayHelper::index(array_merge($getByInstruksiId, $getPenataJasa), null, 'instruksi_id');
                $implementasiData = ArrayHelper::index(InfoImplementasiView::find()->select([
                    'tipe_implementasi',
                    'tgl_implementasi',
                    'implementasi',
                    'paket',
                    'daftar_paket',
                    'perawat_1',
                    'perawat_2',
                    'catatan_implementasi',
                    'instruksi_id'
                ])->where([
                    'instruksi_id' => $instruksiId,
                ])
                    ->orderBy(['tgl_implementasi' => SORT_DESC])
                    ->asArray()
                    ->all(), null, 'instruksi_id');

                foreach ($listAllInstruksi as $key => $item) {
                    $listAllInstruksi[$key]['data_implementasi'] = isset($implementasiData[$item['instruksi_id']]) ? $implementasiData[$item['instruksi_id']] : [];
                    $listAllInstruksi[$key]['data_instruksi'] = isset($instruksiData[$item['instruksi_id']]) ? $instruksiData[$item['instruksi_id']] : [];
                }
            }
            return [
                'data' => $listAllInstruksi,
                'totalCount' => $grandTotal
            ];
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionImplementasiDetail($instruksi_id = null, $groupTipe = '')
    {
        $kelompok = '';
        $data = InfoImplementasiView::find()->select([
            'tipe_implementasi',
            'tgl_implementasi',
            'implementasi',
            'paket',
            'daftar_paket',
            'perawat_1',
            'perawat_2',
            'catatan_implementasi',
            'instruksi_id'
        ])->where([
            'instruksi_id' => $instruksi_id,
        ]);

        if($groupTipe == 'REHAB MEDIK'){
            $kelompok = 'FISIO_TINDAKAN';
        }

        if(!empty($kelompok)){
            $data->andWhere([
                'tipe_implementasi' => $kelompok
            ]);
        }

        $data = $data->orderBy(['tgl_implementasi' => SORT_DESC])
            ->asArray()
            ->all();
        return $data;
    }  

    public function actionBundleDataTransaksi()
    {
        try {
            $params = Yii::$app->request;
            $pendaftaran_id = $params->get('pendaftaran_id', 0);
            $pasienadmisi_id = $params->get('pasienadmisi_id', 0);
            $cppt_id = $params->get('cppt_id', 0);
            $instruksi_id = $params->get('instruksi_id', 0);
            $ruangan_id = $params->get('ruangan_id', 0);

            return [
                'dataAdditional' => $this->dataKeperawatanPasien($pendaftaran_id, $pasienadmisi_id),
                'dataInstruksi' => Instruksi::find()->where(['instruksi_id' => $instruksi_id])->one(),
                'datasetTindakan' => $this->getDatasetTindakan($cppt_id, $instruksi_id),
                'datasetBmhp' => $this->getDatasetBmhp($cppt_id, $instruksi_id),
                'data_perawat' => $this->getPegawaiRuangan($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAKEPERAWATAN)->asArray()->all()
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

    private function getDatasetTindakan($cppt_id, $instruksi_id)
    {
        $data = (new \yii\db\Query())
            ->select([
                'tipe',
                'tindakan_paket_obat_id',
                'tindakan_paket_obat',
                'dokterdpjp_id',
                'dokter_periksa',
                'dokterdelegasi_id',
                'dokter_delegasi',
                'perawat1_id',
                'perawat_1',
                'perawat2_id',
                'perawat_2',
                'tarif_satuan',
                'tarif_cyto',
                'qty',
                'qty_sisa',
                'instruksitindakan_id',
                'is_cyto',
                'tindakan_deleted',
            ])
            ->from('riwayat_instruksitindakan_v')
            ->where(['cppt_id' => $cppt_id, 'instruksi_id' => $instruksi_id])
            ->andWhere(['IN', 'tipe', ['TINDAKAN', 'PAKET']])
            ->all();
        $dataTindakan = [];
        if (count($data) > 0) {
            foreach ($data as $rowData) {
                if ($rowData['tipe'] == 'PAKET') {
                    $result = PaketDetailView::find()->where(['tipepaket_id' => $rowData['tindakan_paket_obat_id']])->asArray()->all();
                    $rowData['paketDetail'] = $result;
                }
                $dataTindakan[] = $rowData;
            }
        }
        return $dataTindakan;
    }

    private function getDatasetBmhp($cppt_id, $instruksi_id)
    {
        // $subquery = "SELECT hargaygdigunakan FROM konfigfarmasi_k LIMIT 1";
        // $harga_konfig_bmhp = new Expression("CASE
        //         WHEN ($subquery) = 'MAX' THEN dataobat.hargamaksimum
        //         WHEN ($subquery) = 'MIN' THEN dataobat.hargaminimum
        //         ELSE dataobat.hargaratarata
        //         END AS harga_konfig_bmhp");
        $query = (new \yii\db\Query())
            ->select([
                'riwayat_instruksitindakan_v.bmhp_namainstruksi_id',
                'riwayat_instruksitindakan_v.bmhp_namainstruksi',
                'riwayat_instruksitindakan_v.tindakan_paket_obat_id',
                'riwayat_instruksitindakan_v.tindakan_paket_obat',
                'riwayat_instruksitindakan_v.perawat1_id',
                'riwayat_instruksitindakan_v.perawat_1',
                'riwayat_instruksitindakan_v.perawat2_id',
                'riwayat_instruksitindakan_v.perawat_2',
                'riwayat_instruksitindakan_v.tarif_satuan',
                'riwayat_instruksitindakan_v.tarif_cyto',
                'riwayat_instruksitindakan_v.qty',
                'riwayat_instruksitindakan_v.qty_sisa',
                'riwayat_instruksitindakan_v.ditagihkan',
                'riwayat_instruksitindakan_v.instruksitindakan_id',
                'riwayat_instruksitindakan_v.tindakan_deleted',
                'riwayat_instruksitindakan_v.tarif_satuan as harga_konfig_bmhp',
            ])
            ->from('riwayat_instruksitindakan_v')
            ->leftJoin("obatalkes_m dataobat", "dataobat.obatalkes_id = riwayat_instruksitindakan_v.tindakan_paket_obat_id AND riwayat_instruksitindakan_v.tipe = 'BMHP'")
            ->where(['riwayat_instruksitindakan_v.cppt_id' => $cppt_id, 'riwayat_instruksitindakan_v.instruksi_id' => $instruksi_id])
            ->andWhere(['riwayat_instruksitindakan_v.tipe' => "BMHP"]);
        $data = $query->all();
        return $data;
    }

    public function dataKeperawatanPasien($pendaftaran_id, $pasienadmisi_id)
    {
        $result = InfoPasienRiView::find()->where(['pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => $pasienadmisi_id])->one();
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

    public function actionCreateTransaksiImplementasi()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;

        try {
            if ($post = $request->post()) {
                $datapost = $dataattrib = $pindahKamar = [];

                $dataattrib = $post['ImplementasiAdditionalForm'];
                $datapost['implementasi'] = $post['ImplementasiForm'];
                $datapost['implementasi_tindakan'] = isset($post['ImplementasiTindakanForm']) ? $post['ImplementasiTindakanForm'] : [];
                $datapost['implementasi_bmhp'] = isset($post['ImplementasiBmhpForm']) ? $post['ImplementasiBmhpForm'] : [];
                $implementasi = $implementasi_tindakan = $implementasi_bmhp = [];
                /** declare attributes */
                $instruksi_id = isset($datapost['implementasi']['instruksi_id']) ? $datapost['implementasi']['instruksi_id'] : null;
                $kelasPelayanan = isset($dataattrib['kelaspelayanan_id']) ? $dataattrib['kelaspelayanan_id'] : null;
                $penjamin = isset($dataattrib['penjamin_id']) ? $dataattrib['penjamin_id'] : null;
                $pasienAdmisi = isset($dataattrib['pasienadmisi_id']) ? $dataattrib['pasienadmisi_id'] : null;
                $caraBayar = isset($dataattrib['carabayar_id']) ? $dataattrib['carabayar_id'] : null;
                $pasienId = isset($dataattrib['pasien_id']) ? $dataattrib['pasien_id'] : null;
                $pendaftaranId = isset($dataattrib['pendaftaran_id']) ? $dataattrib['pendaftaran_id'] : null;
                $noPendaftaran = $kelasPelayananLama = null;
                $ruanganId = isset($dataattrib['ruangan_id']) ? $dataattrib['ruangan_id'] : null;
                if(!empty($pendaftaranId)) {
                    $pendaftaran = Pendaftaran::findOne($pendaftaranId);
                    $isCloseBill = ArrayHelper::getValue($pendaftaran, 'is_close_bill', false);
                    if($isCloseBill) {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Pasien sudah dilakukan proses Lock Bill.'
                        ];
                    }
                }

                $extParam = implode(',', [$pasienId, 'NULL', 'NULL', 'NULL', 'NULL', $pendaftaranId, 'NULL', 1, 0]);
                $instruksiView = (new FGetinstruksi([
                    'extParam' =>  $extParam
                ]))->find()
                ->select([
                    'ruangan_pertindakan_id',
                ])->andWhere(['instruksi_id' => $instruksi_id])->asArray()->one();
                
                if(! empty($instruksiView)) {
                    $ruanganInstruksi = ArrayHelper::getValue($instruksiView, 'ruangan_pertindakan_id');
                    $ruanganId = $ruanganInstruksi == $ruanganId ? $ruanganId : $ruanganInstruksi;
                }

                /** Cek Kelas Titip */
                if ($pasienAdmisi && $pendaftaranId) {
                    $isTitipan = false;
                    $cekPasien = $this->infoPasien($pasienAdmisi);
                    $pindahKamar = PindahKamar::getKelasTitipan($pendaftaranId);
                    $noPendaftaran = $cekPasien['no_pendaftaran'];

                    if (!empty($pindahKamar)) {
                        if ($pindahKamar[0]['is_pasientitipan'] == true) {
                            if ($pindahKamar[0]['is_stoptitipan'] != true) {
                                $kelasPelayanan = !empty($pindahKamar[0]['kelas_ditagihkan_id']) ? $pindahKamar[0]['kelas_ditagihkan_id'] : $kelasPelayanan;
                            } else {
                                $kelasPelayanan = $pindahKamar[0]['kelaspelayanan_id'];
                            }
                        } else {
                            $kelasPelayanan = $pindahKamar[0]['kelaspelayanan_id'];
                        }
                    } else if ($cekPasien['is_pasientitipan'] == true) {
                        $isTitipan = true;
                        if ($cekPasien['is_stoppasientitipan'] != true) {
                            $kelasPelayanan = (isset($cekPasien['kelas_ditagihkan_id']) && !empty($cekPasien['kelas_ditagihkan_id'])) ? $cekPasien['kelas_ditagihkan_id'] : $kelasPelayanan;
                        } else {
                            $kelasPelayanan = $cekPasien['kelaspelayanan_id'];
                        }
                    }
                }

                // Cek kelas pelayanan khusus [HCU, ICU, ICCU]
                $kelasKhusus = $this->constans->actionGetAdditional('kelas_khusus', true);
                if (in_array($kelasPelayanan, $kelasKhusus)) {
                    $subQuery = MasukKamar::find()
                        ->select([
                            'pasienadmisi_id',
                            'MAX(masukkamar_id) AS masukkamar_id'
                        ])->groupBy('pasienadmisi_id');
                    $masukkamar_id = PasienAdmisi::find()
                        ->select(['masukkamar.masukkamar_id'])
                        ->innerJoin(['masukkamar' => $subQuery], 'pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id')
                        ->where([
                            'pasienadmisi_t.pasienadmisi_id' => $pasienAdmisi
                        ])
                        ->scalar();

                    $kelasPelayananLama = MasukKamar::find()
                        ->select([
                            new \yii\db\Expression('COALESCE(masukkamar_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id) AS kelaspelayanan_id'),
                        ])
                        ->innerJoin('pasienadmisi_t', 'masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')
                        ->where([
                            '<>', 'masukkamar_id', $masukkamar_id
                        ])
                        ->andWhere([
                            'pasienadmisi_t.pasienadmisi_id' => $pasienAdmisi
                        ])
                        ->andWhere([
                            'NOT IN', 'masukkamar_t.kelaspelayanan_id', $kelasKhusus
                        ])
                        ->orderBy([
                            'masukkamar_id' => SORT_DESC
                        ])
                        ->scalar();
                } else {
                    $kelasPelayananLama = $kelasPelayanan;
                }

                if (!empty($datapost['implementasi_tindakan'])) {
                    /** Kondisi Instruksi Tindakan */
                    $model = InstruksiTindakan::find()->where([
                        'instruksi_id' => $instruksi_id
                    ])->all();

                    $listInstruksiTindakan = $whereCond = [];
                    foreach ($model as $value) {
                        $idParent = $value->instruksitindakan_id;
                        $listInstruksiTindakan[$idParent] = $value;
                        $tindakan = !empty($value->tipepaket_id) ? $value->tipepaket_id : $value->daftartindakan_id;
                        $whereCond[] = "daftartindakan_id = {$tindakan}";
                    }
                    $condQuery = null;
                    if (!empty($whereCond)) {
                        $condQuery = implode(' OR ', $whereCond);
                    }

                    foreach ($datapost['implementasi_tindakan'] as $rowData) {
                        $instId = $rowData['instruksitindakan_id'];
                        if (empty($rowData['jml_diimplementasi']) || !isset($listInstruksiTindakan[$instId])) continue;

                        $mInstruksiTindakan = $listInstruksiTindakan[$instId];

                        $mImplementasi = new Implementasi;
                        if (isset($datapost['implementasi']['tgl_implementasi'])) {
                            $tglImplement = $datapost['implementasi']['tgl_implementasi'];
                            $datapost['implementasi']['tgl_implementasi'] = date('Y-m-d h:i:s', strtotime($tglImplement));
                        }
                        $mImplementasi->attributes = $datapost['implementasi'];
                        if (!$mImplementasi->save()) {
                            throw new \yii\base\Exception("Error Processing Request", 1);
                        }

                        $rowData['implementasi_id'] = $mImplementasi->implementasi_id;
                        $rowData['qty_tindakan'] = floatval($rowData['jml_diimplementasi']);
                        $rowData['tarif_satuan'] = floatval($rowData['tarif_satuan']);

                        $rowData['pasienadmisi_id'] = $dataattrib['pasienadmisi_id'];
                        $rowData['kelaspelayanan_id'] = $dataattrib['kelaspelayanan_id'];
                        $rowData['pasien_id'] = $dataattrib['pasien_id'];
                        $rowData['instalasi_id'] = $dataattrib['instalasi_id'];
                        $rowData['carabayar_id'] = $dataattrib['carabayar_id'];
                        $rowData['pendaftaran_id'] = $dataattrib['pendaftaran_id'];
                        $rowData['jeniskasuspenyakit_id'] = $dataattrib['jeniskasuspenyakit_id'];
                        $rowData['ruangan_id'] = $dataattrib['ruangan_id'];
                        $rowData['penjamin_id'] = $dataattrib['penjamin_id'];
                        $rowData['tgl_tindakan'] = date('Y-m-d H:i:s');
                        $rowData['daftartindakan_id'] = $mInstruksiTindakan->daftartindakan_id;
                        $rowData['tipepaket_id'] = $mInstruksiTindakan->tipepaket_id;
                        $rowData['cyto_tindakan'] = isset($mInstruksiTindakan->is_cyto) ? $mInstruksiTindakan->is_cyto : false;
                        $rowData['tarifcyto_tindakan'] = isset($mInstruksiTindakan->is_cyto) && $mInstruksiTindakan->is_cyto == TRUE ? $mInstruksiTindakan->tarif_cyto : 0;
                        $rowData['tarif_tindakan'] = ($rowData['tarif_satuan'] + floatval($rowData['tarifcyto_tindakan'])) * $rowData['qty_tindakan'];
                        $rowData['dokterpenanggungjawab_id'] = $mInstruksiTindakan->dokterdpjp_id;

                        $implementasi_tindakan[] = $rowData;

                        $mInstruksiTindakan->qty_sisa = (int) $mInstruksiTindakan->qty_sisa - (int) $rowData['jml_diimplementasi'];

                        if ($mInstruksiTindakan->qty_sisa == 0) {
                            $mInstruksiTindakan->status_implementasi = "455";
                        } else {
                            $mInstruksiTindakan->status_implementasi = "456";
                        }

                        if (!$mInstruksiTindakan->update()) {
                            throw new \yii\base\Exception("Error Processing Request", 1);
                        }
                    }

                    if (!empty($implementasi_tindakan)) {
                        $insertTindakan = $listTindakan = [];
                        foreach ($implementasi_tindakan as $k_tindakan => $v_tindakan) {
                            $additional_data = [];
                            $qtyTindakan = $v_tindakan['qty_tindakan'];
                            $cekTindakan = DaftarTindakan::find()->select(['kelompoktindakan_id', 'is_konsultasi'])->where(['daftartindakan_id' => $v_tindakan['daftartindakan_id']])->one();
                            $kelasPelayananTemp = $cekTindakan['kelompoktindakan_id'] == DocoConstants::KT_VISITE || $cekTindakan['is_konsultasi'] ? $kelasPelayanan : $kelasPelayananLama;

                            /** List untuk ke kasir */
                            $listTindakan[$kelasPelayananTemp][] = [
                                'instruksitindakan_id' => $v_tindakan['instruksitindakan_id'],
                                'implementasi_id' => $v_tindakan['implementasi_id'],
                                'dokter_id' => $v_tindakan['dokterpenanggungjawab_id'],
                                'perawat_id' => isset($v_tindakan['perawat1_id']) && !empty($v_tindakan['perawat1_id']) ? $v_tindakan['perawat1_id'] : null,
                                'perawat2_id' => isset($v_tindakan['perawat2_id']) && !empty($v_tindakan['perawat2_id']) ? $v_tindakan['perawat2_id'] : null,
                                'tipepaket_id' => $v_tindakan['tipepaket_id'],
                                'is_cyto' => !empty($v_tindakan['cyto_tindakan']) ? true : false,
                                'qty' =>  $qtyTindakan,
                                'daftartindakan_id' => $v_tindakan['daftartindakan_id'],
                            ];
                        }

                        // Grouping tindakan berdasarkan kelas pelayanan untuk kelas khusus
                        foreach ($listTindakan as $kp => $val) {
                            $postBill = [
                                'pendaftaran_id' => $pendaftaranId,
                                'no_pendaftaran' => $noPendaftaran,
                                'ruangan_id' => $ruanganId,
                                'instalasi_id' => $dataattrib['instalasi_id'],
                                'penjamin_id' => $penjamin,
                                'kelas_pelayanan_id' => $kp,
                                'tgl_transaksi' => date('Y-m-d H:i:s'),
                                'detail_tindakan' => $val,
                            ];

                            $billKasir = (new KasirService)->post('api/billing', [
                                'form_params' => $postBill,
                                'failed' => function ($data) {
                                    \Yii::error([
                                        "Message-Error" => $data
                                    ]);
                                    return [
                                        'failed' => true,
                                        'message' => [
                                            'status' => 422,
                                            'text' => isset($data['message']) ? $data['message'] : 'Billing tindakan gagal disimpan'
                                        ]
                                    ];
                                }
                            ]);

                            if (isset($billKasir['failed'])) {
                                $transaction->rollBack();
                                return [
                                    'status' => 422,
                                    'title' => 'Proses Gagal!',
                                    'text' => isset($billKasir['message']['text']) ? $billKasir['message']['text'] : 'Terjadi Kesalahan API'
                                ];
                            }
                        }

                        /** Comment jika sudah integrasi ke kasir */
                        // TindakanPelayanan::batchInsert($insertTindakan);
                    }
                }

                if (!empty($datapost['implementasi_bmhp'])) {
                    /** Kondisi Instruksi Obat BMHP */
                    $model = InstruksiTindakanBmhp::find()->where([
                        'instruksi_id' => $instruksi_id
                    ])->all();
                    $listObatBmhp = [];
                    foreach ($model as $value) {
                        $idParent = $value->instruksitindakanbmhp_id;
                        $listObatBmhp[$idParent] = $value;
                    }

                    $headerOa = $listImplement = [];
                    foreach ($datapost['implementasi_bmhp'] as $rowBmhp) {
                        $idBmhp = $rowBmhp['instruksitindakanbmhp_id'];
                        if (empty($rowBmhp['jml_diimplementasi']) || !isset($listObatBmhp[$idBmhp])) continue;

                        $mInstruksiTindakanBmhp = $listObatBmhp[$idBmhp];

                        $mImplementasi = new Implementasi;
                        if (isset($datapost['implementasi']['tgl_implementasi'])) {
                            $tglImplement = $datapost['implementasi']['tgl_implementasi'];
                            $datapost['implementasi']['tgl_implementasi'] = date('Y-m-d h:i:s', strtotime($tglImplement));
                        }

                        $mImplementasi->attributes = $datapost['implementasi'];
                        if (!$mImplementasi->save()) {
                            throw new \yii\base\Exception("Error Processing Request", 1);
                        }
                        $implementId = $mImplementasi->getPrimaryKey();
                        $listImplement[] = $implementId;
                        $implementasi_bmhp[] = [
                            'is_ditagihkan' => (int) $rowBmhp['is_ditagihkan'],
                            'obatalkes_id' => $rowBmhp['obatalkes_id'],
                            'ruangan_id' => $mInstruksiTindakanBmhp->ruangan_id,
                            'pegawai_id' => $mInstruksiTindakanBmhp->dokter_id,
                            'qty_oa' => $rowBmhp['jml_diimplementasi'],
                            'perawat1_id' => $rowBmhp['perawat1_id'],
                            'perawat2_id' => $rowBmhp['perawat2_id'],
                            'instruksitindakanbmhp_id' => $idBmhp,
                            'implementasi_id' => $implementId,
                            'additional_data' => json_encode([
                                'total_implement' => (int) $rowBmhp['jml_diimplementasi']
                            ]),
                        ];

                        $mInstruksiTindakanBmhp->qty_sisa = (int) $mInstruksiTindakanBmhp->qty_sisa - (int) $rowBmhp['jml_diimplementasi'];
                        $mInstruksiTindakanBmhp->status_implementasi = "456";
                        if ($mInstruksiTindakanBmhp->qty_sisa == 0) {
                            $mInstruksiTindakanBmhp->status_implementasi = "455";
                        }

                        if (!$mInstruksiTindakanBmhp->update()) {
                            throw new \yii\base\Exception("Error Processing Request", 1);
                        }
                    }

                    if (count($implementasi_bmhp) > 0) {
                        $headerOa = [
                            'primary_key' => 'implementasi_id',
                            'pendaftaran_id' => $pendaftaranId,
                            'pasien_id' => $pasienId,
                            'penjamin_id' => $penjamin,
                            'carabayar_id' => $caraBayar,
                            'pasienadmisi_id' => $pasienAdmisi,
                            'kelaspelayanan_id' => $kelasPelayanan,
                            'set_tagihan' => false,
                            'implementasi_id' => $listImplement,
                            'ruangan_id' => $mInstruksiTindakanBmhp->ruangan_id,
                        ];
                        $transOa = [
                            'trx_oa' => $headerOa,
                            'trx_oa_detail' => $implementasi_bmhp
                        ];
                        FeatureTindakanBmhp::createOA($transOa, false, Yii::$app->jwt->ruangan_id == $mInstruksiTindakanBmhp->ruangan_id ? true : false);
                    }
                }

                if (count($implementasi_tindakan) > 0 || count($implementasi_bmhp) > 0) {
                    $status = DocoConstants::BELUM_LUNAS;
                    $pendaftaran_id = $dataattrib['pendaftaran_id'];
                    $upd_pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
                    if (is_null($upd_pendaftaran)) {
                        throw new \yii\base\Exception("Data Pendaftaran Tidak Ditemukan", 1);
                    } else {
                        $upd_pendaftaran->status_bayar = $status;
                        if (!$upd_pendaftaran->update()) {
                            throw new \yii\base\Exception("Gagal Ubah Status Pendaftaran", 1);
                        }
                    }
                    /** update status obatalkes untuk permintaan ke farmasi */
                    if (count($implementasi_bmhp) > 0) {
                        foreach ($listImplement as $v) {
                            $model = ObatAlkesPasien::find()->where(['implementasi_id' => $v])->one();
                            $model->status_bmhp = DocoConstants::BMHP_BELUM_VERIFIKASI;
                            $model->update();
                        }
                    }
                    $transaction->commit();
                    $instalasi_id = Yii::$app->jwt->instalasi_id;
                    $no_pendaftaran = $upd_pendaftaran->no_pendaftaran;
                    IntegrasiAkunting::integrateTindakanBmhp($no_pendaftaran, $instalasi_id);
                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
            return ['message' => 'Gagal'];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionCetakImplementasiPdf
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #tgl_cetak# => tgl_cetak
     * @attribute #nama_user# => nama_user
     * @attribute #table_instruksi_implementasi# => table
     * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
     * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
     * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
     * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
     * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
     * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
     * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
     * @attribute #inf_umur# => Informasi Pasien: Umur
     * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
     * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
     * @attribute #inf_nokamar# => Informasi Pasien: No Kamar
     * @attribute #inf_nobed# => Informasi Pasien: No Bed
     * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
     * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
     **/
    public function actionCetakImplementasiPdf()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', 0);
        $ruangan_id = $request->get('ruangan_id', 0);
        $pasienadmisi_id = $request->get('pasienadmisi_id', 0);
        $skip_ruangan = $request->get('skip_ruangan', 0);
        $modelHeader = new InfoPasienRiView;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id' => $request->get('pendaftaran_id'),
                'pasienadmisi_id' => $request->get('pasienadmisi_id'),
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $nama_usercetak = $request->get('nama_usercetak', '');
        $id_usercetak = $request->get('id_usercetak', 0);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id' => $id_usercetak])->asArray()->one();

        if (is_null($mNamaPegawai)) {
            $nama_user = $nama_usercetak;
        } else {
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $header1 = array(
            Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            Yii::t('app', "Tanggal pendaftaran") => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            Yii::t('app', "No Pendaftaran") => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
            Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            Yii::t('app', "Kasus Penyakit") => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : ''
        );

        $header2 = array(
            Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
            Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            Yii::t('app', "Kelas Pelayanan") => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
            Yii::t('app', "No. Kamar / No. Bed") => $resultHeader ? $resultHeader['kamarruangan_nokamar'] . ' / ' . $resultHeader['no_tempattidur'] : ''
        );


        $newList = $listAllInstruksi = [];
        $infoinstruksi = (new \yii\db\Query())
            ->select([
                'infoinstruksi_v.instruksi_id',
                'infoinstruksi_v.cppt_id',
                'cppt_t.pendaftaran_id',
                'infoinstruksi_v.instruksi_deleted',
                'infoinstruksi_v.tanggal_terapi',
                'MAX(infoinstruksi_v.tgl_instruksi) AS tgl_instruksi',
                'infoinstruksi_v.grouping_tipe',
                'infoinstruksi_v.is_verifikasi_dpjp',
                'infoinstruksi_v.catatan_instruksi'
            ])
            ->from('infoinstruksi_v')
            ->leftJoin('cppt_t', 'cppt_t.cppt_id = infoinstruksi_v.cppt_id')
            ->where([
                'cppt_t.pendaftaran_id' => $pendaftaran_id,
                'cppt_t.pasienadmisi_id' => $pasienadmisi_id
            ])
            ->groupBy([
                'infoinstruksi_v.instruksi_id',
                'infoinstruksi_v.cppt_id',
                'cppt_t.pendaftaran_id',
                'infoinstruksi_v.grouping_tipe',
                'infoinstruksi_v.instruksi_deleted',
                'infoinstruksi_v.tanggal_terapi',
                'infoinstruksi_v.is_verifikasi_dpjp',
                'infoinstruksi_v.catatan_instruksi'
            ])
            ->orderBy(['tgl_instruksi' => SORT_DESC]);

        if ($skip_ruangan == 0) {
            $infoinstruksi->andWhere(['cppt_t.ruangan_id' => $ruangan_id]);
        }

        $listGroupInstruksi = $infoinstruksi->all();
        if (count($listGroupInstruksi) > 0) {
            foreach ($listGroupInstruksi as $key =>  $groupInstruksi) {
                $listAllInstruksi[$key]['pendaftaran_id'] = $groupInstruksi['pendaftaran_id'];
                $listAllInstruksi[$key]['instruksi_id'] = $groupInstruksi['instruksi_id'];
                $listAllInstruksi[$key]['cppt_id'] = $groupInstruksi['cppt_id'];
                $listAllInstruksi[$key]['grouping_tipe'] = $groupInstruksi['grouping_tipe'];
                $listAllInstruksi[$key]['tgl_instruksi'] = $groupInstruksi['tgl_instruksi'];
                $listAllInstruksi[$key]['instruksi_deleted'] = $groupInstruksi['instruksi_deleted'];
                $listAllInstruksi[$key]['tanggal_terapi'] = $groupInstruksi['tanggal_terapi'];
                $listAllInstruksi[$key]['is_verifikasi_dpjp'] = $groupInstruksi['is_verifikasi_dpjp'];
                $listAllInstruksi[$key]['catatan_instruksi'] = $groupInstruksi['catatan_instruksi'];
                $listAllInstruksi[$key]['data_instruksi'] = (new \yii\db\Query())
                    ->from('infoinstruksi_v')
                    ->where([
                        'instruksi_id' => $groupInstruksi['instruksi_id'],
                        'cppt_id' => $groupInstruksi['cppt_id'],
                    ])
                    ->orderBy(['tgl_instruksi' => SORT_DESC])
                    ->all();
                $listAllInstruksi[$key]['data_implementasi'] = (new \yii\db\Query())
                    ->from('infoimplementasi_v')
                    ->where([
                        'instruksi_id' => $groupInstruksi['instruksi_id']
                    ])
                    ->orderBy(['tgl_implementasi' => SORT_DESC])
                    ->all();
                // Yang Belum Implementasi Tidak Ditampilkan
                if ($listAllInstruksi[$key]['data_implementasi'] == []) {
                    unset($listAllInstruksi[$key]);
                }
            }
        }
        $print = new DocoPrint();
        $print->attributes = [
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
            '#inf_nokamar#' => $resultHeader ? $resultHeader['kamarruangan_nokamar'] : '',
            '#inf_nobed#' => $resultHeader ? $resultHeader['no_tempattidur'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#table_instruksi_implementasi#' => $this->renderPartial('cetakan', ['data' => $listAllInstruksi, 'header1' => $header1, 'header2' => $header2]),
            '#no_pendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#nama_pasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }

    public function integrateTindakan($pendaftaran_id)
    {
        try {
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'is_jurnal' => 'f'])->all();

            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if ($config->is_akunting != null) {
                if (!empty($tindakan)) {
                    foreach ($tindakan as $key => $value) {
                        $harga = $value->harga;
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->komponentarif_kode,
                            'xtransaction_at' => $value->tanggal_transaksi,
                            'xamount' => $harga,
                            'xdiscount_amount' => $value->diskon,
                            'xamount_netto' => 0,
                            'xamount_ppn' => 0,
                            'xmedical_number' => $value->rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => 'DITAGIHKAN',
                        ];
                        $Tindakankomponen = Tindakankomponen::findOne($value->id);
                        $Tindakankomponen->is_jurnal = true;
                        $Tindakankomponen->update();
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actionIntegrateBmhp($pendaftaran_id)
    {
        try {
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $bmhp = SyncPengeluaranobat::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'jenis' => 'BMHP', 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if ($config->is_akunting != null) {
                if (!empty($bmhp)) {
                    foreach ($bmhp as $key => $value) {
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->jenisobatalkes_kode,
                            'xtransaction_at' => $value->tgl_transaksi,
                            'xamount' => $value->harga,
                            'xdiscount_amount' => $value->discount,
                            'xamount_netto' => $value->harga_netto,
                            'xamount_ppn' => $value->jmlppn,
                            'xmedical_number' => $value->no_rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => $value->is_ditagihkan,
                        ];
                        $obatalkes = ObatAlkesPasien::findOne($value->id);
                        $obatalkes->is_jurnal = true;
                        $obatalkes->scenario = "jurnal";
                        $obatalkes->update();
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    private function infoPasien($admisi)
    {
        $data = [];
        $data = InfoPasienRiView::find()->select(['pasienadmisi_id', 'kelas_ditagihkan_id', 'is_pasientitipan', 'no_pendaftaran', 'kelaspelayanan_id', 'is_stoppasientitipan'])->where(['pasienadmisi_id' => $admisi])->asArray()->one();
        return $data;
    }

    public function actionGetImplementationConfig()
    {
        return Yii::$app->docoPlugin->execute('implementation_button');
    }
}
