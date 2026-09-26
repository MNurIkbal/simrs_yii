<?php

use yii\db\Migration;

/**
 * Class m221213_094451_migrate_GB234_lookuptransaksi_t
 */
class m221213_094451_migrate_GB234_lookuptransaksi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         // Delete Old Data
         $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='daftartindakan_spesialis_ids';");
         $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='daftartindakan_subspesialis_ids';");
         $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='daftartindakan_umum_ids';");
 
         // Insert New Data
         $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('daftartindakan_spesialis_ids', 0, 'List daftartindakan is_checked jika spesialis, ketika order di pendaftaran', '[4449]');");
         $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('daftartindakan_subspesialis_ids', 0, 'List daftartindakan is_checked jika sub_spesialis, ketika order di pendaftaran', '[4450]');");
         $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('daftartindakan_umum_ids', 0, 'List daftartindakan is_checked jika umum, ketika order di pendaftaran', '[]');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_094451_migrate_GB234_lookuptransaksi_t cannot be reverted.\n";
        return false;
    }
}
