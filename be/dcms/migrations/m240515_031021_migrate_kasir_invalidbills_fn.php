<?php

use yii\db\Migration;

/**
 * Class m240515_031021_migrate_kasir_invalidbills_fn
 */
class m240515_031021_migrate_kasir_invalidbills_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $invalidbills_fn = file_get_contents(__DIR__ . '/definitions/invalidbills.fn.sql');
        $this->execute($invalidbills_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240515_031021_migrate_kasir_invalidbills_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240515_031021_migrate_kasir_invalidbills_fn cannot be reverted.\n";

        return false;
    }
    */
}
