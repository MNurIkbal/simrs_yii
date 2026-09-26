<?php

use yii\db\Migration;

/**
 * Class m200925_102515_migrate_20200925_triase
 */
class m200925_102515_migrate_20200925_triase extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS triase_t;');

        $this->execute('DROP SEQUENCE IF EXISTS triase_t_triase_id_seq;');
        
        $this->execute('CREATE SEQUENCE "public"."triase_t_triase_id_seq" 
                        INCREMENT 1
                        MINVALUE  1
                        MAXVALUE 9223372036854775807
                        START 1
                        CACHE 1;');

        $this->execute('CREATE TABLE "public"."triase_t" (
                      "triase_id" int4 NOT NULL DEFAULT nextval(\'triase_t_triase_id_seq\'::regclass),
                      pendaftaran_id int4,
                      dokter_id int4,
                      perawat_id int4,
                      tgl_triase timestamp(6),
                      keluhan_utama text,
                      tekanan_darah VARCHAR(30),
                      nadi VARCHAR(30),
                      nafas VARCHAR(30),
                      suhu VARCHAR(30),
                      is_alergi bool,
                      alergi_obat text,
                      alergi_lainnya text,
                      is_trauma bool,
                      jalan_nafas text,
                      pernafasan text,
                      sirkulasi text,
                      gcseye_id int4,
                      gcsverbal_id int4,
                      gcsmotorik_id int4,
                      is_kapitis bool,
                      hasil_gcs text,
                      waktu_respon text,
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
                      CONSTRAINT "pk_triase_t" PRIMARY KEY ("triase_id")
                    )
                    ;');

      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_102515_migrate_20200925_triase cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_102515_migrate_20200925_triase cannot be reverted.\n";

        return false;
    }
    */
}
