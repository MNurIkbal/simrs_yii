<?php

use yii\db\Migration;

/**
 * Class m220729_100518_migrate_odoo_function_pembayaranpelayanan_batal
 */
class m220729_100518_migrate_odoo_function_pembayaranpelayanan_batal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembayaranpelayanan_batal"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
              

            BEGIN  
            ----------------------------------pemakaianuangmuka_t-----------------------------
            --   IF(NEW.is_deleted = TRUE)
            --     THEN
            --       UPDATE pemakaianuangmuka_t SET is_deleted = TRUE
            --       WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
            -- 
            --   END IF;

            ----------------------------------pembayaranmetode_t-----------------------------
              IF(NEW.is_deleted = TRUE)
                THEN
                  UPDATE pembayaranmetode_t 
                                SET is_deleted = TRUE,
                                        deleted_by = NEW.deleted_by
                  WHERE pembayaran_id = NEW.pembayaran_id;

              END IF;
                
                ----------------------------------pembayaran_t-----------------------------
            --   IF(NEW.is_deleted = TRUE)
            --     THEN
            --       UPDATE pembayaran_t 
            --                  SET is_deleted = TRUE,
            --                          deleted_by = NEW.deleted_by
            --       WHERE pembayaran_id = NEW.pembayaran_id;
            -- 
            --   END IF;

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
        echo "m220729_100518_migrate_odoo_function_pembayaranpelayanan_batal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_100518_migrate_odoo_function_pembayaranpelayanan_batal cannot be reverted.\n";

        return false;
    }
    */
}
