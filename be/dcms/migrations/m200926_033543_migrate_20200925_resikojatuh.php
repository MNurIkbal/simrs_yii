<?php

use yii\db\Migration;

/**
 * Class m200926_033543_migrate_20200925_resikojatuh
 */
class m200926_033543_migrate_20200925_resikojatuh extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS resikojatuh_m;');

        $this->execute('DROP SEQUENCE IF EXISTS resikojatuh_m_resikojatuh_id_seq;');

        $this->execute('CREATE SEQUENCE "public"."resikojatuh_m_resikojatuh_id_seq" 
                        INCREMENT 1
                        MINVALUE  1
                        MAXVALUE 9223372036854775807
                        START 1
                        CACHE 1;');

        $this->execute('CREATE TABLE "public"."resikojatuh_m" (
  "resikojatuh_id" int4 NOT NULL DEFAULT nextval(\'resikojatuh_m_resikojatuh_id_seq\'::regclass),
  resikojatuh_nama VARCHAR(100),
  keterangan text,
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
  CONSTRAINT "pk_resikojatuh_t" PRIMARY KEY ("resikojatuh_id")
)
;');

        $this->execute('TRUNCATE TABLE resikojatuh_m RESTART IDENTITY;');

        $this->execute("INSERT INTO resikojatuh_m(resikojatuh_id, resikojatuh_nama, keterangan, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1, 'Riwayat pernah jatuh', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(2, 'Gangguan status psikologi', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(3, 'Pasien pasca bedah', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(4, 'Anak-anak dan bayi', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(5, 'Gangguan mobilisasi/keseimbangan', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(6, 'Mendapat obat-obat sedative', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(7, 'Ketergantungan alkohol, napza', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(8, 'Tekanan darah yang tidak stabil', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(9, 'Ibu Hamil', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(10, 'Vertigo/dizzines', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(11, 'Usia > 65 tahun', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200926_033543_migrate_20200925_resikojatuh cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200926_033543_migrate_20200925_resikojatuh cannot be reverted.\n";

        return false;
    }
    */
}
