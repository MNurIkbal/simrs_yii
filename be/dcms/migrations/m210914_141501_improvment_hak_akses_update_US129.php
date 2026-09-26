<?php

use yii\db\Migration;

/**
 * Class m210914_141501_improvment_hak_akses_update_US129
 */
class m210914_141501_improvment_hak_akses_update_US129 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."aksesform_k" (
              "aksesform_id" serial8 NOT NULL PRIMARY KEY,
              pasien_id int4 NOT NULL,
                akses json,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );    
        ');

        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (1063, 1064, 1065);
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1063, \'fitur_akses\', \'Asesmen Keperawatan\', \'asesmen_keperawatan\', NULL, NULL, NULL, \'2021-09-14 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1064, \'fitur_akses\', \'Asesmen Medis\', \'asesmen_medis\', NULL, NULL, NULL, \'2021-09-14 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1065, \'fitur_akses\', \'Resume Medis\', \'resume_medis\', NULL, NULL, NULL, \'2021-09-14 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL); 
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210914_141501_improvment_hak_akses_update_US129 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210914_141501_improvment_hak_akses_update_US129 cannot be reverted.\n";

        return false;
    }
    */
}
