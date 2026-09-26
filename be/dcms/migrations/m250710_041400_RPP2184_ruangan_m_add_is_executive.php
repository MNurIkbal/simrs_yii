<?php

use yii\db\Migration;

/**
 * Class m250710_041400_RPP2184_ruangan_m_add_is_executive
 */
class m250710_041400_RPP2184_ruangan_m_add_is_executive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.ruangan_m ADD IF NOT EXISTS is_executive BOOLEAN NOT NULL DEFAULT FALSE;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250710_041400_RPP2184_ruangan_m_add_is_executive cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250710_041400_RPP2184_ruangan_m_add_is_executive cannot be reverted.\n";

        return false;
    }
    */
}
