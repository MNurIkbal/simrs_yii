<?php

use yii\db\Migration;

/**
 * Class m210124_054354_migrate_data_kelompokpegawai
 */
class m210124_054354_migrate_data_kelompokpegawai extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from kelompokpegawai_m WHERE kelompokpegawai_id=13;');
    
        $this->execute("INSERT INTO public.kelompokpegawai_m(kelompokpegawai_id, kelompokpegawai_nama, kelompokpegawai_namalainnya, kelompokpegawai_fungsi, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(13, 'Tenaga Analis', 't_analis', 'tenaga analis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210124_054354_migrate_data_kelompokpegawai cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210124_054354_migrate_data_kelompokpegawai cannot be reverted.\n";

        return false;
    }
    */
}
