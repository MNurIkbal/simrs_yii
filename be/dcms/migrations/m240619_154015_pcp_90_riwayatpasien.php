<?php

use yii\db\Migration;

/**
 * Class m240619_154015_pcp_90_riwayatpasien
 */
class m240619_154015_pcp_90_riwayatpasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS inforiwayatpasien_v");
        $inforiwayatpasien_v = file_get_contents(__DIR__ . '/definitions/inforiwayatpasien_v.view.sql');
        $this->execute($inforiwayatpasien_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240619_154015_pcp_90_riwayatpasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240619_154015_pcp_90_riwayatpasien cannot be reverted.\n";

        return false;
    }
    */
}
