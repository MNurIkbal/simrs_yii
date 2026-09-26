<?php

use yii\db\Migration;

/**
 * Class m230922_104738_rpp_452_asesmenmedisrd_t
 */
class m230922_104738_rpp_452_asesmenmedisrd_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedisrd_t 
            ADD IF NOT EXISTS diagnosis json NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230922_104738_rpp_452_asesmenmedisrd_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230922_104738_rpp_452_asesmenmedisrd_t cannot be reverted.\n";

        return false;
    }
    */
}
