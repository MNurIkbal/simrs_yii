<?php

use yii\db\Migration;

/**
 * Class m221210_231634_migrate_GB123_lookuptransaksi_t
 */
class m221210_231634_migrate_GB123_lookuptransaksi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Delete Old Data
        $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='show_order_fisioterapi_tindakan_ids';");
        $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='spesialis_fisioterapi_ids';");

        // Insert New Data
        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('show_order_fisioterapi_tindakan_ids', 0, 'List Tindakan Order Selain Spesialis Fisioterapi', '[1089,1106,1112,1099,1109,1108,1103,1105]');");
        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('spesialis_fisioterapi_ids', 0, 'List Spesialis Fisioterapi Ids (Kebutuhan Order Fisio)', '[90,91]');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221210_231634_migrate_GB123_lookuptransaksi_t cannot be reverted.\n";
        return false;
    }
}
