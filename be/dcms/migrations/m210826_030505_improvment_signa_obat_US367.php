<?php

use yii\db\Migration;

/**
 * Class m210826_030505_improvment_signa_obat_US367
 */
class m210826_030505_improvment_signa_obat_US367 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE signaobat_m ADD IF NOT EXISTS qty_obat VARCHAR(20);
        ');


        $this->execute('
            ALTER TABLE signaobat_m ADD IF NOT EXISTS iterasi VARCHAR(20);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210826_030505_improvment_signa_obat_US367 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210826_030505_improvment_signa_obat_US367 cannot be reverted.\n";

        return false;
    }
    */
}
