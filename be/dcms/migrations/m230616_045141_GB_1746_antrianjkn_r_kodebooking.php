<?php

use yii\db\Migration;

/**
 * Class m230616_045141_GB_1746_antrianjkn_r_kodebooking
 */
class m230616_045141_GB_1746_antrianjkn_r_kodebooking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE antrianjkn_r ADD column if not exists kodebooking varchar(75);");

        $this->execute("ALTER TABLE antrianjkn_r DROP constraint if exists unique_kodebooking_antrianjkn;");

        $this->execute("
        alter table antrianjkn_r add constraint unique_kodebooking_antrianjkn unique (kodebooking);
        ");

        $this->execute("
        CREATE OR REPLACE FUNCTION public.generate_kodebooking_antrianjkn()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$
                    
                DECLARE
                vNumber VARCHAR;
                    
                BEGIN
                    IF(new.pendaftaranol_id is not null) THEN
                        select 
                        concat(pendaftaranol_t.no_pendaftaranol,'OL',new.pendaftaranol_id)
                        into vNumber
                        from pendaftaranol_t 
                        where pendaftaranol_id = new.pendaftaranol_id;
                    
                    elsif(new.pendaftaran_id is not null) then 
                        select 
                        concat(pendaftaran_t.no_pendaftaran,'OFF',new.pendaftaran_id)
                        into vNumber 
                        from pendaftaran_t 
                        where pendaftaran_id = new.pendaftaran_id;
                    else
                        vNumber := 0;
                    end if;
                
                    new.kodebooking := vNumber;

                    RETURN NEW;
                END
                \$function\$
        ;
        ");

        $this->execute("
        CREATE TRIGGER trigg_generate_kodebooking
        BEFORE INSERT
        ON public.antrianjkn_r
        FOR EACH ROW
        EXECUTE PROCEDURE public.generate_kodebooking_antrianjkn();
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_045141_GB_1746_antrianjkn_r_kodebooking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_045141_GB_1746_antrianjkn_r_kodebooking cannot be reverted.\n";

        return false;
    }
    */
}
