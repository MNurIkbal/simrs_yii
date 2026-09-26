<?php

use yii\db\Migration;

/**
 * Class m251016_085243_GLKJ_8_migrate_lookuptransaksim_konfig_label_gizi
 */
class m251016_085243_GLKJ_8_migrate_lookuptransaksim_konfig_label_gizi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'konfig_label_gizi'");

        $this->execute("
            INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value,kode_nama,kode_singkatan) VALUES
	        ('konfig_label_gizi',0,'True = cetakan gizi memilih waktu untuk penanda dalam cetakan, False = cetakan gizi tanpa memilih waktu ',NULL,NULL,NULL);
        ");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251016_085243_GLKJ_8_migrate_lookuptransaksim_konfig_label_gizi cannot be reverted.\n";

        return false;
    }
    */
}
