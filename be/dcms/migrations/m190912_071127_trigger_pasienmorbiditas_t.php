<?php

use yii\db\Migration;

/**
 * Class m190912_071127_trigger_pasienmorbiditas_t
 */
class m190912_071127_trigger_pasienmorbiditas_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
            CREATE OR REPLACE FUNCTION public.pasienmorbiditas_t_insert()
  RETURNS trigger AS
$BODY$
DECLARE
vpasienmorbiditas_id int4;
vpendaftaran_id int4;
vcreated_id int4;
vdiagnosa_pasien text;
vdiagnosa_penyerta text;
vpasiendiagnosa_id int4;
vpasien_id int4;
vruangan_id int4;

BEGIN
    vpasienmorbiditas_id := NEW.pasienmorbiditas_id;
    vpendaftaran_id := NEW.pendaftaran_id;
    vcreated_id := NEW.last_modified_by;    
    vdiagnosa_pasien := NEW.diagnosa_pasien;
    vpasien_id := NEW.pasien_id;
    vruangan_id := NEW.ruangan_id;
    
    IF (NEW.kelompokdiagnosa_id = 2)
    THEN
        UPDATE soaprj_t
        SET a_diag_utama = vdiagnosa_pasien::json
        WHERE pendaftaran_id = vpendaftaran_id; 
    ELSIF (NEW.kelompokdiagnosa_id = 3) 
    THEN
        UPDATE soaprj_t
        SET a_diag_penyerta = NULL
        WHERE pendaftaran_id = vpendaftaran_id; 
        
        SELECT 
--              diagnosa_pasien::text
                CONCAT(\'[\', string_agg(diagnosa_pasien::text,\',\'),\']\')::text AS penyerta
        INTO
            vdiagnosa_penyerta
        FROM pasienmorbiditas_t
        WHERE pendaftaran_id = vpendaftaran_id
        AND kelompokdiagnosa_id = 3
        AND is_deleted is FALSE;    
        
        UPDATE soaprj_t
        SET a_diag_penyerta = vdiagnosa_penyerta::json
        WHERE pendaftaran_id = vpendaftaran_id; 
    ELSIF (NEW.kelompokdiagnosa_id = 9) 
    THEN
        IF NOT EXISTS(
            SELECT *FROM pasiendiagnosa_t
            WHERE pasien_id = vpasien_id
            AND ruangan_id = vruangan_id
            AND is_active IS TRUE
            LIMIT 1
        )
        THEN
            INSERT INTO pasiendiagnosa_t(
                    pasien_id , diagnosa_pasien, ruangan_id, created_date,
                    created_by, is_deleted, is_active
            )
            VALUES(
                    vpasien_id, vdiagnosa_pasien::json, vruangan_id, CURRENT_TIMESTAMP,
                    vcreated_id, FALSE, TRUE
            )   ;
        ELSE
                
            UPDATE pasiendiagnosa_t
            SET is_active = FALSE
            WHERE pasien_id = vpasien_id
            AND ruangan_id = vruangan_id
            AND is_active IS TRUE;
            
            INSERT INTO pasiendiagnosa_t(
                    pasien_id , diagnosa_pasien, ruangan_id, created_date,
                    created_by, is_deleted, is_active
            )
            VALUES(
                    vpasien_id, vdiagnosa_pasien::json, vruangan_id, CURRENT_TIMESTAMP,
                    vcreated_id, FALSE, TRUE
            )   ;
        END IF;
    END IF;
        
    RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');

        $this->execute('ALTER FUNCTION public.pasienmorbiditas_t_insert()
  OWNER TO postgres;');

       
       
          $this->execute('
            CREATE OR REPLACE FUNCTION public.pasienmorbiditas_t_delete()
  RETURNS trigger AS
$BODY$
DECLARE
vpasienmorbiditas_id int4;
vpendaftaran_id int4;
vcreated_id int4;
vdiagnosa_pasien text;
vpasien_id int4;
vruangan_id int4;
vdiagnosa_penyerta text;

BEGIN
    vpasienmorbiditas_id := NEW.pasienmorbiditas_id;
    vpendaftaran_id := NEW.pendaftaran_id;
    vcreated_id := NEW.last_modified_by;    
    vdiagnosa_pasien := NEW.diagnosa_pasien;
    vpasien_id := NEW.pasien_id;
    vruangan_id := NEW.ruangan_id ;
    
    IF (NEW.is_deleted IS TRUE)
    THEN
        IF (NEW.kelompokdiagnosa_id = 2)
        THEN
            UPDATE soaprj_t
            SET a_diag_utama = NULL,
                    last_modified_by = vcreated_id,
                    last_modified_date = CURRENT_TIMESTAMP
            WHERE pendaftaran_id = vpendaftaran_id;
        ELSIF (NEW.kelompokdiagnosa_id = 3) 
        THEN
            UPDATE soaprj_t
            SET a_diag_penyerta = NULL
            WHERE pendaftaran_id = vpendaftaran_id; 
            
            SELECT 
--                  diagnosa_pasien::json
                CONCAT(\'[\', string_agg(diagnosa_pasien::text,\',\'),\']\')::text AS penyerta
            INTO
                vdiagnosa_penyerta
            FROM pasienmorbiditas_t
            WHERE pendaftaran_id = vpendaftaran_id
            AND kelompokdiagnosa_id = 3
            AND is_deleted is FALSE;    
            
            UPDATE soaprj_t
            SET a_diag_penyerta = vdiagnosa_penyerta::json,
                    last_modified_by = vcreated_id,
                    last_modified_date = CURRENT_TIMESTAMP
            WHERE pendaftaran_id = vpendaftaran_id; 
        ELSIF (NEW.kelompokdiagnosa_id = 9)
        THEN
            UPDATE pasiendiagnosa_t
            SET is_active = FALSE
            WHERE pasien_id = vpasien_id
            AND ruangan_id = vruangan_id
            AND is_active IS TRUE;
        END IF;
    END IF;
    
    RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');

           $this->execute('ALTER FUNCTION public.pasienmorbiditas_t_delete()
  OWNER TO postgres;');

         

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190912_071127_trigger_pasienmorbiditas_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190912_071127_trigger_pasienmorbiditas_t cannot be reverted.\n";

        return false;
    }
    */
}
