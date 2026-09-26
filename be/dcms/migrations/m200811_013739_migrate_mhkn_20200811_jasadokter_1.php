<?php

use yii\db\Migration;

/**
 * Class m200811_013739_migrate_mhkn_20200811_jasadokter_1
 */
class m200811_013739_migrate_mhkn_20200811_jasadokter_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."jasadokter_m" (
  "jasadokter_id" serial8,
  "jasadokter_kode" varchar(100) COLLATE "pg_catalog"."default",
  "jasadokter_nama" varchar(255) COLLATE "pg_catalog"."default",
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
  CONSTRAINT "jasadokter_m_pkey" PRIMARY KEY ("jasadokter_id")
)
;');
        $this->execute('ALTER TABLE "public"."jasadokter_m" OWNER TO "postgres";');

        $this->execute('CREATE TABLE "public"."pelayananjasadokter_t" (
  "pelayananjasadokter_id" serial8,
  "tgl_transaksi" timestamp(6),
  "no_transaksi" varchar(255) COLLATE "pg_catalog"."default",
  "jasadokter_id" int4,
  "pegawai_id" int4,
  "total_jasa" float8 DEFAULT 0,
  "deskripsi" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "pelayananjasadokter_t_pkey" PRIMARY KEY ("pelayananjasadokter_id")
)
;');
        $this->execute('ALTER TABLE "public"."pelayananjasadokter_t" OWNER TO "postgres";');

        $this->execute('DELETE from penomoran_k WHERE penomoran_id=68;');

        $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) 
VALUES (68, 'pelayanan_jasadokter', 'JD', NULL, '0', '0');");

        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_pelayananjasadokter\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 68; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.no_transaksi = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "no_pelayananjasadokter" BEFORE INSERT ON "public"."pelayananjasadokter_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."generate_pelayananjasadokter"();');

   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_013739_migrate_mhkn_20200811_jasadokter_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_013739_migrate_mhkn_20200811_jasadokter_1 cannot be reverted.\n";

        return false;
    }
    */
}
