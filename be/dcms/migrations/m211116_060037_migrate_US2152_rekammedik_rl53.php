<?php

use yii\db\Migration;

/**
 * Class m211116_060037_migrate_US2152_rekammedik_rl53
 */
class m211116_060037_migrate_US2152_rekammedik_rl53 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.rl_pgetdiagnosajumlahranap;
            ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"rl_pgetdiagnosajumlahranap\"(\"xtahun\" varchar, \"xbulan\" varchar=''::character varying)
  RETURNS TABLE(\"diagnosa_id\" int4, \"kode_diagnosa\" varchar, \"nama_diagnosa\" varchar, \"jumlah_lakihidup\" int8, \"jumlah_perempuanhidup\" int8, \"jumlah_lakimati\" int8, \"jumlah_perempuanmati\" int8, \"jumlah_hidup_mati\" int8, \"tahun\" varchar, \"bulan\" varchar) AS \$BODY\$ 
BEGIN
    RETURN QUERY 
        SELECT 
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_namalainnya,
            COALESCE(jml_lakihidup,0)::int8 AS jml_lakihidup,
            COALESCE(jml_perempuanhidup,0)::int8 AS jml_perempuanhidup,
            COALESCE(jml_lakimati,0)::int8 AS jml_lakimati, 
            COALESCE(jml_perempuanmati,0)::int8 AS jml_perempuanmati,
            COALESCE(jml_lakihidup,0)::int8  + COALESCE(jml_perempuanhidup,0)::int8 + COALESCE(jml_lakimati,0)::int8 + COALESCE(jml_perempuanmati,0)::int8 AS jml_hidup_mati,
            xtahun,
            xbulan
        FROM diagnosa_m
        LEFT JOIN (
            SELECT
                koreksidiagnosa_t.diagnosa_id,
                COUNT(*) AS jml_lakihidup
            FROM koreksidiagnosa_t
            JOIN 
            (
                SELECT 
                    pasien_m.pasien_id,
                    pasien_m.jeniskelamin::int AS jeniskelamin
                FROM pasien_m
            ) AS pasien ON koreksidiagnosa_t.pasien_id = pasien.pasien_id
            JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN pasienadmisi_t ON koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id 
            WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 --Kelompok diagnosa utama
            AND koreksidiagnosa_t.pasienadmisi_id IS NOT NULL --Flag pasien ranap
            AND koreksidiagnosa_t.is_deleted IS FALSE
            AND pasien.jeniskelamin = 15
            AND COALESCE(pasienpulang_t.carakeluar_id,1) <> 4
            AND to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::VARCHAR(10)) = xtahun
            AND CASE WHEN xbulan <> ''
            THEN to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::VARCHAR(10)) = xbulan
            ELSE pendaftaran_t.is_deleted = FALSE
            END
            GROUP BY koreksidiagnosa_t.diagnosa_id
        ) AS tbl_jmllakihidup ON diagnosa_m.diagnosa_id = tbl_jmllakihidup.diagnosa_id
        LEFT JOIN (
            SELECT
                koreksidiagnosa_t.diagnosa_id,
                COUNT(*) AS jml_perempuanhidup
            FROM koreksidiagnosa_t
            JOIN 
            (
                SELECT 
                    pasien_m.pasien_id,
                    pasien_m.jeniskelamin::int AS jeniskelamin
                FROM pasien_m
            ) AS pasien ON koreksidiagnosa_t.pasien_id = pasien.pasien_id
            JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN pasienadmisi_t ON koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id 
            WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 --Kelompok diagnosa utama
            AND koreksidiagnosa_t.pasienadmisi_id IS NOT NULL --Flag pasien ranap
            AND koreksidiagnosa_t.is_deleted IS FALSE
            AND pasien.jeniskelamin = 16
            AND COALESCE(pasienpulang_t.carakeluar_id,1) <> 4
            AND to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::VARCHAR(10)) = xtahun
            AND CASE WHEN xbulan <> ''
            THEN to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::VARCHAR(10)) = xbulan
            ELSE pendaftaran_t.is_deleted = FALSE
            END
            GROUP BY koreksidiagnosa_t.diagnosa_id
        ) AS tbl_jmlperempuanhidup ON diagnosa_m.diagnosa_id = tbl_jmlperempuanhidup.diagnosa_id
        LEFT JOIN (
            SELECT
                koreksidiagnosa_t.diagnosa_id,
                COUNT(*) AS jml_lakimati
            FROM koreksidiagnosa_t
            JOIN 
            (
                SELECT 
                    pasien_m.pasien_id,
                    pasien_m.jeniskelamin::int AS jeniskelamin
                FROM pasien_m
            ) AS pasien ON koreksidiagnosa_t.pasien_id = pasien.pasien_id
            JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN pasienadmisi_t ON koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id 
            WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 --Kelompok diagnosa utama
            AND koreksidiagnosa_t.pasienadmisi_id IS NOT NULL --Flag pasien ranap
            AND koreksidiagnosa_t.is_deleted IS FALSE
            AND pasien.jeniskelamin = 15
            AND COALESCE(pasienpulang_t.carakeluar_id,1) = 4
            AND to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::VARCHAR(10)) = xtahun
            AND CASE WHEN xbulan <> ''
            THEN to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::VARCHAR(10)) = xbulan
            ELSE pendaftaran_t.is_deleted = FALSE
            END
            GROUP BY koreksidiagnosa_t.diagnosa_id
        ) AS tbl_jmllakimati ON diagnosa_m.diagnosa_id = tbl_jmllakimati.diagnosa_id
        LEFT JOIN (
            SELECT
                koreksidiagnosa_t.diagnosa_id,
                COUNT(*) AS jml_perempuanmati
            FROM koreksidiagnosa_t
            JOIN 
            (
                SELECT 
                    pasien_m.pasien_id,
                    pasien_m.jeniskelamin::int AS jeniskelamin
                FROM pasien_m
            ) AS pasien ON koreksidiagnosa_t.pasien_id = pasien.pasien_id
            JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN pasienadmisi_t ON koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id 
            WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 --Kelompok diagnosa utama
            AND koreksidiagnosa_t.pasienadmisi_id IS NOT NULL --Flag pasien ranap
            AND koreksidiagnosa_t.is_deleted IS FALSE
            AND pasien.jeniskelamin = 16
            AND COALESCE(pasienpulang_t.carakeluar_id,1) = 4
            AND to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::VARCHAR(10)) = xtahun
            AND CASE WHEN xbulan <> ''
            THEN to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::VARCHAR(10)) = xbulan
            ELSE pendaftaran_t.is_deleted = FALSE
            END
            GROUP BY koreksidiagnosa_t.diagnosa_id
        ) AS tbl_jmlperempuanmati ON diagnosa_m.diagnosa_id = tbl_jmlperempuanmati.diagnosa_id
        WHERE COALESCE(jml_lakihidup,0)::int8  + COALESCE(jml_perempuanhidup,0)::int8 + COALESCE(jml_lakimati,0)::int8 + COALESCE(jml_perempuanmati,0)::int8 > 0
        ORDER BY COALESCE(jml_lakihidup,0)  + COALESCE(jml_perempuanhidup,0) + COALESCE(jml_lakimati,0) + COALESCE(jml_perempuanmati,0) DESC
        LIMIT 10;

END; \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211116_060037_migrate_US2152_rekammedik_rl53 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_060037_migrate_US2152_rekammedik_rl53 cannot be reverted.\n";

        return false;
    }
    */
}
