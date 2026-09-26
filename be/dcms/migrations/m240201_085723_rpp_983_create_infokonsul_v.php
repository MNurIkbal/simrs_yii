<?php

use yii\db\Migration;

/**
 * Class m240201_085723_rpp_983_create_infokonsul_v
 */
class m240201_085723_rpp_983_create_infokonsul_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infokonsul_v");
        $infokonsul_v = file_get_contents(__DIR__ . '/definitions/infokonsul_v.sql');
        $this->execute($infokonsul_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240201_085723_rpp_983_create_infokonsul_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240201_085723_rpp_983_create_infokonsul_v cannot be reverted.\n";

        return false;
    }
    */
}
