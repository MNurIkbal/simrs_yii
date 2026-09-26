<?php

use yii\db\Migration;

/**
 * Class m230227_145216_migrate_GBD128_table_tindakanpelayanan_t
 */
class m230227_145216_migrate_GBD128_table_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."hapus_instruksi"()
RETURNS "pg_catalog"."trigger" AS $BODY$

DECLARE
    Vinstruksitindakan_id int4;
BEGIN
    Vinstruksitindakan_id := NEW.instruksitindakan_id;
                    
    IF(NEW.is_deleted IS TRUE AND Vinstruksitindakan_id IS NOT NULL)
    THEN
                                
        UPDATE instruksitindakan_t SET is_deleted=true, deleted_by=NEW.deleted_by, deleted_date = new.deleted_date
        WHERE instruksitindakan_id = Vinstruksitindakan_id;
            
    END IF;
RETURN NEW;

END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
        ');  

        $this->execute('
            DROP TRIGGER IF EXISTS "hapus_instruksi" ON "public"."tindakanpelayanan_t";
        ');

        $this->execute('
            CREATE TRIGGER "hapus_instruksi" AFTER UPDATE ON "public"."tindakanpelayanan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."hapus_instruksi"();
        ');  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230227_145216_migrate_GBD128_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230227_145216_migrate_GBD128_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
