<?php

use yii\db\Migration;

/**
 * Class m231101_021953_alter_dokumen_m_eklaim
 */
class m231101_021953_alter_dokumen_m_eklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE dokumen_m 
            ADD IF NOT EXISTS is_eklaim bool DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_021953_alter_dokumen_m_eklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_021953_alter_dokumen_m_eklaim cannot be reverted.\n";

        return false;
    }
    */
}
