<?php

use yii\db\Migration;

/**
 * Class m220421_113859_migrate_BTS329_f_getpasienmasuk
 */
class m220421_113859_migrate_BTS329_f_getpasienmasuk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getpasienmasuk;
        ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienmasuk\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4, \"xstatus_pasien\" int4)
  RETURNS TABLE(\"pasien_masuk\" int4) AS \$BODY\$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.ruangan_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
   FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.ruangan_id = xruangan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
   FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.ruangan_id = xruangan_id
    AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.ruangan_id = xruangan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
    AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
    FROM pendaftaran_t
    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
    WHERE pasienadmisi_t.tgl_admisi::DATE = xtanggal::DATE
    AND pasienadmisi_t.status_ranap != 453
    AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone);
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
        echo "m220421_113859_migrate_BTS329_f_getpasienmasuk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220421_113859_migrate_BTS329_f_getpasienmasuk cannot be reverted.\n";

        return false;
    }
    */
}
