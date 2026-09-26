<?php

use yii\db\Migration;

/**
 * Class m230614_043632_migrate_GA254_infopasiensudahsoap_fn
 */
class m230614_043632_migrate_GA254_infopasiensudahsoap_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."infopasiensudahsoap_fn";');
        $this->execute("CREATE OR REPLACE FUNCTION public.infopasiensudahsoap_fn(xstart_date date, xend_date date)
        RETURNS TABLE(jenis text, tgl_pendaftaran date, instalasi_id integer, ruangan_id integer, ruangan character varying, pegawai_id integer, dokter character varying, jumlah_pasien character varying, jumlah_soap bigint, instalasi character varying)
        LANGUAGE plpgsql
       AS \$function\$
       
       BEGIN
       RETURN query
       SELECT 
         'RJ'::text AS jenis,
           pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
           pendaftaran_t.instalasi_id,
           pendaftaran_t.ruangan_id,
           pendaftaran_t.ruangan,
           pendaftaran_t.pegawai_id,
           pegawai_m.nama_pegawai AS dokter,
           pendaftaran_t.no_pendaftaran AS jumlah_pasien,
           COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap,
           pendaftaran_t.instalasi
       FROM pegawai_m
       LEFT JOIN (SELECT 
               pendaftaran_t.pendaftaran_id,
                     pendaftaran_t.tgl_pendaftaran,
                     pendaftaran_t.no_pendaftaran,
                     pendaftaran_t.pegawai_id,
                     pendaftaran_t.instalasi_id,
                     pendaftaran_t.ruangan_id,
                     ruangan_m.ruangan_nama AS ruangan,
                                   instalasi_m.instalasi_nama AS instalasi
             FROM pendaftaran_t
             JOIN (SELECT
                   ruangan_m.ruangan_id,
                             ruangan_m.ruangan_nama,
                             ruangan_m.instalasi_id
                          FROM ruangan_m) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN (SELECT 
                   instalasi_m.instalasi_id,
                   instalasi_m.instalasi_nama
                      FROM instalasi_m) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 WHERE pendaftaran_t.instalasi_id = 1
             AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) 
                 AND pendaftaran_t.pasienbatalperiksa_id IS NULL
                 AND pendaftaran_t.tgl_pendaftaran::date BETWEEN xstart_date AND xend_date) pendaftaran_t ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
       LEFT JOIN (SELECT 
               soaprj_t.pendaftaran_id,
                   soaprj_t.pegawai_id,
                   count(soaprj_t.soaprj_id) AS jumlah_soap
                  FROM soaprj_t
                 WHERE soaprj_t.is_deleted = FALSE
                 AND (soaprj_t.subject <> '-'
                   OR soaprj_t.\"object\" <> '-'
                   OR soaprj_t.a_diag_utama::json ->> 'text' <> '-'
                   OR soaprj_t.planning <> '-')
                 GROUP BY soaprj_t.pendaftaran_id, soaprj_t.pegawai_id) soap ON pendaftaran_t.pendaftaran_id = soap.pendaftaran_id AND pegawai_m.pegawai_id = soap.pegawai_id
       WHERE pegawai_m.kelompokpegawai_id = 1
       AND pegawai_m.pegawai_id NOT IN (1,2,3)
       AND pegawai_m.is_deleted = FALSE
       AND pegawai_m.is_active = TRUE
       UNION ALL
       SELECT
         'RD'::text AS jenis,
           pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
           pendaftaran_t.instalasi_id,
           pendaftaran_t.ruangan_id,
           pendaftaran_t.ruangan,
           pendaftaran_t.pegawai_id,
           pegawai_m.nama_pegawai AS dokter,
           pendaftaran_t.no_pendaftaran AS jumlah_pasien,
           COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap,
           pendaftaran_t.instalasi
       FROM pegawai_m
       LEFT JOIN (SELECT 
               pendaftaran_t.pendaftaran_id,
                   pendaftaran_t.tgl_pendaftaran,
                   pendaftaran_t.no_pendaftaran,
                   pendaftaran_t.pegawai_id,
                   pendaftaran_t.instalasi_id,
                   pendaftaran_t.ruangan_id,
                   ruangan_m.ruangan_nama AS ruangan,
                   instalasi_m.instalasi_nama AS instalasi
                  FROM pendaftaran_t
                  JOIN (SELECT 
                        ruangan_m.ruangan_id,
                           ruangan_m.ruangan_nama,
                             ruangan_m.instalasi_id
                          FROM ruangan_m) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                           JOIN (SELECT 
                                       instalasi_m.instalasi_id,
                                       instalasi_m.instalasi_nama
                                            FROM instalasi_m) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
              WHERE pendaftaran_t.instalasi_id = 2 
              AND pendaftaran_t.pasienadmisi_id IS NULL 
              AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) 
              AND pendaftaran_t.pasienbatalperiksa_id IS NULL
              AND pendaftaran_t.tgl_pendaftaran::date BETWEEN xstart_date AND xend_date) pendaftaran_t ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
       LEFT JOIN (SELECT 
               cppt_t.pendaftaran_id,
                   cppt_t.pegawai_id,
                   count(cppt_t.cppt_id) AS jumlah_soap
                FROM cppt_t
                 WHERE cppt_t.is_deleted = FALSE 
                 AND cppt_t.pasienadmisi_id IS NULL
                 AND (cppt_t.subject <> '-'
                   OR cppt_t.\"object\" <> '-'
                   OR cppt_t.a_diag_utama::json ->> 'text' <> '-'
                   OR cppt_t.planning <> '-')
       GROUP BY cppt_t.pendaftaran_id, cppt_t.pegawai_id) soap ON pendaftaran_t.pendaftaran_id = soap.pendaftaran_id AND pegawai_m.pegawai_id = soap.pegawai_id
       WHERE pegawai_m.kelompokpegawai_id = 1
       AND pegawai_m.pegawai_id NOT IN (1,2,3)
       AND pegawai_m.is_deleted = FALSE
       AND pegawai_m.is_active = TRUE
       UNION ALL
       SELECT 
         'RI'::text AS jenis,
           pasienadmisi_t.tgl_pendaftaran::date AS tgl_pendaftaran,
           pasienadmisi_t.instalasi_id,
           pasienadmisi_t.ruangan_id,
           pasienadmisi_t.ruangan,
           pasienadmisi_t.pegawai_id,
           pegawai_m.nama_pegawai AS dokter,
           pasienadmisi_t.no_pendaftaran AS jumlah_pasien,
           COALESCE(soap.jumlah_soap, 0::bigint) AS jumlah_soap,
           pasienadmisi_t.instalasi
       FROM pegawai_m
       LEFT JOIN (SELECT 
               pasienadmisi_t.pendaftaran_id,
                     pasienadmisi_t.pasienadmisi_id,
                     pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                     pendaftaran_t.no_pendaftaran,
                     pasienadmisi_t.pegawai_id,
                     instalasi_m.instalasi_id,
                     pasienadmisi_t.ruangan_id,
                     ruangan_m.ruangan_nama AS ruangan,
                                   instalasi_m.instalasi_nama AS instalasi
                    FROM pasienadmisi_t
             JOIN (SELECT 
                   pendaftaran_t.pendaftaran_id,
                     pendaftaran_t.tgl_pendaftaran,
                     pendaftaran_t.no_pendaftaran
                    FROM pendaftaran_t) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN (SELECT 
                   ruangan_m.ruangan_id,
                         ruangan_m.instalasi_id,
                         ruangan_m.ruangan_nama
                    FROM ruangan_m) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN (SELECT 
                   instalasi_m.instalasi_id,
                   instalasi_m.instalasi_nama
                      FROM instalasi_m) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
               WHERE pasienadmisi_t.status_ranap <> 453 
               AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
               AND pasienadmisi_t.tgl_admisi::date BETWEEN xstart_date AND xend_date) pasienadmisi_t ON pegawai_m.pegawai_id = pasienadmisi_t.pegawai_id
       LEFT JOIN (SELECT 
               cppt_t.pasienadmisi_id,
                   cppt_t.pegawai_id,
                   count(cppt_t.cppt_id) AS jumlah_soap
                  FROM cppt_t
                 WHERE cppt_t.is_deleted = false 
                 AND cppt_t.pasienadmisi_id IS NOT NULL
                 AND (cppt_t.subject <> '-'
                   OR cppt_t.\"object\" <> '-'
                   OR cppt_t.a_diag_utama::json ->> 'text' <> '-'
                   OR cppt_t.planning <> '-')
                 GROUP BY cppt_t.pasienadmisi_id, cppt_t.pegawai_id) soap ON pasienadmisi_t.pasienadmisi_id = soap.pasienadmisi_id AND pegawai_m.pegawai_id = soap.pegawai_id
       WHERE pegawai_m.kelompokpegawai_id = 1
       AND pegawai_m.pegawai_id NOT IN (1,2,3)
       AND pegawai_m.is_deleted = FALSE
       AND pegawai_m.is_active = TRUE;
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
        echo "m230614_043632_migrate_GA254_infopasiensudahsoap_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_043632_migrate_GA254_infopasiensudahsoap_fn cannot be reverted.\n";

        return false;
    }
    */
}
