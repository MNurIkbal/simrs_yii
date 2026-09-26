<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-13 10:32:20
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-05-28 18:29:22
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-12-07 11:00:15
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-06-29 17:04:02
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoMutasiObatalkesView;
use app\modules\v1\models\DetailMutasiObatAlkesView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\TerimaMutasiObat;
use app\modules\v1\models\TerimaMutasiObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\MutasiObatRuangan;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\InfoTerimaMutasiObat;
use app\modules\v1\models\InfoTerimaMutasiObatDetail;
use app\modules\v1\models\InfoPemesananObatAlkes;
use app\modules\v1\models\DetailPemesananObatAlkes;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;

class InfMutasiObatalkesController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoMutasiObatalkesView';
    /**
    * Attribute dari tabel konfigurasifarmasi_k
    * @type array
    **/
    protected $_config;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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
        $actions['detail'] = 'app\modules\v1\actions\InfMutasiObatalkes\DetailAction';
        $actions['terima'] = 'app\modules\v1\actions\InfMutasiObatalkes\TerimaAction';
        $actions['detail-mutasi-pesanan'] = 'app\modules\v1\actions\General\ListMutasiObatAction';
        // $actions['delete-mutasi'] = [
        //     'class' => 'yii\rest\DeleteAction',
        //     'modelClass' => MutasiObatRuangan::className(),
        //     'checkAccess' => [$this, 'checkAccess']
        // ];
        return $actions;
    }
    //index view info mutasi obat alkes
    public function actionIndex()
    {
        $model = new InfoMutasiObatalkesView;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmutasioa'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tglmutasioa']);
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                    $start = date('Y-m-d 00:00:00', strtotime($start));
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
                    $end = date('Y-m-d 23:59:59', strtotime($end));
                }
                unset($_GET['advanced-filter']['tglmutasioa']); // Unset Advanced Filter  date range
                $between = true;
            }

            if (isset($_GET['advanced-filter']['instalasi_tujuan_id'])) {
                $id = $_GET['advanced-filter']['instalasi_tujuan_id'];
                $query->andWhere('instalasi_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['instalasi_tujuan_id']);
            }

            if (isset($_GET['advanced-filter']['ruangan_tujuan_id'])) {
                $id = $_GET['advanced-filter']['ruangan_tujuan_id'];
                $query->andWhere('ruangan_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['ruangan_tujuan_id']);
            }

            if (isset($_GET['advanced-filter']['ruangan_asal'])) {
                $id = $_GET['advanced-filter']['ruangan_asal'];
                $query->andWhere('ruangan_asal_id = '.$id);
                unset($_GET['advanced-filter']['ruangan_asal']);
            }
        }

        if (isset($_GET['mutasiobatruangan_id'])) {
            $query->andWhere('mutasiobatruangan_id = '.$_GET['mutasiobatruangan_id']);
        }
        $query->andWhere(['between','tglmutasioa',$start,$end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }


    public function actionIndexKeluar()
    {
        $model = new InfoMutasiObatalkesView;
        $query = $model::find(true)->where(['ruangan_asal_id' => $_GET['advanced-filter']['ruangan_asal_id']]);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            // return $_GET['advanced-filter'];
            if(isset($_GET['advanced-filter']['tglmutasioa'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tglmutasioa']);
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0].' 00:00:00';
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0].' 23:59:59';
                }
                unset($_GET['advanced-filter']['tglmutasioa']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $id = $_GET['advanced-filter']['instalasi_nama'];
                $query->andWhere('instalasi_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere('ruangan_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }

        $query->andWhere(['between','tglmutasioa',$start,$end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }
    //create penerimaan mutasi obat alkes
    public function actionCreate($nomutasioa)
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $post = $post['PenerimaanObatForm'];
        $Mutasi = MutasiObatRuangan::find()->where(['nomutasioa'=>$nomutasioa])->one();
        $mutasiobatruangan_id = $Mutasi->mutasiobatruangan_id;

        if($Mutasi->status_mutasi == DocoConstants::STATUS_MUTASI_DITERIMA){
            return [
                'id' => 0,
                'data' => [],
                'message' => 'Mutasi Telah Diterima',
                'status' => 422
            ];
        }

        $model = new TerimaMutasiObat;
        $transaction = $model->getDb()->beginTransaction();
        try {
            $model->mutasiobatruangan_id = $mutasiobatruangan_id;
            $model->tglterima = date("Y-m-d", strtotime($post['tglterima']));
            $model->noterimamutasi = '1';
            $model->totalharganetto = 0;
            $model->totalhargajual = 0;
            $model->keterangan_terima = null;
            $model->ruanganpenerima_id = $Mutasi->ruangantujuan_id;
            $model->ruanganasal_id = $Mutasi->ruanganasal_id;
            $model->pegawaipenerima_id = $post['pegawai_mengetahui'];
            $model->pegawaimengetahui_id = $post['pegawai_mengetahui'];
            $_POST['ruangan_id'] = $Mutasi->ruanganasal_id;
            $_POST['ruangan_penerima_id'] = $Mutasi->ruangantujuan_id;

            if(!$model->save()){
                throw new \yii\db\Exception('Gagal Simpan Terima Mutasi', $model->getErrors(),500);
            }

            $terimamutasiobat_id = $model->getPrimaryKey();

            $mDetail = new DetailMutasiObatAlkesView;

            $dataobat = $mDetail::find(true)->where([
                'mutasiobatruangan_id' => $mutasiobatruangan_id
            ])->asArray()->all();

            if(count($dataobat) == 0) throw new \yii\base\ErrorException("Tidak Ada Data Detail Mutasi Obat", 500);

            $sum_harganetto = $sum_hargajual = 0;

            $konfig = "SELECT hargaygdigunakan FROM konfigfarmasi_k LIMIT 1";
            $konfigFarmasi = \Yii::$app->db->createCommand($konfig)->queryOne();

            $this->_config = $konfigFarmasi;
            $batchInsert = $stokIn = $stokOut = [];
            foreach ($dataobat as $d_obat) {
                $harga_netto_ = $d_obat['harganetto'];
                $harga_jual_ = $this->getHargaJual($d_obat,$this->_config);

                $harga_netto_terima = $harga_netto_ * $d_obat['jumlah_mutasi'];
                $harga_jual_terima = $harga_jual_ * $d_obat['jumlah_mutasi'];
                $sum_hargajual += $harga_jual_terima;
                $sum_harganetto += $harga_netto_terima;
                // prepare untuk nginsert ke penerimaan detail
                $batchInsert[] = [
                    'terimamutasiobat_id' => $terimamutasiobat_id,
                    'mutasiobatdetail_id' => $d_obat['mutasiobatdetail_id'],
                    'satuankecil_id' => $d_obat['satuankecil_id'],
                    'ruangan_id' => $d_obat['ruangan_asal_id'],
                    'obatalkes_id' => $d_obat['obatalkes_id'],
                    'jmlmutasi' => $d_obat['jumlah_mutasi'],
                    'jmlterima' => $d_obat['jumlah_mutasi'],
                    'harganettoterima' => $harga_netto_terima,
                    'hargajualterima' => $harga_jual_terima
                ];

                $stokIn[$d_obat['mutasiobatdetail_id']] = [
                    'ruangan_id' => $d_obat['ruangan_tujuan_id'],
                    'obatalkes_id' => $d_obat['obatalkes_id'],
                    'terimamutasidetail_id' => null,
                    'mutasiobatdetail_id' => $d_obat['mutasiobatdetail_id'],
                    'qty_satuanpakai' => $d_obat['jumlah_mutasi'],
                    'satuankecil_id' => $d_obat['satuankecil_id'],
                    'kadaluarsa' => @$d_obat['expired']
                ];
            }

            uasort($stokIn, function($a,$b){
                return strtotime($a['kadaluarsa']) - strtotime($b['kadaluarsa']);
            });

            $tanggalBerlaku = date('Y-m-d H:i:s');
            // Mencari Metode
            $konfig = Yii::$app->db->createCommand("
                SELECT metodeantrian FROM konfigfarmasi_k
                WHERE tglberlaku >= '{$tanggalBerlaku}'
                AND konfigfarmasi_aktif = true
                AND is_active = true
            ")->queryOne();
            // Mencari Metode dengan nilai default FEFO
            $currentMetode = LogicStokObatAlkes::FEFO;
            if ($konfig) {
                $currentMetode = isset($konfig['metodeantrian'])
                                    ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
            }
            TerimaMutasiObatDetail::batchInsert($batchInsert,false);

            $query = Yii::$app->db->createCommand("
                SELECT obatalkes_id, terimamutasiobatdetail_id, mutasiobatdetail_id FROM terimamutasiobatdetail_t
                WHERE terimamutasiobat_id = {$terimamutasiobat_id}
            ")->queryAll();

            foreach ($query as $key => $value) {
                $id_parent = $value['terimamutasiobatdetail_id'];
                $id_mutasi_detail = $value['mutasiobatdetail_id'];
                if (isset($stokIn[$id_mutasi_detail])) {
                    $stokIn[$id_mutasi_detail]['terimamutasidetail_id'] = $id_parent;
                }
            }
            // Execute By Condition
            if ($currentMetode === LogicStokObatAlkes::FEFO) {
               $methode = LogicStokObatAlkes::methodeFEFO($stokIn,$tanggalBerlaku,true);
            } else {
               $methode = LogicStokObatAlkes::methodeFIFO($stokIn,$tanggalBerlaku,true);
            }

            $model->totalharganetto = $sum_harganetto;
            $model->totalhargajual = $sum_hargajual;
            if(!$model->update()){
                throw new \yii\db\Exception('Gagal simpan harga netto dan harga jual', $model->getErrors(),500);
            }

            $status_terima = DocoConstants::STATUS_MUTASI_DITERIMA;

            Yii::$app->db->createCommand("
                UPDATE mutasiobatruangan_t SET status_mutasi = {$status_terima}
                WHERE mutasiobatruangan_id = {$mutasiobatruangan_id}
            ")->execute();
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => json_encode($e->getMessage()),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=> json_encode($e->getMessage()),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }
        $mTerima = TerimaMutasiObat::findOne($model->getPrimaryKey());
        if($mTerima){
            $no_terimamutasi = $mTerima['noterimamutasi'];
            $msg_simpan_mutasi = "Mutasi berhasil diterima dengan no: ".$no_terimamutasi;
        }

        \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
        return [
            'message' => 'Data Berhasil Disimpan',
            'text' => isset($msg_simpan_mutasi)? $msg_simpan_mutasi : 'Mutasi berhasil diterima',
            'id' => $terimamutasiobat_id
        ];
    }

    public function actionDetailMutasi()
    {
        $request = Yii::$app->request;
        $request_advancefilter =  $request->get('advanced-filter');
        if (empty($request_advancefilter['mutasiobatruangan_id'])) {
            if (empty($request_advancefilter['pesanobatalkes_id'])) {
                return false;
            }
            $model = new DetailPemesananObatAlkes;
            $query = $model::find(true);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } else {
            $model = new DetailMutasiObatAlkesView;
            $query = $model::find(true);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }
    }

    //ambil data pegawai buat disimpen di modal
    public function actionGetDataPegawai(){
        $model = new PegawaiView;
        $query = $model::find(true);
        $query->select(['pegawai_id','nomorindukpegawai', 'nama_pegawai','jabatan_nama']);
        $query->groupBy(['pegawai_id','nomorindukpegawai', 'nama_pegawai','jabatan_nama']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


    public function actionDataNomutasi(){
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDataNomutasi();
        $result->select(['mutasiobatruangan_id','nomutasioa']);
        if(!empty($post['term'])){
            $term = strtoupper($post['term']);
            $result->where(['like', 'nomutasioa', $term]);
        }
        return $result->asArray()->all();
    }
    public function actionDataNomutasi2(){
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDataNomutasi();
        $result->select(['mutasiobatruangan_id','nomutasioa']);
        if(!empty($post['term'])){
            $term = strtoupper($post['term']);
            $result->where(['like', 'nomutasioa', $term]);
        }
        return $result->asArray()->all();
    }
    public function getDataNomutasi(){
        $model = InfoMutasiObatalkesView::find();
        return $model;
    }
    public function actionDataPegawai(){
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDataPegawai();
        $result->select(['pegawai_id','nama_pegawai']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE', 'nama_pegawai', $term]);
            $result->andWhere(['ruangan_id' => $post["ruangan_id"]]);
        }
        $result->groupBy(['pegawai_id', 'nama_pegawai']);
        return $result->asArray()->all();
    }
    public function getDataPegawai()
    {
        $model = PegawaiView::find();
        return $model;
    }

    public function actionGetOneNoMutasi($id)
    {
        $query = InfoMutasiObatalkesView::find()
                    ->where(['mutasiobatruangan_id' => $id])
                    ->one();
        $pemesanan = InfoPemesananObatAlkes::find()
            ->where(["pesanobatalkes_id" => $query->pesanobatalkes_id])
            ->one();
        return [
            'data' => $query,
            'pemesanan' => $pemesanan
        ];
    }

    public function actionGetNoPesanOa($id)
    {
        $pemesanan = InfoPemesananObatAlkes::find()
            ->where(["pesanobatalkes_id" => $id])
            ->one();

        $query = InfoMutasiObatalkesView::find()
                    ->where(['pesanobatalkes_id' => $id])
                    ->one();
        return [
            'data' => $query,
            'pemesanan' => $pemesanan
        ];
    }

    protected function getDataObatAlkes($obatalkes_id)
    {
        $mObatAlkes = new ObatAlkes;
        $return = $mObatAlkes->findOne($obatalkes_id);
        if($return){
            return $return->attributes;
        }else{
            return false;
        }
    }

    public function getHargaJual($data, $konfigFarmasi)
    {
        if($data){
            if($konfigFarmasi){
                $konfigHarga = $konfigFarmasi['hargaygdigunakan'];
                switch ($konfigHarga) {
                    case 'MAX':
                        if(isset($data['hargamaksimum']) && $data['hargamaksimum'] != null) {
                            return $data['hargamaksimum'];
                        } elseif (isset($data['hargajual'])) {
                            return $data['hargajual'];
                        } else {
                            return false;
                        }
                        break;
                    case 'MIN':
                        if (isset($data['hargaminimum']) && $data['hargaminimum'] != null) {
                            return $data['hargaminimum'];
                        } elseif(isset($data['hargajual'])) {
                            return $data['hargajual'];
                        } else {
                            return false;
                        }
                        break;
                    case 'AVERAGE':
                        if(isset($data['hargaratarata']) && $data['hargaratarata'] != null) {
                            return $data['hargaratarata'];
                        } elseif(isset($data['hargajual'])) {
                            return $data['hargajual'];
                        } else {
                            return false;
                        }
                        break;

                    default:
                        if(isset($data['hargajual'])){
                            return $data['hargajual'];
                        }else{
                            return false;
                        }
                        break;
                }
            }
        }
        return false;
    }

    protected function getHargaNetto($obatalkes_id)
    {
        $data = $this->getDataObatAlkes($obatalkes_id);
        if($data){
            if(isset($data['harganetto'])){
                return $data['harganetto'];
            }
        }
        return false;
    }

    /**
    * @controller actionPrint
    * @attribute #nomutasioa# => nomor mutasi
    * @attribute #nopemesanan# => nomor pemesanan
    * @attribute #ruangan_asal# => ruangan asal
    * @attribute #ruangan_tujuan# => ruangan tujuan
    * @attribute #pegawai_mutasi# => pegawai mutasi
    * @attribute #pegawai_mengetahui# => pegawai mengetahui
    * @attribute #table_detail# => table
    **/
    public function actionPrint($id)
    {
        $request = Yii::$app->request;
        $model = new InfoMutasiObatalkesView;
        $query = $model::find()->where(['mutasiobatruangan_id'=>$id])->one();
        $detail = new DetailMutasiObatAlkesView;
        $detail = $detail::find()->where(['mutasiobatruangan_id'=>$id])->orderBy(['obatalkes_nama'=>SORT_ASC])->asArray()->all();

        $result = ['detail'=>$detail];
        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('pemesanan', $result),
            '#nomutasioa#' => $query->nomutasioa,
            '#tanggal_mutasi#' => date('d M Y H:i:s',strtotime($query->tglmutasioa)),
            '#nopemesanan#' => $query->nopemesanan,
            '#ruangan_asal#' => $query->ruangan_asal,
            '#ruangan_tujuan#' => $query->ruangan_nama,
            '#pegawai_mutasi#' => $query->pegawai_mutasi,
            '#pegawai_mengetahui#' => $query->pegawai_mengetahui,
            '#pegawai_penerima#' => $query->nama_pegawai_penerima
        ];
        $print->Output();

    }

    /**
    * @controller actionPrintPenerimaan
    * @attribute #nama_ruangan# => Menampilkan nama Ruangan
    * @attribute #no_mutasi# => Menampilkan No Mutasi
    * @attribute #no_penerimaan# => Menampilkan No Penerimaan
    * @attribute #tanggal_penerimaan# => Menampilkan Tanggal Penerimaan
    * @attribute #tanggal_mutasi# => Menampilkan Tanggal Master
    * @attribute #tabel_penerimaan# => Menampilkan Tabel Penerimaan
    * @attribute #pegawai_mengetahui# => Menampilkan Pegawai Megetahui
    * @attribute #pegawai_menyetujui# => Menampilkan Pegawai Menyetujui
    **/
    public function actionPrintPenerimaan($id)
    {
        $request = Yii::$app->request;
        $model = new InfoTerimaMutasiObat;
        $query = $model::find()->where(['terimamutasiobat_id' => $id])->asArray()->one();
        $detail = new InfoTerimaMutasiObatDetail;
        $detail = $detail::find()->where(['terimamutasiobat_id' => $id])->asArray()->all();

        foreach($detail as $key => $value) {
            $detail[$key]['qty_terima'] = $value['jmlterima'];

            if($value['satuankecil_id'] != $value['satuanbesar_id']) {
                $nilai_konversi = $this->actionGetKonversi(
                    $value['satuanbesar_id'],
                    $value['satuankecil_id'],
                    $value['obatalkes_id']
                );

                $detail[$key]['qty_terima'] = $value['jmlterima'] / ($nilai_konversi == 0 ? 1 : $nilai_konversi);
            }
        }

        $result = ['detail'=>$detail];
        $print = new DocoPrint();
        $print->attributes = [
            '#nama_ruangan#' => strtoupper(@$query['ruangan_pengirim']),
            '#no_mutasi#' => @$query['nomutasioa'],
            '#no_penerimaan#' => @$query['noterimamutasi'],
            '#tanggal_penerimaan#' => date('d M Y H:i:s',strtotime(@$query['tglterima'])),
            '#tanggal_mutasi#' => date('d M Y H:i:s',strtotime(@$query['tgl_mutasi'])),
            '#tabel_penerimaan#' => $this->renderPartial('penerimaan', $result),
            '#pegawai_mengetahui#' => @$query['pegawai_mengetahui'],
            '#pegawai_menyetujui#' => @$query['pegawai_penerima'],
        ];
        $print->Output();

    }

    public function actionGetKonversi($satuanbesar_id, $satuankecil_id, $obatalkes_id = null)
    {
        $condition = [
            'satuanbesar_id' => $satuanbesar_id, 
            'satuankecil_id' => $satuankecil_id
        ];

        if($obatalkes_id) {
            $condition['obatalkes_id'] = $obatalkes_id;
        }
        
        $query = SatuanKonversi::find()->where($condition)->one();

        return ($query) ? $query->nilai_konversi : 0;
    }
    public function actionDeleteMutasi($id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try{
            $delete = (new MutasiObatRuangan)->delete($id);
            if($delete){
                return true;
            }else{
                throw new \yii\db\Exception("Terjadi kesalahan", 1);
            }
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    public function actionGetApi()
    {
        $result['ruangan'] = [];
        $result['instalasi'] = [];
        try{
            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', ['state'=>false]);
            $result['ruangan'] = $result['ruangan']['response'];
            $result['instalasi'] = Yii::$app->runAction('v1/allow/get-instalasi', ['state'=>false]);
            $result['instalasi'] = $result['instalasi']['response']['instalasi'];
            return $result;
        } catch(\Exception $e){
            return $result;
        }
    }
}
