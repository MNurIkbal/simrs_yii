<?php

use yii\db\Migration;

/**
 * Class m240116_153843_rpp_845_ruangan_konversikosong
 */
class m240116_153843_rpp_845_ruangan_konversikosong extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderlineobat_v");
        $newodoo_saleorderlineobat_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderlineobat_v.sql');
        $this->execute($newodoo_saleorderlineobat_v);
        
        $this->execute("DROP VIEW IF EXISTS int_ruangan_v");
        $int_ruangan_v = file_get_contents(__DIR__ . '/definitions/int_ruangan_v.sql');
        $this->execute($int_ruangan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240116_153843_rpp_845_ruangan_konversikosong cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240116_153843_rpp_845_ruangan_konversikosong cannot be reverted.\n";

        return false;
    }
    */
}
