<?php

use yii\db\Migration;

/**
 * Class m240725_075757_migrate_dsv_1339_obatalkesdetail_v
 */
class m240725_075757_migrate_dsv_1339_obatalkesdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS obatalkesdetail_v");
        $obatalkesdetail_v = file_get_contents(__DIR__ . '/definitions/obatalkesdetail_v.sql');
        $this->execute($obatalkesdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240725_075757_migrate_dsv_1339_obatalkesdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240725_075757_migrate_dsv_1339_obatalkesdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
