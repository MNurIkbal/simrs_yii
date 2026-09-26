<?php

use yii\db\Migration;

/**
 * Class m211112_073924_hotfix_tindakan_permintaan_makan
 */
class m211112_073924_hotfix_tindakan_permintaan_makan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS daftartindakan_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211112_073924_hotfix_tindakan_permintaan_makan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211112_073924_hotfix_tindakan_permintaan_makan cannot be reverted.\n";

        return false;
    }
    */
}
