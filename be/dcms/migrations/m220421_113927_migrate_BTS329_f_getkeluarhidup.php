<?php

use yii\db\Migration;

/**
 * Class m220421_113927_migrate_BTS329_f_getkeluarhidup
 */
class m220421_113927_migrate_BTS329_f_getkeluarhidup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getkeluarhidup;
        ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluarhidup\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4, \"xstatus_pasien\" int4)
  RETURNS TABLE(\"keluar_hidup\" int4) AS \$BODY\$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
    FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
   FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
 FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
FROM pendaftaran_t 
JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        AND ((to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date >= ( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k));
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
        echo "m220421_113927_migrate_BTS329_f_getkeluarhidup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220421_113927_migrate_BTS329_f_getkeluarhidup cannot be reverted.\n";

        return false;
    }
    */
}
