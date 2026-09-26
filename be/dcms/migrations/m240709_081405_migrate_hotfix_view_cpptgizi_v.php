<?php

use yii\db\Migration;

/**
 * Class m240709_081405_migrate_hotfix_view_cpptgizi_v
 */
class m240709_081405_migrate_hotfix_view_cpptgizi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS cpptgizi_v");
        $cpptgizi_v = file_get_contents(__DIR__ . '/definitions/cpptgizi_v.sql');
        $this->execute($cpptgizi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240709_081405_migrate_hotfix_view_cpptgizi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240709_081405_migrate_hotfix_view_cpptgizi_v cannot be reverted.\n";

        return false;
    }
    */
}
