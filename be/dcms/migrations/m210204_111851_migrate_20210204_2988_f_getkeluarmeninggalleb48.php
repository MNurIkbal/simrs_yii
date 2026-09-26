<?php

use yii\db\Migration;

/**
 * Class m210204_111851_migrate_20210204_2988_f_getkeluarmeninggalleb48
 */
class m210204_111851_migrate_20210204_2988_f_getkeluarmeninggalleb48 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getkeluarmeninggalleb48;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluarmeninggalleb48\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
            RETURNS TABLE(\"keluar_meninggalleb48\" int4) AS \$BODY\$
            BEGIN

            IF (xruangan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 5
            AND pasienpulang_t.ruanganakhir_id = xruangan_id;
            END IF;

            IF (xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 5
            AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 5
            AND pasienpulang_t.ruanganakhir_id = xruangan_id
            AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 5;
            END IF;

            -- RETURN DATA
            RETURN NEXT;

            END
            \$BODY\$
            LANGUAGE plpgsql IMMUTABLE
            COST 100
            ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210204_111851_migrate_20210204_2988_f_getkeluarmeninggalleb48 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210204_111851_migrate_20210204_2988_f_getkeluarmeninggalleb48 cannot be reverted.\n";

        return false;
    }
    */
}
