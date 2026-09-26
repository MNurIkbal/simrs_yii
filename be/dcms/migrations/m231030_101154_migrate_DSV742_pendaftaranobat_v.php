<?php

use yii\db\Migration;

/**
 * Class m231030_101154_migrate_DSV742_pendaftaranobat_v
 */
class m231030_101154_migrate_DSV742_pendaftaranobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $this->execute("DROP VIEW IF EXISTS pendaftaranobat_v");
        - $pendaftaranobat_v = file_get_contents(__DIR__ . '/definitions/pendaftaranobat_v.sql');
        - $this->execute($pendaftaranobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_101154_migrate_DSV742_pendaftaranobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_101154_migrate_DSV742_pendaftaranobat_v cannot be reverted.\n";

        return false;
    }
    */
}
