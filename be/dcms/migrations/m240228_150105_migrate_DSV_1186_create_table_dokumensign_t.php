<?php

use yii\db\Migration;

/**
 * Class m240228_150105_migrate_DSV_1186_create_table_dokumensign_t
 */
class m240228_150105_migrate_DSV_1186_create_table_dokumensign_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."dokumensign_t" (
                "dokumen_sign_id" serial8 NOT NULL,
                "pendaftaran_id" int4,
                "sign_provider_id" varchar(50),
                "konfig_dokumen_id" int4,
                "type" varchar(50) NOT NULL,
                "transaksi_id" int8,
                "filename" varchar(255) NOT NULL,
                "path" varchar(255) NOT NULL,
                "signed_date" date,
                "doc_status" varchar(50),
                "pegawai_id" int8,
                "additional_data" text,
                "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
                "created_by" int4 NOT NULL,

                "modified_count" int4 NULL,
                "last_modified_date" timestamp(6) NULL,
                "last_modified_by" int4 NULL,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6) NULL,
                "deleted_by" int4 NULL,
                CONSTRAINT "dokumensign_t_pkey" PRIMARY KEY ("dokumen_sign_id")
            )
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240228_150105_migrate_DSV_1186_create_table_dokumensign_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240228_150105_migrate_DSV_1186_create_table_dokumensign_t cannot be reverted.\n";

        return false;
    }
    */
}
