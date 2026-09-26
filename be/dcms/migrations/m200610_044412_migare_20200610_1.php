<?php

use yii\db\Migration;

/**
 * Class m200610_044412_migare_20200610_1
 */
class m200610_044412_migare_20200610_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" DROP COLUMN "is_validasipendaftaran";');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_validasipendaftaranrj" bool;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_validasipendaftaranrd" bool;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_validasipendaftaranri" bool;');
        
        $this->execute('select public.deps_save_and_drop_dependencies(\'public\', \'pasien_m\');');
        $this->execute('ALTER TABLE "public"."pasien_m" 
  ALTER COLUMN "no_rekam_medik" TYPE varchar(100) COLLATE "pg_catalog"."default";');
        
        $this->execute('select public.deps_restore_dependencies(\'public\', \'pasien_m\');');

        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"ins_noantrian_konfig\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
    v_konfigantrian int;
    v_prefix varchar;
    v_noantrian varchar;
    v_last varchar;
    v_id int;
    v_tgl_antrian DATE;
    v_ruangan int;
    v_jenisantrian int;
    v_jadwaldokter_id int4;
    v_jadwalbukapoli_id int4;
    v_fungsiantrian_id int4;
    v_jenisantriandetail_id int4;
    v_is_keteranganpasien BOOLEAN;
    
BEGIN
    v_konfigantrian := NEW.konfigantrian_id;
    v_tgl_antrian := NEW.tgl_antrian::DATE;
    v_ruangan := NEW.ruangan_id;
    v_jadwaldokter_id := NEW.jadwaldokter_id;
    v_jadwalbukapoli_id := NEW.jadwalbukapoli_id;
    v_jenisantrian := NEW.jenisantrian_id;
    v_fungsiantrian_id := NEW.fungsiantrian_id;
    v_jenisantriandetail_id := NEW.jenisantriandetail_id;
    
    SELECT is_keteranganpasien INTO v_is_keteranganpasien
    FROM konfigsystem_k 
    WHERE konfigsystem_id = 1;
    
    IF(v_is_keteranganpasien IS FALSE AND v_jenisantrian = 177)
    THEN
        v_prefix := '';
        
        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
        FROM antrian_t 
        WHERE jenisantrian_id = v_jenisantrian
        AND tgl_antrian::DATE = v_tgl_antrian
        AND jenisantriandetail_id = v_jenisantriandetail_id;
    ELSE
            
        IF (COALESCE(v_konfigantrian,0)=0)
        THEN
        -- > jenis_antrian = 'PENUNJANG' <=============================================
            IF (v_jenisantrian = 179) 
            THEN
                SELECT 
                    kode_antrian,
                    konfigantrian_id
                 INTO
                    v_prefix,
                    v_konfigantrian
                FROM konfigantrian_m 
                WHERE jenisantrian_id = 179
                and ruangan_id = v_ruangan 
                limit 1;
                
                NEW.konfigantrian_id = v_konfigantrian;
                
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE jenisantrian_id = 179
                AND tgl_antrian::DATE = v_tgl_antrian
                AND ruangan_id=v_ruangan;
                        
        -- > jenis_antrian = 'FARMASI' <=============================================
            ELSE
                SELECT 
                    kode_antrian
                 INTO
                    v_prefix
                FROM konfigantrian_m 
                WHERE jenisantrian_id = v_jenisantrian
                and fungsiantrian_id = v_fungsiantrian_id 
                limit 1;
                    
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE jenisantrian_id = v_jenisantrian
                AND fungsiantrian_id = v_fungsiantrian_id
                AND tgl_antrian::DATE = v_tgl_antrian
                AND ruangan_id=v_ruangan;
            END IF;
            
        ELSE
            SELECT 
                kode_antrian
            INTO
                v_prefix
            FROM konfigantrian_m 
            WHERE konfigantrian_id = v_konfigantrian;


            IF(v_jenisantrian = 312)
            THEN
        -- > jenis_antrian = 'Poliklinik langsung' <=============================================   
                IF(COALESCE(v_jadwaldokter_id,0)=0 AND COALESCE(v_jadwalbukapoli_id,0)=0) 
                THEN
                    SELECT  
                        jadwalbukapoli_m.jadwalbukapoli_id INTO v_jadwalbukapoli_id 
                    FROM jadwalbukapoli_m
                    JOIN 
                    (
                        SELECT 
                            ruangan_id,
                            tgl_antrian,
                            CASE
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'monday' THEN '75' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'tuesday' THEN '76' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'wednesday' THEN '77' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'thursday' THEN '78' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'friday' THEN '79' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'saturday' THEN '80' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'sunday' THEN '81' 
                            END AS hari
                        FROM antrian_t 
                        WHERE konfigantrian_id = v_konfigantrian
                        AND ruangan_id=v_ruangan
                        and jadwalbukapoli_id is null 
                        and jadwaldokter_id is null
                        AND tgl_antrian::DATE = v_tgl_antrian
                    ) antrian_t ON jadwalbukapoli_m.ruangan_id = antrian_t.ruangan_id 
                     and jadwalbukapoli_m.hari::int = antrian_t.hari::int
                     and jadwalbukapoli_m.is_active=TRUE 
                     and jadwalbukapoli_m.is_deleted=false
                     and antrian_t.tgl_antrian::time BETWEEN jadwalbukapoli_m.jam_mulai and jadwalbukapoli_m.jam_tutup;
                     
                        
                    NEW.jadwalbukapoli_id = v_jadwalbukapoli_id;
                        
                    SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                    FROM antrian_t 
                    WHERE konfigantrian_id = v_konfigantrian
                    AND tgl_antrian::DATE = v_tgl_antrian
                    AND jadwalbukapoli_id = v_jadwalbukapoli_id
                    AND ruangan_id=v_ruangan;
                        
        --                  SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
        --                  FROM antrian_t 
        --                  WHERE konfigantrian_id = v_konfigantrian
        --                  AND ruangan_id=v_ruangan
        --                  AND tgl_antrian::DATE = v_tgl_antrian;
                ELSE
        -- > jenis_antrian = 'Poliklinik lewat antrian' <=============================================
                    IF(COALESCE(v_jadwaldokter_id,0)<>0)
                    THEN            
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        WHERE konfigantrian_id = v_konfigantrian
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwaldokter_id = v_jadwaldokter_id
                        AND ruangan_id=v_ruangan;
                    ELSE
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        WHERE konfigantrian_id = v_konfigantrian
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwalbukapoli_id = v_jadwalbukapoli_id
                        AND ruangan_id=v_ruangan;
                    END IF;
                END IF;
            
        -- > jenis_antrian = 'xxxxxx' <=============================================    
            ELSE
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE konfigantrian_id = v_konfigantrian
                AND tgl_antrian::DATE = v_tgl_antrian
                AND jenisantriandetail_id = v_jenisantriandetail_id;
            END IF;
        END IF;
    END IF;
    
    v_noantrian = v_prefix || v_last;

    SELECT MAX(antrian_id)
    INTO v_id
    FROM antrian_t;

--     UPDATE antrian_t
--     SET no_antrian = v_noantrian
--     WHERE antrian_id = v_id;
    NEW.no_antrian = v_noantrian;
    NEW.is_keteranganpasien = v_is_keteranganpasien;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pasien_m\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
     vId integer; 
  vPrefix varchar;
  vLast VARCHAR;
  vNumber VARCHAR;
  vIsAps BOOLEAN;
  vdigit_rm INT;
BEGIN
    SELECT digit_rekammedik INTO vdigit_rm
    FROM konfigsystem_k
    WHERE konfigsystem_id = 1;
    
        vIsAps := NEW.is_aps;               
        IF(vIsAps) THEN
                    vId := 128;
                    SELECT 
                         prefix,
                            ( SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no 
                                    FROM penomoran_k where penomoran_id = vId
                            )
                        INTO 
                             vPrefix,
                             vNumber
                        FROM penomoran_k where penomoran_id = vId;

                    vLast := vPrefix || vNumber;
                    UPDATE penomoran_k SET
                        last_number = vNumber,
                        last_generate = vLast
                    WHERE penomoran_id = vId;
                    
                    NEW.no_rekam_medik := vLast;

                    RETURN NEW;
        ELSE
                    vId := 26;
                    SELECT 
                                CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 8) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no
                    INTO 
                                vPrefix
                    FROM penomoran_k where penomoran_id = vId;

                    UPDATE penomoran_k SET
                                last_number = vPrefix,
                                last_generate = vPrefix
                    WHERE penomoran_id = vId;
                
                    NEW.no_rekam_medik := vPrefix;

                    RETURN NEW;
        END IF;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('DROP FUNCTION if exists "public"."ins_noantrian_konfig_copy1"();');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200610_044412_migare_20200610_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200610_044412_migare_20200610_1 cannot be reverted.\n";

        return false;
    }
    */
}
