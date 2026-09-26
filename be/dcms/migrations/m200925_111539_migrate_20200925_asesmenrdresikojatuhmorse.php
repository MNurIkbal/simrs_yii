<?php

use yii\db\Migration;

/**
 * Class m200925_111539_migrate_20200925_asesmenrdresikojatuhmorse
 */
class m200925_111539_migrate_20200925_asesmenrdresikojatuhmorse extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS asesmenrdresikojatuhmorse_t;');

        $this->execute('DROP SEQUENCE IF EXISTS asesmenrdresikojatuhmorse_t_asesmenrdresikojatuhmorse_id_seq;');

        $this->execute('CREATE SEQUENCE "public"."asesmenrdresikojatuhmorse_t_asesmenrdresikojatuhmorse_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;');

        $this->execute('CREATE TABLE "public"."asesmenrdresikojatuhmorse_t" (
  "asesmenrdresikojatuhmorse_id" int4 NOT NULL DEFAULT nextval(\'asesmenrdresikojatuhmorse_t_asesmenrdresikojatuhmorse_id_seq\'::regclass),
  pendaftaran_id int4,
  asesmenperawatrd_id int4,
  resikojatuh_id int4,
  tanggal timestamp(6),
  jam time,
  riwayat_jatuh text,
  diagnosis_sekunder text,
  alat_bantu text,
  catheter text,
  kemampuan_berjalan text,
  status_mental text,
  total_skor float,
  kesimpulan text,
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
  CONSTRAINT "pk_asesmenrdresikojatuhmorse_t" PRIMARY KEY ("asesmenrdresikojatuhmorse_id")
)
;');
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_111539_migrate_20200925_asesmenrdresikojatuhmorse cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_111539_migrate_20200925_asesmenrdresikojatuhmorse cannot be reverted.\n";

        return false;
    }
    */
}
