<?php

use yii\db\Migration;

/**
 * Class m200625_012937_migarate_mhkn_20200625
 */
class m200625_012937_migarate_mhkn_20200625 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pemeriksaanlab_m" ALTER COLUMN "pemeriksaanlab_kode" TYPE varchar(50) COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."pemeriksaanrad_m" ALTER COLUMN "pemeriksaanrad_kode" TYPE varchar(50) COLLATE "pg_catalog"."default";');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200625_012937_migarate_mhkn_20200625 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200625_012937_migarate_mhkn_20200625 cannot be reverted.\n";

        return false;
    }
    */
}
