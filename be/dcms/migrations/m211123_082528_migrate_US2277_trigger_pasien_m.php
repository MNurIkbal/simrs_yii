<?php

use yii\db\Migration;

/**
 * Class m211123_082528_migrate_US2277_trigger_pasien_m
 */
class m211123_082528_migrate_US2277_trigger_pasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    vprefix_rm VARCHAR;
    vis_rekammedik_aps BOOLEAN;
    vno_rekam_medik VARCHAR;
BEGIN
    vIsAps := NEW.is_aps;
    vno_rekam_medik := NEW.no_rekam_medik;                  
    
    SELECT digit_rekammedik , is_rekammedik_aps
    INTO vdigit_rm, vis_rekammedik_aps
    FROM konfigsystem_k
    WHERE konfigsystem_id = 1;
    
    
    SELECT COALESCE(prefix,'') INTO vprefix_rm
    FROM penomoran_k
    WHERE penomoran_id = 57;
    
    IF(vno_rekam_medik IS NULL)
    THEN
    IF(vis_rekammedik_aps IS TRUE )
    THEN
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
                    
                    vPrefix := CONCAT(vprefix_rm,vPrefix);
                    
                    UPDATE penomoran_k SET
                                last_number = vPrefix,
                                last_generate = vPrefix
                    WHERE penomoran_id = vId;
                
                    NEW.no_rekam_medik := vPrefix;

                    RETURN NEW;
        END IF;
    ELSE
        vId := 26;
        SELECT 
                    CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 8) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no
        INTO 
                    vPrefix
        FROM penomoran_k where penomoran_id = vId;
        
        vPrefix := CONCAT(vprefix_rm,vPrefix);
        
        UPDATE penomoran_k SET
                    last_number = vPrefix,
                    last_generate = vPrefix
        WHERE penomoran_id = vId;
    
        NEW.no_rekam_medik := vPrefix;

        RETURN NEW;
    END IF;
            
    ELSE
                UPDATE penomoran_k SET
                        last_number = vno_rekam_medik,
                        last_generate = vno_rekam_medik
                WHERE penomoran_id = vId;
        RETURN NEW;
    END IF;
    
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211123_082528_migrate_US2277_trigger_pasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211123_082528_migrate_US2277_trigger_pasien_m cannot be reverted.\n";

        return false;
    }
    */
}
