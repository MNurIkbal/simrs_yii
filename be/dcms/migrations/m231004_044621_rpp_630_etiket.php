<?php

use yii\db\Migration;

/**
 * Class m231004_044621_rpp_630_etiket
 */
class m231004_044621_rpp_630_etiket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE reseptur_t ADD COLUMN IF NOT EXISTS tgl_cetak_etiket TIMESTAMP NULL");

        $this->execute("ALTER TABLE reseptur_t ADD COLUMN IF NOT EXISTS cetak_etiket_oleh int4 NULL");

        $this->execute("ALTER TABLE penjualanresep_t  ADD COLUMN IF NOT EXISTS tgl_cetak_etiket TIMESTAMP NULL");

        $this->execute("ALTER TABLE penjualanresep_t ADD COLUMN IF NOT EXISTS cetak_etiket_oleh int4 NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231004_044621_rpp_630_etiket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231004_044621_rpp_630_etiket cannot be reverted.\n";

        return false;
    }
    */
}
