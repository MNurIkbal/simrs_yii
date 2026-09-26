<?php

use yii\db\Migration;

/**
 * Class m190806_031504_syncakuntansi_r
 */
class m190806_031504_syncakuntansi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      ALTER TABLE syncakuntansi_r ADD pengajuanklaim_id int4;
        ');

           }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_031504_syncakuntansi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_031504_syncakuntansi_r cannot be reverted.\n";

        return false;
    }
    */
}
