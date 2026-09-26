<?php

use yii\db\Migration;

/**
 * Class m231130_024638_migrate_status_kamartempattidur_m_insert
 */
class m231130_024638_migrate_status_kamartempattidur_m_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $status_kamartempattidur_m_insert = file_get_contents(__DIR__ . '/definitions/status_kamartempattidur_m_insert.fn.sql');
        $this->execute($status_kamartempattidur_m_insert);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231130_024638_migrate_status_kamartempattidur_m_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231130_024638_migrate_status_kamartempattidur_m_insert cannot be reverted.\n";

        return false;
    }
    */
}
