<?php

use yii\db\Migration;

/**
 * Class m220913_065951_migrate_MHG3048_laporanrekapkinerjaprofesional_fn
 */
class m220913_065951_migrate_MHG3048_laporanrekapkinerjaprofesional_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute("
        DROP FUNCTION if exists public.laporanrekapkinerjaprofesional_fn;
        ");
       $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"laporanrekapkinerjaprofesional_fn\"(\"xstart_date\" date, \"xend_date\" date, \"xkelaspelayanan_id\" int4, \"xruangan_id\" int4)
  RETURNS TABLE(\"pasien_awal\" int4, \"pasien_masuk\" int4, \"pasien_keluarhidup\" int4, \"pasien_pindahan\" int4, \"pasien_pindahkan\" int4, \"pasien_keluarmeninggalkur48\" int4, \"pasien_keluarmeninggalleb48\" int4, \"dirujuk_rs_lain\" int4, \"hp\" int4, \"los\" int4, \"alos\" int4, \"bed_tersedia\" int4, \"bor\" int4, \"toi\" int4, \"bto\" float8, \"ndr\" float8, \"gdr\" float8) AS \$BODY\$

DECLARE
--     pasien_masuk int4;
--     pasien_keluarhidup int4;
--     pasien_keluarmeninggalkur48 int4;
--     pasien_keluarmeninggalleb48 int4;
--     dirujuk_rs_lain int4;


BEGIN
IF(xruangan_id != 0)
THEN
        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
                FROM pasienadmisi_t
            JOIN (SELECT a.pasienadmisi_id,
                         a.pendaftaran_id
                  FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            JOIN (SELECT a.pasienadmisi_id,
                         a.tgl_masukkamar,
                                                 a.kelaspelayanan_id,
                                                 a.ruangan_id
                  FROM masukkamar_t a) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
                WHERE pasienadmisi_t.tgl_admisi::DATE BETWEEN xstart_date and xend_date
                AND pasienadmisi_t.status_ranap != 453
                AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
                                AND masukkamar_t.ruangan_id = xruangan_id
                AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone);

        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_keluarhidup
                FROM pendaftaran_t 
            JOIN (SELECT a.pasienadmisi_id,
                         a.pendaftaran_id,
                         a.pasienpulang_id,
                         a.tgl_admisi,
                                                 a.kelaspelayanan_id,
                                                 a.ruangan_id
                  FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            JOIN (SELECT a.pasienpulang_id,
                         a.carakeluar_id,
                         a.pasienadmisi_id,
                         a.tglpasienpulang
                  FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
        WHERE pasienpulang_t.tglpasienpulang::date BETWEEN xstart_date and xend_date
                AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
                                AND pasienadmisi_t.ruangan_id = xruangan_id
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
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xstart_date and xend_date
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 4
                AND pasienpulang_t.kondisikeluar_id IN (6,7)
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
                                AND pasienadmisi_t.ruangan_id = xruangan_id
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
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xstart_date and xend_date
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 4
                AND pasienpulang_t.kondisikeluar_id = 5
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
                                AND pasienadmisi_t.ruangan_id = xruangan_id
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
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xstart_date and xend_date
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 2
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
                                AND pasienadmisi_t.ruangan_id = xruangan_id
                AND pasienpulang_t.kondisikeluar_id IN (3) ;
                                
                SELECT 
                COUNT(pasienadmisi_t.pasienadmisi_id) INTO pasien_pindahan
            FROM pasienadmisi_t
                        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            JOIN pindahkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE BETWEEN xstart_date and xend_date
                        AND pindahkamar_t.kelaspelayanan_id = xkelaspelayanan_id
                        AND pindahkamar_t.ruangan_id = xruangan_id;
                        
                SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_pindahkan
            FROM pasienadmisi_t
            LEFT JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            WHERE masukkamar_t.tgl_keluarkamar::DATE BETWEEN xstart_date AND xend_date
                        AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
                        AND masukkamar_t.pindahkamar_id IS NOT NULL
                        AND masukkamar_t.ruangan_id = xruangan_id;
                        
             SELECT      
--         a.tgl_generate AS tanggal,
           COALESCE(SUM(a.jumlah),0) INTO los
        FROM
                (SELECT 
                tanggal.tgl_generate,
                (
                    SELECT 
                                    SUM(pasienadmisi_t.hari) AS hari
               FROM (pendaftaran_t
                 JOIN ( SELECT a.pasienadmisi_id,
                                                                        a.tgl_admisi,
                                    a.tgl_pendaftaran,
                                                                        a.tgl_pulang,
                                    a.pasien_id,
                                    masukkamar_t.kelaspelayanan_id,
--                                                                      a.tgl_admisi AS tgl_masukkamar,
--                                                                      a.tgl_pulang AS tgl_keluarkamar,
                                                                        masukkamar_t.tgl_masukkamar,
                                                                        masukkamar_t.tgl_keluarkamar,
                                    CASE
                                        WHEN masukkamar_t.tgl_keluarkamar::date > tgl_generate THEN 1
--                                         WHEN masukkamar_t.tgl_keluarkamar::date IS NULL THEN SUM(tgl_generate - masukkamar_t.tgl_masukkamar::date) + 1
                                        WHEN masukkamar_t.tgl_keluarkamar::date = masukkamar_t.tgl_masukkamar::date THEN SUM(masukkamar_t.tgl_keluarkamar::date - masukkamar_t.tgl_masukkamar::date) + 1
                                        ELSE
                                        SUM(masukkamar_t.tgl_keluarkamar::date - masukkamar_t.tgl_masukkamar::date)
                                    END AS hari
                       FROM pasienadmisi_t a
                                             JOIN (SELECT a.pasienadmisi_id,
                                                                        a.kelaspelayanan_id,
                                                                        a.tgl_masukkamar,
                                                                        a.tgl_keluarkamar
                                                            FROM masukkamar_t a) masukkamar_t ON a.pasienadmisi_id = masukkamar_t.pasienadmisi_id
                                 WHERE tgl_admisi >= (SELECT set_tgl_sensus FROM konfigsystem_k)
                                                                 AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
                                 GROUP BY a.pasienadmisi_id, masukkamar_t.tgl_keluarkamar, masukkamar_t.tgl_masukkamar, masukkamar_t.kelaspelayanan_id) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                                 WHERE  pasienadmisi_t.tgl_pulang::DATE = tgl_generate 
                ) as jumlah
            from (SELECT CURRENT_DATE + i AS tgl_generate
                FROM generate_series(date (xstart_date) - CURRENT_DATE, 
                   date (xend_date)  - CURRENT_DATE ) i) as tanggal ) a ;
                                     
                                      SELECT
                        COUNT(kamarruangan_m.kamarruangan_id) INTO bed_tersedia
                    FROM kamartempattidur_m
                                            JOIN (SELECT a.kamarruangan_id,
                                                                     a.is_rekapkinerjaprofesi,
                                                                     a.kelaspelayanan_id,
                                                                     a.is_deleted,
                                                                     a.is_active,
                                                                     a.ruangan_id
                                                        FROM kamarruangan_m a) kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
            --          AND kamarruangan_m.is_kamarthruput=TRUE
                        AND kamarruangan_m.is_rekapkinerjaprofesi=TRUE
                        AND kamarruangan_m.is_deleted=FALSE 
                        AND kamarruangan_m.is_active=TRUE
                                                AND kamarruangan_m.kelaspelayanan_id = xkelaspelayanan_id
                                                AND kamarruangan_m.ruangan_id = xruangan_id
                    WHERE kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true;
                    
                                        

--         SELECT
--             pasien_masuk - pasien_keluarhidup - pasien_keluarmeninggalkur48 - pasien_keluarmeninggalleb48 - dirujuk_rs_lain INTO hasil_sensus;
                        
                SELECT f_getpasienawalrekapkinerja(xstart_date-1, xkelaspelayanan_id) INTO pasien_awal;
                
                SELECT pasien_awal + pasien_masuk - pasien_keluarhidup - pasien_keluarmeninggalkur48 - pasien_keluarmeninggalleb48 - dirujuk_rs_lain INTO hp;
                
                SELECT 
                CASE 
                    WHEN los = 0 THEN 0 
                    WHEN pasien_keluarhidup = 0 THEN 0 
                ELSE
                    los / (pasien_keluarhidup + pasien_keluarmeninggalkur48 + pasien_keluarmeninggalleb48 + dirujuk_rs_lain) 
                END INTO alos;
                
                SELECT 
                CASE WHEN bed_tersedia = 0 THEN 0
                ELSE
                COALESCE(hp / (bed_tersedia::FLOAT)*100,0) 
                END INTO bor;
                
                SELECT 
                CASE WHEN pasien_keluarhidup = 0 THEN 0
                ELSE
                (bed_tersedia - hp) / (pasien_keluarhidup + pasien_keluarmeninggalkur48 + pasien_keluarmeninggalleb48 + dirujuk_rs_lain) 
                END INTO toi;
                
                SELECT 
                CASE WHEN pasien_keluarhidup = 0 THEN 0
                ELSE (pasien_keluarhidup + pasien_keluarmeninggalkur48 + pasien_keluarmeninggalleb48 + dirujuk_rs_lain) / (bed_tersedia)::FLOAT 
                END INTO bto;
                
                SELECT 
                CASE WHEN pasien_keluarmeninggalleb48 = 0 THEN 0 
                ELSE
                pasien_keluarmeninggalleb48 / (pasien_keluarhidup + dirujuk_rs_lain + pasien_keluarmeninggalleb48)::FLOAT 
                END INTO ndr;
                
                SELECT 
                CASE WHEN pasien_keluarmeninggalleb48 = 0 THEN 0 
                ELSE
                (pasien_keluarmeninggalleb48 + pasien_keluarmeninggalkur48)  / (pasien_keluarhidup + dirujuk_rs_lain + pasien_keluarmeninggalleb48 + pasien_keluarmeninggalkur48)::FLOAT 
                END INTO gdr;
                
                ELSE
                
                SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_masuk
                FROM pasienadmisi_t
            JOIN (SELECT a.pasienadmisi_id,
                         a.pendaftaran_id
                  FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            JOIN (SELECT a.pasienadmisi_id,
                         a.tgl_masukkamar,
                                                 a.kelaspelayanan_id
                  FROM masukkamar_t a) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
                WHERE pasienadmisi_t.tgl_admisi::DATE BETWEEN xstart_date and xend_date
                AND pasienadmisi_t.status_ranap != 453
                AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
--                              AND pasienadmisi_t.ruangan_id = xruangan_id
                AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone);

        SELECT 
            COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_keluarhidup
                FROM pendaftaran_t 
            JOIN (SELECT a.pasienadmisi_id,
                         a.pendaftaran_id,
                         a.pasienpulang_id,
                         a.tgl_admisi,
                                                 a.kelaspelayanan_id,
                                                 a.ruangan_id
                  FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            JOIN (SELECT a.pasienpulang_id,
                         a.carakeluar_id,
                         a.pasienadmisi_id,
                         a.tglpasienpulang
                  FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
        WHERE pasienpulang_t.tglpasienpulang::date BETWEEN xstart_date and xend_date
                AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
--                              AND pasienadmisi_t.ruangan_id = xruangan_id
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
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xstart_date and xend_date
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 4
                AND pasienpulang_t.kondisikeluar_id IN (6,7)
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
--                              AND pasienadmisi_t.ruangan_id = xruangan_id
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
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xstart_date and xend_date
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 4
                AND pasienpulang_t.kondisikeluar_id = 5
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
--                              AND pasienadmisi_t.ruangan_id = xruangan_id
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
                WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xstart_date and xend_date
                AND pasienpulang_t.pasienadmisi_id IS NOT NULL
                AND pasienpulang_t.carakeluar_id = 2
                                AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
--                              AND pasienadmisi_t.ruangan_id = xruangan_id
                AND pasienpulang_t.kondisikeluar_id IN (3) ;
                                
                SELECT 
                COUNT(pasienadmisi_t.pasienadmisi_id) INTO pasien_pindahan
            FROM pasienadmisi_t
                        JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            JOIN pindahkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE BETWEEN xstart_date and xend_date
                        AND pindahkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
--                      AND pindahkamar_t.ruangan_id = xruangan_id;
                        
                SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) INTO pasien_pindahkan
            FROM pasienadmisi_t
            LEFT JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            WHERE masukkamar_t.tgl_keluarkamar::DATE BETWEEN xstart_date AND xend_date
                        AND masukkamar_t.pindahkamar_id IS NOT NULL
                        AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id;
--                      AND masukkamar_t.ruangan_id = xruangan_id;
                        
--              SELECT      
-- --         a.tgl_generate AS tanggal,
--            COALESCE(SUM(a.jumlah),0) INTO los
--         FROM
--                 (SELECT 
--                 tanggal.tgl_generate,
--                 (
--                     SELECT 
--                                     SUM(pasienadmisi_t.hari) AS hari
--                FROM (pendaftaran_t
--                  JOIN ( SELECT a.pasienadmisi_id,
--                                                                      a.tgl_admisi,
--                                     a.tgl_pendaftaran,
--                                                                      a.tgl_pulang,
--                                     a.pasien_id,
--                                     a.kelaspelayanan_id,
--                                                                      a.tgl_admisi AS tgl_masukkamar,
--                                                                      a.tgl_pulang AS tgl_keluarkamar,
--                                     CASE
--                                         WHEN a.tgl_pulang::date > tgl_generate THEN 1
--                                         WHEN a.tgl_pulang::date IS NULL THEN SUM(tgl_generate - a.tgl_admisi::date) + 1
--                                         WHEN a.tgl_pulang::date = a.tgl_admisi::date THEN SUM(a.tgl_pulang::date - a.tgl_admisi::date) + 1
--                                         ELSE
--                                         SUM(a.tgl_pulang::date - a.tgl_admisi::date)
--                                     END AS hari
--                        FROM pasienadmisi_t a
--                                  WHERE tgl_admisi >= (SELECT set_tgl_sensus FROM konfigsystem_k)
--                                                               AND a.kelaspelayanan_id = xkelaspelayanan_id
--                                  GROUP BY a.pasienadmisi_id) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
--                                  WHERE  pasienadmisi_t.tgl_pulang::DATE = tgl_generate 
--                 ) as jumlah
--             from (SELECT CURRENT_DATE + i AS tgl_generate
--                 FROM generate_series(date (xstart_date) - CURRENT_DATE, 
--                    date (xend_date)  - CURRENT_DATE ) i) as tanggal ) a ;

SELECT      
--         a.tgl_generate AS tanggal,
           COALESCE(SUM(a.jumlah),0) INTO los
        FROM
                (SELECT 
                tanggal.tgl_generate,
                (
                    SELECT 
                                    SUM(pasienadmisi_t.hari) AS hari
               FROM (pendaftaran_t
                 JOIN ( SELECT a.pasienadmisi_id,
                                                                        a.tgl_admisi,
                                    a.tgl_pendaftaran,
                                                                        a.tgl_pulang,
                                    a.pasien_id,
                                    masukkamar_t.kelaspelayanan_id,
--                                                                      a.tgl_admisi AS tgl_masukkamar,
--                                                                      a.tgl_pulang AS tgl_keluarkamar,
                                                                        masukkamar_t.tgl_masukkamar,
                                                                        masukkamar_t.tgl_keluarkamar,
                                    CASE
                                        WHEN masukkamar_t.tgl_keluarkamar::date > tgl_generate THEN 1
--                                         WHEN masukkamar_t.tgl_keluarkamar::date IS NULL THEN SUM(tgl_generate - masukkamar_t.tgl_masukkamar::date) + 1
                                        WHEN masukkamar_t.tgl_keluarkamar::date = masukkamar_t.tgl_masukkamar::date THEN SUM(masukkamar_t.tgl_keluarkamar::date - masukkamar_t.tgl_masukkamar::date) + 1
                                        ELSE
                                        SUM(masukkamar_t.tgl_keluarkamar::date - masukkamar_t.tgl_masukkamar::date)
                                    END AS hari
                       FROM pasienadmisi_t a
                                             JOIN (SELECT a.pasienadmisi_id,
                                                                        a.kelaspelayanan_id,
                                                                        a.tgl_masukkamar,
                                                                        a.tgl_keluarkamar
                                                            FROM masukkamar_t a) masukkamar_t ON a.pasienadmisi_id = masukkamar_t.pasienadmisi_id
                                 WHERE tgl_admisi >= (SELECT set_tgl_sensus FROM konfigsystem_k)
                                                                 AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
                                 GROUP BY a.pasienadmisi_id, masukkamar_t.tgl_keluarkamar, masukkamar_t.tgl_masukkamar, masukkamar_t.kelaspelayanan_id) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                                 WHERE  pasienadmisi_t.tgl_pulang::DATE = tgl_generate 
                ) as jumlah
            from (SELECT CURRENT_DATE + i AS tgl_generate
                FROM generate_series(date (xstart_date) - CURRENT_DATE, 
                   date (xend_date)  - CURRENT_DATE ) i) as tanggal ) a ;
                                     
                                      SELECT
                        COUNT(kamarruangan_m.kamarruangan_id) INTO bed_tersedia
                    FROM kamartempattidur_m
                                            JOIN (SELECT a.kamarruangan_id,
                                                                     a.is_rekapkinerjaprofesi,
                                                                     a.kelaspelayanan_id,
                                                                     a.is_deleted,
                                                                     a.is_active,
                                                                     a.ruangan_id
                                                        FROM kamarruangan_m a) kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
            --          AND kamarruangan_m.is_kamarthruput=TRUE
                        AND kamarruangan_m.is_rekapkinerjaprofesi=TRUE
                        AND kamarruangan_m.is_deleted=FALSE 
                        AND kamarruangan_m.is_active=TRUE
                                                AND kamarruangan_m.kelaspelayanan_id = xkelaspelayanan_id
--                                              AND kamarruangan_m.ruangan_id = xruangan_id
                    WHERE kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true;
                    
                                        

--         SELECT
--             pasien_masuk - pasien_keluarhidup - pasien_keluarmeninggalkur48 - pasien_keluarmeninggalleb48 - dirujuk_rs_lain INTO hasil_sensus;
                        
                SELECT f_getpasienawalrekapkinerja(xstart_date-1, xkelaspelayanan_id) INTO pasien_awal;
                
                SELECT pasien_awal + pasien_masuk - pasien_keluarhidup - pasien_keluarmeninggalkur48 - pasien_keluarmeninggalleb48 - dirujuk_rs_lain INTO hp;
                
                SELECT 
                CASE 
                    WHEN los = 0 THEN 0
                    WHEN pasien_keluarhidup = 0 THEN 0
                ELSE
                    los / (pasien_keluarhidup + pasien_keluarmeninggalkur48 + pasien_keluarmeninggalleb48 + dirujuk_rs_lain) 
                END INTO alos;
                
                SELECT 
                CASE WHEN bed_tersedia = 0 THEN 0
                ELSE
                COALESCE(hp / (bed_tersedia::FLOAT)*100,0) 
                END INTO bor;
                
                SELECT 
                CASE WHEN pasien_keluarhidup = 0 THEN 0
                ELSE
                (bed_tersedia - hp) / (pasien_keluarhidup + pasien_keluarmeninggalkur48 + pasien_keluarmeninggalleb48 + dirujuk_rs_lain) 
                END INTO toi;
                
                SELECT (pasien_keluarhidup + pasien_keluarmeninggalkur48 + pasien_keluarmeninggalleb48 + dirujuk_rs_lain) / (bed_tersedia)::FLOAT INTO bto;
                
                SELECT 
                CASE WHEN pasien_keluarmeninggalleb48 = 0 THEN 0 
                ELSE
                pasien_keluarmeninggalleb48 / (pasien_keluarhidup + dirujuk_rs_lain + pasien_keluarmeninggalleb48)::FLOAT 
                END INTO ndr;
                
                SELECT 
                CASE WHEN pasien_keluarmeninggalleb48 = 0 THEN 0 
                ELSE
                (pasien_keluarmeninggalleb48 + pasien_keluarmeninggalkur48)  / (pasien_keluarhidup + dirujuk_rs_lain + pasien_keluarmeninggalleb48 + pasien_keluarmeninggalkur48)::FLOAT 
                END INTO gdr;
                
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
        echo "m220913_065951_migrate_MHG3048_laporanrekapkinerjaprofesional_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220913_065951_migrate_MHG3048_laporanrekapkinerjaprofesional_fn cannot be reverted.\n";

        return false;
    }
    */
}
