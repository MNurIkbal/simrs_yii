<?php

use yii\db\Migration;

/**
 * Class m200810_020630_migrate_mhkn_20200810_2
 */
class m200810_020630_migrate_mhkn_20200810_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakankomponen_t" ADD COLUMN "tarifpenyulit_komponen" float8 DEFAULT 0;');
        
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ALTER COLUMN "penyulit_tindakan" SET DEFAULT false;');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200810_020630_migrate_mhkn_20200810_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200810_020630_migrate_mhkn_20200810_2 cannot be reverted.\n";

        return false;
    }
    */
}
