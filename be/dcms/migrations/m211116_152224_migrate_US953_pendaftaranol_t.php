<?php

use yii\db\Migration;

/**
 * Class m211116_152224_migrate_US953_pendaftaranol_t
 */
class m211116_152224_migrate_US953_pendaftaranol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
CREATE OR REPLACE FUNCTION \"public\".\"pendaftaranol_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
            DECLARE
                vPrefixInstalasi VARCHAR;
                vPrefixDate VARCHAR;
                vNumber VARCHAR;
                vYear VARCHAR;
                vMonth VARCHAR;
                vDay VARCHAR;
                vDate VARCHAR;
                vTempDate VARCHAR;
                vLastDate VARCHAR;
                                dataAntrian VARCHAR;
                                paramJson VARCHAR;
                                v_konfigantrian INTEGER;
                                vnourut BOOLEAN;
                                antrianId INTEGER;
            BEGIN
                vYear := to_char(new.tgl_pendaftaranol, 'YYYY');
                vMonth := to_char(new.tgl_pendaftaranol, 'MM');
                vDay := to_char(new.tgl_pendaftaranol, 'DD');
                SET timezone = 'Asia/Jakarta';
                                paramJson := NEW.additional_data;  
                                dataAntrian := paramJson::json->>'antrian';

--                 SELECT
--                     distinct(vYear || vMonth || vDay)
--                 INTO
--                     vDate;
-- 
--                 SELECT
--                     distinct(vYear || '-' || vMonth || '-' || vDay)
--                 INTO
--                     vTempDate;
-- 
--                 SELECT LEFT(MAX(SUBSTRING(no_pendaftaranol FROM '[0-9]+')), 8) AS INT FROM pendaftaranol_t INTO vLastDate WHERE tgl_pendaftaranol = to_date(vTempDate, 'YYYY-MM-DD');
                                
                            
                                
                                SELECT
                                                (RIGHT('0' || date_part('YEAR',now()),4) ||
                        RIGHT('0' || date_part('month',now()),2) ||
                        RIGHT('0' || date_part('DAY',now()),2) ||
                        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pendaftaranol), 3) AS INT), 0) + 1 AS VARCHAR(3)), 3, '0'))) last_no
                                                INTO 
                        vNumber
                    FROM pendaftaranol_t
                    WHERE pendaftaranol_t.created_date::DATE = CURRENT_DATE;

                IF(vDate > vLastDate)
                    THEN
                        SELECT vDate INTO vPrefixDate;

--                         SELECT '001' INTO vNumber;
                                                
                                                SELECT
                                                (RIGHT('0' || date_part('YEAR',now()),4) ||
                        RIGHT('0' || date_part('month',now()),2) ||
                        RIGHT('0' || date_part('DAY',now()),2) ||
                        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pendaftaranol), 3) AS INT), 0) + 1 AS VARCHAR(3)), 3, '0'))) last_no
                                                INTO 
                        vNumber
                    FROM pendaftaranol_t
                    WHERE pendaftaranol_t.created_date::DATE = CURRENT_DATE;
                                                
                END IF;

                IF(vDate = vLastDate)
                    THEN
                        SELECT vLastDate INTO vPrefixDate;

                        SELECT
                                                (RIGHT('0' || date_part('YEAR',now()),4) ||
                        RIGHT('0' || date_part('month',now()),2) ||
                        RIGHT('0' || date_part('DAY',now()),2) ||
                        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pendaftaranol), 3) AS INT), 0) + 1 AS VARCHAR(3)), 3, '0'))) last_no
                                                INTO 
                        vNumber
                    FROM pendaftaranol_t
                    WHERE pendaftaranol_t.created_date::DATE = CURRENT_DATE;
                END IF;

                IF(vLastDate IS NULL)
                    THEN
                        SELECT vDate INTO vPrefixDate;

--                         SELECT '001' INTO vNumber;
SELECT
                                                (RIGHT('0' || date_part('YEAR',now()),4) ||
                        RIGHT('0' || date_part('month',now()),2) ||
                        RIGHT('0' || date_part('DAY',now()),2) ||
                        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pendaftaranol), 3) AS INT), 0) + 1 AS VARCHAR(3)), 3, '0'))) last_no
                                                INTO 
                        vNumber
                    FROM pendaftaranol_t
                    WHERE pendaftaranol_t.created_date::DATE = CURRENT_DATE;
                END IF;

                SELECT 
                        'RSV'
                INTO 
                     vPrefixInstalasi;

--                 NEW.no_pendaftaranol := TRIM(vPrefixInstalasi) || vPrefixDate || vNumber;
                NEW.no_pendaftaranol := TRIM(vPrefixInstalasi) || vNumber;
                                
                                -- Set Antrian 
         IF (dataAntrian::json->>'jenisantrian_id' IS NOT NULL AND NEW.antrian_id IS NULL) THEN
                     --get konfigantrian
                    SELECT konfigantrian_id INTO v_konfigantrian
                    from konfigantrian_m
                    WHERE jenisantrian_id = (dataAntrian::json->>'jenisantrian_id')::INTEGER 
                    and konfigantrian_m.is_deleted=FALSE 
                    and konfigantrian_m.is_active=true
                    limit 1;
                                        
                   SELECT konfigsystem_k.is_nourut INTO vnourut
                   FROM konfigsystem_k
                   LIMIT 1; 
                    
                    IF (vnourut=TRUE) THEN
                                        INSERT INTO antrian_t (
                                pasien_id,
                                ruangan_id,
                                carabayar_id,
                                pendaftaran_id,
                                tgl_antrian,
                                penjamin_id,
                                pegawai_id,
                                status_pasien,
                                jenisantrian_id,
                                is_active,
                                created_by,
                                konfigantrian_id,
                                no_antrian,
                                                                jadwaldokter_id,
                                                                jadwalbukapoli_id,
                                                                is_online,
                                                                slot_sequence,
                                                                groupcarabayar_id
             ) VALUES (
                                NEW.pasien_id,
                                (dataAntrian::json->>'ruangan_id')::INTEGER,
                                (dataAntrian::json->>'carabayar_id')::INTEGER,
                                0,
                                (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                (dataAntrian::json->>'penjamin_id')::INTEGER,
                                (dataAntrian::json->>'pegawai_id')::INTEGER,
                                (dataAntrian::json->>'status_pasien')::INTEGER,
                                (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                FALSE,
                                NEW.created_by,
                                v_konfigantrian,
                                                                (dataAntrian::json->>'no_antrian')::VARCHAR,
                                                                (dataAntrian::json->>'jadwaldokter_id')::INTEGER,
                                                                (dataAntrian::json->>'jadwalbukapoli_id')::INTEGER,
                                                                TRUE,
                                                                (dataAntrian::json->>'slot_sequence')::INTEGER,
                                                                (dataAntrian::json->>'groupcarabayar_id')::INTEGER
             ) RETURNING antrian_id INTO antrianId;
                         ELSE
                         INSERT INTO antrian_t (
                                pasien_id,
                                ruangan_id,
                                carabayar_id,
                                pendaftaran_id,
                                tgl_antrian,
                                penjamin_id,
                                pegawai_id,
                                status_pasien,
                                jenisantrian_id,
                                is_active,
                                created_by,
                                konfigantrian_id,
                                                                jadwaldokter_id,
                                                                jadwalbukapoli_id,
                                                                is_online,
                                                                slot_sequence,
                                                                groupcarabayar_id
             ) VALUES (
                                NEW.pasien_id,
                                (dataAntrian::json->>'ruangan_id')::INTEGER,
                                (dataAntrian::json->>'carabayar_id')::INTEGER,
                                NULL,
                                (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                (dataAntrian::json->>'penjamin_id')::INTEGER,
                                (dataAntrian::json->>'pegawai_id')::INTEGER,
                                (dataAntrian::json->>'status_pasien')::INTEGER,
                                (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                FALSE,
                                NEW.created_by,
                                v_konfigantrian,
                                                                (dataAntrian::json->>'jadwaldokter_id')::INTEGER,
                                                                (dataAntrian::json->>'jadwalbukapoli_id')::INTEGER,
                                                                TRUE,
                                                                (dataAntrian::json->>'slot_sequence')::INTEGER,
                                                                (dataAntrian::json->>'groupcarabayar_id')::INTEGER
             ) RETURNING antrian_id INTO antrianId;
                         END IF;
                         
                NEW.antrian_id = antrianId;
                                                            
         END IF;
                                

                RETURN NEW;
            END;
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
        echo "m211116_152224_migrate_US953_pendaftaranol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_152224_migrate_US953_pendaftaranol_t cannot be reverted.\n";

        return false;
    }
    */
}
