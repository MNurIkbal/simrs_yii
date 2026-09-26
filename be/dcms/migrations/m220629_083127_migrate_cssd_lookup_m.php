<?php

use yii\db\Migration;

/**
 * Class m220629_083127_migrate_cssd_lookup_m
 */
class m220629_083127_migrate_cssd_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1190, 1191,1192,1193,1194,1195,1196,1197,1200,1201,1213,1214,1215,1216,1217,1221,1222,1223,1237,1238);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1190, 'status_cssd', 'Pengajuan', 'Pengajuan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1191, 'status_cssd', 'Diterima', 'Diterima', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1192, 'status_cssd', 'Diproses', 'Diproses', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1193, 'status_cssd', 'Dikirim', 'Dikirim', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1194, 'status_cssd', 'Pencucian', 'Pencucian', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1195, 'status_cssd', 'Pengeringan', 'Pengeringan', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1196, 'status_cssd', 'Pengemasan', 'Pengemasan', 5, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1197, 'status_cssd', 'Selesai', 'Selesai', 8, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1200, 'status_cssd', 'Belum Diproses', 'Belum Diproses', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1201, 'status_cssd', 'Sterilisasi', 'Sterilisasi', 6, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1213, 'status_cssd', 'Dibatalkan', 'Dibatalkan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1214, 'status_cssd', 'Diterima Unit CSSD', 'Diterima Unit CSSD', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1215, 'status_cssd', 'Predining', 'Predining', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1216, 'status_cssd', 'Pembilasan', 'Pembilasan', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1217, 'status_cssd', 'Pendinginan', 'Pendinginan', 7, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1221, 'status_cssdbatal', 'Batal Pengajuan', 'Batal Pengajuan', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1222, 'status_cssdbatal', 'Batal Penerimaan CSSD', 'Batal Penerimaan CSSD', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1223, 'status_cssdbatal', 'Batal Sterilisasi', 'Batal Sterilisasi', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1237, 'status_cssdbatal', 'Batal Pengiriman', 'Batal Pengiriman', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1238, 'status_cssdbatal', 'Batal Penerimaan Unit', 'Batal Penerimaan Unit', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_083127_migrate_cssd_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220629_083127_migrate_cssd_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
