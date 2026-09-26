<?php

use yii\db\Migration;

/**
 * Class m200323_120937_migrate_20200323_1
 */
class m200323_120937_migrate_20200323_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('COMMENT ON COLUMN "public"."tandabuktikeluar_t"."pembayarantransaksi_id" IS \'jenis transaksi = pengeluaran\';');

        $this->execute('DELETE from lookup_m WHERE lookup_id BETWEEN 668 and 702;');

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(668, 'jenis_transaksi', 'Pemasukan', 'Pemasukan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(669, 'jenis_transaksi', 'Pengeluaran', 'Pengeluaran', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(700, 'tipe_transaksi', 'Vendor', 'Vendor', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(701, 'tipe_transaksi', 'Karyawan', 'Karyawan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(702, 'tipe_transaksi', 'Pasien', 'Pasien', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        
        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id in (36,37);');

        $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(37, 'Transaksi Pengeluaran', 'TK', 'TK202030003', '0003', '0'),
(36, 'Transaksi Pemasukan', 'TM', 'TM2020323006', '006', '0');");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200323_120937_migrate_20200323_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200323_120937_migrate_20200323_1 cannot be reverted.\n";

        return false;
    }
    */
}
