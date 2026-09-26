<?php

use yii\db\Migration;

/**
 * Class m231127_063702_alter_kamartempattidur_m_applicare
 */
class m231127_063702_alter_kamartempattidur_m_applicare extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE public.kamartempattidur_m ADD IF NOT EXISTS is_terisi bool NULL DEFAULT false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_063702_alter_kamartempattidur_m_applicare cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_063702_alter_kamartempattidur_m_applicare cannot be reverted.\n";

        return false;
    }
    */
}
