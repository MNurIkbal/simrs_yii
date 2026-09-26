<?php

use yii\db\Migration;

/**
 * Class m201113_082225_migrate_mhkn_20201113_f_getpasienawal
 */
class m201113_082225_migrate_mhkn_20201113_f_getpasienawal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienawal\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
  RETURNS TABLE(\"pasien_awal\" int4) AS \$BODY\$

DECLARE
    pasien_pulang int4;
    pasien_pindah int4;

BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND ((pasienadmisi_t.tgl_pulang IS NULL) OR (pasienadmisi_t.tgl_pulang::DATE > xtanggal::DATE) OR (pasienadmisi_t.tgl_pulang::DATE = xtanggal::DATE))
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.ruangan_id = xruangan_id;
    
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_pulang
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND pasienpulang_t.tglpasienpulang::DATE >xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.ruangan_id = xruangan_id;
    
    SELECT COUNT(pindahkamar_t.pasienadmisi_id) INTO pasien_pindah
    FROM pindahkamar_t
    LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pindahkamar_t.pasienadmisi_id
    WHERE ((pasienadmisi_t.tgl_admisi::DATE > xtanggal::DATE) AND (pindahkamar_t.tgl_pindahkamar::DATE < xtanggal::DATE))
    AND pindahkamar_t.ruangan_id = xruangan_id;
    
    pasien_awal := pasien_awal - pasien_pulang - pasien_pindah;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND ((pasienadmisi_t.tgl_pulang IS NULL) OR (pasienadmisi_t.tgl_pulang::DATE > xtanggal::DATE) OR (pasienadmisi_t.tgl_pulang::DATE = xtanggal::DATE))
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_pulang
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND pasienpulang_t.tglpasienpulang::DATE >xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    SELECT COUNT(pindahkamar_t.pasienadmisi_id) INTO pasien_pindah
    FROM pindahkamar_t
    LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pindahkamar_t.pasienadmisi_id
    WHERE ((pasienadmisi_t.tgl_admisi::DATE > xtanggal::DATE) AND (pindahkamar_t.tgl_pindahkamar::DATE < xtanggal::DATE))
    AND pindahkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    pasien_awal := pasien_awal - pasien_pulang - pasien_pindah;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND ((pasienadmisi_t.tgl_pulang IS NULL) OR (pasienadmisi_t.tgl_pulang::DATE > xtanggal::DATE) OR (pasienadmisi_t.tgl_pulang::DATE = xtanggal::DATE))
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.ruangan_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_pulang
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND pasienpulang_t.tglpasienpulang::DATE >xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.ruangan_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    SELECT COUNT(pindahkamar_t.pasienadmisi_id) INTO pasien_pindah
    FROM pindahkamar_t
    LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pindahkamar_t.pasienadmisi_id
    WHERE ((pasienadmisi_t.tgl_admisi::DATE > xtanggal::DATE) AND (pindahkamar_t.tgl_pindahkamar::DATE < xtanggal::DATE))
    AND pindahkamar_t.ruangan_id = xruangan_id
    AND pindahkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    pasien_awal := pasien_awal - pasien_pulang - pasien_pindah;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_awal
    FROM pasienadmisi_t
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND ((pasienadmisi_t.tgl_pulang IS NULL) OR (pasienadmisi_t.tgl_pulang::DATE > xtanggal::DATE) OR (pasienadmisi_t.tgl_pulang::DATE = xtanggal::DATE))
    AND pasienadmisi_t.status_ranap != 453;
    
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_pulang
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE (pasienadmisi_t.is_deleted = FALSE)
    AND (pasienadmisi_t.tgl_admisi::DATE < xtanggal::DATE)
    AND pasienpulang_t.tglpasienpulang::DATE >xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453;
    
    SELECT COUNT(pindahkamar_t.pasienadmisi_id) INTO pasien_pindah
    FROM pindahkamar_t
    LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pindahkamar_t.pasienadmisi_id
    WHERE ((pasienadmisi_t.tgl_admisi::DATE > xtanggal::DATE) AND (pindahkamar_t.tgl_pindahkamar::DATE < xtanggal::DATE))
    AND pindahkamar_t.ruangan_id = xruangan_id
    AND pindahkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
    
    pasien_awal := pasien_awal - pasien_pulang - pasien_pindah;
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
        echo "m201113_082225_migrate_mhkn_20201113_f_getpasienawal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201113_082225_migrate_mhkn_20201113_f_getpasienawal cannot be reverted.\n";

        return false;
    }
    */
}
