<?php

use yii\db\Migration;

/**
 * Class m220404_063225_migrate_BTS215_f_getkeluarmeninggalkur48
 */
class m220404_063225_migrate_BTS215_f_getkeluarmeninggalkur48 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getkeluarmeninggalkur48;
            ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluarmeninggalkur48\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4, \"xstatus_pasien\" int4)
  RETURNS TABLE(\"keluar_meninggalkur48\" int4) AS \$BODY\$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
--     AND pasienpulang_t.lama_rawat <= 2
        AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
    AND pasienpulang_t.carakeluar_id = 4;
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
        echo "m220404_063225_migrate_BTS215_f_getkeluarmeninggalkur48 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220404_063225_migrate_BTS215_f_getkeluarmeninggalkur48 cannot be reverted.\n";

        return false;
    }
    */
}
