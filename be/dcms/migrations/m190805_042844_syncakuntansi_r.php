<?php

use yii\db\Migration;

/**
 * Class m190805_042844_syncakuntansi_r
 */
class m190805_042844_syncakuntansi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      ALTER TABLE syncakuntansi_r ADD stokopname_id int4;
        ');

        $this->execute('
       ALTER TABLE syncakuntansi_r ADD stokopnamebarang_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190805_042844_syncakuntansi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_042844_syncakuntansi_r cannot be reverted.\n";

        return false;
    }
    */
}
