<?php

use yii\db\Migration;

/**
 * Class m210902_094257_improvment_tindakan_bmhp_US1288
 */
class m210902_094257_improvment_tindakan_bmhp_US1288 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE signaobat_m ADD IF NOT EXISTS signa_kode VARCHAR(50);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210902_094257_improvment_tindakan_bmhp_US1288 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210902_094257_improvment_tindakan_bmhp_US1288 cannot be reverted.\n";

        return false;
    }
    */
}
