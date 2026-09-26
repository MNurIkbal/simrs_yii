<?php

use yii\db\Migration;

/**
 * Class m210806_064812_improvment_pengisian_inputan_carakeluar_US3944
 */
class m210806_064812_improvment_pengisian_inputan_carakeluar_US3944 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE carakeluar_m ADD IF NOT EXISTS is_freetext BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210806_064812_improvment_pengisian_inputan_carakeluar_US3944 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210806_064812_improvment_pengisian_inputan_carakeluar_US3944 cannot be reverted.\n";

        return false;
    }
    */
}
