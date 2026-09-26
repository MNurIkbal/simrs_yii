<?php

use yii\db\Migration;

/**
 * Class m221122_032852_mhg4280_migrate_lookuptransaksi_tindakan_akomodasi
 */
class m221122_032852_mhg4280_migrate_lookuptransaksi_tindakan_akomodasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DELETE FROM lookuptransaksi_m WHERE kode_transaksi=\'tindakan_akomodasi\';');

        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('tindakan_akomodasi', (select daftartindakan_id from daftartindakan_m where is_akomodasi is TRUE and is_deleted IS FALSE limit 1), 'tindakan akomodasi id', NULL);");
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221122_032852_mhg4280_migrate_lookuptransaksi_tindakan_akomodasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221122_032852_mhg4280_migrate_lookuptransaksi_tindakan_akomodasi cannot be reverted.\n";

        return false;
    }
    */
}
