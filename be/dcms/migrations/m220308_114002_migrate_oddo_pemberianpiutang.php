<?php

use yii\db\Migration;

/**
 * Class m220308_114002_migrate_oddo_pemberianpiutang
 */
class m220308_114002_migrate_oddo_pemberianpiutang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE if not exists "public"."pemberianpiutang_r" (
  "id" serial8,
  "pemberianpiutang_id" int8,
  "tgl_pemberianpiutang" date DEFAULT (\'now\'::text)::date,
  "no_pemberianpiutang" varchar(100) COLLATE "pg_catalog"."default",
  "pendaftaran_id" int4,
  "pegawai_id" int4,
  "total_piutang" float8 DEFAULT 0,
  "total_sisapiutang" float8 DEFAULT 0,
  "total_bayarpiutang" float8 DEFAULT 0,
  "catatan" text COLLATE "pg_catalog"."default",
  "status_piutang" int2 DEFAULT 349,
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
  "penjualanresep_id" int4,
  "pegawaimengetahui_id" int4,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "pemberianpiutang_r_pkey" PRIMARY KEY ("id")
)
;
');

         $this->execute('ALTER TABLE "public"."pemberianpiutang_r" 
  OWNER TO "postgres";');

        $this->execute('DROP TRIGGER if exists "pemberianpiutang_r_insert" ON "public"."pemberianpiutang_t";');

        $this->execute('DROP FUNCTION if exists public.pemberianpiutang_r_insert;');
         
         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pemberianpiutang_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pemberianpiutang_r<----------------------------------
        INSERT INTO pemberianpiutang_r (        
                                pemberianpiutang_id,
                                tgl_pemberianpiutang,
                                no_pemberianpiutang,
                                pendaftaran_id,
                                pegawai_id,
                                total_piutang,
                                total_sisapiutang,
                                total_bayarpiutang,
                                catatan,
                                status_piutang,
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
                                penjualanresep_id,
                                pegawaimengetahui_id,
                                keterangan_rekap
        )VALUES(
                                NEW.pemberianpiutang_id,
                                NEW.tgl_pemberianpiutang,
                                NEW.no_pemberianpiutang,
                                NEW.pendaftaran_id,
                                NEW.pegawai_id,
                                NEW.total_piutang,
                                NEW.total_sisapiutang,
                                NEW.total_bayarpiutang,
                                NEW.catatan,
                                NEW.status_piutang,
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
                                NEW.penjualanresep_id,
                                NEW.pegawaimengetahui_id,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");
         $this->execute('CREATE TRIGGER "pemberianpiutang_r_insert" AFTER INSERT ON "public"."pemberianpiutang_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."pemberianpiutang_r_insert"();');

         $this->execute('COMMENT ON TRIGGER "pemberianpiutang_r_insert" ON "public"."pemberianpiutang_t" IS \'insert ke pemberianpiutang_r\';');

         $this->execute('DROP TRIGGER if exists "pemberianpiutang_r_cancel" ON "public"."pemberianpiutang_t";');

         $this->execute('DROP FUNCTION if exists public.pemberianpiutang_r_cancel;');

         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pemberianpiutang_r_cancel\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pemberianpiutang_r<----------------------------------
        INSERT INTO pemberianpiutang_r (        
                                pemberianpiutang_id,
                                tgl_pemberianpiutang,
                                no_pemberianpiutang,
                                pendaftaran_id,
                                pegawai_id,
                                total_piutang,
                                total_sisapiutang,
                                total_bayarpiutang,
                                catatan,
                                status_piutang,
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
                                penjualanresep_id,
                                pegawaimengetahui_id,
                                keterangan_rekap
        )VALUES(
                                NEW.pemberianpiutang_id,
                                NEW.tgl_pemberianpiutang,
                                NEW.no_pemberianpiutang,
                                NEW.pendaftaran_id,
                                NEW.pegawai_id,
                                NEW.total_piutang,
                                NEW.total_sisapiutang,
                                NEW.total_bayarpiutang,
                                NEW.catatan,
                                NEW.status_piutang,
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
                                NEW.penjualanresep_id,
                                NEW.pegawaimengetahui_id,
                'CANCEL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");

         $this->execute('CREATE TRIGGER "pemberianpiutang_r_cancel" AFTER UPDATE OF "is_deleted" ON "public"."pemberianpiutang_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."pemberianpiutang_r_cancel"();');

         $this->execute('COMMENT ON TRIGGER "pemberianpiutang_r_cancel" ON "public"."pemberianpiutang_t" IS \'insert ke pemberianpiutang_r (cancel)\';');

        
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220308_114002_migrate_oddo_pemberianpiutang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220308_114002_migrate_oddo_pemberianpiutang cannot be reverted.\n";

        return false;
    }
    */
}
