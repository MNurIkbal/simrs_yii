<?php

use yii\db\Migration;

/**
 * Class m201104_080014_migrate_mhkn_20201104_f_getkeluarhidup
 */
class m201104_080014_migrate_mhkn_20201104_f_getkeluarhidup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE FUNCTION "public"."f_getkeluarhidup"("xtanggal" date, "xruangan_id" int4, "xkelaspelayanan_id" int4)
  RETURNS TABLE("keluar_hidup" int4) AS $BODY$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7);
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
        echo "m201104_080014_migrate_mhkn_20201104_f_getkeluarhidup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_080014_migrate_mhkn_20201104_f_getkeluarhidup cannot be reverted.\n";

        return false;
    }
    */
}
