<?php

use yii\db\Migration;

/**
 * Class m221213_094450_migrate_GB234_lookuptransaksi_t
 */
class m221213_094450_migrate_GB234_lookuptransaksi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         // Delete Old Data
         $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='daftartindakan_spesialis_ids';");
         $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='daftartindakan_subspesialis_ids';");
 
         // Insert New Data
         $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('daftartindakan_spesialis_ids', 0, 'List daftartindakan is_checked jika spesialis, ketika order di pendaftaran', '[4454]');");
         $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('daftartindakan_subspesialis_ids', 0, 'List daftartindakan is_checked jika sub_spesialis, ketika order di pendaftaran', '[4455, 4456, 3916]');"); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_094450_migrate_GB234_lookuptransaksi_t cannot be reverted.\n";
        return false;
    }
}
