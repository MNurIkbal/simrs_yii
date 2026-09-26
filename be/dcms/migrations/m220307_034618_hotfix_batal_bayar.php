<?php

use yii\db\Migration;

/**
 * Class m220307_034618_hotfix_batal_bayar
 */
class m220307_034618_hotfix_batal_bayar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
            ALTER TABLE pemakaianuangmuka_t ADD IF NOT EXISTS pembayaran_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220307_034618_hotfix_batal_bayar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220307_034618_hotfix_batal_bayar cannot be reverted.\n";

        return false;
    }
    */
}
