<?php

use yii\db\Migration;

/**
 * Class m190529_024941_indikatorrs_r_create
 */
class m190529_024941_indikatorrs_r_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists indikatorrs_v;');

        $this->execute('DROP TABLE if exists indikatorrs_r;');

        $this->execute('CREATE TABLE indikatorrs_r
(
  indikatorrs_id integer NOT NULL,
  bulan character varying(10),
  tahun character varying(10),
  lama_dirawat integer DEFAULT 0,
  hari_perawatan integer DEFAULT 0,
  jumlah_tempat_tidur integer DEFAULT 0,
  jumlah_hari_periode integer DEFAULT 0,
  jumlah_pasien_keluar integer DEFAULT 0,
  pasien_mati_48_jam integer DEFAULT 0,
  pasien_mati_all integer DEFAULT 0,
  additional_data text,
  created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
  created_by integer,
  modified_count integer,
  last_modified_date timestamp(6) without time zone,
  last_modified_by integer,
  is_deleted boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  deleted_date timestamp(6) without time zone,
  deleted_by integer,
  CONSTRAINT indikatorrs_r_pkey PRIMARY KEY (indikatorrs_id)
)
WITH (
  OIDS=FALSE
);
');

        $this->execute('ALTER TABLE indikatorrs_r
  OWNER TO postgres;');

         $this->execute('GRANT ALL ON TABLE indikatorrs_r TO postgres;');

         $this->execute('GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE indikatorrs_r TO dev;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190529_024941_indikatorrs_r_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190529_024941_indikatorrs_r_create cannot be reverted.\n";

        return false;
    }
    */
}
