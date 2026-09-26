<?php

namespace app\components\rabbitmq\eklaim;

use app\modules\v1\models\Cron;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyDiagnosaView;
use app\modules\v1\models\SyKoreksiDiagnosa;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganTagihanView;
use Doco\components\DocoConstants;
use Doco\rabbitmq\task\IntegrasiTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class SinkronEklaimTask extends IntegrasiTask
{
   protected $groupBpjs = DocoConstants::GROUP_BPJS;
   protected $instalasiRanap = DocoConstants::INST_ID_RI;
   protected $kunjungan = [];
   protected $kunjunganRanap = [];
   protected $tmpNoDaftar = [];
   protected $hapusNoPendaftaran = [];
   protected $statusLunas = DocoConstants::LUNAS;
   protected $statusSudahKoreksi = DocoConstants::STATUS_SUDAH_KOREKSI;
   protected $statusBelumKoreksi = DocoConstants::STATUS_BELUM_KOREKSI;
   protected $diagnosa = [];
   protected $koreksiDiagnosa = [];
   protected $tmpTagihan = [];
   protected $tagihan = [];

   /**
    * Main execute
    * 
    * @author Maulana Muhammad Rizky
    */
   public function prosesSync()
   {
      $connection = Yii::$app->db;
      $transaction = $connection->beginTransaction();
      try {

         $this->SinkronKunjungan();

         $this->SinkronDiagnosa();

         $this->SinkronTagihan();

         $transaction->commit();

         Yii::error(json_encode([
            'service' => 'Sirs-SinkronEKlaim',
            'payload' => $this,
            'response' => 'Success',
            'timestamp' => date('Y-m-d H:i:s'),
         ]));

         if(! empty($this->progress_bar)) {
            if($this->progress_bar == 100) {
               self::publishMessage($this->progress_bar, false, 'Harap tunggu sedang melakukan Sinkronisasi data '.$this->no_referensi, 'finish');
               self::publishMessage($this->progress_bar, false, 'Sinkronisasi Berhasil !', 'finish');
            } else {
               self::publishMessage($this->progress_bar, false, 'Harap tunggu sedang melakukan Sinkronisasi data '.$this->no_referensi, 'finish');
            }
         }

      } catch (\Throwable $th) {
         $transaction->rollBack();
         Yii::error(json_encode([
            'service' => 'Sirs-SinkronEKlaim',
            'payload' => $this,
            'response' => $th->getMessage(),
            'timestamp' => date('Y-m-d H:i:s'),
         ]));

         if(! empty($this->progress_bar)) {
            self::publishMessage($this->progress_bar, false, 'Gagal Insert untuk no pendaftaran '.$this->no_referensi, 'failed');
         }
      }
   }

   protected function SinkronKunjungan()
   {
      $cache = Yii::$app->cache;
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;
      $no_pendaftaran = $this->no_referensi;
      $instalasi_kode = $this->instalasi_kode;
      $is_api =  filter_var($this->is_api, FILTER_VALIDATE_BOOLEAN);
      $is_trigger =  filter_var($this->is_trigger, FILTER_VALIDATE_BOOLEAN);
      $result = [];
      // Kondisi sinkron semua pasien
      if (!empty($tgl_pendaftaran) && !empty($jam_pendaftaran)) {
         $dataKunjungan = SyKunjunganPasien::find()
            ->select(['no_pendaftaran', 'status_kunjungan'])
            ->where("tgl_pulang::date='$tgl_pendaftaran'")
            ->andWhere(['<>', 'status_kunjungan', DocoConstants::STATUS_SUDAH_KOREKSI])
            ->andWhere(['<>', 'instalasi_kode', DocoConstants::VAR_I_RI])
            ->groupBy(['no_pendaftaran', 'status_kunjungan'])
            ->asArray()->all();

         if (!empty($dataKunjungan)) {
            foreach ($dataKunjungan as $val) {
               $this->tmpNoDaftar[] = ArrayHelper::getValue($val, 'no_pendaftaran');
            }
         }

         $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
      } else {
         // Kondisi sinkron perpasien
         if ($is_trigger == false) {
            $cekStatusKlaim = $this->getDataKunjunganPerPasien(); // Kalo kondisi pasien sudah ada
            $statusKunjungan = ArrayHelper::getValue($cekStatusKlaim, 'status_kunjungan');
            $cekStatusKunjungan = in_array($statusKunjungan, [DocoConstants::STATUS_BELUM_KOREKSI, DocoConstants::STATUS_SUDAH_KOREKSI]);
            if (!empty($cekStatusKlaim) && !$cekStatusKunjungan) {
               if ($statusKunjungan == DocoConstants::STATUS_FINAL_KLAIM) {
                  if(empty($this->progress_bar)) {
                     self::publishMessage(60, false, 'Pasien sudah di final klaim !', 'failed');
                  }
               } else if ($statusKunjungan == DocoConstants::STATUS_PROSES_KLAIM) {

                  if(empty($this->progress_bar)) {
                     self::publishMessage(60, false, 'Pasien sudah di proses klaim.', 'failed');
                  }
               }
            } else {
               $kunjungan = $this->getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
               $kunjunganRajal = ArrayHelper::getValue($kunjungan, 'kunjunganRajal', []);
               $kunjunganRanap = ArrayHelper::getValue($kunjungan, 'kunjunganRanap', []);
               if (empty($kunjunganRajal) && empty($kunjunganRanap)) {
                  if(empty($this->progress_bar)) {
                     self::publishMessage(60, false, 'Data pasien tidak ditemukan !', 'failed');
                  }
               } else {
                  // Kondisi sy_kunjungan apabila data kosong baru insert data baru.
                  if (empty($cekStatusKlaim)) {
                     $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
                  } else {
                     $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, false, true);
                  }
               }
            }
         }

         // Kondisi sinkron ini untuk menghandle data dari trigger 
         if ($is_trigger) {
            $kunjungan = $this->getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
            $kunjunganRajal = ArrayHelper::getValue($kunjungan, 'kunjunganRajal', []);
            $kunjunganRanap = ArrayHelper::getValue($kunjungan, 'kunjunganRanap', []);
            if (empty($kunjunganRajal) && empty($kunjunganRanap)) {
               if(empty($this->progress_bar)) {
                  self::publishMessage(60, false, 'Data pasien tidak ditemukan !', 'failed');
               }
            } else {
               $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
            }
         }
      }

      Yii::error(json_encode([
         'service' => 'Sirs-SyncKunjungan',
         'response' => $result,
         'timestamp' => date('Y-m-d H:i:s'),
      ]));
   }

   protected function getDataKunjunganRajal($tgl_pendaftaran, $jam_pendaftaran)
   {
      return [];
   }

   protected function getDataKunjunganRanap($tgl_pendaftaran, $jam_pendaftaran)
   {
      return [];
   }

   protected function getDataKunjunganRajalPerPasien($no_referensi, $instalasi_kode)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);

      $query = SyKunjungan::find()
         ->where(['not', ['tipe' => DocoConstants::VAR_I_RI]]);

      if ($is_nobayar == true) {
         $query->andWhere(['no_pembayaran' => $no_referensi]);
      } else {
         $query->andWhere(['no_pendaftaran' => $no_referensi]);
      }

      return $query->asArray()->all();
   }

   protected function getDataKunjunganRanapPerPasien($no_referensi, $instalasi_kode)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      $query = SyKunjungan::find()->where([
         'tipe' => DocoConstants::VAR_I_RI
      ]);

      if ($is_nobayar == true) {
         $query->andWhere(['no_pembayaran' => $no_referensi]);
      } else {
         $query->andWhere(['no_pendaftaran' => $no_referensi]);
      }

      return $query->asArray()->all();
   }

   /**
    * Inserting data into sy_kunjungan
    */
   protected function insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api = false, $is_update = false)
   {
      $kunjungan = $this->getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
      $kunjunganRajal = ArrayHelper::getValue($kunjungan, 'kunjunganRajal', []);
      $kunjunganRanap = ArrayHelper::getValue($kunjungan, 'kunjunganRanap', []);
      $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
      $now = date('Y-m-d H:i:s');
      $is_trigger =  filter_var($this->is_trigger, FILTER_VALIDATE_BOOLEAN);

      if (!empty($kunjunganRajal)) {
         foreach ($kunjunganRajal as $key => $value) {
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $tglPulang = ArrayHelper::getValue($value, 'tgl_pulang', NULL);
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $jamPendaftaran = ArrayHelper::getValue($value, 'jam_pendaftaran');
            $jamPulang = ArrayHelper::getValue($value, 'jam_pulang', NULL);
            $instalasiId = ArrayHelper::getValue($value, 'instalasi_id');
            $pasienAdmisi = ArrayHelper::getValue($value, 'pasienadmisi_id');

            $jamDaftarAkhirRnp = $tglPendaftaran . ' ' . $jamPendaftaran;

            if (!empty($tglPulang) && !empty($jamPulang)) {
               $jamPlgAkhirRnp = $tglPulang . ' ' . $jamPulang;
               $datediff = strtotime($tglPulang) - strtotime($tglPendaftaran);
               $lama_rawat = round($datediff / (60 * 60 * 24));
            } else {
               $jamPlgAkhirRnp = NULL;
               $lama_rawat = 1;
            }
            
            $no_sepRajal = ArrayHelper::getValue($value, 'nosep') ?? ArrayHelper::getValue($value, 'no_sep');

            if (!empty($tgl_pendaftaran) && !empty($jam_pendaftaran)) {
               if (in_array($noPendaftaran, $this->tmpNoDaftar)) {
                  $this->hapusNoPendaftaran[] = $noPendaftaran;
               }
            }

            if ($instalasiId == DocoConstants::VAR_IGD_ID && $pasienAdmisi != null) {
            } else {
               $item = [
                  'no_pendaftaran' => ArrayHelper::getValue($value, 'no_pendaftaran'),
                  'no_pembayaran' => ArrayHelper::getValue($value, 'no_pembayaran'),
                  'no_rekammedik' => ArrayHelper::getValue($value, 'no_rekam_medik'),
                  'pasien_id' => ArrayHelper::getValue($value, 'pasien_id'),
                  'nama_pasien' => ArrayHelper::getValue($value, 'nama_pasien'),
                  'jenis_kelamin' => ArrayHelper::getValue($value, 'jeniskelamin'),
                  'tgl_lahir' => ArrayHelper::getValue($value, 'tanggal_lahir'),
                  'umur' => ArrayHelper::getValue($value, 'umur'),
                  'tgl_pendaftaran' => $jamDaftarAkhirRnp,
                  'tgl_pulang' => $jamPlgAkhirRnp,
                  'instalasi_id' => trim(ArrayHelper::getValue($value, 'instalasi_id')),
                  'instalasi_kode' => trim(ArrayHelper::getValue($value, 'instalasi_singkatan')),
                  'instalasi_nama' => trim(ArrayHelper::getValue($value, 'instalasi_nama')),
                  'ruangan_id' => trim(ArrayHelper::getValue($value, 'ruangan_id')),
                  'ruangan_kode' => ArrayHelper::getValue($value, 'ruangan_kode'),
                  'ruangan_nama' => ArrayHelper::getValue($value, 'ruangan_nama'),
                  'carabayar_kode' => ArrayHelper::getValue($value, 'carabayar_kode'),
                  'carabayar_nama' => ArrayHelper::getValue($value, 'carabayar_nama'),
                  'penjamin_kode' => ArrayHelper::getValue($value, 'penjamin_kode'),
                  'penjamin_nama' => ArrayHelper::getValue($value, 'penjamin_nama'),
                  'kelas_kode' => ArrayHelper::getValue($value, 'kelaspelayanan_kode'),
                  'kelas_nama' => ArrayHelper::getValue($value, 'kelaspelayanan_nama'),
                  'dokter_kode' =>  ArrayHelper::getValue($value, 'nomorindukpegawai'),
                  'dokter_nama' =>  ArrayHelper::getValue($value, 'nama_pegawai'),
                  'no_kamar' => ArrayHelper::getValue($value, 'kamarruangan_kode'),
                  'no_tempattidur' => ArrayHelper::getValue($value, 'kamartempattidur_kode'),
                  'carakeluar_kode' => ArrayHelper::getValue($value, 'carakeluar_kode'),
                  'lama_rawat' => (string) $lama_rawat,
                  'status_kunjungan' => DocoConstants::STATUS_BELUM_KOREKSI,
                  'is_active' => true,
                  'created_date' => $now,
                  'created_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                  'is_deleted' => false,
                  'nosep' => $no_sepRajal,
                  'no_asuransi' => ArrayHelper::getValue($value, 'nokartuasuransi'),
                  'hak_kelasbpjs' => ArrayHelper::getValue($value, 'klsrawat'),
                  'additional_data' => ArrayHelper::getValue($value, 'additional_data'),
                  'info_response_bpjs' => ArrayHelper::getValue($value, 'info_response'),
                  'jeniskasuspenyakit_id' => ArrayHelper::getValue($value, 'jeniskasuspenyakit_id'),
                  'jeniskasuspenyakit_nama' => ArrayHelper::getValue($value, 'jeniskasuspenyakit_nama')
               ];
               $this->kunjungan[] = $item;
            }
         }
      }

      if (!empty($kunjunganRanap)) {
         foreach ($kunjunganRanap as $key => $value) {
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $tglPulang = ArrayHelper::getValue($value, 'tgl_pulang', NULL);
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $jamPendaftaran = ArrayHelper::getValue($value, 'jam_pendaftaran');
            $jamPulang = ArrayHelper::getValue($value, 'jam_pulang', NULL);

            $jamDaftarAkhirRnp = $tglPendaftaran . ' ' . str_replace('-', ':', $jamPendaftaran);

            if (!empty($tglPulang) && !empty($jamPulang)) {
               $jamPlgAkhirRnp = $tglPulang . ' ' . str_replace('-', ':', $jamPulang);
               $datediff = strtotime($tglPulang) - strtotime($tglPendaftaran);
               $lama_rawat = round($datediff / (60 * 60 * 24));
            } else {
               $jamPlgAkhirRnp = NULL;
               $lama_rawat = 1;
            }
            
            $no_sepRanap = ArrayHelper::getValue($value, 'nosep') ?? ArrayHelper::getValue($value, 'no_sep');

            if (in_array($noPendaftaran, $this->tmpNoDaftar)) {
               $this->hapusNoPendaftaran[] = $noPendaftaran;
            }

            if ($is_trigger == false && $this->instalasi_kode == DocoConstants::INST_ID_RD || $is_trigger == false && $this->instalasi_kode == DocoConstants::INST_ID_RJ) {

               if(empty($this->progress_bar)) {
                  self::publishMessage(60, false, 'Data pasien tidak ditemukan !', 'failed');
               }

               continue;
            }

            $item = [
               'no_pendaftaran' => ArrayHelper::getValue($value, 'no_pendaftaran'),
               'no_pembayaran' => ArrayHelper::getValue($value, 'no_pembayaran'),
               'no_rekammedik' => ArrayHelper::getValue($value, 'no_rekam_medik'),
               'pasien_id' => ArrayHelper::getValue($value, 'pasien_id'),
               'nama_pasien' => ArrayHelper::getValue($value, 'nama_pasien'),
               'jenis_kelamin' => ArrayHelper::getValue($value, 'jeniskelamin'),
               'tgl_lahir' => ArrayHelper::getValue($value, 'tanggal_lahir'),
               'umur' => ArrayHelper::getValue($value, 'umur'),
               'tgl_pendaftaran' => $jamDaftarAkhirRnp,
               'tgl_pulang' => $jamPlgAkhirRnp,
               'instalasi_id' => trim(ArrayHelper::getValue($value, 'instalasi_id')),
               'instalasi_kode' => trim(ArrayHelper::getValue($value, 'instalasi_singkatan')),
               'instalasi_nama' => trim(ArrayHelper::getValue($value, 'instalasi_nama')),
               'ruangan_id' => trim(ArrayHelper::getValue($value, 'ruangan_id')),
               'ruangan_kode' => ArrayHelper::getValue($value, 'ruangan_kode'),
               'ruangan_nama' => ArrayHelper::getValue($value, 'ruangan_nama'),
               'carabayar_kode' => ArrayHelper::getValue($value, 'carabayar_kode'),
               'carabayar_nama' => ArrayHelper::getValue($value, 'carabayar_nama'),
               'penjamin_kode' => ArrayHelper::getValue($value, 'penjamin_kode'),
               'penjamin_nama' => ArrayHelper::getValue($value, 'penjamin_nama'),
               'kelas_kode' => ArrayHelper::getValue($value, 'kelaspelayanan_kode'),
               'kelas_nama' => ArrayHelper::getValue($value, 'kelaspelayanan_nama'),
               'dokter_kode' =>  ArrayHelper::getValue($value, 'nomorindukpegawai'),
               'dokter_nama' =>  ArrayHelper::getValue($value, 'nama_pegawai'),
               'no_kamar' => ArrayHelper::getValue($value, 'kamarruangan_kode'),
               'no_tempattidur' => ArrayHelper::getValue($value, 'kamartempattidur_kode'),
               'carakeluar_kode' => ArrayHelper::getValue($value, 'carakeluar_kode'),
               'lama_rawat' => (string) $lama_rawat,
               'status_kunjungan' => DocoConstants::STATUS_BELUM_KOREKSI,
               'is_active' => true,
               'created_date' => $now,
               'created_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
               'is_deleted' => false,
               'nosep' => $no_sepRanap,
               'no_asuransi' => ArrayHelper::getValue($value, 'nokartuasuransi'),
               'hak_kelasbpjs' => ArrayHelper::getValue($value, 'klsrawat'),
               'additional_data' => ArrayHelper::getValue($value, 'additional_data'),
               'info_response_bpjs' => ArrayHelper::getValue($value, 'info_response'),
               'jeniskasuspenyakit_id' => ArrayHelper::getValue($value, 'jeniskasuspenyakit_id'),
               'jeniskasuspenyakit_nama' => ArrayHelper::getValue($value, 'jeniskasuspenyakit_nama')
            ];

            $this->kunjungan[] = $item;
         }
      }

      if (!empty($this->hapusNoPendaftaran)) {
         $hapusNoPendaftaran = implode("','", $this->hapusNoPendaftaran);
         $sql = "delete from sy_kunjungan where no_pendaftaran IN ('$hapusNoPendaftaran')";
         $deleteKunjungan = Yii::$app->db->createCommand($sql)->execute();
      }
      if (!empty($this->kunjungan)) {
         if ($is_update) {
            // KALO UDAH ADA UPDATE 1 data masih belum ke update
            if (!empty($no_pendaftaran) && !empty($instalasi_kode)) {

               foreach ($this->kunjungan as $key => $value) {
                  $no_pembayaran = ArrayHelper::getValue($value, 'no_pembayaran');
                  $updateData = array_diff_key($value, array_flip(["status_kunjungan"]));
                  $model = SyKunjunganPasien::find()->where(['no_pembayaran' => $no_pembayaran])->one();
                  if (!empty($model)) {
                     $model->attributes = $updateData;
                     $model->save();
                  } else {
                     $modelParent = new SyKunjunganPasien();
                     $modelParent->attributes = $value;
                     $modelParent->save();
                  }
                  // SyKunjunganPasien::updateAll($updateData, ['no_pembayaran' => $no_pembayaran]);
               }
            }
         } else {
            if (!empty($no_pendaftaran) && !empty($instalasi_kode)) {
               $deleteKunjungan = "delete from sy_kunjungan where no_pendaftaran ='$no_pendaftaran'";
               $hapusKunjungan = Yii::$app->db->createCommand($deleteKunjungan)->execute();
            }

            $syncKunjungan = SyKunjunganPasien::batchInsert($this->kunjungan);
         }
      }

      if(empty($this->progress_bar)) {
         self::publishMessage(70, false, 'Berhasil menyiapkan data Kunjungan !', 'finish');
      }

      return $this->kunjungan;
   }

   protected function getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api = false)
   {
      $kunjunganRajal = $kunjunganRanap = [];
      if (!empty($no_pendaftaran) && !empty($instalasi_kode)) {
         $kunjunganRajal = $this->getDataKunjunganRajalPerPasien($no_pendaftaran, $instalasi_kode);
         $kunjunganRanap = $this->getDataKunjunganRanapPerPasien($no_pendaftaran, $instalasi_kode);
      } else {
         $kunjunganRajal = $this->getDataKunjunganRajal($tgl_pendaftaran, $jam_pendaftaran);
         $kunjunganRanap = $this->getDataKunjunganRanap($tgl_pendaftaran, $jam_pendaftaran);
      }

      return [
         'kunjunganRajal' => $kunjunganRajal,
         'kunjunganRanap' => $kunjunganRanap,
      ];
   }

   protected function getDataKunjunganPerPasien()
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      if ($is_nobayar == true) {
         return Yii::$app->db->createCommand("
             SELECT * FROM sy_kunjungan WHERE no_pembayaran = '{$this->no_referensi}' AND is_deleted = false
          ")->queryOne();
      } else {
         return Yii::$app->db->createCommand("
             SELECT * FROM sy_kunjungan WHERE no_pendaftaran = '{$this->no_referensi}' AND is_deleted = false
          ")->queryOne();
      }
   }

   // -------------------------- SINKRON KUNJUNGAN END -------------------------------

   // -------------------------- SINKRON DIAGNOSA ------------------------------------

   protected function SinkronDiagnosa()
   {
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;
      $no_pendaftaran = $this->no_referensi;
      $is_api = filter_var($this->is_api, FILTER_VALIDATE_BOOLEAN);

      $data = $this->insertDiagnosa($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $is_api);

      Yii::error(json_encode([
         'service' => 'Sirs-SyncDiagnosa',
         'response' => $data,
         'timestamp' => date('Y-m-d H:i:s'),
      ]));
   }

   protected function getDataDiagnosa($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $is_api = false)
   {
      $diagnosaRajal = $diagnosaRanap = $kunjunganRajal = $kunjunganRanap = [];

      if (!empty($no_pendaftaran)) {
         $kunjunganRajal = $this->findKunjunganDiagnosaRajalPasien($no_pendaftaran);
         $kunjunganRanap = $this->findKunjunganDiagnosaRanapPasien($no_pendaftaran);
         $diagnosaRajal = $this->getDataDiagnosaRajalPerPasien($no_pendaftaran);
         $diagnosaRanap = $this->getDataDiagnosaRanapPerPasien($no_pendaftaran);
      } else {
         $kunjunganRajal = $this->findAllDiagnosaDataKunjungan($tgl_pendaftaran);
         $kunjunganRanap = $this->findAllDiagnosaDataKunjungan($tgl_pendaftaran);
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

   /**
    * Inserting data into sy_kunjungandetail and sy_koreksidiagnosa for suggestions
    */
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

               if (!isset($tmpDiagnosa[$noDaftarRajal])) {
                  $tmpDiagnosa[$noDaftarRajal] = $noDaftarRajal;
               }

               $kunjunganIdRajal = ArrayHelper::getValue($listKunjunganId, $noDaftarRajal);

               if (!empty($kunjunganIdRajal)) {

                  $item = [
                     'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRajal),
                     'no_pendaftaran' => $noDaftarRajal,
                     'no_rekammedik' => ArrayHelper::getValue($value, 'no_rekam_medik'),
                     'kelompok_diagnosa' => ArrayHelper::getValue($value, 'kelompok_diagnosa'),
                     'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                     'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama')
                  ];

                  if (ArrayHelper::getValue($value, 'diagnosa_kode')) {
                     $tabular = ArrayHelper::getValue($value, 'tabularlist_versi');
                     $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
                     $kelompokDiagnosa = ArrayHelper::getValue($value, 'kelompok_diagnosa');

                     if ($kelompokDiagnosa == DocoConstants::DIAGNOSA_UTAMA) {
                        $kelompokdiagnosa_id = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
                     } else {
                        $kelompokdiagnosa_id = self::klasifikasiDiagnosa($tabular);
                     }

                     if (!empty($diagnosaId) && $tabular == DocoConstants::ICD_10) {
                        $icdX = [
                           'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRajal),
                           'kelompokdiagnosa_id' => $kelompokdiagnosa_id,
                           'diagnosa_id' => $diagnosaId,
                           'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                           'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama'),
                           'is_inacbg' => false,
                           'is_idrg' => true,
                           'is_icdprimer' => $kelompokdiagnosa_id == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA ? true : false
                        ];

                        array_push($this->koreksiDiagnosa, $icdX);
                     }
                  }

                  if (ArrayHelper::getValue($value, 'prosedur_kode') || ArrayHelper::getValue($value, 'diagnosa_kode')) {
                     $kodeDiagnosa = isset($value['prosedur_kode']) ? $value['prosedur_kode'] : ArrayHelper::getValue($value, 'diagnosa_kode');
                     $tabularIx = ArrayHelper::getValue($value, 'tabularlist_versi');
                     $diagnosaIdIx = ArrayHelper::getValue($value, 'diagnosa_id');
                     if (!empty($diagnosaIdIx) && $tabularIx == DocoConstants::ICD_9) {
                        $icdIx = [
                           'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRajal),
                           'kelompokdiagnosa_id' => self::klasifikasiDiagnosa($tabularIx),
                           'diagnosa_id' => $diagnosaIdIx,
                           'diagnosa_kode' => $kodeDiagnosa,
                           'diagnosa_nama' => isset($value['prosedur_nama']) ? $value['prosedur_nama'] : ArrayHelper::getValue($value, 'diagnosa_nama'),
                           'is_inacbg' => false,
                           'is_idrg' => true,
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
               if (!isset($tmpDiagnosa[$noDaftarRanap])) {
                  $tmpDiagnosa[$noDaftarRanap] = $noDaftarRanap;
               }

               $kunjnganIdRanap = ArrayHelper::getValue($listKunjunganId, $noDaftarRanap);

               if (!empty($kunjnganIdRanap)) {

                  $item = [
                     'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRanap),
                     'no_pendaftaran' => $noDaftarRanap,
                     'no_rekammedik' => ArrayHelper::getValue($value, 'no_rekam_medik'),
                     'kelompok_diagnosa' => ArrayHelper::getValue($value, 'kelompok_diagnosa'),
                     'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                     'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama'),

                  ];

                  if (ArrayHelper::getValue($value, 'diagnosa_kode')) {
                     $tabular = ArrayHelper::getValue($value, 'tabularlist_versi');
                     $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
                     $kelompokDiagnosa = ArrayHelper::getValue($value, 'kelompok_diagnosa');

                     if ($kelompokDiagnosa == DocoConstants::DIAGNOSA_UTAMA) {
                        $kelompokdiagnosa_id = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
                     } else {
                        $kelompokdiagnosa_id = self::klasifikasiDiagnosa($tabular);
                     }

                     if (!empty($diagnosaId) && $tabular == DocoConstants::ICD_10) {
                        $icdX = [
                           'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRanap),
                           'kelompokdiagnosa_id' => $kelompokdiagnosa_id,
                           'diagnosa_id' => $diagnosaId,
                           'diagnosa_kode' => ArrayHelper::getValue($value, 'diagnosa_kode'),
                           'diagnosa_nama' => ArrayHelper::getValue($value, 'diagnosa_nama'),
                           'is_inacbg' => false,
                           'is_idrg' => true,
                           'is_icdprimer' => $kelompokdiagnosa_id == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA ? true : false
                        ];

                        array_push($this->koreksiDiagnosa, $icdX);
                     }
                  }

                  if (ArrayHelper::getValue($value, 'prosedur_kode') || ArrayHelper::getValue($value, 'diagnosa_kode')) {
                     $kodeDiagnosa = isset($value['prosedur_kode']) ? $value['prosedur_kode'] : ArrayHelper::getValue($value, 'diagnosa_kode');
                     $tabularIx = ArrayHelper::getValue($value, 'tabularlist_versi');
                     $diagnosaIdIx = ArrayHelper::getValue($value, 'diagnosa_id');
                     if (!empty($diagnosaIdIx) && $tabularIx == DocoConstants::ICD_9) {
                        $icdIx = [
                           'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noDaftarRanap),
                           'kelompokdiagnosa_id' => self::klasifikasiDiagnosa($tabularIx),
                           'diagnosa_id' => $diagnosaIdIx,
                           'diagnosa_kode' => $kodeDiagnosa,
                           'diagnosa_nama' => isset($value['prosedur_nama']) ? $value['prosedur_nama'] : ArrayHelper::getValue($value, 'diagnosa_nama'),
                           'is_inacbg' => false,
                           'is_idrg' => true,
                        ];

                        array_push($this->koreksiDiagnosa, $icdIx);
                     }
                  }

                  $this->diagnosa[] = $item;
               }
            }
         }
      }

      if (!empty($tmpDiagnosa)) {
         $tmpDiagnosaDouble = implode("','", array_keys($tmpDiagnosa));
         $sql = "delete from sy_kunjungandetail where no_pendaftaran IN ('$tmpDiagnosaDouble')";
         $hapusDiagnosa = Yii::$app->db->createCommand($sql)->execute();
         foreach ($listKunjunganId as $key => $value) {
            SyKoreksiDiagnosa::updateAll(['is_deleted' => true], ['kunjungan_id' => $value]);
         }
      }

      if (!empty($this->diagnosa)) {
         $syncDiagnosa = SyKunjunganDetail::batchInsert($this->diagnosa);
      }

      if (!empty($this->koreksiDiagnosa)) {
         $syncKoreksiDiagnosa = SyKoreksiDiagnosa::batchInsert($this->koreksiDiagnosa);
      }


      if(empty($this->progress_bar)) {
         self::publishMessage(80, false, 'Berhasil menyiapkan data Diagnosa !', 'finish');
      }

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

   /**
    * Find all about kunjungan data with date parameter
    */
   protected function findAllDiagnosaDataKunjungan($tgl_pendaftaran)
   {
      return Yii::$app->db->createCommand("
          SELECT no_pendaftaran, kunjungan_id
          FROM sy_kunjungan 
          WHERE to_char(tgl_pendaftaran, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
          AND status_kunjungan != '{$this->statusSudahKoreksi}'
          AND instalasi_kode != '{$this->instalasiRanap}'
          GROUP BY no_pendaftaran, kunjungan_id
       ")->queryAll();
   }

   // fetch data perpasien
   protected function getDataDiagnosaRajalPerPasien($no_referensi)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      if ($is_nobayar == true) {
         $kunjunganData = SyKunjunganPasien::find()->where(['no_pembayaran' => $no_referensi])->asArray()->one();
         $no_referensi = ArrayHelper::getValue($kunjunganData, 'no_pendaftaran');
      }

      return KoreksiDiagnosaView::find()->where(['no_pendaftaran' => $no_referensi, 'pasienadmisi_id' => NULL])->asArray()->all();
   }

   protected function getDataDiagnosaRanapPerPasien($no_referensi)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      if ($is_nobayar == true) {
         $kunjunganData = SyKunjunganPasien::find()->where(['no_pembayaran' => $no_referensi])->asArray()->one();
         $no_referensi = ArrayHelper::getValue($kunjunganData, 'no_pendaftaran');
      }

      return KoreksiDiagnosaView::find()
         ->where(['no_pendaftaran' => $no_referensi])
         ->andWhere(['not', ['pasienadmisi_id' => NULL]])
         ->asArray()
         ->all();
   }

   /**
    * Find all data about kunjungan only Rawat Jalan
    * 
    * @return Array
    */
   protected function findKunjunganDiagnosaRajalPasien($no_referensi)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      $withbayar = $is_nobayar ? "WHERE no_pembayaran = '{$no_referensi}'" : "WHERE no_pendaftaran = '{$no_referensi}'";
      return Yii::$app->db->createCommand("
          SELECT no_pendaftaran, kunjungan_id
          FROM sy_kunjungan 
          {$withbayar}
          AND status_kunjungan IN ({$this->statusBelumKoreksi})
          AND instalasi_kode != '{$this->instalasiRanap}'
          GROUP BY no_pendaftaran, kunjungan_id
       ")->queryAll();
   }
   /**
    * Find all data about kunjungan only Rawat Inap
    * 
    * @return Array
    */
   protected function findKunjunganDiagnosaRanapPasien($no_referensi)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      $withbayar = $is_nobayar ? "WHERE no_pembayaran = '{$no_referensi}'" : "WHERE no_pendaftaran = '{$no_referensi}'";
      return Yii::$app->db->createCommand("
          SELECT no_pendaftaran, kunjungan_id
          FROM sy_kunjungan 
          {$withbayar}
          AND status_kunjungan IN ({$this->statusBelumKoreksi})
          AND instalasi_kode = '{$this->instalasiRanap}'
          GROUP BY no_pendaftaran, kunjungan_id
       ")->queryAll();
   }

   /**
    * Classificattion kelompokdiagnosa_id
    * 
    * @param string $tabular 
    * 
    * @return int
    */
   protected function klasifikasiDiagnosa($tabular)
   {
      if (strtoupper($tabular) == strtoupper(DocoConstants::ICD_9)) {

         return DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
      } else {

         return DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
      }
   }

   // ---------------------------------- SINKRON DIAGNOSA END ---------------------------------

   // ---------------------------------- SINKRON TAGIHAN --------------------------------------

   public function SinkronTagihan()
   {
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;
      $no_pendaftaran  = $this->no_referensi;
      $instalasi_kode  = $this->instalasi_kode;
      $is_api =  filter_var($this->is_api, FILTER_VALIDATE_BOOLEAN);

      if(empty($this->progress_bar)) {
         self::publishMessage(90);
      }

      $data = $this->insertTagihan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);

      Yii::error(json_encode([
         'service' => 'Sirs-SyncTagihan',
         'response' => $data,
         'timestamp' => date('Y-m-d H:i:s'),
      ]));
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data all pasien 
    */
   protected function getDataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran)
   {
      return SyKunjunganTagihanView::find()->asArray()->all();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data per pasien
    */
   protected function getDataTagihanRajalPasien($no_referensi)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      $query = SyKunjunganTagihanView::find()
         ->where(['not', ['tipe' => DocoConstants::VAR_I_RI]]);

      if ($is_nobayar == true) {
         $query->andWhere(['no_pembayaran' => $no_referensi]);
      } else {
         $query->andWhere(['no_pendaftaran' => $no_referensi]);
      }

      return $query->asArray()->all();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data all pasien 
    */
   protected function getDataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran)
   {
      return SyKunjunganTagihanView::find()->asArray()->all();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data per pasien
    */
   protected function getTagihanRanapPasien($no_referensi)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      $query = SyKunjunganTagihanView::find()
         ->where(['tipe' => DocoConstants::VAR_I_RI]);

      if ($is_nobayar == true) {
         $query->andWhere(['no_pembayaran' => $no_referensi]);
      } else {
         $query->andWhere(['no_pendaftaran' => $no_referensi]);
      }

      return $query->asArray()->all();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Proses untuk melakukan insert data kedalam basis data 
    */
   protected function insertTagihan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api = false)
   {
      $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);
      $dataRow = $this->findReferenceKunjunganAll($tgl_pendaftaran, $no_pendaftaran);
      if (!empty($dataRow)) {

         // Mapping tagihan rawat inap
         $tagihanRanap = $this->mappingTagihanRanap($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);

         // Mapping tagihan rawat jalan
         $tagihanRajal = $this->mappingTagihanRajal($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
         /** Kondisi single sync */
         if (!empty($no_pendaftaran)) {
            if (!empty($this->tagihan)) {
               $this->deletePreviousData($no_pendaftaran);
               $syncTagihan = SyKunjunganTagihan::batchInsert($this->tagihan);
            }
         } else {
            if (!empty($this->tagihan)) {
               SyKunjunganTagihan::batchInsert($this->tagihan);
            }
         }

         if (!empty($no_pendaftaran)) {
            // Handle kondisi notif untuk di status berhasil.
            if (in_array($dataRow[0]['status_kunjungan'], [DocoConstants::STATUS_SUDAH_KOREKSI, DocoConstants::STATUS_BELUM_KOREKSI])) {
               if(empty($this->progress_bar)) {
                  self::publishMessage(100, false);
               }
            } else {
               if(empty($this->progress_bar)) {
                  self::publishMessage(100);
               }
            }
         } else {
            if(empty($this->progress_bar)) {
               self::publishMessage(100, false);
            }
         }
      } else {
         if(empty($this->progress_bar)) {
            self::publishMessage(100);
         }
      }

      return $this->tagihan;
   }

   /**
    * @author Maulana Muhammad Rizky
    * Routing untuk pengambilan data
    */
   protected function dataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api)
   {
      if (!empty($tgl_pendaftaran) && !empty($jam_pendaftaran) && !$is_api) {
         return $this->getDataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran);
      } else {
         return $this->getTagihanRanapPasien($no_pendaftaran);
      }
   }

   protected function dataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api)
   {
      if (!empty($tgl_pendaftaran) && !empty($jam_pendaftaran) && !$is_api) {
         return $this->getDataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran);
      } else {
         return $this->getDataTagihanRajalPasien($no_pendaftaran);
      }
   }

   /**
    * Deleting prev data on sy_kunjungantagihan 
    *
    * @return bool
    */
   private function deletePreviousData($no_pendaftaran)
   {
      $deleteTagihan = "delete from sy_kunjungantagihan where no_pendaftaran in (SELECT no_pendaftaran from sy_kunjungan where no_pendaftaran='$no_pendaftaran')";
      $hapusTagihan = Yii::$app->db->createCommand($deleteTagihan)->execute();
   }

   /**
    * Finding all data kunjungan relate to sy_kunjungantagihan only Rawat Jalan
    * 
    * @return Array
    */
   protected function findKunjunganTagihanRajal($no_referensi, $withStatus = true, $isRanap = false)
   {
      $is_nobayar =  filter_var($this->is_trigger, FILTER_VALIDATE_BOOLEAN);
      $statusSudahKoreksi = DocoConstants::STATUS_SUDAH_KOREKSI;
      $statusBelumKoreksi = DocoConstants::STATUS_BELUM_KOREKSI;
      $instalasiKodeRi = DocoConstants::VAR_I_RI;
      $withCondition = $withStatus ? ' AND status_kunjungan IN (' . $statusSudahKoreksi . ',' . $statusBelumKoreksi . ') ' : '';
      $conditionNonRanap = "AND instalasi_kode != '{$instalasiKodeRi}'";
      $conditionRanap = "AND instalasi_kode = '{$instalasiKodeRi}'";
      $withRanap = $isRanap ? $conditionRanap : $conditionNonRanap;
      $withbayar = $is_nobayar ? "WHERE no_pembayaran = '{$no_referensi}'" : "WHERE no_pendaftaran = '{$no_referensi}'";

      return Yii::$app->db->createCommand("
            SELECT no_pendaftaran, kunjungan_id, no_pembayaran
            FROM sy_kunjungan 
            {$withbayar}
            {$withCondition}
            {$withRanap}
            AND is_deleted = FALSE
            AND is_active = TRUE
            GROUP BY no_pendaftaran, kunjungan_id
        ")->queryAll();
   }

   /**
    * Finding reference relate to sy_kunjungantagihan only Rawat Jalan
    * 
    * @return Array
    */
   protected function findReferenceKunjunganAll($tgl_pendaftaran, $no_referensi)
   {
      $statusSudahKoreksi = DocoConstants::STATUS_SUDAH_KOREKSI;
      $statusBelumKoreksi = DocoConstants::STATUS_BELUM_KOREKSI;
      if (!empty($no_referensi)) {
         $is_nobayar =  filter_var($this->is_nobayar, FILTER_VALIDATE_BOOLEAN);

         // Kondisi handle dengan no pembayaran
         if ($is_nobayar == true) {
            $condition = "WHERE no_pembayaran = '{$no_referensi}'";
         } else {
            $condition = "WHERE no_pendaftaran = '{$no_referensi}'";
         }

         return Yii::$app->db->createCommand("
                SELECT no_pendaftaran, kunjungan_id, status_kunjungan, no_pembayaran
                FROM sy_kunjungan 
                $condition
                AND status_kunjungan IN ('{$statusSudahKoreksi}','{$statusBelumKoreksi}')
                AND is_deleted = FALSE
                AND is_active = TRUE
                GROUP BY no_pendaftaran, kunjungan_id, no_pembayaran
            ")->queryAll();
      } else {
         return Yii::$app->db->createCommand("
                SELECT no_pendaftaran, kunjungan_id
                FROM sy_kunjungan 
                WHERE to_char(tgl_pendaftaran, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
                OR to_char(tgl_pulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
                AND is_deleted = FALSE
                AND is_active = TRUE
                GROUP BY no_pendaftaran, kunjungan_id
            ")->queryAll();
      }
   }

   /**
    * Re - Mapping ulang untuk tagihan Rawat Jalan
    *
    * @author Maulana Muhammad Rizky
    */
   private function mappingTagihanRajal($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api)
   {
      $is_nobayar =  filter_var($this->is_trigger, FILTER_VALIDATE_BOOLEAN);
      $dataTagihanRajal = $this->dataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
      if (!empty($dataTagihanRajal)) {
         foreach ($dataTagihanRajal as $key => $value) {
            $noTagihanRajal = ArrayHelper::getValue($value, 'no_pendaftaran');
            $noPembayaranRajal = ArrayHelper::getValue($value, 'no_pembayaran');

            // Kondisi trigger bayar
            if ($is_nobayar == true) {
               $no_referensi = $noPembayaranRajal;
            } else {
               $no_referensi = $noTagihanRajal;
            }

            if (!empty($no_pendaftaran)) {
               $modelTagihan = $this->findKunjunganTagihanRajal($no_referensi, false);
            } else {
               $modelTagihan = $this->findKunjunganTagihanRajal($no_referensi);
            }

            $inacbgId = ArrayHelper::getValue($value, "inacbg_id");
            if (!empty($modelTagihan) || !empty($inacbgId)) {
               if (!isset($this->tmpTagihan[$noTagihanRajal])) {
                  $this->tmpTagihan[$noTagihanRajal] = $noTagihanRajal;
               }

               $listKunjunganId = [];
               $totalAdjust = 0;
               foreach ($modelTagihan as $key => $data) {
                  $kunjunganId = ArrayHelper::getValue($data, 'kunjungan_id');
                  $noPembayaran = ArrayHelper::getValue($data, 'no_pembayaran');
                  if (!empty($noPembayaran) && !empty($kunjunganId)) {
                     $listKunjunganId[$noPembayaran] = $kunjunganId;
                  }
               }

               if (isset($value['total_adjust'])) {
                  if ($value['total_adjust'] == '') {
                     $totalAdjust = 0;
                  } else {
                     $totalAdjust = $value['total_adjust'];
                  }
               }

               if (isset($value['tarifrs_akt'])) {
                  if ($value['tarifrs_akt'] == '') {
                     $tarifrs_akt = 0;
                  } else {
                     $tarifrs_akt = $value['tarifrs_akt'];
                  }
               }
               $kunjunganId = ArrayHelper::getValue($listKunjunganId, $noPembayaranRajal);

               if($kunjunganId) {
                  $value['qty'] = !empty($value['qty']) ? ceil($value['qty']) : 0;
                  $item = [
                     'kunjungan_id' => $kunjunganId,
                     'no_pendaftaran' => $noTagihanRajal,
                     'no_rekammedik' => ArrayHelper::getValue($value, "no_rekam_medik"),
                     'layanan_kode' => ArrayHelper::getValue($value, "tindakan_obat_kode "),
                     'layanan_nama' => ArrayHelper::getValue($value, "tindakan_obat_nama"),
                     'layanan_qty' => ArrayHelper::getValue($value, "qty"),
                     'layanan_tarif' => ArrayHelper::getValue($value, "layanan_tarif"),
                     'tindakan_kode' => ArrayHelper::getValue($value, "no_tindakan_obat"),
                     'ruangan_kode' => ArrayHelper::getValue($value, "ruangan_singkatan"),
                     'ruangan_nama' => ArrayHelper::getValue($value, "ruangan_nama"),
                     'jasa_rs' => ArrayHelper::getValue($value, "jasa_rs"),
                     'jasa_dokter' => ArrayHelper::getValue($value, "jasa_dokter"),
                     'dokter_kode' => ArrayHelper::getValue($value, "dokter_kode"),
                     'dokter_nama' => ArrayHelper::getValue($value, "nama_pegawai"),
                     'kode_nota' => ArrayHelper::getValue($value, "kode_nota"),
                     'kel_report' => ArrayHelper::getValue($value, "kel_report"),
                     'tarifrs_akt' => $tarifrs_akt,
                     'no_buktitrans' => ArrayHelper::getValue($value, "no_pembayaran"),
                     'kelas_kode' => ArrayHelper::getValue($value, "kelaspelayanan_kode"),
                     'tgl_pendaftaran' => ArrayHelper::getValue($value, "tgl_pendaftaran"),
                     'total_adjust' => $totalAdjust,
                     'kode_adjust' => ArrayHelper::getValue($value, "kode_adjust"),
                     'groupinacbg_id' => (string) ArrayHelper::getValue($value, "groupinacbg_id") ?? ArrayHelper::getValue($value, "inacbg_id"),
                     'groupinacbg_nama' => ArrayHelper::getValue($value, "groupinacbg_nama") ?? ArrayHelper::getValue($value, "inacbg_nama"),
                     'groupinacbg_kode' => ArrayHelper::getValue($value, "groupinacbg_kode") ?? ArrayHelper::getValue($value, "inacbg_kode"),
                     'is_active' => true,
                     'is_deleted' => false
                  ];

                  $this->tagihan[] = $item;
               }
            }
         }
      }
   }

   /**
    * Re - Mapping ulang untuk tagihan Rawat Inap
    *
    * @author Maulana Muhammad Rizky
    */
   private function mappingTagihanRanap($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api)
   {
      $is_nobayar =  filter_var($this->is_trigger, FILTER_VALIDATE_BOOLEAN);
      $dataTagihanRanap = $this->dataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
      if (!empty($dataTagihanRanap)) {
         foreach ($dataTagihanRanap as $key => $value) {
            $noTagihRanap = (isset($value['no_pendaftaran'])) ? $value['no_pendaftaran'] : $value['NO_PENDAFTARAN'];
            $noPembayaranRanap = ArrayHelper::getValue($value, 'no_pembayaran');

            // Kondisi trigger bayar
            if ($is_nobayar == true) {
               $no_referensi = $noPembayaranRanap;
            } else {
               $no_referensi = $noTagihRanap;
            }

            if (!empty($no_pendaftaran)) {
               $modelTagihan = $this->findKunjunganTagihanRajal($no_referensi, false, true);
            } else {
               $modelTagihan = $this->findKunjunganTagihanRajal($no_referensi, true, true);
            }

            $inacbgId = ArrayHelper::getValue($value, "inacbg_id");
            if (!empty($modelTagihan) || !empty($inacbgId)) {
               if (!isset($this->tmpTagihan[$noTagihRanap])) {
                  $this->tmpTagihan[$noTagihRanap] = $noTagihRanap;
               }

               $listKunjunganId = [];
               $totalAdjust = 0;
               foreach ($modelTagihan as $key => $data) {
                  $kunjunganId = ArrayHelper::getValue($data, 'kunjungan_id');
                  $noPembayaran = ArrayHelper::getValue($data, 'no_pembayaran');
                  if (!empty($noPembayaran) && !empty($kunjunganId)) {
                     $listKunjunganId[$noPembayaran] = $kunjunganId;
                  }
               }

               if (isset($value['total_adjust'])) {
                  if ($value['total_adjust'] == '') {
                     $totalAdjust = 0;
                  } else {
                     $totalAdjust = $value['total_adjust'];
                  }
               }

               if (isset($value['tarifrs_akt'])) {
                  if ($value['tarifrs_akt'] == '') {
                     $tarifrs_akt = 0;
                  } else {
                     $tarifrs_akt = $value['tarifrs_akt'];
                  }
               }

               $kunjunganId = ArrayHelper::getValue($listKunjunganId, $noPembayaranRanap);

               if($kunjunganId) {
                  $value['qty'] = !empty($value['qty']) ? ceil($value['qty']) : 0;
                  $item = [
                     'kunjungan_id' => $kunjunganId,
                     'no_pendaftaran' => $noTagihRanap,
                     'no_rekammedik' => ArrayHelper::getValue($value, "no_rekam_medik"),
                     'layanan_kode' => ArrayHelper::getValue($value, "tindakan_obat_kode "),
                     'layanan_nama' => ArrayHelper::getValue($value, "tindakan_obat_nama"),
                     'layanan_qty' => ArrayHelper::getValue($value, "qty"),
                     'layanan_tarif' => ArrayHelper::getValue($value, "layanan_tarif"),
                     'tindakan_kode' => ArrayHelper::getValue($value, "no_tindakan_obat"),
                     'ruangan_kode' => ArrayHelper::getValue($value, "ruangan_singkatan"),
                     'ruangan_nama' => ArrayHelper::getValue($value, "ruangan_nama"),
                     'jasa_rs' => ArrayHelper::getValue($value, "jasa_rs"),
                     'jasa_dokter' => ArrayHelper::getValue($value, "jasa_dokter"),
                     'dokter_kode' => ArrayHelper::getValue($value, "dokter_kode"),
                     'dokter_nama' => ArrayHelper::getValue($value, "nama_pegawai"),
                     'kode_nota' => ArrayHelper::getValue($value, "kode_nota"),
                     'kel_report' => ArrayHelper::getValue($value, "kel_report"),
                     'tarifrs_akt' => $tarifrs_akt,
                     'no_buktitrans' => ArrayHelper::getValue($value, "no_pembayaran"),
                     'kelas_kode' => ArrayHelper::getValue($value, "kelaspelayanan_kode"),
                     'tgl_pendaftaran' => ArrayHelper::getValue($value, "tgl_pendaftaran"),
                     'total_adjust' => $totalAdjust,
                     'kode_adjust' => ArrayHelper::getValue($value, "kode_adjust"),
                     'groupinacbg_id' => (string) ArrayHelper::getValue($value, "groupinacbg_id") ?? ArrayHelper::getValue($value, "inacbg_id"),
                     'groupinacbg_nama' => ArrayHelper::getValue($value, "groupinacbg_nama") ?? ArrayHelper::getValue($value, "inacbg_nama"),
                     'groupinacbg_kode' => ArrayHelper::getValue($value, "groupinacbg_kode") ?? ArrayHelper::getValue($value, "inacbg_kode"),
                     'is_active' => true,
                     'is_deleted' => false
                  ];

                  $this->tagihan[] = $item;
               }
            }
         }
      }
   }

   protected function publishMessage($progressBar, $hide = true, $message = 'Berhasil menyiapkan data Tagihan.', $state = 'finish')
   {
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'sync-eklaim:' . $this->unique_str,
         'message' => json_encode([
            'status' => $state,
            'messageProcess' => $message,
            'progress' => $progressBar,
            'hide' => $hide,
            'no_pendaftaran' => ! empty($this->progress_bar) ? $this->no_referensi : null
         ]),
      ]);
   }
}
