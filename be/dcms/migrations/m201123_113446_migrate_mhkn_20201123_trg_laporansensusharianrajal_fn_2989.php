<?php

use yii\db\Migration;

/**
 * Class m201123_113446_migrate_mhkn_20201123_trg_laporansensusharianrajal_fn_2989
 */
class m201123_113446_migrate_mhkn_20201123_trg_laporansensusharianrajal_fn_2989 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS "public"."laporansensusharianrajal_fn"("xfirstdate" date, "xlastdate" date, "xjenis_pendaftaran" int4);
        ');

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"laporansensusharianrajal_fn\"(\"xfirstdate\" date, \"xlastdate\" date, \"xjenis_pendaftaran\" int4)
  RETURNS TABLE(\"ruangan_id\" int4, \"ruangan_nama\" varchar, \"jenis_ruangan\" int4, \"carabayar_id\" int4, \"carabayar_nama\" varchar, \"baru_laki\" int4, \"baru_perempuan\" int4, \"jml_baru\" int4, \"lama_laki\" int4, \"lama_perempuan\" int4, \"jml_lama\" int4, \"kunjungan\" int4, \"hp\" int4, \"adoa\" int4, \"adoad\" int4, \"adoapp\" int4) AS \$BODY\$

DECLARE
--jenis_ruangan int4;
BEGIN



-- IF (xfirstdate = xlastdate)
-- THEN
        IF xjenis_pendaftaran=726 --Pendaftaran Langsung
        THEN
            FOR ruangan_id, ruangan_nama, jenis_ruangan IN
                SELECT ruangan_m.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.jenis_ruangan from ruangan_m WHERE ruangan_m.jenis_ruangan IS NOT NULL AND is_active = TRUE AND is_deleted = FALSE
            LOOP 
                FOR carabayar_id, carabayar_nama IN
                    SELECT * FROM carabayar_m WHERE is_active = TRUE AND is_deleted = FALSE 
                LOOP
                    SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', '180', carabayar_id, jenis_ruangan) INTO baru_laki;
                    
                    SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', '180', carabayar_id, jenis_ruangan) INTO baru_perempuan;
                    
                    SELECT baru_laki + baru_perempuan INTO jml_baru;
                    
                    SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', '181', carabayar_id, jenis_ruangan) INTO lama_laki;
                    
                    SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', '181', carabayar_id, jenis_ruangan) INTO lama_perempuan;
                    
                    SELECT lama_laki + lama_perempuan INTO jml_lama;
                    
                    SELECT jml_baru + jml_lama INTO kunjungan;
                    
                    SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;
                    SELECT 0 INTO adoa;
                    
                    SELECT 0 INTO adoad;
                    
                    SELECT 0 INTO adoapp;
                     RETURN NEXT;
                END LOOP;
--          SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;
            END LOOP;
    END IF;
    
    IF xjenis_pendaftaran=727 --Pendaftaran Online
        THEN
            FOR ruangan_id, ruangan_nama, jenis_ruangan IN
                SELECT ruangan_m.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.jenis_ruangan from ruangan_m WHERE ruangan_m.jenis_ruangan IS NOT NULL AND is_active = TRUE AND is_deleted = FALSE
            LOOP 
                FOR carabayar_id, carabayar_nama IN
                    SELECT * FROM carabayar_m WHERE is_active = TRUE AND is_deleted = FALSE 
                LOOP
                    SELECT * from f_getpasienolbaru(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', carabayar_id, jenis_ruangan) INTO baru_laki;
                    
                    SELECT * from f_getpasienolbaru(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', carabayar_id, jenis_ruangan) INTO baru_perempuan;
                    
                    SELECT baru_laki + baru_perempuan INTO jml_baru;
                    
                    SELECT * from f_getpasienollama(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', carabayar_id, jenis_ruangan) INTO lama_laki;
                    
                    SELECT * from f_getpasienollama(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', carabayar_id, jenis_ruangan) INTO lama_perempuan;
                    
                    SELECT lama_laki + lama_perempuan INTO jml_lama;
                    
                    SELECT jml_baru + jml_lama INTO kunjungan;
                    
                    SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;
                    SELECT 0 INTO adoa;
                    
                    SELECT 0 INTO adoad;
                    
                    SELECT 0 INTO adoapp;
                     RETURN NEXT;
                END LOOP;
--          SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;
            END LOOP;
    END IF;
    
-- END IF;
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
        echo "m201123_113446_migrate_mhkn_20201123_trg_laporansensusharianrajal_fn_2989 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_113446_migrate_mhkn_20201123_trg_laporansensusharianrajal_fn_2989 cannot be reverted.\n";

        return false;
    }
    */
}
