<?php

use yii\db\Migration;

/**
 * Class m220324_161847_migrate_skema_fisio_lookup_m
 */
class m220324_161847_migrate_skema_fisio_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1170,1171,1172);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1171, 'status_program_fisio', 'OPEN', 'OPEN', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1172, 'status_program_fisio', 'BATAL', 'BATAL', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1170, 'status_program_fisio', 'CLOSE', 'CLOSE', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");

        $this->execute("
            DELETE from lookup_m where lookup_id IN (1202);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1202, 'status_program_fisio', 'EXPIRED', 'EXPIRED', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");

        $this->execute("
            DELETE from lookup_m where lookup_id IN (1206,1207,1208,1209);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1206, 'status_kunjungan_fisio', 'Belum Realisasi', 'Belum Realisasi', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1207, 'status_kunjungan_fisio', 'Realisasi', 'Realisasi', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1208, 'status_kunjungan_fisio', 'DROP OUT', 'DROP OUT', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1209, 'status_kunjungan_fisio', 'Tidak Hadir', 'Tidak Hadir', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");
            
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_161847_migrate_skema_fisio_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_161847_migrate_skema_fisio_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
