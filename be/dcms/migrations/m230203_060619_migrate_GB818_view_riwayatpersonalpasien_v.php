<?php

use yii\db\Migration;

/**
 * Class m230203_060619_migrate_GB818_view_riwayatpersonalpasien_v
 */
class m230203_060619_migrate_GB818_view_riwayatpersonalpasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."riwayatpersonalpasien_v";
        ');

        $this->execute('
            CREATE VIEW "public"."riwayatpersonalpasien_v" AS  SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    obat.riwayat_obat, 
    penyakit.riwayat_penyakit,
    alergi.riwayat_alergi,
    obat_terakhir.riwayat_obat AS riwayat_obat_terakhir,
    penyakit_terakhir.riwayat_penyakit AS riwayat_penyakit_terakhir,
    pasien_m.catatanpenting_pasien
   FROM pasien_m
     LEFT JOIN ( SELECT x.pasien_id,
            string_agg(x.riwayat_obat, \', \'::text) AS riwayat_obat
           FROM ( SELECT 1 AS instalasi_id,
                    anamnesa_t.pasien_id,
                    anamnesa_t.obat_dikonsumsi_nama AS riwayat_obat
                   FROM anamnesa_t
                  WHERE anamnesa_t.obat_dikonsumsi_nama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t.instalasi_id,
                    pendaftaran_t.pasien_id,
                    asesmenperawatrd_t.r_pengobatan AS riwayat_obat
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.instalasi_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE asesmenperawatrd_t.r_pengobatan IS NOT NULL
                UNION ALL
                 SELECT 3 AS instalasi_id,
                    pendaftaran_t.pasien_id,
                    asesmenawal_t.additional_data::json ->> \'r_pengobatan\'::text AS riwayat_obat
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE asesmenawal_t.additional_data IS NOT NULL) x
          GROUP BY x.pasien_id) obat ON pasien_m.pasien_id = obat.pasien_id
     LEFT JOIN ( SELECT x.pasien_id,
            string_agg(x.riwayat_penyakit, \', \'::text) AS riwayat_penyakit
           FROM ( SELECT 1 AS instalasi_id,
                    anamnesa_t.pasien_id,
                    anamnesa_t.riwayat_penyakit_nama AS riwayat_penyakit
                   FROM anamnesa_t
                  WHERE anamnesa_t.riwayat_penyakit_nama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t.instalasi_id,
                    pendaftaran_t.pasien_id,
                    concat(COALESCE(asesmenperawatrd_t.r_penyakitdahulu, \'\'::text, \' \'::text, COALESCE(asesmenperawatrd_t.r_penyakitsaatini, \'\'::text))) AS riwayat_penyakit
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.instalasi_id,
                            a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE asesmenperawatrd_t.r_penyakitdahulu IS NOT NULL OR asesmenperawatrd_t.r_penyakitsaatini IS NOT NULL
                UNION ALL
                 SELECT 3 AS instalasi_id,
                    pendaftaran_t.pasien_id,
                    asesmenawal_t.r_kes_sekarang
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE asesmenawal_t.r_kes_sekarang IS NOT NULL) x
          GROUP BY x.pasien_id) penyakit ON pasien_m.pasien_id = penyakit.pasien_id
     LEFT JOIN ( SELECT x.pasien_id,
            string_agg(x.riwayat_alergi, \', \'::text) AS riwayat_alergi
           FROM ( SELECT anamnesa_t.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(anamnesa_t.alergi_obat, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(anamnesa_t.alergi_obat, \', \')
                        END,
                        CASE
                            WHEN COALESCE(anamnesa_t.alergi_lainnya, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(anamnesa_t.alergi_lainnya, \', \')
                        END,
                        CASE
                            WHEN COALESCE(anamnesa_t.riwayat_alergiobat, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(anamnesa_t.riwayat_alergiobat, \', \')
                        END,
                        CASE
                            WHEN COALESCE(anamnesa_t.alergi_makanan, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(anamnesa_t.alergi_makanan, \', \')
                        END) AS riwayat_alergi
                   FROM anamnesa_t
                  WHERE COALESCE(anamnesa_t.alergi_obat, \'\'::text) <> \'\'::text OR COALESCE(anamnesa_t.alergi_lainnya, \'\'::text) <> \'\'::text OR COALESCE(anamnesa_t.riwayat_alergiobat, \'\'::text) <> \'\'::text OR COALESCE(anamnesa_t.alergi_makanan, \'\'::text) <> \'\'::text
                UNION ALL
                 SELECT pendaftaran_t.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(asesmenperawatrd_t.alergi_obat, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(asesmenperawatrd_t.alergi_obat, \', \')
                        END,
                        CASE
                            WHEN COALESCE(asesmenperawatrd_t.alergi_lainnya, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(asesmenperawatrd_t.alergi_lainnya, \', \')
                        END) AS riwayat_alergi
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE COALESCE(asesmenperawatrd_t.alergi_obat, \'\'::text) <> \'\'::text OR COALESCE(asesmenperawatrd_t.alergi_lainnya, \'\'::text) <> \'\'::text
                UNION ALL
                 SELECT pendaftaran_t.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> \'alergi_obat\'::text, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(asesmenawal_t.additional_data::json ->> \'alergi_obat\'::text, \', \')
                        END,
                        CASE
                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> \'alergi_lainnya\'::text, \'\'::text) = \'\'::text THEN \'\'::text
                            ELSE concat(asesmenawal_t.additional_data::json ->> \'alergi_lainnya\'::text)
                        END) AS riwayat_alergi
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE asesmenawal_t.additional_data IS NOT NULL) x
          GROUP BY x.pasien_id) alergi ON pasien_m.pasien_id = alergi.pasien_id
     LEFT JOIN ( SELECT x.pasien_id,
            x.riwayat_obat,
            x.pendaftaran_id
           FROM ( SELECT 1 AS instalasi_id,
                    anamnesa_t.pasien_id,
                    anamnesa_t.obat_dikonsumsi_nama AS riwayat_obat,
                    anamnesa_t.pendaftaran_id,
                    anamnesa_t.created_date
                   FROM anamnesa_t
                     JOIN ( SELECT max(a.anamesa_id) AS max_anamnesa_id,
                            a.pendaftaran_id
                           FROM anamnesa_t a
                          GROUP BY a.pendaftaran_id) max_anamnesa_t ON anamnesa_t.anamesa_id = max_anamnesa_t.max_anamnesa_id
                UNION ALL
                 SELECT pendaftaran_t.instalasi_id,
                    pendaftaran_t.pasien_id,
                    asesmenperawatrd_t.r_pengobatan AS riwayat_obat,
                    asesmenperawatrd_t.pendaftaran_id,
                    asesmenperawatrd_t.created_date
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.instalasi_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN ( SELECT max(a.asesmenperawatrd_id) AS max_asesmenperawatrd_id,
                            a.pendaftaran_id
                           FROM asesmenperawatrd_t a
                          GROUP BY a.pendaftaran_id) max_asesmenperawatrd_t ON asesmenperawatrd_t.asesmenperawatrd_id = max_asesmenperawatrd_t.max_asesmenperawatrd_id
                UNION ALL
                 SELECT 3 AS instalasi_id,
                    pendaftaran_t.pasien_id,
                    asesmenawal_t.additional_data::json ->> \'r_pengobatan\'::text AS riwayat_obat,
                    asesmenawal_t.pendaftaran_id,
                    asesmenawal_t.created_date
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN ( SELECT max(a.asesmenawal_id) AS max_asesmenawal_id,
                            a.pendaftaran_id
                           FROM asesmenawal_t a
                          GROUP BY a.pendaftaran_id) max_asesmenawal_t ON asesmenawal_t.asesmenawal_id = max_asesmenawal_t.max_asesmenawal_id) x
             JOIN ( SELECT max(y.created_date) AS created_date,
                    y.pasien_id
                   FROM ( SELECT anamnesa_t.pasien_id,
                            max(anamnesa_t.created_date) AS created_date
                           FROM anamnesa_t
                             JOIN ( SELECT max(a.anamesa_id) AS max_anamnesa_id,
                                    a.pendaftaran_id
                                   FROM anamnesa_t a
                                  GROUP BY a.pendaftaran_id) max_anamnesa_t ON anamnesa_t.anamesa_id = max_anamnesa_t.max_anamnesa_id
                          GROUP BY anamnesa_t.pasien_id
                        UNION ALL
                         SELECT pendaftaran_t.pasien_id,
                            max(asesmenperawatrd_t.created_date) AS created_date
                           FROM asesmenperawatrd_t
                             JOIN ( SELECT a.instalasi_id,
                                    a.pendaftaran_id,
                                    a.pasien_id
                                   FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                             JOIN ( SELECT max(a.asesmenperawatrd_id) AS max_asesmenperawatrd_id,
                                    a.pendaftaran_id
                                   FROM asesmenperawatrd_t a
                                  GROUP BY a.pendaftaran_id) max_asesmenperawatrd_t ON asesmenperawatrd_t.asesmenperawatrd_id = max_asesmenperawatrd_t.max_asesmenperawatrd_id
                          GROUP BY pendaftaran_t.pasien_id
                        UNION ALL
                         SELECT pendaftaran_t.pasien_id,
                            max(asesmenawal_t.created_date) AS created_date
                           FROM asesmenawal_t
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.pasien_id
                                   FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                             JOIN ( SELECT max(a.asesmenawal_id) AS max_asesmenawal_id,
                                    a.pendaftaran_id
                                   FROM asesmenawal_t a
                                  GROUP BY a.pendaftaran_id) max_asesmenawal_t ON asesmenawal_t.asesmenawal_id = max_asesmenawal_t.max_asesmenawal_id
                          GROUP BY pendaftaran_t.pasien_id) y
                  GROUP BY y.pasien_id) z ON x.created_date = z.created_date) obat_terakhir ON pasien_m.pasien_id = obat_terakhir.pasien_id
     LEFT JOIN ( SELECT x.pasien_id,
            x.riwayat_penyakit,
            x.pendaftaran_id
           FROM ( SELECT 1 AS instalasi_id,
                    anamnesa_t.pasien_id,
                    anamnesa_t.riwayat_penyakit_nama AS riwayat_penyakit,
                    anamnesa_t.pendaftaran_id,
                    anamnesa_t.created_date
                   FROM anamnesa_t
                     JOIN ( SELECT max(a.anamesa_id) AS max_anamnesa_id,
                            a.pendaftaran_id
                           FROM anamnesa_t a
                          GROUP BY a.pendaftaran_id) max_anamnesa_t ON anamnesa_t.anamesa_id = max_anamnesa_t.max_anamnesa_id
                UNION ALL
                 SELECT pendaftaran_t.instalasi_id,
                    pendaftaran_t.pasien_id,
                    concat(COALESCE(asesmenperawatrd_t.r_penyakitdahulu, \'\'::text, \' \'::text, COALESCE(asesmenperawatrd_t.r_penyakitsaatini, \'\'::text))) AS riwayat_penyakit,
                    asesmenperawatrd_t.pendaftaran_id,
                    asesmenperawatrd_t.created_date
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.instalasi_id,
                            a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN ( SELECT max(a.asesmenperawatrd_id) AS max_asesmenperawatrd_id,
                            a.pendaftaran_id
                           FROM asesmenperawatrd_t a
                          GROUP BY a.pendaftaran_id) max_asesmenperawatrd_t ON asesmenperawatrd_t.asesmenperawatrd_id = max_asesmenperawatrd_t.max_asesmenperawatrd_id
                UNION ALL
                 SELECT 3 AS instalasi_id,
                    pendaftaran_t.pasien_id,
                    asesmenawal_t.r_kes_sekarang,
                    asesmenawal_t.pendaftaran_id,
                    asesmenawal_t.created_date
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN ( SELECT max(a.asesmenawal_id) AS max_asesmenawal_id,
                            a.pendaftaran_id
                           FROM asesmenawal_t a
                          GROUP BY a.pendaftaran_id) max_asesmenawal_t ON asesmenawal_t.asesmenawal_id = max_asesmenawal_t.max_asesmenawal_id) x
             JOIN ( SELECT max(y.created_date) AS created_date,
                    y.pasien_id
                   FROM ( SELECT anamnesa_t.pasien_id,
                            max(anamnesa_t.created_date) AS created_date
                           FROM anamnesa_t
                             JOIN ( SELECT max(a.anamesa_id) AS max_anamnesa_id,
                                    a.pendaftaran_id
                                   FROM anamnesa_t a
                                  GROUP BY a.pendaftaran_id) max_anamnesa_t ON anamnesa_t.anamesa_id = max_anamnesa_t.max_anamnesa_id
                          GROUP BY anamnesa_t.pasien_id
                        UNION ALL
                         SELECT pendaftaran_t.pasien_id,
                            max(asesmenperawatrd_t.created_date) AS created_date
                           FROM asesmenperawatrd_t
                             JOIN ( SELECT a.instalasi_id,
                                    a.pendaftaran_id,
                                    a.pasien_id
                                   FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                             JOIN ( SELECT max(a.asesmenperawatrd_id) AS max_asesmenperawatrd_id,
                                    a.pendaftaran_id
                                   FROM asesmenperawatrd_t a
                                  GROUP BY a.pendaftaran_id) max_asesmenperawatrd_t ON asesmenperawatrd_t.asesmenperawatrd_id = max_asesmenperawatrd_t.max_asesmenperawatrd_id
                          GROUP BY pendaftaran_t.pasien_id
                        UNION ALL
                         SELECT pendaftaran_t.pasien_id,
                            max(asesmenawal_t.created_date) AS created_date
                           FROM asesmenawal_t
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.pasien_id
                                   FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                             JOIN ( SELECT max(a.asesmenawal_id) AS max_asesmenawal_id,
                                    a.pendaftaran_id
                                   FROM asesmenawal_t a
                                  GROUP BY a.pendaftaran_id) max_asesmenawal_t ON asesmenawal_t.asesmenawal_id = max_asesmenawal_t.max_asesmenawal_id
                          GROUP BY pendaftaran_t.pasien_id) y
                  GROUP BY y.pasien_id) z ON x.created_date = z.created_date) penyakit_terakhir ON pasien_m.pasien_id = penyakit_terakhir.pasien_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230203_060619_migrate_GB818_view_riwayatpersonalpasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230203_060619_migrate_GB818_view_riwayatpersonalpasien_v cannot be reverted.\n";

        return false;
    }
    */
}
