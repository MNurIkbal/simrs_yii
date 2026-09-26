<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\AsuransiPenjaminService;
use Integrasi\Service\Sirs\Models\SyKunjunganPasien;
use Integrasi\Service\Sirs\Models\Cron;
use Integrasi\Service\Sirs\Models\SyKunjunganTagihan;

class SinkronTagihan extends \Integrasi\Contracts\DocoImplement
{

   protected $groupBpjs = DocoConstants::GROUP_BPJS;
   protected $instalasiRanap = DocoConstants::INST_ID_RI;
   protected $kunjungan = [];
   protected $kunjunganRanap = [];
   protected $tmpNoDaftar = [];
   protected $hapusNoPendaftaran = [];
   protected $tmpTagihan = [];
   protected $tagihan = [];

   /**
     * @todo Function get data kunjungan
     * @return array
     * @author Budi <budi@sirs.co.id>
     */
   public function execute()
   {
      date_default_timezone_set('Asia/Jakarta');
      $tgl_pendaftaran = $this->tgl_pendaftaran;
      $jam_pendaftaran = $this->jam_pendaftaran;
      $no_pendaftaran  = $this->no_pendaftaran;
      $instalasi_kode  = $this->instalasi_kode;
      $is_api =  filter_var($this->is_api, FILTER_VALIDATE_BOOLEAN);

      self::publishMessage(90);

      $data = $this->insertTagihan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
      return json_encode([
         'service' => 'Sirs-SinkronTagihan',
         'attributes' => $this->attributes,
         'payload' => $data,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data all pasien 
    */
   protected function getDataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT 
         pendaftaran_t.no_pendaftaran,
         pendaftaran_t.pasien_id, 
         pasien_m.no_rekam_medik as no_rekammedik,
         pendaftaran_T.pendaftaran_id,
         tindakanpelayanan_t.pendaftaran_id,
         daftartindakan_m.daftartindakan_kode as layanan_kode,
         daftartindakan_m.daftartindakan_nama as layanan_nama,
         ruangan_m.ruangan_singkatan as ruangan_kode,
         ruangan_m.ruangan_nama as ruangan_nama,
         tindakanpelayanan_t.no_tindakanpelayanan as tindakan_kode,
         tindakanpelayanan_t.qty_tindakan as layanan_qty,
         tindakanpelayanan_t.tarif_tindakan as layanan_tarif,
         tindakanpelayanan_t.tarif_tindakan as jasa_rs,
         0 as jasa_dokter,
         pegawai_m.dokter_id  as dokter_kode,
         pegawai_m.nama_pegawai as dokter_nama,
         pasienpulang_t.tglpasienpulang as tgl_pulang,
         kelaspelayanan_m.kelaspelayanan_kode as kelas_kode,
         pendaftaran_t.tgl_pendaftaran,
         pembayaran_t.no_pembayaran as no_buktitrans,
         NULL as kode_nota,
         NULL as kel_report,
         0 as tarifrs_akt,
         NULL as total_adjust,
         NULL as kode_adjust,
         groupinacbg_m.groupinacbg_id,
         groupinacbg_m.groupinacbg_nama,
         groupinacbg_m.groupinacbg_kode
      FROM pendaftaran_t
      JOIN pasienpulang_t on pasienpulang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id and pasienpulang_t.is_deleted = false and pasienpulang_t.pasienbatalpulang_id is null
      JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
      JOIN pembayaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id and pembayaran_t.is_deleted = false
      JOIN tindakansudahbayar_t on tindakansudahbayar_t.pembayaran_id = pembayaran_t.pembayaran_id and tindakansudahbayar_t.is_deleted = false
      JOIN tindakanpelayanan_t on tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id and tindakanpelayanan_t.is_deleted = false
      JOIN daftartindakan_m on tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id  
      JOIN ruangan_m  on pendaftaran_t.ruangan_id  = ruangan_m.ruangan_id  
      JOIN pegawai_m on tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id 
      JOIN kelaspelayanan_m on tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
      JOIN carabayar_m on carabayar_m.carabayar_id = pendaftaran_t.carabayar_id 
      JOIN groupinacbg_m on groupinacbg_m.groupinacbg_id = daftartindakan_m.groupinacbg_id
      WHERE 
         to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD') = '{$tgl_pendaftaran}' 
         AND to_char(pendaftaran_t.tgl_pendaftaran, 'HH24-MI-SS') > '{$jam_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
      ORDER BY pendaftaran_t.no_pendaftaran ASC
      ")->queryAll();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data per pasien
    */
   protected function getDataTagihanRajalPasien($no_pendaftaran)
   {
      $inacbgKronisId = DocoConstants::OBAT_KRONIS;
      return Yii::$app->db->createCommand("
      SELECT
         DISTINCT ON (tindakanpelayanan_t.tindakanpelayanan_id)
         pendaftaran_t.no_pendaftaran,
         pendaftaran_t.pasien_id, 
         pasien_m.no_rekam_medik as no_rekammedik,
         pendaftaran_T.pendaftaran_id,
         tindakanpelayanan_t.pendaftaran_id,
         daftartindakan_m.daftartindakan_kode as layanan_kode,
         daftartindakan_m.daftartindakan_nama as layanan_nama,
         ruangan_m.ruangan_singkatan as ruangan_kode,
         ruangan_m.ruangan_nama as ruangan_nama,
         tindakanpelayanan_t.no_tindakanpelayanan as tindakan_kode,
         tindakanpelayanan_t.qty_tindakan as layanan_qty,
         tindakanpelayanan_t.dijamin_payer as layanan_tarif,
         tindakanpelayanan_t.dijamin_payer as jasa_rs,
         0 as jasa_dokter,
         pegawai_m.dokter_id  as dokter_kode,
         pegawai_m.nama_pegawai as dokter_nama,
         pasienpulang_t.tglpasienpulang as tgl_pulang,
         kelaspelayanan_m.kelaspelayanan_kode as kelas_kode,
         pendaftaran_t.tgl_pendaftaran,
         pembayaran_t.no_pembayaran as no_buktitrans,
         NULL as kode_nota,
         NULL as kel_report,
         0 as tarifrs_akt,
         NULL as total_adjust,
         NULL as kode_adjust,
         groupinacbg_m.groupinacbg_id,
         groupinacbg_m.groupinacbg_nama,
         groupinacbg_m.groupinacbg_kode
      FROM pendaftaran_t
      JOIN pasienpulang_t on pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id   and pasienpulang_t.is_deleted = false and pasienpulang_t.pasienbatalpulang_id is null
      JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
      JOIN pembayaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id and pembayaran_t.is_deleted = false
      JOIN tindakansudahbayar_t on tindakansudahbayar_t.pembayaran_id = pembayaran_t.pembayaran_id and tindakansudahbayar_t.is_deleted = false
      JOIN tindakanpelayanan_t on tindakansudahbayar_t.tindakanpelayanan_id  = tindakanpelayanan_t.tindakanpelayanan_id  and tindakanpelayanan_t.is_deleted = false
      JOIN daftartindakan_m on tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id  
      JOIN ruangan_m  on pendaftaran_t.ruangan_id  = ruangan_m.ruangan_id  
      JOIN pegawai_m on tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id 
      JOIN kelaspelayanan_m on tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
      JOIN carabayar_m on carabayar_m.carabayar_id = pendaftaran_t.carabayar_id 
      JOIN groupinacbg_m on groupinacbg_m.groupinacbg_id = daftartindakan_m.groupinacbg_id
      WHERE 
         pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
      UNION ALL
      SELECT
         DISTINCT ON (obatalkespasien_t.obatalkespasien_id)
         pendaftaran_t.no_pendaftaran,
         pendaftaran_t.pasien_id, 
         pasien_m.no_rekam_medik as no_rekammedik,
         pendaftaran_t.pendaftaran_id,
         obatalkespasien_t.pendaftaran_id,
         obatalkes_m.obatalkes_kode  as layanan_kode,
         obatalkes_m.obatalkes_namalain  as layanan_nama,
         ruangan_m.ruangan_singkatan as ruangan_kode,
         ruangan_m.ruangan_nama as ruangan_nama,
         obatalkespasien_t.obatalkespasien_id::varchar as tindakan_kode,
         obatalkespasien_t.qty_oa as layanan_qty,
         obatalkespasien_t.dijamin_payer as layanan_tarif,
         obatalkespasien_t.dijamin_payer as jasa_rs,
         0 as jasa_dokter,
         pegawai_m.dokter_id  as dokter_kode,
         pegawai_m.nama_pegawai as dokter_nama,
         pasienpulang_t.tglpasienpulang as tgl_pulang,
         kelaspelayanan_m.kelaspelayanan_kode as kelas_kode,
         pendaftaran_t.tgl_pendaftaran,
         pembayaran_t.no_pembayaran as no_buktitrans,
         NULL as kode_nota,
         NULL as kel_report,
         0 as tarifrs_akt,
         NULL as total_adjust,
         NULL as kode_adjust,
         groupinacbg_m.groupinacbg_id,
         groupinacbg_m.groupinacbg_nama,
         groupinacbg_m.groupinacbg_kode
      FROM pendaftaran_t
      JOIN pasienpulang_t on pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id and pasienpulang_t.is_deleted = false and pasienpulang_t.pasienbatalpulang_id is null
      JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
      JOIN pembayaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id and pembayaran_t.is_deleted = false
      JOIN obatsudahbayar_t on obatsudahbayar_t.pembayaran_id = pembayaran_t.pembayaran_id
      JOIN obatalkespasien_t on obatalkespasien_t.obatsudahbayar_id  = obatsudahbayar_t.obatsudahbayar_id and obatalkespasien_t.is_deleted = false and obatalkespasien_t.obatsudahbayar_id is not null
      JOIN obatalkes_m on obatalkespasien_t.obatalkes_id  = obatalkes_m.obatalkes_id  
      JOIN ruangan_m  on pendaftaran_t.ruangan_id  = ruangan_m.ruangan_id  
      JOIN pegawai_m on obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id 
      JOIN kelaspelayanan_m on obatalkespasien_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
      JOIN carabayar_m on carabayar_m.carabayar_id = pendaftaran_t.carabayar_id 
      JOIN groupinacbg_m on groupinacbg_m.groupinacbg_id = (
      	CASE 
      		WHEN obatalkespasien_t.is_kronis THEN '{$inacbgKronisId}'
      		ELSE obatalkes_m.groupinacbg_id
      	END
      )
      WHERE 
      pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
      AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
      ")->queryAll();
   }


   /**
    * @author Maulana Muhammad Rizky
    * Get data all pasien 
    */
   protected function getDataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran)
   {
      return Yii::$app->db->createCommand("
         SELECT 
            pasienadmisi_t.pendaftaran_id, 
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id, 
            pasien_m.no_rekam_medik as no_rekammedik,
            pendaftaran_T.pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            daftartindakan_m.daftartindakan_kode as layanan_kode,
            daftartindakan_m.daftartindakan_nama as layanan_nama,
            ruangan_m.ruangan_singkatan as ruangan_kode,
            ruangan_m.ruangan_nama as ruangan_nama,
            tindakanpelayanan_t.no_tindakanpelayanan as tindakan_kode,
            tindakanpelayanan_t.qty_tindakan as layanan_qty,
            tindakanpelayanan_t.tarif_tindakan as layanan_tarif,
            tindakanpelayanan_t.tarif_tindakan as jasa_rs,
            0 as jasa_dokter,
            pegawai_m.dokter_id  as dokter_kode,
            pegawai_m.nama_pegawai as dokter_nama,
            pasienpulang_t.tglpasienpulang as tgl_pulang,
            kelaspelayanan_m.kelaspelayanan_kode as kelas_kode,
            pendaftaran_t.tgl_pendaftaran,
            pembayaran_t.no_pembayaran as no_buktitrans,
            null as kode_nota,
            null as kel_report,
            0 as tarifrs_akt,
            NULL as total_adjust,
            NULL as kode_adjust,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            groupinacbg_m.groupinacbg_kode
         FROM pasienadmisi_t
         JOIN pendaftaran_t on pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id 
         JOIN pasienpulang_t on pasienpulang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id and pasienpulang_t.is_deleted = false and pasienpulang_t.pasienbatalpulang_id is null and pasienpulang_t.pasienadmisi_id is not null
         JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN pembayaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id and pembayaran_t.is_deleted = false
         JOIN tindakansudahbayar_t on tindakansudahbayar_t.pembayaran_id = pembayaran_t.pembayaran_id and tindakansudahbayar_t.is_deleted = false
	      JOIN tindakanpelayanan_t on tindakansudahbayar_t.tindakanpelayanan_id  = tindakanpelayanan_t.tindakanpelayanan_id  and tindakanpelayanan_t.is_deleted = false
         JOIN daftartindakan_m on tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id  
         JOIN ruangan_m  on pendaftaran_t.ruangan_id  = ruangan_m.ruangan_id  
         JOIN pegawai_m on tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id 
         JOIN kelaspelayanan_m on tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN carabayar_m on carabayar_m.carabayar_id = pendaftaran_t.carabayar_id 
         JOIN groupinacbg_m on groupinacbg_m.groupinacbg_id = daftartindakan_m.groupinacbg_id
         WHERE 
            to_char(pasienpulang_t.tglpasienpulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}' 
            AND to_char(pasienpulang_t.tglpasienpulang, 'HH24-MI-SS') > '{$jam_pendaftaran}'
            AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         ORDER BY pendaftaran_t.no_pendaftaran ASC 
      ")->queryAll();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Get data per pasien
    */
   protected function getTagihanRanapPasien($no_pendaftaran)
   {
      $inacbgKronisId = DocoConstants::OBAT_KRONIS;
      return Yii::$app->db->createCommand("
         SELECT 
            DISTINCT ON (tindakanpelayanan_t.tindakanpelayanan_id)
            pasienadmisi_t.pendaftaran_id, 
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id, 
            pasien_m.no_rekam_medik as no_rekammedik,
            pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            daftartindakan_m.daftartindakan_kode as layanan_kode,
            daftartindakan_m.daftartindakan_nama as layanan_nama,
            ruangan_m.ruangan_singkatan as ruangan_kode,
            ruangan_m.ruangan_nama as ruangan_nama,
            tindakanpelayanan_t.no_tindakanpelayanan as tindakan_kode,
            tindakanpelayanan_t.qty_tindakan as layanan_qty,
            tindakanpelayanan_t.dijamin_payer as layanan_tarif,
            tindakanpelayanan_t.dijamin_payer as jasa_rs,
            0 as jasa_dokter,
            pegawai_m.dokter_id  as dokter_kode,
            pegawai_m.nama_pegawai as dokter_nama,
            pasienpulang_t.tglpasienpulang as tgl_pulang,
            kelaspelayanan_m.kelaspelayanan_kode as kelas_kode,
            pendaftaran_t.tgl_pendaftaran,
            pembayaran_t.no_pembayaran as no_buktitrans,
            null as kode_nota,
            null as kel_report,
            0 as tarifrs_akt,
            NULL as total_adjust,
            NULL as kode_adjust,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            groupinacbg_m.groupinacbg_kode
         FROM pasienadmisi_t
         JOIN pendaftaran_t on pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id 
         JOIN pasienpulang_t on pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id and pasienpulang_t.is_deleted = false and pasienpulang_t.pasienbatalpulang_id is null and pasienpulang_t.pasienadmisi_id is not null
         JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN pembayaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id and pembayaran_t.is_deleted = false
         JOIN tindakansudahbayar_t on tindakansudahbayar_t.pembayaran_id = pembayaran_t.pembayaran_id and tindakansudahbayar_t.is_deleted = false
         JOIN tindakanpelayanan_t on tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id and tindakanpelayanan_t.is_deleted = false
         JOIN daftartindakan_m on tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id  
         JOIN ruangan_m  on pendaftaran_t.ruangan_id  = ruangan_m.ruangan_id  
         JOIN pegawai_m on tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id 
         JOIN kelaspelayanan_m on tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
         JOIN carabayar_m on carabayar_m.carabayar_id = pasienadmisi_t.carabayar_id 
         JOIN groupinacbg_m on groupinacbg_m.groupinacbg_id = daftartindakan_m.groupinacbg_id
         WHERE 
            pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
            AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
            AND pasienadmisi_t.is_deleted = false  
            AND pasienadmisi_t.is_active = true  
         UNION ALL
         SELECT 
            DISTINCT ON (obatalkespasien_t.obatalkespasien_id)
            pasienadmisi_t.pendaftaran_id, 
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id, 
            pasien_m.no_rekam_medik as no_rekammedik,
            pendaftaran_t.pendaftaran_id,
            obatalkespasien_t.pendaftaran_id,
            obatalkes_m.obatalkes_kode  as layanan_kode,
            obatalkes_m.obatalkes_namalain  as layanan_nama,
            ruangan_m.ruangan_singkatan as ruangan_kode,
            ruangan_m.ruangan_nama as ruangan_nama,
            obatalkespasien_t.obatalkespasien_id::varchar as tindakan_kode,
            obatalkespasien_t.qty_oa as layanan_qty,
            obatalkespasien_t.dijamin_payer as layanan_tarif,
            obatalkespasien_t.dijamin_payer as jasa_rs,
            0 as jasa_dokter,
            pegawai_m.dokter_id  as dokter_kode,
            pegawai_m.nama_pegawai as dokter_nama,
            pasienpulang_t.tglpasienpulang as tgl_pulang,
            kelaspelayanan_m.kelaspelayanan_kode as kelas_kode,
            pendaftaran_t.tgl_pendaftaran,
            pembayaran_t.no_pembayaran as no_buktitrans,
            null as kode_nota,
            null as kel_report,
            0 as tarifrs_akt,
            NULL as total_adjust,
            NULL as kode_adjust,
            groupinacbg_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            groupinacbg_m.groupinacbg_kode
         FROM pasienadmisi_t
         JOIN pendaftaran_t on pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id 
         JOIN pasienpulang_t on pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id  and pasienpulang_t.is_deleted = false and pasienpulang_t.pasienbatalpulang_id is null and pasienpulang_t.pasienadmisi_id is not null
         JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN pembayaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id and pembayaran_t.is_deleted = false
         JOIN obatsudahbayar_t on obatsudahbayar_t.pembayaran_id = pembayaran_t.pembayaran_id
         JOIN obatalkespasien_t on obatalkespasien_t.obatsudahbayar_id  = obatsudahbayar_t.obatsudahbayar_id and obatalkespasien_t.is_deleted = false and obatalkespasien_t.obatsudahbayar_id is not null
         JOIN obatalkes_m on obatalkespasien_t.obatalkes_id  = obatalkes_m.obatalkes_id  
         JOIN ruangan_m  on pendaftaran_t.ruangan_id  = ruangan_m.ruangan_id  
         JOIN pegawai_m on obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id 
         JOIN kelaspelayanan_m on obatalkespasien_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
         JOIN carabayar_m on carabayar_m.carabayar_id = pasienadmisi_t.carabayar_id 
         JOIN groupinacbg_m on groupinacbg_m.groupinacbg_id = (
            CASE 
               WHEN obatalkespasien_t.is_kronis THEN '{$inacbgKronisId}'
               ELSE obatalkes_m.groupinacbg_id
            END
         )
         WHERE 
         pendaftaran_t.no_pendaftaran = '{$no_pendaftaran}'
         AND carabayar_m.groupcarabayar_id = '{$this->groupBpjs}'
         AND pasienadmisi_t.is_deleted = false  
         AND pasienadmisi_t.is_active = true  
      ")->queryAll();
   }

   /**
    * @author Maulana Muhammad Rizky
    * Proses untuk melakukan insert data kedalam basis data 
    */
   protected function insertTagihan($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api = false)
   {
      $dataRow = $this->getDataKunjungan($tgl_pendaftaran, $no_pendaftaran);
      if(! empty($dataRow)) {
         $dataTagihanRanap = $this->dataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
         if (! empty($dataTagihanRanap)) {
            foreach ($dataTagihanRanap as $key => $value) {
               $noTagihRanap = (isset($value['no_pendaftaran'])) ? $value['no_pendaftaran'] : $value['NO_PENDAFTARAN'];
               if (! empty($no_pendaftaran)) {
                  $modelTagihan = $this->getDataKunjunganRajal($noTagihRanap, false, true);
               }else {
                  $modelTagihan = $this->getDataKunjunganRajal($noTagihRanap, true, true);
               }
               
               $inacbgId = ArrayHelper::getValue($value, "inacbg_id");
               if (! empty($modelTagihan) || ! empty($inacbgId) ) {
                  if (!isset($tmpTagihan[$noTagihRanap])) {
                     $tmpTagihan[$noTagihRanap] = $noTagihRanap;
                  }

                  $listKunjunganId = [];
                  $totalAdjust = 0;
                  foreach ($modelTagihan as $key => $data) {
                     $kunjunganId = ArrayHelper::getValue($data, 'kunjungan_id');
                     $noPendaftaran = ArrayHelper::getValue($data, 'no_pendaftaran');
                     if(!empty($noPendaftaran) && !empty($kunjunganId)) {
                        $listKunjunganId[$noPendaftaran] = $kunjunganId;
                     }
                  }

                  if (isset($value['total_adjust'])) {
                     if ($value['total_adjust'] == '') {
                        $totalAdjust = 0;
                     }else{
                        $totalAdjust = $value['total_adjust'];
                     }
                  }

                  if (isset($value['tarifrs_akt'])) {
                     if ($value['tarifrs_akt'] == '') {
                        $tarifrs_akt = 0;
                     }else{
                        $tarifrs_akt = $value['tarifrs_akt'];
                     }
                  }
                    
                  $item = [
                     'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noTagihRanap),
                     'no_pendaftaran' => $noTagihRanap,
                     'no_rekammedik' => ArrayHelper::getValue($value, "no_rekammedik"),
                     'layanan_kode' => ArrayHelper::getValue($value, "layanan_kode"),
                     'layanan_nama' => ArrayHelper::getValue($value, "layanan_nama"),
                     'layanan_qty' => ArrayHelper::getValue($value, "layanan_qty"),
                     'layanan_tarif' => ArrayHelper::getValue($value, "layanan_tarif"),
                     'tindakan_kode' => ArrayHelper::getValue($value, "tindakan_kode"),
                     'ruangan_kode' => ArrayHelper::getValue($value, "ruangan_kode"),
                     'ruangan_nama' => ArrayHelper::getValue($value, "ruangan_nama"),
                     'jasa_rs' => ArrayHelper::getValue($value, "jasa_rs"),
                     'jasa_dokter' => ArrayHelper::getValue($value, "jasa_dokter"),
                     'dokter_kode' => ArrayHelper::getValue($value, "dokter_kode"),
                     'dokter_nama' => ArrayHelper::getValue($value, "dokter_nama"),
                     'kode_nota' => ArrayHelper::getValue($value, "kode_nota"),
                     'kel_report' => ArrayHelper::getValue($value, "kel_report"),
                     'tarifrs_akt' => $tarifrs_akt,
                     'no_buktitrans' => ArrayHelper::getValue($value, "no_buktitrans"),
                     'kelas_kode' => ArrayHelper::getValue($value, "kelas_kode"),
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
         
         $dataTagihanRajal = $this->dataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api);
         if (! empty($dataTagihanRajal)) {
            foreach ($dataTagihanRajal as $key => $value) {
               $noTagihanRajal = ArrayHelper::getValue($value, 'no_pendaftaran');
               if (! empty($no_pendaftaran)) {
                  $modelTagihan = $this->getDataKunjunganRajal($noTagihanRajal, false);
               }else {
                  $modelTagihan = $this->getDataKunjunganRajal($noTagihanRajal);
               }
               
               $inacbgId = ArrayHelper::getValue($value, "inacbg_id");
               if (! empty($modelTagihan) || ! empty($inacbgId)) {
                  if (!isset($tmpTagihan[$noTagihanRajal])) {
                     $tmpTagihan[$noTagihanRajal] = $noTagihanRajal;
                  }

                  $listKunjunganId = [];
                  $totalAdjust = 0;
                  foreach ($modelTagihan as $key => $data) {
                     $kunjunganId = ArrayHelper::getValue($data, 'kunjungan_id');
                     $noPendaftaran = ArrayHelper::getValue($data, 'no_pendaftaran');
                     if(!empty($noPendaftaran) && !empty($kunjunganId)) {
                        $listKunjunganId[$noPendaftaran] = $kunjunganId;
                     }
                  }
                  if (isset($value['total_adjust'])) {
                     if ($value['total_adjust'] == '') {
                        $totalAdjust = 0;
                     }else{
                        $totalAdjust = $value['total_adjust'];
                     }
                  }


                  if (isset($value['tarifrs_akt'])) {
                     if ($value['tarifrs_akt'] == '') {
                        $tarifrs_akt = 0;
                     }else{
                        $tarifrs_akt = $value['tarifrs_akt'];
                     }
                  }

                  $item = [
                     'kunjungan_id' => ArrayHelper::getValue($listKunjunganId, $noTagihanRajal),
                     'no_pendaftaran' => $noTagihanRajal,
                     'no_rekammedik' => ArrayHelper::getValue($value, "no_rekammedik"),
                     'layanan_kode' => ArrayHelper::getValue($value, "layanan_kode"),
                     'layanan_nama' => ArrayHelper::getValue($value, "layanan_nama"),
                     'layanan_qty' => ArrayHelper::getValue($value, "layanan_qty"),
                     'layanan_tarif' => ArrayHelper::getValue($value, "layanan_tarif"),
                     'tindakan_kode' => ArrayHelper::getValue($value, "tindakan_kode"),
                     'ruangan_kode' => ArrayHelper::getValue($value, "ruangan_kode"),
                     'ruangan_nama' => ArrayHelper::getValue($value, "ruangan_nama"),
                     'jasa_rs' => ArrayHelper::getValue($value, "jasa_rs"),
                     'jasa_dokter' => ArrayHelper::getValue($value, "jasa_dokter"),
                     'dokter_kode' => ArrayHelper::getValue($value, "dokter_kode"),
                     'dokter_nama' => ArrayHelper::getValue($value, "dokter_nama"),
                     'kode_nota' => ArrayHelper::getValue($value, "kode_nota"),
                     'kel_report' => ArrayHelper::getValue($value, "kel_report"),
                     'tarifrs_akt' => $tarifrs_akt,
                     'no_buktitrans' => ArrayHelper::getValue($value, "no_buktitrans"),
                     'kelas_kode' => ArrayHelper::getValue($value, "kelas_kode"),
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
            
         if (!empty($tmpTagihan)) {
            $tmpTagihanDouble = array();
            foreach ($tmpTagihan as $key) {
                  $tmpTagihanDouble[] = $key;
            }
            $tmpTagihanDouble = implode("','", $tmpTagihanDouble);
            $sql = "delete from sy_kunjungantagihan where no_pendaftaran IN ('$tmpTagihanDouble')";
            $hapusTagihan = Yii::$app->db->createCommand($sql)->execute();
         }
         /** Kondisi single sync */
         if(! empty($no_pendaftaran)) {
               if (!empty($this->tagihan)) {
                  $this->deletePreviousData($no_pendaftaran);
                  $syncTagihan = SyKunjunganTagihan::batchInsert($this->tagihan);
               }
         }else {
            if (!empty($this->tagihan)) {
               SyKunjunganTagihan::batchInsert($this->tagihan);
            }
         }

         if(! empty($no_pendaftaran)) {
            // Handle kondisi notif untuk di status berhasil.
            if(in_array($dataRow[0]['status_kunjungan'], [DocoConstants::STATUS_SUDAH_KOREKSI, DocoConstants::STATUS_BELUM_KOREKSI])) {
               self::publishMessage(100, false);
            } else {
               self::publishMessage(100);
            }
         } else {
           self::publishMessage(100, false);
         }
      } else {
         self::publishMessage(100);
      }
      /* ganti proses agar cron tetap berjalan */
      $this->updateCron();
      
      return $this->tagihan;
   }

   /**
    * @author Maulana Muhammad Rizky
    * Routing untuk pengambilan data
    */
   protected function dataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api)
   {
      if (! empty($tgl_pendaftaran) && ! empty($jam_pendaftaran) && ! $is_api) {
         return $this->getDataTagihanRanap($tgl_pendaftaran, $jam_pendaftaran);
      } elseif(! empty($tgl_pendaftaran) && ! empty($jam_pendaftaran) && $is_api) {
         return $this->getDataTagihanRanapApi($tgl_pendaftaran);
      } else {
         return $this->getTagihanRanapPasien($no_pendaftaran);
      }
   }

   protected function dataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran, $no_pendaftaran, $instalasi_kode, $is_api)
   {
      if (! empty($tgl_pendaftaran) && ! empty($jam_pendaftaran) && ! $is_api) {
         return $this->getDataTagihanRajal($tgl_pendaftaran, $jam_pendaftaran);
      }elseif(! empty($tgl_pendaftaran) && ! empty($jam_pendaftaran) && $is_api) {
         return $this->getDataTagihanRajalApi($tgl_pendaftaran);
      } else {
         return $this->getDataTagihanRajalPasien($no_pendaftaran);
      }
   }

   /**
    * Fungsi untuk hapus data sebelumnya
    */
   private function deletePreviousData($no_pendaftaran)
   {
      $deleteTagihan = "delete from sy_kunjungantagihan where no_pendaftaran in (SELECT no_pendaftaran from sy_kunjungan where no_pendaftaran='$no_pendaftaran')";
      $hapusTagihan = Yii::$app->db->createCommand($deleteTagihan)->execute();
   }

   /**
    * Fungsi untuk melakukan update cron
    */
   protected function updateCron()
   {
      $modelCronKunjungan = Cron::find()->where(['cron_id' => DocoConstants::VAR_CRON_TAGIHAN])->one();
      if(!empty($modelCronKunjungan)) {
         $modelCronKunjungan->cron_tgl_mulai = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCronKunjungan->cron_tgl_akhir = date('Y-m-d H:i:s', strtotime('NOW'));
         $modelCronKunjungan->save();
      }
   }

   protected function getDataKunjunganRajal($no_pendaftaran, $withStatus = true, $isRanap = false)
   {
      $statusSudahKoreksi = DocoConstants::STATUS_SUDAH_KOREKSI;
      $statusBelumKoreksi = DocoConstants::STATUS_BELUM_KOREKSI;
      $instalasiKodeRi = DocoConstants::SINGKATAN_RI;
      $withCondition = $withStatus ? ' AND status_kunjungan IN ('.$statusSudahKoreksi.','.$statusBelumKoreksi.') ' : '';
      $conditionNonRanap = "AND instalasi_kode != '{$instalasiKodeRi}'";
      $conditionRanap = "AND instalasi_kode = '{$instalasiKodeRi}'";
      $withRanap = $isRanap ? $conditionRanap : $conditionNonRanap;

      return Yii::$app->db->createCommand("
         SELECT no_pendaftaran, kunjungan_id, status_kunjungan
         FROM sy_kunjungan 
         WHERE no_pendaftaran = '{$no_pendaftaran}'
         {$withCondition}
         {$withRanap}
         GROUP BY no_pendaftaran, kunjungan_id
      ")->queryAll();
   }

   protected function getDataKunjungan($tgl_pendaftaran, $no_pendaftaran)
   {
      $statusSudahKoreksi = DocoConstants::STATUS_SUDAH_KOREKSI;
      $statusBelumKoreksi = DocoConstants::STATUS_BELUM_KOREKSI;
      if(! empty($no_pendaftaran)) {
         return Yii::$app->db->createCommand("
            SELECT no_pendaftaran, kunjungan_id, status_kunjungan
            FROM sy_kunjungan 
            WHERE no_pendaftaran = '{$no_pendaftaran}'
            AND status_kunjungan IN ('{$statusSudahKoreksi}','{$statusBelumKoreksi}')
            GROUP BY no_pendaftaran, kunjungan_id
         ")->queryAll();
      }else{
         return Yii::$app->db->createCommand("
            SELECT no_pendaftaran, kunjungan_id
            FROM sy_kunjungan 
            WHERE to_char(tgl_pendaftaran, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
            OR to_char(tgl_pulang, 'YYYY-MM-DD') = '{$tgl_pendaftaran}'
            GROUP BY no_pendaftaran, kunjungan_id
         ")->queryAll();
      }
   }

   /**
   * Function untuk mendapatkan API diagnosa Rawat Inap
   */
   protected function getDataTagihanRajalApi($tgl_pendaftaran)
   {
      $url = 'sirs/opdbill';
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
   protected function getDataTagihanRanapApi($tgl_pendaftaran)
   {
      $url = 'sirs/ipdbill';
      $options = [
         'tanggal_awal' => $tgl_pendaftaran, 
         'tanggal_akhir' => $tgl_pendaftaran
      ];

      $response = (new AsuransiPenjaminService)->getData($url, $options);
      $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
      return $result;
   } 

   protected function publishMessage($progressBar, $hide = true)
   {
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Berhasil menyiapkan data Tagihan.',
            'progress' => $progressBar,
            'hide' => $hide
         ]),
      ]);
   }
}