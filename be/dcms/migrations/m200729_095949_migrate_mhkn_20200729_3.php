<?php

use yii\db\Migration;

/**
 * Class m200729_095949_migrate_mhkn_20200729_3
 */
class m200729_095949_migrate_mhkn_20200729_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN "penyulit_tindakan" bool;');
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN "tarifpenyulit_tindakan" float8 DEFAULT 0;');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_095949_migrate_mhkn_20200729_3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_095949_migrate_mhkn_20200729_3 cannot be reverted.\n";

        return false;
    }
    */
}
