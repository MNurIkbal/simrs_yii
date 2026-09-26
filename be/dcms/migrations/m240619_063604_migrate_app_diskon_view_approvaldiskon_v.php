<?php

use yii\db\Migration;

/**
 * Class m240619_063604_migrate_app_diskon_view_approvaldiskon_v
 */
class m240619_063604_migrate_app_diskon_view_approvaldiskon_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS approvaldiskon_v");
        $approvaldiskon_v = file_get_contents(__DIR__ . '/definitions/approvaldiskon_v.sql');
        $this->execute($approvaldiskon_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240619_063604_migrate_app_diskon_view_approvaldiskon_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240619_063604_migrate_app_diskon_view_approvaldiskon_v cannot be reverted.\n";

        return false;
    }
    */
}
