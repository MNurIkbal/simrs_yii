<?php

use yii\db\Migration;

/**
 * Class m230405_021723_migrate_ga223_table_dokumenupload_t
 */
class m230405_021723_migrate_ga223_table_dokumenupload_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE dokumenupload_t
        ADD IF NOT EXISTS pasien_id INT4,
        ADD IF NOT EXISTS ruangan_id INT4,
        ADD IF NOT EXISTS dokter_id INT4,
        ADD IF NOT EXISTS doc_date TIMESTAMP,
        ADD IF NOT EXISTS nama_dokumen_freetext TEXT
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230405_021723_migrate_ga223_table_dokumenupload_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230405_021723_migrate_ga223_table_dokumenupload_t cannot be reverted.\n";

        return false;
    }
    */
}
