<?php

use yii\db\Migration;

/**
 * Class m241107_094203_migrate_GLS836_laporanjasapelayananrajal_v
 */
class m241107_094203_migrate_GLS836_laporanjasapelayananrajal_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanjasapelayananrajal_v");
        $laporanjasapelayananrajal_v = file_get_contents(__DIR__ . '/definitions/laporanjasapelayananrajal_v.sql');
        $this->execute($laporanjasapelayananrajal_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241107_094203_migrate_GLS836_laporanjasapelayananrajal_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241107_094203_migrate_GLS836_laporanjasapelayananrajal_v cannot be reverted.\n";

        return false;
    }
    */
}
