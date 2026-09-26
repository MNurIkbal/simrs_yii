<?php

use yii\db\Migration;

/**
 * Class m220408_084424_migrate_multypayer_function_pemakaianuangmuka_insert
 */
class m220408_084424_migrate_multypayer_function_pemakaianuangmuka_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pemakaianuangmuka_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
            DECLARE  
            --      v_pembayaranpelayanan_id int;
                    v_total_uangmuka float;
                    v_pemakaian_uangmuka float;
                    v_sisa_uangmuka float;

            BEGIN   

                IF(NEW.penggunaan_uangmuka <> 0)
                    THEN
            --             SELECT 
            --                 pembayaranpelayanan_t.pembayaranpelayanan_id
            --             INTO
            --                 v_pembayaranpelayanan_id
            --             FROM pembayaranpelayanan_t
            --             WHERE pembayaranpelayanan_t.pembayaran_id = NEW.pembayaran_id;

                        SELECT
                            SUM(bayaruangmuka_t.jumlah_uangmuka) 
                        INTO 
                             v_total_uangmuka
                        FROM bayaruangmuka_t
                        WHERE bayaruangmuka_t.is_deleted=FALSE and bayaruangmuka_t.pendaftaran_id = NEW.pendaftaran_id
                        GROUP BY bayaruangmuka_t.pendaftaran_id;    

                        SELECT
                            SUM(pemakaianuangmuka_t.pemakaian_uangmuka)
                        INTO 
                            v_pemakaian_uangmuka
                        FROM pemakaianuangmuka_t
                        WHERE pemakaianuangmuka_t.pendaftaran_id =NEW.pendaftaran_id and pemakaianuangmuka_t.is_deleted=FALSE
                        GROUP BY pemakaianuangmuka_t.pendaftaran_id;

                    v_sisa_uangmuka = v_total_uangmuka - COALESCE(v_pemakaian_uangmuka,0);
                    v_sisa_uangmuka = v_sisa_uangmuka - NEW.penggunaan_uangmuka;
                        
                        INSERT INTO pemakaianuangmuka_t 
                        (       
                            pembayaranpelayanan_id,
                            pendaftaran_id,
                            tgl_pemakaian,
                            total_uangmuka,
                            pemakaian_uangmuka,
                            sisa_uangmuka,
                            created_date,
                            created_by,
                            is_deleted,
                            is_active,
                                            pembayaran_id
                        )
                        VALUES 
                        (
                            NULL,
                            NEW.pendaftaran_id,
                            NEW.created_date,
                            v_total_uangmuka,
                            NEW.penggunaan_uangmuka,
                            v_sisa_uangmuka,
                            NEW.created_date,
                            NEW.created_by,
                            NEW.is_deleted,
                            NEW.is_active,
                                            NEW.pembayaran_id
                        );

            END IF;
            RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_084424_migrate_multypayer_function_pemakaianuangmuka_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_084424_migrate_multypayer_function_pemakaianuangmuka_insert cannot be reverted.\n";

        return false;
    }
    */
}
