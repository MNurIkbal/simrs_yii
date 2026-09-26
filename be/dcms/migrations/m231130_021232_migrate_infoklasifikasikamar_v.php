<?php

use yii\db\Migration;

/**
 * Class m231130_021232_migrate_infoklasifikasikamar_v
 */
class m231130_021232_migrate_infoklasifikasikamar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoklasifikasikamar_v");
        $infoklasifikasikamar_v = file_get_contents(__DIR__ . '/definitions/infoklasifikasikamar_v.sql');
        $this->execute($infoklasifikasikamar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231130_021232_migrate_infoklasifikasikamar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231130_021232_migrate_infoklasifikasikamar_v cannot be reverted.\n";

        return false;
    }
    */
}
