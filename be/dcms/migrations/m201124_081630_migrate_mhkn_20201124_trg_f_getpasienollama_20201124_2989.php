<?php

use yii\db\Migration;

/**
 * Class m201124_081630_migrate_mhkn_20201124_trg_f_getpasienollama_20201124_2989
 */
class m201124_081630_migrate_mhkn_20201124_trg_f_getpasienollama_20201124_2989 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienollama\"(\"xtanggalawal\" date, \"xtanggalakhir\" date, \"xruangan_id\" int4, \"xjeniskelamin\" int4, \"xcarabayar_id\" int4, \"xjenisruangan\" int4)
  RETURNS TABLE(\"pasienbaru\" int4) AS \$BODY\$
BEGIN

IF (xjenisruangan = 722 OR xjenisruangan = 724) --Poliklinik dan IGD UMUM
THEN
    SELECT COUNT(pendaftaranol_t.pendaftaran_id) INTO pasienbaru
    FROM pendaftaranol_t
    JOIN pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
    JOIN pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
    JOIN pasienpulang_t ON pendaftaranol_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
    WHERE pendaftaranol_t.tgl_pendaftaranol::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaranol_t.pendaftaran_id IS NOT NULL
    AND pendaftaranol_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text
    AND pendaftaran_t.pasienpulang_id IS NOT NULL;
END IF;

IF (xjenisruangan = 725) --Penunjang Medis
THEN
    SELECT COUNT(pendaftaranol_t.pendaftaran_id) INTO pasienbaru
    FROM pendaftaranol_t
    JOIN pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
    JOIN pasienmasukpenunjang_t ON pendaftaranol_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
    JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
    WHERE pendaftaranol_t.tgl_pendaftaranol::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaranol_t.pendaftaran_id IS NOT NULL
    AND pendaftaranol_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text
    AND tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL
    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
END IF;

IF (xjenisruangan = 723) --MCU
THEN
    SELECT COUNT(pendaftaranol_t.pendaftaran_id) INTO pasienbaru
    FROM pendaftaranol_t
    JOIN pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
    WHERE pendaftaranol_t.tgl_pendaftaranol::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaranol_t.pendaftaran_id IS NOT NULL
    AND pendaftaranol_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text;
END IF;


    
-- RETURN DATA
RETURN NEXT;

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
            ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_081630_migrate_mhkn_20201124_trg_f_getpasienollama_20201124_2989 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_081630_migrate_mhkn_20201124_trg_f_getpasienollama_20201124_2989 cannot be reverted.\n";

        return false;
    }
    */
}
