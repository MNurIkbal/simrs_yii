<?php

use yii\db\Migration;

/**
 * Class m190725_070053_monitorsetdiagnosa_t
 */
class m190725_070053_monitorsetdiagnosa_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
          DROP SEQUENCE if exists public.monitorsetdiagnosa_t_seq;
        ');

          $this->execute('
          CREATE SEQUENCE public.monitorsetdiagnosa_t_seq
            INCREMENT 1
            MINVALUE 1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');

           $this->execute('
          ALTER TABLE public.monitorsetdiagnosa_t_seq
  OWNER TO postgres;
        ');

           $this->execute('
          DROP TABLE if exists public.monitorsetdiagnosa_t;
        ');

           $this->execute('
          CREATE TABLE public.monitorsetdiagnosa_t
(
  monitorsetdiagnosa_id integer NOT NULL DEFAULT nextval(\'monitorsetdiagnosa_t_seq\'::regclass),
  pendaftaran_id integer NOT NULL,
  pasienadmisi_id integer NOT NULL,
  diag_utama_id integer NOT NULL,
  diag_penyerta text, -- json format
  diag_tindakan text, -- json format
  hak_kelas smallint,
  kelaspelayanan_id integer,
  total double precision DEFAULT 0,
  tambahan_biaya double precision DEFAULT 0,
  persen_tambahan double precision DEFAULT 0,
  total_naikkelas double precision DEFAULT 0, -- naik kelas
  total_kelaspelayanan double precision DEFAULT 0, -- kelas saat ini
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
  CONSTRAINT monitorsetdiagnosa_t_pkey PRIMARY KEY (monitorsetdiagnosa_id)
)
WITH (
  OIDS=FALSE
);

        ');

           $this->execute('
          ALTER TABLE public.monitorsetdiagnosa_t
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_070053_monitorsetdiagnosa_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_070053_monitorsetdiagnosa_t cannot be reverted.\n";

        return false;
    }
    */
}
