<?php

use yii\db\Migration;

/**
 * Class m211124_160259_migrate_hotfix_harga_jual
 */
class m211124_160259_migrate_hotfix_harga_jual extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
CREATE OR REPLACE FUNCTION \"public\".\"harga_jual\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
        IF (NEW.det is null)
            THEN 
                NEW.hargajual_oa = NEW.hargasatuan_oa * NEW.qty_oa;
            ELSE 
                NEW.hargajual_oa = NEW.hargasatuan_oa * NEW.det;
        END IF; 

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211124_160259_migrate_hotfix_harga_jual cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211124_160259_migrate_hotfix_harga_jual cannot be reverted.\n";

        return false;
    }
    */
}
