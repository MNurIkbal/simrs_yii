<?php

use yii\db\Migration;

/**
 * Class m200227_042008_migratefunction_20200227_1
 */
class m200227_042008_migratefunction_20200227_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $this->execute("
                        CREATE OR REPLACE FUNCTION public.fgetvaluelookup(vlookup_id int4)
                        RETURNS pg_catalog.varchar AS 
                        \$BODY\$
                        DECLARE vlookup_value VARCHAR;
                        BEGIN
                        SELECT lookup_value INTO vlookup_value
                        FROM lookup_m
                        WHERE lookup_id = vlookup_id;

                        RETURN vlookup_value;

                        END
                        \$BODY\$
                        LANGUAGE plpgsql VOLATILE
                        COST 100;");

         $this->execute('ALTER FUNCTION public.fgetvaluelookup(vlookup_id int4) OWNER TO postgres;');
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_042008_migratefunction_20200227_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_042008_migratefunction_20200227_1 cannot be reverted.\n";

        return false;
    }
    */
}
