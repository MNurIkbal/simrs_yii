<?php

use yii\db\Migration;

/**
 * Class m201104_075137_migrate_mhkn_20201104_f_getpasienawal
 */
class m201104_075137_migrate_mhkn_20201104_f_getpasienawal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('CREATE OR REPLACE FUNCTION "public"."f_getpasienawal"("xtanggal" date, "xruangan_id" int4, "xkelaspelayanan_id" int4)
  RETURNS TABLE("pasien_awal" int4) AS $BODY$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    LEFT JOIN pendaftaran_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE
    AND pasienadmisi_t.pasienpulang_id IS NULL
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.ruangan_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    LEFT JOIN pendaftaran_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE
    AND pasienadmisi_t.pasienpulang_id IS NULL
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    LEFT JOIN pendaftaran_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE
    AND pasienadmisi_t.pasienpulang_id IS NULL
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.ruangan_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    LEFT JOIN pendaftaran_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE
    AND pasienadmisi_t.pasienpulang_id IS NULL
    AND pasienadmisi_t.status_ranap != 453;
END IF;
    
-- RETURN DATA
RETURN NEXT;

END
$BODY$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
                ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_075137_migrate_mhkn_20201104_f_getpasienawal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_075137_migrate_mhkn_20201104_f_getpasienawal cannot be reverted.\n";

        return false;
    }
    */
}
