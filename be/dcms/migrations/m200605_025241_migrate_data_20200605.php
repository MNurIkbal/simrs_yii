<?php

use yii\db\Migration;

/**
 * Class m200605_025241_migrate_data_20200605
 */
class m200605_025241_migrate_data_20200605 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Dokter Operator', lookup_value = '100', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Dokter Bedah', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 508;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Dokter Anastesi', lookup_value = '40', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Asisten Bedah 1', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 509;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Dokter Anak', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Asisten Bedah 2', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 510;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Asisten Anastesi 1', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Dokter Anastesi', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 511;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Asisten Anastesi 2', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Asisten Anastesi', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 512;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Asisten Bedah 1', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Perawat Instrumen 1', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 513;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Asisten Bedah 2', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Perawat Instrumen 2', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 514;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Perawat Instrumen', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Perawat Sirkuler 1', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 515;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Perawat Sirkuler', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Perawat Sirkuler 2', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 516;");
                $this->execute("UPDATE lookup_m SET lookup_type = 'tim_operasi', lookup_name = 'Perawat Anastesi', lookup_value = '20', lookup_urutan = NULL, lookup_kode = NULL, additional_data = 'Perawat Anastesi', created_date = '2020-01-30 00:00:00', created_by = NULL, modified_count = NULL, last_modified_date = NULL, last_modified_by = NULL, is_deleted = 'f', is_active = 't', deleted_date = NULL, deleted_by = NULL WHERE lookup_id = 517;");
                

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200605_025241_migrate_data_20200605 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200605_025241_migrate_data_20200605 cannot be reverted.\n";

        return false;
    }
    */
}
