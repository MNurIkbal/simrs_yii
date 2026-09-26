<?php

namespace app\modules\v1\traits;

use Yii;
use Doco\Services\KasirService;
use Doco\exceptions\ValidationException;
use app\modules\v1\models\InpostOperasi;
use app\modules\v1\models\InpostOperasiDetail;
use app\modules\v1\models\TimOperasi;
use app\modules\v1\models\BmhpOperasi;
use app\modules\v1\models\ObatAlkesPasien;
use SirsCore\features\FeatureTindakanBmhp;
use app\modules\v1\models\TindakanLuarOperasi;
use Doco\models\TarifTotalFn;
use yii\db\Expression;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\Services\PlafonBpjsService;

trait IntegrateKasirTrait {

   /**
   * Function for integrate operation bill into casheer
   * 
   * @param Integer pasienPenunjangId
   * @param Boolean lastProccess
   * @return JSON
   * @author : Rizqi Fitrianto (rizqi@docotel.com)
   * A product of PT. Docotel Teknologi
   * Powered by Sirs
   */
   protected function sentToKasir($pasienPenunjangId, $lastProccess = false)
   {
      if( !$lastProccess ){
         return [
            'penunjang' => null,
            'kamar' => null,
         ];
      }
      $getBillsHeader = InpostOperasi::find()->select([
         'inpostoperasi_t.pasienmasukpenunjang_id',
         'inpostoperasi_t.inpostoperasi_id',
         'pendaftaran_t.no_pendaftaran',
         'pendaftaran_t.pendaftaran_id',
         'ruangan_m.instalasi_id',
         'pasienmasukpenunjang_t.ruangan_id',
         'pasienmasukpenunjang_t.kelaspelayanan_id',
         'pendaftaran_t.penjamin_id'
      ])
      ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id')
      ->rightJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id')
      ->rightJoin('ruangan_m', 'pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id')
      ->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $pasienPenunjangId])
      ->asArray()->one();

      if( empty($getBillsHeader) ){
         return [
            'httpStatusCode' => 404,
            'message' => 'Data Header Operasi Tidak Ditemukan!'
         ];
      }

      $getServices = TimOperasi::find()
      ->select([
         'timoperasi_t.inpostoperasi_id',
         'timoperasi_t.posisi_tim',
         'timoperasi_t.harga',
         'timoperasi_t.persentase',
         'tindakanoperasi_mp.daftartindakan_id',
         'timoperasi_t.pegawai_id'
      ])
      ->rightJoin('tindakanoperasi_mp', 'timoperasi_t.posisi_tim = tindakanoperasi_mp.timoperasi_id')
      ->where(['inpostoperasi_id' => $getBillsHeader['inpostoperasi_id']])->asArray()->all();

      $getBillsDetail = InpostOperasiDetail::find()->where(['inpostoperasi_id' => $getBillsHeader['inpostoperasi_id']])->all();
      $detailIntegration = [];
      $isCyto = false;
      $isPenyulit = false;
      foreach($getBillsDetail as $bill){
         if( $bill->is_cyto ){
            $isCyto = $bill->is_cyto;
         }
         if( $bill->is_penyulit ){
            $isPenyulit = $bill->is_penyulit;
         }
         $detailIntegration[] = [
            'dokter_id' => $bill->dokter_id,
            'perawat_id' => null,
            'tipepaket_id' => null,
            'daftartindakan_id' => $bill->daftartindakan_id,
            'is_cyto' => $bill->is_cyto,
            'is_penyulit' => $bill->is_penyulit,
            'qty' => 1,
            'harga' => $bill->harga
         ];
      }
      foreach($getServices as $service){
         $detailIntegration[] = [
            'dokter_id' => $service['pegawai_id'],
            'perawat_id' => null,
            'tipepaket_id' => null,
            'daftartindakan_id' => $service['daftartindakan_id'],
            'is_cyto' => $isCyto,
            'is_penyulit' => $isPenyulit,
            'qty' => 1,
            'harga' => (int) $service['harga']
         ];
      }
      $tindakanLuarOperasiRecord = TindakanLuarOperasi::find()
         ->select([
            'pasienmasukpenunjang_id',
            'daftartindakan_id',
            'qty'
         ])
         ->andWhere([
            'pasienmasukpenunjang_id' => $pasienPenunjangId
         ])
         ->asArray()
         ->all();
      foreach($tindakanLuarOperasiRecord as $tindakan){
         $detailIntegration[] = [
            'dokter_id' => null,
            'perawat_id' => null,
            'tipepaket_id' => null,
            'daftartindakan_id' => $tindakan['daftartindakan_id'],
            'is_cyto' => false,
            'is_penyulit' => false,
            'qty' => $tindakan['qty'],
            'harga' => (int) $service['harga']
         ];
      }
      $tarifAkomodasiRuangan = (new TarifTotalFn([
         'extParam' => [
            $getBillsHeader['ruangan_id'],
            $getBillsHeader['penjamin_id'],
            $getBillsHeader['kelaspelayanan_id'],
            'kamar'
         ]
      ]))
      ->find()
      ->select(['daftartindakan_id'])
      ->asArray()
      ->one();

      return [
         'penunjang' => (new KasirService)->tagihanPenunjang($getBillsHeader, $detailIntegration),
         'kamar' => !empty($tarifAkomodasiRuangan) ? (new KasirService)->tagihanKamar($getBillsHeader, [$tarifAkomodasiRuangan]) : null
      ];
   }

   /**
    * Function for integrate operation medicine usage into casheer
    * 
    * @param Integer pasienPenunjangId
    * @param Boolean lastProccess
    * @return JSON
    * @author : Rizqi Fitrianto (rizqi@docotel.com)
    * A product of PT. Docotel Teknologi
    * Powered by Sirs
    */
   protected function sentToApotek($pasienPenunjangId, $lastProccess = false, $dokter_id = null, $ruangan_id = null, $pendaftaranId = null)
   {
      if( !$lastProccess ){
         return true;
      }
      $getPenggunaanObat = BmhpOperasi::find()
      ->select([
         'bmhpoperasi_t.pasienmasukpenunjang_id',
         'bmhpoperasi_t.obatalkes_id',
         'bmhpoperasi_t.terpakai as qty',
         'bmhpoperasi_t.daftartindakan_id',
         'bmhpoperasi_t.daftartindakan_id',
         'bmhpoperasi_t.is_ditagihkan',
         'pasienmasukpenunjang_t.ruangan_id',
         'pasienmasukpenunjang_t.pendaftaran_id',
         'pasienmasukpenunjang_t.pasien_id',
         new Expression('COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) as penjamin_id'),
         new Expression('COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) as carabayar_id'),
         'pasienmasukpenunjang_t.kelaspelayanan_id',
         'pasienmasukpenunjang_t.pasienadmisi_id',
      ])
      ->where([
         'bmhpoperasi_t.pasienmasukpenunjang_id' => $pasienPenunjangId,
      ])
      ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = bmhpoperasi_t.pasienmasukpenunjang_id')
      ->rightJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id')
      ->leftJoin('pasienadmisi_t', 'pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')
      ->asArray()->all();
      $listObat = $stokObatAlkes = $dataInsert = $is_ditagihkan = [];
      $stringInObatCondition = '(';
      $params = [
         ':penjamin_id' => null,
         ':ruangan_id' => $ruangan_id,
         ':kelaspelayanan_id' => null,
      ];
      
      if( !empty($getPenggunaanObat) ){
         foreach($getPenggunaanObat as $obat) {
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
            $is_ditagihkan[] = $obat['is_ditagihkan'];
            $stokObatAlkes[] = [
               'obatalkes_id' => $obat['obatalkes_id'],
               'qty_satuanpakai' => $obat['qty'],
               'satuankecil_id' => null,
               'obatalkespasien_id' => null,
               'harganetto' => null,
               'persendiscount' => 0,
               'persenppn' => 0,
               'persenmargin' => 0,
               'jmldiscount' => 0,
               'jmlmargin' => 0,
               'jmlppn' => 0,
            ];
            $dataInsert[] = [
               'ruangan_id' => $ruangan_id,
               'carabayar_id' => $obat['carabayar_id'],
               'pegawai_id' => $dokter_id,
               'daftartindakan_id' => $obat['daftartindakan_id'],
               'pendaftaran_id' => $obat['pendaftaran_id'],
               'obatalkes_id' => $obat['obatalkes_id'],
               'pasien_id' => $obat['pasien_id'],
               'penjamin_id' => $obat['penjamin_id'],
               'kelaspelayanan_id' => $obat['kelaspelayanan_id'],
               'pasienmasukpenunjang_id' => $obat['pasienmasukpenunjang_id'],
               'pasienadmisi_id' => $obat['pasienadmisi_id'],
               'tglpelayanan' => date('Y-m-d H:i:s'),
               'qty_oa' => $obat['qty'],
               'hargasatuan_oa' => 0,
               'harganetto_oa' => 0,
               'hargajual_oa' => 0,
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
         if(! empty($getObatTarif)) {
            foreach($getObatTarif as $itemObat){
               $obatTarif[$itemObat['obatalkes_id']] = $itemObat;
            }
         }
         $totalTarif = 0;
         if(!empty($obatTarif)) {
            foreach($dataInsert as $key => $val) {
               $obatId = isset($val['obatalkes_id']) ? $val['obatalkes_id'] : null;
               if(isset($dataInsert[$key]['obatalkes_id'])) {
                  if($obatId == $obatTarif[$obatId]['obatalkes_id']) {
                     $totalTarif += (float) $obatTarif[$obatId]['jml_hargajual'];
                     $dataInsert[$key]['satuankecil_id'] = (float) $obatTarif[$obatId]['satuankecil_id'];
                     $dataInsert[$key]['hargasatuan_oa'] = (float) ceil($obatTarif[$obatId]['hargaygdipakai']);
                     $dataInsert[$key]['harganetto_oa'] = (float) $obatTarif[$obatId]['harganetto_ygdipakai'];
                     $dataInsert[$key]['hargajual_oa'] = (float) $obatTarif[$obatId]['jml_hargajual'];
                     if(isset($is_ditagihkan[$key]) && $is_ditagihkan[$key] == FALSE) {
                        $dataInsert[$key]['hargasatuan_oa'] = 0;
                     }
                  }
               }
            }
         }
         $transaction = Yii::$app->db->beginTransaction();
        //  if($pendaftaranId) {
        //     $validasiPlafon = new PlafonBpjsService($pendaftaranId, $totalTarif);
        //     $result = $validasiPlafon->validasiPlafon();
        //     if (!$result['isValid']) {
        //         $transaction->rollBack();
        //         throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
        //             'text' => $result['message'] ? $result['message'] : 'Validasi Plafon Gagal',
        //         ]);
        //     }
        //  }
         
         $model = new ObatAlkesPasien;
         $status = ObatAlkesPasien::batchInsert($dataInsert, FALSE);
         $getObatAlkes = ObatAlkesPasien::find()
            ->select(['obatalkespasien_id', 'obatalkes_id', 'ruangan_id'])
            ->where(['pasienmasukpenunjang_id' => $pasienPenunjangId, 'ruangan_id' => $params[':ruangan_id']])
            ->asArray()->all();
            
         if(!$getObatAlkes ) {
            Yii::error([
               'err-message' => 'integrasi dengan stok error, obat alkes tidak ditemukan!'
            ]);
            $transaction->rollBack();
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
               'text' => "Integrasi obat alkes gagal"
            ]);
         }
         $obatAlkesPasienData = [];
         foreach ($getObatAlkes as $key => $item) {
            $obatAlkesPasienData[$item['obatalkes_id']] = $item;
         }
         if(!empty($obatAlkesPasienData) && !empty($obatTarif)) {
            foreach($stokObatAlkes as $index => $item){
               if(isset($stokObatAlkes[$index])) {
                  $stokObatAlkes[$index]['obatalkespasien_id'] = $obatAlkesPasienData[$item['obatalkes_id']]['obatalkespasien_id'];
                  $stokObatAlkes[$index]['obatalkes_id'] = $obatAlkesPasienData[$item['obatalkes_id']]['obatalkes_id'];
                  $stokObatAlkes[$index]['satuankecil_id'] = (int) $obatTarif[$item['obatalkes_id']]['satuankecil_id'];
                  $stokObatAlkes[$index]['harganetto'] = (float) $obatTarif[$item['obatalkes_id']]['harganetto_ygdipakai'];
               }
            }
         }
         $_POST['ruangan_id'] =$params[':ruangan_id'];
         $bmhp = FeatureTindakanBmhp::stokObatAlkes($stokObatAlkes, false);
         $transaction->commit();
         return [
            'status' => true,
            'message' => 'Integrasi obat alkes pasien berhasil!' 
         ];
      }
   }

   protected function integrateTindakanLuarBedah($pasienPenunjangId, $lastProccess = false, $dokter_id = null)
   {
      $getBillsHeader = InpostOperasi::find()->select([
         'inpostoperasi_t.pasienmasukpenunjang_id',
         'inpostoperasi_t.inpostoperasi_id',
         'pendaftaran_t.no_pendaftaran',
         'pendaftaran_t.pendaftaran_id',
         'ruangan_m.instalasi_id',
         'pasienmasukpenunjang_t.ruangan_id',
         'pasienmasukpenunjang_t.kelaspelayanan_id',
         'pendaftaran_t.penjamin_id'
      ])
      ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id')
      ->rightJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id')
      ->rightJoin('ruangan_m', 'pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id')
      ->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $pasienPenunjangId])
      ->asArray()->one();
      
      if(!empty($this->kelas_ditagihkan_id)){
         $getBillsHeader['kelaspelayanan_id'] = $this->kelas_ditagihkan_id;
      }

      if( empty($getBillsHeader) ){
         return [
            'httpStatusCode' => 404,
            'message' => 'Data Header Operasi Tidak Ditemukan!'
         ];
      }
      $tindakanLuarOperasiRecord = TindakanLuarOperasi::find()
         ->selectAttr()
         ->findByPenunjangId($pasienPenunjangId)
         ->getDataArray();

      $detailIntegration = [];
      foreach($tindakanLuarOperasiRecord as $tindakan){
         $detailIntegration[] = [
            'pasienmasukpenunjang_id' => $pasienPenunjangId,
            'dokter_id' => $dokter_id,
            'perawat_id' => null,
            'tipepaket_id' => null,
            'daftartindakan_id' => $tindakan['daftartindakan_id'],
            'is_cyto' => false,
            'is_penyulit' => false,
            'qty' => $tindakan['qty'],
         ];
      }
      return [
         'tindakanluarbedah' => !empty($detailIntegration) ? (new KasirService)->tagihanPenunjang($getBillsHeader, $detailIntegration) : true,
      ];
   }

}
