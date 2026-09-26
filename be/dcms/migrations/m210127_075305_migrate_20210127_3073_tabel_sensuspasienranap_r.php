<?php

use yii\db\Migration;

/**
 * Class m210127_075305_migrate_20210127_3073_tabel_sensuspasienranap_r
 */
class m210127_075305_migrate_20210127_3073_tabel_sensuspasienranap_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."sensuspasienranap_r" (
            "id" serial8,
            "ruangan_id" int4,
            "kelaspelayanan_id" int4,
            "tgl_sensus" date,
            "pasien_awal" int4 DEFAULT 0,
            "pasien_masuk" int4 DEFAULT 0,
            "pasien_pindahan" int4 DEFAULT 0,
            "pasien_keluarhidup" int4 DEFAULT 0,
            "pasien_keluardipindahkan" int4 DEFAULT 0,
            "pasien_keluarmeninggalkur48" int4 DEFAULT 0,
            "pasien_keluarmeninggalleb48" int4 DEFAULT 0,
            "pasien_akhir" int4 DEFAULT 0,
            "additional_data" text COLLATE "pg_catalog"."default",
            "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
            "created_by" int4,
            "modified_count" int4,
            "last_modified_date" timestamp(6),
            "last_modified_by" int4,
            "is_deleted" bool DEFAULT false,
            "is_active" bool DEFAULT true,
            "deleted_date" timestamp(6),
            "deleted_by" int4
            )
            ;
        ');

        $this->execute('
            ALTER TABLE "public"."sensuspasienranap_r" 
            OWNER TO "postgres";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_075305_migrate_20210127_3073_tabel_sensuspasienranap_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_075305_migrate_20210127_3073_tabel_sensuspasienranap_r cannot be reverted.\n";

        return false;
    }
    */
}
