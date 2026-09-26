<?php

use yii\db\Migration;

/**
 * Class m231030_101201_migrate_DSV742_newpendaftaranobat_v
 */
class m231030_101201_migrate_DSV742_newpendaftaranobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $this->execute("DROP VIEW IF EXISTS newpendaftaranobat_v");
        - $newpendaftaranobat_v = file_get_contents(__DIR__ . '/definitions/newpendaftaranobat_v.sql');
        - $this->execute($newpendaftaranobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_101201_migrate_DSV742_newpendaftaranobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_101201_migrate_DSV742_newpendaftaranobat_v cannot be reverted.\n";

        return false;
    }
    */
}
