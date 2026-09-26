<?php
namespace SirsCore\businessLogic;

use Yii;
use yii\db\Expression;
use Doco\components\DocoConstants;
use SirsCore\models\InpostOperasiDetail;
use SirsCore\models\InpostOperasi;
use SirsCore\models\TimOperasi;
use SirsCore\models\InfoPasienOperasiView;
use Doco\models\TarifTotalFn;
use SirsCore\models\TimOperasiView;
use SirsCore\models\InfoInpostOperasiDetailView;
use SirsCore\models\TindakanLuarOperasi;
use Doco\models\Bedah\VerifikasiBedahR;
use yii\helpers\ArrayHelper;

class TagihanBedah {
   public $tarifOperator;
   public $tarifTertinggi;
   public $tindakanOperator;
   public $tarifTindakan;
   public $_pasienOperasi;

   const TIPE_TINDAKAN = 'tindakan';
   const TIPE_JASA = 'jasa';

   public function __construct()
   {
      $this->tarifOperator = [];
      $this->tindakanOperator = [];
      $this->tarifTertinggi = [];
      $this->tarifTindakan = [];
      $this->_pasienOperasi = [];
   }
   
   public function getBills($pasienpenunjangId, $ruangan_id = null)
   {
      $data = $daftarTindakanId = $tindakanPegawai = $rawdata = [];
      $posisiDokterOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      $timOperasi = $this->getTimOperasi($pasienpenunjangId);
      $verifikasiBedah = $this->getVerifikasiBedah($pasienpenunjangId);
      foreach($timOperasi as $key => $tim){
         $persentase = ArrayHelper::getValue($tim, 'persentase', 0);
         $timOperasiId = ArrayHelper::getValue($tim, 'timoperasi_id');
         $posisiTim = ArrayHelper::getValue($tim, 'posisi_tim');
         $pegawaiId = ArrayHelper::getValue($tim, 'pegawai_id');
         $tindakanId = ArrayHelper::getValue($tim, 'daftartindakan_id');
         $daftarTindakanId[] = $tindakanId;
         $tindakanPegawai[$pegawaiId][] = $tindakanId;
         $additionalData = ArrayHelper::getValue($tim, 'additional_data', []);
         $additionalData = !empty($additionalData) ? json_decode($additionalData, true) : [];
         $isCitoAdditional = ArrayHelper::getValue($additionalData, 'is_cyto', false);
         $isPenyulitAdditional = ArrayHelper::getValue($additionalData, 'is_penyulit', false);
         $persentase = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['persentase'] : $persentase;
         $isCito = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['is_cyto'] : $isCitoAdditional;
         $qtyAdditional = ArrayHelper::getValue($additionalData, 'qty', 1);
         $hargaCitoAdditional = ArrayHelper::getValue($additionalData, 'harga_cyto', 0);
         $hargaPenyulitAdditional = ArrayHelper::getValue($additionalData, 'harga_penyulit', 0);
         $hargaSatuanAdditional = ArrayHelper::getValue($additionalData, 'harga', 0);
         $isPenyulit = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['is_penyulit'] : $isPenyulitAdditional;
         $qty = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['qty'] : $qtyAdditional;
         $hargaCito = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['harga_cyto'] : $hargaCitoAdditional;
         $hargaPenyulit = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['harga_penyulit'] : $hargaPenyulitAdditional;
         $hargaSatuan = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['harga'] : $hargaSatuanAdditional;
         $totalHarga = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['total_harga'] : 0;
         $totalHargaReal = isset($verifikasiBedah[$timOperasiId]) ? $verifikasiBedah[$timOperasiId]['total_harga_real'] : 0;

         $tim['useprice'] = true;
         $tim['perawat_id'] = null;
         $tim['tipepaket_id'] = null;
         $tim['qty'] = $qty;
         $tim['harga'] = $hargaSatuan;
         $tim['persentase'] = $persentase;
         $tim['total_harga'] = $totalHarga;
         $tim['total_harga_real'] = $totalHargaReal;
         $tim['is_cyto'] = ($posisiTim == $posisiDokterOperator) ? $isCito : false;
         $tim['is_penyulit'] = ($posisiTim == $posisiDokterOperator) ? $isPenyulit : false;
         $tim['persencyto_tindakan'] = ArrayHelper::getValue($additionalData, 'persencyto_tindakan', 0);
         $tim['persen_penyulit'] = ArrayHelper::getValue($additionalData, 'persen_penyulit', 0);
         $tim['harga_cyto'] = ($posisiTim == $posisiDokterOperator) ? $hargaCito : 0;
         $tim['harga_penyulit'] = ($posisiTim == $posisiDokterOperator) ? $hargaPenyulit : 0;
         $tim['is_akomodasi'] = false;
         $rawdata[$tindakanId][] = $tim;
      }
      
      $pasienOperasi = $this->getPasienOperasi($pasienpenunjangId);
      $this->_pasienOperasi = $pasienOperasi;
      $tarifAkomodasi = $this->getTarifAkomodasi($pasienOperasi, $daftarTindakanId);
      $tarifTindakan = $this->getTarifTindakan($pasienOperasi, $daftarTindakanId);
      $tarifFungsi = [];
      $tarifFungsiDokter = [];
      foreach($tarifTindakan as $tindakan){
         $dokter_id = ArrayHelper::getValue($tindakan, 'dokter_id');
         $daftartindakan_id = ArrayHelper::getValue($tindakan, 'daftartindakan_id');
         if(!empty($dokter_id) && !empty($daftartindakan_id)){
            $tarifFungsiDokter[$daftartindakan_id][$dokter_id] = $tindakan;
         }else{
            $tarifFungsi[$daftartindakan_id] = $tindakan;
         }
      }
      
      $persen_cyto = 0;
      $persen_penyulit = 0;
      $totalBills = $totalCyto = $totalPenyulit = 0;
      $harga = $harga_cyto = 0;

      /* untuk menentukan perhitungan bedah case 4, bedah dengan kasus satu dokter yang sama dengan satu sayatan */
      $grouping_dokter = [];
      
      /* */
      if(!empty($rawdata)) {
         foreach ($rawdata as $key => $value) {
            $harga_cyto = $harga_penyulit = 0;
            foreach ($value as $index => $item) {
               $daftartindakan_id = ArrayHelper::getValue($item, 'daftartindakan_id');
               $daftartindakan_nama = ArrayHelper::getValue($item, 'daftartindakan_nama');
               $dokter_id = ArrayHelper::getValue($item, 'pegawai_id');
               $persentase = ArrayHelper::getValue($item, 'persentase', 0);
               $totalHarga = ArrayHelper::getValue($item, 'total_harga', 0);
               $posisiTim = ArrayHelper::getValue($item, 'posisi_tim');
               $tarifOp = 0;
               $item['total_harga'] = $totalHarga;
               $arrTarif = isset($tarifFungsi[$daftartindakan_id]) ? $tarifFungsi[$daftartindakan_id] : [];
               if($posisiTim == $posisiDokterOperator) {
                  $isCito = ArrayHelper::getValue($item, 'is_cyto', false);
                  $isPenyulit = ArrayHelper::getValue($item, 'is_penyulit', false);
                  $arrTarif = isset($tarifFungsiDokter[$daftartindakan_id][$dokter_id]) ? $tarifFungsiDokter[$daftartindakan_id][$dokter_id] : $arrTarif;
                  $tarifPersenCito = ArrayHelper::getValue($arrTarif, 'persencyto_tindakan', 0);
                  $tarifPersenPenyulit = ArrayHelper::getValue($arrTarif, 'persen_penyulit', 0);
                  $harga = ArrayHelper::getValue($arrTarif, 'harga_tariftindakan', 0);
                  $qty = ArrayHelper::getValue($arrTarif, 'qty', 1);
                  $persen_cyto = !empty($arrTarif) ? $tarifPersenCito : 0;
                  $harga_cyto = ($isCito) ? ($persen_cyto/100) * $harga : 0;
                  $persen_penyulit = !empty($arrTarif) ? $tarifPersenPenyulit : 0;
                  $harga_penyulit = ($isPenyulit) ? ($persen_penyulit/100) * $harga : 0;
                  $totalHarga = ($harga + $harga_cyto + $harga_penyulit) * ($persentase / 100) * $qty;
                  $item['harga'] = $harga;
                  $item['total_harga'] = $totalHarga;
                  $item['total_harga_real'] = $totalHarga;
                  $tarifOp = $item['total_harga'];
                  $grouping_dokter[$daftartindakan_id]= [
                     'daftartindakan_id' => !empty($daftartindakan_id) ? $daftartindakan_id : null,
                     'dokter_id' => !empty($dokter_id) ? $dokter_id : null,
                     'harga' => $tarifOp,
                     'harga_satuan' => $harga,
                     'harga_cyto' => $harga_cyto,
                     'harga_penyulit' => $harga_penyulit
                  ];
               }
               else {
                  $dataMaping = $this->getMappingTindakanOperasi($item['posisi_tim']);
                  $tindakanJasaId = ArrayHelper::getValue($dataMaping, 'daftartindakan_id', 0);
                  $tindakanJasaNama = ArrayHelper::getValue($dataMaping, 'daftartindakan_nama', 0);
                  $item['daftartindakan_id'] = empty($tindakanJasaId) ? $daftartindakan_id : $tindakanJasaId;
                  $item['daftartindakan_nama'] = empty($tindakanJasaNama) ? $daftartindakan_nama : $tindakanJasaNama;
                  $hargaSatuan = isset($grouping_dokter[$daftartindakan_id]) ? $grouping_dokter[$daftartindakan_id]['harga_satuan'] : 0;
                  $hargaCyto = isset($grouping_dokter[$daftartindakan_id]) ? $grouping_dokter[$daftartindakan_id]['harga_cyto'] : 0;
                  $hargaPenyulit = isset($grouping_dokter[$daftartindakan_id]) ? $grouping_dokter[$daftartindakan_id]['harga_penyulit'] : 0;
                  $tarifOperator = $hargaSatuan + $hargaCyto + $hargaPenyulit;
                  $tarifPersenCito = 0;
                  $tarifPersenPenyulit = 0;
                  $item['harga'] = ($tarifOperator) * ($persentase / 100);
                  $item['total_harga'] = ($tarifOperator) * ($persentase / 100) * $qty;
                  $item['total_harga_real'] = ($tarifOperator) * ($persentase / 100) * $qty;
               }
               
               $item['persencyto_tindakan'] = $tarifPersenCito;
               $item['persen_penyulit'] = $tarifPersenPenyulit;
               $data[] = $item;
               $totalBills += $value[$index]['harga'];
               $totalCyto += $value[$index]['harga_cyto'];
               $totalPenyulit += $value[$index]['harga_penyulit'];
            }
         }
      }
      if(!empty($tarifAkomodasi)){
         $tarifKamar = $this->setTarifAkomodasi($tarifAkomodasi, $pasienpenunjangId);
         $data[] = $tarifKamar;
         $totalBills += ArrayHelper::getValue($tarifKamar, 'harga', 0);
      }
      
      $dokterId_tindakanLuarBedah = null;
      $dokterNama_tindakanLuarBedah = '';
      
      /** tindakan luar bedah */
      $tindakanLuarBedah = $this->getTindakanLuarBedah($pasienpenunjangId, $dokterId_tindakanLuarBedah, $dokterNama_tindakanLuarBedah);
      if(!empty($tindakanLuarBedah)){
         $data = array_merge($data, $tindakanLuarBedah);
      }

      /** penggunaan bmhp */
      $bmhp = $this->getBmhp($pasienpenunjangId, $ruangan_id);
      if(!empty($bmhp)){
         $data = array_merge($data, $bmhp);
      }

      $grandTotal = 0;
      if(!empty($data)) {
         foreach ($data as $key => $value) {
            $grandTotal += isset($value['total_harga']) ? $value['total_harga'] : 0;
         }
      }
      return [
         'detail' => $data,
         'total' => $grandTotal,
      ];
   }

   /* 
   * array example: 
   * [
   *   [
   *       'daftartindakan_id' => xxx, 
   *       'persencyto_tindakan' => xxx , 
   *       'persen_penyulit' => xxx,
   *       'harga_tariftindakan' => xxx
   *       'pegawai_id' => xxx
   *   ]
   * ]
   */    
   public function perhitunganCyto($item, $is_cyto, $is_penyulit)
   {
      $total = $item['harga_tariftindakan'];
      $tarifCyto = $tarifPenyulit = 0;
      if ($is_penyulit) {
         $tarifPenyulit = $total*($item['persen_penyulit'] / 100);
      }

      if($is_cyto){
         $tarifCyto = $total*($item['persencyto_tindakan'] / 100);
      }
      $total = (int) $total + $tarifCyto + $tarifPenyulit;

      if( !isset($this->tarifTertinggi[$item['pegawai_id']] ) ){
         $this->tarifTertinggi[$item['pegawai_id']] = 0;
      }
      $this->tarifTindakan[$item['pegawai_id']][$item['daftartindakan_id']] = $total;
      $this->tarifTertinggi[$item['pegawai_id']] =  $total > $this->tarifTertinggi[$item['pegawai_id']] 
         ? $total : $this->tarifTertinggi[$item['pegawai_id']];

      return $total;
   }

   /* 
   * array example: 
   * [
   *   [
   *       'posisi_tim' => xxx, 
   *       'harga_tariftindakan' => xxx, 
   *       'persencyto_tindakan' => xxx , 
   *   ]
   * ]
   */ 
   public function setJasa($getPresentase)
   {
      $tarifTertinggi = $this->getHighestOperator();
      return (int) $tarifTertinggi * ($getPresentase / 100);
   }
   
   /* 
   * array example: 
   * [
   *     509 => [
   *         '232-1094' => '40.00',
   *         '233-1094' => '30.00',
   *     ],
   * ]
   */ 
   public static function getHighestPrecentage($item)
   {
      return reset($item);    
   }

   /* 
   * array example: 
   * [
   *   [
   *       'daftartindakan_id' => xxx, 
   *       'daftartindakan_nama' => xxx, 
   *       'persencyto_tindakan' => xxx ,
   *       'harga_tariftindakan' => xxx,
   *       'kamarruangan_nokamar' => xxx
   *   ]
   * ]
   */   
   public function setTarifAkomodasi($tarifAkomodasi, $pasienpenunjang_id)
   {
      $pasienOperasi = $this->_pasienOperasi;
      $namaKamar = isset($pasienOperasi['kamarruangan_nama']) ? $pasienOperasi['kamarruangan_nama'] : '';
      $kamar = isset($tarifAkomodasi['kamarruangan_nokamar']) ? $tarifAkomodasi['kamarruangan_nokamar'] : '('.$namaKamar.')';
      $tarif = 0;
      if(isset($tarifAkomodasi['tarif'])) {
         $tarif = $tarifAkomodasi['tarif'];
      }
      elseif(isset($tarifAkomodasi['harga_tariftindakan'])) {
         $tarif = $tarifAkomodasi['harga_tariftindakan'];
      }

      $perhitungan_durasi = isset($tarifAkomodasi['perhitungan_durasi']) ? $tarifAkomodasi['perhitungan_durasi'] : true;
      $data = [
         'pasienmasukpenunjang_id' => $pasienpenunjang_id,
         'operasi_nama' => '-',
         'posisi' => '-',
         'golonganoperasi_nama' => '-',
         'nama_pegawai' => '-',
         'pegawai_input' => '-',
         'dokter_id' => null,
         'perawat_id' => null,
         'tipepaket_id' => null,
         'daftartindakan_id' => isset($tarifAkomodasi['daftartindakan_id']) ? $tarifAkomodasi['daftartindakan_id'] : null,
         'is_cyto' => false,
         'is_penyulit' => false,
         'qty' => 1,
         'harga' => 0,
         'daftartindakan_nama' => isset($tarifAkomodasi['daftartindakan_nama']) ? $tarifAkomodasi['daftartindakan_nama']. ' ' . $kamar : '',
         'useprice' => true,
         'is_akomodasi' => true,
      ];

      $getDataOperasi = InpostOperasi::find()->select([
               'inpostoperasi_t.mulai_operasi',
               'inpostoperasi_t.selesai_operasi',
               '(EXTRACT(EPOCH FROM inpostoperasi_t.selesai_operasi) - EXTRACT(EPOCH FROM inpostoperasi_t.mulai_operasi))/3600 as timediff',
               'kamarruangan_m.durasi'
      ])->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $pasienpenunjang_id ])
      ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id')
      ->rightJoin('kamarruangan_m', 'kamarruangan_m.kamarruangan_id = pasienmasukpenunjang_t.kamarruangan_id')
      ->asArray()->one();

      $data['harga'] = $tarif;
      if($perhitungan_durasi) {
         $persenKenaikanDurasi = 25;
         if($getDataOperasi['timediff'] > $getDataOperasi['durasi']){
            $kelebihandurasi = $getDataOperasi['timediff'] - $getDataOperasi['durasi'];
            $persenKenaikanDurasi = $persenKenaikanDurasi * abs($kelebihandurasi);
            if($persenKenaikanDurasi >= 100){
               $persenKenaikanDurasi = 100;
            }
            $data['harga'] += ($tarif * ($persenKenaikanDurasi/100));
         }
      }
      $data['total_harga'] = $data['harga'];
      return $data;
   }
   
   private function getHighestOperator()
   {
      $total = 0;
      if( !empty($this->tarifOperator) ){
         foreach($this->tarifOperator as $tarif){
               $total = $total > $tarif['harga'] ? $total : $tarif['harga'];
         }
      }
      return $total;
   }

   public function getPasienOperasi($pasienpenunjangId)
   {
      return InfoPasienOperasiView::find()
         ->select([
            'ruangan_id',
            'penjamin_id',
            'kelaspelayanan_id',
            'kamarruangan_id',
            'kamarruangan_nama',
            'pendaftaran_id',
         ])
         ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
         ->asArray()
         ->one();
   }

   public function getTimOperasi($pasienpenunjangId)
   {
      $query = "SELECT * FROM inpostoperasi1_v WHERE pasienmasukpenunjang_id = {$pasienpenunjangId} ORDER BY timoperasi_id ASC";
      $query = Yii::$app->db->createCommand($query)->queryAll();
      $newArr = array_column($query, 'posisi_tim');
      array_multisort($newArr, SORT_ASC, $query);
      return $query;
   }

   public function getTarifTindakan($data, $daftarTindakanId)
   {
      $pendaftaran_id = isset($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null;
      $kelaspelayanan_id = isset($data['kelaspelayanan_id']) ? $data['kelaspelayanan_id'] : null;
      $kelasPelayananId = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
      $where = ['daftartindakan_id' => $daftarTindakanId];
      $tarif = (new TarifTotalFn([
         'extParam' => [
               $data['ruangan_id'],
               $data['penjamin_id'],
               $kelasPelayananId,
               'penunjang'
         ]
      ]))
      ->find()
      ->select([
               'daftartindakan_id',
               'daftartindakan_nama',
               'kamarruangan_nokamar',
               'harga_tariftindakan',
               'persencyto_tindakan',
               'persen_penyulit',
               'dokter_id'
      ])
      ->where($where)
      ->asArray();
      
      return $tarif->all();
   }

   public function getTarifAkomodasi($data, $daftarTindakanId)
   {
      $_GET['daftarTindakanId'] = $daftarTindakanId;
      $pendaftaran_id = isset($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null;
      $kelaspelayanan_id = isset($data['kelaspelayanan_id']) ? $data['kelaspelayanan_id'] : null;
      $kelasDitagihkan = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
      $data['kelaspelayanan_id'] = $kelasDitagihkan;
      $_GET['data'] = $data;
      return Yii::$app->docoPlugin->execute('tarif_akomodasi_ot');
   }

   public function getTindakanLuarBedah($pasienPenunjangId, $dokterId_tindakanLuarBedah, $dokterNama_tindakanLuarBedah)
   {
      $tindakanLuarOperasiRecord = TindakanLuarOperasi::find()
         ->select([
            "tindakanluaroperasi_t.pasienmasukpenunjang_id",
            "tindakanluaroperasi_t.daftartindakan_id",
            "tindakanluaroperasi_t.qty",
            "tindakanluaroperasi_t.harga",
            "daftartindakan_m.daftartindakan_nama",
            "pegawailogin.nama_pegawai as pegawai_input"
         ])
         ->where(['pasienmasukpenunjang_id' => $pasienPenunjangId])
         ->rightJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tindakanluaroperasi_t.daftartindakan_id')
         ->rightJoin('loginpemakai_k', 'tindakanluaroperasi_t.created_by = loginpemakai_k.loginpemakai_id')
         ->rightJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
         ->asArray()->all();

      $data = [];

      if(!empty($tindakanLuarOperasiRecord)){
         foreach($tindakanLuarOperasiRecord as $tindakan){
            $array= [
               'pasienmasukpenunjang_id' => $pasienPenunjangId,
               'dokter_id' => $dokterId_tindakanLuarBedah,
               'perawat_id' => null,
               'tipepaket_id' => null,
               'daftartindakan_id' => $tindakan['daftartindakan_id'],
               'is_cyto' => false,
               'is_penyulit' => false,
               'qty' => $tindakan['qty'],
               'operasi_nama' => "-",
               'posisi' => '-',
               'golonganoperasi_nama' =>"-",
               'nama_pegawai' => $dokterNama_tindakanLuarBedah,
               'pegawai_input' =>  $tindakan['pegawai_input'],
               'harga' =>  $tindakan['harga'],
               'total_harga' =>  $tindakan['harga'] * $tindakan['qty'],
               'daftartindakan_nama' => $tindakan['daftartindakan_nama'],
               'useprice' => false
            ];
            $data[] = $array;
         }
      };
      return $data;
   }

   public function getBmhp($pasienPenunjangId, $ruangan_id)
   {
      $data = "SELECT 
         bmhpoperasi_t.pasienmasukpenunjang_id, 
         bmhpoperasi_t.daftartindakan_id,
         bmhpoperasi_t.obatalkes_id,
         obatalkes_m.obatalkes_nama,
         bmhpoperasi_t.terpakai,
         bmhpoperasi_t.is_ditagihkan,
         pasienmasukpenunjang_t.ruangan_id,
         pasienmasukpenunjang_t.pendaftaran_id,
         pasienmasukpenunjang_t.pasien_id,
         COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) AS penjamin_id,
         COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) AS carabayar_id,
         pasienmasukpenunjang_t.kelaspelayanan_id,
         pasienmasukpenunjang_t.pasienadmisi_id,
         pegawai_m.nama_pegawai AS pegawai_input
         FROM bmhpoperasi_t 
         JOIN obatalkes_m ON obatalkes_m.obatalkes_id = bmhpoperasi_t.obatalkes_id
         JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = bmhpoperasi_t.pasienmasukpenunjang_id
         JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
         LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = bmhpoperasi_t.created_by
         JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
         WHERE bmhpoperasi_t.pasienmasukpenunjang_id = {$pasienPenunjangId}";

      $data = Yii::$app->db->createCommand($data)->queryAll();
      $bmhpList = $listObat = $stokObatAlkes = $dataInsert = $ditagihkan = [];
      $stringInObatCondition = '(';
      $params = [
         ':penjamin_id' => null,
         ':ruangan_id' => $ruangan_id,
         ':kelaspelayanan_id' => null,
      ];
      if(!empty($data)) {
         foreach ($data as $key => $obat) {
            if( !in_array($obat['obatalkes_id'], $listObat) ){
               $stringInObatCondition .= $obat['obatalkes_id'].',';
            }
            if( is_null($params[':penjamin_id']) ){
                  $params[':penjamin_id'] = $obat['penjamin_id'];
            }
            if( is_null($params[':ruangan_id']) ){
                  $params[':ruangan_id'] = $obat['ruangan_id'];
            }
            if( is_null($params[':kelaspelayanan_id']) ){
                  $params[':kelaspelayanan_id'] = $obat['kelaspelayanan_id'];
            }
            $ditagihkan[] = $obat['is_ditagihkan'];
            $bmhpList[] = [
               'pasienmasukpenunjang_id' => $obat['pasienmasukpenunjang_id'],
               'dokter_id' => null,
               'perawat_id' => null,
               'tipepaket_id' => null,
               'obatalkes_id' => $obat['obatalkes_id'],
               'obatalkes_nama' => $obat['obatalkes_nama'],
               'is_cyto' => false,
               'is_penyulit' => false,
               'qty' => $obat['terpakai'],
               'operasi_nama' => "-",
               'posisi' => '-',
               'golonganoperasi_nama' =>"-",
               'nama_pegawai' => null,
               'pegawai_input' =>  $obat['pegawai_input'],
               'harga' =>  null,
               'total_harga' =>  null,
               'useprice' => false
            ];
         }

         $stringInObatCondition = rtrim($stringInObatCondition, ',');
         $stringInObatCondition .= ')';
         $getObatTarif = Yii::$app->db->createCommand('
               select obatalkes_id, satuankecil_id, hargaygdipakai, harganetto_ygdipakai, jml_hargajual 
               from infostokobatalkes_fnr_new(:penjamin_id, :kelaspelayanan_id, :ruangan_id) where obatalkes_id IN '.$stringInObatCondition);
         
         $getObatTarif->bindValues($params);
         $getObatTarif = $getObatTarif->queryAll();
         $obatTarif = [];
         if(!empty($getObatTarif)) {
            foreach($getObatTarif as $itemObat){
               $obatTarif[$itemObat['obatalkes_id']] = $itemObat;
            }
         }
         if(!empty($obatTarif)) {
            foreach($bmhpList as $index => $item) {
               if(isset($bmhpList[$index])) {
                  $bmhpList[$index]['harga'] = isset($obatTarif[$item['obatalkes_id']]) ? (float) $obatTarif[$item['obatalkes_id']]['hargaygdipakai'] : 0;
                  if(isset($ditagihkan[$index]) && $ditagihkan[$index] == FALSE) {
                     $bmhpList[$index]['harga'] = 0;
                  }
                  $bmhpList[$index]['total_harga'] = $bmhpList[$index]['qty'] * $bmhpList[$index]['harga'];
                  $bmhpList[$index]['daftartindakan_id'] = $bmhpList[$index]['obatalkes_id'];
                  $bmhpList[$index]['daftartindakan_nama'] = $bmhpList[$index]['obatalkes_nama'];
               }
            }
         }
         return $bmhpList;
      }
   }

   private function getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id)
   {
      $admisi = "SELECT pendaftaran_id, kelaspelayanan_id, kelas_ditagihkan_id, is_pasientitipan
      FROM infopasienri_v
      WHERE pendaftaran_id = {$pendaftaran_id}";
      $admisi = Yii::$app->db->createCommand($admisi)->queryOne();
      $kelasPelayananId = $kelaspelayanan_id;
      if($admisi) {
         if(!empty($admisi)) {
            $isTitipan = isset($admisi['is_pasientitipan']) ? $admisi['is_pasientitipan'] : false;
            if($isTitipan) {
               if(isset($admisi['kelas_ditagihkan_id'])) {
                  $kelasPelayananId = $admisi['kelas_ditagihkan_id'];
               }
            }
         }
      }
      return $kelasPelayananId;
   }

   private function getMappingTindakanOperasi($kode_posisi)
   {
      $data = "SELECT tindakanoperasi_mp.daftartindakan_id, daftartindakan_m.daftartindakan_nama, tindakanoperasi_mp.prosentase
      FROM tindakanoperasi_mp 
      JOIN daftartindakan_m ON tindakanoperasi_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
      WHERE tindakanoperasi_mp.timoperasi_id = $kode_posisi";
      return Yii::$app->db->createCommand($data)->queryOne();
   }

   private function getVerifikasiBedah($penunjangId)
   {
      $results = [];
      $data = VerifikasiBedahR::find()->where(['pasienmasukpenunjang_id' => $penunjangId])->all();
      if(!empty($data)) {
         foreach ($data as $key => $value) {
            $timOperasiId = ArrayHelper::getValue($value, 'timoperasi_id', null);
            $results[$timOperasiId] = $value;
         }
      }
      return $results;
   }
}
