<?php

use yii\db\Migration;

/**
 * Class m240308_105604_migrate_DSV_1065_surat_keterangan_pasien_t
 */
class m240308_105604_migrate_DSV_1065_surat_keterangan_pasien_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE surat_keterangan_pasien_t ADD IF NOT EXISTS is_eklaim bool DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240308_105604_migrate_DSV_1065_surat_keterangan_pasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240308_105604_migrate_DSV_1065_surat_keterangan_pasien_t cannot be reverted.\n";

        return false;
    }
    */
}
