<?php

use yii\db\Migration;

/**
 * Class m200320_151509_migrate_20200320
 */
class m200320_151509_migrate_20200320 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*konfigfarmasi_k*/
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN "is_verifpemesanan" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN "is_verifpenerimaan" bool DEFAULT false;');

/*konfigsystem_k*/
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_keteranganpasien" bool DEFAULT false;');

/*pemberrianpiutang_t*/
        $this->execute('ALTER TABLE "public"."pemberianpiutang_t" ALTER COLUMN "pendaftaran_id" DROP NOT NULL;');
        $this->execute('ALTER TABLE "public"."pemberianpiutang_t" ADD COLUMN "penjualanresep_id" int4;');
        $this->execute('ALTER TABLE "public"."pemberianpiutang_t" ADD COLUMN "pegawaimengetahui_id" int4;');
        $this->execute('COMMENT ON COLUMN "public"."pemberianpiutang_t"."pendaftaran_id" IS \'pemberian piutang untuk pasien dalam tagihan\';');
        $this->execute('COMMENT ON COLUMN "public"."pemberianpiutang_t"."penjualanresep_id" IS \'pemberian piutang untuk penjualan resep bebas\';');
        $this->execute('COMMENT ON COLUMN "public"."pemberianpiutang_t"."pegawaimengetahui_id" IS \'pegawai yang dibebankan\';');

/*pesanobatalkes_t*/
        $this->execute('ALTER TABLE "public"."pesanobatalkes_t" ADD COLUMN "status_verifikasi" int2;');
        $this->execute('COMMENT ON COLUMN "public"."pesanobatalkes_t"."status_verifikasi" IS \'lookup_type=status_verifobat\';');
       
/*tandabuktibayar_t*/
        $this->execute('ALTER TABLE "public"."tandabuktibayar_t" ADD COLUMN "pembayaranpiutang_id" int4;');
        
/*infopemberianpiutang_v*/
        $this->execute('DROP VIEW if exists public.infopemberianpiutang_v;');        
        $this->execute("
            CREATE OR REPLACE VIEW public.infopemberianpiutang_v
 AS
 SELECT 'tagihan_rs'::text AS jenis,
    pemberianpiutang_t.pemberianpiutang_id,
    pemberianpiutang_t.no_pemberianpiutang,
    pemberianpiutang_t.tgl_pemberianpiutang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.total_tagihan AS tagihan,
    pemberianpiutang_t.total_piutang,
    pemberianpiutang_t.total_sisapiutang,
    pemberianpiutang_t.total_bayarpiutang,
    pemberianpiutang_t.status_piutang,
    fgetnamalookup(pemberianpiutang_t.status_piutang::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    pasien_m.tanggal_lahir,
    pendaftaran_t.tgl_pendaftaran,
    pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
    peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
    peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip
   FROM pemberianpiutang_t
     JOIN pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT p.pegawai_id,
            p.nomorindukpegawai,
            p.nama_pegawai
           FROM pegawai_m p
          WHERE p.is_deleted = false AND p.is_active = true) peg_mengetahui ON pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id
     JOIN ( SELECT gabung.pendaftaran_id,
            (COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) + COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision))::integer AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
                    NULL::double precision AS total_obat,
                    tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, tindakanpelayanan_t.tindakansudahbayar_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    NULL::double precision AS total_tindakan,
                    sum(obatalkespasien_t.hargajual_oa) AS total_obat,
                    obatalkespasien_t.obatsudahbayar_id
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, obatalkespasien_t.obatsudahbayar_id) gabung
             LEFT JOIN pemberianpiutang_t pemberianpiutang_t_1 ON gabung.pendaftaran_id = pemberianpiutang_t_1.pendaftaran_id AND pemberianpiutang_t_1.is_deleted = false
          WHERE gabung.sudah_bayar IS NULL
          GROUP BY gabung.pendaftaran_id) tagihan ON pemberianpiutang_t.pendaftaran_id = tagihan.pendaftaran_id
  WHERE pemberianpiutang_t.is_deleted = false
UNION ALL
 SELECT 'resep_bebas'::text AS jenis,
    pemberianpiutang_t.pemberianpiutang_id,
    pemberianpiutang_t.no_pemberianpiutang,
    pemberianpiutang_t.tgl_pemberianpiutang,
    pemberianpiutang_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    NULL::integer AS pasien_id,
    NULL::character varying AS no_rekam_medik,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    tagihan_resep.tagihan_obat AS tagihan,
    pemberianpiutang_t.total_piutang,
    pemberianpiutang_t.total_sisapiutang,
    pemberianpiutang_t.total_bayarpiutang,
    pemberianpiutang_t.status_piutang,
    fgetnamalookup(pemberianpiutang_t.status_piutang::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    NULL::date AS tanggal_lahir,
    penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
    pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
    peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
    peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip
   FROM pemberianpiutang_t
     JOIN penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false
          GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
     LEFT JOIN ( SELECT p.pegawai_id,
            p.nomorindukpegawai,
            p.nama_pegawai
           FROM pegawai_m p
          WHERE p.is_deleted = false AND p.is_active = true) peg_mengetahui ON pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id
  WHERE pemberianpiutang_t.is_deleted = false;");

          $this->execute('ALTER TABLE public.infopemberianpiutang_v
    OWNER TO postgres;');


/*fpemberianpiutang_v*/ 
 $this->execute('DROP VIEW if exists public.fpemberianpiutang_v;');

        $this->execute("
                    CREATE OR REPLACE VIEW public.fpemberianpiutang_v
 AS
 SELECT 'tagihan_rs'::text AS jenis,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_resep,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS penjualanresep_id,
    pasien_m.nama_pasien,
    COALESCE(total_tagihan.total_tagihan, 0::double precision) AS total_tagihan,
    COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.tgl_pendaftaran,
    COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
    konfigsystem_k.adm_persen,
    adm.tarif_max,
    COALESCE(tagihan_ranap.total_tagihan, 0::double precision) AS tagihan_ranap,
    bayaruangmuka_t.jumlah_uangmuka AS uang_muka,
    NULL::text AS is_pembulatankeatas,
    NULL::text AS satuanpembulatan
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            sum(tagihan.tagihan) AS total_tagihan
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                  GROUP BY tindakanpelayanan_t.pendaftaran_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan
                   FROM obatalkespasien_t
                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
          GROUP BY tagihan.pendaftaran_id) total_tagihan ON pendaftaran_t.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN pemberianpiutang_t ON pendaftaran_t.pendaftaran_id = pemberianpiutang_t.pendaftaran_id
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
     LEFT JOIN konfigsystem_k ON konfigsystem_k.is_deleted = false
     LEFT JOIN ( SELECT tariftindakan_m.tariftindakan_id,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            tariftindakan_m.penjamin_id,
            tariftindakan_m.harga_tariftindakan AS tarif_max
           FROM tariftindakan_m
             JOIN konfigsystem_k konfig_tarif ON tariftindakan_m.daftartindakan_id = konfig_tarif.adm_tindakan_id
          WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.komponentarif_id = 6) adm ON adm.kelaspelayanan_id = pasienadmisi_t.kelaspelayanan_id AND adm.penjamin_id = pasienadmisi_t.penjamin_id
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            sum(tagihan.tagihan) AS total_tagihan
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.pasienadmisi_id IS NOT NULL AND tindakanpelayanan_t.instalasi_id = 3
                  GROUP BY tindakanpelayanan_t.pendaftaran_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan
                   FROM obatalkespasien_t
                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL AND obatalkespasien_t.pasienadmisi_id IS NOT NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
          GROUP BY tagihan.pendaftaran_id) tagihan_ranap ON pendaftaran_t.pendaftaran_id = tagihan_ranap.pendaftaran_id
  WHERE pendaftaran_t.is_deleted = false
UNION ALL
 SELECT 'resep_bebas'::text AS jenis,
    penjualanresep_t.noresep AS no_pendaftaran,
    penjualanresep_t.noresep AS no_resep,
    NULL::character varying AS no_rekam_medik,
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    tagihan_resep.tagihan_obat AS total_tagihan,
    COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
    NULL::date AS tanggal_lahir,
    NULL::character varying AS umur,
    penjualanresep_t.tglresep AS tgl_pendaftaran,
    COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
    0 AS adm_persen,
    0 AS tarif_max,
    0 AS tagihan_ranap,
    0 AS uang_muka,
    NULL::text AS is_pembulatankeatas,
    NULL::text AS satuanpembulatan
   FROM penjualanresep_t
     LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
          GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
     LEFT JOIN pemberianpiutang_t ON penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id
  WHERE penjualanresep_t.status_bayar = 349 AND penjualanresep_t.is_deleted = false AND penjualanresep_t.pendaftaran_id IS NULL;
");
          $this->execute('ALTER TABLE public.fpemberianpiutang_v
    OWNER TO postgres;');

 /*lookup_m*/
        $this->execute('DELETE from lookup_m where lookup_type=\'status_verifobat\';');
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (667, 'status_verifobat', 'Sudah Verifkasi', 'Sudah Verifkasi', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (666, 'status_verifobat', 'Belum Verifikasi', 'Belum Verifikasi', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200320_151509_migrate_20200320 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200320_151509_migrate_20200320 cannot be reverted.\n";

        return false;
    }
    */
}
