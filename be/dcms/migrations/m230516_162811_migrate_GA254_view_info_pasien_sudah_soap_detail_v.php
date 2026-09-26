<?php

use yii\db\Migration;

/**
 * Class m230516_162811_migrate_GA254_view_info_pasien_sudah_soap_detail_v
 */
class m230516_162811_migrate_GA254_view_info_pasien_sudah_soap_detail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasiensudahsoapdetail_v";
        ');

        $this->execute("CREATE VIEW \"public\".\"infopasiensudahsoapdetail_v\" AS
             SELECT pendaftaran_t.pegawai_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                pasien_m.no_rekam_medik AS no_rm,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
                pasien_m.nama_pasien,
                look_jeniskelamin.lookup_name AS jenis_kelamin,
                pasien_m.alamat_pasien
            FROM pendaftaran_t
                LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.is_deleted,
                        a.soaprj_id
                    FROM soaprj_t a) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id AND soaprj_t.is_deleted = false
                JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.jeniskelamin,
                        a.alamat_pasien
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
            WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND pendaftaran_t.pegawai_id IS NOT NULL AND soaprj_t.soaprj_id IS NOT NULL
            UNION ALL
            SELECT pendaftaran_t.pegawai_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                pasien_m.no_rekam_medik AS no_rm,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
                pasien_m.nama_pasien,
                look_jeniskelamin.lookup_name AS jenis_kelamin,
                pasien_m.alamat_pasien
            FROM pendaftaran_t
                JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.jeniskelamin,
                        a.alamat_pasien
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.cppt_id,
                        a.pegawai_id
                    FROM cppt_t a) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
                LEFT JOIN ( SELECT a.ruangan_id
                    FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
            WHERE pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND cppt_t.cppt_id IS NOT NULL AND pendaftaran_t.pegawai_id IS NOT NULL AND (cppt_t.cppt_id IS NOT NULL OR cppt_t.pegawai_id = pendaftaran_t.pegawai_id)
            UNION ALL
            SELECT
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN pegawai_m.pegawai_id
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN pegawai_m.pegawai_id
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::integer
                        ELSE NULL::integer
                    END AS pegawai_id,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.instalasi_id
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.instalasi_id
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::integer
                        ELSE NULL::integer
                    END AS instalasi_id,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.ruangan_id
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.ruangan_id
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::integer
                        ELSE NULL::integer
                    END AS ruangan_id,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.no_rm
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.no_rm
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS no_rm,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.no_pendaftaran
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.no_pendaftaran
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS no_pendaftaran,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.tgl_pendaftaran
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.tgl_pendaftaran
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::date
                        ELSE NULL::date
                    END AS tgl_pendaftaran,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.nama_pasien
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.nama_pasien
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS nama_pasien,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.jenis_kelamin
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.jenis_kelamin
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS jenis_kelamin,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.alamat_pasien
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.alamat_pasien
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::text
                        ELSE NULL::text
                    END AS alamat_pasien
            FROM pegawai_m
                JOIN ( SELECT pasienadmisi_t.tgl_pendaftaran::date AS tgl_pendaftaran,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasienadmisi_id,
                        ruangan_m.instalasi_id,
                        pasienadmisi_t.ruangan_id,
                        ruangan_m.ruangan_nama AS ruangan,
                        pasien_m.nama_pasien,
                        pasien_m.no_rekam_medik AS no_rm,
                        look_jeniskelamin.lookup_name AS jenis_kelamin,
                        pasien_m.alamat_pasien,
                        COALESCE(cppt_t.pegawai_id, pasienadmisi_t.pegawai_id) AS pegawai_id,
                        cppt_t.cppt_id,
                            CASE
                                WHEN pasienadmisi_t.pegawai_id = cppt_t.pegawai_id THEN 't'::text
                                ELSE 'f'::text
                            END AS ket
                    FROM pendaftaran_t
                        JOIN ( SELECT a.pasienadmisi_id,
                                a.pegawai_id,
                                a.ruangan_id,
                                a.tgl_pendaftaran
                            FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                        LEFT JOIN ( SELECT a.pendaftaran_id,
                                a.pasienadmisi_id,
                                a.pegawai_id,
                                a.cppt_id
                            FROM cppt_t a) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
                        JOIN ( SELECT a.pasien_id,
                                a.nama_pasien,
                                a.no_rekam_medik,
                                a.jeniskelamin,
                                a.alamat_pasien
                            FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                        JOIN ( SELECT a.ruangan_id,
                                a.instalasi_id,
                                a.ruangan_nama
                            FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN ( SELECT a.lookup_id,
                                a.lookup_name
                            FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                    WHERE pendaftaran_t.pasienbatalperiksa_id IS NULL) cppt ON pegawai_m.pegawai_id = cppt.pegawai_id
        ;");
         $this->execute('ALTER TABLE public.infopasiensudahsoapdetail_v OWNER TO postgres;');    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230516_162811_migrate_GA254_view_info_pasien_sudah_soap_detail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230516_162811_migrate_GA254_view_info_pasien_sudah_soap_detail_v cannot be reverted.\n";

        return false;
    }
    */
}
