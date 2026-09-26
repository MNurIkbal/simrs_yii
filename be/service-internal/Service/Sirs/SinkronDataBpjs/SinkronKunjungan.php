<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;
use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\AsuransiPenjaminService;
use Integrasi\Service\Sirs\Models\SyKunjunganPasien;
use Integrasi\Service\Sirs\Models\Cron;
use Mpdf\Tag\P;

class SinkronKunjungan extends \Integrasi\Contracts\DocoImplement
{

   protected $groupBpjs = DocoConstants::GROUP_BPJS;
   protected $instalasiRanap = DocoConstants::INST_ID_RI;
   protected $kunjungan = [];
   protected $kunjunganRanap = [];
   protected $tmpNoDaftar = [];
   protected $hapusNoPendaftaran = [];
   protected $statusLunas = DocoConstants::LUNAS;

   /**
     * @todo Function get data kunjungan
     * @return array
     * @author Budi <budi@sirs.co.id>
     */
   public function execute()
   {
      date_default_timezone_set('Asia/Jakarta');
      $cache = Yii::$app->cache;
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;
      $no_pendaftaran = $this->no_pendaftaran;
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
            ->andWhere(['<>', 'instalasi_kode', DocoConstants::SINGKATAN_RI])
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
         if($is_trigger == false) {
            $cekStatusKlaim = $this->getDataKunjunganPerPasien(); // Kalo kondisi pasien sudah ada
            $statusKunjungan = ArrayHelper::getValue($cekStatusKlaim, 'status_kunjungan');
            $cekStatusKunjungan = in_array($statusKunjungan, [DocoConstants::STATUS_BELUM_KOREKSI,DocoConstants::STATUS_SUDAH_KOREKSI]);
            if (!empty($cekStatusKlaim) && ! $cekStatusKunjungan) {
               if ($statusKunjungan == DocoConstants::STATUS_FINAL_KLAIM) {
                  $cache->delete($this->service);
                  Yii::$app->redis->executeCommand('PUBLISH', [
                     'channel' => 'export-excel:'.$this->unique_str,
                     'message' => json_encode([
                        'status' => 'failed', 
                        'messageProcess' => 'Pasien sudah di final klaim.',
                        'progress' => 60
                     ]),
                  ]);
               } else if ($statusKunjungan == DocoConstants::STATUS_PROSES_KLAIM) {
                  $cache->delete($this->service);
                  Yii::$app->redis->executeCommand('PUBLISH', [
                     'channel' => 'export-excel:'.$this->unique_str,
                     'message' => json_encode([
                        'status' => 'failed', 
                        'messageProcess' => 'Pasien sudah di proses klaim.',
                        'progress' => 60
                     ]),
                  ]);
               }
            } else {
               $kunjungan = $this->getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
               $kunjunganRajal = ArrayHelper::getValue($kunjungan, 'kunjunganRajal', []);
               $kunjunganRanap = ArrayHelper::getValue($kunjungan, 'kunjunganRanap', []);
               if(empty($kunjunganRajal) && empty($kunjunganRanap)) {
                  Yii::$app->redis->executeCommand('PUBLISH', [
                     'channel' => 'export-excel:'.$this->unique_str,
                     'message' => json_encode([
                        'status' => 'failed', 
                        'messageProcess' => 'Data pasien tidak ditemukan',
                        'progress' => 60
                     ]),
                  ]);
               }
               else {
                  // Kondisi sy_kunjungan apabila data kosong baru insert data baru.
                  if(empty($cekStatusKlaim)) {
                     $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
                  } else {
                     $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, false, true);
                  }
               }
            }   
         }

         // Kondisi sinkron ini untuk menghandle data dari trigger 
         if($is_trigger) {
            $kunjungan = $this->getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
            $kunjunganRajal = ArrayHelper::getValue($kunjungan, 'kunjunganRajal', []);
            $kunjunganRanap = ArrayHelper::getValue($kunjungan, 'kunjunganRanap', []);
            if(empty($kunjunganRajal) && empty($kunjunganRanap)) {
               Yii::$app->redis->executeCommand('PUBLISH', [
                  'channel' => 'export-excel:'.$this->unique_str,
                  'message' => json_encode([
                     'status' => 'failed', 
                     'messageProcess' => 'Data pasien tidak ditemukan',
                     'progress' => 60
                  ]),
               ]);
            }
            else {
               $result = $this->insertKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode);
            }
         }
      }
      
      return json_encode([
         'service' => 'Sirs-SinkronKunjungan',
         'payload' => $this->attributes,
         'response' => $result,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   protected function getDataKunjunganRajal($tgl_pendaftaran, $jam_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT 
         pendaftaran_t.pendaftaran_id AS pendaftaran_id,
         pendaftaran_t.no_pendaftaran AS no_pendaftaran,
         to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD') AS tgl_pendaftaran,
         to_char(pendaftaran_t.tgl_pendaftaran, 'hh24:mi:ss') AS jam_pendaftaran,
         pasienpulang_t.tglpasienpulang AS tgl_pulang,
         instalasi_m.instalasi_id,
         instalasi_m.instalasi_singkatan AS instalasi_kode,
         instalasi_m.instalasi_nama AS instalasi_nama,
         ruangan_m.ruangan_id,
         ruangan_m.ruangan_singkatan AS ruangan_kode,
         ruangan_m.ruangan_nama AS ruangan_nama,
         pasien_m.no_rekam_medik AS no_rm,
         pendaftaran_t.pasien_id AS pasien_id,
         pasien_m.nama_pasien AS nama_pasien,
         pasien_m.tanggal_lahir AS tgl_lahir,
         pendaftaran_t.umur AS umur,
         pasien_m.jeniskelamin AS jns_kelamin,
         kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
         kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
         carabayar_m.carabayar_kode AS carabayar_kode,
         carabayar_m.carabayar_nama AS carabayar_nama,
         penjamin_m.penjamin_kode AS penjamin_kode,
         penjamin_m.penjamin_nama AS penjamin_nama,
         pegawai_m.nomorindukpegawai AS dokter_kode,
         pegawai_m.nama_pegawai AS dokter_nama,
         carakeluar_m.carakeluar_kode AS carakeluar_kode,
         carakeluar_m.carakeluar_nama AS carakeluar_nama,
         pendaftaran_t.jeniskasuspenyakit_id AS jeniskasuspenyakit_id,
         jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jeniskasuspenyakit_nama,
         1 AS lama_rawat,
         bpjs_t.nosep AS nosep
         FROM pendaftaran_t
         JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
         JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
         JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
         JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
         JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
         JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
         JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
         LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         WHERE 
         to_char(pasienpulang_t.tglpasienpulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
         AND to_char(pasienpulang_t.tglpasienpulang, 'HH-MI-SS') > '{$jam_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pendaftaran_t.instalasi_id != '{$this->instalasiRanap}'
         AND pendaftaran_t.status_bayar = '{$this->statusLunas}'
         AND pasienpulang_t.is_deleted = FALSE
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      "
      )->queryAll();
   }

   protected function getDataKunjunganRanap($tgl_pendaftaran, $jam_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT 
         pendaftaran_t.no_pendaftaran AS no_pendaftaran,
         pendaftaran_t.pendaftaran_id AS pendaftaran_id,
         to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD') AS tgl_pendaftaran,
         to_char(pasienadmisi_t.tgl_pendaftaran, 'HH-MI-SS') AS jam_pendaftaran,
         to_char(pasienadmisi_t.tgl_pulang, 'YYYY-MM-DD') AS tgl_pulang,
         to_char(pasienadmisi_t.tgl_pulang, 'HH-MI-SS') AS jam_pulang,
         'RI'::TEXT AS instalasi_kode,
         'Rawat Inap'::TEXT AS instalasi_nama,
         pendaftaran_t.instalasi_id,
         ruangan_m.ruangan_id,
         ruangan_m.ruangan_singkatan AS ruangan_kode,
         ruangan_m.ruangan_nama AS ruangan_nama,
         kamarruangan_m.kamarruangan_kode AS kamar_kode,
         kamarruangan_m.kamarruangan_nokamar AS kamar_nama,
         kamartempattidur_m.kamartempattidur_kode AS bed_kode,
         kamartempattidur_m.no_tempattidur AS bed_nama,
         pasien_m.no_rekam_medik AS no_rm,
         pendaftaran_t.pasien_id AS pasien_id,
         pasien_m.nama_pasien AS nama_pasien,
         pasien_m.tanggal_lahir AS tgl_lahir,
         pendaftaran_t.umur AS umur,
         pasien_m.jeniskelamin AS jns_kelamin,
         kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
         kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
         carabayar_m.carabayar_kode AS carabayar_kode,
         carabayar_m.carabayar_nama AS carabayar_nama,
         penjamin_m.penjamin_kode AS penjamin_kode,
         penjamin_m.penjamin_nama AS penjamin_nama,
         pegawai_m.nomorindukpegawai AS dokter_kode,
         pegawai_m.nama_pegawai AS dokter_nama,
         carakeluar_m.carakeluar_kode AS carakeluar_kode,
         carakeluar_m.carakeluar_nama AS carakeluar_nama,
         pendaftaran_t.jeniskasuspenyakit_id AS jeniskasuspenyakit_id,
         jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jeniskasuspenyakit_nama,
         bpjs_t.nosep AS nosep
         FROM pasienadmisi_t
         JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
         JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
         JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
         JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
         JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
         JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
         JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
         JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
         LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         WHERE 
         to_char(pasienpulang_t.tglpasienpulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
         AND to_char(pasienpulang_t.tglpasienpulang, 'HH-MI-SS') > '{$jam_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pendaftaran_t.status_bayar = '{$this->statusLunas}'
         AND pasienpulang_t.is_deleted = FALSE
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      "
      )->queryAll();
   }

   protected function getDataKunjunganRajalPerPasien($no_pendaftaran, $instalasi_kode)
   {
      return Yii::$app->db->createCommand("
         SELECT 
         pendaftaran_t.no_pendaftaran AS no_pendaftaran,
         pendaftaran_t.pasienadmisi_id,
         pendaftaran_t.instalasi_id,
         to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD') AS tgl_pendaftaran,
         to_char(pendaftaran_t.tgl_pendaftaran, 'hh24:mi:ss') AS jam_pendaftaran,
         pasienpulang_t.tglpasienpulang AS tgl_pulang,
         instalasi_m.instalasi_singkatan AS instalasi_kode,
         instalasi_m.instalasi_nama AS instalasi_nama,
         ruangan_m.ruangan_id,
         ruangan_m.ruangan_singkatan AS ruangan_kode,
         ruangan_m.ruangan_nama AS ruangan_nama,
         pasien_m.no_rekam_medik AS no_rm,
         pendaftaran_t.pasien_id AS pasien_id,
         pasien_m.nama_pasien AS nama_pasien,
         pasien_m.tanggal_lahir AS tgl_lahir,
         pendaftaran_t.umur AS umur,
         pasien_m.jeniskelamin AS jns_kelamin,
         kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
         kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
         carabayar_m.carabayar_kode AS carabayar_kode,
         carabayar_m.carabayar_nama AS carabayar_nama,
         penjamin_m.penjamin_kode AS penjamin_kode,
         penjamin_m.penjamin_nama AS penjamin_nama,
         pegawai_m.nomorindukpegawai AS dokter_kode,
         pegawai_m.nama_pegawai AS dokter_nama,
         carakeluar_m.carakeluar_kode AS carakeluar_kode,
         carakeluar_m.carakeluar_nama AS carakeluar_nama,
         pendaftaran_t.jeniskasuspenyakit_id AS jeniskasuspenyakit_id,
         jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jeniskasuspenyakit_nama,
         1 AS lama_rawat,
         bpjs_t.nosep AS nosep
         FROM pendaftaran_t
         JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
         JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
         JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
         JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
         JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
         JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
         LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
         LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         WHERE 
         pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
         AND pendaftaran_t.instalasi_id = '{$instalasi_kode}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pasienpulang_t.is_deleted = FALSE
         AND pasienpulang_t.pasienbatalpulang_id IS NULL
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      "
      )->queryAll();
   }

   protected function getDataKunjunganRanapPerPasien($no_pendaftaran, $instalasi_kode)
   {
      return Yii::$app->db->createCommand("
         SELECT 
         pendaftaran_t.no_pendaftaran AS no_pendaftaran,
         pendaftaran_t.instalasi_id,
         to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD') AS tgl_pendaftaran,
         to_char(pasienadmisi_t.tgl_pendaftaran, 'HH-MI-SS') AS jam_pendaftaran,
         to_char(pasienadmisi_t.tgl_pulang, 'YYYY-MM-DD') AS tgl_pulang,
         to_char(pasienadmisi_t.tgl_pulang, 'HH-MI-SS') AS jam_pulang,
         'RI'::TEXT AS instalasi_kode,
         'Rawat Inap'::TEXT AS instalasi_nama,
         pendaftaran_t.instalasi_id,
         ruangan_m.ruangan_id,
         ruangan_m.ruangan_singkatan AS ruangan_kode,
         ruangan_m.ruangan_nama AS ruangan_nama,
         kamarruangan_m.kamarruangan_kode AS kamar_kode,
         kamarruangan_m.kamarruangan_nokamar AS kamar_nama,
         kamartempattidur_m.kamartempattidur_kode AS bed_kode,
         kamartempattidur_m.no_tempattidur AS bed_nama,
         pasien_m.no_rekam_medik AS no_rm,
         pendaftaran_t.pasien_id AS pasien_id,
         pasien_m.nama_pasien AS nama_pasien,
         pasien_m.tanggal_lahir AS tgl_lahir,
         pendaftaran_t.umur AS umur,
         pasien_m.jeniskelamin AS jns_kelamin,
         kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
         kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
         carabayar_m.carabayar_kode AS carabayar_kode,
         carabayar_m.carabayar_nama AS carabayar_nama,
         penjamin_m.penjamin_kode AS penjamin_kode,
         penjamin_m.penjamin_nama AS penjamin_nama,
         pegawai_m.nomorindukpegawai AS dokter_kode,
         pegawai_m.nama_pegawai AS dokter_nama,
         carakeluar_m.carakeluar_kode AS carakeluar_kode,
         carakeluar_m.carakeluar_nama AS carakeluar_nama,
         pendaftaran_t.jeniskasuspenyakit_id AS jeniskasuspenyakit_id,
         jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jeniskasuspenyakit_nama,
         bpjs_t.nosep AS nosep
         FROM pasienadmisi_t
         JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.is_deleted = FALSE
         JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
         JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
         JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
         JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
         JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
         LEFT JOIN bpjs_t ON pasienadmisi_t .bpjs_id = bpjs_t.bpjs_id
         LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         WHERE pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pasienadmisi_t.is_deleted = false 
         AND pasienadmisi_t.is_active = true
         AND pasienpulang_t.pasienbatalpulang_id IS NULL
         AND pasienadmisi_t.pasienbatalperiksa_id is null 
         ORDER BY pendaftaran_t.no_pendaftaran ASC
      "
      )->queryAll();
   }

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
            $tglPulang = ArrayHelper::getValue($value, 'tgl_pulang');
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $jamPendaftaran = ArrayHelper::getValue($value, 'jam_pendaftaran');
            $jamPulang = ArrayHelper::getValue($value, 'jam_pulang');
            $instalasiId = ArrayHelper::getValue($value, 'instalasi_id');
            $pasienAdmisi = ArrayHelper::getValue($value, 'pasienadmisi_id');
            
            $jamDaftarAkhirRnp = $tglPendaftaran . ' ' . $jamPendaftaran;
            $jamPlgAkhirRnp = $tglPulang . ' ' . $jamPulang;
            $datediff = strtotime($tglPulang) - strtotime($tglPendaftaran);
            $lama_rawat = round($datediff / (60 * 60 * 24));
            $no_sepRajal = ArrayHelper::getValue($value, 'nosep') ?? ArrayHelper::getValue($value, 'no_sep');

            if(!empty($tgl_pendaftaran) && !empty($jam_pendaftaran)) {
               if (in_array($noPendaftaran, $this->tmpNoDaftar)) {
                  $this->hapusNoPendaftaran[] = $noPendaftaran;
               }
            }

           if($instalasiId == DocoConstants::VAR_IGD_ID && $pasienAdmisi != null) {

           }else{
               $item = [
                  'no_pendaftaran' => ArrayHelper::getValue($value, 'no_pendaftaran'),
                  'no_rekammedik' => ArrayHelper::getValue($value, 'no_rm'),
                  'pasien_id' => ArrayHelper::getValue($value, 'pasien_id'),
                  'nama_pasien' => ArrayHelper::getValue($value, 'nama_pasien'),
                  'jenis_kelamin' => ArrayHelper::getValue($value, 'jns_kelamin'),
                  'tgl_lahir' => ArrayHelper::getValue($value, 'tgl_lahir'),
                  'umur' => ArrayHelper::getValue($value, 'umur'),
                  'tgl_pendaftaran' => $jamDaftarAkhirRnp,
                  'tgl_pulang' => $jamPlgAkhirRnp,
                  'instalasi_id' => trim(ArrayHelper::getValue($value, 'instalasi_id')),
                  'instalasi_kode' => trim(ArrayHelper::getValue($value, 'instalasi_kode')),
                  'instalasi_nama' => trim(ArrayHelper::getValue($value, 'instalasi_nama')),
                  'ruangan_id' => trim(ArrayHelper::getValue($value, 'ruangan_id')),
                  'ruangan_kode' => ArrayHelper::getValue($value, 'ruangan_kode'),
                  'ruangan_nama' => ArrayHelper::getValue($value, 'ruangan_nama'),
                  'carabayar_kode' => ArrayHelper::getValue($value, 'carabayar_kode'),
                  'carabayar_nama' => ArrayHelper::getValue($value, 'carabayar_nama'),
                  'penjamin_kode' => ArrayHelper::getValue($value, 'penjamin_kode'),
                  'penjamin_nama' => ArrayHelper::getValue($value, 'penjamin_nama'),
                  'kelas_kode' => ArrayHelper::getValue($value, 'kelas_kode'),
                  'kelas_nama' => ArrayHelper::getValue($value, 'kelas_nama'),
                  'dokter_kode' =>  ArrayHelper::getValue($value, 'dokter_kode'),
                  'dokter_nama' =>  ArrayHelper::getValue($value, 'dokter_nama'),
                  'no_kamar' => ArrayHelper::getValue($value, 'kamar_kode'),
                  'no_tempattidur' => ArrayHelper::getValue($value, 'bed_kode'),
                  'carakeluar_kode' => ArrayHelper::getValue($value, 'carakeluar_kode'),
                  'lama_rawat' => (string) $lama_rawat,
                  'status_kunjungan' => DocoConstants::STATUS_BELUM_KOREKSI,
                  'is_active' => true,
                  'created_date' => $now,
                  'created_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                  'is_deleted' => false,
                  'nosep' => $no_sepRajal,
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
            $tglPulang = ArrayHelper::getValue($value, 'tgl_pulang');
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $jamPendaftaran = ArrayHelper::getValue($value, 'jam_pendaftaran');
            $jamPulang = ArrayHelper::getValue($value, 'jam_pulang');
            
            $jamDaftarAkhirRnp = $tglPendaftaran . ' ' . str_replace('-', ':', $jamPendaftaran);
            $jamPlgAkhirRnp = $tglPulang . ' ' . str_replace('-', ':', $jamPulang);
            $datediff = strtotime($tglPulang) - strtotime($tglPendaftaran);
            $lama_rawat = round($datediff / (60 * 60 * 24));
            $no_sepRanap = ArrayHelper::getValue($value, 'nosep') ?? ArrayHelper::getValue($value, 'no_sep');

            if (in_array($noPendaftaran, $this->tmpNoDaftar)) {
               $this->hapusNoPendaftaran[] = $noPendaftaran;
            }

            if($is_trigger == false && $this->instalasi_kode == DocoConstants::INST_ID_RD || $is_trigger == false && $this->instalasi_kode == DocoConstants::INST_ID_RJ) {
               Yii::$app->redis->executeCommand('PUBLISH', [
                  'channel' => 'export-excel:'.$this->unique_str,
                  'message' => json_encode([
                     'status' => 'failed', 
                     'messageProcess' => 'Data pasien tidak ditemukan',
                     'progress' => 60
                  ]),
               ]);

               continue;
            }

            $item = [
               'no_pendaftaran' => ArrayHelper::getValue($value, 'no_pendaftaran'),
               'no_rekammedik' => ArrayHelper::getValue($value, 'no_rm'),
               'pasien_id' => ArrayHelper::getValue($value, 'pasien_id'),
               'nama_pasien' => ArrayHelper::getValue($value, 'nama_pasien'),
               'jenis_kelamin' => ArrayHelper::getValue($value, 'jns_kelamin'),
               'tgl_lahir' => ArrayHelper::getValue($value, 'tgl_lahir'),
               'umur' => ArrayHelper::getValue($value, 'umur'),
               'tgl_pendaftaran' => $jamDaftarAkhirRnp,
               'tgl_pulang' => $jamPlgAkhirRnp,
               'instalasi_id' => trim(ArrayHelper::getValue($value, 'instalasi_id')),
               'instalasi_kode' => trim(ArrayHelper::getValue($value, 'instalasi_kode')),
               'instalasi_nama' => trim(ArrayHelper::getValue($value, 'instalasi_nama')),
               'ruangan_id' => trim(ArrayHelper::getValue($value, 'ruangan_id')),
               'ruangan_kode' => ArrayHelper::getValue($value, 'ruangan_kode'),
               'ruangan_nama' => ArrayHelper::getValue($value, 'ruangan_nama'),
               'carabayar_kode' => ArrayHelper::getValue($value, 'carabayar_kode'),
               'carabayar_nama' => ArrayHelper::getValue($value, 'carabayar_nama'),
               'penjamin_kode' => ArrayHelper::getValue($value, 'penjamin_kode'),
               'penjamin_nama' => ArrayHelper::getValue($value, 'penjamin_nama'),
               'kelas_kode' => ArrayHelper::getValue($value, 'kelas_kode'),
               'kelas_nama' => ArrayHelper::getValue($value, 'kelas_nama'),
               'dokter_kode' =>  ArrayHelper::getValue($value, 'dokter_kode'),
               'dokter_nama' =>  ArrayHelper::getValue($value, 'dokter_nama'),
               'no_kamar' => ArrayHelper::getValue($value, 'kamar_kode'),
               'no_tempattidur' => ArrayHelper::getValue($value, 'bed_kode'),
               'carakeluar_kode' => ArrayHelper::getValue($value, 'carakeluar_kode'),
               'lama_rawat' => (string) $lama_rawat,
               'status_kunjungan' => DocoConstants::STATUS_BELUM_KOREKSI,
               'is_active' => true,
               'created_date' => $now,
               'created_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
               'is_deleted' => false,
               'nosep' => $no_sepRanap,
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
         if($is_update) {
            if(!empty($no_pendaftaran) && !empty($instalasi_kode)) {
               $updateData = array_diff_key($this->kunjungan[0], array_flip(["status_kunjungan"]));
               SyKunjunganPasien::updateAll($updateData, ['no_pendaftaran' => $no_pendaftaran]);
            }
            
         } else {
            if(!empty($no_pendaftaran) && !empty($instalasi_kode)) {
               $deleteKunjungan = "delete from sy_kunjungan where no_pendaftaran ='$no_pendaftaran'";
               $hapusKunjungan = Yii::$app->db->createCommand($deleteKunjungan)->execute();
            }
            
            $syncKunjungan = SyKunjunganPasien::batchInsert($this->kunjungan);
         }
      }

      /* ganti proses agar cron tetap berjalan */
      $this->updateCron();

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Berhasil menyiapkan data Kunjungan.',
            'progress' => 70
         ]),
      ]);

      return $this->kunjungan;
   }

   protected function updateCron()
   {
      $modelCronKunjungan = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_KUNJUNGAN])->one();
      if(!empty($modelCronKunjungan)) {
         $modelCronKunjungan->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCronKunjungan->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCronKunjungan->save();
      }
   }

   protected function getDataKunjungan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api = false)
   {
      $kunjunganRajal = $kunjunganRanap = [];
      if(!empty($no_pendaftaran) && !empty($instalasi_kode)) {
         $kunjunganRajal = $this->getDataKunjunganRajalPerPasien($no_pendaftaran, $instalasi_kode);
         $kunjunganRanap = $this->getDataKunjunganRanapPerPasien($no_pendaftaran, $instalasi_kode);
      } elseif($is_api) {
         $kunjunganRajal = $this->getDataKunjunganRajalApi($tgl_pendaftaran);
         $kunjunganRanap = $this->getDataKunjunganRanapApi($tgl_pendaftaran);
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
      return Yii::$app->db->createCommand("
         SELECT * FROM sy_kunjungan WHERE no_pendaftaran = '{$this->no_pendaftaran}' AND is_deleted = false
      ")->queryOne();
   }

   protected function getDataKunjunganRajalApi($tgl_pendaftaran)
   {
      $url = 'sirs/opdvisit';
      $options = [
         'tanggal_awal' => $tgl_pendaftaran, 
         'tanggal_akhir' => $tgl_pendaftaran
      ];


      $response = (new AsuransiPenjaminService)->getData($url, $options);
      $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
      return $result;
   }

   protected function getDataKunjunganRanapApi($tgl_pendaftaran)
   {
      $url = 'sirs/ipdvisit';
      $options = [
         'tanggal_awal' => $tgl_pendaftaran, 
         'tanggal_akhir' => $tgl_pendaftaran
      ];

      $response = (new AsuransiPenjaminService)->getData($url, $options);
      $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
      return $result;
   }
}