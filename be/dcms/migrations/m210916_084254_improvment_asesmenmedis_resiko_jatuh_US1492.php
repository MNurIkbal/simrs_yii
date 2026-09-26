<?php

use yii\db\Migration;

/**
 * Class m210916_084254_improvment_asesmenmedis_resiko_jatuh_US1492
 */
class m210916_084254_improvment_asesmenmedis_resiko_jatuh_US1492 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenrdresikojatuh_t ADD IF NOT EXISTS asesmenawal_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210916_084254_improvment_asesmenmedis_resiko_jatuh_US1492 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210916_084254_improvment_asesmenmedis_resiko_jatuh_US1492 cannot be reverted.\n";

        return false;
    }
    */
}
