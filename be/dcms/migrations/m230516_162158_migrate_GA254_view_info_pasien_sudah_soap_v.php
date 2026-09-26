<?php

use yii\db\Migration;

/**
 * Class m230516_162158_migrate_GA254_view_info_pasien_sudah_soap_v
 */
class m230516_162158_migrate_GA254_view_info_pasien_sudah_soap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasiensudahsoap_v";
        ');

        $this->execute("CREATE VIEW \"public\".\"infopasiensudahsoap_v\" AS
             SELECT 'RJ'::text AS jenis,
                soap_rj.tgl_pendaftaran,
                soap_rj.instalasi_id,
                soap_rj.ruangan_id,
                soap_rj.ruangan,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai AS dokter,
                soap_rj.no_pendaftaran AS jumlah
            FROM pegawai_m
                JOIN ( SELECT pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pegawai_id,
                        pendaftaran_t.instalasi_id,
                        pendaftaran_t.ruangan_id,
                        ruangan_m.ruangan_nama AS ruangan
                    FROM pendaftaran_t
                        LEFT JOIN ( SELECT a.pendaftaran_id,
                                a.is_deleted,
                                a.soaprj_id
                            FROM soaprj_t a) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id AND soaprj_t.is_deleted = false
                        JOIN ( SELECT a.ruangan_id,
                                a.ruangan_nama
                            FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                    WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND pendaftaran_t.pegawai_id IS NOT NULL AND soaprj_t.soaprj_id IS NOT NULL) soap_rj ON pegawai_m.pegawai_id = soap_rj.pegawai_id
            WHERE pegawai_m.kelompokpegawai_id = 1 AND pegawai_m.is_deleted = false
            UNION ALL
            SELECT 'RD'::text AS jenis,
                soap_rd.tgl_pendaftaran,
                soap_rd.instalasi_id,
                soap_rd.ruangan_id,
                soap_rd.ruangan,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai AS dokter,
                soap_rd.no_pendaftaran AS jumlah
            FROM pegawai_m
                JOIN ( SELECT pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pegawai_id,
                        pendaftaran_t.instalasi_id,
                        pendaftaran_t.ruangan_id,
                        ruangan_m.ruangan_nama AS ruangan
                    FROM pendaftaran_t
                        LEFT JOIN ( SELECT a.pendaftaran_id,
                                a.cppt_id,
                                a.pegawai_id
                            FROM cppt_t a) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
                        JOIN ( SELECT a.ruangan_id,
                                a.ruangan_nama
                            FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                    WHERE pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND cppt_t.cppt_id IS NOT NULL AND pendaftaran_t.pegawai_id IS NOT NULL AND (cppt_t.cppt_id IS NOT NULL OR cppt_t.pegawai_id = pendaftaran_t.pegawai_id)) soap_rd ON pegawai_m.pegawai_id = soap_rd.pegawai_id
            WHERE pegawai_m.kelompokpegawai_id = 1 AND pegawai_m.is_deleted = false
            UNION ALL
            SELECT 'RI'::text AS jenis,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.tgl_pendaftaran
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.tgl_pendaftaran
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::date
                        ELSE NULL::date
                    END AS tgl_pendaftaran,
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
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.ruangan
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.ruangan
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS ruangan,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN pegawai_m.pegawai_id
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN pegawai_m.pegawai_id
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::integer
                        ELSE NULL::integer
                    END AS pegawai_id,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN pegawai_m.nama_pegawai
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN pegawai_m.nama_pegawai
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS dokter,
                    CASE
                        WHEN cppt.cppt_id IS NOT NULL AND cppt.ket = 'f'::text THEN cppt.no_pendaftaran
                        WHEN cppt.cppt_id IS NULL AND cppt.ket = 'f'::text THEN cppt.no_pendaftaran
                        WHEN cppt.cppt_id IS NOT NULL THEN NULL::character varying
                        ELSE NULL::character varying
                    END AS jumlah
            FROM pegawai_m
                JOIN ( SELECT pasienadmisi_t.tgl_pendaftaran::date AS tgl_pendaftaran,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasienadmisi_id,
                        ruangan_m.instalasi_id,
                        pasienadmisi_t.ruangan_id,
                        ruangan_m.ruangan_nama AS ruangan,
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
                        LEFT JOIN ( SELECT a.cppt_id,
                                a.pendaftaran_id,
                                a.pasienadmisi_id,
                                a.pegawai_id
                            FROM cppt_t a) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
                        JOIN ( SELECT a.ruangan_id,
                                a.instalasi_id,
                                a.ruangan_nama
                            FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
                    WHERE pendaftaran_t.pasienbatalperiksa_id IS NULL) cppt ON pegawai_m.pegawai_id = cppt.pegawai_id
        ;");
        $this->execute('ALTER TABLE public.infopasiensudahsoap_v OWNER TO postgres;');    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230516_162158_migrate_GA254_view_info_pasien_sudah_soap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230516_162158_migrate_GA254_view_info_pasien_sudah_soap_v cannot be reverted.\n";

        return false;
    }
    */
}
