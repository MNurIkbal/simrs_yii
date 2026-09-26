<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\penatajasa\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\penatajasa\models\TindakanForm;
use app\modules\penatajasa\models\HapusTindakanForm;
use app\modules\penatajasa\models\EditTindakanForm;

trait TagihanTindakanTrait
{
   public function actionGetDataTindakan()
   {
      return Yii::$app->docoPlugin->execute($this,'get_data');

   }

   protected function setSessionTindakan($data, $pendaftaranID, $init = false)
   {
      $session = Yii::$app->session;
      if($init){
         $session->set('tagihanTindakan-pasien-'.$pendaftaranID, $data);
      }else{
         $sessionData = $session->get('tagihanTindakan-pasien-'.$pendaftaranID);
         $session->set('tagihanTindakan-pasien-'.$pendaftaranID, $data);
      }
      return true;
   }

   public function actionAddTindakan()
   {
      $title = 'Tambah Tindakan & BMHP';
      $model = new TindakanForm;
      $request = Yii::$app->request;
      $pendaftaran_id = $request->get('pendaftaran_id');
      $pendaftaranID = DocoHelpers::decrypt( $pendaftaran_id );
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cache = Yii::$app->cache;

      try {
         $tindakanPelayananId = $kelompokTindakanId = null;
         $model->daftartindakan_id = '';
         $attributes = $this->getAttibutes($pendaftaran_id);
         $dataPasien = $this->getPasien($pendaftaran_id);
         $instalasi = isset($attributes['instalasi']) ? $attributes['instalasi'] : [];
         $kelasPelayanan = isset($attributes['kelaspelayanan']) ? $attributes['kelaspelayanan'] : [];
         $jenisPelayanan = isset($attributes['jenisPelayanan']) ? $attributes['jenisPelayanan'] : [];
         $isValidasiTglAkomodasi = isset($attributes['penatajasa_validasi_tgl_akomodasi']) ? $attributes['penatajasa_validasi_tgl_akomodasi'] : true;
         $tindakanAkomodasi = isset($attributes['tindakanAkomodasi']) ? $attributes['tindakanAkomodasi'] : [];
         $konfigSystem = isset($attributes['konfigSystem']) ? $attributes['konfigSystem'] : [];
         $isEditBilling = !empty($konfigSystem['edit_billing']) ? $konfigSystem['edit_billing'] : false;
         $isShowObatForm = !empty($konfigSystem['is_show_obat_form_penatajasa']) ?  true : false; // null atau false == empty, jadi kalo tidak empty dianggap true
         $isHargaReadOnly = ($isEditBilling ? false : true);
         $konfigKelompokTindakan = !empty($konfigSystem['konfig_kelompok_tindakan']) ? $konfigSystem['konfig_kelompok_tindakan'] : [];
         $newKonfigKelompok = [];
         if(!empty($konfigKelompokTindakan)) {
            $konfigKelompokTindakan = json_decode($konfigKelompokTindakan, true);
            $konfigKelompokTindakan = ArrayHelper::getValue($konfigKelompokTindakan, 'konfig_kelompok_tindakan', []);
            foreach ($konfigKelompokTindakan as $key => $value) {
               if(is_array($value)) {
                  foreach ($value as $k => $val) {
                     $newKonfigKelompok[] = [
                        'kelompoktindakan_id' => $k,
                        'kelompoktindakan_nama' => $val,
                     ];
                  }
               }
            }
         }
         $model->instalasi_id = $dataPasien['instalasi_id'];
         $isPasienRI = ($model->instalasi_id == DocoConstants::INSTALASI_ID_RI) ? '' : 'disabled';
         $instalasi_ri =  DocoConstants::INSTALASI_ID_RI;
         $model->kelas_pelayanan = ($dataPasien['is_pasientitipan'])?$dataPasien['kelas_ditagihkan_id']:$dataPasien['kelaspelayanan_id'];
         $model->dokter_pj = $dataPasien['pegawai_id'];
         $tglPendaftaran = ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran_awal') != null ? ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran_awal') : ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran');
         $tglpasienpulang = $dataPasien['tglpasienpulang'];
         $status_periksa = $dataPasien['status_periksa'];
         $validasiTgl = $dataPasien['status_periksa'] == 'Pulang' ? $tglpasienpulang : $tglPendaftaran;
         $startDate = $tglPendaftaran;
         $endDate = $dataPasien['status_periksa'] == 'Pulang' ? $tglpasienpulang : (!empty($dataPasien['tglpasienpulang']) ? $dataPasien['tglpasienpulang'] : date('Y-m-d H:i:s'));
         $no_pendaftaran = $dataPasien['no_pendaftaran'];
         $penjamin_id = $dataPasien['penjamin_id'];
         $model->tanggal_tindakan = date('d-M-Y H:i:s', strtotime($endDate));

         $restPenatajasa = Yii::$app->docoRest->penatajasa;
         $getDepo = $restPenatajasa->get('paket-tindakan/get-depo', [
            'form_params' => []
         ]);
         $getDepo = json_decode($getDepo->getBody(), true);
         $listDepo = !empty($getDepo['response']) ? $getDepo['response'] : [];

         $getRuangan = $this->_penataJasa->get('informasi-pasien/get-list-ruangan',[
            'query'=>[
               'instalasi_id'=> $model->instalasi_id
            ],'form_params'=>[]
         ]);
         $getRuangan = json_decode($getRuangan->getBody(), True)['response'];
         $listRuangan = [];
         foreach($getRuangan as $key => $value){
            $listRuangan[] = [
               'ruangan_id' => $key,
               'ruangan_nama' => $value
            ];
         }
         $listDepo = array_merge($listDepo, $listRuangan);
         $listDepo = \yii\helpers\ArrayHelper::map($listDepo, 'ruangan_id', 'ruangan_nama');
         $valueTindakan = [];
         $cache->delete($this->prefixCacheName('bmhp'));
         $cache->delete($this->prefixCacheName('tindakan'));
      } catch (RequestException $e) {
         $kelasPelayanan = $instalasi = $jenisPelayanan = [];
         $tglPendaftaran = date('Y-m-d h:i:s');
      }
      $modul_id = Yii::$app->docoVars->workspace('modul_id');
      $path = Yii::$app->docoPlugin->execute($this, 'form_penatajasa');
      return $this->renderAjax($path,get_defined_vars());
   }

   public function getAttibutes($pendaftaran_id)
   {
      $response = $this->_penataJasa->get('informasi-pasien/get-attribute-tindakan',[
         'query' => [
               'pendaftaran_id' => $pendaftaran_id
         ]
      ]);
      $result= json_decode($response->getBody(),true)['response'];
      return $result;
   }

   public function actionGetKamar($ruangan_id)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $kamarruangan_id = $request->get('kamarruangan_id');
      $kelas_pelayanan = null;
      if (isset($_POST['depdrop_parents'])) {
         $parents = $_POST['depdrop_parents'];
         if ($parents != null) {
             $ruangan_id = $parents[0];
             $kelaspelayanan_id = $parents[1];
         }
      }
      $result = [];
      $result['output'] = [];
      $result['selected'] = $kamarruangan_id;

      try {
         $response = $this->_penataJasa->get('informasi-pasien/get-kamar',[
            'query' => [
                  'ruangan_id' => $ruangan_id,
                  'kelaspelayanan_id' => $kelaspelayanan_id
            ]
         ]);
         $data= json_decode($response->getBody(),true)['response'];
         foreach ($data as $key => $value)
                  $result['output'][] = [
                     'id' => $key,
                     'name' => $value
                  ];
         return $result;
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionGetAkomodasiRuangan()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $ruangan_id = $request->post('ruanganID');
      $penjamin_id = $request->post('penjaminID');
      $kelas_pelayanan = $request->post('kelasPelayanan');
      $kamar_ruangan = $request->post('kamarRuangan');
      $tempat_tidur = $request->post('tempatTidur');
      $response = $this->_penataJasa->get('informasi-pasien/get-akomodasi-ruangan',[
         'query' => [
               'ruangan_id' => $ruangan_id,
               'penjamin_id' => $penjamin_id,
               'kelas_pelayanan' => $kelas_pelayanan,
               'kamar_ruangan' => $kamar_ruangan,
               'tempat_tidur' => $tempat_tidur,
         ]
      ]);
      $result= json_decode($response->getBody(),true)['response'];
      return $result;
   }

   public function actionGetNoTempatTidur($ruangan_id)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $kamarruangan_id = null;
      $result = [];
      $result['output'] = [];
      if (isset($_POST['depdrop_parents'])) {
         $parents = $_POST['depdrop_parents'];
         if ($parents != null) {
             $kamarruangan_id = $parents[0];
             $ruangan_id = $parents[1];
             if(!is_numeric($kamarruangan_id) || !is_numeric($ruangan_id)){
               $result['output'][] = [
                     'id' => '',
                     'name' => '',
               ];
               return $result;
            }
         }
      }

      try {
         if(!empty($kamarruangan_id)){
            $response = $this->_penataJasa->get('informasi-pasien/get-no-tempat-tidur',[
               'query' => [
                     'ruangan_id' => $ruangan_id,
                     'kamarruangan_id' => $kamarruangan_id,
               ]
            ]);
            $data= json_decode($response->getBody(),true)['response'];
            foreach ($data as $key => $value)
                     $result['output'][] = [
                        'id' => $key,
                        'name' => $value
                     ];
            return $result;
         } else {
            return [];
         }
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionGetRuangan()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $depdrop_parents = $request->post('depdrop_parents');
      $ruangan_id = $request->get('ruangan_id');
      // $instalasi_id = $request->get('instalasi_id');
      $instalasi_id = $depdrop_parents[0];
      if ($request->post()) {
         $instalasi_id = $depdrop_parents[0];
      }
      $result = [];
      $result['output'] = [];
      $result['selected'] = $ruangan_id;

      try {
         $response = $this->_penataJasa->get('informasi-pasien/get-list-ruangan',[
               'query'=>[
                  'instalasi_id'=> $instalasi_id
               ],'form_params'=>[]
         ]);
         $getData = json_decode($response->getBody(), True)['response'];
         foreach ($getData as $key => $value)
               $result['output'][] = [
                  'id' => $key,
                  'name' => $value
               ];
         return $result;
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionGetDokter()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $depdrop_parents = $request->post('depdrop_parents');
      // $instalasi_id = $request->get('instalasi_id');
      $instalasi_id = $depdrop_parents[0];
      // $ruangan_id = $request->get('ruangan_id');
      $ruangan_id = $depdrop_parents[1];
      $dokter_pj = $request->get('dokter_pj');
      if(!is_numeric($instalasi_id) || !is_numeric($ruangan_id)){
         $result['output'][] = [
               'id' => '',
               'name' => '',
         ];
         return $result;
      }
      if ($request->post()) {
         $instalasi_id = $depdrop_parents[0];
         $ruangan_id = $depdrop_parents[1];
      }
      $result = [];
      $result['output'] = [];
      $result['selected'] = $dokter_pj;

      try {
         $response = $this->_penataJasa->get('informasi-pasien/get-dokter',[
               'query'=>[
                  'instalasi_id'=> $instalasi_id,
                  'ruangan_id'=> $ruangan_id
               ],'form_params'=>[]
         ]);
         $getData = json_decode($response->getBody(), True)['response'];
         foreach ($getData as $key => $value)
               $result['output'][] = [
                  'id' => $key,
                  'name' => $value
               ];
         return $result;
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionGetPerawat()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $depdrop_parents = $request->post('depdrop_parents');
      // $instalasi_id = $request->get('instalasi_id');
      $instalasi_id = $depdrop_parents[0];
      // $ruangan_id = $request->get('ruangan_id');
      $ruangan_id = $depdrop_parents[1];
      $perawat = $request->get('perawat');
      if(!is_numeric($instalasi_id) || !is_numeric($ruangan_id) ){
         $result['output'][] = [
               'id' => '',
               'name' => '',
         ];
         return $result;
      }
      if ($request->post()) {
         $instalasi_id = $depdrop_parents[0];
         $ruangan_id = $depdrop_parents[1];
      }
      $result = [];
      $result['output'] = [];
      $result['selected'] = $perawat;

      try
      {
         $response = $this->_penataJasa->get('informasi-pasien/get-perawat',[
               'query'=>[
                  'instalasi_id'=> $instalasi_id,
                  'ruangan_id'=> $ruangan_id
               ],'form_params'=>[]
         ]);
         $getData = json_decode($response->getBody(), True)['response'];
         foreach ($getData as $key => $value)
               $result['output'][] = [
                  'id' => $key,
                  'name' => $value
               ];
         return $result;
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionGetTindakan()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      try {
         $response = $this->_penataJasa->get('informasi-pasien/get-tindakan',[
               'query' => $get
         ]);
         $response = json_decode($response->getBody(),true);
         $data = [];
         foreach ($response['response'] as $key => $value) {
               $data[] = [
                  'id' => $value['tariftindakan_id'].'-'.$value['harga_tariftindakan'].'-'.$value['persencyto_tindakan'].'-'.$value['penjamin_id'],
                  'text' => $value['daftartindakan_nama'],
                  'harga_tariftindakan' => $value['harga_tariftindakan'],
                  'persencyto_tindakan' => $value['persencyto_tindakan']
               ];
         }
         $return = [
               'result' => $data,
               'total_count' => count($data),
               'incomplete_results' => false,
         ];
         return DocoHelpers::response($return);
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionSaveTindakan()
   {
      $request = Yii::$app->request;
      $randString = $request->post('unique_string');
      $session = Yii::$app->session;
      $pendaftaran_id = $request->get('pendaftaran_id');
      $pendaftaranID = DocoHelpers::encrypt($pendaftaran_id);
      $model = new TindakanForm;
      $cache = Yii::$app->cache;
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheObat = $cache->get($this->prefixCacheName('bmhp'));
      $cacheTindakan = $cache->get($this->prefixCacheName('tindakan'));
      $countObat = count($cacheObat);
      $countTindakan = count($cacheTindakan);
      $dataPost = $request->post()['TindakanForm'];
      $jenis_pelayanan = !empty($dataPost['jenis_pelayanan']) ? $dataPost['jenis_pelayanan'] : null;
      $tglpasienpulang = !empty($dataPost['tglpasienpulang']) ? $dataPost['tglpasienpulang'] : '';
      $alasan_edit_harga = !empty($dataPost['alasan_edit_harga']) ? $dataPost['alasan_edit_harga'] : null;
      $daftartindakan_id_list = [];

      if(empty($dataPost['daftartindakan_id']) && empty($dataPost['tipepaket_id']) && empty($cacheTindakan)) {
         $model->scenario = 'bmhp';
      }else {
         if(!empty($tglpasienpulang)) {
            $model->scenario = $model::PULANG;
         }else if($jenis_pelayanan == $model::TINDAKAN) {
            $model->scenario = $model::TINDAKAN;
         }elseif($jenis_pelayanan == $model::PAKET){
            $model->scenario = $model::PAKET;
         } else {
            $model->scenario = $model::AKOMODASI;
         }
      }

      // Validasi untuk skenario aslinya akomodasi & bukan UI Baru
      if($jenis_pelayanan == $model::AKOMODASI && empty($cacheTindakan) ){
         $model_akomodasi = new TindakanForm;
         $model_akomodasi->load($request->post());
         $model_akomodasi->scenario = $model_akomodasi::AKOMODASI;
         if(!$model_akomodasi->validate()){

            $error = $model_akomodasi->errors;
            return DocoHelpers::response($error,422,'TindakanForm');
         }

         $result_akomodasi = $this->_penataJasa->post('paket-tindakan/cek-akomodasi-tindakan', [
            'form_params' => [
               'data'=>$dataPost,
               'pendaftaran_id'=>$pendaftaran_id,
            ],
         ]);
         $resultAkomodasi = json_decode($result_akomodasi->getBody(), true);
         if($resultAkomodasi['response'] != false){
               $tgl_transaksi = date('d M Y',strtotime($dataPost['tanggal_tindakan']));
               $response['response']['title'] = 'Proses Gagal';
               $response['response']['text'] = 'Akomodasi tanggal '. $tgl_transaksi .', Kelas Pelayanan dan Kamar tersebut sudah ada';
               return DocoHelpers::response($response, 422);
         };
      }

      /** Validasi obat terlebih dahulu */
      if($cacheObat){
         $is_valid = true;
         $message = "";
         $fail_list = "";
         foreach($cacheObat as $key => $value){
            if(isset($value['is_available'])){
               if(!$value['is_available']){
                  $nama_bmhp = !empty($value['nama_bmhp']) ? $value['nama_bmhp'] : "";
                  $is_valid = false;
                  $strOp = !empty($fail_list) ? ", " : '';
                  $fail_list .= $strOp . $nama_bmhp;
                  $message = " tidak tersedia.";
               }
            }

            if(!empty($value['daftartindakan_id'])){
               $daftartindakan_id_list[] = $value['daftartindakan_id'];
            }
            if(isset($value['qty_obat'])){
               $value['qty_obat'] = DocoHelpers::convertToAngka($value['qty_obat']);
            }
            $cacheObat[$key] = $value;
         }

         if(!$is_valid){
            $message = "BMHP " . $fail_list . $message;
            $response['response']['title'] = 'Proses Gagal!';
            $response['response']['text'] = $message;
            return DocoHelpers::response($response,422);
         }
      }
      
      Yii::$app->redis->executeCommand('PUBLISH', [
          'channel' => 'integrasi-kasir:'.$randString,
          'message' => json_encode([
               'messageType' => 'total_data',
               'obat' => !empty($cacheObat) ? count($cacheObat) : 0,
               'tindakan' => !empty($cacheTindakan) ? count($cacheTindakan) : 0,
               'filename' => $randString,
           ]),
      ]);

      $model->load($request->post());

      if($model->scenario != 'bmhp'){

         if($cacheTindakan){

            $response = [];
            $cacheVal = [];
            foreach($cacheTindakan as $val){

               //Inject Alasan Edit Harga (Apabila Ada)
               $val['alasan'] = null;
               $isOverride = !empty($val['is_override']) ? $val['is_override'] : FALSE;
               if ($isOverride){
                  $val['alasan_edit_harga'] = $alasan_edit_harga;
               }

               // $response = $this->_penataJasa->post(
               //    'paket-tindakan/simpan', [
               //    'form_params' => $val
               // ]);
               
               $cacheVal[] = $val;
            }
            
            $response = $this->_penataJasa->post(
               'paket-tindakan/simpan-multiple', [
               'json' => [
                  'kunjungan' => $model->attributes,
                  'detail' => $cacheVal,
                  'randString' => $randString
               ]
            ]);
            
            $response = json_decode($response->getBody(), true);
            $response['randString'] = $randString;
            if(!$cacheObat){
               return DocoHelpers::response($response ,200);
            }
         }else if($model->validate()) {
            $qty = $dataPost['qty'];
            $is_cyto = $is_penyulit = false;
            $val_cyto = $val_penyulit = false;
            if (isset($dataPost['is_cyto']) && $dataPost['is_cyto'] == 1) {
               $is_cyto = $dataPost['is_cyto'];
               $val_cyto = true;
            }

            if (isset($dataPost['is_penyulit']) && $dataPost['is_penyulit'] == 1) {
               $is_penyulit = $dataPost['is_penyulit'];
               $val_penyulit = true;
            }

            $daftartindakan_id = !empty($dataPost['daftartindakan_id']) ? $dataPost['daftartindakan_id'] : null;
            $tipepaket_id = !empty($dataPost['tipepaket_id']) ? $dataPost['tipepaket_id'] : null;
            $harga_tariftindakan = !empty($dataPost['harga_tariftindakan']) ? $dataPost['harga_tariftindakan'] : null;
            $persencyto_tindakan = !empty($dataPost['persencyto_tindakan']) ? $dataPost['persencyto_tindakan'] : null;
            $no_pendaftaran = !empty($dataPost['no_pendaftaran']) ? $dataPost['no_pendaftaran'] : null;
            $penjamin_id = !empty($dataPost['penjamin_id']) ? $dataPost['penjamin_id'] : null;
            $is_ditagihkan = !empty($dataPost['is_ditagihkan']) ? $dataPost['is_ditagihkan'] : true;
            $instalasi_id = !empty($dataPost['instalasi_id']) ? $dataPost['instalasi_id'] : null;
            $ruangan_id = !empty($dataPost['ruangan_id']) ? $dataPost['ruangan_id'] : null;
            $kelas_pelayanan_id = !empty($dataPost['kelas_pelayanan']) ? $dataPost['kelas_pelayanan'] : null;
            $tglpasienpulang = !empty($dataPost['tglpasienpulang']) ? $dataPost['tglpasienpulang'] : '';
            $tglPendaftaran = !empty($dataPost['tglPendaftaran']) ? $dataPost['tglPendaftaran'] : '';
            $status_periksa = !empty($dataPost['status_periksa']) ? $dataPost['status_periksa'] : '';
            $dokter_id = !empty($dataPost['dokter_pj']) ? $dataPost['dokter_pj'] : null;
            $perawat_id = !empty($dataPost['perawat']) ? $dataPost['perawat'] : null;
            $time = date('H:i:s');
            $obatalkes_id ='';
            $tgl_tindakan = date('Y-m-d H:i:s',strtotime($dataPost['tanggal_tindakan']));
            $hargaTindakanAndQty = (int)$harga_tariftindakan * $qty;
            $hargatindakan = (int)$harga_tariftindakan;
            $cytoTindakan = ($hargatindakan*$persencyto_tindakan)/100;
            $hargatindakan = ((int)$hargatindakan + (int)$cytoTindakan)*$qty;
            $is_akomodasi = ($jenis_pelayanan == 'akomodasi') ? TRUE : FALSE;
            $is_half_day = ($dataPost['is_half']) ? TRUE : FALSE;
            $kamarruangan_id = (!empty($dataPost['kamarruangan_id']) && $jenis_pelayanan == 'akomodasi') ? $dataPost['kamarruangan_id'] : null;
            $kamartempattidur_id = (!empty($dataPost['kamartempattidur_id']) && $jenis_pelayanan == 'akomodasi') ? $dataPost['kamartempattidur_id'] : null;
            $tipepaket_id = (!empty($dataPost['tipepaket_id']) && ($jenis_pelayanan != 'akomodasi') ) ?  $dataPost['tipepaket_id'] : null;
            $harga_satuan_origin = !empty($dataPost['harga_satuan_origin']) ? $dataPost['harga_satuan_origin'] : null;
            $is_override = ($harga_tariftindakan != $harga_satuan_origin && !empty($harga_tariftindakan) && !empty($harga_satuan_origin)) ? true : false ;
            $persen_penyulit = !empty($dataPost['persen_penyulit']) ? $dataPost['persen_penyulit'] : null;
            $alasan_edit_harga = !empty($dataPost['alasan_edit_harga']) ? $dataPost['alasan_edit_harga'] : '';
            $remarks = !empty($dataPost['remarks']) ? $dataPost['remarks'] : null;
            $tindakanPelayananId = !empty($dataPost['tindakanpelayanan_id']) ? $dataPost['tindakanpelayanan_id'] : null;
            $data = [
               'no_pendaftaran' => $no_pendaftaran,
               'tgl_transaksi' => $tgl_tindakan,
               'instalasi_id' => $instalasi_id,
               'ruangan_id' => $ruangan_id,
               'kelas_pelayanan_id' => $kelas_pelayanan_id,
               'dokter_id' => $dokter_id,
               'perawat_id' => $perawat_id,
               'tipepaket_id' => $tipepaket_id,
               'daftartindakan_id' => $daftartindakan_id,
               'obatalkes_id' => $obatalkes_id,
               'tglpasienpulang' => $tglpasienpulang,
               'tglPendaftaran' => $tglPendaftaran,
               'status_periksa' => $status_periksa,
               'is_cyto' => $val_cyto,
               'is_penyulit' => $val_penyulit,
               'qty' => $qty,
               'penjamin_id' => $penjamin_id,
               'is_ditagihkan' => $is_ditagihkan,
               'isMappingBmhp' => true,
               'is_akomodasi' => $is_akomodasi,
               'kamarruangan_id' => $kamarruangan_id,
               'kamartempattidur_id' => $kamartempattidur_id,
               'is_half_day' => $is_half_day,
               //'satuankecil_id' => $dataPost['satuan_id'],
               'harga_satuan_origin' => $harga_satuan_origin,
               'harga' => $harga_tariftindakan,
               'persencyto_tindakan' => $persencyto_tindakan,
               'persen_penyulit' => $persen_penyulit,
               'is_override' => $is_override,
               'alasan_edit_harga' => $alasan_edit_harga,
               'remarks' => $remarks,
               'tindakanpelayanan_id' => $tindakanPelayananId,
            ];

            $response = $this->_penataJasa->post('paket-tindakan/simpan', [
               'form_params' => $data
            ]);
            $response = json_decode($response->getBody(), true);
            if(!$cacheObat){
               return DocoHelpers::response($response ,200);
            }
         } else {
            return DocoHelpers::response($model->errors,422,'TindakanForm');
         }
      }
      // Simpan Obat List
      if($cacheObat)
      {
         try {
            $model->pendaftaran_id = $pendaftaran_id;
            $response = $this->_penataJasa->post('paket-tindakan/simpan-bmhp-list', [
               'form_params' => [
                  'cacheObat' => $cacheObat,
                  'data' => $model->attributes,
                  'daftartindakan_id_list' => $daftartindakan_id_list,
                  'randString' => $randString

               ]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response ,200);
         } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
         } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
         }
      }
      else if (!$cacheObat && $model->scenario == 'bmhp'){
         $response['response']['text'] = 'Tindakan ataupun obat belum diisi.';
         $response['response']['title'] = 'Proses Gagal!';
         return DocoHelpers::response($response, 422);
      }

   }

   public function actionShowPopupSimpanTindakan()
   {
      $title = Yii::t('fe', 'Simpan Tindakan');
      $request = Yii::$app->request;
      $pendaftaranId = $request->get('pendaftaran_id');
      $randString = DocoHelpers::generateRandomString();
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      Yii::$app->session->setFlash($randString, $yiiRestfulParams);
      return $this->renderAjax('partial/tindakan/_modal', get_defined_vars());
   }

   public function actionDeleteTindakan()
   {
      $request = Yii::$app->request;
      $session = Yii::$app->session;
      $getData = $request->get();
      $pendaftaranID = $request->get('pendaftaran_id');
      $pendaftaran_id = DocoHelpers::decrypt($pendaftaranID);
      $ids = $request->get('id');
      $keys = DocoHelpers::decrypt($ids);
      try {
         $responData = $session->get('tagihanTindakan-pasien-'.$pendaftaranID);
         if ($responData) {
               unset($responData[$keys]);
         }
         $this->setSessionTindakan($responData, $pendaftaranID);
         $response['response']['status'] = 200;
         $response['response']['title'] = 'Proses Berhasil';
         $response['response']['text'] = 'Data Berhasil di hapus sementara!';
         $response['response']['pendaftaran_id'] = $pendaftaranID;
         $response['response']['pendaftaran_ids'] = $pendaftaran_id;
         return DocoHelpers::response($response);
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionDeleteDataBmhp($id, $no_regis)
   {
      $request = Yii::$app->request;
      $session = Yii::$app->session;
      $title = Yii::t('fe', 'Hapus BMHP');
      $modelRemove = new HapusTindakanForm;
      if($request->post()){
         $modelRemove->load($request->post());
         $pendaftaranID = $request->get('pendaftaran_id');
         $pendaftaran_id = DocoHelpers::decrypt($pendaftaranID);
         $no_regis = DocoHelpers::decrypt($no_regis);
         $id = DocoHelpers::decrypt($id);
         if ($modelRemove->validate()) {
            $response = $this->_penataJasa->delete('paket-tindakan/hapus-bmhp',[
               'query' => [
                  'id' => $id
               ],
               'form_params' => [
                  'no_pendaftaran' => $no_regis,
                  'pendaftaran_id' => $pendaftaran_id,
                  'alasan' => $modelRemove->alasan
               ]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response, false, 'HapusTindakanForm');
         } else {
            return DocoHelpers::response($modelRemove->errors,422,'HapusTindakanForm');
         }
      }
      return $this->renderAjax('partial/tindakan/__form_hapus',get_defined_vars());
   }

   public function actionDeleteDataTindakan($id, $no_regis, $no_masukpenunjang = null)
   {
      $request = Yii::$app->request;
      $session = Yii::$app->session;
      $title = Yii::t('fe', 'Hapus Tindakan');
      $modelRemove = new HapusTindakanForm;
      if($request->post()){
         $modelRemove->load($request->post());
         $pendaftaranID = $request->get('pendaftaran_id');
         $pendaftaran_id = DocoHelpers::decrypt($pendaftaranID);
         $no_regis = DocoHelpers::decrypt($no_regis);
         $id = DocoHelpers::decrypt($id);
         if ($modelRemove->validate()) {
               $response = $this->_penataJasa->delete('paket-tindakan/hapus-tindakan',[
                  'query' => [
                     'id' => $id
                  ],
                  'form_params' => [
                     'no_pendaftaran' => $no_regis,
                     'pendaftaran_id' => $pendaftaran_id,
                     'alasan' => $modelRemove->alasan,
                     'no_masukpenunjang' => $no_masukpenunjang
                  ]
               ]);
               $response = json_decode($response->getBody(), true);
               return DocoHelpers::response($response, false, 'HapusTindakanForm');
         } else {
               return DocoHelpers::response($modelRemove->errors,422,'HapusTindakanForm');
         }
      }
      return $this->renderAjax('partial/tindakan/__form_hapus',get_defined_vars());
   }

   public function actionTambahBmhpTambahan()
   {
      $request = Yii::$app->request;
      $session = Yii::$app->session;
      $title = Yii::t('fe', 'Tambah BMHP');
      $tindakanpelayanan_id = $request->get('id');
      $pendaftaran_id = $request->get('pendaftaran_id');
      $tglPendaftaran = date('d-m-Y');
      $model = new TindakanForm;
      $model->scenario = 'bmhp';
      $model->ditagihkan = 0;
      $dataResponse = $this->getDataResponse($tindakanpelayanan_id, $pendaftaran_id);
      $dataTindakan = $dataResponse['list_tindakan'];
      // $dataSatuan = $dataResponse['list_satuan'];
      $dataPendaftaran = $dataResponse['list_pendaftaran'];
      $no_pendaftaran = $dataPendaftaran['no_pendaftaran'];
      $tglPendaftaran = ArrayHelper::getValue($dataPendaftaran, 'tgl_pendaftaran_awal') != null ? ArrayHelper::getValue($dataPendaftaran, 'tgl_pendaftaran_awal') : ArrayHelper::getValue($dataPendaftaran, 'tgl_pendaftaran');
      $tglpasienpulang = $dataPendaftaran['tglpasienpulang'];
      $status_periksa = $dataPendaftaran['status_periksa'];
      $startDate = $tglPendaftaran;
      $endDate = $dataPendaftaran['status_periksa'] == 'Pulang' ? $tglpasienpulang : date('Y-m-d H:i:s');
      return $this->renderAjax('partial/tindakan/__form_tambah_bmhp',get_defined_vars());
   }

   private function getDataResponse($tindakanpelayanan_id, $pendaftaran_id)
   {
      try {
         $request = $this->_penataJasa->get('informasi-pasien/get-data-response',[
               'query' => ['id' => $pendaftaran_id, 'tindakanpelayanan_id' => $tindakanpelayanan_id]
         ]);
         $response = json_decode($request->getBody(), true);
         $attributes = $response['response'];
         return $attributes;
      } catch (\RequestException $e) {
         return [];
      }
   }

   public function actionGetBmhp()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      $get['keyword'] = isset($get['term']) ? $get['term'] : null;
      try {
         $response = $this->_restApotek->get('allow/get-list-stok-apotek', [
               'query' => $get,
         ]);
         $response = json_decode($response->getBody(),true);
         $data = [];
         foreach ($response['response']['data'] as $key => $value) {
               $flag_obat = true;
               if($cacheObat){
                  foreach ($cacheObat as $cache_key => $cache_value){
                     if((int)$cache_value['bmhp'] == $value['obatalkes_id']){
                        $flag_obat = false;
                     }
                  }
               }
               if($flag_obat){
                  $data[] = [
                     'id' => $value['obatalkes_id'],
                     'text' => $value['obatalkes_nama'],
                     'hargaygdipakai' => $value['hargaygdipakai'],
                     'harganetto_ygdipakai' => $value['harganetto'],
                     'qty_tersedia' => $value['qty_tersedia'],
                  ];
               }
         }

         $return = [
               'result' => $data,
               'total_count' => count($data),
               'incomplete_results' => true,
         ];
         return DocoHelpers::response($return);
      } catch (RequestException $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      } catch (\Exception $e) {
         return DocoHelpers::response(['message' => $e->getMessage()]);
      }
   }

   public function actionSaveBmhp()
   {
      $request = Yii::$app->request;
      $model = new TindakanForm;
      $model->scenario = 'bmhp';
      $model->load($request->post());
      $model->jenis_pelayanan = '-';
      $model->harga_satuan = DocoHelpers::convertToAngka($model->harga_satuan);
      $model->total = DocoHelpers::convertToAngka($model->total);

      if ($model->validate()) {
         $response = $this->_penataJasa->post('paket-tindakan/simpan-bmhp', [
               'form_params' => $model->attributes
         ]);
         $body = json_decode($response->getBody(), true);
         return DocoHelpers::response($body ,200);
      }
      else {
         return DocoHelpers::response($model->errors,422,'TindakanForm');
      }
   }

   public function actionGetListObat()
   {
      $request = Yii::$app->request;
      Yii::$app->response->format = Response::FORMAT_JSON;
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $draw = $request->get('draw', 1);
      $data = [];
      $result = [];
      $result['data'] = [];
      $result['draw'] = $draw;
      $result['recordsTotal'] = 0;
      $result['recordsFiltered'] = 0;
      $no_urut = $request->get('start', 1);
      $resetCache = [];
      $user_login = Yii::$app->user->identity->loginpemakai_id;

               // if (!empty($request->get('kontrak_id')) ) {
               //     $kontrak_id = $request->get('kontrak_id');
               //     $cacheGrade = $this->getDataGrades($kontrak_id);
               //     if (!empty($cacheGrade)){
               //         $result = $this->listDataObat($cacheGrade, $no_urut, $draw);
               //     }
               // }else{
      $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      $result = $this->listDataObat($cacheObat, $no_urut, $draw);
               // }



      return $result;

   }

   private function listDataObat($data, $no_urut, $draw){
      $result['recordsTotal'] = 0;
      $result['recordsFiltered'] = 0;
      $result['draw'] = $draw;
      $result['data'] = [];
      if (!empty($data)) {
         foreach ($data as $key => $value) {
               $no_urut++;
               $primaryKey = DocoHelpers::encrypt($key);
               $satuan_bmhp = $value['satuan_bmhp'];
               if ($value['is_ditagihkan'] == 'true'){
                  $bool_tagihan = true;
               } else {
                  $bool_tagihan = false;

                  $value['satuan_bmhp'] = 0;
               };
               $dataCache = [
                  'rowNum' => $no_urut,
                  'nama_bmhp' => $value['nama_bmhp'],
                  'qty_obat' => $value['qty_obat'],
                  'nama_satuan' => $value['nama_satuan'],
                  'is_available' => isset($value['is_available']) ? $value['is_available'] : true,
                  'ruangan_nama' => isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '',
                  'daftartindakan_nama' => isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '',
                  'ruangan_nama' => isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '',
                  'depo_id' => isset($value['depo_id']) ? $value['depo_id'] : null,
                  'satuan_bmhp' => DocoHelpers::rupiahDisplay($satuan_bmhp),
                  'is_ditagihkan' => Html::checkbox('',$bool_tagihan,[
                     'class' => 'styled',
                     'disabled' => 'disabled',
                  ]),
                  'subtotal' => DocoHelpers::rupiahDisplay((int)$value['qty_obat'] * (int)$value['satuan_bmhp']),
                  'aksi' => Html::button(
                     "<i class='fa fa-trash'></i>",[
                           'style' => 'margin-right:5px',
                           'class' => 'btn btn-danger btn-xs delete-cache',
                           'style' => 'margin-right:5px; padding-left:10px !important;',
                           'data-id' => $primaryKey,
                           'data-action' => Url::to([$this->_module.'/delete-cache','id' => $primaryKey]),
                     ]
                  ),


               ];

               $result['data'][] = $dataCache;
         }
         $result['recordsTotal'] = count($data);
         $result['recordsFiltered'] = $no_urut;
         $result['draw'] = $draw;
      }
      return $result;
   }


   public function actionAddCacheObat(){
      $cache = Yii::$app->cache;
      $request = Yii::$app->request;
      $post = $request->post('res');
      $obatalkes_id = $post[0]['bmhp'];
      $ruangan_id = $post[0]['ruangan_id_obat'];
      $instalasi_id = $post[0]['instalasi_id_obat'];
      $qty = $post[0]['qty_obat'];
      $depo_id = !empty($post[0]['depo_id']) ? $post[0]['depo_id'] : null;
      $cacheObatName = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      $cacheTindakan = $cache->get($this->prefixCacheName('tindakan'));
      $countObat;
      $countTindakan;

      if(!empty($depo_id) && $depo_id != $ruangan_id){
         $ruangan_id = $depo_id;
         $instalasi_id = DocoConstants::INSTALASI_FARMASI;
      }

      if(empty($obatalkes_id)) {
         $error = [
            'obatalkes_id' => [
                'BMHP belum dipilih.',
            ]
         ];
         return DocoHelpers::response($error,422,'TindakanForm');
      }

      $response_obat = $this->_penataJasa->post('paket-tindakan/cek-stok-obat', [
         'form_params'=> [
            'obatalkes_id' =>  $obatalkes_id,
            'ruangan_id' => $ruangan_id,
            'instalasi_id' => $instalasi_id
        ],
      ]);

      $stok_obat = json_decode($response_obat->getBody(),true);
      $stokobat_tersedia = (int)$stok_obat['response']['qty_stok'];

      $setItem = [];
      try {

         if(empty($obatalkes_id)) {
               $error = [
                  'obatalkes_id' => [
                      'Jumlah Tindakan/Paket Tidak boleh kosong',
                  ]
               ];
               return DocoHelpers::response($error,422,'TindakanForm');
         }
         elseif(empty($qty)) {
            $error = [
               'qty' => [
                   'Qty harus lebih dari 0',
               ]
            ];
            return DocoHelpers::response($error,422,'TindakanForm');
         }
         elseif(!$stokobat_tersedia){
            $response['response']['text'] = 'Obat Alkes tidak ditemukan.';
            $response['response']['title'] = 'Proses Gagal!';
            return DocoHelpers::response($response, 422);
         }
         elseif($stokobat_tersedia < $qty){
            $response['response']['text'] = 'Silakan cek kembali Stok Obat.';
            $response['response']['title'] = 'Stok obat tidak mencukupi.';
            return DocoHelpers::response($response, 422);
         }
         else {
               // $obatalkes = $this->getObat($obatalkes_id);
               $user_login = Yii::$app->user->identity->loginpemakai_id;
               /*check if create and update method */
               if (!empty($request->post('res')) ) {
                  $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
                  $cacheObat = $this->saveCacheObat($cacheObat, $user_login,$post);
                  Yii::$app->cache->set($this->prefixCacheName('bmhp'),$cacheObat);
               }else{
                  $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
                  $cacheObat = $this->saveCacheObat($cacheObat, $user_login,$post);

                  Yii::$app->cache->set($this->prefixCacheName('bmhp'),$cacheObat,3600);
               }
         
               if($cacheTindakan == false){
                  $countTindakan = 0;
               }else{
                  $countTindakan = count($cacheTindakan);
               }

               if ($cacheObat == true) {
                  $countObat = count($cacheObat);
                  $response['response'] = [
                     'title' => 'Proses Berhasil !',
                     'text' => 'Data berhasil di tambah',
                     'qty'=>$countObat+$countTindakan
                  ];
               }else{
                  $response['response'] = [
                     'title' => 'Proses Gagal !',
                     'text' => 'Data gagal di tambah'
                  ];
               }

               return DocoHelpers::response($response);
         }
      } catch (RequestException $e) {
         throw new \yii\web\HttpException(500, $e->getMessage());
      } catch (\Exception $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
      }
   }

   private function saveCacheObat($cacheObat, $user_login,$post){
      if ($cacheObat == false) {
         Yii::$app->cache->set($this->prefixCacheName('bmhp'),[]);
         $cacheObat = [];
      }
      // if (!isset($cacheAmbulance[$obatalkes_id])) {
      //     $cacheAmbulance[$obatalkes_id] = [];
      // }
      // $data =$post[0];

      // $setItem = [
      //     'bmhp' => $data['bmhp'],
      //     'qty_obat' => $data['qty_obat'],
      //     'satuan_id' => $data['satuan_id'],
      //     'satuan_bmhp' => $data['satuan_bmhp'],
      //     'nama_bmhp' => $data['nama_bmhp'],
      //     'nama_satuan' => $data['nama_satuan'],
      //     'is_ditagihkan' => $data['is_ditagihkan'],
      // ];

      $cacheObat = array_merge($cacheObat, $post);
      return $cacheObat;
   }

   public function actionDeleteCache($id = null)
   {
      $request = Yii::$app->request;
      $id = DocoHelpers::decrypt($id);
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      $cacheTindakan = Yii::$app->cache->get($this->prefixCacheName('tindakan'));
      $countObat;
      $countTindakan;

      if ($cacheObat !== false) {
            if (isset($cacheObat[$id])) {
               unset($cacheObat[$id]);
               Yii::$app->cache->set($this->prefixCacheName('bmhp'),$cacheObat);
            }
            $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
            $countObat = count($cacheObat);
      }

      if($cacheTindakan == false){
         $countTindakan = 0;
      }else{
         $countTindakan = count($cacheTindakan);
      }

      $response['response'] = [
         'title' => 'Proses Berhasil !',
         'text' => 'Data berhasil dihapus',
         'qty'=>$countObat+$countTindakan

      ];
      return DocoHelpers::response($response);
   }

   public function actionEditTanggalPelayanan()
   {
      $title = Yii::t('fe', 'Edit Tanggal Pelayanan');
      $request = Yii::$app->request;
      $tindakanpelayanan_id = $request->get('id', null);
      $tindakanpelayanan_id = DocoHelpers::decrypt($tindakanpelayanan_id);
      $pendaftaran_id = $request->get('pendaftaran_id', null);
      $dataPasien = $this->getPasien($pendaftaran_id);
      $tglPendaftaran = ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran_awal') != null ? ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran_awal') : ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran');
      $tglpasienpulang = $dataPasien['tglpasienpulang'];
      $startDate = $tglPendaftaran;
      $endDate = $dataPasien['status_periksa'] == 'Pulang' ? $tglpasienpulang : date('Y-M-d H:i:s');
      $response = $this->_penataJasa->get('informasi-pasien/get-list-tindakan', [
         'query' => [
               'tindakanpelayanan_id' => $tindakanpelayanan_id,
               'id' => $pendaftaran_id
         ],
      ]);

      $response = json_decode($response->getBody(),true);
      $response = $response['response'];
      $tgl_tindakan = isset($response['tgl_tindakan']) ? $response['tgl_tindakan'] : null;

      $model = new EditTindakanForm;
      $model->attributes = $response;
      $model->tanggal_tindakan = $tanggal_asal =  date('d-M-Y H:i:s', strtotime($tgl_tindakan));

      if($model->load($request->post())) {
         $post = $request->post('EditTindakanForm');
         $data = [
               'pendaftaran_id' => $pendaftaran_id,
               'tindakanpelayanan_id' => $tindakanpelayanan_id,
               'tgl_transaksi' => date('Y-m-d H:i:s', strtotime($post['tanggal_tindakan'])),
               'alasan_edit' => $model->alasan_edit,
               'tanggal_asal' => date('Y-m-d H:i:s', strtotime($model->tanggal_asal)),
         ];
         $response = $this->_penataJasa->post('paket-tindakan/update-tanggal', [
               'form_params' => $data
         ]);
         $response = json_decode($response->getBody(), true);
         return DocoHelpers::response($response ,200);
      }
      return $this->renderAjax('partial/tindakan/__formEditTanggal',get_defined_vars());
   }

   public function actionAddCacheTindakan(){
      $cache = Yii::$app->cache;
      $request = Yii::$app->request;
      $session = Yii::$app->session;
      $pendaftaran_id = $request->get('pendaftaran_id');
      $isShowObatForm = $request->get('isShowObatForm', true);
      $isValidasiTglAkomodasi = $request->get('isValidasiTglAkomodasi', true);
      $pendaftaranID = DocoHelpers::encrypt($pendaftaran_id);
      $model = new TindakanForm;
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheObatName = $this->prefixCacheName('bmhp');
      $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      $cacheTindakan = $cache->get($this->prefixCacheName('tindakan'));
      $countObat;
      $countTindakan;

      $dataPost = $request->post()['TindakanForm'];
      $jenis_pelayanan = $dataPost['jenis_pelayanan'];
      $tglpasienpulang = $dataPost['tglpasienpulang'];
      $model->load($request->post());
      if(!empty($tglpasienpulang)) {
         $model->scenario = $model::PULANG;
      }else {
         if($jenis_pelayanan == $model::TINDAKAN) {
            $model->scenario = $model::TINDAKAN;
         }
         elseif($jenis_pelayanan == $model::PAKET){
            $model->scenario = $model::PAKET;
         } else {
            $model->scenario = $model::AKOMODASI;
         }
      }

      // Validasi untuk scenario sudah stop akomodasi, dan skenario aslinya akomodasi
      if($model->scenario == $model::PULANG && $jenis_pelayanan == $model::AKOMODASI){
         $model_akomodasi = new TindakanForm;
         $model_akomodasi->load($request->post());
         $model_akomodasi->scenario = $model_akomodasi::AKOMODASI;
         if(!$model_akomodasi->validate()){
            $error = $model_akomodasi->errors;
            return DocoHelpers::response($error,422,'TindakanForm');
         }
      }

      if ($model->validate()) {
         try {
            /** GET Tindakan bmhp */
            $restPenatajasa = Yii::$app->docoRest->penatajasa;
            if($model->scenario != $model::AKOMODASI && $isShowObatForm){
               $bmhp = $restPenatajasa->get('paket-tindakan/bmhp-tindakan', [
                  'form_params' => [
                     'daftartindakan_id' => $model->daftartindakan_id,
                     'ruangan_id' => $model->ruangan_id,
                     'penjamin_id' => $model->penjamin_id,
                     'kelaspelayanan_id' => $model->kelas_pelayanan
                     ]
                  ]);
               $body = json_decode($bmhp->getBody(), true);
            }
            $bmhp_tindakan = !empty($body['response']) ? $body['response'] : [];

            /** simpan ke cache */
            $data_bmhp_tindakan = [];
            foreach($bmhp_tindakan as $key => $val){
               $bmhp = !empty($val['obatalkes_id']) ? $val['obatalkes_id'] : null;
               $qty_obat = !empty($val['qty_input']) ? $val['qty_input'] : null;
               $satuan_id = !empty($val['satuanunit_id']) ? $val['satuanunit_id'] : null;
               $nama_satuan = !empty($val['satuanunit_nama']) ? $val['satuanunit_nama'] : null;
               $nama_bmhp = !empty($val['obatalkes_nama']) ? $val['obatalkes_nama'] : null;
               $satuan_bmhp = !empty($val['hargaygdipakai']) ? $val['hargaygdipakai'] : null;
               $harganetto_ygdipakai = !empty($val['harganetto_ygdipakai']) ? $val['harganetto_ygdipakai'] : null;
               $hargaygdipakai = !empty($val['hargaygdipakai']) ? $val['hargaygdipakai'] : null;
               $jml_hargajual = !empty($val['jml_hargajual']) ? $val['jml_hargajual'] : null;
               $is_available = isset($val['is_available']) ? $val['is_available'] : true;
               $daftartindakan_nama = !empty($val['daftartindakan_nama']) ? $val['daftartindakan_nama'] : '';
               $ruangan_nama = !empty($val['ruangan_nama']) ? $val['ruangan_nama'] : '';

               $data_bmhp_tindakan = [
                  'bmhp' => $bmhp,
                  'qty_obat' => $qty_obat,
                  'satuan_id' => $satuan_id,
                  'satuan_bmhp' => $satuan_bmhp,
                  'nama_bmhp' => $nama_bmhp,
                  'nama_satuan' => $nama_satuan,
                  'is_available' => $is_available,
                  'is_ditagihkan' => false,
                  'harganetto_ygdipakai' => $harganetto_ygdipakai,
                  'ruangan_id_obat' => $model->ruangan_id,
                  'instalasi_id_obat' => $model->instalasi_id,
                  'daftartindakan_id' => $model->daftartindakan_id,
                  'daftartindakan_nama' => $daftartindakan_nama,
                  'ruangan_nama' => $ruangan_nama,
               ];
               $cacheObat = $this->saveCache($cacheObatName, $data_bmhp_tindakan);
            }

            $cacheName = $this->prefixCacheName('tindakan');
            $cacheTindakan = $cache->get($cacheName);
            if($jenis_pelayanan == $model::AKOMODASI){
               //Pengecekan akomodasi apakah sudah pernah diinput sebelumnya di cache
               if(!empty($cacheTindakan) && $isValidasiTglAkomodasi){
                  foreach($cacheTindakan as $key=>$value){
                     $tgl_transaksi = date('d M Y',strtotime($dataPost['tanggal_tindakan']));
                     $tgl_transaksi_cache = date('d M Y',strtotime($value['tgl_transaksi']));
                     if($tgl_transaksi == $tgl_transaksi_cache && $dataPost['kelas_pelayanan'] == $value['kelas_pelayanan_id'] && $dataPost['kamarruangan_id'] == $value['kamarruangan_id'] && $jenis_pelayanan == $model::AKOMODASI && $dataPost['daftartindakan_id'] == $value['daftartindakan_id']){
                     $response['response']['title'] = 'Proses Gagal';
                     $response['response']['text'] = 'Akomodasi tanggal '. $tgl_transaksi .', Kelas Pelayanan dan Kamar tersebut sudah ada';
                     return DocoHelpers::response($response, 422);
                     }
                  }
               }
               //Pengecekan akomodasi apakah sudah pernah diinput sebelumnya di tindakan_pelayanan
               $result_akomodasi = $this->_penataJasa->post('paket-tindakan/cek-akomodasi-tindakan', [
                  'form_params' => [
                     'data'=>$dataPost,
                     'pendaftaran_id'=>$pendaftaran_id,
                  ],
               ]);
               $resultAkomodasi = json_decode($result_akomodasi->getBody(), true);
               if(isset($resultAkomodasi['response']) && $resultAkomodasi['response'] != false){
                     $tgl_transaksi = date('d M Y',strtotime($dataPost['tanggal_tindakan']));
                     $response['response']['title'] = 'Proses Gagal';
                     $response['response']['text'] = 'Akomodasi tanggal '. $tgl_transaksi .', Kelas Pelayanan dan Kamar tersebut sudah ada';
                     return DocoHelpers::response($response, 422);
               };

            }
            $qty = $dataPost['qty'];
            if($dataPost['is_half'] == 1 && ($model->scenario == $model::AKOMODASI || $model->scenario == $model::PULANG)){
               $qty = 0.5;
            }
            $is_cyto = $is_penyulit = false;
            $val_cyto = $val_penyulit = false;
            if (isset($dataPost['is_cyto']) && $dataPost['is_cyto'] == 1) {
               $is_cyto = $dataPost['is_cyto'];
               $val_cyto = true;
            }

            if (isset($dataPost['is_penyulit']) && $dataPost['is_penyulit'] == 1) {
               $is_penyulit = $dataPost['is_penyulit'];
               $val_penyulit = true;
            }

            $daftartindakan_id = !empty($dataPost['daftartindakan_id']) ? $dataPost['daftartindakan_id'] : null;
            $instalasi_id = !empty($dataPost['instalasi_id']) ? $dataPost['instalasi_id'] : null;
            $ruangan_id = !empty($dataPost['ruangan_id']) ? $dataPost['ruangan_id'] : null;
            $kelas_pelayanan_id = !empty($dataPost['kelas_pelayanan']) ? $dataPost['kelas_pelayanan'] : null;
            $tglpasienpulang = !empty($dataPost['tglpasienpulang']) ? $dataPost['tglpasienpulang'] : '';
            $tglPendaftaran = !empty($dataPost['tglPendaftaran']) ? $dataPost['tglPendaftaran'] : '';
            $status_periksa = !empty($dataPost['status_periksa']) ? $dataPost['status_periksa'] : null;
            $tindakan_nama_post = !empty($dataPost['tindakan_nama']) ? $dataPost['tindakan_nama'] : "";
            $tindakan_nama = explode('-',$tindakan_nama_post);
            $tindakan_nama = $tindakan_nama_post;

            if(( $jenis_pelayanan == $model::AKOMODASI) &&  !empty($dataPost['tindakan_akomodasi'])){
               $tindakan_nama = $dataPost['tindakan_akomodasi'];
            };
            $is_akomodasi = ( $jenis_pelayanan == $model::AKOMODASI) ? TRUE : FALSE;
            $is_half_day = ($dataPost['is_half']) ? TRUE : FALSE;
            $kamarruangan_id = (!empty($dataPost['kamarruangan_id']) && ($jenis_pelayanan == $model::AKOMODASI)  ) ? $dataPost['kamarruangan_id'] : null;
            $kamartempattidur_id = (!empty($dataPost['kamartempattidur_id']) && ( $jenis_pelayanan == $model::AKOMODASI) ) ? $dataPost['kamartempattidur_id'] : null;
            $harga_tariftindakan = !empty($dataPost['harga_tariftindakan']) ? $dataPost['harga_tariftindakan'] : null;
            $persencyto_tindakan = !empty($dataPost['persencyto_tindakan']) ? $dataPost['persencyto_tindakan'] : null;
            $no_pendaftaran = !empty($dataPost['no_pendaftaran']) ? $dataPost['no_pendaftaran'] : null;
            $penjamin_id = !empty($dataPost['penjamin_id']) ? $dataPost['penjamin_id'] : null;
            // if(!empty($dataPost['penjamin_tindakan_id'])){
            //    $penjamin_id = $dataPost['penjamin_tindakan_id'];
            // }
            $dokter_pj = (!empty($dataPost['dokter_pj']) ) ?  $dataPost['dokter_pj'] : null;
            $perawat_id = !empty($dataPost['perawat'])  ?  $dataPost['perawat'] : null;
            $tipepaket_id = (!empty($dataPost['tipepaket_id']) && ($jenis_pelayanan != $model::AKOMODASI) ) ?  $dataPost['tipepaket_id'] : null;
            $is_ditagihkan = !empty($dataPost['is_ditagihkan']) ? $dataPost['is_ditagihkan'] : null;
            $harga_satuan_origin = !empty($dataPost['harga_satuan_origin']) ? $dataPost['harga_satuan_origin'] : null;
            $is_override = ($harga_tariftindakan != $harga_satuan_origin && !empty($harga_tariftindakan) && !empty($harga_satuan_origin)) ? true : false ;
            $persen_penyulit = !empty($dataPost['persen_penyulit']) ? $dataPost['persen_penyulit'] : null;


            $time = date('H:i:s');
            $obatalkes_id ='';
            $tgl_tindakan = date('Y-m-d H:i:s',strtotime($dataPost['tanggal_tindakan']));
            $hargaTindakanAndQty = (int)$harga_tariftindakan * $qty;
            $hargatindakan = (int)$harga_tariftindakan;
            $cytoTindakan = ($hargatindakan*$persencyto_tindakan)/100;
            $penyulitTindakan = (($hargatindakan + (int)$cytoTindakan)*$persen_penyulit)/100;
            $hargatindakan = ((int)$hargatindakan + (int)$cytoTindakan + $penyulitTindakan)*$qty;

            $data = [
               'no_pendaftaran' => $no_pendaftaran,
               'tgl_transaksi' => $tgl_tindakan,
               'instalasi_id' => $instalasi_id,
               'ruangan_id' => $ruangan_id,
               'kelas_pelayanan_id' => $kelas_pelayanan_id,
               'dokter_id' => $dokter_pj,
               'perawat_id' => $perawat_id,
               'tipepaket_id' => $tipepaket_id,
               'daftartindakan_id' => $daftartindakan_id,
               'obatalkes_id' => $obatalkes_id,
               'tglpasienpulang' => $tglpasienpulang,
               'tglPendaftaran' => $tglPendaftaran,
               'status_periksa' => $status_periksa,
               'is_cyto' => $val_cyto,
               'is_penyulit' => $val_penyulit,
               'qty' => $qty,
               'penjamin_id' => $penjamin_id,
               'is_ditagihkan' => $is_ditagihkan,
               'tindakan_nama' => $tindakan_nama,
               'is_akomodasi' => $is_akomodasi,
               'kamarruangan_id' => $kamarruangan_id,
               'kamartempattidur_id' => $kamartempattidur_id,
               'is_half_day' => $is_half_day,
               'harga_satuan_origin' => $harga_satuan_origin,
               'harga' => $harga_tariftindakan,
               'persencyto_tindakan' => $persencyto_tindakan,
               'persen_penyulit' => $persen_penyulit,
               'is_override' => $is_override,
            ];

            $cacheName = $this->prefixCacheName('tindakan');
            $cacheTindakan = $this->saveCache($cacheName, $data);
            $cacheTindakan = $cache->get($cacheName, $data);

            if($cacheObat == false){
               $countObat = 0;
            }else{
               $countObat = count($cacheObat);
            }

            if ($cacheTindakan) {
               $countTindakan = count($cacheTindakan);
               $response['response'] = [
                  'title' => 'Proses Berhasil !',
                  'text' => 'Data berhasil ditambah.',
                  'qty'=>$countObat+$countTindakan
               ];
            }else{
               $response['response'] = [
                  'title' => 'Proses Gagal !',
                  'text' => 'Data gagal ditambah.'
               ];
            }
            return DocoHelpers::response($response ,200);
         } catch (\RequestException $e) {
            Yii::error(["message" => $e->getMessage()]);
         }
      } else {
         $error = $model->errors;
         if(!empty($error['daftartindakan_id'])){
            $error['tindakan_pelayanan_id'] = $error['daftartindakan_id'];
            unset($error['daftartindakan_id']);
         }
         return DocoHelpers::response($error,422,'TindakanForm');
      }
   }

   public function actionGetCacheTindakan(){
      $request = Yii::$app->request;
      Yii::$app->response->format = Response::FORMAT_JSON;
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $draw = $request->get('draw', 1);
      $data = [];
      $cache = Yii::$app->cache;
      $no_urut = $request->get('start', 1);
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheName = $this->prefixCacheName('tindakan');

      $data = $cache->get($cacheName, $data);
      $result['recordsTotal'] = 0;
      $result['recordsFiltered'] = 0;
      $result['draw'] = $draw;
      $result['data'] = [];
      if (!empty($data)) {
         foreach ($data as $key => $value) {
               $no_urut++;
               $primaryKey = DocoHelpers::encrypt($key);
               $dataCache = $value;
               $dataCache['rowNum'] = $no_urut;
               $is_cyto = isset($value['is_cyto']) && $value['is_cyto'] ? true : false;
               $is_penyulit = isset($value['is_penyulit']) && $value['is_penyulit'] ? true : false;
               $dataCache['is_cyto'] = Html::checkbox('',$is_cyto,[
                  'class' => 'styled',
                  'disabled' => 'disabled',
               ]);
               $dataCache['is_penyulit'] = Html::checkbox('',$is_penyulit,[
                  'class' => 'styled',
                  'disabled' => 'disabled',
               ]);
               $dataCache['aksi'] = Html::button(
                                             "<i class='fa fa-trash'></i>",[
                                                   'style' => 'margin-right:5px',
                                                   'class' => 'btn btn-danger btn-xs delete-cache-tindakan',
                                                   'style' => 'margin-right:5px; padding-left:10px !important;',
                                                   'data-id' => $primaryKey,
                                                   'data-action' => Url::to([$this->_module.'/delete-cache-tindakan','id' => $primaryKey]),
                                             ]);
               $result['data'][] = $dataCache;
         }
         $result['recordsTotal'] = count($data);
         $result['recordsFiltered'] = $no_urut;
         $result['draw'] = $draw;
      }
      return $result;
   }

   private function saveCache($cacheName, $data){
      $cache = Yii::$app->cache;
      $cacheData = $cache->get($cacheName);
      if ($cacheData == false) {
          if (!isset($data[0])) {
              $data = [$data];
          }
          $cache->set($cacheName, $data);
      } else {
         $arr = $cacheData;
         array_push($arr, $data);
         $cache->set($cacheName, $arr);
      }
      return true;
   }

   public function actionDeleteCacheTindakan($id = null)
   {
      $request = Yii::$app->request;
      $id = DocoHelpers::decrypt($id);
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheName = $this->prefixCacheName('tindakan');
      $cacheData = Yii::$app->cache->get($cacheName);
      $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      $cacheTindakan = Yii::$app->cache->get($this->prefixCacheName('tindakan'));
      $countObat;
      $countTindakan;

      if ($cacheData !== false) {
         $countTindakan = count($cacheTindakan)-1;
         if (isset($cacheData[$id])) {
            /** provide delete bmhp yang tindakannya sama */
            $daftartindakan_id = !empty($cacheData[$id]['daftartindakan_id']) ? $cacheData[$id]['daftartindakan_id'] : null;
            $this->deleteTindakanBmhp($daftartindakan_id);
            unset($cacheData[$id]);
            Yii::$app->cache->set($cacheName,$cacheData);
         }
      }

      
      if($cacheObat == false){
         $countObat = 0;
      }else{
         $countObat = count($cacheObat);
      }
      $response['response'] = [
         'title' => 'Proses Berhasil !',
         'text' => 'Data berhasil dihapus',
         'qty'=>$countObat+$countTindakan
      ];

      return DocoHelpers::response($response);
   }

   private function deleteTindakanBmhp($daftartindakan_id){
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      $cacheObat = Yii::$app->cache->get($this->prefixCacheName('bmhp'));
      if(!empty($daftartindakan_id) && !empty($cacheObat)){
         foreach($cacheObat as $key => $value){
            $id = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
            if ($id == $daftartindakan_id) {
               unset($cacheObat[$key]);
            }
         }
         Yii::$app->cache->set($this->prefixCacheName('bmhp'),$cacheObat);
      }
   }

   public function actionGetTindakanList(){
      $cache = Yii::$app->cache;
      $cacheTindakan = $cache->get($this->prefixCacheName('tindakan'));
      $listTindakan = [];
      $result['results'] = [];
      if($cacheTindakan){
         foreach($cacheTindakan as $val){
            $is_akomodasi = isset($val['is_akomodasi']) ? $val['is_akomodasi'] : true;
            if(!$is_akomodasi){
               $result['results'][] = [
                  "id" => !empty($val['daftartindakan_id']) ?$val['daftartindakan_id'] : null,
                  "text" => !empty($val['tindakan_nama']) ? $val['tindakan_nama'] : '',
               ];
            }
         }
      }
      return DocoHelpers::response([
         'result' => $result['results'],
         'total_count' => count($result['results']),
         'incomplete_results' => true,
         'pagination' => ['more' => false]
      ]);

   }

   private function prefixCacheName($type){
      $user_login = Yii::$app->user->identity->loginpemakai_id;
      if($type == 'tindakan'){
         return 'tindakan-penatajasa-'.$user_login;
      }elseif($type == 'bmhp'){
         return 'obat-bmhp-'.$user_login;
      }
   }

   public function actionEditTindakan()
   {
      $title = 'Edit Tindakan & BMHP';
      $model = new TindakanForm;
      $request = Yii::$app->request;
      $tindakanPelayananId = $request->get('id');
      $tindakanPelayananId = DocoHelpers::decrypt($tindakanPelayananId);
      $pendaftaran_id = $request->get('pendaftaran_id');
      $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
      $attributes = $this->getDetailTindakan($tindakanPelayananId, $pendaftaran_id);
      $dataTindakan = ArrayHelper::getValue($attributes, 'data_tindakan', []);
      $konfigSystem = ArrayHelper::getValue($attributes, 'konfigSystem', []);
      $dataPasien = ArrayHelper::getValue($attributes, 'data_pasien', []);
      $jenisPelayanan = ArrayHelper::getValue($attributes, 'jenis_pelayanan', []);
      $kelasPelayanan = ArrayHelper::getValue($attributes, 'kelaspelayanan');
      $instalasi = ArrayHelper::getValue($attributes, 'instalasi');
      $tindakanAkomodasi = ArrayHelper::getValue($attributes, 'tindakanAkomodasi', []);
      $isValidasiTglAkomodasi = ArrayHelper::getValue($attributes, 'penatajasa_validasi_tgl_akomodasi', true);
      $additionalData = ArrayHelper::getValue($dataTindakan, 'additional_data', []);
      $daftarTindakanId = ArrayHelper::getValue($dataTindakan, 'daftartindakan_id');
      $daftarTindakanNama = ArrayHelper::getValue($dataTindakan, 'daftartindakan_nama');
      $daftarTindakanKode = ArrayHelper::getValue($dataTindakan, 'daftartindakan_kode');
      $kelompokTindakanId = ArrayHelper::getValue($dataTindakan, 'kelompoktindakan_id');
      $kelompokTindakanNama = ArrayHelper::getValue($dataTindakan, 'kelompok');
      $valueTindakan = [
         'daftartindakan_id' =>  $daftarTindakanId,
         'daftartindakan_nama' => $daftarTindakanKode.' - '.$daftarTindakanNama.' - '.$kelompokTindakanNama,
      ];

      $remarks = null;
      $hargaSatuanOrigin = 0;
      if(!empty($additionalData)) {
         $additionalData = json_decode($additionalData, true);
         $listKomponen = ArrayHelper::getValue($additionalData, 'list_komponen');
         $listKomponen = ArrayHelper::getValue($listKomponen, 0, []);
         $hargaSatuanOrigin = ArrayHelper::getValue($listKomponen, 'harga_satuan_origin');
         if(isset($additionalData['remarks'])) {
            $remarks = $additionalData['remarks'];
         }
      }

      $model->attributes = $dataTindakan;
      $model->tindakan_pelayanan_id = ArrayHelper::getValue($dataTindakan, 'tindakanpelayanan_id');
      $model->qty = ArrayHelper::getValue($dataTindakan, 'qty_tindakan');
      $model->harga_satuan = ArrayHelper::getValue($dataTindakan, 'tarif_satuan', 0);
      $model->total = $model->qty * $model->harga_satuan;
      $model->remarks = $remarks;
      $model->dokter_pj = ArrayHelper::getValue($dataTindakan, 'dokterpenanggungjawab_id');
      $model->kelas_pelayanan = ArrayHelper::getValue($dataTindakan, 'kelaspelayanan_id');
      $model->tanggal_tindakan = ArrayHelper::getValue($dataTindakan, 'tgl_tindakan');
      $model->harga_satuan_origin = $hargaSatuanOrigin;

      $isEditBilling = !empty($konfigSystem['edit_billing']) ? $konfigSystem['edit_billing'] : false;
      $isShowObatForm = !empty($konfigSystem['is_show_obat_form_penatajasa']) ?  true : false;
      $isHargaReadOnly = ($isEditBilling ? false : true);
      $konfigKelompokTindakan = !empty($konfigSystem['konfig_kelompok_tindakan']) ? $konfigSystem['konfig_kelompok_tindakan'] : [];
      $newKonfigKelompok = [];
      if(!empty($konfigKelompokTindakan)) {
         $konfigKelompokTindakan = json_decode($konfigKelompokTindakan, true);
         $konfigKelompokTindakan = ArrayHelper::getValue($konfigKelompokTindakan, 'konfig_kelompok_tindakan', []);
         foreach ($konfigKelompokTindakan as $key => $value) {
            if(is_array($value)) {
               foreach ($value as $k => $val) {
                  $newKonfigKelompok[] = $k;
               }
            }
         }
      }

      $isPasienRI = ($model->instalasi_id == DocoConstants::INSTALASI_ID_RI) ? '' : 'disabled';
      $instalasi_ri =  DocoConstants::INSTALASI_ID_RI;
      $tglPendaftaran = ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran_awal') != null ? ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran_awal') : ArrayHelper::getValue($dataPasien, 'tgl_pendaftaran');
      $tglpasienpulang = ArrayHelper::getValue($dataPasien, 'tglpasienpulang');
      $status_periksa = ArrayHelper::getValue($dataPasien, 'status_periksa');
      $validasiTgl = $status_periksa == 'Pulang' ? $tglpasienpulang : $tglPendaftaran;
      $startDate = $tglPendaftaran;
      $endDate = $status_periksa == 'Pulang' ? $tglpasienpulang : (!empty($tglpasienpulang) ? $tglpasienpulang : date('Y-m-d H:i:s'));
      $no_pendaftaran = ArrayHelper::getValue($dataPasien, 'no_pendaftaran');
      $penjamin_id = ArrayHelper::getValue($dataPasien, 'penjamin_id');
      $path = Yii::$app->docoPlugin->execute($this, 'form_penatajasa');
      $modul_id = Yii::$app->docoVars->workspace('modul_id');
      return $this->renderAjax($path,get_defined_vars());
   }

   private function getDetailTindakan($tindakanPelayananId, $pendaftaran_id)
   {
      $response = $this->_penataJasa->get('informasi-pasien/get-detail-tindakan',[
         'query' => [
            'tindakanpelayanan_id' => $tindakanPelayananId,
            'pendaftaran_id' => $pendaftaran_id
         ]
      ]);
      return json_decode($response->getBody(),true)['response'];
   }
}
