<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use GuzzleHttp\Psr7\Response;
use \yii\base\Model;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;

trait DocoSelect2Trait
{
   /**
    *
    * get data infinity scroll
    * 
    * Custom Function Target API and Custom Data
    *
    */

   //  penjamin
   public function actionListPenjamin()
   {
      $api = 'allow/list-penjamin';     // api get data
      $data_id = 'penjamin_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'carabayar_nama',
         'penjamin_nama'
      ];
      $getRest = $this->_restKasir;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }

   // pasien
   public function actionSearchPasien()
   {
      $api = 'allow/get-data-pasien';     // api get data
      $data_id = 'pasien_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'no_rekam_medik',
         'nama_pasien'
      ];
      $getRest = $this->_restAmbulan;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }

   // tindakan
   public function actionSearchTindakan()
   {
      $api = 'allow/get-data-tarif-ambulan';     // api get data
      $data_id = 'daftartindakan_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'daftartindakan_nama'
      ];
      $getRest = $this->_restAmbulan;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }

   // obat
   public function actionSearchObat()
   {
      $api = 'allow/get-data-obat';     // api get data
      $data_id = 'obatalkes_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'obatalkes_nama'
      ];
      $getRest = $this->_restAmbulan;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }

   public function actionGetTindakanInfinity()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $api = 'informasi-pasien/get-data-tindakan'; 
      $data_id = 'tariftindakan_id'; 
      $data_name = [                    
         'daftartindakan_nama'
      ];
      $getRest = $this->_penataJasa;
      return $this->helper->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

   public function actionGetPaketInfinity()
   {
      $request = Yii::$app->request;
      $get = $request->get(); // parsing multiple data custom
      $api = 'informasi-pasien/get-data-paket';     // api get data
      $data_id = 'tipepaket_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'tipepaket_nama'
      ];
      $getRest = $this->_penataJasa;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

   // instalasi ruangan
   public function actionInstalasiRuangan()
   {
      $api = 'allow/get-data-instalasi-ruangan';     // api get data
      $data_id = 'ruangan_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'instalasi_nama',
         'ruangan_nama'
      ];
      $getRest = $this->_restMaster;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest);
   }

   // paket ruangan
   public function actionPaketRuangan()
   {
      $request = Yii::$app->request;
      $get = $request->get(); // parsing multiple data custom
      $api = 'allow/get-data-paket-ruangan';     // api get data
      $data_id = 'tipepaket_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'tipepaket_kode',
         'tipepaket_nama'
      ];
      $getRest = $this->_restMaster;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

   // tindakan ruangan
   public function actionTindakanRuangan()
   {
      $request = Yii::$app->request;
      $get = $request->get(); // parsing multiple data custom
      $api = 'allow/get-data-tindakan-ruangan';     // api get data
      $data_id = 'daftartindakan_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'daftartindakan_kode',
         'daftartindakan_nama'
      ];
      $getRest = $this->_restMaster;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

   //  pegawai
   public function actionListPegawai()
   {
      $api = 'allow/list-pegawai';     // api get data
      $data_id = 'pegawai_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'nomorindukpegawai',
         'nama_pegawai'
      ];
      $getRest = $this->_restMaster;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }

   // list komponen
   public function actionListKomponen()
   {
      $api = 'allow/list-komponen';     // api get data
      $data_id = 'komponentarif_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'komponentarif_nama',
         'komponentarif_kode'
      ];
      $getRest = $this->_restMaster;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }
   //  kwitansi
   public function actionListKwitansi()
   {
      $api = 'allow/list-kwitansi';     // api get data
      $data_id = 'tandabuktibayar_id';         // get id data - fungsi untuk label id
      $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
         'no_pembayaran'
      ];
      $getRest = $this->_restMaster;     // init master api
      return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
   }

   public function actionNewTindakanRuangan()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $api = 'informasi-pasien/new-data-tindakan-ruangan'; 
      $data_id = 'daftartindakan_id'; 
      $data_name = [                    
         'daftartindakan_kode',
         'daftartindakan_nama'
      ];
      $getRest = $this->_penataJasa;
      return $this->helper->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

   public function actionNewPaketRuangan()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $api = 'informasi-pasien/new-data-paket-ruangan'; 
      $data_id = 'tipepaket_id'; 
      $data_name = [                    
         'tipepaket_kode',
         'tipepaket_nama'
      ];
      $getRest = $this->_penataJasa;
      return $this->helper->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

   public function actionTindakanPaketRuangan()
   {
      $request = Yii::$app->request;
      return Yii::$app->docoPlugin->execute($this, 'form_tindakan');
   }
}
