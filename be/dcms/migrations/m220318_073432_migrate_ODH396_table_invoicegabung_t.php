<?php

use yii\db\Migration;

/**
 * Class m220318_073432_migrate_ODH396_table_invoicegabung_t
 */
class m220318_073432_migrate_ODH396_table_invoicegabung_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."invoicegabung_t" (
                "invoicegabung_id" serial8 NOT NULL PRIMARY KEY,
                "tgl_invoicegabung" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
                "no_invoicegabung" varchar(255) COLLATE "pg_catalog"."default",
                "total_invoicegabung" float8 DEFAULT 0,
                "status_invoicegabung" int2,
                "tgl_invoicegabung_cetak" timestamp(6),
                "penjamin_id_cetak" int4,
                "pendaftaran_id_cetak" int4,
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
            ALTER TABLE invoicegabung_t ADD IF NOT EXISTS tgl_invoicegabung_cetak timestamp(6);
        ');

        $this->execute('
            ALTER TABLE invoicegabung_t ADD IF NOT EXISTS penjamin_id_cetak int4;
        ');

        $this->execute('
            ALTER TABLE invoicegabung_t ADD IF NOT EXISTS pendaftaran_id_cetak int4;
        ');

        $this->execute("
            COMMENT ON COLUMN public.invoicegabung_t.status_invoicegabung IS 'lookup_type=''status_invoicegabung''';    
        ");
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073432_migrate_ODH396_table_invoicegabung_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073432_migrate_ODH396_table_invoicegabung_t cannot be reverted.\n";

        return false;
    }
    */
}
