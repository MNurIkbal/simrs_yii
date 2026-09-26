<?php

use yii\db\Migration;

/**
 * Class m200923_033218_migrate_20200923_kamarruangan
 */
class m200923_033218_migrate_20200923_kamarruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE kamarruangan_m ADD COLUMN IF NOT EXISTS durasi float8;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200923_033218_migrate_20200923_kamarruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200923_033218_migrate_20200923_kamarruangan cannot be reverted.\n";

        return false;
    }
    */
}
