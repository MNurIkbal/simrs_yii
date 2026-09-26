<?php

use yii\db\Migration;

/**
 * Class m220616_130350_migrate_skema_fisioterapi_lookup_m_lookuptransaksi_m
 */
class m220616_130350_migrate_skema_fisioterapi_lookup_m_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("
            DELETE from lookup_m where lookup_id IN (1202,1210);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1202, 'status_program_fisio', 'EXPIRED', 'EXPIRED', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1210, 'status_program_fisio', 'DROP OUT', 'DROP OUT', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_rj';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('instalasi_rj', 1, 'Instalasi Rawat Jalan', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_ri';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('instalasi_ri', 3, 'Instalasi Rawat Inap', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('instalasi_fisio', 7, 'Instalasi Fisioterapi', NULL, NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220616_130350_migrate_skema_fisioterapi_lookup_m_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220616_130350_migrate_skema_fisioterapi_lookup_m_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
