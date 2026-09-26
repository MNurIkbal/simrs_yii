<?php

use yii\db\Migration;

/**
 * Class m210914_140446_improvment_asesmenmedis_rd_US1284
 */
class m210914_140446_improvment_asesmenmedis_rd_US1284 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS kasus_kecelakaan TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS ket_imt VARCHAR(100);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210914_140446_improvment_asesmenmedis_rd_US1284 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210914_140446_improvment_asesmenmedis_rd_US1284 cannot be reverted.\n";

        return false;
    }
    */
}
