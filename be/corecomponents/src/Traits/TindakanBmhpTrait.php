<?php

namespace Doco\Traits;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InstruksiTindakanBmhp;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\models\Lookup;
use Doco\models\LookupTransaksi;
use Doco\models\InfoStokObatAlkesFnr;
use Doco\models\PaketDetailView;
use Doco\models\PegawaiView;
use Doco\models\Ruangan;
use Doco\models\TarifTotalFn;
use Doco\models\TindakanBmhpView;
use Doco\models\WorklistPasien;
use Doco\models\KonfigTarif;
use Doco\models\LoginForm;
use app\modules\v1\models\Instruksi;
use Doco\models\ObatAlkesPasien;
use Doco\models\TindakanPelayanan;
use Doco\models\Pendaftaran;
use Doco\Services\KasirService;
use Doco\models\PasienKirimUnitlain;
use Doco\models\BatalOrderPenunjangT;
use Doco\models\PermintaanKepenunjangan;
use Doco\models\PasienMasukPenunjangT;
use yii\helpers\ArrayHelper;
use Doco\models\RencanaOperasi;
use Doco\models\KonfigPelayanan;
use Doco\models\Spesialis;
use Doco\models\Reseptur;
use Doco\models\PenjualanResep;
use Doco\models\RiwayatTindakanView;
use SirsCore\features\FeatureTindakanBmhp;

trait TindakanBmhpTrait
{
    public $type;

    /**
     * cek kelas tagihan
     */
    protected function getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id)
    {
        /**
         * untuk pengambilan data kelas ditagihkan perlu improve query lagi setelah perbaikan bugs pindah kamar di pelayanan (pasienadmisi_t)
         */
        $admisi = "SELECT pendaftaran_id, kelaspelayanan_id, kelas_ditagihkan_id, is_pasientitipan
            FROM infopasienri_v
            WHERE pendaftaran_id = {$pendaftaran_id}";
        $admisi = Yii::$app->db->createCommand($admisi)->queryOne();
        $kelasPelayananId = $kelaspelayanan_id;
        if($admisi) {
            if(!empty($admisi)) {
                if(!empty($admisi['kelas_ditagihkan_id'])) {
                    $kelasPelayananId = $admisi['kelas_ditagihkan_id'];
                }
            }
        }
        return $kelasPelayananId;
    }

    /**
     * This function will return data bundle tindakan bmhp
     * bundle list : [kunjungan | all tindakan | all paket | all dokter | all perawat | jenis obat | default depo]
     *
     * @param String var
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionBundleDataTindakanBmhp()
    {

            $request = Yii::$app->request;
            $no_pendaftaran = $request->get('no_pendaftaran', 0);
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $pegawai_id = $request->get('pegawai_id', null);
            $spesialis_id = $request->get('spesialis_id', null);
            $kelompokpegawai_id = $request->get('kelompokpegawai_id', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $constCathlab = $this->constans->actionGetId('kdtindakan_cathlab');
            $konfigFormulir = (new KonfigPelayanan)->getAdditionalConditions([
                'nama_fitur' => 'tindakan',
                'instalasi_id' => Yii::$app->jwt->instalasi_id
            ]);
            $useDefaultDpjp = $this->constans->actionGetId('use_default_dpjp');
            switch ($this->type) {
                case 'RJ':
                    $constDepo = 'apotek_rj';
                    break;
                case 'RI':
                    $constDepo = 'apotek_ri';
                    break;
                case 'RD':
                    $constDepo = 'apotek_rd';
                    break;
                default:
                    $constDepo = 'apotek_rd';
            }

            $default_depo = $this->constans->actionGetId($constDepo);
            $data_kunjungan = $this->registrationData($pendaftaran_id, $no_pendaftaran, $pegawai_id, $spesialis_id);
            if (empty($data_kunjungan)) {
                return $this->responseJson(400, 'Pendaftaran tidak ditemukan');
            }

            // $ruangan_id = isset($data_kunjungan['ruangan_id']) ? $data_kunjungan['ruangan_id'] : 0;
            $kelaspelayanan_id = isset($data_kunjungan['kelaspelayanan_id']) ? $data_kunjungan['kelaspelayanan_id'] : 0;
            /* cek kelas tagihan */
            $kelaspelayanan_id = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
            $penjamin_id = isset($data_kunjungan['penjamin_id']) ? $data_kunjungan['penjamin_id'] : 0;
            $kelompokPegawaiId =DocoConstansId::actionGetAdditional('kelompokpegawai_id_nakes',true);
            $data_pegawai = [];
            $data_dokter = $this->employeeByWard($ruangan_id, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS);
            $data_perawat = $this->perawatByWard($ruangan_id, $kelompokPegawaiId);
            $data_spesialis = $this->spesialis();
            $data_tindakanbmhp = [];
            $tindakanWithPackage = $this->tindakanWithBmhpStock(compact('ruangan_id', 'kelaspelayanan_id', 'penjamin_id'), $default_depo);
            $filterJenisObat = ['lookup_type' => DocoConstants::GROUP_JENIS_OBAT];
            if ($kelompokpegawai_id == DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
                $result = LookupTransaksi::find()
                    ->select(['additional_value'])
                    ->where(['kode_transaksi' => DocoConstants::GOUP_ALKES_PERAWAT_BMHP])
                    ->scalar();
                $result = json_decode($result, true);
                if (!empty($result)) {
                    $filterJenisObat['lookup_id'] = $result;
                }
            }
            $jenisObat = Lookup::find()->select(['lookup_id', 'lookup_name'])
                ->where($filterJenisObat)->all();

            $ruangan_depo_nama = '';
            $ruangan_depo = Ruangan::find()
                ->select(['ruangan_id', 'ruangan_nama'])
                ->where(['ruangan_id' => $default_depo])
                ->one();
            if ($ruangan_depo) {
                $ruangan_depo_nama = $ruangan_depo->ruangan_nama;
            }

            $konfig_spesialis = LookupTransaksi::find()
                ->select(['additional_value'])
                ->where(['kode_transaksi' => 'tindakan_spesialis'])
                ->scalar();
            $konfig_spesialis = json_decode($konfig_spesialis, true);
            $konfig_spesialis = isset($konfig_spesialis) ? $konfig_spesialis : false;

            $konfig_tindakan_harga = LookupTransaksi::find()
                ->select(['additional_value'])
                ->where(['kode_transaksi' => 'tindakan_harga_bmhp'])
                ->scalar();
            $konfig_tindakan_harga = json_decode($konfig_tindakan_harga, true);
            $konfig_tindakan_harga = isset($konfig_tindakan_harga) ? $konfig_tindakan_harga : false;

            $konfig_depo_ruangan = LookupTransaksi::find()
                ->select(['additional_value'])
                ->where(['kode_transaksi' => 'depo_ruangan_bmhp'])
                ->scalar();
            $konfig_depo_ruangan = json_decode($konfig_depo_ruangan, true);
            $konfig_depo_ruangan = isset($konfig_depo_ruangan) ? $konfig_depo_ruangan : false;

            if($konfig_depo_ruangan){
                $depo_farmasi_id = Ruangan::find()
                ->select(['ruangan_farmasi_id'])
                ->where(['ruangan_id' => $ruangan_id])
                ->one();

                if($depo_farmasi_id){
                    $depo_farmasi = Ruangan::find()
                    ->select(['ruangan_id', 'ruangan_nama'])
                    ->where(['ruangan_id' => $depo_farmasi_id])
                    ->one();
                    if($depo_farmasi){
                        $default_depo = isset($depo_farmasi['ruangan_id']) ? $depo_farmasi['ruangan_id'] : $default_depo;
                        $ruangan_depo_nama = isset($depo_farmasi['ruangan_nama']) ? $depo_farmasi['ruangan_nama'] : $ruangan_depo_nama;
                    }
                }
            }

            return [
                'data_kunjungan' => $data_kunjungan,
                'data_tindakanruangan' => $tindakanWithPackage['tindakan'],
                'data_paketruangan' => $tindakanWithPackage['paket'],
                'data_dokter' => $data_dokter,
                'data_perawat' => $data_perawat,
                'data_group_obat' => $jenisObat,
                'ruangan_depo_nama' => $ruangan_depo_nama,
                'default_depo' => $default_depo,
                'const_cathlab' => $constCathlab,
                'konfigFormulir' => $konfigFormulir,
                'use_default_dpjp' => $useDefaultDpjp,
                'data_spesialis' => $data_spesialis,
                'konfig_spesialis' => $konfig_spesialis,
                'konfig_tindakan_harga' => $konfig_tindakan_harga,
            ];

    }

    /**
     * This function will mapping data tindakan and package
     *
     * @param String $pendaftaran_id
     * @param String $depo_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListTindakanBmhp($pendaftaran_id, $depo_id, $pegawai_id = null)
    {
        $registrationArray = $this->registrationData($pendaftaran_id, null, $pegawai_id);
        return $this->tindakanWithBmhpStock($registrationArray, $depo_id);
    }

    public function actionListTindakanSpesialis()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');
        $kelaspelayanan_id = $request->get('kelaspelayanan_id');
        $penjamin_id = $request->get('penjamin_id');
        $spesialis_id = $request->get('spesialis_id');
        $tindakanWithPackage = $this->tindakanWithBmhpStock(compact('ruangan_id', 'kelaspelayanan_id', 'penjamin_id','spesialis_id'), $ruangan_id);
        return $tindakanWithPackage;
    }
    /**
     * This function will mapping data tindakan and package
     *
     * @param String $registrationArray
     * @param String $depo_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function tindakanWithBmhpStock($registrationArray, $depo_id)
    {
        $request = Yii::$app->request;
        $spesialis_id = $request->get('spesialis_id');
        $spesialis_id = isset($spesialis_id) ? $spesialis_id : null;
        $ruangan_id = isset($registrationArray['ruangan_id']) ? $registrationArray['ruangan_id'] : null;
        $penjamin_id = isset($registrationArray['penjamin_id']) ? $registrationArray['penjamin_id'] : null;
        $kelaspelayanan_id = isset($registrationArray['kelaspelayanan_id']) ? $registrationArray['kelaspelayanan_id'] : null;
        $data_tindakanruangan = $this->listTindakan($ruangan_id, $penjamin_id, $kelaspelayanan_id, false, $spesialis_id);
        $data_paketruangan = $this->listTindakan($ruangan_id, $penjamin_id, $kelaspelayanan_id, true, $spesialis_id);

        $listDaftarTindakan = $tmpListTindakan = [];
        /**  Untuk Paket */
        $listTipePaket = $listpaket = $qDetailPaket = [];
        /** List Tindakan Ruangan **/

        foreach ($data_tindakanruangan as $key => $value) {
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
                $row['list_tindakan'] = [];
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
            ])
                ->where([
                    'daftartindakan_id' => $listDaftarTindakan
                ])
                ->asArray()
                ->all();

            // collect medicine ids to get data obat
            $medicineIds = [];
            $listStok = [];
            foreach ($qBmhp as $bmhp) {
                if (!in_array($bmhp['obatalkes_id'], $medicineIds)) {
                    $medicineIds[] = $bmhp['obatalkes_id'];
                }
            }
            if (!empty($medicineIds)) {
                $qStok = (new InfoStokObatAlkesFnr(['extParam' => [$penjamin_id, $kelaspelayanan_id, $depo_id]]))->find()->select([
                    'obatalkes_id',
                    'qty_tersedia',
                    'jml_hargajual'
                ])->where(['obatalkes_id' => $medicineIds])->asArray()->all();
                // $qStok = [];

                /** Mapping Obat Ke Stok */
                foreach ($qStok as $value) {
                    $listStok[$value['obatalkes_id']] = $value;
                }
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

                    if (isset($listpaket[$tipePaketId]['list_tindakan'])) {
                        $listpaket[$tipePaketId]['list_tindakan'][] = [
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

        return [
            'tindakan' => $tmpListTindakan,
            'paket' => $listpaket
        ];
    }

    /**
     * This function will return result employee by ward
     *
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

    private function spesialis()
    {
        $query = Spesialis::find()->select([
            'spesialis_id',
            'spesialis_nama'
        ]);
        return $query->asArray()->all();
    }
    private function employeeByWard($wardId, $type = null)
    {
        $query = PegawaiView::find()
            ->select([
                'pegawai_id',
                'nama_pegawai'
            ])
            ->andWhere([
                'ruangan_id' => $wardId
            ]);
        if (!empty($type)) {
            $query->andWhere([
                'kelompokpegawai_namalainnya' => $type
            ]);
        }
        return $query->asArray()->all();
    }
    private function perawatByWard( $wardId, $type = null)
    {
        $query = PegawaiView::find()
            ->select([
                'pegawai_id',
                'nama_pegawai'
            ])
            ->andWhere([
                'ruangan_id' => $wardId
            ]);
        if (!empty($type)) {
            $query->andWhere([
                'kelompokpegawai_id' => $type
            ]);
        }
        return $query->asArray()->all();
    }

    /**
     * this function will return registration data by type
     *
     * @param String $pendaftaran_id
     * @param String $no_pendaftaran
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function registrationData($pendaftaran_id, $no_pendaftaran = null, $pegawai_id = null)
    {
        $query = WorklistPasien::find()
            ->select([
                'ruangan_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'pegawai_id',
                'nama_pegawai'
            ])
            ->andWhere(!empty($pendaftaran_id) ? compact('pendaftaran_id') : compact('no_pendaftaran'));
        if (!is_null($pegawai_id)) {
            $query->andWhere([
                'pegawai_id' => $pegawai_id
            ]);
        }
        if (!empty($this->type) && $this->type != 'RJ') {
            $query->andWhere([
                'jenis' => $this->type
            ]);
        }
        return $query->one();
    }

    /**
     * This function will return tindakan and paket by query
     *
     * @param String $ruangan_id
     * @param String $penjamin_id
     * @param String $kelaspelayanan_id
     * @param String $spesialis_id
     * @param Boolean $isPackage
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function listTindakan($ruangan_id = null, $penjamin_id = null, $kelaspelayanan_id = null, $isPackage = false, $spesialis_id = null)
    {
        $result = [];
        $jenis_tindakan = DocoConstants::JENIS_TINDAKAN_PELAYANAN;
        $konfigTarif = KonfigTarif::find()->select([
            'is_spesialis'
        ])->asArray()->one();
        $params = [
            $ruangan_id,
            $penjamin_id,
            $kelaspelayanan_id,
        ];
        if ($spesialis_id && $konfigTarif['is_spesialis'] ==  true) {
            $params = [
                $ruangan_id,
                $penjamin_id,
                $kelaspelayanan_id,
                $jenis_tindakan,
                $spesialis_id
            ];
        }

        try {
            $foodGroupAction = $this->constans->actionGetAdditional('not_in_kelompoktindakan_m', true);
            $query = (new TarifTotalFn(['extParam' =>  $params]))
                ->find()
                ->select([
                    'daftartindakan_id',
                    'tipepaket_id',
                    'tipepaket_nama',
                    'daftartindakan_nama',
                    'harga_tariftindakan',
                    'is_akomodasi',
                    'persencyto_tindakan',
                    'tariftindakan_id',
                    'kelompoktindakan_id',
                    'is_konsultasi'
                ])
                ->andWhere([$isPackage ? 'IS NOT' : 'IS', 'tipepaket_id', null]);
            if (!$isPackage) {
                $query->andWhere(['NOT IN', 'kelompoktindakan_id', $foodGroupAction]);
            }
            return $query->orderBy(['daftartindakan_nama' => SORT_ASC])
                ->asArray()
                ->all();
        } catch (\Exception $e) {
            $this->logError($e);
            return $result;
        }
    }

    /**
     * This function will return list of depo
     *
     * @param String $page
     * @param String $term
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListDepo($page)
    {
        $term = Yii::$app->request->get('term', null);
        // Set manual limit for showing depo
        $limit = 50;
        $query = Ruangan::find()
            ->select(['ruangan_id as id', 'ruangan_nama as text'])
            ->andWhere([
                'instalasi_id' => DocoConstants::INSTALASI_FARMASI
            ]);
        $query->orWhere([
            'ruangan_id' => Yii::$app->jwt->ruangan_id
        ]);
        if (!empty($term)) {
            $query->andWhere(['like', 'lower(ruangan_nama)', strtolower($term)]);
        }
        return $query
            ->limit($limit)
            ->offset(($page - 1) * ($limit - 1))
            ->asArray()
            ->all();
    }

    public function actionBatalInstruksi()
    {
        // try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $instruksitindakan_id = $request->post('instruksitindakan_id', null);
            $instruksi_id = $request->post('instruksi_id', null);
            $password = $request->post('deleted_by_password', null);
            $tipe_instruksi = $request->post('tipe_instruksi', null);
            $modelLogin = new LoginForm();
            $modelLogin->username = Yii::$app->jwt->user->nama_pemakai;
            $modelLogin->password = $password;
            if (!$modelLogin->validate()) {
                return [
                    'status' => 422,
                    'text' => 'Password Salah',
                ];
            }

            if ($tipe_instruksi == 'reseptur') {
                $noresep = $request->post('noresep', null);
                $alasan_batal = $request->post('alasan_pembatalan', null);
                $return = $this->batalReseptur($noresep, $alasan_batal);
                return $return;
            }

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            $jenis = $request->post('jenis', null);
            $loginpemakai_id = Yii::$app->jwt->user->loginpemakai_id;
            $billModel = $model = null;
            switch ($jenis) {
                case 'tindakan':
                    if(empty($instruksi_id)){
                        $model = TindakanPelayanan::find()
                            ->select([
                                'tindakanpelayanan_id',
                                'tindakansudahbayar_id'
                            ])
                            ->andWhere([
                                'tindakanpelayanan_id' => $instruksitindakan_id
                            ])->one();
    
                        if (!is_null($billModel)) {
                            $dataDetail['tindakan'] = [
                                'tindakanpelayanan_id' => $billModel->tindakanpelayanan_id
                            ];
                        }

                    }else{

                        $model = new InstruksiTindakan;
                        $billModel = TindakanPelayanan::find()
                            ->select([
                                'tindakanpelayanan_id',
                                'tindakansudahbayar_id'
                            ])
                            ->andWhere([
                                'instruksitindakan_id' => $instruksitindakan_id
                            ])->one();
    
                        if (!is_null($billModel)) {
                            $dataDetail['tindakan'] = [
                                'tindakanpelayanan_id' => $billModel->tindakanpelayanan_id
                            ];
                        }
                    }
                    break;
                case 'bmhp':
                    $model = new InstruksiTindakanBmhp;
                    $billModel = ObatAlkesPasien::find()
                        ->select([
                            'obatalkespasien_id',
                            'obatsudahbayar_id'
                        ])
                        ->andWhere([
                            'instruksitindakanbmhp_id' => $instruksitindakan_id
                        ])->one();

                    if (!is_null($billModel)) {
                        $dataDetail['obat'] = [
                            'obatalkespasien_id' => $billModel->obatalkespasien_id
                        ];
                    }
                    break;
                default:
                    return [
                        'status' => 422,
                        'text' => 'Data tidak ditemukan.'
                    ];
                    break;
            }

            $model = $model->findOne($instruksitindakan_id);
            if (!empty($model)) {
                $instruksi_id = $model->instruksi_id;
                $model->deleted_by = $loginpemakai_id;
                $model->deleted_date = date('Y-m-d H:i:s');
                $model->alasan_batal = $request->post('alasan_pembatalan', null);
                $model->is_deleted = true;

                if (!is_null($billModel)) {
                    $no_pendaftaran = Pendaftaran::find()->select(['no_pendaftaran'])->where(['pendaftaran_id' => $pendaftaran_id])->scalar();
                    if (is_null($no_pendaftaran)) {
                        throw new \Exception("No Pendaftaran Tidak Ditemukan", 1);
                    }
                    $tagihanBatal = (new KasirService)->batalTagihan(
                        [
                            'no_pendaftaran' => $no_pendaftaran
                        ],
                        $dataDetail,
                        $model->alasan_batal
                    );
                    if (isset($tagihanBatal['meta']) && $tagihanBatal['meta']['code'] >= 400) {
                        Yii::$app->response->statusCode = 500;
                        $transaction->rollBack();
                        $e = isset($tagihanBatal['message']) ? $tagihanBatal['message'] : 'Terjadi Kesalahan API';
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => $e
                        ]);
                    }

                    
                    if(isset($dataDetail['obat']['obatalkespasien_id'])) {
                        FeatureTindakanBmhp::hapusTindakanBmhp(
                            [$dataDetail['obat']['obatalkespasien_id']],
                            $model->alasan_batal);
                    }

                }

                if ($model->validate() && $model->save()) {
                    $cekInstruksiTindakan = InstruksiTindakan::find()->where(['instruksi_id' => $instruksi_id])->count();
                    $cekInstruksiTindakanBmhp = InstruksiTindakanBmhp::find()->where(['instruksi_id' => $instruksi_id])->count();
                    if ($cekInstruksiTindakan == 0 && $cekInstruksiTindakanBmhp == 0) {
                        $modelInstruksi = Instruksi::findOne($instruksi_id);
                        if ($modelInstruksi) {
                            $modelInstruksi->is_deleted = true;
                            $modelInstruksi->deleted_by = $loginpemakai_id;
                            $modelInstruksi->deleted_date = date('Y-m-d H:i:s');
                            $modelInstruksi->save();
                        }
                    }
                    $transaction->commit();
                    $result = [
                        'title' => 'Proses Berhasil!',
                        'text' => 'Berhasil menghapus data.',
                    ];
                } else {
                    $transaction->rollBack();
                    throw new \yii\db\Exception('Gagal menghapus data', $model->getErrors(), 500);
                }
            } else {
                $transaction->rollBack();
                $result = [
                    'status' => 422,
                    'text' => 'Data tidak ditemukan.'
                ];
            }
            return $result;
        // } catch (\yii\db\Exception $e) {
        //     $transaction->rollBack();
        //     \Yii::$app->response->statusCode = 500;
        //     $this->logError($e);
        //     return [
        //         'message' => 'Terjadi kesalahan pada server'
        //     ];
        // } catch (\Exception $e) {
        //     $transaction->rollBack();
        //     \Yii::$app->response->statusCode = 500;
        //     $this->logError($e);
        //     return [
        //         'message' => 'Terjadi kesalahan pada server'
        //     ];
        // }
    }

    public function actionHistoryPembatalan()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $instruksitindakan_id = $request->get('instruksitindakan_id', null);
            $noresep = $request->get('noresep', null);
            if(!empty($instruksitindakan_id)) {
                return InfoInstruksiView::find()
                    ->select(['tgl_batal', 'pegawai_hapus_nama', 'alasan_batal', 'qty', 'qty_sisa' , 'tipe_instruksi'])
                    ->where(['pendaftaran_id' => $pendaftaran_id, 'instruksitindakan_id' => $instruksitindakan_id])->one();
            } else if(!empty($noresep)) {
                return InfoInstruksiView::find()
                    ->select(['tgl_batal', 'pegawai_hapus_nama', 'alasan_batal', 'qty', 'qty_sisa' , 'tipe_instruksi'])
                    ->where(['pendaftaran_id' => $pendaftaran_id, 'noresep' => $noresep])->one();
            
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => 'Terjadi kesalahan pada server'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => 'Terjadi kesalahan pada server'
            ];
        }
    }

    public function actionBatalPenunjang()
    {
        $request = Yii::$app->request;
        /**
         * di infoinstruksi_v, kolom permintaankepenunjang_id diberi alias instruksitindakan_id
         * sehingga diberi pengkondisian berdasarkan parameter yang dikirim
         */
        $reqInstTindId = $request->post('instruksitindakan_id', null);
        $reqPermKepId = $request->post('permintaankepenunjang_id', null);

        $permintaankepenunjang_id = isset($reqPermKepId) ? $reqPermKepId : $reqInstTindId;
        $instalasi_id = $request->post('instalasi_id', null);
        $alasan_pembatalan = $request->post('alasan_pembatalan', null);
        $password = $request->post('deleted_by_password', null);
        $modelLogin = new LoginForm();
        $modelLogin->username = Yii::$app->jwt->user->nama_pemakai;
        $modelLogin->password = $password;
        if (!$modelLogin->validate()) {
            return [
                'status' => 422,
                'text' => 'Password Salah',
            ];
        }

        $getPermintaanKepenunjangData = PermintaanKepenunjangan::find()->select([
            'permintaankepenunjang_t.permintaankepenunjang_id',
            'permintaankepenunjang_t.tindakanpelayanan_id',
            'pasienkirimkeunitlain_t.pasienkirimkeunitlain_id',
            'pasienkirimkeunitlain_t.pasienmasukpenunjang_id',
            'pasienkirimkeunitlain_t.status_penunjang',
            'pasienmasukpenunjang_t.no_masukpenunjang'
        ])
            ->rightJoin('pasienkirimkeunitlain_t', 'permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id')
            ->leftJoin('pasienmasukpenunjang_t', 'pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
            ->andWhere([
                'permintaankepenunjang_t.permintaankepenunjang_id' => $permintaankepenunjang_id
            ])
            ->asArray()->one();

        $pasienkirimkeunitlain_id = ArrayHelper::getValue($getPermintaanKepenunjangData, 'pasienkirimkeunitlain_id');
        $tindakanpelayanan_id = ArrayHelper::getValue($getPermintaanKepenunjangData, 'tindakanpelayanan_id');
        $pasienmasukpenunjang_id = ArrayHelper::getValue($getPermintaanKepenunjangData, 'pasienmasukpenunjang_id');
        $no_masukpenunjang = ArrayHelper::getValue($getPermintaanKepenunjangData, 'no_masukpenunjang');

        $transaction = Yii::$app->db->beginTransaction();

        $modelPermintaanKepenunjang = PermintaanKepenunjangan::find()->where([
            'permintaankepenunjang_id' => $permintaankepenunjang_id
        ])->one();
        
        $modelPermintaanKepenunjang->attributes = [
            'is_deleted' => true,
            'deleted_date' => date('Y-m-d H:i:s'),
            'deleted_by' => Yii::$app->jwt->user->loginpemakai_id,
            'alasan_batal' => $alasan_pembatalan,
        ];

        if (!$modelPermintaanKepenunjang->save()) {
            $transaction->rollBack();
            Yii::error([
                'model-permintaan-kepenunjang' => $modelPermintaanKepenunjang->errors()
            ]);
            return $this->helper->response([
                'message' => 'Pembatalan Tindakan Gagal Dilakukan'
            ], 422);
        }
        if ($pasienkirimkeunitlain_id) {
            $countPermintaanKepenunjang = PermintaanKepenunjangan::find()->select([
                'permintaankepenunjang_id'
            ])->where([
                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
            ])->count();
            if (!$countPermintaanKepenunjang) {
                $modelPasienUnitLain = PasienKirimUnitlain::find()->where([
                    'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                ])->one();
                Yii::error($modelPasienUnitLain);
                $modelPasienUnitLain->attributes = [
                    'status_penunjang' => DocoConstants::BTL_APPROVE,
                    'is_deleted' => true,
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'deleted_by' => Yii::$app->jwt->user->loginpemakai_id
                ];
                if (!$modelPasienUnitLain->save()) {
                    $transaction->rollBack();
                    Yii::error([
                        'model-pasien-unitlain' => $modelPasienUnitLain->errors()
                    ]);
                    return $this->helper->response([
                        'message' => 'Pembatalan Tindakan Gagal Dilakukan'
                    ], 422);
                }

                $modelBatalOrder = BatalOrderPenunjangT::find()->where([
                    'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                ])->one();
                if (is_null($modelBatalOrder)) {
                    $modelBatalOrder = new BatalOrderPenunjangT();
                }
                $modelBatalOrder->attributes = [
                    'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
                    'tgl_batalorder' => date('Y-m-d H:i:s'),
                    'peg_menyetujui_id' => Yii::$app->jwt->user->pegawai_id,
                    'alasan' => $alasan_pembatalan,
                    'additional_data' => json_encode([
                        'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                    ]),
                ];

                if (!$modelBatalOrder->save()) {
                    $transaction->rollBack();
                    Yii::error([
                        'model-batal-order' => $modelBatalOrder->errors()
                    ]);
                    return $this->helper->response([
                        'message' => 'Pembatalan Tindakan Gagal Dilakukan'
                    ], 422);
                }

                if ($pasienmasukpenunjang_id) {
                    $modelPasienMasukPenunjang = PasienMasukPenunjangT::find()->where([
                        'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id
                    ])->one();
                    $modelPasienMasukPenunjang->status_periksa = DocoConstants::ST_P_PEN_BTL;
                    if (!$modelPasienMasukPenunjang->save()) {
                        $transaction->rollBack();
                        Yii::error([
                            'model-pasien-masukpenunjang' => $modelPasienMasukPenunjang->errors()
                        ]);
                        return $this->helper->response([
                            'message' => 'Pembatalan Tindakan Gagal Dilakukan'
                        ], 422);
                    }
                }

                if ($instalasi_id == DocoConstants::INST_ID_BEDAH) {
                    $modelRencanaOperasi = RencanaOperasi::find()->where([
                        'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                    ])->one();
                    $modelRencanaOperasi->attributes = [
                        'is_deleted' => true,
                        'deleted_date' => date('Y-m-d H:i:s'),
                        'deleted_by' => Yii::$app->jwt->user->loginpemakai_id
                    ];
                    if (!$modelRencanaOperasi->save()) {
                        $transaction->rollBack();
                        Yii::error([
                            'model-pasien-unitlain' => $modelRencanaOperasi->errors()
                        ]);
                        return $this->helper->response([
                            'message' => 'Pembatalan Tindakan Gagal Dilakukan'
                        ], 422);
                    }
                }
            }
        }

        if ($no_masukpenunjang && $tindakanpelayanan_id) {
            $registrationData = [
                'no_masukpenunjang' => $no_masukpenunjang,
                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                'instalasi_id' => $instalasi_id,
                'tgl_transaksi' => date('Y-m-d H:i:00'),
                'detail_tindakan' => [
                    'tindakanpelayanan_id' => $tindakanpelayanan_id
                ]
            ];

            $tagihanBatal = (new KasirService)->batalTagihan(
                $registrationData,
                [],
                $alasan_pembatalan
            );
            if (isset($tagihanBatal['meta']) && $tagihanBatal['meta']['code'] >= 400) {
                Yii::$app->response->statusCode = 500;
                $transaction->rollBack();
                $e = isset($tagihanBatal['message']) ? $tagihanBatal['message'] : 'Terjadi Kesalahan API';
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => $e
                ]);
            }
        }
        $transaction->commit();
        return $this->helper->response([
            'message' => 'Pembatalan Tindakan Berhasil Dilakukan!'
        ]);
    }

    private function batalReseptur($no_resep, $alasan_batal) {        
        $reseptur = Reseptur::find()->where(['noresep' => $no_resep])->one();
        if(empty($reseptur)) {
            return $this->helper->response([
                'message' => 'Data Reseptur Tidak Ditemukan'
            ], 422);
        }
        $penjualan = PenjualanResep::find(true)->where(['reseptur_id' => $reseptur->reseptur_id])->one();
        if(empty($penjualan)) {
            $reseptur->status_reseptur = DocoConstants::VAR_B_R;
            $reseptur->alasan_batal = $alasan_batal;
            $reseptur->deleted_by = Yii::$app->jwt->user->loginpemakai_id;
            $reseptur->deleted_date = date('Y-m-d H:i:s');
            if($reseptur->update()) {
                return $this->helper->response([
                    'message' => 'Pembatalan Tindakan Berhasil Dilakukan!'
                ]);
            } else {
                return $this->helper->response([
                    'message' => 'Pembatalan Tindakan Gagal Dilakukan'
                ], 422);
            }
        } else {
            $reseptur->status_reseptur = DocoConstants::VAR_B_R;
            $reseptur->alasan_batal = $alasan_batal;
            $reseptur->deleted_by = Yii::$app->jwt->user->loginpemakai_id;
            $reseptur->deleted_date = date('Y-m-d H:i:s');
            if($reseptur->update()) {
                $response = Yii::$app->docoRest->apotek->delete('inf-reseptur/batal-resep', [
                    'query' => [
                        'no_resep' => $no_resep,
                        'alasan_batal' => $alasan_batal
                    ]
                ]);
                $result = json_decode($response->getBody(), true);
                return $result;
            } else {
                return $this->helper->response([
                    'message' => 'Pembatalan Tindakan Gagal Dilakukan'
                ], 422);
            }
        }
    }

    public function actionTindakanBmhp()
    {
        $model = new RiwayatTindakanView;
        $query = $model::find(true);

        $request = Yii::$app->request;
        $no_pendaftaran = $request->get('no_pendaftaran', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $limit = Yii::$app->request->get('length', 10);
        $offset = Yii::$app->request->get('start', 0);

        $getTindakan = $query->select([
                    'tindakanbmhp_is_deleted',
                    'tindakansudahbayar_id',
                    'pasienpulang_id',
                    'tgl_tindakan',
                    'tindakan_obat',
                    'dokter_pemeriksa',
                    'dokter_delegasi',
                    'perawat_1',
                    'perawat_2',
                    'qty'
                    ])->where(['no_pendaftaran' => $no_pendaftaran,'ruangan_pendaftaran_id'=>$ruangan_id])
                    ->andWhere(['tipe_pelayanan' => 'TINDAKAN', 'tindakanbmhp_is_deleted' => false])
                    ->orderBy(['tgl_tindakan'=>SORT_DESC]);

        $totalDataCount = $getTindakan->count();
        $data = $getTindakan->limit($limit)->offset($offset)->asArray()->all();

        return [
            'data' => $data,
            '_meta' => [
                'totalCount' => $totalDataCount
            ]
        ];
    }

}
