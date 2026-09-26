<?php

use yii\db\Migration;

/**
 * Class m250205_041133_migrate_or_7_new_laporanpasienrujukkeri_fn
 */
class m250205_041133_migrate_or_7_new_laporanpasienrujukkeri_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.new_laporanpasienrujukkeri_fn(date, date);");
        $this->execute("
            CREATE OR REPLACE FUNCTION public.new_laporanpasienrujukkeri_fn(xfirstdate date, xlastdate date)
            RETURNS TABLE(tglpasienpulang date, pasienrjkeri integer, pasienrdkeri integer, jumlah integer)
            LANGUAGE plpgsql
            IMMUTABLE
            AS \$function\$
                BEGIN
                    WITH
                        pasienrj AS (
                            SELECT
                                min(pasienpulang_t.tglpasienpulang) AS tanggal_pulang,
                                COUNT(pendaftaran_t.pendaftaran_id) AS count_pasienrj
                            FROM pendaftaran_t
                            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                            JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                            WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
                            AND pasienpulang_t.carakeluar_id = 5
                            AND pendaftaran_t.instalasi_id = 1 --RJ
                            AND pendaftaran_t.is_active = TRUE 
                            AND pendaftaran_t.is_deleted = FALSE			
                        ),
                        pasienrd AS (
                            SELECT
                                min(pasienpulang_t.tglpasienpulang) as tanggal_pulang,
                                COUNT(pendaftaran_t.pendaftaran_id) as count_pasienrd
                            FROM pendaftaran_t
                            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                            JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                            WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
                            AND pasienpulang_t.carakeluar_id = 5
                            AND pendaftaran_t.instalasi_id = 2 --RD
                            AND pendaftaran_t.is_active = TRUE 
                            AND pendaftaran_t.is_deleted = FALSE
                        ),
                        pasienrujukranap AS (
                            SELECT
                                LEAST(
                                    (select tanggal_pulang::date from pasienrj),
                                    (select tanggal_pulang::date from pasienrd)
                                ) AS tglpasienpulang,
                                (select count_pasienrj from pasienrj) AS pasienrjkeri,
                                (select count_pasienrd from pasienrd) AS pasienrdkeri		
                        )
                    SELECT
                        pasienrujukranap.tglpasienpulang,
                        pasienrujukranap.pasienrjkeri,
                        pasienrujukranap.pasienrdkeri,
                        (pasienrujukranap.pasienrjkeri + pasienrujukranap.pasienrdkeri) AS jumlah
                    INTO
                        tglpasienpulang,
                        pasienrjkeri,
                        pasienrdkeri,
                        jumlah
                    FROM pasienrujukranap;
                    RETURN NEXT;
                END;
            \$function\$
            ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250205_041133_migrate_or_7_new_laporanpasienrujukkeri_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250205_041133_migrate_or_7_new_laporanpasienrujukkeri_fn cannot be reverted.\n";

        return false;
    }
    */
}
