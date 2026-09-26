<?php

use yii\db\Migration;

/**
 * Class m211018_103626_improvment_asper_pilihan_skala_US1272
 */
class m211018_103626_improvment_asper_pilihan_skala_US1272 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS pilih_skala TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211018_103626_improvment_asper_pilihan_skala_US1272 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211018_103626_improvment_asper_pilihan_skala_US1272 cannot be reverted.\n";

        return false;
    }
    */
}
