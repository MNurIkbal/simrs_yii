<?php

use yii\db\Migration;

/**
 * Class m200416_014619_migrate20200416
 */
class m200416_014619_migrate20200416 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."koreksidiagnosa_v";');

        $this->execute("
            CREATE VIEW \"public\".\"koreksidiagnosa_v\" AS  SELECT koreksidiagnosa_t.koreksidiagnosa_id,
    koreksidiagnosa_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    koreksidiagnosa_t.pasienadmisi_id,
    koreksidiagnosa_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    koreksidiagnosa_t.dokterdpjp_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    koreksidiagnosa_t.tgl_koreksidiagnosa,
    koreksidiagnosa_t.kelompokdiagnosa_id,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    koreksidiagnosa_t.diagnosa_id,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
    koreksidiagnosa_t.diagnosaasal_id,
    koreksidiagnosa_t.diag_asal_masuk,
    koreksidiagnosa_t.diag_asal_utama,
    koreksidiagnosa_t.diag_asal_penyerta,
    koreksidiagnosa_t.diag_asal_terapi,
    koreksidiagnosa_t.is_inacbg,
    koreksidiagnosa_t.is_icdprimer,
    koreksidiagnosa_t.is_deleted,
    koreksidiagnosa_t.is_active,
    kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa
   FROM ((((((koreksidiagnosa_t
     JOIN pendaftaran_t ON ((koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((koreksidiagnosa_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((koreksidiagnosa_t.dokterdpjp_id = pegawai_m.pegawai_id)))
     JOIN kelompokdiagnosa_m ON ((koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id)))
     JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
  WHERE (koreksidiagnosa_t.is_deleted = false);");
        
        $this->execute('ALTER TABLE "public"."koreksidiagnosa_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopemberianpiutang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopemberianpiutang_v\" AS  SELECT 'tagihan_rs'::text AS jenis,
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
    fgetnamalookup((pemberianpiutang_t.status_piutang)::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    pasien_m.tanggal_lahir,
    pendaftaran_t.tgl_pendaftaran,
    pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
    peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
    peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip
   FROM (((((pemberianpiutang_t
     JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT p.pegawai_id,
            p.nomorindukpegawai,
            p.nama_pegawai
           FROM pegawai_m p
          WHERE ((p.is_deleted = false) AND (p.is_active = true))) peg_mengetahui ON ((pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id)))
     LEFT JOIN ( SELECT gabung.pendaftaran_id,
            ((COALESCE((sum((gabung.total_tindakan)::integer))::double precision, (0)::double precision) + COALESCE((sum((gabung.total_obat)::integer))::double precision, (0)::double precision)))::integer AS total_tagihan
           FROM (( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
                    NULL::double precision AS total_obat,
                    tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
                   FROM (((pendaftaran_t pendaftaran_t_1
                     LEFT JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
                     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                     LEFT JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id <> 5))))
                  WHERE (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)
                  GROUP BY pendaftaran_t_1.pendaftaran_id, tindakanpelayanan_t.tindakansudahbayar_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    NULL::double precision AS total_tindakan,
                    sum(obatalkespasien_t.hargajual_oa) AS total_obat,
                    obatalkespasien_t.obatsudahbayar_id
                   FROM (((pendaftaran_t pendaftaran_t_1
                     LEFT JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
                     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                     LEFT JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id <> 5))))
                  WHERE (obatalkespasien_t.obatsudahbayar_id IS NULL)
                  GROUP BY pendaftaran_t_1.pendaftaran_id, obatalkespasien_t.obatsudahbayar_id) gabung
             LEFT JOIN pemberianpiutang_t pemberianpiutang_t_1 ON (((gabung.pendaftaran_id = pemberianpiutang_t_1.pendaftaran_id) AND (pemberianpiutang_t_1.is_deleted = false))))
          WHERE (gabung.sudah_bayar IS NULL)
          GROUP BY gabung.pendaftaran_id) tagihan ON ((pemberianpiutang_t.pendaftaran_id = tagihan.pendaftaran_id)))
  WHERE (pemberianpiutang_t.is_deleted = false)
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
    fgetnamalookup((pemberianpiutang_t.status_piutang)::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    NULL::date AS tanggal_lahir,
    penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
    pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
    peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
    peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip
   FROM ((((pemberianpiutang_t
     JOIN penjualanresep_t ON ((pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN pegawai_m ON ((pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
           FROM obatalkespasien_t
          WHERE (obatalkespasien_t.is_deleted = false)
          GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON ((penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id)))
     LEFT JOIN ( SELECT p.pegawai_id,
            p.nomorindukpegawai,
            p.nama_pegawai
           FROM pegawai_m p
          WHERE ((p.is_deleted = false) AND (p.is_active = true))) peg_mengetahui ON ((pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id)))
  WHERE (pemberianpiutang_t.is_deleted = false);");

        $this->execute('ALTER TABLE "public"."infopemberianpiutang_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienrskoreksidetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienrskoreksidetail_v\" AS  SELECT 'RJ'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.pasienadmisi_id,
    pasienmorbiditas_t.pasienmorbiditas_id AS diagnosapasien_id,
        CASE
            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 1) THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_masuk,
        CASE
            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_utama,
        CASE
            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 3) THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_penyerta,
        CASE
            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 6) THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_terapi
   FROM ((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
UNION ALL
 SELECT 'RD'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    NULL::integer AS pasienadmisi_id,
    cppt_t.cppt_id AS diagnosapasien_id,
    NULL::json AS diagnosa_masuk,
    cppt_t.a_diag_utama AS diagnosa_utama,
    cppt_t.a_diag_penyerta AS diagnosa_penyerta,
    NULL::json AS diagnosa_terapi
   FROM ((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ( SELECT cppt_t_1.cppt_id,
            cppt_t_1.pendaftaran_id,
            cppt_t_1.pasienadmisi_id,
            cppt_t_1.a_diag_utama,
            cppt_t_1.a_diag_penyerta
           FROM (cppt_t cppt_t_1
             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                    cppt_last.pendaftaran_id,
                    cppt_last.pasienadmisi_id
                   FROM cppt_t cppt_last
                  WHERE (cppt_last.is_deleted = false)
                  GROUP BY cppt_last.pendaftaran_id, cppt_last.pasienadmisi_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id)))
UNION ALL
 SELECT 'RI'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.pasienadmisi_id,
    resumemedisri_t.resumemedisri_id AS diagnosapasien_id,
    resumemedisri_t.diag_masuk AS diagnosa_masuk,
    resumemedisri_t.diag_utama AS diagnosa_utama,
    resumemedisri_t.diag_penyerta AS diagnosa_penyerta,
    resumemedisri_t.prosedur_diag AS diagnosa_terapi
   FROM (((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))));");
        
        $this->execute('ALTER TABLE "public"."infopasienrskoreksidetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200416_014619_migrate20200416 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200416_014619_migrate20200416 cannot be reverted.\n";

        return false;
    }
    */
}
