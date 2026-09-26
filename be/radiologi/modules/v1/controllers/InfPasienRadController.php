<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi pasien radiologi
 * @copyright 26 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoAkunting;
use Doco\components\DocoMessages;
use Doco\Traits\TindakanPenunjangTrait;

// model
use app\modules\v1\models\InfoPasienRadView;
use app\modules\v1\models\InfoPasienRadDetailView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\RujukanDari;
use app\modules\v1\models\BatalPeriksaPenunjangT;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\PemeriksaanPasienRadiologiView;
use app\modules\v1\models\PermintaanKepenunjangan;
use app\modules\v1\models\PerujukView;
use app\modules\v1\models\ObatAlkesPasien;
use Doco\components\NoCountDataProvider;
use Doco\models\TindakanPelayanan;
use Doco\Services\KasirService;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\models\Lookup;

class InfPasienRadController extends DocoActiveController
{
    use TindakanPenunjangTrait;
    public $modelClass = 'app\modules\v1\models\InfoPasienRadView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-display-antrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new PemeriksaanPasienRadiologiView;
            $query = $model::find();
            $query->noInStatusPeriksa(DocoConstants::BTL_APPROVE);
            $query->noInStatusPeriksa(null);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';
            $status = null;

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                    $between = true;
                }
                if(isset($_GET['advanced-filter']['tipe_pasien'])){
                    $_GET['advanced-filter']['tipe_pasien'] = DocoConstants::$asal_rujukan[$_GET['advanced-filter']['tipe_pasien']];
                }

                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']);
                    $between = true;
                }

                // if(!isset($_GET['advanced-filter']['type'])) {
                //     $query->andWhere(['or',
                //         ['status_batal' => false],
                //         ['status_batal' => null]
                //     ]);
                // }

                if(isset($_GET['advanced-filter']['type'])) {
                    $type = $_GET['advanced-filter']['type'];
                    if($type != 0 ){
                        unset($_GET['advanced-filter']['status_periksa']);
                    }
                    if($type == 1) {
                        $query->andWhere(['is_selesai' => true, 'status_batal' => false]);
                    }
                    elseif($type == 2) {
                        $query->andWhere(['is_hasil' => false]);
                        $query->andWhere(['not',['status_batal' => true]]);
                    }
                    elseif($type == 3) {
                        $query->andWhere(['is_hasil' => true, 'is_selesai' => false, 'status_batal' => false]);
                    }
                    elseif($type == 4) {
                        // $query->andWhere(['or',
                                        // ['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB],
                        //                 ['status_batal' => true]
                                    // ]);
                    }
                    elseif($type == 5) {
                        $query->andWhere([
                            'cyto_tindakan' => true, 
                        ]);
                    }
                }elseif(isset($_GET['advanced-filter']['status_periksa'])) {
                    $status = $_GET['advanced-filter']['status_periksa'];
                    unset($_GET['advanced-filter']['status_periksa']);
                    if($status == DocoConstants::ST_SELESAI) {
                        $query->andWhere(['is_selesai' => true, 'status_batal' => false]);
                        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
                    }
                    elseif($status == DocoConstants::LAB_BELUM_PERIKSA) {
                        $query->andWhere(['is_hasil' => false]);
                        $query->andWhere(['not',['status_batal' => true]]);
                        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
                    }
                    elseif($status == DocoConstants::ST_PERIKSA) {
                        $query->andWhere(['is_hasil' => true, 'is_selesai' => false, 'status_batal' => false]);
                        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
                    }
                    elseif($status == DocoConstants::BTL_PERIKSA_LAB) {
                        $query->andWhere(['or',
                                        ['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB],
                                        ['status_batal' => true]
                                    ]);
                    }
                }

                if (isset($_GET['advanced-filter']['tgl_hasilrad'])) {
                    if ($_GET['advanced-filter']['tgl_hasilrad'] == DocoConstants::STATUS_EXPERTISE_SUDAH) {
                        $query->andWhere(['not', ['tgl_hasilrad' => null]]);
                    } else {
                        $query->andWhere(['tgl_hasilrad' => null]);
                    }
                    unset($_GET['advanced-filter']['tgl_hasilrad']);
                }

                if(isset($_GET['advanced-filter']['type'])) {
                    if ($_GET['advanced-filter']['type'] == 4) {
                        $query->andWhere(['or',
                            ['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB],
                            ['status_batal' => true]
                        ]);
                    } else {
                        $query->andWhere(['status_batal' => false]);
                        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
                    }
                } else {
                    // Hide pasien batal periksa ketika tidak memakai filter status periksa
                    if (!$status) {
                        $query->andWhere(['status_batal' => false]);
                        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
                    }
                }
            } else {
                $query->andWhere([ 'status_batal' => false ]);
                $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
            }

            $query->betweenTglMasuk($start, $end);

            if (!empty($startLahir) && !empty($endLahir) && $between) {
                $query->betweenTglLahir($startLahir, $endLahir);
            }
            /**
             * End Special Condition date range
             **/

            $query->andWhere(['tindakanpelayananasal_id' => null]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new NoCountDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        }
    }

    public function actionListInstalasi()
    {
        $data = Instalasi::find()->where(['is_active' => 't'])->orderBy('instalasi_id');
        $items = ArrayHelper::map($data->all(), 'instalasi_id', 'instalasi_nama');

        return $items;
    }

    public function actionListRuangan($instalasi_id = null, $singkatan = null)
    {
        $data = Ruangan::find()->joinWith('instalasi');
        $data->where(['ruangan_m.is_active' => 't']);
        if ($instalasi_id) {
            $data->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
        }
        if ($singkatan) {
            $data->andWhere(['instalasi_m.instalasi_singkatan' => $singkatan]);
        }
        $data->orderBy('ruangan_m.ruangan_nama');

        $items = ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama');
        return $items;
    }

    public function actionListAsalRujukan()
    {
        $data = AsalRujukan::find()->where(['is_active' => 't'])->orderBy('asalrujukan_id');
        $items = ArrayHelper::map($data->all(), 'asalrujukan_id', 'asalrujukan_nama');

        return $items;
    }

    public function actionListAsalRujukanDari($asalrujukan_id = null)
    {
        $data = RujukanDari::find()->where(['is_active' => 't']);
        if ($asalrujukan_id) {
            $data->andWhere(['asalrujukan_id' => $asalrujukan_id]);
        }
        $data->orderBy('rujukandari_id');
        $items = ArrayHelper::map($data->all(), 'rujukandari_id', 'nama_perujuk');

        return $items;
    }


    public function actionProsesBatal(){
        $post = \Yii::$app->request->post();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();

        try {

            $PasienMasukPenunjangT = PasienMasukPenunjangT::findOne(['pasienmasukpenunjang_id'=>$post['pasienmasukpenunjang_id']]);
            $pendaftaran_id = $PasienMasukPenunjangT->pendaftaran_id;
            if(!empty($PasienMasukPenunjangT)){


                //  proses input batal
                $tgl_batalperiksa = "";
                if (!empty($post['tgl_batalperiksa'])) {
                    $tgl_batalperiksa = date('Y-m-d H:i:s', strtotime($post['tgl_batalperiksa']));
                }


                $inputBatalOrder = array(
                    'pasienmasukpenunjang_id' => $post['pasienmasukpenunjang_id'],
                    'tgl_batalperiksa' => $tgl_batalperiksa,
                    'peg_menyetujui_id' => $post['peg_menyetujui_id'],
                    'alasan' => $post['alasan'],
                    'additional_data' => json_encode(array('pasienmasukpenunjang_id' => $post['pasienmasukpenunjang_id'])),
                );

                $mBatalPeriksaPenunjangT = new BatalPeriksaPenunjangT;
                $mBatalPeriksaPenunjangT->attributes = $inputBatalOrder;

                if ($mBatalPeriksaPenunjangT->save()) {

                    // update pasien kirim unit lain
                    $PasienMasukPenunjangT->status_periksa = DocoConstants::BTL_PERIKSA_LAB;
                    $PasienMasukPenunjangT->save();
                    // update pasien kirim unit lain
                    $transaction->commit();
                    // Integrasi Akunting
                    $this->integrateTindakanCrud($pendaftaran_id, 'DELETE');
                    $this->integrateBmhpTrx($pendaftaran_id, 'DELETE');
                    $result = [
                        'status' => 200,
                        'title' => 'Input Berhasil',
                        'text' => 'Pembatalan Periksa Berhasil'
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 500;
                    $result['title'] = 'Gagal insert';
                    $result['text'] = $mBatalPeriksaPenunjangT->getErrors();
                }

                // proses input batal


            }else{
                $transaction->rollBack();
                $result['status'] = 500;
                $result['title'] = 'Gagal insert';
                $result['text'] = 'Karena tidak dikenali';
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    public function actionDetailPeriksa($pasienmasukpenunjang_id = null){
        try {
            $return = array('labDetail' => array());
            $request = Yii::$app->request;
            $model = new InfoPasienRadView;
            $query = $model::findOne(['pasienmasukpenunjang_id'=>$pasienmasukpenunjang_id]);

            if(!empty($query)){
                $return['labDetail'] = $query;
            }

            return $return;
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

    public function actionGetPemeriksaanView($id)
    {
        // Try catch
        try {
            // Define model
            $model = new InfoPasienRadDetailView;
            $query = $model::find()->where(['pasienmasukpenunjang_id' => $id]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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


    /**
     * @controller actionPrintRincian
     * @attribute #tanggal# => Tanggal Pendaftaran
     * @attribute #no_pendaftaran# => Nomor Pendaftaran
     * @attribute #no_rm# => Nomor Rekam Medik
     * @attribute #nama# => Nama pasien
     * @attribute #jenis_kasus_penyakit# => Jenis Kasus Penyakit
     * @attribute #ruangan# => Ruangan
     * @attribute #dokter# => Nama dokter
     * @attribute #kelas_pelayanan# => Kelas Pelayanan
     * @attribute #penjamin# => Penjamin
     * @attribute #cara_bayar# => Cara Bayar
     * @attribute #status_bayar# => Status Bayar
     * @attribute #total_tagihan# => Menampilkan detail tindakan
     * @attribute #riwayat_pembayaran# => Menampilkan Tabel tagihan riwayat pasien
     * @attribute #detail_tindakan# => Menampilkan detail tindakan
     * @attribute #total_uang_muka# => Menampilkan detail tindakan
     * @attribute #total_dibayar# => Menampilkan detail tindakan
     * @attribute #sisa_tagihan# => Menampilkan detail tindakan
     * @attribute #biaya_admin# => Menampilkan biaya Admin
     * @attribute #pembulatann# => Menampilkan biaya Pembulatan
     * @attribute #sub_assuransi# => Menampilkan biaya Sub sidi asuransi
     * @attribute #detail_tindakan# => Tindakan Laboratorium
     *
     **/

    public function actionPrintRincian($id)
    {
        $header = Yii::$app->db->createCommand("
            SELECT
              tipe_pasien,
              pasienmasukpenunjang_id,
              pendaftaran_id,
              tgl_pendaftaran,
              no_pendaftaran,
              no_rekam_medik,
              nama_pasien,
              jeniskasuspenyakit_nama,
              dokter,
              ruangan_nama,
              kelaspelayanan_nama,
              penjamin_nama,
              carabayar_nama,
              status_bayar,
              SUM(total_tagihan) AS total_tagihan,
              SUM(total_sdh_bayar) AS total_sdh_bayar,
              SUM(total_sisa_tagihan) AS total_sisa_tagihan,
              SUM(total_uangmuka) AS total_uangmuka
            FROM rincianpasienrad_v
            WHERE pasienmasukpenunjang_id = {$id}
            GROUP BY tipe_pasien,pasienmasukpenunjang_id,pendaftaran_id,
            tgl_pendaftaran,no_pendaftaran,no_rekam_medik,nama_pasien,jeniskasuspenyakit_nama,
            dokter,ruangan_nama,kelaspelayanan_nama,penjamin_nama,carabayar_nama,status_bayar
        ")->queryOne();
        $pendaftaranId = isset($header['pendaftaran_id']) ? $header['pendaftaran_id'] : null;
        $tagihan_detail = Yii::$app->db->createCommand("
            SELECT * FROM rincian_header_penunjang_view WHERE pasienmasukpenunjang_id = {$id}
        ")->queryOne();
        
        $where = '';
        if(!empty($pendaftaranId)) {
            $where .= ' AND pendaftaran_id = '.$pendaftaranId.'';
        }
        $detail = Yii::$app->db->createCommand("
            SELECT * FROM infotagihandetail_v WHERE pasienmasukpenunjang_id = {$id} {$where}
        ")->queryAll();
        $listData = [];
        foreach ($detail as $value) {
            $is_obat = isset($value['is_obat']) ? $value['is_obat'] : null;
            $instalasi = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
            $ruangan = isset($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : null;
            if (!isset($listData[$value['pasienmasukpenunjang_id']])) {
                $listData[$value['pasienmasukpenunjang_id']] = [
                    'obat' => [],
                    'tindakan' => [],
                    'penunjang' => []
                ];
            }

            if ($is_obat) {
                $listData[$value['pasienmasukpenunjang_id']]['obat'][] = $value;
            } else {
                // Penunjang
                if (in_array($instalasi, DocoConstants::$exceptPenunjang)) {
                    $listData[$value['pasienmasukpenunjang_id']]['tindakan'][$instalasi]['data'][] = $value;
                    $listData[$value['pasienmasukpenunjang_id']]['tindakan'][$instalasi]['title'] = $ruangan;
                } else {
                    $listData[$value['pasienmasukpenunjang_id']]['penunjang'][$instalasi]['data'][] = $value;
                    $listData[$value['pasienmasukpenunjang_id']]['penunjang'][$instalasi]['title'] = $ruangan;
                }
            }
        }

        $query = $header;
        // if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            $sisa_tagihan = $tagihan_detail['total_tagihan'] - $tagihan_detail['total_asuransi'] - $tagihan_detail['total_sdh_bayar'] + $tagihan_detail['total_administrasi'] + $tagihan_detail['total_pembulatan'];
                $print->attributes = [
                    '#tanggal#' => isset($query['tgl_pendaftaran']) ? $query['tgl_pendaftaran'] : null,
                    '#no_rm#' => isset($query['no_rekam_medik']) ? $query['no_rekam_medik'] : null,
                    '#no_pendaftaran#' => isset($query['no_pendaftaran']) ? $query['no_pendaftaran'] : null,
                    '#nama#' => isset($query['nama_pasien']) ? $query['nama_pasien'] : null,
                    '#jenis_kasus_penyakit#' => isset($query['jeniskasuspenyakit_nama'])
                        ? $query['jeniskasuspenyakit_nama'] : null,
                    '#dokter#' => isset($query['dokter'])
                        ? $query['dokter'] : null,
                    '#ruangan#' => isset($query['ruangan_nama'])
                        ? $query['ruangan_nama'] : null,
                    '#kelas_pelayanan#' => isset($query['kelaspelayanan_nama']) ? $query['kelaspelayanan_nama'] : null,
                    '#penjamin#' => isset($query['penjamin_nama']) ? $query['penjamin_nama'] : null,
                    '#cara_bayar#' => isset($query['carabayar_nama']) ? $query['carabayar_nama'] : null,
                    '#status_bayar#' => empty($sisa_tagihan) ? 'Lunas' : 'Belum Lunas',
                    '#total_tagihan#' => isset($tagihan_detail['total_tagihan'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_tagihan']) : DocoHelpers::rupiahDisplay(0),
                    '#total_uang_muka#' => isset($tagihan_detail['total_uang_muka'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_uang_muka']) : DocoHelpers::rupiahDisplay(0),
                    '#total_dibayar#' => isset($tagihan_detail['total_sdh_bayar'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_sdh_bayar']) : DocoHelpers::rupiahDisplay(0),
                    '#sisa_tagihan#' => isset($tagihan_detail['total_tagihan'])
                        ? DocoHelpers::rupiahDisplay($sisa_tagihan) : DocoHelpers::rupiahDisplay(0),
                    '#biaya_admin#' => isset($tagihan_detail['total_administrasi'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_administrasi']) : DocoHelpers::rupiahDisplay(0),
                    '#pembulatan#' => isset($tagihan_detail['total_pembulatan'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_pembulatan']) : DocoHelpers::rupiahDisplay(0),
                    '#sub_assuransi#' => isset($tagihan_detail['total_asuransi'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_asuransi']) : DocoHelpers::rupiahDisplay(0),
                    '#detail_tindakan#' => $this->renderPartial('riwayat_pemeriksaan', [
                        'detail' => isset($listData[$id]) ? $listData[$id] : []
                    ]),
                ];

            $print->Output();
        // }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail pemesanan
    * @attribute #title# => Untuk Menampilkan title
    **/
    public function actionCetakPdf()
    {
        try {
            $title = 'Laporan Pasien Radiologi';
            $query = InfoPasienLabView::find();
            $request = Yii::$app->request;
            // return $_GET['advanced-filter'];
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';

            if(isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if(isset($advancedFilter['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if(isset($advancedFilter['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if(isset($advancedFilter['status_periksa'])) {
                    $status_periksa = $advancedFilter['status_periksa'];
                    $query->andWhere(['status_periksa' => $status_periksa]);
                }
                if(isset($advancedFilter['dokter_penunjang'])) {
                    $dokter_penunjang = $advancedFilter['dokter_penunjang'];
                    $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
                }
                if(isset($advancedFilter['carabayar_nama'])) {
                    $carabayar_nama = $advancedFilter['carabayar_nama'];
                    $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
                }
                if(isset($advancedFilter['penjamin_nama'])) {
                    $penjamin_nama = $advancedFilter['penjamin_nama'];
                    $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                }
                if(isset($advancedFilter['asalrujukan_nama'])) {
                    $asalrujukan_nama = $advancedFilter['asalrujukan_nama'];
                    $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
                }
                // if(isset($advancedFilter['penjamin_nama'])) {
                //     $penjamin_nama = $advancedFilter['penjamin_nama'];
                //     $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                // }
                // if(isset($advancedFilter['penjamin_nama'])) {
                //     $penjamin_nama = $advancedFilter['penjamin_nama'];
                //     $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                // }
            }

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);

            if(!empty($startLahir) && !empty($endLahir) && $between){
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }

            $model = $query->all();

            if($model) {
                $print = new DocoPrint();
                $print->attributes = [
                    '#title#' => $title,
                    '#tabel_detail#' => $this->renderPartial('index', [
                        'query' => $model
                    ]),
                ];

                $print->Output();
            }
        } catch (\Exception $e) {
             \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    protected $_title = "Laporan Pasien Radiologi";
    public function actionExportExcel()
    {
        $model = new InfoPasienLabView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $startLahir = '';
        $endLahir = '';

        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($advancedFilter['tanggal_lahir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d', strtotime($explode[0]));
                    $endLahir = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($advancedFilter['status_periksa'])) {
                $status_periksa = $advancedFilter['status_periksa'];
                $query->andWhere(['status_periksa' => $status_periksa]);
            }
            if(isset($advancedFilter['dokter_penunjang'])) {
                $dokter_penunjang = $advancedFilter['dokter_penunjang'];
                $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
            }
            if(isset($advancedFilter['carabayar_nama'])) {
                $carabayar_nama = $advancedFilter['carabayar_nama'];
                $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
            }
            if(isset($advancedFilter['penjamin_nama'])) {
                $penjamin_nama = $advancedFilter['penjamin_nama'];
                $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
            }
            if(isset($advancedFilter['asalrujukan_nama'])) {
                $asalrujukan_nama = $advancedFilter['asalrujukan_nama'];
                $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
            }
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        if(!empty($startLahir) && !empty($endLahir) && $between){
            $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $result = [];

        foreach ($dataProvider->getModels() as $key => $value) {
            // Data Selection
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pendaftaran')] = date('d M Y', strtotime($value['tglmasukpenunjang']));
            $newValue[\Yii::t('app', 'Nomor Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No Rekam Medis')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Tanggal Lahir')] = $value['tanggal_lahir'];
            $newValue[\Yii::t('app', 'Dokter')] = $value['dokter_penunjang'];
            $newValue[\Yii::t("app", "Cara Bayar")] = $value['carabayar_nama'];
            $newValue[\Yii::t("app", "Penjamin")] = $value['penjamin_nama'];
            $newValue[\Yii::t("app", "No Radiologi")] = $value['no_masukpenunjang'];
            $newValue[\Yii::t("app", "Asal Rujukan")] = $value['asalrujukan_nama'];
            $newValue[\Yii::t("app", "Status")] = isset($value['status_periksa']) ?
            DocoConstants::$status_lab[$value['status_periksa']] : '';
            $result[$key] = $newValue;
        }

        // Directory Creation
        $header = array(
            Yii::t("app", "Tanggal Pendaftaran") => (isset($_GET['advanced-filter']['tglmasukpenunjang'])) ? $_GET['advanced-filter']['tglmasukpenunjang'] : '',
            Yii::t("app", "Dokter") => (isset($_GET['advanced-filter']['dokter_penunjang'])) ? $_GET['advanced-filter']['dokter_penunjang'] : '',
            Yii::t("app", "Cara Bayar") => (isset($_GET['advanced-filter']['carabayar_nama'])) ? $_GET['advanced-filter']['carabayar_nama'] : '',
            Yii::t("app", "Penjamin") => (isset($_GET['advanced-filter']['penjamin_nama'])) ? $_GET['advanced-filter']['penjamin_nama'] : '',
            Yii::t("app", "Asal Rujukan") => (isset($_GET['advanced-filter']['asalrujukan_nama'])) ? $_GET['advanced-filter']['asalrujukan_nama'] : '',
            Yii::t("app", "Status") => (isset($_GET['advanced-filter']['status_periksa'])) ? $_GET['advanced-filter']['status_periksa'] : '',
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));

        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    public function actionGetDokter()
    {
        try {
            $model = new DokterView;
            $query = $model::find();

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }
            if ($between) {
                $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            }
            /**
             * End Special Condition date range
             **/

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    public function actionGenerateApi($instalasi_id)
    {
        try {
            $cara_bayar = CaraBayar::find()->andWhere(['is_deleted' => false, 'is_active' => true]);
            $data_cara_bayar = $cara_bayar->all();
            $status_radiologi = DocoHelpers::getLookUpByInstalasi('status_periksa_penunjang', $instalasi_id);
            $status_expertise = Lookup::find()->where([
                'is_active' => true,
                'lookup_type' => DocoConstants::STATUS_EXPERTISE_RADIOLOGI
            ])->all();
            $asalRujukan = AsalRujukan::find()->where([
                'is_active' => true,
                'is_rujukan' => true
            ])->orderBy('asalrujukan_id')->all();

            $rujukanDari = PerujukView::find()->andWhere([
                'is_active'=>true,
            ])->all();

            $return = [
                'status_radiologi' => $status_radiologi,
                'cara_bayar' => $data_cara_bayar,
                'asalRujukan' => $asalRujukan,
                'rujukanDari' => $rujukanDari,
                'status_expertise' => $status_expertise,
            ];

            return $return;
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

    public function actionGetPegawaiRuangan()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pegawai_m');

        $model = new Pegawai;
        $query = $model::find()->select([
            'pegawai_m.pegawai_id',
            'pegawai_m.nomorindukpegawai',
            'pegawai_m.gelardepan',
            'pegawai_m.gelarbelakang',
            'pegawai_m.jeniskelamin',
            'pegawai_m.tempatlahir_pegawai',
            'pegawai_m.tgl_lahirpegawai',
            'pegawai_m.agama',
            'pegawai_m.alamat_pegawai',
            'pegawai_m.nama_pegawai',
            'pegawai_m.is_active',
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function integrateTindakanCrud($pendaftaran_id, $method)
    {
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'instalasi_id' => 5])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if($config->is_akunting != null) {
                if(!empty($tindakan)){
                    foreach($tindakan as $key => $value){
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
                            'xcrud' => $method,
                        ];
                    }
                    return $var = DocoAkunting::api('POST', 'integrations_crud', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateBmhpTrx($id, $method)
    {
        try{
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $resep = SyncPengeluaranobat::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'instalasi_id' => 5])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if($config->is_akunting != null) {
                if(!empty($resep)){
                    foreach($resep as $key => $value){
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
                            'xcrud' => $method
                        ];
                        // $obatalkes = ObatAlkesPasien::findOne($value->id);
                        // $obatalkes->is_jurnal = true;
                        // $obatalkes->scenario = "jurnal";
                        // $obatalkes->update();
                        // var_dump($obatalkes->getErrors(),100,true);
                        // die;
                    }
                    $var = DocoAkunting::api('POST', 'integrations_crud', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    /**
     * This function will update doctor
     *
     * @param String var
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateDoctor()
    {
        $payload = $this->validatePayload([
            'payloadKey' => [
                'daftartindakan_id' => 'required',
                'no_masukpenunjang' => 'required',
                'pegawai_id' => 'required',
            ]
        ]);
        if (isset($payload['errors'])) {
            return $this->responseJson(422, 'Silakan cek kembali input', ['errors' => $payload['errors']]);
        } else {
            // get data
            $recordPemeriksaan = PemeriksaanPasienRadiologiView::find()
                ->select([
                    'no_masukpenunjang',
                    'no_pendaftaran',
                    'pasienmasukpenunjang_id',
                    'pasienkirimkeunitlain_id',
                    'pendaftaran_id',
                    'tipe_pasien',
                    'is_mcu',
                    'daftartindakan_id',
                    'daftartindakan_nama'
                ])
                ->andWhere([
                    'daftartindakan_id' => $payload['daftartindakan_id'],
                    'no_masukpenunjang' => $payload['no_masukpenunjang'],
                ])
                ->asArray()
                ->one();
            if (!empty($recordPemeriksaan) && !$recordPemeriksaan['is_mcu']) {
                if ($recordPemeriksaan['tipe_pasien'] != 'APS' && !empty($recordPemeriksaan['pasienkirimkeunitlain_id'])) {
                    $whereClause = [
                        'pasienkirimkeunitlain_id' => $recordPemeriksaan['pasienkirimkeunitlain_id'],
                        'daftartindakan_id' => $recordPemeriksaan['daftartindakan_id']
                    ];
                    PermintaanKepenunjangan::updateAll([
                        'dokter_id' => $payload['pegawai_id']
                    ], $whereClause);
                    $detailIntegration = PermintaanKepenunjangan::find()
                        ->select([
                            'tindakanpelayanan_id',
                            'pasienkirimkeunitlain_id',
                            'daftartindakan_id',
                            'dokter_id'
                        ])
                        ->andWhere($whereClause)
                        ->asArray()
                        ->all();
                } else {
                    $detailIntegration = TindakanPelayanan::find()
                        ->select([
                            'tindakanpelayanan_id',
                            'daftartindakan_id',
                            'pasienmasukpenunjang_id'
                        ])
                        ->andWhere([
                            'pasienmasukpenunjang_id' => $recordPemeriksaan['pasienmasukpenunjang_id'],
                            'daftartindakan_id' => $recordPemeriksaan['daftartindakan_id']
                        ])
                        ->asArray()
                        ->one();
                    $detailIntegration = [array_merge($detailIntegration, [
                        'dokter_id' => $payload['pegawai_id']
                    ])];
                }
                (new KasirService)->editTindakan($recordPemeriksaan['no_pendaftaran'], $detailIntegration);
                return $this->responseJson(200, 'Dokter berhasil diperbarui.');
            } else {
                return $this->responseJson(400, empty($recordPemeriksaan) ? 'Data tidak ditemukan.' : 'Tidak dapat memperbarui data dokter pemeriksa Pendaftaran MCU.');
            }
        }
    }

    /**
     * This function will update doctor
     *
     * @param String var
     * @return JSON
     * @author : aprianto
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionBatalPeriksa()
    {
        $request = Yii::$app->request;
        $p = $request->post();

        $params = [
            'no_pendaftaran' => $p['no_pendaftaran'],
            'ruangan_id' => $p['ruangan_id'],
            'tindakanpelayanan_id' => DocoHelpers::decrypt($p['tindakanpelayanan_id']),
            'no_masukpenunjang' => $p['no_masukpenunjang']
        ];

        $no_masukpenunjang = isset($params['no_masukpenunjang']) ? $params['no_masukpenunjang'] : null;
        $no_pendaftaran = isset($params['no_pendaftaran']) ? $params['no_pendaftaran'] : null;
        $ruangan_id = isset($params['ruangan_id']) ? $params['ruangan_id'] : null;
        $tindakanpelayanan_id = isset($params['tindakanpelayanan_id']) ? $params['tindakanpelayanan_id'] : null;

        if($no_masukpenunjang) {
            $param = [
                'no_pendaftaran' => $no_pendaftaran,
                'no_masukpenunjang' => $no_masukpenunjang
            ];
        }
        else {
            $param = [
                'no_pendaftaran' => $no_pendaftaran,
                'ruangan_id' => $ruangan_id,
            ];
        }

        $param['detail_tindakan'] = ['tindakanpelayanan_id' => $tindakanpelayanan_id];

        
        $order = new PemeriksaanPasienRadiologiView;
        $queryorder =  $order::find()
                                ->where([
                                    'no_pendaftaran' => $no_pendaftaran,
                                    'status_batal' => 'f'
                                ]);
        $data = $queryorder->all();

        $billKasir = (new KasirService)->post('api/batal-tagihan', [
            'form_params' => $param,
            'failed' => function($data) {
                \Yii::error([
                    "Message-Error" => $data
                ]);
                return [
                    'failed' => true,
                    'message' => [
                        'status' => 422,
                        'text' => $data['message']
                    ]
                ];
            }
        ]);

        if (isset($billKasir['failed'])) {
            return $this->responseJson(422, $billKasir['message']['text']);
        }else{
            $obatAlkes = ObatAlkesPasien::find()->where(['tindakanpelayanan_id' => $tindakanpelayanan_id])->asArray()->All();
            
            if(!empty($obatAlkes)){
                foreach($obatAlkes as $val){
                    $obatAlkesId = !empty($val['obatalkespasien_id']) ? $val['obatalkespasien_id'] : null;
                    if(!empty($obatAlkesId)){
                        $_GET['obatalkespasien_id'] = $obatAlkesId;
                        $this->actionDeleteTindakanObat();
                    }
                }
            }
            if($queryorder->count() == 0){
                $pasienmasukpenunjang_id = $data[0]['pasienmasukpenunjang_id'];

                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();
                $PasienMasukPenunjangT = PasienMasukPenunjangT::findOne($pasienmasukpenunjang_id);
                $result = array();
                if(!empty($PasienMasukPenunjangT)){
                    $PasienMasukPenunjangT->status_periksa = DocoConstants::BTL_PERIKSA_LAB;
                    $PasienMasukPenunjangT->update();

                    $getInstalasiPenunjang = Instalasi::find()->where(['instalasi_id' => $PasienMasukPenunjangT->instalasiasal_id])
                                            ->andWhere(['is_penunjang' => true])->one();
                    if ($getInstalasiPenunjang) {
                        /** update status pasien pendaftaran **/
                        $getPendaftaran = Pendaftaran::findOne(['pendaftaran_id' => $PasienMasukPenunjangT->pendaftaran_id]);
                        $getPendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                        $getPendaftaran->update();
                        /** update status pasien pendaftaran **/
                    }

                    $result = [
                        'status' => 200,
                        'title' => 'Input Berhasil',
                        'text' => 'Pembatalan Periksa Berhasil'
                    ];
                    $transaction->commit();
                 }else {
                    $transaction->rollBack();
                    $result['status'] = 500;
                    $result['title'] = 'Gagal insert';
                }
                return $result;
            }

        }

    }
}
