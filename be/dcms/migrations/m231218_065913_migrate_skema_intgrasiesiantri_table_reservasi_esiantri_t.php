<?php

use yii\db\Migration;

/**
 * Class m231218_065913_migrate_skema_intgrasiesiantri_table_reservasi_esiantri_t
 */
class m231218_065913_migrate_skema_intgrasiesiantri_table_reservasi_esiantri_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
		CREATE TABLE if not EXISTS "public"."reservasi_esiantri_t" (
		  "reservasi_esiantri_id" serial4 NOT NULL ,
		  "id" int4 NOT NULL,
		  "no_mr" varchar COLLATE "pg_catalog"."default",
		  "nama_pasien" varchar COLLATE "pg_catalog"."default" NOT NULL,
		  "no_identitas" varchar COLLATE "pg_catalog"."default" NOT NULL,
		  "jenis_kelamin" varchar COLLATE "pg_catalog"."default",
		  "tgl_lahir" date,
		  "alamat" text COLLATE "pg_catalog"."default",
		  "no_hp" varchar(50) COLLATE "pg_catalog"."default",
		  "tgl_kunjungan" timestamp(6),
		  "cara_bayar" varchar COLLATE "pg_catalog"."default",
		  "no_rujukan" varchar COLLATE "pg_catalog"."default",
		  "no_bpjs" varchar COLLATE "pg_catalog"."default",
		  "kode_booking" varchar COLLATE "pg_catalog"."default",
		  "nomor_antrian" varchar(6) COLLATE "pg_catalog"."default",
		  "jenis_pasien" varchar(10) COLLATE "pg_catalog"."default",
		  "loket" varchar(2) COLLATE "pg_catalog"."default",
		  "kodepoli" int4,
		  "additional_data" text COLLATE "pg_catalog"."default",
		  "created_date" timestamp(6),
		  "created_by" int4,
		  "modified_count" int4,
		  "last_modified_date" timestamp(6),
		  "last_modified_by" int4,
		  "deleted_date" timestamp(6),
		  "deleted_by" int4,
		  "is_active" bool DEFAULT true,
		  "is_deleted" bool DEFAULT false,
		  "pendaftaranol_id" int4,
		  CONSTRAINT "reservasi_esiantri_id_pk" PRIMARY KEY ("reservasi_esiantri_id")
		)
		;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231218_065913_migrate_skema_intgrasiesiantri_table_reservasi_esiantri_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231218_065913_migrate_skema_intgrasiesiantri_table_reservasi_esiantri_t cannot be reverted.\n";

        return false;
    }
    */
}
