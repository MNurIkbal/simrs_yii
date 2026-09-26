<?php

use yii\db\Migration;

/**
 * Class m231031_040703_migrate_unduh_dokumen_sy_kunjungan
 */
class m231031_040703_migrate_unduh_dokumen_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE sy_kunjungan ADD COLUMN IF NOT EXISTS status_unduh_dokumen int4 NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231031_040703_migrate_unduh_dokumen_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231031_040703_migrate_unduh_dokumen_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
