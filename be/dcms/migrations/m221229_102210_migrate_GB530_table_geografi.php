<?php

use yii\db\Migration;

/**
 * Class m221229_102210_migrate_GB530_table_geografi
 */
class m221229_102210_migrate_GB530_table_geografi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE propinsi_m ADD IF NOT EXISTS kode_propinsi_bpjs varchar default NULL;
        ');
        $this->execute('
            ALTER TABLE kabupaten_m ADD IF NOT EXISTS kode_kabupaten_bpjs varchar default NULL;
        ');
        $this->execute('
            ALTER TABLE kecamatan_m ADD IF NOT EXISTS kode_kecamatan_bpjs varchar default NULL;
        ');
        $this->execute('
            ALTER TABLE kelurahan_m ADD IF NOT EXISTS kode_kelurahan_bpjs varchar default NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221229_102210_migrate_GB530_table_geografi cannot be reverted.\n";

        return false;
    }
}
