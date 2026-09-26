<?php

use yii\db\Migration;

/**
 * Class m190703_040228_suratketlahir_t_create
 */
class m190703_040228_suratketlahir_t_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
   DROP VIEW IF exists suratketlahir_v;
        ');

          $this->execute('
   DROP TABLE IF exists public.suratketlahir_t;
        ');

          $this->execute('
   DROP SEQUENCE IF exists public.suratketlahir_t_suratketlahir_id_seq;
        ');

            $this->execute('
   CREATE SEQUENCE public.suratketlahir_t_suratketlahir_id_seq
        INCREMENT 1
        MINVALUE 1
        MAXVALUE 9223372036854775807
        START 1
  CACHE 1;
        ');

              $this->execute('
   ALTER TABLE public.suratketlahir_t_suratketlahir_id_seq
  OWNER TO postgres;
        ');

            

              $this->execute('
   CREATE TABLE public.suratketlahir_t
(
  suratketlahir_id integer NOT NULL DEFAULT nextval(\'suratketlahir_t_suratketlahir_id_seq\'::regclass),
  pendaftaran_id integer,
  pasienadmisi_id integer,
  pasien_id integer,
  dokterdpjp_id integer,
  ibu_nama character varying(100),
  ibu_ktp character varying(100),
  ibu_alamat text,
  ibu_pekerjaan character varying(100),
  ibu_golongandarah character varying(10),
  ayah_nama character varying(100),
  ayah_ktp character varying(100),
  ayah_alamat text,
  ayah_pekerjaan_id integer,
  ayah_golongandarah_id integer,
  hari_lahir integer,
  tgl_lahir date,
  jam_lahir time(0) without time zone,
  bb_lahir real,
  panjang_lahir real,
  kelahiran character varying(255),
  golongan_darah integer,
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
  anakke smallint,
  CONSTRAINT suratketlahir_t_pkey PRIMARY KEY (suratketlahir_id)
)
WITH (
  OIDS=FALSE
);
        ');


        $this->execute('
   ALTER TABLE public.suratketlahir_t
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190703_040228_suratketlahir_t_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_040228_suratketlahir_t_create cannot be reverted.\n";

        return false;
    }
    */
}
