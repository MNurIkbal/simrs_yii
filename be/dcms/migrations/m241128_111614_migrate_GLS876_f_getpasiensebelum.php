<?php

use yii\db\Migration;

/**
 * Class m241128_111614_migrate_GLS876_f_getpasiensebelum
 */
class m241128_111614_migrate_GLS876_f_getpasiensebelum extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.f_getpasiensebelum(date, int4, int4);");
        $this->execute("CREATE OR REPLACE FUNCTION public.f_getpasiensebelum(xtanggal date, xruangan_id integer, xkelaspelayanan_id integer)
 RETURNS integer
 LANGUAGE plpgsql
AS \$function\$

        DECLARE
            vpasien_masuk int4;
            vkeluar_hidup int4;
            vmeninggal_kurang_48 int4;
            vmeninggal_lebih_48 int4;

        BEGIN
----- PASIEN MASUK
            SELECT
                count(*) INTO vpasien_masuk
            FROM pasienadmisi_t
            WHERE pasienadmisi_t.status_ranap <> 453
            AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
			AND pasienadmisi_t.ruangan_id = xruangan_id
			AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
            AND pasienadmisi_t.tgl_admisi::date BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) AND (xtanggal - INTERVAL '1 day');

----- PASIEN KELUAR HIDUP + RUJUK RS LAIN
            SELECT
                count(*) INTO vkeluar_hidup
            FROM pasienadmisi_t
            JOIN (SELECT
                        a.pasienpulang_id,
                        a.tglpasienpulang
                    FROM pasienpulang_t a
                    WHERE a.is_deleted = FALSE
                    AND a.pasienbatalpulang_id IS NULL
                    AND a.carakeluar_id IN (1,2,3,6)) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
			WHERE pasienadmisi_t.ruangan_id = xruangan_id
			AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
            AND pasienpulang_t.tglpasienpulang::date BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) AND (xtanggal - INTERVAL '1 day');

----- PASIEN KELUAR MENINGGAL KURANG 48 JAM + DEATH ON ARRIVAL
            SELECT
                count(*) INTO vmeninggal_kurang_48
            FROM pasienadmisi_t
            JOIN (SELECT
                        a.pasienpulang_id,
                        a.tglpasienpulang
                    FROM pasienpulang_t a
                    WHERE a.is_deleted = FALSE
                    AND a.pasienbatalpulang_id IS NULL
                    AND a.carakeluar_id = 4
                    AND a.kondisikeluar_id IN (6,7)) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
			WHERE pasienadmisi_t.ruangan_id = xruangan_id
			AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
            AND pasienpulang_t.tglpasienpulang::date BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) AND (xtanggal - INTERVAL '1 day');

----- PASIEN MENINGGAL LEBIH 48 JAM
            SELECT
                count(*) INTO vmeninggal_lebih_48
            FROM pasienadmisi_t
            JOIN (SELECT
                        a.pasienpulang_id,
                        a.tglpasienpulang
                    FROM pasienpulang_t a
                    WHERE a.is_deleted = FALSE
                    AND a.pasienbatalpulang_id IS NULL
                    AND a.carakeluar_id = 4
                    AND a.kondisikeluar_id = 5) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
			WHERE pasienadmisi_t.ruangan_id = xruangan_id
			AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
            AND pasienpulang_t.tglpasienpulang::date BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) AND (xtanggal - INTERVAL '1 day');

            RETURN vpasien_masuk - vkeluar_hidup - vmeninggal_kurang_48 - vmeninggal_lebih_48;
        END
        \$function\$
;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241128_111614_migrate_GLS876_f_getpasiensebelum cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241128_111614_migrate_GLS876_f_getpasiensebelum cannot be reverted.\n";

        return false;
    }
    */
}
