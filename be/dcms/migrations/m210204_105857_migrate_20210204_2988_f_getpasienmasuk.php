<?php

use yii\db\Migration;

/**
 * Class m210204_105857_migrate_20210204_2988_f_getpasienmasuk
 */
class m210204_105857_migrate_20210204_2988_f_getpasienmasuk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getpasienmasuk;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienmasuk\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
            RETURNS TABLE(\"pasien_masuk\" int4) AS \$BODY\$
            BEGIN

            IF (xruangan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
            FROM pasienadmisi_t
            JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
            AND pasienadmisi_t.status_ranap != 453
            AND pendaftaran_t.pasienbatalperiksa_id IS NULL
            AND pasienadmisi_t.ruangan_id = xruangan_id;
            END IF;

            IF (xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
            FROM pasienadmisi_t
            JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
            AND pasienadmisi_t.status_ranap != 453
            AND pendaftaran_t.pasienbatalperiksa_id IS NULL
            AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
            FROM pasienadmisi_t
            JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
            AND pasienadmisi_t.status_ranap != 453
            AND pendaftaran_t.pasienbatalperiksa_id IS NULL
            AND pasienadmisi_t.ruangan_id = xruangan_id
            AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
            FROM pasienadmisi_t
            JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
            AND pendaftaran_t.pasienbatalperiksa_id IS NULL;
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
        echo "m210204_105857_migrate_20210204_2988_f_getpasienmasuk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210204_105857_migrate_20210204_2988_f_getpasienmasuk cannot be reverted.\n";

        return false;
    }
    */
}
