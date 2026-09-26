<?php

use yii\db\Migration;

/**
 * Class m250812_072717_migrate_restriction_infostokobatalkes_fnr_new
 */
class m250812_072717_migrate_restriction_infostokobatalkes_fnr_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS infostokobatalkes_fnr_new");
        $infostokobatalkes_fnr_new = file_get_contents(__DIR__ . '/definitions/infostokobatalkes_fnr_new.sql');
        $this->execute($infostokobatalkes_fnr_new);
    }
    

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250812_072717_migrate_restriction_infostokobatalkes_fnr_new cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250812_072717_migrate_restriction_infostokobatalkes_fnr_new cannot be reverted.\n";

        return false;
    }
    */
}
