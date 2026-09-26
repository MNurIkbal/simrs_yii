<?php

use yii\db\Migration;

/**
 * Class m251024_085701_migrate_SBAR_infosbar_v
 */
class m251024_085701_migrate_SBAR_infosbar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS infosbar_v;');
        $infosbar_v = file_get_contents(__DIR__ . '/definitions/infosbar_v.sql');
        $this->execute($infosbar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251024_085701_migrate_SBAR_infosbar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251024_085701_migrate_SBAR_infosbar_v cannot be reverted.\n";

        return false;
    }
    */
}
