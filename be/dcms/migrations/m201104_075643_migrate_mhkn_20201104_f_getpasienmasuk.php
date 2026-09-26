<?php

use yii\db\Migration;

/**
 * Class m201104_075643_migrate_mhkn_20201104_f_getpasienmasuk
 */
class m201104_075643_migrate_mhkn_20201104_f_getpasienmasuk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('CREATE OR REPLACE FUNCTION "public"."f_getpasienmasuk"("xtanggal" date, "xruangan_id" int4, "xkelaspelayanan_id" int4)
  RETURNS TABLE("pasien_masuk" int4) AS $BODY$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pendaftaran_id) INTO pasien_masuk
    FROM pasienadmisi_t
    WHERE tgl_admisi::DATE = xtanggal::DATE
    AND status_ranap != 453
    AND ruangan_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pendaftaran_id) INTO pasien_masuk
    FROM pasienadmisi_t
    WHERE tgl_admisi::DATE = xtanggal::DATE
    AND status_ranap != 453
    AND kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pendaftaran_id) INTO pasien_masuk
    FROM pasienadmisi_t
    WHERE tgl_admisi::DATE = xtanggal::DATE
    AND status_ranap != 453
    AND ruangan_id = xruangan_id
    AND kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
THEN
    SELECT COUNT(pendaftaran_id) INTO pasien_masuk
    FROM pasienadmisi_t
    WHERE tgl_admisi::DATE = xtanggal::DATE
    AND status_ranap != 453;
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
        echo "m201104_075643_migrate_mhkn_20201104_f_getpasienmasuk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_075643_migrate_mhkn_20201104_f_getpasienmasuk cannot be reverted.\n";

        return false;
    }
    */
}
