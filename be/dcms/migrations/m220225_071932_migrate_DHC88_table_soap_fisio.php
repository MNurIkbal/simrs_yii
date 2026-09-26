<?php

use yii\db\Migration;

/**
 * Class m220317_071932_migrate_DHC88_table_soap_fisio
 */
class m220225_071932_migrate_DHC88_table_soap_fisio extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."soapfisioterapi_t" (
                "soapfisioterapi_id" serial8 NOT NULL PRIMARY KEY,
                "pendaftaran_id" int4 NOT NULL,
                "pasien_id" int4 NOT NULL,
                "terapis_id" int4 NOT NULL,
                "tgl_soapfisioterapi" timestamp(0) DEFAULT (\'now\'::text)::date,
                "subject" text COLLATE "pg_catalog"."default",
                "object" text COLLATE "pg_catalog"."default",
                "assesment" text COLLATE "pg_catalog"."default",
                "planning" text COLLATE "pg_catalog"."default",
                "a_diag_utama" json,
                "a_diag_penyerta" json,
                "catatan_dokter" text COLLATE "pg_catalog"."default",
                "instruksi" text COLLATE "pg_catalog"."default",
                "programterapi_id" int4,
                "pasienmasukpenunjang_id" int4,
                "tipe_instalasi" varchar(8) COLLATE "pg_catalog"."default",
                "is_edit" bool DEFAULT false,
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
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220225_071932_migrate_DHC88_table_soap_fisio cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220317_071932_migrate_DHC88_table_soap_fisio cannot be reverted.\n";

        return false;
    }
    */
}
