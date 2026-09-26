<?php

use yii\db\Migration;

/**
 * Class m210319_024109_migrate_20200319_konfigrak_trg
 */
class m210319_024109_migrate_20200319_konfigrak_trg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"konfigrak_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table konfigrak_m
        INSERT INTO konfigrak_m (       
        ruangan_id,
        obatalkes_id,
        stokobatr_id
        )VALUES(
        NEW.ruangan_id,
        NEW.obatalkes_id,
        NEW.stokobatr_id 
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");

        $this->execute('ALTER FUNCTION "public"."konfigrak_insert"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "konfigrak_m_insert" AFTER INSERT ON "public"."stokobatalkes_r"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."konfigrak_insert"();');

       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210319_024109_migrate_20200319_konfigrak_trg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_024109_migrate_20200319_konfigrak_trg cannot be reverted.\n";

        return false;
    }
    */
}
