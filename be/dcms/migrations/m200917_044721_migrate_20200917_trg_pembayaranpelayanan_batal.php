<?php

use yii\db\Migration;

/**
 * Class m200917_044721_migrate_20200917_trg_pembayaranpelayanan_batal
 */
class m200917_044721_migrate_20200917_trg_pembayaranpelayanan_batal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaranpelayanan_batal\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
  

BEGIN  

  IF(NEW.is_deleted = TRUE)
    THEN
      UPDATE pemakaianuangmuka_t SET is_deleted = TRUE
      WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;

  END IF;


RETURN NEW;

END

\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "pembayaranpelayanan_batal" AFTER UPDATE ON "public"."pembayaranpelayanan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaranpelayanan_batal"();');

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_044721_migrate_20200917_trg_pembayaranpelayanan_batal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_044721_migrate_20200917_trg_pembayaranpelayanan_batal cannot be reverted.\n";

        return false;
    }
    */
}
