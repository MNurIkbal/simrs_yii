<?php

use yii\db\Migration;

/**
 * Class m201023_090450_migrate_2978_reminderpuasa
 */
class m201023_090450_migrate_2978_reminderpuasa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."instruksi_t" ADD COLUMN IF NOT EXISTS "is_puasa" bool DEFAULT FALSE;');  
        
        $this->execute('DROP TABLE IF EXISTS reminderpuasa_t;');
        $this->execute('DROP SEQUENCE IF EXISTS reminderpuasa_t_reminderpuasa_id_seq;');

        $this->execute('
            CREATE SEQUENCE "public"."reminderpuasa_t_reminderpuasa_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');
        
        $this->execute('
            CREATE TABLE "public"."reminderpuasa_t" (
                "reminderpuasa_id" int4 NOT NULL DEFAULT nextval(\'reminderpuasa_t_reminderpuasa_id_seq\'::regclass),
                pendaftaran_id int4,
                instruksi_id int4,
                tgl_awal_puasa timestamp(6),
                tgl_akhir_puasa timestamp(6),
                tgl_tindakan timestamp(6),
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "pk_reminderpuasa_t" PRIMARY KEY ("reminderpuasa_id")
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201023_090450_migrate_2978_reminderpuasa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_090450_migrate_2978_reminderpuasa cannot be reverted.\n";

        return false;
    }
    */
}
