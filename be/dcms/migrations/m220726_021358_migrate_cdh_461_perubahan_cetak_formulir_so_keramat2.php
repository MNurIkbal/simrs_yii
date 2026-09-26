<?php

use yii\db\Migration;

/**
 * Class m220726_021358_migrate_cdh_461_perubahan_cetak_formulir_so_keramat2
 */
class m220726_021358_migrate_cdh_461_perubahan_cetak_formulir_so_keramat2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('
	   			ALTER TABLE "public"."konfiggudang_k" ADD IF NOT EXISTS  "cetak_form_so" json;');
		
	    $this->execute('
	   			update konfiggudang_k set cetak_form_so =\'{"layout":"cetak-form","kode_doc":"transaksi-formulir-barang"}\'
	   			WHERE konfiggudang_id = 1;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220726_021358_migrate_cdh_461_perubahan_cetak_formulir_so_keramat2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220726_021358_migrate_cdh_461_perubahan_cetak_formulir_so_keramat2 cannot be reverted.\n";

        return false;
    }
    */
}
