<?php

use yii\db\Migration;

/**
 * Class m230822_025901_RPP_497_pendaftaran_t
 */
class m230822_025901_RPP_497_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE pendaftaran_t ADD COLUMN IF NOT EXISTS referal_pegawai_id int4 NULL");
        $this->execute("ALTER TABLE pendaftaran_t ADD COLUMN IF NOT EXISTS referal_luar varchar(50) NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_025901_RPP_497_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_025901_RPP_497_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
