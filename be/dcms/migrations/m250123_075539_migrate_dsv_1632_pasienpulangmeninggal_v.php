<?php

use yii\db\Migration;

/**
 * Class m250123_075539_migrate_dsv_1632_pasienpulangmeninggal_v
 */
class m250123_075539_migrate_dsv_1632_pasienpulangmeninggal_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS pasienpulangmeninggal_v");
        $pasienpulangmeninggal_v = file_get_contents(__DIR__ . '/definitions/pasienpulangmeninggal_v.sql');
        $this->execute($pasienpulangmeninggal_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250123_075539_migrate_dsv_1632_pasienpulangmeninggal_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250123_075539_migrate_dsv_1632_pasienpulangmeninggal_v cannot be reverted.\n";

        return false;
    }
    */
}
