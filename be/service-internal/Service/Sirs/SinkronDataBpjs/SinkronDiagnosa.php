<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;
use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\AsuransiPenjaminService;
use Integrasi\Service\Sirs\Models\SyKunjungan;
use Integrasi\Service\Sirs\Models\SyKunjunganDetail;
use Integrasi\Service\Sirs\Models\Cron;
use Integrasi\Service\Sirs\Models\SyDiagnosaView;
use Integrasi\Service\Sirs\Models\SyKoreksiDiagnosa;
use Integrasi\Service\Sirs\Models\SyKunjunganPasien;

class SinkronDiagnosa extends \Integrasi\Contracts\DocoImplement
{

   protected $groupBpjs = DocoConstants::GROUP_BPJS;
   protected $instalasiRanap = DocoConstants::INST_ID_RI;
   protected $statusSudahKoreksi = DocoConstants::STATUS_SUDAH_KOREKSI;
   protected $instalasiKodeRi = DocoConstants::SINGKATAN_RI;
   protected $diagnosa = [];
   protected $koreksiDiagnosa = [];

   /**
     * @todo Function get data diagnosa
     * @return array
     * @author Budi <budi@sirs.co.id>
     */
   public function execute()
   {
      date_default_timezone_set('Asia/Jakarta');
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;
      $no_pendaftaran = $this->no_pendaftaran;
      $is_api = filter_var($this->is_api, FILTER_VALIDATE_BOOLEAN);

      $data = $this->insertDiagnosa($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $is_api);

      return json_encode([
         'service' => 'Sirs-SinkronDiagnosa',
         'payload' => $data,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   protected function getDataDiagnosa($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $is_api = false)
   {
      $diagnosaRajal = $diagnosaRanap = $kukunjunganRajalkunjunganRajaljunganRajal = $kunjunganRanap = [];
      
      if(!empty($no_pendaftaran)) {
         $kunjunganRajal = $this->getDataKunjunganRajalPerPasien($no_pendaftaran);
         $kunjunganRanap = $this->getDataKunjunganRanapPerPasien($no_pendaftaran);
         $diagnosaRajal = $this->getDataDiagnosaRajalPerPasien($no_pendaftaran);
         $diagnosaRanap = $this->getDataDiagnosaRanapPerPasien($no_pendaftaran);

      } elseif($is_api) {
         $kunjunganRajal = $this->getDataKunjunganRajal($tgl_pendaftaran);
         $kunjunganRanap = $this->getDataKunjunganRanap($tgl_pendaftaran);;
         $diagnosaRajal = $this->getDataDiagnosaRajalApi($tgl_pendaftaran);
         $diagnosaRanap = $this->getDataDiagnosaRanapApi($tgl_pendaftaran);

      } else {
         $kunjunganRajal = $this->getDataKunjunganRajal($tgl_pendaftaran);
         $kunjunganRanap = $this->getDataKunjunganRanap($tgl_pendaftaran);
         $diagnosaRajal = $this->getDataDiagnosaRajal($tgl_pendaftaran, $jam_pendaftaran);
         $diagnosaRanap = $this->getDataDiagnosaRanap($tgl_pendaftaran, $jam_pendaftaran);
         
      }

      return [
         'kunjunganRajal' => $kunjunganRajal,
         'kunjunganRanap' => $kunjunganRanap,
         'diagnosaRajal' => $diagnosaRajal,
         'diagnosaRanap' => $diagnosaRanap
      ];
   }

   protected function insertDiagnosa($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $is_api)
   {
      $diagnosa = $this->getDataDiagnosa($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $is_api);
      // Rajal
      $kunjunganRajal = ArrayHelper::getValue($diagnosa, 'kunjunganRajal', []);
      $diagnosaRajal = ArrayHelper::getValue($diagnosa, 'diagnosaRajal', []);

      // Ranap
      $kunjunganRanap = ArrayHelper::getValue($diagnosa, 'kunjunganRanap', []);
      $diagnosaRanap = ArrayHelper::getValue($diagnosa, 'diagnosaRanap', []);

      $listKunjunganId = [];

      if (!empty($kunjunganRajal)) {
         foreach ($kunjunganRajal as $key => $value) {
            $kunjunganId = ArrayHelper::getValue($value, 'kunjungan_id');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            if (!empty($noPendaftaran) && !empty($kunjunganId)) {
               $listKunjunganId[$noPendaftaran] = $kunjunganId;
            }
         }
      }

      if (!empty($diagnosaRajal)) {
         foreach ($diagnosaRajal as $key => $value) {
            $noDaftarRajal = ArrayHelper::getValue($value, 'no_pendaftaran');
            if ($noDaftarRajal) {
               $modelDiagnosa = SyKunjunganPasien::find()
                  ->where(['no_pendaftaran' => $noDaftarRajal])
                  ->andWhere(['<>', 'status_kunjungan', DocoConstants::STATUS_PROSES_KLAIM])
                  ->asArray()->one();

               if ($modelDiagnosa != '') {
                  if (!isset($tmpDiagnosa[$noDaftarRajal])) {
                     $tmpDiagnosa[$noDaftarRajal] = $noDaftarRajal;
                  }
                  
                  $kunjunganIdRajal = ArrayHelper::getValue($listKunjunganId, $noDaftarRajal);

                  if(!empty($kunjunganIdRajal)) {

                     $item = [
                        'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRajal),
                        'no_pendaftaran' => $noDaftarRajal,
                        'no_rekammedik' => ArrayHelper::getValue($value, 'no_rm'),
                        'kelompok_diagnosa' => ArrayHelper::getValue($value, 'kelompok_diagnosa'),
                        'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                        'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama')
                     ];

                     if (ArrayHelper::getValue($value, 'diagnosa_kode')) {
                        $diagnosaX = $this->searchDiagnosa($value['diagnosa_kode'], DocoConstants::ICD_10);
                        $tabular = ArrayHelper::getValue($diagnosaX, 'tabular');
                        $diagnosaId = ArrayHelper::getValue($diagnosaX, 'diagnosa_id');
                        if(! empty($diagnosaId)) {
                           $icdX = [
                              'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRajal),
                              'kelompokdiagnosa_id' => self::klasifikasiDiagnosa($tabular),
                              'diagnosa_id' => $diagnosaId,
                              'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                              'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama'),
                              'is_inacbg' => true,
                              'is_icdprimer' => true
                           ];
                           
                           array_push($this->koreksiDiagnosa, $icdX);
                        }
                     }
                     
                     if (ArrayHelper::getValue($value, 'prosedur_kode') || ArrayHelper::getValue($value, 'diagnosa_kode')) {
                           $kodeDiagnosa = isset($value['prosedur_kode']) ? $value['prosedur_kode'] : ArrayHelper::getValue($value, 'diagnosa_kode');
                           $diagnosaIx = $this->searchDiagnosa($kodeDiagnosa, DocoConstants::ICD_9);
                           $tabularIx = ArrayHelper::getValue($diagnosaIx, 'tabular');
                           $diagnosaIdIx = ArrayHelper::getValue($diagnosaIx, 'diagnosa_id');
                           if(! empty($diagnosaIdIx)) {
                              $icdIx = [
                                 'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRajal),
                                 'kelompokdiagnosa_id' => self::klasifikasiDiagnosa($tabularIx),
                                 'diagnosa_id' => $diagnosaIdIx,
                                 'diagnosa_kode' => $kodeDiagnosa,
                                 'diagnosa_nama' => isset($value['prosedur_nama']) ? $value['prosedur_nama'] : ArrayHelper::getValue($value, 'diagnosa_nama'),
                                 'is_inacbg' => true,
                                 'is_icdprimer' => false,
                              ];

                              array_push($this->koreksiDiagnosa, $icdIx);
                           }
                     }

                     $this->diagnosa[] = $item;
                  }
               }
            }
         }
      }

      if (!empty($kunjunganRanap)) {
         foreach ($kunjunganRanap as $key => $value) {
            $kunjunganId = ArrayHelper::getValue($value, 'kunjungan_id');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            if (!empty($noPendaftaran) && !empty($kunjunganId)) {
               $listKunjunganId[$noPendaftaran] = $kunjunganId;
            }
         }
      }

      if (!empty($diagnosaRanap)) {
         foreach ($diagnosaRanap as $key => $value) {
            $noDaftarRanap = ArrayHelper::getValue($value, 'no_pendaftaran');
            if ($noDaftarRanap) {
               $modelDiagnosa = SyKunjunganPasien::find()
                  ->where(['no_pendaftaran' => $noDaftarRanap])
                  ->andWhere(['<>', 'status_kunjungan', DocoConstants::STATUS_PROSES_KLAIM])
                  ->asArray()->one();

               if ($modelDiagnosa != '') {
                  if (!isset($tmpDiagnosa[$noDaftarRanap])) {
                     $tmpDiagnosa[$noDaftarRanap] = $noDaftarRanap;
                  }

                  $kunjnganIdRanap = ArrayHelper::getValue($listKunjunganId, $noDaftarRanap);

                  if(!empty($kunjnganIdRanap)) {
                  
                     $item = [
                        'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRanap),
                        'no_pendaftaran' => $noDaftarRanap,
                        'no_rekammedik' => ArrayHelper::getValue($value, 'no_rm'),
                        'kelompok_diagnosa' => ArrayHelper::getValue($value, 'kelompok_diagnosa'),
                        'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                        'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama'),
                        
                     ];

                     if (ArrayHelper::getValue($value, 'diagnosa_kode')) {
                        $diagnosaX = $this->searchDiagnosa($value['diagnosa_kode'], DocoConstants::ICD_10);
                        $tabular = ArrayHelper::getValue($diagnosaX, 'tabular');
                        $diagnosaId = ArrayHelper::getValue($diagnosaX, 'diagnosa_id');
                        if(!empty($diagnosaId)) {
                           $icdX = [
                              'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRanap),
                              'kelompokdiagnosa_id' => self::klasifikasiDiagnosa($tabular),
                              'diagnosa_id' => $diagnosaId,
                              'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                              'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama'),
                              'is_inacbg' => true,
                              'is_icdprimer' => true,
                           ];
                           
                           array_push($this->koreksiDiagnosa, $icdX);
                        }
                     }

                     if (ArrayHelper::getValue($value, 'prosedur_kode') || ArrayHelper::getValue($value, 'diagnosa_kode')) {
                           $kodeDiagnosa = isset($value['prosedur_kode']) ? $value['prosedur_kode'] : ArrayHelper::getValue($value, 'diagnosa_kode');
                           $diagnosaIx = $this->searchDiagnosa($kodeDiagnosa, DocoConstants::ICD_9);
                           $tabularIx = ArrayHelper::getValue($diagnosaIx, 'tabular');
                           $diagnosaIdIx = ArrayHelper::getValue($diagnosaIx, 'diagnosa_id');
                           if(! empty($diagnosaIdIx)) {
                              $icdIx = [
                                 'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRanap),
                                 'kelompokdiagnosa_id' => self::klasifikasiDiagnosa($tabularIx),
                                 'diagnosa_id' => $diagnosaIdIx,
                                 'diagnosa_kode' => $kodeDiagnosa,
                                 'diagnosa_nama' => isset($value['prosedur_nama']) ? $value['prosedur_nama'] : ArrayHelper::getValue($value, 'diagnosa_nama'),
                                 'is_inacbg' => true,
                                 'is_icdprimer' => false
                              ];

                              array_push($this->koreksiDiagnosa, $icdIx);
                           }
                     }

                     $this->diagnosa[] = $item;
                  }
               }
            }
         }
      }

      if (!empty($tmpDiagnosa)) {
         $tmpDiagnosaDouble = implode("','", array_keys($tmpDiagnosa));
         $sql = "delete from sy_kunjungandetail where no_pendaftaran IN ('$tmpDiagnosaDouble')";
         $hapusDiagnosa = Yii::$app->db->createCommand($sql)->execute();
      }

      if (!empty($this->diagnosa)) {
         $syncDiagnosa = SyKunjunganDetail::batchInsert($this->diagnosa);
      }

      if (!empty($this->koreksiDiagnosa)){
         $syncKoreksiDiagnosa = SyKoreksiDiagnosa::batchInsert($this->koreksiDiagnosa);
      }

      $this->updateCron();

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:' . $this->unique_str,
         'message' => json_encode([
            'status' => 'finish',
            'messageProcess' => 'Berhasil menyiapkan data Diagnosa.',
            'progress' => 80
         ]),
      ]);

      return $this->diagnosa;
   }

   public function searchDiagnosa($kode, $tab)
   {
       $find = SyDiagnosaView::find();
       $find->select(['diagnosa_id', 'tabularlist_versi']);
       $find->where(['diagnosa_kode' => $kode]);
       $find->andWhere(['LIKE', 'tabularlist_versi', $tab]);
       $result = $find->asArray()->one();
       
       $diagnosa_id = ArrayHelper::getValue($result, "diagnosa_id");
       $tabular = ArrayHelper::getValue($result, "tabularlist_versi");
       return [
         'diagnosa_id' => $diagnosa_id,
         'tabular' => $tabular
       ];
   }

   protected function updateCron()
   {
      $modelCronDiagnosa = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_DIAGNOSA])->one();
      
      if (!empty($modelCronDiagnosa)) {
         $modelCronDiagnosa->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCronDiagnosa->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCronDiagnosa->save();
      }
   }

   // Fetching data
   protected function getDataDiagnosaRajal($tgl_pendaftaran, $jam_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT 
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            kelompokdiagnosa_m.kelompokdiagnosa_nama AS kelompok_diagnosa,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama 
         FROM pendaftaran_t
         JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
         JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
         JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
         WHERE to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
         AND to_char(pendaftaran_t.tgl_pendaftaran, 'hh24:mi:ss') > '{$jam_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pendaftaran_t.instalasi_id != '{$this->instalasiRanap}'
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      ")->queryAll();
   }

   protected function getDataDiagnosaRanap($tgl_pendaftaran, $jam_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            kelompokdiagnosa_m.kelompokdiagnosa_nama as kelompok_diagnosa,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            to_char(pasienadmisi_t.tgl_pulang, 'YYYY-MM-DD') AS tgl_pulang,
            to_char(pasienadmisi_t.tgl_pulang, 'hh24-MI-SS') AS jam_pulang
         FROM pasienadmisi_t
         JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id 
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN koreksidiagnosa_t ON pasienadmisi_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
         JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
         JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
         JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
         WHERE to_char(pasienadmisi_t.tgl_pulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
         AND to_char(pasienadmisi_t.tgl_pulang, 'hh24:mi:ss') > '{$jam_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      ")->queryAll();
   }

   protected function getDataKunjunganRajal($tgl_pendaftaran)
   {  
      return Yii::$app->db->createCommand("
         SELECT no_pendaftaran, kunjungan_id
         FROM sy_kunjungan 
         WHERE to_char(tgl_pendaftaran, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
         AND status_kunjungan != '{$this->statusSudahKoreksi}'
         AND instalasi_kode != '{$this->instalasiKodeRi}'
         GROUP BY no_pendaftaran, kunjungan_id
      ")->queryAll();
   }

   protected function getDataKunjunganRanap($tgl_pendaftaran)
   {  
      return Yii::$app->db->createCommand("
         SELECT no_pendaftaran, kunjungan_id
         FROM sy_kunjungan 
         WHERE to_char(tgl_pulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
         AND status_kunjungan != '{$this->statusSudahKoreksi}'
         AND instalasi_kode = '{$this->instalasiKodeRi}'
         GROUP BY no_pendaftaran, kunjungan_id
      ")->queryAll();
   }

   // fetch data perpasien
   protected function getDataDiagnosaRajalPerPasien($no_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT 
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            kelompokdiagnosa_m.kelompokdiagnosa_nama AS kelompok_diagnosa,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama 
         FROM pendaftaran_t
         JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
         JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
         JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
         WHERE pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pendaftaran_t.instalasi_id != '{$this->instalasiRanap}'
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      ")->queryAll();
   }

   protected function getDataDiagnosaRanapPerPasien($no_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            kelompokdiagnosa_m.kelompokdiagnosa_nama as kelompok_diagnosa,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            to_char(pasienadmisi_t.tgl_pulang, 'YYYY-MM-DD') AS tgl_pulang,
            to_char(pasienadmisi_t.tgl_pulang, 'hh24-MI-SS') AS jam_pulang
         FROM pasienadmisi_t
         JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id 
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN koreksidiagnosa_t ON pasienadmisi_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
         JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
         JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
         JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
         WHERE pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      ")->queryAll();
   }

   protected function getDataKunjunganRajalPerPasien($no_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT no_pendaftaran, kunjungan_id
         FROM sy_kunjungan 
         WHERE no_pendaftaran = '{$no_pendaftaran}'
         AND status_kunjungan != '{$this->statusSudahKoreksi}'
         AND instalasi_kode != '{$this->instalasiKodeRi}'
         GROUP BY no_pendaftaran, kunjungan_id
      ")->queryAll();
   }

   protected function getDataKunjunganRanapPerPasien($no_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT no_pendaftaran, kunjungan_id
         FROM sy_kunjungan 
         WHERE no_pendaftaran = '{$no_pendaftaran}'
         AND status_kunjungan != '{$this->statusSudahKoreksi}'
         AND instalasi_kode = '{$this->instalasiKodeRi}'
         GROUP BY no_pendaftaran, kunjungan_id
      ")->queryAll();
   }

   /**
 * Function untuk mendapatkan API diagnosa Rawat Jalan
   */
   protected function getDataDiagnosaRajalApi($tgl_pendaftaran)
   {
      $url = 'sirs/opddiagnosis';
      $options = [
         'tanggal_awal' => $tgl_pendaftaran, 
         'tanggal_akhir' => $tgl_pendaftaran
      ];


      $response = (new AsuransiPenjaminService)->getData($url, $options);
      $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
      return $result;
   }

   /**
    * Function untuk mendapatkan API diagnosa Rawat Inap
    */
   protected function getDataDiagnosaRanapApi($tgl_pendaftaran)
   {
      $url = 'sirs/ipddiagnosis';
      $options = [
         'tanggal_awal' => $tgl_pendaftaran, 
         'tanggal_akhir' => $tgl_pendaftaran
      ];

      $response = (new AsuransiPenjaminService)->getData($url, $options);
      $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
      return $result;
   } 

   protected function klasifikasiDiagnosa($tabular)
   {
      if(strtoupper($tabular) == strtoupper(DocoConstants::ICD_9)) {
         
         return DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN;

      }else{
         
         return DocoConstants::MAP_DIAGNOSA_TAMBAHAN;
      }
   }
}
