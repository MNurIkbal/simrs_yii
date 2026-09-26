<?php

use yii\db\Migration;

/**
 * Class m230616_081732_GB_1746_function_generate_kodebooking
 */
class m230616_045718_GB_1746_function_generate_kodebooking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE OR REPLACE FUNCTION public.generate_kodebooking_antrianjkn()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$
                    
                DECLARE
                vNumber VARCHAR;
                    
                BEGIN
                    IF(new.pendaftaranol_id is not null) THEN
                        vNumber := concat('OL',new.pendaftaranol_id);
                    
                    elsif(new.pendaftaran_id is not null) then 
                        vNumber := concat('WI',new.pendaftaran_id);
                    else
                        vNumber := 0;
                    end if;
                
                    new.kodebooking := vNumber;

                    RETURN NEW;
                END
                \$function\$
        ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_081732_GB_1746_function_generate_kodebooking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_081732_GB_1746_function_generate_kodebooking cannot be reverted.\n";

        return false;
    }
    */
}
