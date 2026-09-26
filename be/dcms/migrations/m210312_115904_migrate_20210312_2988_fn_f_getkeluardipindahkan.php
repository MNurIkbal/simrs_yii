<?php

use yii\db\Migration;

/**
 * Class m210312_115904_migrate_20210312_2988_fn_f_getkeluardipindahkan
 */
class m210312_115904_migrate_20210312_2988_fn_f_getkeluardipindahkan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluardipindahkan\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
            RETURNS TABLE(\"keluar_dipindahkan\" int4) AS \$BODY\$
            BEGIN

            IF (xruangan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_dipindahkan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = xtanggal::DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            AND masukkamar_t.ruangan_id = xruangan_id;
            END IF;

            IF (xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_dipindahkan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = xtanggal::DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_dipindahkan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = xtanggal::DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            AND masukkamar_t.ruangan_id = xruangan_id
            AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_dipindahkan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = xtanggal::DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL;
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
        echo "m210312_115904_migrate_20210312_2988_fn_f_getkeluardipindahkan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_115904_migrate_20210312_2988_fn_f_getkeluardipindahkan cannot be reverted.\n";

        return false;
    }
    */
}
