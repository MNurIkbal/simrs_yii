<?php

use yii\db\Migration;

/**
 * Class m231116_114416_rpp_901_add_estimasi
 */
class m231116_114416_rpp_901_add_estimasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE pendaftaranol_t ADD COLUMN IF NOT EXISTS estimasidilayani bigint NULL");

        $this->execute("ALTER TABLE antrian_t ADD COLUMN IF NOT EXISTS estimasidilayani bigint NULL");

        $this->execute("ALTER TABLE antrian_t ADD COLUMN IF NOT EXISTS tglpilih_antrian timestamp NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231116_114416_rpp_901_add_estimasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231116_114416_rpp_901_add_estimasi cannot be reverted.\n";

        return false;
    }
    */
}
