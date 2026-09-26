<?php

use yii\db\Migration;

/**
 * Class m220714_085048_migrate_MHG2695_data_lookup_m
 */
class m220714_085048_migrate_MHG2695_data_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            UPDATE "public"."lookup_m" SET "lookup_type" = \'jenis_skrining_nrs\', "lookup_name" = \'Skrining Lanjut 1\', "lookup_value" = \'Dewasa\', "lookup_urutan" = NULL, "lookup_kode" = NULL, "additional_data" = NULL, "created_date" = \'2022-06-10 00:00:00\', "created_by" = NULL, "modified_count" = NULL, "last_modified_date" = NULL, "last_modified_by" = NULL, "is_deleted" = \'f\', "is_active" = \'t\', "deleted_date" = NULL, "deleted_by" = NULL WHERE "lookup_id" = 1211;
        ');

        $this->execute('
            UPDATE "public"."lookup_m" SET "lookup_type" = \'jenis_skrining_nrs\', "lookup_name" = \'Skrining Lanjut 2\', "lookup_value" = \'Dewasa\', "lookup_urutan" = NULL, "lookup_kode" = NULL, "additional_data" = NULL, "created_date" = \'2022-06-10 00:00:00\', "created_by" = NULL, "modified_count" = NULL, "last_modified_date" = NULL, "last_modified_by" = NULL, "is_deleted" = \'f\', "is_active" = \'t\', "deleted_date" = NULL, "deleted_by" = NULL WHERE "lookup_id" = 1212;
        ');

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1233,
                1234,
                1235,
                1236
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1233, \'jenis_skrining_nrs\', \'Nafsu Makan\', \'Anak\', 1, NULL, NULL, \'2022-06-27 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1234, \'jenis_skrining_nrs\', \'Kemampuan Untuk Makan\', \'Anak\', 2, NULL, NULL, \'2022-06-27 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1235, \'jenis_skrining_nrs\', \'Faktor Stress\', \'Anak\', 3, NULL, NULL, \'2022-06-27 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1236, \'jenis_skrining_nrs\', \'Persentil Berat Badan\', \'Anak\', 4, NULL, NULL, \'2022-06-27 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220714_085048_migrate_MHG2695_data_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220714_085048_migrate_MHG2695_data_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
