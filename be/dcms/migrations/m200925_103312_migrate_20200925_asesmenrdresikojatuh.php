<?php

use yii\db\Migration;

/**
 * Class m200925_103312_migrate_20200925_asesmenrdresikojatuh
 */
class m200925_103312_migrate_20200925_asesmenrdresikojatuh extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS asesmenrdresikojatuh_t;');

        $this->execute('DROP SEQUENCE IF EXISTS asesmenrdresikojatuh_t_asesmenrdresikojatuh_id_seq;');
        
        $this->execute('CREATE SEQUENCE "public"."asesmenrdresikojatuh_t_asesmenrdresikojatuh_id_seq" 
                        INCREMENT 1
                        MINVALUE  1
                        MAXVALUE 9223372036854775807
                        START 1
                        CACHE 1;
                        ');

        $this->execute('
            CREATE TABLE "public"."asesmenrdresikojatuh_t" (
            "asesmenrdresikojatuh_id" int4 NOT NULL DEFAULT nextval(\'asesmenrdresikojatuh_t_asesmenrdresikojatuh_id_seq\'::regclass),
            asesmenperawatrd_id int4,
            tgl_pengkajian timestamp(6),
            skala_nyeri text,
            lokasi_nyeri text,
            intesitas_nyeri text,
            durasi_nyeri text,
            frekuensi_nyeri text,
            karakteristik_nyeri text,
            lamanya_nyeri text,
            faktor_nyeri text,
            rencana_tindakan text,
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
            CONSTRAINT "pk_asesmenrdresikojatuh_t" PRIMARY KEY ("asesmenrdresikojatuh_id")
            )
            ;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_103312_migrate_20200925_asesmenrdresikojatuh cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_103312_migrate_20200925_asesmenrdresikojatuh cannot be reverted.\n";

        return false;
    }
    */
}
