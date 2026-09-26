<?php

use yii\db\Migration;

/**
 * Class m220727_120548_migrate_MHG3013_f_gethasilakhirsensus
 */
class m220727_120548_migrate_MHG3013_f_gethasilakhirsensus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
     $this->execute("
        DROP FUNCTION if exists public.f_gethasilakhirsensus;
        ");
     $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_gethasilakhirsensus\"(\"xtanggal\" date)
  RETURNS TABLE(\"hasil_sensus\" int4) AS \$BODY\$

DECLARE
    pasien_masuk int4;
    pasien_keluarhidup int4;
    pasien_keluarmeninggalkur48 int4;
    pasien_keluarmeninggalleb48 int4;
    dirujuk_rs_lain int4;

BEGIN

        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
                FROM pasienadmisi_t
            JOIN (SELECT a.pasienadmisi_id,
                                                 a.pendaftaran_id
                                    FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            JOIN (SELECT a.pasienadmisi_id,
                                                 a.tgl_masukkamar
                                    FROM masukkamar_t a) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
                WHERE pasienadmisi_t.tgl_admisi::DATE BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) and xtanggal
                AND pasienadmisi_t.status_ranap != 453
                AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
                AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone);

        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_keluarhidup
                FROM pendaftaran_t 
            JOIN (SELECT a.pasienadmisi_id,
                                                 a.pendaftaran_id,
                                                 a.pasienpulang_id,
                                                 a.tgl_admisi
                                    FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            JOIN (SELECT a.pasienpulang_id,
                                                 a.carakeluar_id,
                                                 a.pasienadmisi_id,
                                                 a.tglpasienpulang
                                    FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
        WHERE pasienpulang_t.tglpasienpulang::date BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) and xtanggal
                AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienadmisi_t.tgl_admisi >= (SELECT set_tgl_sensus FROM konfigsystem_k);
    
        SELECT 
                        COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_keluarmeninggalkur48
                FROM pasienadmisi_t
                        JOIN (SELECT a.pasienpulang_id,
                                                 a.tglpasienpulang,
                                                 a.pasienadmisi_id,
                                                 a.carakeluar_id,
                                                 a.kondisikeluar_id
                                    FROM pasienpulang_t a) pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) and xtanggal
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 4
                AND pasienpulang_t.kondisikeluar_id IN (6,7)
                AND pasienadmisi_t.tgl_admisi >= (SELECT set_tgl_sensus FROM konfigsystem_k);
        
        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_keluarmeninggalleb48
                FROM pasienadmisi_t
            JOIN (SELECT a.pasienpulang_id,
                                                 a.tglpasienpulang,
                                                 a.pasienadmisi_id,
                                                 a.carakeluar_id,
                                                 a.kondisikeluar_id
                                    FROM pasienpulang_t a) pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) and xtanggal
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 4
                AND pasienpulang_t.kondisikeluar_id = 5
                AND pasienadmisi_t.tgl_admisi >= (SELECT set_tgl_sensus FROM konfigsystem_k);               

        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO dirujuk_rs_lain
                FROM pasienadmisi_t
            LEFT JOIN (SELECT a.pasienpulang_id,
                                                            a.tglpasienpulang,
                                                            a.pasienadmisi_id,
                                                            a.carakeluar_id,
                                                            a.kondisikeluar_id
                                            FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN (SELECT set_tgl_sensus FROM konfigsystem_k) and xtanggal
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 2
                AND pasienpulang_t.kondisikeluar_id = 3 ;

        SELECT
            pasien_masuk - pasien_keluarhidup - pasien_keluarmeninggalkur48 - pasien_keluarmeninggalleb48 - dirujuk_rs_lain INTO hasil_sensus;

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
        echo "m220727_120548_migrate_MHG3013_f_gethasilakhirsensus cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220727_120548_migrate_MHG3013_f_gethasilakhirsensus cannot be reverted.\n";

        return false;
    }
    */
}
