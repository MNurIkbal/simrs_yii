<?php

use yii\db\Migration;

/**
 * Class m230126_021715_migrate_glbj1_view_cpptgizi_v
 */
class m230126_021715_migrate_glbj1_view_cpptgizi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."cpptgizi_v";
        ');

        $this->execute("
            CREATE OR REPLACE VIEW public.cpptgizi_v
            AS SELECT pagt_t.pagt_id,
                pegawai_m.nama_pegawai AS pemberi_asuhan,
                pagt_t.pendaftaran_id,
                pagt_t.tgl_kajian,
                pagt_t.bb,
                pagt_t.tb,
                pagt_t.imt_dewasa,
                pagt_t.imt_anak,
                pagt_t.lila,
                pagt_t.bb_anak,
                pagt_t.ulna,
                pagt_t.status_gizi,
                pagt_t.trigliserida,
                pagt_t.hdl,
                pagt_t.ldl,
                pagt_t.kolesterol,
                pagt_t.ureum,
                pagt_t.kreatinin,
                pagt_t.kalium,
                pagt_t.natrium,
                pagt_t.kalsium,
                pagt_t.phospor,
                pagt_t.sgot,
                pagt_t.sgpt,
                pagt_t.bilirubin,
                pagt_t.gd_sewaktu,
                pagt_t.gd_puasa,
                pagt_t.hba1c,
                pagt_t.dua_jam_pp,
                pagt_t.hb,
                pagt_t.albumin,
                pagt_t.ht,
                pagt_t.pemeriksaan_fisik,
                pagt_t.tekanan_darah,
                pagt_t.gangguan_pencernaan,
                pagt_t.makan_pagi_pokok,
                pagt_t.makan_pagi_hewani,
                pagt_t.makan_pagi_nabati,
                pagt_t.makan_pagi_sayur,
                pagt_t.makan_pagi_buah,
                pagt_t.makan_pagi_energi,
                pagt_t.makan_pagi_protein,
                pagt_t.makan_pagi_lemak,
                pagt_t.makan_pagi_kh,
                pagt_t.selingan_pagi_pokok,
                pagt_t.selingan_pagi_hewani,
                pagt_t.selingan_pagi_nabati,
                pagt_t.selingan_pagi_sayur,
                pagt_t.selingan_pagi_buah,
                pagt_t.selingan_pagi_energi,
                pagt_t.selingan_pagi_protein,
                pagt_t.selingan_pagi_lemak,
                pagt_t.selingan_pagi_kh,
                pagt_t.makan_siang_pokok,
                pagt_t.makan_siang_hewani,
                pagt_t.makan_siang_nabati,
                pagt_t.makan_siang_sayur,
                pagt_t.makan_siang_buah,
                pagt_t.makan_siang_energi,
                pagt_t.makan_siang_protein,
                pagt_t.makan_siang_lemak,
                pagt_t.makan_siang_kh,
                pagt_t.selingan_sore_pokok,
                pagt_t.selingan_sore_hewani,
                pagt_t.selingan_sore_nabati,
                pagt_t.selingan_sore_sayur,
                pagt_t.selingan_sore_buah,
                pagt_t.selingan_sore_energi,
                pagt_t.selingan_sore_protein,
                pagt_t.selingan_sore_lemak,
                pagt_t.selingan_sore_kh,
                pagt_t.makan_malam_pokok,
                pagt_t.makan_malam_hewani,
                pagt_t.makan_malam_nabati,
                pagt_t.makan_malam_sayur,
                pagt_t.makan_malam_buah,
                pagt_t.makan_malam_energi,
                pagt_t.makan_malam_protein,
                pagt_t.makan_malam_lemak,
                pagt_t.makan_malam_kh,
                pagt_t.selingan_malam_pokok,
                pagt_t.selingan_malam_hewani,
                pagt_t.selingan_malam_nabati,
                pagt_t.selingan_malam_sayur,
                pagt_t.selingan_malam_buah,
                pagt_t.selingan_malam_energi,
                pagt_t.selingan_malam_protein,
                pagt_t.selingan_malam_lemak,
                pagt_t.selingan_malam_kh,
                pagt_t.diagnosa_gizi,
                pagt_t.cara_intervensi,
                pagt_t.diet_diberikan,
                pagt_t.energi,
                pagt_t.lemak,
                pagt_t.protein,
                pagt_t.kh,
                pagt_t.tujuan_diet,
                pagt_t.bentuk_makanan,
                pagt_t.bentuk_makanan_saji AS sonde_voeding_saji,
                pagt_t.bentuk_makanan_hari AS sonde_voeding_hari,
                pagt_t.cara_pemberian,
                pagt_t.bagi_makan_pagi_nasi,
                pagt_t.bagi_makan_pagi_hewani,
                pagt_t.bagi_makan_pagi_nabati,
                pagt_t.bagi_makan_pagi_sayur,
                pagt_t.bagi_makan_pagi_buah,
                pagt_t.bagi_selingan_pagi_nasi,
                pagt_t.bagi_selingan_pagi_hewani,
                pagt_t.bagi_selingan_pagi_nabati,
                pagt_t.bagi_selingan_pagi_sayur,
                pagt_t.bagi_selingan_pagi_buah,
                pagt_t.bagi_makan_siang_nasi,
                pagt_t.bagi_makan_siang_hewani,
                pagt_t.bagi_makan_siang_nabati,
                pagt_t.bagi_makan_siang_sayur,
                pagt_t.bagi_makan_siang_buah,
                pagt_t.bagi_selingan_sore_nasi,
                pagt_t.bagi_selingan_sore_hewani,
                pagt_t.bagi_selingan_sore_nabati,
                pagt_t.bagi_selingan_sore_sayur,
                pagt_t.bagi_selingan_sore_buah,
                pagt_t.bagi_makan_malam_nasi,
                pagt_t.bagi_makan_malam_hewani,
                pagt_t.bagi_makan_malam_nabati,
                pagt_t.bagi_makan_malam_sayur,
                pagt_t.bagi_makan_malam_buah,
                pagt_t.bagi_selingan_malam_nasi,
                pagt_t.bagi_selingan_malam_hewani,
                pagt_t.bagi_selingan_malam_nabati,
                pagt_t.bagi_selingan_malam_sayur,
                pagt_t.bagi_selingan_malam_buah,
                pagtmonev_t.pagtmonev_id,
                pagtmonev_t.tgl_monev,
                pagtmonev_t.berat_badan,
                pagtmonev_t.tekanan_darah AS tekanan_darah_monev,
                pagtmonev_t.nilai_lab_abnormal,
                pagtmonev_t.tgl_monev AS tgl_asupan_makanan,
                pagtmonev_t.oral_energi,
                pagtmonev_t.oral_protein,
                pagtmonev_t.oral_lemak,
                pagtmonev_t.oral_kh,
                pagtmonev_t.enteral_energi,
                pagtmonev_t.enteral_protein,
                pagtmonev_t.enteral_lemak,
                pagtmonev_t.enteral_kh,
                pagtmonev_t.parenteral_energi,
                pagtmonev_t.parenteral_protein,
                pagtmonev_t.parenteral_lemak,
                pagtmonev_t.parenteral_kh,
                pagtmonev_t.total_asupan_energi,
                pagtmonev_t.total_asupan_protein,
                pagtmonev_t.total_asupan_lemak,
                pagtmonev_t.total_asupan_kh,
                pagtmonev_t.evaluasi_usulan,
                ''::character varying AS instruksi_ppa,
                pagt_t.is_verifikasi,
                pagt_t.tgl_verifikasi,
                pagt_t.pegawai_verifikasi_id,
                pegawai_verifikasi.nama_pegawai AS pegawai_verifikasi_nama,
                pasienadmisi.dokteradmisi_id,
                pasienadmisi.dokteradmisi_nama,
                pegawai_m.pegawai_id AS pemberi_instruksi_id,
                kelompokpegawai_m.kelompokpegawai_nama,
                kelompokpegawai_m.kelompokpegawai_id,
                cpptadime_t.asesmen_gizi AS asesmen_adime,
                cpptadime_t.diagnosa_gizi AS diagnosa_adime,
                cpptadime_t.intervensi_gizi AS intervensi_adime,
                cpptadime_t.monitoring AS monitoring_adime,
                cpptadime_t.evaluasi AS evaluasi_adime
            FROM pagt_t
                LEFT JOIN ( SELECT pagtmonev_t_1.pagt_id,
                        max(pagtmonev_t_1.pagtmonev_id) AS pagtmonev_id
                    FROM pagtmonev_t pagtmonev_t_1
                    GROUP BY pagtmonev_t_1.pagt_id) pagtmonev_last ON pagt_t.pagt_id = pagtmonev_last.pagt_id
                LEFT JOIN pagtmonev_t ON pagtmonev_last.pagtmonev_id = pagtmonev_t.pagtmonev_id
                LEFT JOIN loginpemakai_k ON pagt_t.created_by = loginpemakai_k.loginpemakai_id
                LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
                LEFT JOIN pegawai_m pegawai_verifikasi ON pagt_t.pegawai_verifikasi_id = pegawai_verifikasi.pegawai_id
                LEFT JOIN ( SELECT pasienadmisi_t.pegawai_id AS dokteradmisi_id,
                        dokteradmisi.nama_pegawai AS dokteradmisi_nama,
                        pasienadmisi_t.pendaftaran_id
                    FROM pasienadmisi_t
                        LEFT JOIN pegawai_m dokteradmisi ON pasienadmisi_t.pegawai_id = dokteradmisi.pegawai_id) pasienadmisi ON pagt_t.pendaftaran_id = pasienadmisi.pendaftaran_id
                LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                LEFT JOIN cpptadime_t ON pagt_t.pagt_id = cpptadime_t.pagt_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230126_021715_migrate_glbj1_view_cpptgizi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230126_021715_migrate_glbj1_view_cpptgizi_v cannot be reverted.\n";

        return false;
    }
    */
}
