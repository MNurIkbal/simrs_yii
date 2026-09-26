<?php

use yii\db\Migration;

/**
 * Class m210125_062643_oddo_penyesuaianfunction
 */
class m210125_062643_oddo_penyesuaianfunction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_nontunai_batal\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE  
        v_tipe_pembayaran INT;
        
BEGIN
            SELECT 
                jenisnontunai_m.tipe_pembayaran
            INTO
                v_tipe_pembayaran
            FROM jenisnontunai_m
            WHERE jenisnontunai_m.jenisnontunai_id = NEW.jenisnontunai_id;
            
        IF(NEW.is_deleted IS TRUE)
            THEN
        -- INSERT table history pembayaran_r untuk case pembatalan non tunai
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        total_tunai,
        total_nontunai,
        nama_edc,
        tipe_pembayaran,
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
        keterangan
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        0, --total_tunai
        -1 * OLD.total_dibayar ,
        NEW.nama_edc,
        v_tipe_pembayaran,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'REFUND'
        );
END IF;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."pembayaran_r_nontunai_batal"() OWNER TO "postgres";');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210125_062643_oddo_penyesuaianfunction cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210125_062643_oddo_penyesuaianfunction cannot be reverted.\n";

        return false;
    }
    */
}
