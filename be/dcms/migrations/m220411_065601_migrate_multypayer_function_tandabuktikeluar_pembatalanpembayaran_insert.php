<?php

use yii\db\Migration;

/**
 * Class m220411_065601_migrate_multypayer_function_tandabuktikeluar_pembatalanpembayaran_insert
 */
class m220411_065601_migrate_multypayer_function_tandabuktikeluar_pembatalanpembayaran_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tandabuktikeluar_pembatalanpembayaran_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                
                    
            BEGIN
                    -- INSERT table history tandabuktikeluar_t.pembatalanpembayaran_id
                    INSERT INTO tandabuktikeluar_t (        
                            tgl_buktikeluar,
                            jml_pembayaran,
                            pembatalanpembayaran_id,
                            created_by
                    )VALUES(
                            NEW.tgl_batal,
                            NEW.total_dibayar,
                            NEW.pembatalanpembayaran_id,
                            NEW.created_by
                    );

                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER if exists "tandabuktikeluar_pembatalanpembayaran_insert" ON "public"."pembatalanpembayaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "tandabuktikeluar_pembatalanpembayaran_insert" AFTER INSERT ON "public"."pembatalanpembayaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."tandabuktikeluar_pembatalanpembayaran_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220411_065601_migrate_multypayer_function_tandabuktikeluar_pembatalanpembayaran_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220411_065601_migrate_multypayer_function_tandabuktikeluar_pembatalanpembayaran_insert cannot be reverted.\n";

        return false;
    }
    */
}
