<?php

use yii\db\Migration;

/**
 * Class m230208_165428_migrate_GB645_infopasienrskoreksidetail_v
 */
class m230208_165428_migrate_GB645_infopasienrskoreksidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienrskoreksidetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienrskoreksidetail_v
        AS SELECT 'RJ'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pasienadmisi_id,
            soaprj_t.soaprj_id AS diagnosapasien_id,
            NULL::text AS diagnosa_masuk,
            soaprj_t.a_diag_utama AS diagnosa_utama,
            soaprj_t.a_diag_penyerta AS diagnosa_penyerta,
            NULL::json AS diagnosa_terapi
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.soaprj_id,
                    a.pendaftaran_id,
                    a.a_diag_utama,
                    a.a_diag_penyerta
                   FROM soaprj_t a
                  WHERE a.tgl_soaprj = (( SELECT max(b.tgl_soaprj) AS max
                           FROM soaprj_t b
                             JOIN ( SELECT a_1.pegawai_id,
                                    a_1.kelompokpegawai_id
                                   FROM pegawai_m a_1
                                  WHERE a_1.kelompokpegawai_id = 1) pegawai_m ON b.pegawai_id = pegawai_m.pegawai_id
                          WHERE a.pendaftaran_id = b.pendaftaran_id AND b.is_deleted = false)) AND a.is_deleted = false) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 1
        UNION ALL
         SELECT 'RD'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            NULL::integer AS pasienadmisi_id,
            cppt_t.cppt_id AS diagnosapasien_id,
            concat(asesmenmedisrd_t.diagnosa_id::text, ',', diag_masukrd.diagnosa_nama, ',', diag_masukrd.diagnosa_kode) AS diagnosa_masuk,
            cppt_t.a_diag_utama AS diagnosa_utama,
            cppt_t.a_diag_penyerta AS diagnosa_penyerta,
            NULL::json AS diagnosa_terapi
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.cppt_id,
                    a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.a_diag_utama,
                    a.a_diag_penyerta
                   FROM cppt_t a
                  WHERE a.tgl_cppt = (( SELECT max(b.tgl_cppt) AS max
                           FROM cppt_t b
                             JOIN ( SELECT a_1.pegawai_id,
                                    a_1.kelompokpegawai_id
                                   FROM pegawai_m a_1
                                  WHERE a_1.kelompokpegawai_id = 1) pegawai_m ON b.pegawai_id = pegawai_m.pegawai_id
                          WHERE a.pendaftaran_id = b.pendaftaran_id AND b.pasienadmisi_id IS NULL AND b.is_deleted = false AND b.referred_id IS NULL)) AND a.is_deleted = false) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
             LEFT JOIN asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
             LEFT JOIN diagnosa_m diag_masukrd ON asesmenmedisrd_t.diagnosa_id = diag_masukrd.diagnosa_id
          WHERE cppt_t.pasienadmisi_id IS NULL
        UNION ALL
         SELECT 'RI'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pasienadmisi_id,
            resumemedisri_t.resumemedisri_id AS diagnosapasien_id,
            resumemedisri_t.diag_awal::text AS diagnosa_masuk,
            resumemedisri_t.diag_utama AS diagnosa_utama,
            resumemedisri_t.diag_penyerta AS diagnosa_penyerta,
            resumemedisri_t.prosedur_diag AS diagnosa_terapi
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.resumemedisri_id,
                    a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.diag_awal,
                    a.diag_utama,
                    a.diag_penyerta,
                    a.prosedur_diag,
                    a.created_by,
                    a.created_date,
                    a.is_deleted
                   FROM resumemedisri_t a
                  WHERE a.created_date = (( SELECT max(b.created_date) AS max
                           FROM resumemedisri_t b
                             JOIN ( SELECT a_1.loginpemakai_id,
                                    a_1.pegawai_id
                                   FROM loginpemakai_k a_1
                                     JOIN ( SELECT a_2.pegawai_id,
                                            a_2.kelompokpegawai_id
                                           FROM pegawai_m a_2
                                          WHERE a_2.kelompokpegawai_id = 1) pegawai_m ON a_1.pegawai_id = pegawai_m.pegawai_id) pegawai ON b.created_by = pegawai.loginpemakai_id
                          WHERE a.pendaftaran_id = b.pendaftaran_id AND b.is_deleted = false)) AND a.is_deleted = false) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230208_165428_migrate_GB645_infopasienrskoreksidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230208_165428_migrate_GB645_infopasienrskoreksidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
