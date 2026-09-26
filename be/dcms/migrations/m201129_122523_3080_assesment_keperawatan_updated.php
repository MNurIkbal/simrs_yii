<?php

use yii\db\Migration;

/**
 * Class m201129_122523_3080_assesment_keperawatan_updated
 */
class m201129_122523_3080_assesment_keperawatan_updated extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('
            ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS hasil_resiko_jatuh TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201129_122523_3080_assesment_keperawatan_updated cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201129_122523_3080_assesment_keperawatan_updated cannot be reverted.\n";

        return false;
    }
    */
}
