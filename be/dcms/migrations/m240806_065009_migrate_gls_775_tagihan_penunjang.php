<?php

use yii\db\Migration;

/**
 * Class m240806_065009_migrate_gls_775_tagihan_penunjang
 */
class m240806_065009_migrate_gls_775_tagihan_penunjang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanpenunjang_v");
        $infotagihanpenunjang_v = file_get_contents(__DIR__ . '/definitions/infotagihanpenunjang_v.view.sql');
        $this->execute($infotagihanpenunjang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240806_065009_migrate_gls_775_tagihan_penunjang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240806_065009_migrate_gls_775_tagihan_penunjang cannot be reverted.\n";

        return false;
    }
    */
}
