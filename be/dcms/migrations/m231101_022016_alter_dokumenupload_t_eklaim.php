<?php

use yii\db\Migration;

/**
 * Class m231101_022016_alter_dokumenupload_t_eklaim
 */
class m231101_022016_alter_dokumenupload_t_eklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE dokumenupload_t 
        ADD IF NOT EXISTS is_eklaim bool DEFAULT false;
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_022016_alter_dokumenupload_t_eklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_022016_alter_dokumenupload_t_eklaim cannot be reverted.\n";

        return false;
    }
    */
}
