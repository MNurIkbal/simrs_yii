<?php

use yii\db\Migration;

/**
 * Class m230207_121213_migrate_GB822_laporansensusharianrajal_fn
 */
class m230207_121213_migrate_GB822_laporansensusharianrajal_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.laporansensusharianrajal_fn(xfirstdate date, xlastdate date, xjenis_pendaftaran integer)
        RETURNS TABLE(instalasi_id integer, ruangan_id integer, ruangan_nama character varying, jenis_ruangan integer, carabayar_id integer, carabayar_nama character varying, baru_laki integer, baru_perempuan integer, jml_baru integer, lama_laki integer, lama_perempuan integer, jml_lama integer, kunjungan integer, hp integer, adoa integer, adoad integer, adoapp integer)
        LANGUAGE plpgsql
        IMMUTABLE
       AS \$function\$
             
       DECLARE
       --jenis_ruangan int4;
       baru_laki_semualangsung int4;
       baru_laki_semuaonline int4;
       baru_perempuan_semualangsung int4;
       baru_perempuan_semuaonline int4;
       lama_laki_semualangsung int4;
       lama_laki_semuaonline int4;
       lama_perempuan_semualangsung int4;
       lama_perempuan_semuaonline int4;
       
       BEGIN
       -- IF (xfirstdate = xlastdate)
       -- THEN
               IF xjenis_pendaftaran = 726 --Pendaftaran Langsung
               THEN
                   FOR instalasi_id, ruangan_id, ruangan_nama, jenis_ruangan IN
                       SELECT ruangan_m.instalasi_id, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.jenis_ruangan from ruangan_m WHERE ruangan_m.jenis_ruangan IS NOT NULL
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
       -- 			SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;
                   END LOOP;
           END IF;
           
           IF xjenis_pendaftaran=727 --Pendaftaran Online
               THEN
                   FOR ruangan_id, ruangan_nama, jenis_ruangan IN
                       SELECT ruangan_m.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.jenis_ruangan from ruangan_m WHERE ruangan_m.jenis_ruangan IS NOT NULL
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
       -- 			SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;
                   END LOOP;
           END IF;
       
           IF xjenis_pendaftaran=1145 --Pendaftaran Semua (Online & Langsung)
               THEN 
                   FOR instalasi_id, ruangan_id, ruangan_nama, jenis_ruangan IN
                       SELECT ruangan_m.instalasi_id, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.jenis_ruangan FROM ruangan_m WHERE ruangan_m.jenis_ruangan IS NOT NULL
                   LOOP
                       FOR carabayar_id, carabayar_nama IN
                           SELECT * FROM carabayar_m WHERE is_active = TRUE AND is_deleted = FALSE
                       LOOP
                           SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', '180', carabayar_id, jenis_ruangan) INTO baru_laki_semualangsung;
       
                           SELECT * from f_getpasienolbaru(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', carabayar_id, jenis_ruangan) INTO baru_laki_semuaonline;		
       
                           SELECT baru_laki_semualangsung + baru_laki_semuaonline INTO baru_laki;	
       
                           SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', '180', carabayar_id, jenis_ruangan) INTO baru_perempuan_semualangsung;			
       
                           SELECT * from f_getpasienolbaru(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', carabayar_id, jenis_ruangan) INTO baru_perempuan_semuaonline;
       
                           SELECT baru_perempuan_semualangsung + baru_perempuan_semuaonline INTO baru_perempuan;			
       
                           SELECT baru_laki + baru_perempuan INTO jml_baru;	
       
                           SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', '181', carabayar_id, jenis_ruangan) INTO lama_laki_semualangsung;
       
                           SELECT * from f_getpasienollama(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '15', carabayar_id, jenis_ruangan) INTO lama_laki_semuaonline;
       
                           SELECT lama_laki_semualangsung + lama_laki_semuaonline INTO lama_laki;
       
                           SELECT * from f_getpasien(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', '181', carabayar_id, jenis_ruangan) INTO lama_perempuan_semualangsung;					
       
                           SELECT * from f_getpasienollama(xfirstdate::DATE, xlastdate::DATE, ruangan_id, '16', carabayar_id, jenis_ruangan) INTO lama_perempuan_semuaonline;
       
                           SELECT lama_perempuan_semualangsung + lama_perempuan_semuaonline INTO lama_perempuan;		
       
                           SELECT lama_laki + lama_perempuan INTO jml_lama;			
       
                           SELECT jml_baru + jml_lama INTO kunjungan;		
       
                           SELECT count(vidhari) from f_gethp(xfirstdate, xlastdate, ruangan_id) WHERE jumlah_jadwal IS NOT NULL INTO hp;			
       
                           SELECT 0 INTO adoa;
                           
                           SELECT 0 INTO adoad;
                           
                           SELECT 0 INTO adoapp;
       
                           RETURN NEXT;
       
                       END LOOP;
       
                   END LOOP;
           
           END IF;
       
       -- END IF;
       END
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230207_121213_migrate_GB822_laporansensusharianrajal_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230207_121213_migrate_GB822_laporansensusharianrajal_fn cannot be reverted.\n";

        return false;
    }
    */
}
