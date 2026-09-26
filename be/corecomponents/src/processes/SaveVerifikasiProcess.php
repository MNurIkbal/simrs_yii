<?php
/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\Services\KasirService;
use SirsCore\businessLogic\TagihanBedah;
use app\modules\v1\traits\IntegrateKasirTrait;
use Doco\models\Bedah\InpostOperasi;
use Doco\models\Bedah\InpostOperasiDetail;
use Doco\models\Bedah\InfoPasienOperasiView;
use Doco\models\Bedah\PasienMasukPenunjang;
use Doco\models\Bedah\PemeriksaanPelengkap;
use Doco\models\Bedah\VerifikasiBedahR;
use Doco\models\PermintaanKepenunjangan;
use Doco\models\PasienKirimUnitlain;
use yii\db\Expression;

class SaveVerifikasiProcess extends \Doco\components\DocoBaseProcessExtension
{
   use IntegrateKasirTrait;

   const NON_BLOCKING = 'nonblocking';

   public $request;

   /**
      * [$pasienpenunjangId pasien penunjang id]
      * @var integer
      */
   protected $pasienpenunjangId;

   /**
      * [$verifikasibedah data log verifikasi]
      * @var array
      */
   protected $verifikasibedah;

   /**
      * [$headerBill header bill]
      * @var array
      */
   protected $headerBill;

   /**
      * [$dokter_tindakanluarbedah data dokter_id u/ tindakan luar bedah]
      * @var integer
      */
   protected $dokter_tindakanluarbedah;

   protected $ruangan_id;

   protected $kelas_ditagihkan_id;

   protected function validation()
   {
      $pasienpenunjangId = $this->_requestData->post('pasienpenunjangId', null);
      if( is_null($pasienpenunjangId) ){
         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Parameter pasienpenunjangId tidak boleh kosong!"
         ]);
      }

      //validasi jika ada yang melakukan verifikasi dua kali
      if(!empty($pasienpenunjangId)){
         $dataPenunjang = PasienMasukPenunjang::find()->select([
            'status_periksa'
         ])->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])->one();
         
         if(!empty($dataPenunjang->status_periksa) && $dataPenunjang->status_periksa == DocoConstants::VAR_P_SdhO){
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
               'text' => "Operasi ini sudah diverifikasi! Silakan refresh halaman."
            ]);
            
         }
      }

      $headerBill = InpostOperasi::find()->select([
         'inpostoperasi_t.pasienmasukpenunjang_id',
         'inpostoperasi_t.inpostoperasi_id',
         'pendaftaran_t.no_pendaftaran',
         'ruangan_m.instalasi_id',
         'pasienmasukpenunjang_t.ruangan_id',
         'pasienmasukpenunjang_t.kelaspelayanan_id',
            new Expression('COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) as penjamin_id'),
         'pendaftaran_t.pasien_id',
         'pendaftaran_t.pasienadmisi_id',
         'pendaftaran_t.pendaftaran_id',
      ])
      ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id')
      ->rightJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id')
      ->rightJoin('ruangan_m', 'pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id')
      ->leftJoin('pasienadmisi_t', 'pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')
      ->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $pasienpenunjangId])
      ->asArray()->one();

      if( empty($headerBill) ){
         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Data Header Operasi Tidak Ditemukan!"
         ]);
      }

      $kelaspelayanan_id = !empty($headerBill['kelaspelayanan_id']) ? $headerBill['kelaspelayanan_id'] : null;
      $pendaftaran_id = !empty($headerBill['pendaftaran_id']) ? $headerBill['pendaftaran_id'] : null;
      if(!empty($pendaftaran_id)){
         $kelaspelayanan_id = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
      }
      $this->kelas_ditagihkan_id = $kelaspelayanan_id;

      $headerBill['kelaspelayanan_id'] = $kelaspelayanan_id;

      $this->headerBill = $headerBill;
      $this->pasienpenunjangId = $pasienpenunjangId;
      $ruangan_id = isset($headerBill['ruangan_id']) ? $headerBill['ruangan_id'] : null;
      $this->ruangan_id = $ruangan_id;
      $data = [];
      $dokter_tindakanluarbedah = null;
      $tarifmax = 0;
      $posisiOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      $verifikasibedah = VerifikasiBedahR::find()
         ->select(['verifikasibedah_r.*', 'timoperasi_t.daftartindakan_id AS parent_tim', 'timoperasi_t.timoperasi_id'])
         ->leftJoin('timoperasi_t', 'timoperasi_t.timoperasi_id = verifikasibedah_r.timoperasi_id')
         ->where(['verifikasibedah_r.pasienmasukpenunjang_id' => $pasienpenunjangId])->asArray()->all();
      
      $listBelumMaping = [];
      foreach($verifikasibedah as $row){
         if($row['useprice']) {
            if(!empty($row['daftartindakan_id'])) {
               $is_dokteroperator = ($row['kode_posisi'] == $posisiOperator) ? true : false;
               $harga = !empty($row['harga']) ? $row['harga'] : 0;
               $persencyto_tindakan = !empty($row['persencyto_tindakan']) ? $row['persencyto_tindakan'] : 0;
               $persen_penyulit = !empty($row['persen_penyulit']) ? $row['persen_penyulit'] : 0;
               $harga = $harga * ($row['persentase']/100);
               $is_cyto = !empty($row['is_cyto']) ? $row['is_cyto'] : false;
               $is_penyulit = !empty($row['is_penyulit']) ? $row['is_penyulit'] : false;
               if($is_dokteroperator){
                  $row['harga'] = $harga;
                  $row['harga_cyto'] = $is_cyto ? ($harga * ($persencyto_tindakan/100)) : 0;
                  $row['harga_penyulit'] = $is_penyulit ? ($harga * ($persen_penyulit/100)) : 0;
                  $row['parent_tim'] = null;
               }else{
                  $row['harga_cyto'] = 0;
                  $row['harga_penyulit'] = 0;
                  // $row['parent'] = null;
               }
               array_push($data, $row);

               /** Untuk menentukan dokter_id yang akan dikirimkan ke Odoo jika ada tindakan luar bedah atau obat alkes
               * Dokter ID diambil dari dokter operator bedah yang memiliki harga tindakan paling tinggi
               */
               if($is_dokteroperator){
                  if($row['total_harga']  > $tarifmax ){
                     $tarifmax = $row['total_harga'];
                     $dokter_tindakanluarbedah = $row['dokter_id'];
                  }
               }
            }
            else {
               $listBelumMaping[] = $row['posisi_tim'];
            }
         }else{
            // array_push($data, $row);
         }
      }
      $posisiBelumMaping = '';
      if(!empty($listBelumMaping)) {
         foreach ($listBelumMaping as $key => $value) {
            $posisiBelumMaping[] = $value;
         }
         $posisiBelumMaping = implode(", ", $posisiBelumMaping);
         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => 'Posisi Tim : <br/> <strong>'.$posisiBelumMaping.'</strong> <br/> Belum di Mapping ke Tindakan Operasi.'
         ]);
      }
      $this->verifikasibedah = $data;
      $this->dokter_tindakanluarbedah = $dokter_tindakanluarbedah;
   }

   protected function save()
   {
      $this->saveIntraOp();

      //integrasi tagihan ke kasir
      $tagihanKasir = (new KasirService)->tagihanPenunjang($this->headerBill, $this->verifikasibedah);
      if(!empty($tagihanKasir)){
         if(isset($tagihanKasir['meta']['result']) && $tagihanKasir['meta']['result'] == 'failed'){
            $message = isset($tagihanKasir['message']) ? $tagihanKasir['message'] : 'Terjadi kesalahan saat integerasi dengan kasir'; 
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
               'text' => $message
            ]);
         }
      }
      /**ini tidak dipakai karena tindakan luar bedah sudah ditampilkan dihalaman verifikasi, dan masuk di param verifikasibedah */
      //integrasi tindakan luar bedah ke kasir
      $tindakanLuarBedah = $this->integrateTindakanLuarBedah($this->pasienpenunjangId, true, $this->dokter_tindakanluarbedah);
      $this->pemeriksaanPelengkap($this->pasienpenunjangId, $this->headerBill);

      //integrasi obat ke kasir
      $pendaftaranId = ArrayHelper::getValue($this->headerBill, 'pendaftaran_id');
      $this->sentToApotek($this->pasienpenunjangId, true, $this->dokter_tindakanluarbedah, $this->ruangan_id, $pendaftaranId);
   }

   protected function updatePenunjang()
   {
      $updateBills = InpostOperasiDetail::updateAll([
         'is_verifikasi' => true
      ], "inpostoperasi_id = ".$this->headerBill['inpostoperasi_id']);

      $updatePenunjang = PasienMasukPenunjang::updateAll([
         'status_periksa' => DocoConstants::VAR_P_SdhO
      ], 'pasienmasukpenunjang_id = '.$this->pasienpenunjangId);

   }

   private function pemeriksaanPelengkap($penunjangId, $data = [])
   {
      $instLab = DocoConstansId::actionGetId('LAB');
      $pegawai_id = Yii::$app->jwt->user->pegawai_id;
      $headers = Yii::$app->request->headers;
      $restSerconn = Yii::$app->serconn->guzzle(self::NON_BLOCKING);
      $getPelengkap = PemeriksaanPelengkap::find()
         ->selectAttr()
         ->findByPenunjangId($penunjangId)
         ->getDataArray();

      $listKirimUnitLain = $getPermintaanKepenunjang = $listTindakanId = [];
      $getKirimUnitLain = PasienKirimUnitLain::find()
         ->where(['ref_pasienmasukpenunjang_id' => $penunjangId])
         ->asArray()->All();
      
      if(!empty($getKirimUnitLain)){
         foreach($getKirimUnitLain as $v){
            $listKirimUnitLain[] = !empty($v['pasienkirimkeunitlain_id']) ? $v['pasienkirimkeunitlain_id'] : null; 
         }
         if(!empty($listKirimUnitLain)){
            $getPermintaanKepenunjang = PermintaanKepenunjangan::find()
               ->where(['IN', 'pasienkirimkeunitlain_id', $listKirimUnitLain ])
               ->asArray()->All();
         }
         if(!empty($getPermintaanKepenunjang)){
            foreach($getPermintaanKepenunjang as $v){
               $listTindakanId[] = !empty($v['daftartindakan_id']) ? $v['daftartindakan_id'] : null; 
            }
         }
      }

      foreach ($getPelengkap as $tindakan) {
         $daftartindakanId = $tindakan['daftartindakan_id'] ? $tindakan['daftartindakan_id'] : null;
         if(in_array($daftartindakanId, $listTindakanId)){
            continue;
         }
         $namaJaringan = isset($tindakan['nama_jaringan']) ? $tindakan['nama_jaringan'] : null;
         $ukuran = isset($tindakan['qty']) ? $tindakan['qty'] : null;
         $ruanganId = isset($tindakan['ruangan_id']) ? $tindakan['ruangan_id'] : null;
         $additional = !empty($tindakan['additional_data']) ? json_decode($tindakan['additional_data'],true) : [];
         $tarifId = isset($additional['tariftindakan_id']) ? $additional['tariftindakan_id'] : null;
         $catatan = "Nama Jaringan : $namaJaringan \nUkuran : $ukuran";

         $orderPenunjang = [
            'authorization' => isset($headers['authorization']) ? $headers['authorization'] : null,
            'x-owner' => isset($headers['x-owner']) ? $headers['x-owner'] : null,
            'pegawai_id' => $pegawai_id,
            'instalasi_id' => $instLab,
            'pasien_id' => isset($data['pasien_id']) ? $data['pasien_id'] : null,
            'ref_pasienmasukpenunjang_id' => isset($penunjangId) ? $penunjangId : null, // untuk menandakan bahwa orderan berasal dari bedah
            'pendaftaran_id' => isset($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null,
            'kelaspelayanan_id' => isset($data['kelaspelayanan_id']) ? $data['kelaspelayanan_id'] : null,
            'ruangan_id' => $ruanganId,
            'tgl_kirimpasien' => date('Y-m-d H:i:s'),
            'catatan_dokterpengirim' => $catatan,
            'pasienadmisi_id' => isset($data['pasienadmisi_id']) ? $data['pasienadmisi_id'] : null,
            'list_order' => json_encode([
               [
                  'is_paket' => false,
                  'is_cyto' => !empty($tindakan['is_cyto']) ? $tindakan['is_cyto'] : 0,
                  'tariftindakan_id' => $tarifId
               ]
            ]),
         ];

         try {
            if(empty($orderPenunjang)){
               /** Prevent jika tidak ada pemeriksaan yang diorder */
               return true;
            }
            
            $endpoint = 'on/integerasipenunjang/order';
            $payload =  json_encode($orderPenunjang);

            $request = $restSerconn->post($endpoint, [
               'body' => $payload
            ]);

            $response = json_decode($request->getBody(), true);
            $prosesUid = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;

            Yii::error(
               'Message : API PENUNJANG SUKSES '. $prosesUid .' --||--Line : NULL --||--File : VerifikasiTagihanController.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($payload),
               'server-error'
            );
         } catch (RequestException $e) {
            Yii::error([
               'Error' => $e->getMessage(),
               'Line' => $e->getLine(),
               'File' => $e->getFile()
            ]);
         }
      }

      return true;
   }

   protected function saveIntraOp()
   {
      $model = InpostOperasi::find()->where(['pasienmasukpenunjang_id' => $this->pasienpenunjangId])->one();
      $dokterbedah_id = isset($model['dokterbedah_id']) ? $model['dokterbedah_id'] : null;
      if(!empty($model)) {
         $additionalData = json_encode([
            'pos' => 4
         ]);
         $model->additional_data = $additionalData;
         if (!$model->validate() || !$model->save()) {
            \Yii::error(["errors" => $model->getErrors()]);
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
               'text' => "Terjadi kesalahan saat simpan Verifikasi!"
            ]);
         }
      }
   }

   protected function getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id)
   {
      $admisi = "SELECT pendaftaran_id, kelaspelayanan_id, kelas_ditagihkan_id, is_pasientitipan
         FROM infopasienri_v
         WHERE pendaftaran_id = {$pendaftaran_id}";
      $admisi = Yii::$app->db->createCommand($admisi)->queryOne();
      $kelasPelayananId = $kelaspelayanan_id;
      if($admisi) {
         if(!empty($admisi)) {
            $isTitipan = !empty($admisi['is_pasientitipan']) ? $admisi['is_pasientitipan'] : false;
            if($isTitipan) {
               if(!empty($admisi['kelas_ditagihkan_id'])) {
                  $kelasPelayananId = $admisi['kelas_ditagihkan_id'];
               }
            }
         }
      }
      return $kelasPelayananId;
   }

   protected function processFlow()
   {
      $this->validation();
      $this->startDBTransaction();
      $this->save();
      $this->updatePenunjang();
      $this->commitDBTransaction();
      return [
         'title' => '<b> Verifikasi Tagihan Bedah Sentral Berhasil! </b>',
         'message' => 'Anda akan diarahkan kembali ke halaman informasi pasien operasi'
      ];
   }
}
