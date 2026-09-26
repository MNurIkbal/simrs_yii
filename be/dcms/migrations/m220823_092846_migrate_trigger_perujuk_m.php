<?php

use yii\db\Migration;

/**
 * Class m220823_092846_migrate_trigger_perujuk_m
 */
class m220823_092846_migrate_trigger_perujuk_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."update_kode_perujuk"()
              RETURNS "pg_catalog"."trigger" AS $BODY$

            BEGIN
                IF(NEW.perujuk_kode IS NULL)
                THEN
                    NEW.perujuk_kode = NEW.kodeppk;
                END IF;
                
                RETURN NEW;
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "update_perujuk" ON "public"."perujuk_m";
        ');

        $this->execute('
            CREATE TRIGGER "update_perujuk" BEFORE INSERT ON "public"."perujuk_m"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."update_kode_perujuk"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220823_092846_migrate_trigger_perujuk_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220823_092846_migrate_trigger_perujuk_m cannot be reverted.\n";

        return false;
    }
    */
}
