<?php

use yii\db\Migration;

/**
 * Class m230104_043305_GB587_lookuptransaksi_t
 */
class m230104_043305_GB587_lookuptransaksi_t extends Migration

{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         // Delete Old Data
         $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='VisitSubDokterSpesialis';");

         // Insert New Data
         $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('VisitSubDokterSpesialis', 3924, 'Tindakan Visit Sub Dokter Spesialis', null);");
         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230104_043305_GB587_lookuptransaksi_t cannot be reverted.\n";
        return false;
    }
}
