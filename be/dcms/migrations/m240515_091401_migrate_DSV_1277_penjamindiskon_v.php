<?php

use yii\db\Migration;

/**
 * Class m240515_091401_migrate_DSV_1277_penjamindiskon_v
 */
class m240515_091401_migrate_DSV_1277_penjamindiskon_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS penjamindiskon_v");
        $penjamindiskon_v = file_get_contents(__DIR__ . '/definitions/penjamindiskon_v.view.sql');
        $this->execute($penjamindiskon_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240515_091401_migrate_DSV_1277_penjamindiskon_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240515_091401_migrate_DSV_1277_penjamindiskon_v cannot be reverted.\n";

        return false;
    }
    */
}
