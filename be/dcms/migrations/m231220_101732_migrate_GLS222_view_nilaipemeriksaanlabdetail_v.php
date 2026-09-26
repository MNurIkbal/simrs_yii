<?php

use yii\db\Migration;

/**
 * Class m231220_101732_migrate_GLS222_view_nilaipemeriksaanlabdetail_v
 */
class m231220_101732_migrate_GLS222_view_nilaipemeriksaanlabdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS nilaipemeriksaanlabdetail_v");
        $nilaipemeriksaanlabdetail_v = file_get_contents(__DIR__ . '/definitions/nilaipemeriksaanlabdetail_v.sql');
        $this->execute($nilaipemeriksaanlabdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231220_101732_migrate_GLS222_view_nilaipemeriksaanlabdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231220_101732_migrate_GLS222_view_nilaipemeriksaanlabdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
