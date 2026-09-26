<?php

use yii\db\Migration;

/**
 * Class m200226_062354_migrate_20200226
 */
class m200226_062354_migrate_20200226 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
  WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true AND tariftindakan_m.tarifparent_id IS NULL
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
  WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true AND tariftindakan_m.tarifparent_id IS NOT NULL;
");
    	 
    	 $this->execute('ALTER TABLE public.infotarifrs_v
    OWNER TO postgres;');

    	 $this->execute('DROP VIEW if exists public.komponentindakan_v;');

    	 $this->execute("
    	 	CREATE OR REPLACE VIEW public.komponentindakan_v AS
 SELECT gabung.pendaftaran_id,
    gabung.pasienadmisi_id,
    gabung.no_pendaftaran,
    gabung.tindakanpelayanan_id,
    gabung.tgl_tindakan,
    gabung.dokterpenanggungjawab_id,
    gabung.nama_pegawai,
    gabung.pasien_id,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.daftartindakan_id,
    gabung.daftartindakan_nama,
    gabung.komponentarif_id,
    gabung.komponentarif_nama,
    gabung.tarif_tindakankomp,
    gabung.discount_tindakan,
    gabung.discount_komponen
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tgl_tindakan,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakankomponen_t.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            tindakankomponen_t.tarif_tindakankomp,
            tindakanpelayanan_t.discount_tindakan,
            tindakankomponen_t.discount_komponen
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id AND tindakankomponen_t.is_deleted = false
             JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
             LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE pendaftaran_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tgl_tindakan,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tindakankomponen_t.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            tindakankomponen_t.tarif_tindakankomp,
            tindakanpelayanan_t.discount_tindakan,
            tindakankomponen_t.discount_komponen
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id AND tindakankomponen_t.is_deleted = false
             JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
             LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE pendaftaran_t.is_deleted = false) gabung;");

    	 $this->execute('ALTER TABLE public.komponentindakan_v
    OWNER TO postgres;');

    	 $this->execute('DROP VIEW if exists public.mastertariftindakan_v;');

    	 $this->execute("
    	 	CREATE OR REPLACE VIEW public.mastertariftindakan_v AS
 SELECT 'TINDAKAN'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
    daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
  WHERE tariftindakan_m.is_deleted = false
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.tipepaket_id AS tindakan_paket_id,
    tipepaket_m.tipepaket_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    tipepaket_m.tipepaket_kode AS daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama
   FROM tariftindakan_m
     JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
  WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.tarifparent_id IS NULL
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
    daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    kelompoktindakan_m.kelompoktindakan_nama AS kelompok_nama,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN kelompoktindakan_m ON kelompoktindakan_m.kelompoktindakan_id = daftartindakan_m.kelompoktindakan_id
     JOIN tipepaket_m ON tipepaket_m.tipepaket_id = tariftindakan_m.tipepaket_id
     JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false AND daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = paketpelayanan_mp.ruangan_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
  WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.tarifparent_id IS NOT NULL;");

    	 $this->execute('ALTER TABLE public.mastertariftindakan_v
    OWNER TO postgres;');

    	 $this->execute('DROP VIEW if exists public.tandabuktibayar_v;');

    	 $this->execute("
    	 	CREATE OR REPLACE VIEW public.tandabuktibayar_v AS
 SELECT tandabuktibayar_t.tandabuktibayar_id,
    tandabuktibayar_t.nobuktibayar,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    tandabuktibayar_t.tglbuktibayar,
    COALESCE(retur.sisa_pembayaran, tandabuktibayar_t.uangditerima) AS jmlpembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    carabayar_m.carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.instalasi_nama
            ELSE pulang_ri.instalasi_nama
        END AS instalasi_akhir,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.ruangan_nama
            ELSE pulang_ri.ruangan_nama
        END AS ruangan_akhir,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.tanggal_lahir AS tgl_lahir,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
            ELSE pendaftaran_t.tgl_stopakomodasi
        END AS tgl_keluar,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_rj.klsrawat
            ELSE bpjs_ri.klsrawat
        END AS hak_kelas,
        CASE
            WHEN retur.tandabuktibayar_id IS NULL THEN false
            ELSE true
        END AS is_returbayarpelayanan
   FROM tandabuktibayar_t
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN bpjs_t bpjs_rj ON pendaftaran_t.bpjs_id = bpjs_rj.bpjs_id
     LEFT JOIN bpjs_t bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
     LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM pasienpulang_t
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
     LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM pasienpulang_t
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
     LEFT JOIN ( SELECT returbayarpelayanan_t.tandabuktibayar_id,
            sum(returbayarpelayanan_t.total_biayaretur) AS total_retur,
            tandabuktibayar_t_1.uangditerima - sum(returbayarpelayanan_t.total_biayaretur) AS sisa_pembayaran
           FROM returbayarpelayanan_t
             JOIN tandabuktibayar_t tandabuktibayar_t_1 ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t_1.tandabuktibayar_id
          WHERE returbayarpelayanan_t.is_deleted = false
          GROUP BY returbayarpelayanan_t.tandabuktibayar_id, tandabuktibayar_t_1.uangditerima) retur ON retur.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
UNION ALL
 SELECT tandabuktibayar_t.tandabuktibayar_id,
    tandabuktibayar_t.nobuktibayar,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    tandabuktibayar_t.tglbuktibayar,
    tandabuktibayar_t.jmlpembayaran,
    NULL::character varying AS no_pembayaran,
    carabayar_m.carabayar_id,
    NULL::character varying AS instalasi_akhir,
    NULL::character varying AS ruangan_akhir,
    NULL::timestamp without time zone AS tgl_pendaftaran,
    NULL::date AS tgl_lahir,
    NULL::timestamp without time zone AS tgl_keluar,
    NULL::integer AS hak_kelas,
    NULL::boolean AS is_returbayarpelayanan
   FROM tandabuktibayar_t
     JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id;
");

    	 $this->execute('ALTER TABLE public.tandabuktibayar_v
    OWNER TO postgres;');

    	 $this->execute('DROP VIEW if exists public.infopasiensudahbayar_v;');

    	 $this->execute("
    	 	CREATE OR REPLACE VIEW public.infopasiensudahbayar_v AS
 SELECT pendaftaran.pendaftaran_id,
    pendaftaran.pembayaranpelayanan_id,
    pendaftaran.tgl_pembayaran,
    pendaftaran.no_pembayaran,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_id
            ELSE ruang_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_nama
            ELSE ruang_ri.ruangan_nama
        END AS ruangan_nama,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    pendaftaran.no_rekam_medik,
        CASE
            WHEN pendaftaran.nama_pasien IS NULL THEN pendaftaran.nama_pembeli
            ELSE pendaftaran.nama_pasien
        END AS nama_pasien,
    pendaftaran.tanggal_lahir,
    pendaftaran.umur,
    pendaftaran.jeniskelamin,
    pendaftaran.penjamin_id,
    pendaftaran.penjamin_nama,
    pendaftaran.carabayar_id,
    pendaftaran.carabayar_nama,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_id
            ELSE ruang_ri.instalasi_id
        END AS instalasi_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
    pendaftaran.status_bayar,
    pendaftaran.closingkasir_id,
    pendaftaran.total_tagihan::integer AS total_tagihan,
    pendaftaran.total_uang_muka::integer AS total_uang_muka,
    pendaftaran.subsidi_asuransi::integer AS subsidi_asuransi,
    pendaftaran.total_sudah_dibayarkan::integer AS total_sudah_dibayarkan,
    pendaftaran.total_sisa_tagihan::integer AS total_sisa_tagihan,
    pendaftaran.biaya_administrasi::integer AS biaya_administrasi,
    pendaftaran.pembulatan,
    pendaftaran.jeniskasuspenyakit_nama,
    pendaftaran.pegawai_rd_rj,
    pendaftaran.kelaspelayanan_id,
    pendaftaran.kelaspelayanan_nama,
    pendaftaran.penjualanresep_id,
    pendaftaran.pembayaran_id,
    pendaftaran.total_ditagihkan
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_pendaftaran.kelaspelayanan_id
                    ELSE kelas_admisi.kelaspelayanan_id
                END AS kelaspelayanan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_pendaftaran.kelaspelayanan_nama
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            NULL::integer AS penjualanresep_id,
            NULL::character varying AS nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan
           FROM pembayaranpelayanan_t
             JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t pasienadmisi_t_1 ON pembayaranpelayanan_t.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
             LEFT JOIN kelaspelayanan_m kelas_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id
             LEFT JOIN kelaspelayanan_m kelas_admisi ON pasienadmisi_t_1.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
             LEFT JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            NULL::character varying AS umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer) AS status_bayar,
            NULL::integer AS instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            NULL::character varying AS jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan
           FROM pembayaranpelayanan_t
             JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id
             LEFT JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false) pendaftaran
     LEFT JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
     LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
     LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id;");

    	 $this->execute('ALTER TABLE public.infopasiensudahbayar_v
    OWNER TO postgres;');

    	 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200226_062354_migrate_20200226 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_062354_migrate_20200226 cannot be reverted.\n";

        return false;
    }
    */
}
