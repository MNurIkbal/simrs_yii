<?php

use yii\db\Migration;

/**
 * Class m201027_060338_oddo_penyesuaianfunction_20201027
 */
class m201027_060338_oddo_penyesuaianfunction_20201027 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pemakaianuangmuka_r_delete\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
------------------------------> INSERT table rekap delete pemakaianuangmuka_r <---------------------------------------------------
    IF(NEW.is_deleted = TRUE)
         THEN
        INSERT INTO pemakaianuangmuka_r (       
            pemakaianuangmuka_id,
            pembayaranpelayanan_id,
            tandabuktikeluar_id,
            pendaftaran_id,
            tgl_pemakaian,
            total_uangmuka,
            pemakaian_uangmuka,
            sisa_uangmuka,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            bayaruangmuka_id,
            keterangan
        )VALUES(
            NEW.pemakaianuangmuka_id,
            NEW.pembayaranpelayanan_id,
            NEW.tandabuktikeluar_id,
            NEW.pendaftaran_id,
            NEW.tgl_pemakaian,
            NEW.total_uangmuka,
            NEW.pemakaian_uangmuka,
            NEW.sisa_uangmuka,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.bayaruangmuka_id,
            'Deposit Availed'
        );
  END IF;
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "pemakaianuangmuka_r_delete" AFTER UPDATE ON "public"."pemakaianuangmuka_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pemakaianuangmuka_r_delete"();');

        $this->execute('COMMENT ON TRIGGER "pemakaianuangmuka_r_delete" ON "public"."pemakaianuangmuka_t" IS \'rekap int_uangmuka_v\';');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"upd_noresep\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vId integer := 4; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    v_day VARCHAR;
    v_reset VARCHAR;
    
BEGIN
    SELECT 
        penjualanresep_id 
    INTO
        vIdPenjualan
    FROM 
        penjualanresep_t 
    ORDER BY    
        penjualanresep_id 
    DESC LIMIT 1;
    
    SELECT date_part('DAY',now()) INTO v_day;
    
    IF(v_day = '1')
    THEN
        SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) AS VARCHAR(5)), 5, '0'))
            INTO v_reset
        FROM penomoran_k WHERE penomoran_id = vId;
        
        IF(v_reset <> '00001')
        THEN
            UPDATE penomoran_k SET 
                last_generate = '00001'
            WHERE penomoran_id = vId;
        END IF;
        
    END IF;
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.noresep = v_Nomor;
    
--  UPDATE 
--      penjualanresep_t
--  SET
--      noresep = v_Nomor
--  WHERE
--      penjualanresep_id = vIdPenjualan;


    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('DROP TRIGGER "upd_noresep" ON "public"."penjualanresep_t";');

        $this->execute('CREATE TRIGGER "upd_noresep" BEFORE INSERT ON "public"."penjualanresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."upd_noresep"();');

    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201027_060338_oddo_penyesuaianfunction_20201027 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201027_060338_oddo_penyesuaianfunction_20201027 cannot be reverted.\n";

        return false;
    }
    */
}
