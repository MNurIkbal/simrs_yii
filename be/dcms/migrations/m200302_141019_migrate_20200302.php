<?php

use yii\db\Migration;

/**
 * Class m200302_141019_migrate_20200302
 */
class m200302_141019_migrate_20200302 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
                      ADD COLUMN "header" text COLLATE "pg_catalog"."default",
                      ADD COLUMN "footer" text COLLATE "pg_catalog"."default",
                      ADD COLUMN "is_slider" int2,
                      ADD COLUMN "url_slider" varchar(255) COLLATE "pg_catalog"."default",
                      ADD COLUMN "header_detail" text COLLATE "pg_catalog"."default",
                      ADD COLUMN "path_logoheader" text COLLATE "pg_catalog"."default";');

        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."is_slider" IS \'0=photo, 1=video, 2=url\';');

        $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_sisatagihan" IS \'nominal Pemberian piutang \';');

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
    pembayaran_t.total_tagihan + pembayaran_t.total_administrasi AS jmlpembayaran,
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
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
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
        CASE
            WHEN agr_bukti_bayar.total_tagihan IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tagihan
        END AS jmlpembayaran,
        CASE
            WHEN agr_bukti_bayar.total_tunai IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tunai
        END AS pembayaran_tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS pembayaran_nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS pembayaran_penjamin,
    agr_bukti_bayar.pembayaran_id
   FROM ( SELECT 'PEMBAYARAN'::text AS jenis,
            tandabuktibayar_t.tandabuktibayar_id,
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
                END AS total_tunai,
            pembayaran_penjamin.total_tagihan,
            pembayaran_penjamin.pembayaran_id
           FROM tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
             LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN ( SELECT COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    pembayaran_t.total_nontunai,
                    COALESCE(pembayaran_t.total_tunai, 0::double precision) AS total_tunai,
                    pembayaran_t.total_tagihan + pembayaran_t.total_administrasi AS total_tagihan
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
          WHERE tandabuktibayar_t.is_deleted = false
        UNION ALL
         SELECT 'RETUR'::text AS jenis,
            tandabuktibayar_t.tandabuktibayar_id,
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
            returbayarpelayanan_t.no_returbayar AS no_pembayaran,
            pembayaranpelayanan_t.pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            NULL::character varying AS carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            NULL::character varying AS penjamin_nama,
            pembayaran_t.total_dibayar AS jmlpembayaran,
            NULL::integer AS penjualanresep_id,
            NULL::double precision AS total_penjamin,
            - returbayarpelayanan_t.total_nontunai,
            - returbayarpelayanan_t.total_biayaretur AS total_tunai,
            0 AS total_tagihan,
            pembayaran_t.pembayaran_id
           FROM returbayarpelayanan_t
             JOIN tandabuktibayar_t ON returbayarpelayanan_t.returbayarpelayanan_id = tandabuktibayar_t.returbayarpelayanan_id
             JOIN pembayaranpelayanan_t ON tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id
             JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE returbayarpelayanan_t.is_deleted = false AND tandabuktibayar_t.is_deleted = false AND pembayaranpelayanan_t.is_deleted = false) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;");

        $this->execute('ALTER TABLE public.closing_kasir_view
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infoorderanlabdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infoorderanlabdetail_v AS 
 SELECT permintaankepenunjang_t.permintaankepenunjang_id,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    daftartindakan_m.daftartindakan_nama,
    NULL::character varying AS tipepaket_nama,
    permintaankepenunjang_t.qtypermintaan,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tarif_pelayanan,
    permintaankepenunjang_t.daftartindakan_id,
    permintaankepenunjang_t.tipepaket_id,
    permintaankepenunjang_t.tarif_cytotindakan,
    permintaankepenunjang_t.satuan_tindakan
   FROM pasienkirimkeunitlain_t
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pemeriksaanlab_m.is_deleted = false AND jenispemeriksaanlab_m.is_deleted = false AND daftartindakan_m.is_deleted = false
UNION ALL
 SELECT permintaankepenunjang_t.permintaankepenunjang_id,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    concat(tipepaket_m.tipepaket_nama, '-', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
    tipepaket_m.tipepaket_nama,
    permintaankepenunjang_t.qtypermintaan,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tarif_pelayanan,
    paketpelayanan_mp.daftartindakan_id,
    permintaankepenunjang_t.tipepaket_id,
    permintaankepenunjang_t.tarif_cytotindakan,
    permintaankepenunjang_t.satuan_tindakan
   FROM pasienkirimkeunitlain_t
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienkirimkeunitlain_t.is_deleted = false AND jenispemeriksaanlab_m.is_deleted = false AND daftartindakan_m.is_deleted = false AND paketpelayanan_mp.is_deleted = false AND pemeriksaanlab_m.is_deleted = false;
");

        $this->execute('ALTER TABLE public.infoorderanlabdetail_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infoclosingkasir_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infoclosingkasir_v AS 
 SELECT 'pembayaran'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pembayaran_t.total_dijamin + pembayaran_t.total_tunai + pembayaran_t.total_nontunai AS total_tagihan,
    pembayaran_t.total_dibayar - pembayaran_t.total_kembalian AS total_tunai,
    pembayaran_t.total_nontunai,
    pembayaran_t.total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar
UNION ALL
 SELECT 'uang_muka'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    0 AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.bayaruangmuka_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.bayaruangmuka_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.tglbuktibayar, tandabuktibayar_t.uangditerima, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
UNION ALL
 SELECT 'resep_bebas'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    pasien_m.pasien_id,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    0 AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, penjualanresep_t.penjualanresep_id, penjualanresep_t.noresep, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
UNION ALL
 SELECT 'retur'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    - returbayarpelayanan_t.total_biayaretur AS total_tunai,
    - returbayarpelayanan_t.total_nontunai AS total_nontunai,
    0 AS total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar,
            tandabuktibayar_t_1.returbayarpelayanan_id
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar, tandabuktibayar_t_1.returbayarpelayanan_id) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN returbayarpelayanan_t ON tandabuktibayar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, returbayarpelayanan_t.total_biayaretur, returbayarpelayanan_t.total_nontunai, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar;
");

        $this->execute('ALTER TABLE public.infoclosingkasir_v
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
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
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
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN instalasi_m instalasi_pelayanan ON tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN ruangan_m ruangan_pelayanan ON tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND tindakanpelayanan_t.is_deleted = false
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
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
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
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
  WHERE obatalkespasien_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.rincianpasiendetail_v
  OWNER TO postgres;');


        $this->execute('DROP TABLE if exists public.konfigsystemdetail_k;');

        $this->execute('CREATE TABLE public.konfigsystemdetail_k
(
  konfigsystemdetail_id serial8,
  konfigsystem_id integer NOT NULL,
  file text,
  is_foto boolean DEFAULT true,
  additional_data text,
  created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
  created_by integer,
  modified_count integer,
  last_modified_date timestamp(6) without time zone,
  last_modified_by integer,
  is_deleted boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  deleted_date timestamp(6) without time zone,
  deleted_by integer,
  CONSTRAINT konfigsystemdetail_k_pkey PRIMARY KEY (konfigsystemdetail_id)
)
WITH (
  OIDS=FALSE
);');
        
        $this->execute('ALTER TABLE public.konfigsystemdetail_k
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200302_141019_migrate_20200302 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200302_141019_migrate_20200302 cannot be reverted.\n";

        return false;
    }
    */
}
