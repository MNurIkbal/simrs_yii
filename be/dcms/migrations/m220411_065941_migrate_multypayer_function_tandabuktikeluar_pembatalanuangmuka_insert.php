<?php

use yii\db\Migration;

/**
 * Class m220411_065941_migrate_multypayer_function_tandabuktikeluar_pembatalanuangmuka_insert
 */
class m220411_065941_migrate_multypayer_function_tandabuktikeluar_pembatalanuangmuka_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tandabuktikeluar_pembatalanuangmuka_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                
                    
            BEGIN
                    -- INSERT table history tandabuktikeluar_t.pembatalanuangmuka_id
                    INSERT INTO tandabuktikeluar_t (        
                            pembatalanuangmuka_id,
                            tgl_buktikeluar,
                            jml_pembayaran,
                            created_by
                    )VALUES(
                            NEW.pembatalanuangmuka_id,
                            NEW.tglpembatalan,
                            NEW.jmlkaskeluarbatal,
                            NEW.created_by
                    );

                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER if exists "tandabuktikeluar_pembatalanuangmuka_insert" ON "public"."pembatalanuangmuka_t";
        ');

        $this->execute('
            CREATE TRIGGER "tandabuktikeluar_pembatalanuangmuka_insert" AFTER INSERT ON "public"."pembatalanuangmuka_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."tandabuktikeluar_pembatalanuangmuka_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220411_065941_migrate_multypayer_function_tandabuktikeluar_pembatalanuangmuka_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220411_065941_migrate_multypayer_function_tandabuktikeluar_pembatalanuangmuka_insert cannot be reverted.\n";

        return false;
    }
    */
}
