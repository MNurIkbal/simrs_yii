<?php

/**
 * @author Aris Munandar
 * @todo Informasi pasien laboratorium intregasi roche
 * @copyright 21 Mei 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\InfoPasienLabView;
use app\modules\v1\models\InfoPasienLabDetailView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\RujukanDari;
use app\modules\v1\models\BatalPeriksaPenunjangT;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Pendaftaran;

use yii\helpers\ArrayHelper;


class InfPasienLabRocheController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienLabView';
    public $konfig_farmasi;

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
        return Yii::$app->docoPlugin->execute('laboratorium');
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
        // echo '<pre>';
        // print_r($post);
        // echo '</pre>';
        // exit;
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        
        try {
           
            $PasienMasukPenunjangT = PasienMasukPenunjangT::findOne(['pasienmasukpenunjang_id'=>$post['pasienmasukpenunjang_id']]);
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
            $model = new InfoPasienLabView;
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
            $model = new InfoPasienLabDetailView;
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

    private function getStatusPeriksa()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa1 FROM lookup_m WHERE lookup_type = 'status_periksa_penunjang'";
        $result = Lookup::findBySql($sql);

        return $result;

    }

    private function getPegawaiRuangan($id_ruangan)
    {
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $result;

    }

    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;
            $find_statusperiksa = $this->getStatusPeriksa();
            $data_statusperiksa = $find_statusperiksa->asArray()->all();

            $find_pegawai = $this->getPegawaiRuangan($request->get('id_ruangan'));
            $data_pegawai = $find_pegawai->asArray()->all();

            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
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

    public function actionGetPasien($id)
    {
        try {
            $params = [];
            $model = PasienMasukPenunjangT::findOne($id);
            // $find = $this->getPasienDetail($id)->asArray()->one();
            return [
                'data' => $model
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

    private function getPasienDetail($id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pendaftaran_id,
                    antrian_id,
                    pendaftaran_t.pegawai_id as pegawai_id,
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pendaftaran_t.no_pendaftaran AS no_pendaftaran,
                    pasien_m.nama_pasien AS nama_pasien,
                    pasien_m.pasien_id AS pasien_id,
                    pegawai_m.nama_pegawai AS nama_dokter
                FROM
                    pendaftaran_t
                JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
                WHERE
                    pendaftaran_t.is_deleted = FALSE
            ";

        // filter
        if ($id){
            $sql .= " AND pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        $result = PasienMasukPenunjangT::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

    public function actionUbahDokter()
    {
        $request = Yii::$app->request;
        try {
            if ($post = $request->post()){
                $data_dokter = PasienMasukPenunjangT::find()
                ->where([
                    'pasienmasukpenunjang_id' => $post["pasienmasukpenunjang_id"],
                    'is_deleted' => false,
                    ])->one();

                    if ($data_dokter){
                        $data_dokter->pegawai_id = $post["pegawai_id"];
                        $data_dokter->tglmasukpenunjang = $post["tglmasukpenunjang"];
                        $data_dokter->status_periksa = DocoConstants::ST_PERIKSA;
                    if($data_dokter->validate() && $data_dokter->save()) {
                        return [
                            'message' => 'status_success'
                        ];
                    }
                }else{
                    \Yii::$app->response->statusCode = 203;

                    return [
                        'message' => 'status_notfound'
                    ];
                }
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
            FROM rincianpasienlab_v
            WHERE pasienmasukpenunjang_id = {$id}
            GROUP BY tipe_pasien,pasienmasukpenunjang_id,pendaftaran_id,
            tgl_pendaftaran,no_pendaftaran,no_rekam_medik,nama_pasien,jeniskasuspenyakit_nama,
            dokter,ruangan_nama,kelaspelayanan_nama,penjamin_nama,carabayar_nama,status_bayar
        ")->queryOne();

        $tagihan_detail = Yii::$app->db->createCommand("
            SELECT * FROM rincian_header_penunjang_view WHERE pasienmasukpenunjang_id = {$id}
        ")->queryOne();

        $detail = Yii::$app->db->createCommand("
            SELECT * FROM infotagihandetail_v WHERE pasienmasukpenunjang_id = {$id}
        ")->queryAll();

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
        if (!empty($header)) {
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
                        'detail' => $listData[$id]
                    ]),
                ];

            $print->Output();
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail pemesanan
    * @attribute #title# => Untuk Menampilkan title
    * @attribute #periode# => Untuk Menampilkan title
    **/
    public function actionCetakPdf()
    {
        try {
            $title = 'Laporan Pasien Laboratorium';
            $model = new InfoPasienLabView;
            $query = $model::find();
            $query->andWhere(['not',['status_penunjang'=>DocoConstants::BTL_APPROVE]]);
            $query->orWhere(['status_penunjang'=>null]);
            $request = Yii::$app->request;
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
                    $header['Tanggal Pendaftaran'] = $_GET['advanced-filter']['tglmasukpenunjang'];
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if(isset($advancedFilter['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    $header['Tanggal Lahir'] = $_GET['advanced-filter']['tanggal_lahir'];
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if(isset($advancedFilter['status_periksa'])) {
                    $status_periksa = $advancedFilter['status_periksa'];
                    $header['Status'] = isset(DocoConstants::$status_lab[$status_periksa]) 
                            ? DocoConstants::$status_lab[$status_periksa] : null;
                    $query->andWhere(['status_periksa' => $status_periksa]);
                }
                if(isset($advancedFilter['dokter_penunjang'])) {
                    $dokter_penunjang = $advancedFilter['dokter_penunjang'];
                    $header['Dokter'] = $dokter_penunjang;
                }
                if(isset($advancedFilter['carabayar_nama'])) {
                    $carabayar_nama = $advancedFilter['carabayar_nama'];
                    $header['Cara Bayar'] = $carabayar_nama;
                    // $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
                }
                if(isset($advancedFilter['penjamin_nama'])) {
                    $penjamin_nama = $advancedFilter['penjamin_nama'];
                    $header['Penjamin'] = $penjamin_nama;
                    // $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                }
                if(isset($advancedFilter['asalrujukan_nama'])) {
                    $asalrujukan_nama = $advancedFilter['asalrujukan_nama'];
                    $header['Asal Rujukan'] = $asalrujukan_nama;
                    // $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
                }
                if(isset($advancedFilter['asalrujukan_id'])) {
                    $asalrujukan_id = (int) $advancedFilter['asalrujukan_id'];
                    $header['Asal Rujukan Nama / Instalasi'] = $asalrujukan_id;
                }
                if(isset($advancedFilter['ruanganasal_id'])) {
                    $ruanganasal_id = (int) $advancedFilter['ruanganasal_id'];
                    $header['Ruangan Asal'] = $ruanganasal_id;
                }
            }
            
            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            if(!empty($startLahir) && !empty($endLahir) && $between){
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }
                
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $model = $query->all();

            if($model) {
                $print = new DocoPrint();
                $print->attributes = [
                    '#title#' => $title,
                    '#periode#' => date('d-M-Y',strtotime($start)) . ' s/d ' . date('d-M-Y',strtotime($end)),
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

    protected $_title = "Laporan Pasien Laboratorium";
    public function actionExportExcel()
    {
        $model = new InfoPasienLabView;
        $query = $model::find();
        $query->andWhere(['not',['status_penunjang'=>DocoConstants::BTL_APPROVE]]);
        $query->orWhere(['status_penunjang'=>null]);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $startLahir = '';
        $endLahir = '';
        $header = [];
        // Directory Creation
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                $header['Tanggal Pendaftaran'] = $_GET['advanced-filter']['tglmasukpenunjang'];
                unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            
            if(isset($advancedFilter['tanggal_lahir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d', strtotime($explode[0]));
                    $endLahir = date('Y-m-d', strtotime($explode[1]));
                }
                $header['Tanggal Lahir'] = $_GET['advanced-filter']['tanggal_lahir'];
                unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($advancedFilter['status_periksa'])) {
                $status_periksa = $advancedFilter['status_periksa'];
                $header['Status'] = isset(DocoConstants::$status_lab[$status_periksa]) 
                        ? DocoConstants::$status_lab[$status_periksa] : null;
                $query->andWhere(['status_periksa' => $status_periksa]);
            }
            if(isset($advancedFilter['dokter_penunjang'])) {
                $dokter_penunjang = $advancedFilter['dokter_penunjang'];
                $header['Dokter'] = $dokter_penunjang;
                // $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
            }
            if(isset($advancedFilter['carabayar_nama'])) {
                $carabayar_nama = $advancedFilter['carabayar_nama'];
                $header['Cara Bayar'] = $carabayar_nama;
                // $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
            }
            if(isset($advancedFilter['penjamin_nama'])) {
                $penjamin_nama = $advancedFilter['penjamin_nama'];
                $header['Penjamin'] = $penjamin_nama;
                // $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
            }
            if(isset($advancedFilter['asalrujukan_nama'])) {
                $asalrujukan_nama = $advancedFilter['asalrujukan_nama'];
                $header['Asal Rujukan'] = $asalrujukan_nama;
                // $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
            }
            if(isset($advancedFilter['asalrujukan_id'])) {
                $asalrujukan_id = (int) $advancedFilter['asalrujukan_id'];
                $header['Asal Rujukan Nama / Instalasi'] = $asalrujukan_id;
            }
            if(isset($advancedFilter['ruanganasal_id'])) {
                $ruanganasal_id = (int) $advancedFilter['ruanganasal_id'];
                $header['Ruangan Asal'] = $ruanganasal_id;
            }
        }
        
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        if(!empty($startLahir) && !empty($endLahir) && $between){
            $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
        }
            
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $result = [];

        foreach ($query->all() as $key => $value) {
            // Data Selection
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pendaftaran')] = date('d M Y', strtotime($value['tglmasukpenunjang']));
            $newValue[\Yii::t('app', 'Nomor Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No Rekam Medis')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Tanggal Lahir')] = date('d M Y', strtotime($value['tanggal_lahir']));
            $newValue[\Yii::t('app', 'Dokter')] = $value['dokter_penunjang'];
            $newValue[\Yii::t("app", "Cara Bayar")] = $value['carabayar_nama'];
            $newValue[\Yii::t("app", "Penjamin")] = $value['penjamin_nama'];
            $newValue[\Yii::t("app", "No.Lab")] = $value['no_masukpenunjang'];
            $newValue[\Yii::t("app", "Asal Rujukan")] = $value['asalrujukan_nama'];
            $newValue[\Yii::t("app", "Status")] = isset($value['status_periksa']) ? 
            DocoConstants::$status_lab[$value['status_periksa']] : '';
            if( isset($header['Asal Rujukan Nama / Instalasi']) ){
                $header['Asal Rujukan Nama / Instalasi'] = $value['asalrujukan_nama'];
            }
            if( isset($header['Ruangan Asal']) ){
                $header['Ruangan Asal'] = $value['ruangan_nama'];
            }
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], [],[],true);
        $filePath->save('php://output');
        die;
    }

}