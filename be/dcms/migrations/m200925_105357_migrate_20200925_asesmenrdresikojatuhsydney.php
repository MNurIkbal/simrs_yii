<?php

use yii\db\Migration;

/**
 * Class m200925_105357_migrate_20200925_asesmenrdresikojatuhsydney
 */
class m200925_105357_migrate_20200925_asesmenrdresikojatuhsydney extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS asesmenrdresikojatuhsydney_t;');

        $this->execute('DROP SEQUENCE IF EXISTS asesmenrdresikojatuhsydney_t_asesmenrdresikojatuhsydney_id_seq;');
        
        $this->execute('CREATE SEQUENCE "public"."asesmenrdresikojatuhsydney_t_asesmenrdresikojatuhsydney_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;');

        $this->execute('
CREATE TABLE "public"."asesmenrdresikojatuhsydney_t" (
  "asesmenrdresikojatuhsydney_id" int4 NOT NULL DEFAULT nextval(\'asesmenrdresikojatuhsydney_t_asesmenrdresikojatuhsydney_id_seq\'::regclass),
  pendaftaran_id int4,
  asesmenperawatrd_id int4,
  is_karena_jatuh text,
  karena_jatuh text,
  skor_karena_jatuh float,
  is_dua_bulan_terakhir text,
  dua_bulan_terakhir text,
  skor_dua_bulan_terakhir float,
  is_delirium text,
  delirium text,
  skor_delirium float,
  is_kacamata text,
  kacamata text,
  skor_kacamata float,
  is_buram text,
  buram text,
  skor_buram float,
  is_glaucoma text,
  glaucoma text,
  skor_glaucoma float,
  is_berkemih text,
  berkemih text,
  skor_berkemih float,
  is_mandiri text,
  mandiri text,
  skor_mandiri float,
  is_bantuan_sedikit text,
  bantuan_sedikit text,
  skor_bantuan_sedikit float,
  is_bantuan_nyata text,
  bantuan_nyata text,
  skor_bantuan_nyata float,
  is_bantuan_total text,
  bantuan_total text,
  skor_bantuan_total float,
  is_mobilitas_mandiri text,
  mobilitas_mandiri text,
  skor_mobilitas_mandiri float,
  is_mobilitas_bantuan text,
  mobilitas_bantuan text,
  skor_mobilitas_bantuan float,
  is_kursi_roda text,
  kursi_roda text,
  skor_kursi_roda float,
  is_imobilisasi text,
  imobilisasi text,
  skor_imobilisasi float,
  total_skor float,
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
  CONSTRAINT "pk_asesmenrdresikojatuhsydney_t" PRIMARY KEY ("asesmenrdresikojatuhsydney_id")
)
;');
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_105357_migrate_20200925_asesmenrdresikojatuhsydney cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_105357_migrate_20200925_asesmenrdresikojatuhsydney cannot be reverted.\n";

        return false;
    }
    */
}
