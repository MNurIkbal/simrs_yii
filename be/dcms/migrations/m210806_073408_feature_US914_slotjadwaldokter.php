<?php

use yii\db\Migration;

/**
 * Class m210806_073408_feature_US914_slotjadwaldokter
 */
class m210806_073408_feature_US914_slotjadwaldokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."slotjadwaldokter_m" (
            "slotjadwaldokter_id" serial8,
            "jadwaldokter_id" int4,
            "slot_sequence" int4,
            "jam_mulai" time(6),
            "jam_selesai" time(6),
            "slot_type" bool,
            "additional_data" text COLLATE "pg_catalog"."default",
            "created_date" timestamp(6),
            "created_by" int4,
            "modified_count" int4,
            "last_modified_date" timestamp(6),
            "last_modified_by" int4,
            "is_deleted" bool,
            "is_active" bool,
            "deleted_date" timestamp(6),
            "deleted_by" int4,
            CONSTRAINT "slotjadwaldokter_m_pkey" PRIMARY KEY ("slotjadwaldokter_id")
            );
        ');

        $this->execute('
            ALTER TABLE "public"."slotjadwaldokter_m" 
            OWNER TO "postgres";
            ');

        $this->execute('
            ALTER TABLE "public"."antrian_t" 
            ADD COLUMN IF NOT EXISTS "slot_sequence" int4,
            ADD COLUMN IF NOT EXISTS "temp_urutan" varchar(12) COLLATE "pg_catalog"."default";
            ');

        $this->execute('
            ALTER TABLE "public"."jadwaldokter_m" 
            ADD COLUMN IF NOT EXISTS  "kuota_total" float4;
            ');

            $this->execute(" CREATE OR REPLACE FUNCTION \"public\".\"ins_noantrian_konfig\"()
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
    vis_nourut BOOLEAN;
    v_instalasi_id int;
    v_slot_sequence int;
    v_temp_nourut varchar;
     
BEGIN
    v_konfigantrian := NEW.konfigantrian_id;
    v_tgl_antrian := NEW.tgl_antrian::DATE;
    v_ruangan := NEW.ruangan_id;
    v_jadwaldokter_id := NEW.jadwaldokter_id;
    v_jadwalbukapoli_id := NEW.jadwalbukapoli_id;
    v_jenisantrian := NEW.jenisantrian_id;
    v_fungsiantrian_id := NEW.fungsiantrian_id;
    v_jenisantriandetail_id := NEW.jenisantriandetail_id;
    v_slot_sequence := NEW.slot_sequence;
    
    SELECT instalasi_id INTO v_instalasi_id
    FROM ruangan_m
    WHERE ruangan_id = v_ruangan;
    
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

            SELECT 
                konfigsystem_k.is_nourut INTO vis_nourut
            FROM konfigsystem_k;
            
            IF (vis_nourut = FALSE)
            THEN
            -- > jenis_antrian = 'Poliklinik'
                IF(v_jenisantrian = 312)
                THEN
--                  NEW.additional_data = '312-X01';
                    -- > jenis_antrian = 'Poliklinik langsung' <=============================================   
                    IF(COALESCE(v_jadwaldokter_id,0)=0 AND COALESCE(v_jadwalbukapoli_id,0)=0) 
                    THEN
                    
--                      NEW.additional_data = '312-X02';
                        
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
                            -- WHERE konfigantrian_id = v_konfigantrian
                            WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                            AND ruangan_id=v_ruangan
                            and jadwalbukapoli_id is null 
                            and jadwaldokter_id is null
                            AND tgl_antrian::DATE = v_tgl_antrian
                        ) antrian_t ON jadwalbukapoli_m.ruangan_id = antrian_t.ruangan_id 
                         and jadwalbukapoli_m.hari::int = antrian_t.hari::int
                         and jadwalbukapoli_m.is_active=TRUE 
                         and jadwalbukapoli_m.is_deleted=false
                         and antrian_t.tgl_antrian::time BETWEEN jadwalbukapoli_m.jam_mulai and jadwalbukapoli_m.jam_tutup;
                        IF(COALESCE(v_jadwalbukapoli_id,0) = 0)
                        THEN
                            SELECT jadwalbukapoli_m.jadwalbukapoli_id INTO v_jadwalbukapoli_id
                            FROM jadwalbukapoli_m
                            WHERE ruangan_id = 2
                            AND CURRENT_TIMESTAMP::time BETWEEN jadwalbukapoli_m.jam_mulai and jadwalbukapoli_m.jam_tutup
                            AND jadwalbukapoli_m.hari::VARCHAR IN (
                                    SELECT
                                    CASE
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'monday' THEN '75' 
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'tuesday' THEN '76' 
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'wednesday' THEN '77' 
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'thursday' THEN '78' 
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'friday' THEN '79' 
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'saturday' THEN '80' 
                                        WHEN trim(to_char(CURRENT_DATE, 'day'::text)) = 'sunday' THEN '81' 
                                    END)
                                LIMIT 1;
                                
                            NEW.jadwalbukapoli_id = v_jadwalbukapoli_id;
                        ELSE
                            NEW.jadwalbukapoli_id = v_jadwalbukapoli_id;
                        END IF;
                        
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        -- WHERE konfigantrian_id = v_konfigantrian
                        WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwalbukapoli_id = v_jadwalbukapoli_id
                        AND ruangan_id=v_ruangan;
                        
                        -- US914 Kebutuhan No Urut SMH--
                        SELECT temp_urutan into v_temp_nourut
                                FROM antrian_t 
                            WHERE slot_sequence IS NOT NULL
                            and tgl_antrian::DATE = v_tgl_antrian 
                            AND is_online = TRUE 
                            AND ruangan_id = v_ruangan
                            AND jadwalbukapoli_id = v_jadwalbukapoli_id
                            AND temp_urutan LIKE v_last;
                            IF(v_temp_nourut IS NOT NULL)
                                THEN
                                        SELECT RIGHT( '000'|| COALESCE(temp_urutan::int, 0) + 1, 3) INTO v_last
              FROM antrian_t WHERE temp_urutan = v_temp_nourut;
--                              ELSE
                            END IF;
                            
                    ELSE
--                      NEW.additional_data = '312-X03';
                        -- > jenis_antrian = 'Poliklinik lewat antrian' <=============================================
                        IF(COALESCE(v_jadwaldokter_id,0)<>0)
                        THEN    
                            IF (v_slot_sequence IS NOT NULL)
                  THEN
                    SELECT RIGHT( '000'|| COALESCE(MAX(v_slot_sequence),0), 3)  INTO v_last
                    FROM antrian_t 
                    -- WHERE konfigantrian_id = v_konfigantrian
                    WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                AND tgl_antrian::DATE = v_tgl_antrian
                                AND jadwaldokter_id = v_jadwaldokter_id;
                        ELSE
--                          NEW.additional_data = '312-X04';
                            
                            SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                            FROM antrian_t 
                            -- WHERE konfigantrian_id = v_konfigantrian
                            WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                            AND tgl_antrian::DATE = v_tgl_antrian
                            AND slot_sequence IS NULL
                            AND jadwaldokter_id = v_jadwaldokter_id
--                          AND no_antrian::int LIKE v_temp_nourut::int
                            AND ruangan_id=v_ruangan;
                            
                            -- US914 Kebutuhan No Urut SMH--
                            SELECT temp_urutan into v_temp_nourut
                            FROM antrian_t 
                            WHERE slot_sequence IS NOT NULL
                            and tgl_antrian::DATE = v_tgl_antrian 
                            AND is_online = TRUE 
                            AND ruangan_id = v_ruangan
                            AND jadwaldokter_id = v_jadwaldokter_id
                            AND temp_urutan LIKE v_last;
                            IF(v_temp_nourut IS NOT NULL)
                                THEN
                                        SELECT RIGHT( '000'|| COALESCE(temp_urutan::int, 0) + 1, 3) INTO v_last
              FROM antrian_t WHERE temp_urutan = v_temp_nourut;
--                              ELSE
                            END IF;
                            
                        END IF;
                        ELSE
--                          NEW.additional_data = '312-X05';
                            
                            SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                            FROM antrian_t 
                            -- WHERE konfigantrian_id = v_konfigantrian
                            WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                            AND tgl_antrian::DATE = v_tgl_antrian
                            AND jadwalbukapoli_id = v_jadwalbukapoli_id
                            AND ruangan_id=v_ruangan;
                        END IF;
                    END IF;
                
            -- > jenis_antrian = 'xxxxxx' <=============================================    
                ELSE
                    SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                    FROM antrian_t 
                    -- WHERE konfigantrian_id = v_konfigantrian
                    WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                    AND tgl_antrian::DATE = v_tgl_antrian
                    AND jenisantriandetail_id = v_jenisantriandetail_id;
                END IF;
            ELSE
            -- > jenis_antrian = 'Selain Poliklinik'
                IF(v_jenisantrian <> 312)
                    THEN
                        SELECT 
                                kode_antrian
                        INTO
                            v_prefix
                        FROM konfigantrian_m 
                        WHERE jenisantrian_id = v_jenisantrian
                        AND konfigantrian_id = v_konfigantrian
                        limit 1;
                            
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwaldokter_id = v_jadwaldokter_id
                        AND ruangan_id=v_ruangan;
                ELSE
                    IF(v_instalasi_id <> 1)
                        THEN
                            SELECT 
                                kode_antrian
                            INTO
                                v_prefix
                            FROM konfigantrian_m 
                            WHERE jenisantrian_id = v_jenisantrian
                            AND konfigantrian_id = v_konfigantrian
                            limit 1;
                                
                            SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                            FROM antrian_t 
                            WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                            AND tgl_antrian::DATE = v_tgl_antrian
                            AND jadwaldokter_id = v_jadwaldokter_id
                            AND ruangan_id=v_ruangan;
                    END IF;
                END IF;
            END IF;
        END IF;
    END IF;
    
    v_noantrian = v_prefix || v_last;
    
--  NEW.additional_data = NEW.additional_data || v_prefix;
    
    SELECT MAX(antrian_id)
    INTO v_id
    FROM antrian_t;

--     UPDATE antrian_t
--     SET no_antrian = v_noantrian
--     WHERE antrian_id = v_id;
    
    IF (vis_nourut = FALSE) 
    THEN
--      NEW.additional_data = NEW.additional_data || v_prefix || '01-' || COALESCE(v_last,'') || '-' || COALESCE(v_noantrian);
        NEW.no_antrian = v_noantrian;
        NEW.temp_urutan = v_last;
    ELSE
        IF(v_jenisantrian <> 312)
        THEN
--          NEW.additional_data = NEW.additional_data || v_prefix || '02-' || COALESCE(v_last,'') || '-' || COALESCE(v_noantrian);
            NEW.no_antrian = v_noantrian;
            NEW.temp_urutan = v_last;
        ELSE
            IF(v_instalasi_id <> 1)
            THEN
--              NEW.additional_data = NEW.additional_data || v_prefix || '03-' || COALESCE(v_last,'') || '-' || COALESCE(v_noantrian);
                NEW.no_antrian = v_noantrian;
                NEW.temp_urutan = v_last;
            END IF;
        END IF;
    END IF;
    NEW.is_keteranganpasien = v_is_keteranganpasien;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
                ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210806_073408_feature_US914_slotjadwaldokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210806_073408_feature_US914_slotjadwaldokter cannot be reverted.\n";

        return false;
    }
    */
}
