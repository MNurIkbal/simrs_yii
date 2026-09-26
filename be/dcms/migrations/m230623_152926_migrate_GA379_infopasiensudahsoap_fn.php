<?php

use yii\db\Migration;

/**
 * Class m230623_152926_migrate_GA379_infopasiensudahsoap_fn
 */
class m230623_152926_migrate_GA379_infopasiensudahsoap_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."infopasiensudahsoap_fn";');
        $this->execute("CREATE OR REPLACE FUNCTION public.infopasiensudahsoap_fn(xstart_date date, xend_date date)
        RETURNS TABLE(jenis text, tgl_pendaftaran date, instalasi_id integer, instalasi character varying, ruangan_id integer, ruangan character varying, pegawai_id integer, dokter character varying, jumlah_pasien character varying, jumlah_soap bigint)
        LANGUAGE plpgsql
       AS \$function\$
       
       BEGIN
           RETURN query
           SELECT
               'RJ' AS jenis,
               pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
               instalasi_m.instalasi_id,
               instalasi_m.instalasi_nama AS instalasi,
               ruanganpegawai_mp.ruangan_id,
               ruangan_m.ruangan_nama AS ruangan,
               ruanganpegawai_mp.pegawai_id,
               pegawai_m.nama_pegawai AS dokter,
               pendaftaran_t.no_pendaftaran AS jumlah_pasien,
               COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap
           FROM ruanganpegawai_mp
           JOIN (SELECT
                       pegawai_m.pegawai_id,
                       pegawai_m.nama_pegawai
                   FROM pegawai_m
                   WHERE pegawai_m.kelompokpegawai_id = 1
                   AND pegawai_m.is_deleted = FALSE
                   AND pegawai_m.is_active = TRUE) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
           JOIN (SELECT
                       ruangan_m.ruangan_id,
                       ruangan_m.instalasi_id,
                       ruangan_m.ruangan_nama
                   FROM ruangan_m
                   WHERE ruangan_m.is_deleted = FALSE
                   AND ruangan_m.is_active = TRUE) ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
           JOIN (SELECT
                       instalasi_m.instalasi_id,
                       instalasi_m.instalasi_nama
                   FROM instalasi_m
                   WHERE instalasi_m.is_deleted = FALSE
                   AND instalasi_m.is_active = TRUE) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
           LEFT JOIN (SELECT
                           pendaftaran_t.pendaftaran_id,
                           pendaftaran_t.no_pendaftaran,
                           pendaftaran_t.tgl_pendaftaran,
                           pendaftaran_t.pegawai_id,
                           pendaftaran_t.ruangan_id
                       FROM pendaftaran_t
                       WHERE pendaftaran_t.instalasi_id = 1
                       AND pendaftaran_t.status_periksa::integer NOT IN (402,628)
                       AND pendaftaran_t.pasienbatalperiksa_id IS NULL
                       AND pendaftaran_t.tgl_pendaftaran::date BETWEEN xstart_date AND xend_date) pendaftaran_t ON ruanganpegawai_mp.pegawai_id = pendaftaran_t.pegawai_id AND pendaftaran_t.ruangan_id = ruanganpegawai_mp.ruangan_id
           LEFT JOIN (SELECT 
                           soaprj_t.pendaftaran_id,
                           soaprj_t.pegawai_id,
                           count(soaprj_t.soaprj_id) AS jumlah_soap
                       FROM soaprj_t
                       WHERE soaprj_t.is_deleted = FALSE
                       AND (soaprj_t.subject <> '-'
                           OR soaprj_t.object <> '-'
                           OR soaprj_t.a_diag_utama::json ->> 'text' <> '-'
                           OR soaprj_t.planning <> '-')
                       GROUP BY soaprj_t.pendaftaran_id, soaprj_t.pegawai_id) soap ON pendaftaran_t.pendaftaran_id = soap.pendaftaran_id AND pendaftaran_t.pegawai_id = soap.pegawai_id
           WHERE ruanganpegawai_mp.pegawai_id NOT IN (1,2,3)
           AND ruanganpegawai_mp.is_deleted = FALSE
           AND ruanganpegawai_mp.is_active = TRUE
           UNION ALL
           SELECT
               'RD' AS jenis,
               pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
               instalasi_m.instalasi_id,
               instalasi_m.instalasi_nama AS instalasi,
               ruanganpegawai_mp.ruangan_id,
               ruangan_m.ruangan_nama AS ruangan,
               ruanganpegawai_mp.pegawai_id,
               pegawai_m.nama_pegawai AS dokter,
               pendaftaran_t.no_pendaftaran AS jumlah_pasien,
               COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap
           FROM ruanganpegawai_mp
           JOIN (SELECT
                       pegawai_m.pegawai_id,
                       pegawai_m.nama_pegawai
                   FROM pegawai_m
                   WHERE pegawai_m.kelompokpegawai_id = 1
                   AND pegawai_m.is_deleted = FALSE
                   AND pegawai_m.is_active = TRUE) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
           JOIN (SELECT
                       ruangan_m.ruangan_id,
                       ruangan_m.instalasi_id,
                       ruangan_m.ruangan_nama
                   FROM ruangan_m
                   WHERE ruangan_m.is_deleted = FALSE
                   AND ruangan_m.is_active = TRUE) ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
           JOIN (SELECT
                       instalasi_m.instalasi_id,
                       instalasi_m.instalasi_nama
                   FROM instalasi_m
                   WHERE instalasi_m.is_deleted = FALSE
                   AND instalasi_m.is_active = TRUE) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
           LEFT JOIN (SELECT
                           pendaftaran_t.pendaftaran_id,
                           pendaftaran_t.no_pendaftaran,
                           pendaftaran_t.tgl_pendaftaran,
                           pendaftaran_t.pegawai_id,
                           pendaftaran_t.ruangan_id
                       FROM pendaftaran_t
                       WHERE pendaftaran_t.instalasi_id = 2
                       AND pendaftaran_t.pasienadmisi_id IS NULL
                       AND pendaftaran_t.status_periksa::integer NOT IN (402,628)
                       AND pendaftaran_t.pasienbatalperiksa_id IS NULL
                       AND pendaftaran_t.tgl_pendaftaran::date BETWEEN xstart_date AND xend_date) pendaftaran_t ON ruanganpegawai_mp.pegawai_id = pendaftaran_t.pegawai_id AND pendaftaran_t.ruangan_id = ruanganpegawai_mp.ruangan_id
           LEFT JOIN (SELECT 
                           cppt_t.pendaftaran_id,
                           cppt_t.pegawai_id,
                           count(cppt_t.cppt_id) AS jumlah_soap
                       FROM cppt_t
                       WHERE cppt_t.is_deleted = FALSE 
                       AND cppt_t.pasienadmisi_id IS NULL
                       AND (cppt_t.subject <> '-'
                           OR cppt_t.object <> '-'
                           OR cppt_t.a_diag_utama::json ->> 'text' <> '-'
                           OR cppt_t.planning <> '-')
                       GROUP BY cppt_t.pendaftaran_id, cppt_t.pegawai_id) soap ON pendaftaran_t.pendaftaran_id = soap.pendaftaran_id AND pendaftaran_t.pegawai_id = soap.pegawai_id
           WHERE ruanganpegawai_mp.pegawai_id NOT IN (1,2,3)
           AND ruanganpegawai_mp.is_deleted = FALSE
           AND ruanganpegawai_mp.is_active = TRUE
           UNION ALL
           SELECT
               'RI'::text AS jenis,
               pasienadmisi_t.tgl_pendaftaran::date AS tgl_pendaftaran,
               instalasi_m.instalasi_id,
               instalasi_m.instalasi_nama AS instalasi,
               ruanganpegawai_mp.ruangan_id,
               ruangan_m.ruangan_nama AS ruangan,
               ruanganpegawai_mp.pegawai_id,
               pegawai_m.nama_pegawai AS dokter,
               pasienadmisi_t.no_pendaftaran AS jumlah_pasien,
           COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap
           FROM ruanganpegawai_mp
           JOIN (SELECT
                       pegawai_m.pegawai_id,
                       pegawai_m.nama_pegawai
                   FROM pegawai_m
                   WHERE pegawai_m.kelompokpegawai_id = 1
                   AND pegawai_m.is_deleted = FALSE
                   AND pegawai_m.is_active = TRUE) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
           JOIN (SELECT
                       ruangan_m.ruangan_id,
                       ruangan_m.instalasi_id,
                       ruangan_m.ruangan_nama
                   FROM ruangan_m
                   WHERE ruangan_m.is_deleted = FALSE
                   AND ruangan_m.is_active = TRUE) ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
           JOIN (SELECT
                       instalasi_m.instalasi_id,
                       instalasi_m.instalasi_nama
                   FROM instalasi_m
                   WHERE instalasi_m.is_deleted = FALSE
                   AND instalasi_m.is_active = TRUE) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
           LEFT JOIN (SELECT
                           pasienadmisi_t.pasienadmisi_id,
                           pendaftaran_t.no_pendaftaran,
                           pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                           pasienadmisi_t.pegawai_id,
                           pasienadmisi_t.ruangan_id
                       FROM pasienadmisi_t
                       JOIN (SELECT
                                   pendaftaran_t.pendaftaran_id,
                                   pendaftaran_t.no_pendaftaran
                               FROM pendaftaran_t) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                       WHERE pasienadmisi_t.status_ranap::integer <> 453
                       AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
                       AND pasienadmisi_t.tgl_admisi::date BETWEEN xstart_date AND xend_date) pasienadmisi_t ON pasienadmisi_t.pegawai_id = ruanganpegawai_mp.pegawai_id AND pasienadmisi_t.ruangan_id = ruanganpegawai_mp.ruangan_id
           LEFT JOIN (SELECT 
                           cppt_t.pasienadmisi_id,
                           cppt_t.pegawai_id,
                           count(cppt_t.cppt_id) AS jumlah_soap
                       FROM cppt_t
                       WHERE cppt_t.is_deleted = false 
                       AND cppt_t.pasienadmisi_id IS NOT NULL
                       AND (cppt_t.subject <> '-'
                           OR cppt_t.object <> '-'
                           OR cppt_t.a_diag_utama::json ->> 'text' <> '-'
                           OR cppt_t.planning <> '-')
                       GROUP BY cppt_t.pasienadmisi_id, cppt_t.pegawai_id) soap ON pasienadmisi_t.pasienadmisi_id = soap.pasienadmisi_id AND pasienadmisi_t.pegawai_id = soap.pegawai_id
           WHERE ruanganpegawai_mp.pegawai_id NOT IN (1,2,3)
           AND ruanganpegawai_mp.is_deleted = FALSE
           AND ruanganpegawai_mp.is_active = TRUE
           UNION ALL
           SELECT
               'KONSUL' AS jenis,
               konsul.tgl_pendaftaran,
               konsul.instalasi_id,
               konsul.instalasi,
               konsul.ruangan_id,
               konsul.ruangan,
               konsul.pegawai_id,
               konsul.dokter,
               konsul.jumlah_pasien,
               konsul.jumlah_soap
           FROM (
               SELECT
                   'KONSUL RJ' AS tipe,
                   konsulpoli_t.tgl_konsulpoli::date AS tgl_pendaftaran,
                   instalasi_m.instalasi_id,
                   instalasi_m.instalasi_nama AS instalasi,
                   ruanganpegawai_mp.ruangan_id,
                   ruangan_m.ruangan_nama AS ruangan,
                   ruanganpegawai_mp.pegawai_id,
                   pegawai_m.nama_pegawai AS dokter,
                   pendaftaran_t.no_pendaftaran AS jumlah_pasien,
                   COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap
               FROM ruanganpegawai_mp
               JOIN (SELECT
                           pegawai_m.pegawai_id,
                           pegawai_m.nama_pegawai
                       FROM pegawai_m
                       WHERE pegawai_m.kelompokpegawai_id = 1
                       AND pegawai_m.is_deleted = FALSE
                       AND pegawai_m.is_active = TRUE) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
               JOIN (SELECT
                           ruangan_m.ruangan_id,
                           ruangan_m.instalasi_id,
                           ruangan_m.ruangan_nama
                       FROM ruangan_m
                       WHERE ruangan_m.is_deleted = FALSE
                       AND ruangan_m.is_active = TRUE) ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
               JOIN (SELECT
                           instalasi_m.instalasi_id,
                           instalasi_m.instalasi_nama
                       FROM instalasi_m
                       WHERE instalasi_m.is_deleted = FALSE
                       AND instalasi_m.is_active = TRUE) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
               JOIN (SELECT
                           konsulpoli_t.pendaftaran_id,
                           konsulpoli_t.tgl_konsulpoli,
                           konsulpoli_t.ruangan_id,
                           konsulpoli_t.pegawai_id
                       FROM konsulpoli_t
                       WHERE konsulpoli_t.status_periksa::integer NOT IN (402,628)
                       AND konsulpoli_t.tgl_konsulpoli::DATE BETWEEN xstart_date AND xend_date) konsulpoli_t ON ruanganpegawai_mp.pegawai_id = konsulpoli_t.pegawai_id AND konsulpoli_t.ruangan_id = ruanganpegawai_mp.ruangan_id
               JOIN (SELECT
                           pendaftaran_t.pendaftaran_id,
                           pendaftaran_t.no_pendaftaran
                       FROM pendaftaran_t
                       WHERE pendaftaran_t.instalasi_id = 1
                       AND pendaftaran_t.status_periksa::integer NOT IN (402,628)
                       AND pendaftaran_t.pasienbatalperiksa_id IS NULL) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               LEFT JOIN (SELECT 
                               soaprj_t.pendaftaran_id,
                               soaprj_t.pegawai_id,
                               count(soaprj_t.soaprj_id) AS jumlah_soap
                           FROM soaprj_t
                           WHERE soaprj_t.is_deleted = FALSE
                           AND (soaprj_t.subject <> '-'
                               OR soaprj_t.object <> '-'
                               OR soaprj_t.a_diag_utama::json ->> 'text' <> '-'
                               OR soaprj_t.planning <> '-')
                           GROUP BY soaprj_t.pendaftaran_id, soaprj_t.pegawai_id) soap ON konsulpoli_t.pendaftaran_id = soap.pendaftaran_id AND konsulpoli_t.pegawai_id = soap.pegawai_id
               WHERE ruanganpegawai_mp.pegawai_id NOT IN (1,2,3)
               AND ruanganpegawai_mp.is_deleted = FALSE
               AND ruanganpegawai_mp.is_active = TRUE
               UNION ALL
               SELECT
                   'KONSUL RD' AS tipe,
                   konsulpoli_t.tgl_konsulpoli::date AS tgl_pendaftaran,
                   instalasi_m.instalasi_id,
                   instalasi_m.instalasi_nama AS instalasi,
                   ruanganpegawai_mp.ruangan_id,
                   ruangan_m.ruangan_nama AS ruangan,
                   ruanganpegawai_mp.pegawai_id,
                   pegawai_m.nama_pegawai AS dokter,
                   pendaftaran_t.no_pendaftaran AS jumlah_pasien,
                   COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap
               FROM ruanganpegawai_mp
               JOIN (SELECT
                           pegawai_m.pegawai_id,
                           pegawai_m.nama_pegawai
                       FROM pegawai_m
                       WHERE pegawai_m.kelompokpegawai_id = 1
                       AND pegawai_m.is_deleted = FALSE
                       AND pegawai_m.is_active = TRUE) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
               JOIN (SELECT
                           ruangan_m.ruangan_id,
                           ruangan_m.instalasi_id,
                           ruangan_m.ruangan_nama
                       FROM ruangan_m
                       WHERE ruangan_m.is_deleted = FALSE
                       AND ruangan_m.is_active = TRUE) ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
               JOIN (SELECT
                           instalasi_m.instalasi_id,
                           instalasi_m.instalasi_nama
                       FROM instalasi_m
                       WHERE instalasi_m.is_deleted = FALSE
                       AND instalasi_m.is_active = TRUE) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
               JOIN (SELECT
                           konsulpoli_t.pendaftaran_id,
                           konsulpoli_t.tgl_konsulpoli,
                           konsulpoli_t.ruangan_id,
                           konsulpoli_t.pegawai_id
                       FROM konsulpoli_t
                       WHERE konsulpoli_t.status_periksa::integer NOT IN (402,628)
                       AND konsulpoli_t.tgl_konsulpoli::DATE BETWEEN xstart_date AND xend_date) konsulpoli_t ON ruanganpegawai_mp.pegawai_id = konsulpoli_t.pegawai_id AND konsulpoli_t.ruangan_id = ruanganpegawai_mp.ruangan_id
               JOIN (SELECT
                           pendaftaran_t.pendaftaran_id,
                           pendaftaran_t.no_pendaftaran
                       FROM pendaftaran_t
                       WHERE pendaftaran_t.instalasi_id = 2
                       AND pendaftaran_t.pasienadmisi_id IS NULL
                       AND pendaftaran_t.status_periksa::integer NOT IN (402,628)
                       AND pendaftaran_t.pasienbatalperiksa_id IS NULL) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               LEFT JOIN (SELECT 
                               cppt_t.pendaftaran_id,
                               cppt_t.pegawai_id,
                               count(cppt_t.cppt_id) AS jumlah_soap
                           FROM cppt_t
                           WHERE cppt_t.is_deleted = FALSE 
                           AND cppt_t.pasienadmisi_id IS NULL
                           AND (cppt_t.subject <> '-'
                               OR cppt_t.object <> '-'
                               OR cppt_t.a_diag_utama::json ->> 'text' <> '-'
                               OR cppt_t.planning <> '-')
                           GROUP BY cppt_t.pendaftaran_id, cppt_t.pegawai_id) soap ON konsulpoli_t.pendaftaran_id = soap.pendaftaran_id AND konsulpoli_t.pegawai_id = soap.pegawai_id
               WHERE ruanganpegawai_mp.pegawai_id NOT IN (1,2,3)
               AND ruanganpegawai_mp.is_deleted = FALSE
               AND ruanganpegawai_mp.is_active = TRUE
               UNION ALL
               SELECT
                   'KONSUL RI' AS tipe,
                   permintaankonsul_t.waktu_permintaan::date AS tgl_pendaftaran,
                   instalasi_m.instalasi_id,
                   instalasi_m.instalasi_nama AS instalasi,
                   pasienadmisi_t.ruangan_id,
                   ruangan_m.ruangan_nama AS ruangan,
                   ruanganpegawai_mp.pegawai_id,
                   pegawai_m.nama_pegawai AS dokter,
                   pendaftaran_t.no_pendaftaran AS jumlah_pasien,
                   COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap
               FROM ruanganpegawai_mp
               JOIN (SELECT
                           pegawai_m.pegawai_id,
                           pegawai_m.nama_pegawai
                       FROM pegawai_m
                       WHERE pegawai_m.kelompokpegawai_id = 1
                       AND pegawai_m.is_deleted = FALSE
                       AND pegawai_m.is_active = TRUE) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
               JOIN (SELECT
                           permintaankonsul_t.pasienadmisi_id,
                           permintaankonsul_t.dokter_id,
                           permintaankonsul_t.waktu_permintaan
                       FROM permintaankonsul_t
                       WHERE permintaankonsul_t.is_deleted = FALSE
                       AND permintaankonsul_t.waktu_permintaan::DATE BETWEEN xstart_date AND xend_date) permintaankonsul_t ON ruanganpegawai_mp.pegawai_id = permintaankonsul_t.dokter_id
               JOIN (SELECT
                           pasienadmisi_t.pasienadmisi_id,
                           pasienadmisi_t.pendaftaran_id,
                           pasienadmisi_t.ruangan_id
                       FROM pasienadmisi_t
                       WHERE pasienadmisi_t.status_ranap::integer <> 453
                       AND pasienadmisi_t.pasienbatalperiksa_id IS NULL) pasienadmisi_t ON permintaankonsul_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pasienadmisi_t.ruangan_id = ruanganpegawai_mp.ruangan_id
               JOIN (SELECT
                           pendaftaran_t.pendaftaran_id,
                           pendaftaran_t.no_pendaftaran
                       FROM pendaftaran_t) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               JOIN (SELECT
                           ruangan_m.ruangan_id,
                           ruangan_m.instalasi_id,
                           ruangan_m.ruangan_nama
                       FROM ruangan_m
                       WHERE ruangan_m.is_deleted = FALSE
                       AND ruangan_m.is_active = TRUE) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
               JOIN (SELECT
                           instalasi_m.instalasi_id,
                           instalasi_m.instalasi_nama
                       FROM instalasi_m
                       WHERE instalasi_m.is_deleted = FALSE
                       AND instalasi_m.is_active = TRUE) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
               LEFT JOIN (SELECT 
                               cppt_t.pasienadmisi_id,
                               cppt_t.pegawai_id,
                               count(cppt_t.cppt_id) AS jumlah_soap
                           FROM cppt_t
                           WHERE cppt_t.is_deleted = false 
                           AND cppt_t.pasienadmisi_id IS NOT NULL
                           AND (cppt_t.subject <> '-'
                               OR cppt_t.object <> '-'
                               OR cppt_t.a_diag_utama::json ->> 'text' <> '-'
                               OR cppt_t.planning <> '-')
                           GROUP BY cppt_t.pasienadmisi_id, cppt_t.pegawai_id) soap ON permintaankonsul_t.pasienadmisi_id = soap.pasienadmisi_id AND permintaankonsul_t.dokter_id = soap.pegawai_id
               WHERE ruanganpegawai_mp.pegawai_id NOT IN (1,2,3)
               AND ruanganpegawai_mp.is_deleted = FALSE
               AND ruanganpegawai_mp.is_active = TRUE) konsul;
       END
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230623_152926_migrate_GA379_infopasiensudahsoap_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230623_152926_migrate_GA379_infopasiensudahsoap_fn cannot be reverted.\n";

        return false;
    }
    */
}
