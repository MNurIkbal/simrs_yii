<?php

use yii\db\Migration;

/**
 * Class m200214_064852_migrate_20200214
 */
class m200214_064852_migrate_20200214 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."closingkasir_t" ADD COLUMN "pembayaran_nontunai" float8;');

         $this->execute('ALTER TABLE "public"."tariftindakan_m" ADD COLUMN "tarifparent_id" int4;');

         $this->execute('DROP VIEW if exists public.tindakanruangan_v;');

         $this->execute('
            CREATE OR REPLACE VIEW public.tindakanruangan_v AS 
        SELECT tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanruangan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    daftartindakan_m.daftartindakan_namalainnya,
    kategoritindakan_m.kategoritindakan_nama,
    kelompoktindakan_m.kelompoktindakan_nama,
    jeniskegiatantindakan_m.jeniskegiatantindakan_nama,
    groupinacbg_m.groupinacbg_nama,
    tindakanruangan_mp.is_deleted,
    tindakanruangan_mp.is_active,
    tindakanruangan_mp.created_date,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM tindakanruangan_mp
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN daftartindakan_m ON tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN jeniskegiatantindakan_m ON daftartindakan_m.jeniskegiatantindakan_id = jeniskegiatantindakan_m.jeniskegiatantindakan_id
     LEFT JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id;');

         $this->execute('ALTER TABLE public.tindakanruangan_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infoobatalkespasien_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infoobatalkespasien_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    obatalkespasien_t.obatalkespasien_id,
    racikan_m.racikan_nama,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    satuanunit_m.satuanunit_nama AS satuankecil_nama,
    obatalkespasien_t.signa_oa,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.tglpelayanan,
    obatalkespasien_t.obatsudahbayar_id,
    pegawai_1.nama_pegawai,
    pegawai_2.nama_pegawai AS nama_pegawai_dua,
    obatalkespasien_t.pasienmasukpenunjang_id,
    obatalkespasien_t.harganetto_oa,
        CASE
            WHEN obatalkespasien_t.daftartindakan_id IS NULL THEN obatalkespasien_t.tipepaket_id
            ELSE obatalkespasien_t.daftartindakan_id
        END AS daftartindakan_id,
        CASE
            WHEN obatalkespasien_t.daftartindakan_id IS NULL THEN tipepaket_m.tipepaket_nama::character varying(200)
            ELSE daftartindakan_m.daftartindakan_nama
        END AS daftartindakan_nama,
    pendaftaran_t.pasien_id
   FROM obatalkespasien_t
     JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN satuanunit_m ON obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN pegawai_m pegawai_1 ON pegawai_1.pegawai_id = obatalkespasien_t.perawat1_id
     LEFT JOIN pegawai_m pegawai_2 ON pegawai_2.pegawai_id = obatalkespasien_t.perawat2_id
     LEFT JOIN daftartindakan_m ON obatalkespasien_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON obatalkespasien_t.tipepaket_id = tipepaket_m.tipepaket_id
  WHERE obatalkespasien_t.is_active = true AND obatalkespasien_t.is_deleted = false;");

         $this->execute('ALTER TABLE public.infoobatalkespasien_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.rincianpasiendetail2_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.rincianpasiendetail2_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.no_rekam_medik,
    COALESCE(sum(tagihan.tagihan), 0::double precision) AS total_tagihan,
    COALESCE(sum(pembayaranpelayanan_t.total_terbayar), 0::double precision) AS total_sdh_bayar,
    COALESCE(sum(pembayaranpelayanan_t.total_subsidiasuransi), 0::double precision) AS total_asuransi,
    COALESCE(sum(tagihan.tagihan), 0::double precision) - COALESCE(sum(pembayaranpelayanan_t.total_terbayar), 0::double precision) - COALESCE(sum(pembayaranpelayanan_t.total_subsidiasuransi), 0::double precision) AS total_sisa_tagihan,
    COALESCE(sum(bayaruangmuka_t.jumlah_uangmuka), 0::double precision) AS total_uang_muka,
    COALESCE(sum(pembayaranpelayanan_t.biaya_administrasi), 0::double precision) AS total_administrasi,
    0 AS total_pembulatan,
    0 AS total_pembayaran_pasien
   FROM pendaftaran_t
     LEFT JOIN ( SELECT 'tindakan'::text AS jenis,
            tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pendaftaran_id
        UNION ALL
         SELECT 'obat'::text AS jenis,
            obatalkespasien_t.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false
          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id AND bayaruangmuka_t.is_deleted = false
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE pendaftaran_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pasien_m.no_rekam_medik;");

         $this->execute('ALTER TABLE public.rincianpasiendetail2_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.rincianpasiendetail_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.rincianpasiendetail_v AS 
 SELECT 'tindakan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.instalasi_id AS instalasi_pelayanan_id,
    instalasi_pelayanan.instalasi_nama AS instalasi_pelayanan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    ruangan_pelayanan.ruangan_nama AS ruangan_pelayanan,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    false AS is_obat,
    tindakanpelayanan_t.tindakansudahbayar_id,
    daftartindakan_m.is_akomodasi,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN instalasi_m instalasi_pelayanan ON tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id
     LEFT JOIN ruangan_m ruangan_pelayanan ON tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id
     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
UNION ALL
 SELECT 'obat'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    NULL::integer AS instalasi_pelayanan_id,
    NULL::character varying AS instalasi_pelayanan,
    NULL::integer AS ruangan_pelayanan_id,
    NULL::character varying AS ruangan_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS pemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama,
    NULL::character varying AS pemeriksaanrad_nama,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    true AS is_obat,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    false AS is_akomodasi,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id;");

         $this->execute('ALTER TABLE public.rincianpasiendetail_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infotarifrs_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infotarifrs_v AS 
 SELECT 'tindakan'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    r_tindakan.ruangan_nama,
    r_tindakan.instalasi_id,
    ins_tindakan.instalasi_nama,
    NULL::integer AS ruanganpaket_id,
    NULL::character varying AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    daftartindakan_m.is_akomodasi,
    penjamin_m.carabayar_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
     JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
     JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
  WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true AND tariftindakan_m.tarifparent_id IS NULL
UNION ALL
 SELECT 'paket'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    r_paket.ruangan_nama,
    r_paket.instalasi_id,
    ins_paket.instalasi_nama,
    paketruangan_mp.ruangan_id AS ruanganpaket_id,
    r_paket.ruangan_namalainnya AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama,
    NULL::integer AS kategoritindakan_id,
    NULL::character varying AS kategoritindakan_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    NULL::boolean AS is_akomodasi,
    penjamin_m.carabayar_id
   FROM tariftindakan_m
     JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
     JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
     JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
  WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true AND tariftindakan_m.tarifparent_id IS NULL;
");

         $this->execute('ALTER TABLE public.infotarifrs_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infodatapendaftaran_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infodatapendaftaran_v AS 
 SELECT data_info.pendaftaran_id,
    data_info.instalasi_id AS ins_id,
    data_info.ruangan_id AS rua_id,
    data_info.pasien_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS pen_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS car_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.kelaspelayanan_id
            ELSE data_info.kelaspelayananri_id
        END AS kelaspelayanan_id,
    data_info.pasienpulang_id,
    data_info.no_pendaftaran,
    data_info.tgl_pendaftaran,
    data_info.no_rekam_medik,
    data_info.nama_pasien,
    data_info.no_mobile_pasien,
    data_info.instalasi_nama AS ins_nama,
    data_info.ruangan_nama AS rua_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS car,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS pen,
    data_info.kelaspelayanan_nama,
    data_info.jumlah_uangmuka,
    data_info.pasienpulangri_id,
    data_info.pasienadmisi_id,
    data_info.status_pasien,
    data_info.pasienmasukpenunjang_id,
        CASE
            WHEN data_info.tglpasienpulang IS NULL THEN data_info.tglpasienpulang_ri
            ELSE data_info.tglpasienpulang
        END AS tglpasienpulang,
    data_info.dokterrj_id,
    data_info.nama_dok_rj_rd,
    data_info.dokterri_id,
    data_info.nama_dok_ri,
    data_info.jeniskasuspenyakit_nama,
    data_info.umur,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS carabayar_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS penjamin_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS carabayar_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS penjamin_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_id
            ELSE data_info.instalasiri_id
        END AS instalasi_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_id
            ELSE data_info.ruanganri_id
        END AS ruangan_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_nama
            ELSE data_info.instalasi_nama_ri
        END AS instalasi_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_nama
            ELSE data_info.ruangan_nama_ri
        END AS ruangan_nama,
    data_info.status_bayar,
    data_info.jeniskasuspenyakit_id,
    data_info.tanggal_lahir,
    data_info.penjualanresep_id,
    data_info.jasa,
    data_info.administrasi,
    data_info.obat,
    data_info.totalharga_jual,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.kelas_bpjspendaftaran
            ELSE data_info.kelas_bpjsadmisi
        END AS hak_kelas,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL AND data_info.bpjs_idpendaftaran IS NOT NULL THEN data_info.no_bpjspendaftaran
            WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.bpjs_idadmisi IS NOT NULL THEN data_info.no_bpjsadmisi
            WHEN data_info.pasienadmisi_id IS NULL AND data_info.bpjs_idadmisi IS NULL THEN data_info.no_asuransipendaftaran
            WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.bpjs_idadmisi IS NULL THEN data_info.no_asuransiadmisi
            ELSE NULL::character varying
        END AS no_kartu,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.groupcarabayar_pendaftaran
            ELSE data_info.groupcarabayar_admisi
        END AS group_carabayar,
    data_info.total_piutang,
    data_info.keadaanmasuk_id,
    data_info.keadaan_masuk,
    data_info.transportasi_id,
    data_info.transportasi,
    data_info.keterangan_pendaftaran
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienpulang_t.tglpasienpulang,
            pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
            COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision) - COALESCE(pengembalianuangmuka_t.total_pengembalian, 0::double precision) AS jumlah_uangmuka,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.status_pasien,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
            dok_ri.nama_pegawai AS nama_dok_ri,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.umur,
            pasienadmisi_t.carabayar_id AS carabayarri_id,
            carabayar_ri.carabayar_nama AS carabayar_nama_ri,
            pasienadmisi_t.penjamin_id AS penjaminri_id,
            penjamin_ri.penjamin_nama AS penjamin_nama_ri,
            pasienadmisi_t.ruangan_id AS ruanganri_id,
            ruang_ri.instalasi_id AS instalasiri_id,
            ruang_ri.ruangan_nama AS ruangan_nama_ri,
            ins_ri.instalasi_nama AS instalasi_nama_ri,
            pendaftaran_t.status_bayar,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            NULL::integer AS penjualanresep_id,
            0 AS jasa,
                CASE
                    WHEN penjualan_resep.biaya_adm IS NULL THEN 0::double precision
                    ELSE penjualan_resep.biaya_adm
                END AS administrasi,
            0 AS obat,
            0 AS totalharga_jual,
            bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
            bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
            bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
            bpjs_admisi.bpjs_id AS bpjs_idadmisi,
            bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
            bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
            asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
            asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
            COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
            fgetnamalookup(pendaftaran_t.keadaan_masuk::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup(pendaftaran_t.transportasi::integer) AS transportasi,
            pendaftaran_t.keterangan_pendaftaran
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
             LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
             LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN kelaspelayanan_m kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN pasienpulang_t pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE bayaruangmuka_t_1.is_deleted = false
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             LEFT JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                    sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                  WHERE pengembalianuangmuka_t_1.is_deleted = false
                  GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id
             LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                    sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                  WHERE pemakaianuangmuka_t_1.is_deleted = false
                  GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
             LEFT JOIN pegawai_m dok_rj_rd ON pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id
             LEFT JOIN pegawai_m dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
             LEFT JOIN bpjs_t bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
             LEFT JOIN bpjs_t bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
             LEFT JOIN asuransipasien_m asuransi_pendaftaran ON pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id
             LEFT JOIN asuransipasien_m asuransi_admisi ON pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id
             LEFT JOIN pemberianpiutang_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                    pt.pendaftaran_id
                   FROM penjualanresep_t pt
                  WHERE pt.status_bayar = 349
                  GROUP BY pt.pendaftaran_id) penjualan_resep ON penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            ruangan_m.instalasi_id,
            penjualanresep_t.ruangan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
            0 AS pasienpulang_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            penjualanresep_t.nama_pembeli AS nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::character varying AS kelaspelayanan_nama,
            penjualanresep_t.tglresep AS tglpasienpulang,
            penjualanresep_t.tglresep AS tglpasienpulang_ri,
            0 AS jumlah_uangmuka,
            0 AS pasienpulangri_id,
            0 AS pasienadmisi_id,
            NULL::character varying AS status_pasien,
            0 AS pasienmasukpenunjang_id,
            pegawai_m.nama_pegawai AS nama_dok_rj_rd,
            pegawai_m.nama_pegawai AS nama_dok_ri,
            NULL::character varying AS jeniskasuspenyakit_nama,
            NULL::character varying AS umur,
            penjualanresep_t.carabayar_id AS carabayarri_id,
            carabayar_m.carabayar_nama AS carabayar_nama_ri,
            penjualanresep_t.penjamin_id AS penjaminri_id,
            penjamin_m.penjamin_nama AS penjamin_nama_ri,
            penjualanresep_t.ruangan_id AS ruanganri_id,
            ruangan_m.instalasi_id AS instalasiri_id,
            ruangan_m.ruangan_nama AS ruangan_nama_ri,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            penjualanresep_t.status_bayar,
            0 AS jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            penjualanresep_t.penjualanresep_id,
            COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, 0::double precision) AS obat,
            COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totalharga_jual,
            NULL::integer AS kelas_bpjspendaftaran,
            NULL::integer AS kelas_bpjsadmisi,
            NULL::integer AS bpjs_idpendaftaran,
            NULL::integer AS bpjs_idadmisi,
            NULL::character varying AS no_bpjspendaftaran,
            NULL::character varying AS no_bpjsadmisi,
            NULL::character varying AS no_asuransipendaftaran,
            NULL::character varying AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
            0 AS total_piutang,
            NULL::integer AS dokterrj_id,
            NULL::integer AS dokterri_id,
            NULL::character varying AS keadaanmasuk_id,
            NULL::character varying AS keadaan_masuk,
            NULL::character varying AS transportasi_id,
            NULL::character varying AS transportasi,
            NULL::text AS keterangan_pendaftaran
           FROM penjualanresep_t
             LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
          WHERE penjualanresep_t.jenispenjualan::text = '343'::text AND penjualanresep_t.is_deleted = false) data_info;");

         $this->execute('ALTER TABLE public.infodatapendaftaran_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infopenjualanresep_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infopenjualanresep_v AS 
 SELECT penjualanresep_t.penjualanresep_id,
    penjualanresep_t.jenispenjualan,
    fgetnamalookup(penjualanresep_t.jenispenjualan::integer) AS jenis_penjualan,
    penjualanresep_t.tglresep,
    penjualanresep_t.noresep,
    penjualanresep_t.totharganetto,
    penjualanresep_t.totalhargajual,
    penjualanresep_t.totaltarifservice,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.biayakonseling,
    penjualanresep_t.pembulatanharga,
    penjualanresep_t.jasadokterresep,
    penjualanresep_t.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    penjualanresep_t.penjamin_id,
    penjualanresep_t.tglpenjualan,
    penjualanresep_t.discount,
    penjualanresep_t.subsidiasuransi,
    penjualanresep_t.subsidipemerintah,
    penjualanresep_t.subsidirs,
    penjualanresep_t.iurbiaya,
    penjualanresep_t.lamapelayanan,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.nama_pembeli,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    penjualanresep_t.pegawai_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    pegawai_m.nama_pegawai,
    penjualanresep_t.ruangan_id,
    penjualanresep_t.karyawan_id,
    karyawan.nama_pegawai AS nama_karyawan,
    reseptur.antrian_id,
    antrian.no_antrian,
    peg_reseptur.nama_pegawai AS pegawai_reseptur,
    penjualanresep_t.catatan,
    fgetstatuspembayaranresep(penjualanresep_t.penjualanresep_id) AS status_pembayaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    penjualanresep_t.iter,
    pasien_m.no_rekam_medik
   FROM penjualanresep_t
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN reseptur_t reseptur ON penjualanresep_t.reseptur_id = reseptur.reseptur_id
     LEFT JOIN antrian_t antrian ON reseptur.antrian_id = antrian.antrian_id
     LEFT JOIN pegawai_m peg_reseptur ON reseptur.pegawai_id = peg_reseptur.pegawai_id
     LEFT JOIN ruangan_m ON reseptur.ruanganreseptur_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;");

         $this->execute('ALTER TABLE public.infopenjualanresep_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.paketpelayananmp_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.paketpelayananmp_v AS 
 SELECT paketpelayanan_mp.tipepaket_id,
        CASE
            WHEN paketpelayanan_mp.daftartindakan_id IS NOT NULL THEN paketpelayanan_mp.daftartindakan_id
            ELSE paketpelayanan_mp.paketdetail_id
        END AS tindakan_paket_id,
        CASE
            WHEN paketpelayanan_mp.daftartindakan_id IS NOT NULL THEN daftartindakan_m.daftartindakan_nama
            ELSE tipepaket_m.tipepaket_nama
        END AS tindakan_paket_nama,
        CASE
            WHEN paketpelayanan_mp.daftartindakan_id IS NOT NULL THEN true
            ELSE false
        END AS is_tindakan,
    paketpelayanan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    parent.is_mcu
   FROM paketpelayanan_mp
     LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON paketpelayanan_mp.paketdetail_id = tipepaket_m.tipepaket_id
     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ruangan_m ON paketpelayanan_mp.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN tipepaket_m parent ON paketpelayanan_mp.tipepaket_id = parent.tipepaket_id
  WHERE paketpelayanan_mp.is_deleted = false;");

         $this->execute('ALTER TABLE public.paketpelayananmp_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.inforiwayatpasien_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.inforiwayatpasien_v AS 
 SELECT 'RJ/RD'::text AS tes,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS pasienadmisi_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.pasienpulang_id,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.ruangan_id AS ruangan_pend_id,
    pend_ruangan.ruangan_nama AS ruangan_pend,
    NULL::integer AS ruangan_adm_id,
    NULL::character varying AS ruangan_adm,
    pendaftaran_t.pegawai_id AS dok_rjrd_id,
    dok_rjrd.nama_pegawai AS dok_rjrd,
    NULL::integer AS dok_ri_id,
    NULL::character varying AS dok_ri,
        CASE
            WHEN anamnesa_t.r_anamesa IS NULL THEN 0
            ELSE 1
        END AS r_anamesa,
        CASE
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL THEN 0
            ELSE 1
        END AS r_pemeriksaanfisik,
        CASE
            WHEN pasienmorbiditas_t.r_diagnosa IS NULL THEN 0
            ELSE 1
        END AS r_diagnosa,
        CASE
            WHEN konsulpoli_t.r_konsulpoli IS NULL THEN 0
            ELSE 1
        END AS r_konsulpoli,
        CASE
            WHEN tindakanpelayanan_t.r_tindakan IS NULL THEN 0
            ELSE 1
        END AS r_tindakan,
        CASE
            WHEN obatalkespasien_t.r_bmhp IS NULL THEN 0
            ELSE 1
        END AS r_bmhp,
        CASE
            WHEN reseptur_t.r_reseptur IS NULL THEN 0
            ELSE 1
        END AS r_reseptur,
        CASE
            WHEN anamnesa_t.r_anamesa IS NULL AND pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL AND pasienmorbiditas_t.r_diagnosa IS NULL AND tindakanpelayanan_t.r_tindakan IS NULL AND obatalkespasien_t.r_bmhp IS NULL AND reseptur_t.r_reseptur IS NULL AND hasilpemeriksaanlab_t.p_laboratorium IS NULL AND hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
        END AS r_resumemedis_rj_rd,
        CASE
            WHEN hasilpemeriksaanlab_t.p_laboratorium IS NULL THEN 0
            ELSE 1
        END AS p_laboratorium,
        CASE
            WHEN hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
        END AS p_radiologi,
        CASE
            WHEN operasi.p_operasi IS NULL THEN 0
            ELSE 1
        END AS p_operasi,
        CASE
            WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
            ELSE 1
        END AS r_asesmenawal,
        CASE
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat IS NULL THEN 0
            ELSE 1
        END AS r_rekonsiliasiobat,
        CASE
            WHEN asesmenmedis_t.r_asesmenmedis IS NULL THEN 0
            ELSE 1
        END AS r_asesmenmedis,
        CASE
            WHEN rencanapulang_t.r_dischargeplan IS NULL THEN 0
            ELSE 1
        END AS r_dischargeplan,
        CASE
            WHEN cppt_t.r_cppt IS NULL THEN 0
            ELSE 1
        END AS r_cppt,
        CASE
            WHEN instruksitindakan_t.r_instruktitindakan IS NULL THEN 0
            ELSE 1
        END AS r_instruktitindakan,
        CASE
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp IS NULL THEN 0
            ELSE 1
        END AS r_instruktitindakanbmhp,
        CASE
            WHEN pemberianobat_t.r_pemberianobat IS NULL THEN 0
            ELSE 1
        END AS r_pemberianobat,
        CASE
            WHEN permintaankonsul_t.r_permintaankonsul IS NULL THEN 0
            ELSE 1
        END AS r_permintaankonsul,
        CASE
            WHEN pindahkamar_t.r_pindahkamar IS NULL THEN 0
            ELSE 1
        END AS r_pindahkamar,
        CASE
            WHEN resumemedisri_t.r_resumemedis_ri IS NULL THEN 0
            ELSE 1
        END AS r_resumemedis_ri,
        CASE
            WHEN visitdokter.r_visitedokter IS NULL THEN 0
            ELSE 1
        END AS r_visitedokter,
        CASE
            WHEN kesimpulanrd_t.r_kesimpulan_rd IS NULL THEN 0
            ELSE 1
        END AS r_kesimpulan_rd,
        CASE
            WHEN asesmenmedisrd_t.r_asesmendokter IS NULL THEN 0
            ELSE 1
        END AS r_asesmendokter,
        CASE
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
            ELSE 1
        END AS r_asesmenperawat_rd,
        CASE
            WHEN asuhangizi_t.r_asuhangizi IS NULL THEN 0
            ELSE 1
        END AS r_asuhangizi,
        CASE
            WHEN soaprj_t.r_soaprj IS NULL THEN 0
            ELSE 1
        END AS r_soaprj,
    pasienpulang_t.tglpasienpulang,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
        CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
        END AS status_rj,
        CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            WHEN asesmenawal_t.r_asesmenawal = 1 THEN 'SELESAI'::text
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat = 1 THEN 'SELESAI'::text
            WHEN asesmenmedis_t.r_asesmenmedis = 1 THEN 'SELESAI'::text
            WHEN rencanapulang_t.r_dischargeplan = 1 THEN 'SELESAI'::text
            WHEN cppt_t.r_cppt = 1 THEN 'SELESAI'::text
            WHEN instruksitindakan_t.r_instruktitindakan = 1 THEN 'SELESAI'::text
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp = 1 THEN 'SELESAI'::text
            WHEN pemberianobat_t.r_pemberianobat = 1 THEN 'SELESAI'::text
            WHEN pindahkamar_t.r_pindahkamar = 1 THEN 'SELESAI'::text
            WHEN permintaankonsul_t.r_permintaankonsul = 1 THEN 'SELESAI'::text
            WHEN resumemedisri_t.r_resumemedis_ri = 1 THEN 'SELESAI'::text
            WHEN visitdokter.r_visitedokter = 1 THEN 'SELESAI'::text
            WHEN kesimpulanrd_t.r_kesimpulan_rd = 1 THEN 'SELESAI'::text
            WHEN asesmenmedisrd_t.r_asesmendokter = 1 THEN 'SELESAI'::text
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd = 1 THEN 'SELESAI'::text
            WHEN asuhangizi_t.r_asuhangizi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
        END AS status_rd_ri,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pendaftaran_t.instalasi_id AS instalasi_pend_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m pend_ruangan ON pendaftaran_t.ruangan_id = pend_ruangan.ruangan_id
     JOIN pegawai_m dok_rjrd ON pendaftaran_t.pegawai_id = dok_rjrd.pegawai_id
     LEFT JOIN ( SELECT anamnesa_t_1.pendaftaran_id,
            count(anamnesa_t_1.pendaftaran_id) AS r_anamesa
           FROM anamnesa_t anamnesa_t_1
          WHERE anamnesa_t_1.is_deleted = false
          GROUP BY anamnesa_t_1.pendaftaran_id) anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pemeriksaanfisik_t_1.pendaftaran_id,
            count(pemeriksaanfisik_t_1.pendaftaran_id) AS r_pemeriksaanfisik
           FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
          WHERE pemeriksaanfisik_t_1.is_deleted = false
          GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
            count(pasienmorbiditas_t_1.pendaftaran_id) AS r_diagnosa
           FROM pasienmorbiditas_t pasienmorbiditas_t_1
          WHERE pasienmorbiditas_t_1.is_deleted = false
          GROUP BY pasienmorbiditas_t_1.pendaftaran_id) pasienmorbiditas_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT konsulpoli_t_1.pendaftaran_id,
            count(konsulpoli_t_1.pendaftaran_id) AS r_konsulpoli
           FROM konsulpoli_t konsulpoli_t_1
          WHERE konsulpoli_t_1.is_deleted = false
          GROUP BY konsulpoli_t_1.pendaftaran_id) konsulpoli_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(tindakanpelayanan_t_1.pendaftaran_id) AS r_tindakan
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
                   FROM daftartindakan_m
                  WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t_1.daftartindakan_id
          GROUP BY tindakanpelayanan_t_1.pendaftaran_id) tindakanpelayanan_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            count(obatalkespasien_t_1.pendaftaran_id) AS r_bmhp
           FROM obatalkespasien_t obatalkespasien_t_1
          GROUP BY obatalkespasien_t_1.pendaftaran_id) obatalkespasien_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT reseptur_t_1.pendaftaran_id,
            count(reseptur_t_1.pendaftaran_id) AS r_reseptur
           FROM reseptur_t reseptur_t_1
          WHERE reseptur_t_1.is_deleted = false
          GROUP BY reseptur_t_1.pendaftaran_id) reseptur_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT resumemedis_t_1.pendaftaran_id,
            count(resumemedis_t_1.pendaftaran_id) AS r_resumemedis_rj_rd
           FROM resumemedis_t resumemedis_t_1
          WHERE resumemedis_t_1.is_deleted = false
          GROUP BY resumemedis_t_1.pendaftaran_id) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t_1.pendaftaran_id,
            count(hasilpemeriksaanlab_t_1.pendaftaran_id) AS p_laboratorium
           FROM hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1
          WHERE hasilpemeriksaanlab_t_1.is_deleted = false
          GROUP BY hasilpemeriksaanlab_t_1.pendaftaran_id) hasilpemeriksaanlab_t ON hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT hasilpemeriksaanrad_t_1.pendaftaran_id,
            count(hasilpemeriksaanrad_t_1.pendaftaran_id) AS p_radiologi
           FROM hasilpemeriksaanrad_t hasilpemeriksaanrad_t_1
          WHERE hasilpemeriksaanrad_t_1.is_deleted = false
          GROUP BY hasilpemeriksaanrad_t_1.pendaftaran_id) hasilpemeriksaanrad_t ON hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            count(*) AS p_operasi
           FROM inpostoperasi_t
             JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
          GROUP BY pasienmasukpenunjang_t.pendaftaran_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id
     LEFT JOIN ( SELECT asesmenawal_t_1.pendaftaran_id,
            count(asesmenawal_t_1.pendaftaran_id) AS r_asesmenawal
           FROM asesmenawal_t asesmenawal_t_1
          WHERE asesmenawal_t_1.is_deleted = false
          GROUP BY asesmenawal_t_1.pendaftaran_id) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
     LEFT JOIN ( SELECT rekonsiliasiobat_t_1.pendaftaran_id,
            count(rekonsiliasiobat_t_1.pendaftaran_id) AS r_rekonsiliasiobat
           FROM rekonsiliasiobat_t rekonsiliasiobat_t_1
          WHERE rekonsiliasiobat_t_1.is_deleted = false
          GROUP BY rekonsiliasiobat_t_1.pendaftaran_id) rekonsiliasiobat_t ON pendaftaran_t.pendaftaran_id = rekonsiliasiobat_t.pendaftaran_id
     LEFT JOIN ( SELECT asesmenmedis_t_1.pendaftaran_id,
            count(asesmenmedis_t_1.pendaftaran_id) AS r_asesmenmedis
           FROM asesmenmedis_t asesmenmedis_t_1
          WHERE asesmenmedis_t_1.is_deleted = false
          GROUP BY asesmenmedis_t_1.pendaftaran_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
     LEFT JOIN ( SELECT rencanapulang_t_1.pendaftaran_id,
            count(rencanapulang_t_1.pendaftaran_id) AS r_dischargeplan
           FROM rencanapulang_t rencanapulang_t_1
          WHERE rencanapulang_t_1.is_deleted = false
          GROUP BY rencanapulang_t_1.pendaftaran_id) rencanapulang_t ON pendaftaran_t.pendaftaran_id = rencanapulang_t.pendaftaran_id
     LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
            count(cppt_t_1.pendaftaran_id) AS r_cppt
           FROM cppt_t cppt_t_1
          WHERE cppt_t_1.is_deleted = false
          GROUP BY cppt_t_1.pendaftaran_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
     LEFT JOIN ( SELECT instruksitindakan_t_1.pendaftaran_id,
            count(instruksitindakan_t_1.pendaftaran_id) AS r_instruktitindakan
           FROM instruksitindakan_t instruksitindakan_t_1
          WHERE instruksitindakan_t_1.is_deleted = false
          GROUP BY instruksitindakan_t_1.pendaftaran_id) instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
     LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.pendaftaran_id,
            count(instruksitindakanbmhp_t_1.pendaftaran_id) AS r_instruktitindakanbmhp
           FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1
          WHERE instruksitindakanbmhp_t_1.is_deleted = false
          GROUP BY instruksitindakanbmhp_t_1.pendaftaran_id) instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
     LEFT JOIN ( SELECT pemberianobat_t_1.pendaftaran_id,
            count(pemberianobat_t_1.pendaftaran_id) AS r_pemberianobat
           FROM pemberianobat_t pemberianobat_t_1
          WHERE pemberianobat_t_1.is_deleted = false
          GROUP BY pemberianobat_t_1.pendaftaran_id) pemberianobat_t ON pendaftaran_t.pendaftaran_id = pemberianobat_t.pendaftaran_id
     LEFT JOIN ( SELECT permintaankonsul_t_1.pendaftaran_id,
            count(permintaankonsul_t_1.pendaftaran_id) AS r_permintaankonsul
           FROM permintaankonsul_t permintaankonsul_t_1
          WHERE permintaankonsul_t_1.is_deleted = false
          GROUP BY permintaankonsul_t_1.pendaftaran_id) permintaankonsul_t ON pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id
     LEFT JOIN ( SELECT pindahkamar_t_1.pendaftaran_id,
            count(pindahkamar_t_1.pendaftaran_id) AS r_pindahkamar
           FROM pindahkamar_t pindahkamar_t_1
          WHERE pindahkamar_t_1.is_deleted = false
          GROUP BY pindahkamar_t_1.pendaftaran_id) pindahkamar_t ON pendaftaran_t.pendaftaran_id = pindahkamar_t.pendaftaran_id
     LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
            count(resumemedisri_t_1.pendaftaran_id) AS r_resumemedis_ri
           FROM resumemedisri_t resumemedisri_t_1
          WHERE resumemedisri_t_1.is_deleted = false
          GROUP BY resumemedisri_t_1.pendaftaran_id) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(*) AS r_visitedokter
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN cppt_t cppt_t_1 ON tindakanpelayanan_t_1.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE
          GROUP BY tindakanpelayanan_t_1.pendaftaran_id) visitdokter ON pendaftaran_t.pendaftaran_id = visitdokter.pendaftaran_id
     LEFT JOIN ( SELECT kesimpulanrd_t_1.pendaftaran_id,
            count(kesimpulanrd_t_1.pendaftaran_id) AS r_kesimpulan_rd
           FROM kesimpulanrd_t kesimpulanrd_t_1
          WHERE kesimpulanrd_t_1.is_deleted = false
          GROUP BY kesimpulanrd_t_1.pendaftaran_id) kesimpulanrd_t ON pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
     LEFT JOIN ( SELECT asesmenmedisrd_t_1.pendaftaran_id,
            count(asesmenmedisrd_t_1.pendaftaran_id) AS r_asesmendokter
           FROM asesmenmedisrd_t asesmenmedisrd_t_1
          WHERE asesmenmedisrd_t_1.is_deleted = false
          GROUP BY asesmenmedisrd_t_1.pendaftaran_id) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
     LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
            count(asesmenperawatrd_t_1.pendaftaran_id) AS r_asesmenperawat_rd
           FROM asesmenperawatrd_t asesmenperawatrd_t_1
          WHERE asesmenperawatrd_t_1.is_deleted = false
          GROUP BY asesmenperawatrd_t_1.pendaftaran_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
     LEFT JOIN ( SELECT asuhangizi_t_1.pendaftaran_id,
            count(asuhangizi_t_1.pendaftaran_id) AS r_asuhangizi
           FROM asuhangizi_t asuhangizi_t_1
          WHERE asuhangizi_t_1.is_deleted = false
          GROUP BY asuhangizi_t_1.pendaftaran_id) asuhangizi_t ON pendaftaran_t.pendaftaran_id = asuhangizi_t.pendaftaran_id
     LEFT JOIN ( SELECT soaprj_t_1.pendaftaran_id,
            count(soaprj_t_1.pendaftaran_id) AS r_soaprj
           FROM soaprj_t soaprj_t_1
          WHERE soaprj_t_1.is_deleted = false
          GROUP BY soaprj_t_1.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
  WHERE pendaftaran_t.instalasi_id <> 3
UNION ALL
 SELECT 'RI'::text AS tes,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.pasien_id,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
        CASE pendaftaran_t.instalasi_id
            WHEN 12 THEN adm_ruangan.ruangan_id
            ELSE pendaftaran_t.ruangan_id
        END AS ruangan_pend_id,
        CASE pendaftaran_t.instalasi_id
            WHEN 12 THEN adm_ruangan.ruangan_nama
            ELSE ruangan_m.ruangan_nama
        END AS ruangan_pend,
    pasienadmisi_t.ruangan_id AS ruangan_adm_id,
    adm_ruangan.ruangan_nama AS ruangan_adm,
    pendaftaran_t.pegawai_id AS dok_rjrd_id,
    pegawai_m.nama_pegawai AS dok_rjrd,
    pasienadmisi_t.pegawai_id AS dok_ri_id,
    dok_ri.nama_pegawai AS dok_ri,
        CASE
            WHEN anamnesa_t.r_anamesa IS NULL THEN 0
            ELSE 1
        END AS r_anamesa,
        CASE
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL THEN 0
            ELSE 1
        END AS r_pemeriksaanfisik,
        CASE
            WHEN pasienmorbiditas_t.r_diagnosa IS NULL THEN 0
            ELSE 1
        END AS r_diagnosa,
        CASE
            WHEN konsulpoli_t.r_konsulpoli IS NULL THEN 0
            ELSE 1
        END AS r_konsulpoli,
        CASE
            WHEN tindakanpelayanan_t.r_tindakan IS NULL THEN 0
            ELSE 1
        END AS r_tindakan,
        CASE
            WHEN obatalkespasien_t.r_bmhp IS NULL THEN 0
            ELSE 1
        END AS r_bmhp,
        CASE
            WHEN reseptur_t.r_reseptur IS NULL THEN 0
            ELSE 1
        END AS r_reseptur,
        CASE
            WHEN anamnesa_t.r_anamesa IS NULL AND pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL AND pasienmorbiditas_t.r_diagnosa IS NULL AND tindakanpelayanan_t.r_tindakan IS NULL AND obatalkespasien_t.r_bmhp IS NULL AND reseptur_t.r_reseptur IS NULL AND hasilpemeriksaanlab_t.p_laboratorium IS NULL AND hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
        END AS r_resumemedis_rj_rd,
        CASE
            WHEN hasilpemeriksaanlab_t.p_laboratorium IS NULL THEN 0
            ELSE 1
        END AS p_laboratorium,
        CASE
            WHEN hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
        END AS p_radiologi,
        CASE
            WHEN operasi.p_operasi IS NULL THEN 0
            ELSE 1
        END AS p_operasi,
        CASE
            WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
            ELSE 1
        END AS r_asesmenawal,
        CASE
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat IS NULL THEN 0
            ELSE 1
        END AS r_rekonsiliasiobat,
        CASE
            WHEN asesmenmedis_t.r_asesmenmedis IS NULL THEN 0
            ELSE 1
        END AS r_asesmenmedis,
        CASE
            WHEN rencanapulang_t.r_dischargeplan IS NULL THEN 0
            ELSE 1
        END AS r_dischargeplan,
        CASE
            WHEN cppt_t.r_cppt IS NULL THEN 0
            ELSE 1
        END AS r_cppt,
        CASE
            WHEN instruksitindakan_t.r_instruktitindakan IS NULL THEN 0
            ELSE 1
        END AS r_instruktitindakan,
        CASE
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp IS NULL THEN 0
            ELSE 1
        END AS r_instruktitindakanbmhp,
        CASE
            WHEN pemberianobat_t.r_pemberianobat IS NULL THEN 0
            ELSE 1
        END AS r_pemberianobat,
        CASE
            WHEN permintaankonsul_t.r_permintaankonsul IS NULL THEN 0
            ELSE 1
        END AS r_permintaankonsul,
        CASE
            WHEN pindahkamar_t.r_pindahkamar IS NULL THEN 0
            ELSE 1
        END AS r_pindahkamar,
        CASE
            WHEN resumemedisri_t.r_resumemedis_ri IS NULL THEN 0
            ELSE 1
        END AS r_resumemedis_ri,
        CASE
            WHEN visitdokter.r_visitedokter IS NULL THEN 0
            ELSE 1
        END AS r_visitedokter,
        CASE
            WHEN kesimpulanrd_t.r_kesimpulan_rd IS NULL THEN 0
            ELSE 1
        END AS r_kesimpulan_rd,
        CASE
            WHEN asesmenmedisrd_t.r_asesmendokter IS NULL THEN 0
            ELSE 1
        END AS r_asesmendokter,
        CASE
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
            ELSE 1
        END AS r_asesmenperawat_rd,
        CASE
            WHEN asuhangizi_t.r_asuhangizi IS NULL THEN 0
            ELSE 1
        END AS r_asuhangizi,
        CASE
            WHEN soaprj_t.r_soaprj IS NULL THEN 0
            ELSE 1
        END AS r_soaprj,
    pasienpulang_t.tglpasienpulang,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
        CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
        END AS status_rj,
        CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            WHEN asesmenawal_t.r_asesmenawal = 1 THEN 'SELESAI'::text
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat = 1 THEN 'SELESAI'::text
            WHEN asesmenmedis_t.r_asesmenmedis = 1 THEN 'SELESAI'::text
            WHEN rencanapulang_t.r_dischargeplan = 1 THEN 'SELESAI'::text
            WHEN cppt_t.r_cppt = 1 THEN 'SELESAI'::text
            WHEN instruksitindakan_t.r_instruktitindakan = 1 THEN 'SELESAI'::text
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp = 1 THEN 'SELESAI'::text
            WHEN pemberianobat_t.r_pemberianobat = 1 THEN 'SELESAI'::text
            WHEN pindahkamar_t.r_pindahkamar = 1 THEN 'SELESAI'::text
            WHEN permintaankonsul_t.r_permintaankonsul = 1 THEN 'SELESAI'::text
            WHEN resumemedisri_t.r_resumemedis_ri = 1 THEN 'SELESAI'::text
            WHEN visitdokter.r_visitedokter = 1 THEN 'SELESAI'::text
            WHEN kesimpulanrd_t.r_kesimpulan_rd = 1 THEN 'SELESAI'::text
            WHEN asesmenmedisrd_t.r_asesmendokter = 1 THEN 'SELESAI'::text
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd = 1 THEN 'SELESAI'::text
            WHEN asuhangizi_t.r_asuhangizi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
        END AS status_rd_ri,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
        CASE pendaftaran_t.instalasi_id
            WHEN 12 THEN adm_ruangan.instalasi_id
            ELSE pendaftaran_t.instalasi_id
        END AS instalasi_pend_id
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ruangan_m adm_ruangan ON pasienadmisi_t.ruangan_id = adm_ruangan.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN pegawai_m dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
     LEFT JOIN ( SELECT anamnesa_t_1.pendaftaran_id,
            count(anamnesa_t_1.pendaftaran_id) AS r_anamesa
           FROM anamnesa_t anamnesa_t_1
          WHERE anamnesa_t_1.is_deleted = false
          GROUP BY anamnesa_t_1.pendaftaran_id) anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pemeriksaanfisik_t_1.pendaftaran_id,
            count(pemeriksaanfisik_t_1.pendaftaran_id) AS r_pemeriksaanfisik
           FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
          WHERE pemeriksaanfisik_t_1.is_deleted = false
          GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
            count(pasienmorbiditas_t_1.pendaftaran_id) AS r_diagnosa
           FROM pasienmorbiditas_t pasienmorbiditas_t_1
          WHERE pasienmorbiditas_t_1.is_deleted = false
          GROUP BY pasienmorbiditas_t_1.pendaftaran_id) pasienmorbiditas_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT konsulpoli_t_1.pendaftaran_id,
            count(konsulpoli_t_1.pendaftaran_id) AS r_konsulpoli
           FROM konsulpoli_t konsulpoli_t_1
          WHERE konsulpoli_t_1.is_deleted = false
          GROUP BY konsulpoli_t_1.pendaftaran_id) konsulpoli_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(tindakanpelayanan_t_1.pendaftaran_id) AS r_tindakan
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
                   FROM daftartindakan_m
                  WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t_1.daftartindakan_id
          GROUP BY tindakanpelayanan_t_1.pendaftaran_id) tindakanpelayanan_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            count(obatalkespasien_t_1.pendaftaran_id) AS r_bmhp
           FROM obatalkespasien_t obatalkespasien_t_1
          GROUP BY obatalkespasien_t_1.pendaftaran_id) obatalkespasien_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT reseptur_t_1.pendaftaran_id,
            count(reseptur_t_1.pendaftaran_id) AS r_reseptur
           FROM reseptur_t reseptur_t_1
          WHERE reseptur_t_1.is_deleted = false
          GROUP BY reseptur_t_1.pendaftaran_id) reseptur_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT resumemedis_t_1.pendaftaran_id,
            count(resumemedis_t_1.pendaftaran_id) AS r_resumemedis_rj_rd
           FROM resumemedis_t resumemedis_t_1
          WHERE resumemedis_t_1.is_deleted = false
          GROUP BY resumemedis_t_1.pendaftaran_id) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t_1.pendaftaran_id,
            count(hasilpemeriksaanlab_t_1.pendaftaran_id) AS p_laboratorium
           FROM hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1
          WHERE hasilpemeriksaanlab_t_1.is_deleted = false
          GROUP BY hasilpemeriksaanlab_t_1.pendaftaran_id) hasilpemeriksaanlab_t ON hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT hasilpemeriksaanrad_t_1.pendaftaran_id,
            count(hasilpemeriksaanrad_t_1.pendaftaran_id) AS p_radiologi
           FROM hasilpemeriksaanrad_t hasilpemeriksaanrad_t_1
          WHERE hasilpemeriksaanrad_t_1.is_deleted = false
          GROUP BY hasilpemeriksaanrad_t_1.pendaftaran_id) hasilpemeriksaanrad_t ON hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            count(*) AS p_operasi
           FROM inpostoperasi_t
             JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
          GROUP BY pasienmasukpenunjang_t.pendaftaran_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id
     LEFT JOIN ( SELECT asesmenawal_t_1.pendaftaran_id,
            count(asesmenawal_t_1.pendaftaran_id) AS r_asesmenawal
           FROM asesmenawal_t asesmenawal_t_1
          WHERE asesmenawal_t_1.is_deleted = false
          GROUP BY asesmenawal_t_1.pendaftaran_id) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
     LEFT JOIN ( SELECT rekonsiliasiobat_t_1.pendaftaran_id,
            count(rekonsiliasiobat_t_1.pendaftaran_id) AS r_rekonsiliasiobat
           FROM rekonsiliasiobat_t rekonsiliasiobat_t_1
          WHERE rekonsiliasiobat_t_1.is_deleted = false
          GROUP BY rekonsiliasiobat_t_1.pendaftaran_id) rekonsiliasiobat_t ON pendaftaran_t.pendaftaran_id = rekonsiliasiobat_t.pendaftaran_id
     LEFT JOIN ( SELECT asesmenmedis_t_1.pendaftaran_id,
            count(asesmenmedis_t_1.pendaftaran_id) AS r_asesmenmedis
           FROM asesmenmedis_t asesmenmedis_t_1
          WHERE asesmenmedis_t_1.is_deleted = false
          GROUP BY asesmenmedis_t_1.pendaftaran_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
     LEFT JOIN ( SELECT rencanapulang_t_1.pendaftaran_id,
            count(rencanapulang_t_1.pendaftaran_id) AS r_dischargeplan
           FROM rencanapulang_t rencanapulang_t_1
          WHERE rencanapulang_t_1.is_deleted = false
          GROUP BY rencanapulang_t_1.pendaftaran_id) rencanapulang_t ON pendaftaran_t.pendaftaran_id = rencanapulang_t.pendaftaran_id
     LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
            count(cppt_t_1.pendaftaran_id) AS r_cppt
           FROM cppt_t cppt_t_1
          WHERE cppt_t_1.is_deleted = false
          GROUP BY cppt_t_1.pendaftaran_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
     LEFT JOIN ( SELECT instruksitindakan_t_1.pendaftaran_id,
            count(instruksitindakan_t_1.pendaftaran_id) AS r_instruktitindakan
           FROM instruksitindakan_t instruksitindakan_t_1
          WHERE instruksitindakan_t_1.is_deleted = false
          GROUP BY instruksitindakan_t_1.pendaftaran_id) instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
     LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.pendaftaran_id,
            count(instruksitindakanbmhp_t_1.pendaftaran_id) AS r_instruktitindakanbmhp
           FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1
          WHERE instruksitindakanbmhp_t_1.is_deleted = false
          GROUP BY instruksitindakanbmhp_t_1.pendaftaran_id) instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
     LEFT JOIN ( SELECT pemberianobat_t_1.pendaftaran_id,
            count(pemberianobat_t_1.pendaftaran_id) AS r_pemberianobat
           FROM pemberianobat_t pemberianobat_t_1
          WHERE pemberianobat_t_1.is_deleted = false
          GROUP BY pemberianobat_t_1.pendaftaran_id) pemberianobat_t ON pendaftaran_t.pendaftaran_id = pemberianobat_t.pendaftaran_id
     LEFT JOIN ( SELECT permintaankonsul_t_1.pendaftaran_id,
            count(permintaankonsul_t_1.pendaftaran_id) AS r_permintaankonsul
           FROM permintaankonsul_t permintaankonsul_t_1
          WHERE permintaankonsul_t_1.is_deleted = false
          GROUP BY permintaankonsul_t_1.pendaftaran_id) permintaankonsul_t ON pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id
     LEFT JOIN ( SELECT pindahkamar_t_1.pendaftaran_id,
            count(pindahkamar_t_1.pendaftaran_id) AS r_pindahkamar
           FROM pindahkamar_t pindahkamar_t_1
          WHERE pindahkamar_t_1.is_deleted = false
          GROUP BY pindahkamar_t_1.pendaftaran_id) pindahkamar_t ON pendaftaran_t.pendaftaran_id = pindahkamar_t.pendaftaran_id
     LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
            count(resumemedisri_t_1.pendaftaran_id) AS r_resumemedis_ri
           FROM resumemedisri_t resumemedisri_t_1
          WHERE resumemedisri_t_1.is_deleted = false
          GROUP BY resumemedisri_t_1.pendaftaran_id) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(*) AS r_visitedokter
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN cppt_t cppt_t_1 ON tindakanpelayanan_t_1.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE
          GROUP BY tindakanpelayanan_t_1.pendaftaran_id) visitdokter ON pendaftaran_t.pendaftaran_id = visitdokter.pendaftaran_id
     LEFT JOIN ( SELECT kesimpulanrd_t_1.pendaftaran_id,
            count(kesimpulanrd_t_1.pendaftaran_id) AS r_kesimpulan_rd
           FROM kesimpulanrd_t kesimpulanrd_t_1
          WHERE kesimpulanrd_t_1.is_deleted = false
          GROUP BY kesimpulanrd_t_1.pendaftaran_id) kesimpulanrd_t ON pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
     LEFT JOIN ( SELECT asesmenmedisrd_t_1.pendaftaran_id,
            count(asesmenmedisrd_t_1.pendaftaran_id) AS r_asesmendokter
           FROM asesmenmedisrd_t asesmenmedisrd_t_1
          WHERE asesmenmedisrd_t_1.is_deleted = false
          GROUP BY asesmenmedisrd_t_1.pendaftaran_id) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
     LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
            count(asesmenperawatrd_t_1.pendaftaran_id) AS r_asesmenperawat_rd
           FROM asesmenperawatrd_t asesmenperawatrd_t_1
          WHERE asesmenperawatrd_t_1.is_deleted = false
          GROUP BY asesmenperawatrd_t_1.pendaftaran_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
     LEFT JOIN ( SELECT asuhangizi_t_1.pendaftaran_id,
            count(asuhangizi_t_1.pendaftaran_id) AS r_asuhangizi
           FROM asuhangizi_t asuhangizi_t_1
          WHERE asuhangizi_t_1.is_deleted = false
          GROUP BY asuhangizi_t_1.pendaftaran_id) asuhangizi_t ON pendaftaran_t.pendaftaran_id = asuhangizi_t.pendaftaran_id
     LEFT JOIN ( SELECT soaprj_t_1.pendaftaran_id,
            count(soaprj_t_1.pendaftaran_id) AS r_soaprj
           FROM soaprj_t soaprj_t_1
          WHERE soaprj_t_1.is_deleted = false
          GROUP BY soaprj_t_1.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id;");

         $this->execute('ALTER TABLE public.inforiwayatpasien_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.closing_kasir_view;');

         $this->execute("
            CREATE OR REPLACE VIEW public.closing_kasir_view AS 
 SELECT agr_bukti_bayar.tandabuktibayar_id,
    agr_bukti_bayar.ruangan_id,
    agr_bukti_bayar.bayaruangmuka_id,
    agr_bukti_bayar.closingkasir_id,
    agr_bukti_bayar.pembayaranpelayanan_id,
    agr_bukti_bayar.shift_id,
    agr_bukti_bayar.nourutkasir,
    agr_bukti_bayar.nobuktibayar,
    agr_bukti_bayar.tglbuktibayar,
    agr_bukti_bayar.uangditerima,
    agr_bukti_bayar.pendaftaran_id,
        CASE
            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    agr_bukti_bayar.pegawai1_id,
    agr_bukti_bayar.no_pembayaran,
    pasien_m.no_rekam_medik,
    agr_bukti_bayar.carabayar_id,
    agr_bukti_bayar.carabayar_nama,
    agr_bukti_bayar.penjamin_id,
    agr_bukti_bayar.penjamin_nama,
    agr_bukti_bayar.jmlpembayaran,
        CASE
            WHEN agr_bukti_bayar.total_tunai IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tunai
        END AS pembayaran_tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS pembayaran_nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS pembayaran_penjamin
   FROM ( SELECT tandabuktibayar_t.tandabuktibayar_id,
            tandabuktibayar_t.ruangan_id,
            tandabuktibayar_t.bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            tandabuktibayar_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            tandabuktibayar_t.nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            tandabuktibayar_t.pegawai1_id,
            pembayaranpelayanan_t.no_pembayaran,
                CASE
                    WHEN tandabuktibayar_t.bayaruangmuka_id IS NOT NULL THEN bayaruangmuka_t.pendaftaran_id
                    WHEN tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL THEN pembayaranpelayanan_t.pendaftaran_id
                    ELSE NULL::integer
                END AS pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            tandabuktibayar_t.jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaran_penjamin.total_penjamin,
            pembayaran_penjamin.total_nontunai,
                CASE
                    WHEN pembayaran_penjamin.total_tunai < 0::double precision THEN 0::double precision
                    ELSE pembayaran_penjamin.total_tunai
                END AS total_tunai
           FROM tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
             LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN ( SELECT COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    total_pembayaran.total_nontunai,
                    COALESCE(pembayaran_t.total_dibayar, 0::double precision) - COALESCE(total_pembayaran.total_nontunai, 0::double precision) AS total_tunai
                   FROM pembayaran_t
                     LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                     LEFT JOIN ( SELECT sum(pembayaranmetode_t.total_dibayar) AS total_nontunai,
                            pembayaranmetode_t.pembayaran_id
                           FROM pembayaranmetode_t
                          WHERE pembayaranmetode_t.is_deleted = false
                          GROUP BY pembayaranmetode_t.pembayaran_id) total_pembayaran ON total_pembayaran.pembayaran_id = pembayaran_t.pembayaran_id
                  WHERE pembayaran_t.is_deleted = false) pembayaran_penjamin ON pembayaran_penjamin.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tandabuktibayar_t.is_deleted = false) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;");

         $this->execute('ALTER TABLE public.closing_kasir_view
  OWNER TO postgres;');    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_064852_migrate_20200214 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_064852_migrate_20200214 cannot be reverted.\n";

        return false;
    }
    */
}
