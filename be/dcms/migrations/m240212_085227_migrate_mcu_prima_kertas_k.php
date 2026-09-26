<?php

use yii\db\Migration;

/**
 * Class m240212_085227_migrate_mcu_prima_kertas_k
 */
class m240212_085227_migrate_mcu_prima_kertas_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM kertas_k WHERE kertas_kode = 'mcu-prima' AND kertas_nama = 'mcu-prima';");
        $this->execute("INSERT INTO public.kertas_k
        (kertas_kode, kertas_nama, panjang, lebar, batas_kiri, batas_kanan, batas_atas, batas_bawah, ppi, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES('mcu-prima', 'mcu-prima', 29.7, 21.0, 1.0, 1.0, 0.0, 0.0, 0.0, NULL, '2020-06-13 00:00:00.000', NULL, 3, '2020-07-07 12:33:25.000', 1, false, false, NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_085227_migrate_mcu_prima_kertas_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_085227_migrate_mcu_prima_kertas_k cannot be reverted.\n";

        return false;
    }
    */
}
