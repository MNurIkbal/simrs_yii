<?php

use yii\db\Migration;

/**
 * Class m250702_082844_rpp_2175_harga_jual
 */
class m250702_082844_rpp_2175_harga_jual extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $harga_jual = file_get_contents(__DIR__ . '/definitions/harga_jual.sql');
        $this->execute($harga_jual);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250702_082844_rpp_2175_harga_jual cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250702_082844_rpp_2175_harga_jual cannot be reverted.\n";

        return false;
    }
    */
}
