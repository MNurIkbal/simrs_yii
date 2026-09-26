<?php

use yii\db\Migration;

/**
 * Class m220308_120225_migrate_oddo_pembayaranpiutang
 */
class m220308_120225_migrate_oddo_pembayaranpiutang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE if not exists "public"."pembayaranpiutang_r" (
  "id" serial8,
  "pembayaranpiutang_id" int8,
  "pemberianpiutang_id" int4,
  "tgl_pembayaranpiutang" date DEFAULT (\'now\'::text)::date,
  "no_pembayaranpiutang" varchar(100) COLLATE "pg_catalog"."default",
  "pendaftaran_id" int4,
  "pegawai_id" int4,
  "total_bayarpiutang" float8,
  "catatan" text COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool DEFAULT false,
  "is_active" bool DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "metode_pembayaran" int2,
  "jenisnontunai_id" int4,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "pembayaranpiutang_r_pkey" PRIMARY KEY ("id")
)
;
');
         $this->execute('ALTER TABLE "public"."pembayaranpiutang_r" 
  OWNER TO "postgres";');

         $this->execute('DROP TRIGGER if exists "pembayaranpiutang_r_insert" ON "public"."pembayaranpiutang_t";');
         
         $this->execute('DROP FUNCTION if exists public.pembayaranpiutang_r_insert;');

         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaranpiutang_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pembayaranpiutang_r<----------------------------------
        INSERT INTO pembayaranpiutang_r (        
                                pembayaranpiutang_id,
                                pemberianpiutang_id,
                                tgl_pembayaranpiutang,
                                no_pembayaranpiutang,
                                pendaftaran_id,
                                pegawai_id,
                                total_bayarpiutang,
                                catatan,
                                additional_data,
                                created_date,
                                created_by,
                                modified_count,
                                last_modified_date,
                                last_modified_by,
                                is_deleted,
                                is_active,
                                deleted_date,
                                deleted_by,
                                metode_pembayaran,
                                jenisnontunai_id,
                                keterangan_rekap
        )VALUES(
                                NEW.pembayaranpiutang_id,
                                NEW.pemberianpiutang_id,
                                NEW.tgl_pembayaranpiutang,
                                NEW.no_pembayaranpiutang,
                                NEW.pendaftaran_id,
                                NEW.pegawai_id,
                                NEW.total_bayarpiutang,
                                NEW.catatan,
                                NEW.additional_data,
                                NEW.created_date,
                                NEW.created_by,
                                NEW.modified_count,
                                NEW.last_modified_date,
                                NEW.last_modified_by,
                                NEW.is_deleted,
                                NEW.is_active,
                                NEW.deleted_date,
                                NEW.deleted_by,
                                NEW.metode_pembayaran,
                                NEW.jenisnontunai_id,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");

         $this->execute('CREATE TRIGGER "pembayaranpiutang_r_insert" AFTER INSERT ON "public"."pembayaranpiutang_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."pembayaranpiutang_r_insert"();');

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220308_120225_migrate_oddo_pembayaranpiutang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220308_120225_migrate_oddo_pembayaranpiutang cannot be reverted.\n";

        return false;
    }
    */
}
