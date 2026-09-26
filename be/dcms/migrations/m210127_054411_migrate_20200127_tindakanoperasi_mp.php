<?php

use yii\db\Migration;

/**
 * Class m210127_054411_migrate_20200127_tindakanoperasi_mp
 */
class m210127_054411_migrate_20200127_tindakanoperasi_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('ALTER TABLE "public"."tindakanoperasi_mp" ADD COLUMN if not exists "prosentase" float8 DEFAULT 0;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_054411_migrate_20200127_tindakanoperasi_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_054411_migrate_20200127_tindakanoperasi_mp cannot be reverted.\n";

        return false;
    }
    */
}
