<?php

use yii\db\Migration;

/**
 * Class m190805_032807_monitorbpjspersen_r
 */
class m190805_032807_monitorbpjspersen_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP SEQUENCE if exists public.monitorbpjspersen_seq;
        ');

          $this->execute('
         CREATE SEQUENCE public.monitorbpjspersen_seq
            INCREMENT 1
            MINVALUE 1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
         ');

          $this->execute('
        ALTER TABLE public.monitorbpjspersen_seq
              OWNER TO postgres;
        ');

           $this->execute('
         DROP TABLE if exists public.monitorbpjspersen_r;
        ');

            $this->execute('
        CREATE TABLE public.monitorbpjspersen_r
            (
              monitorbpjspersen_id integer NOT NULL DEFAULT nextval(\'monitorbpjspersen_seq\'::regclass),
              pendaftaran_id integer NOT NULL,
              pasienadmisi_id integer NOT NULL,
              monitorbpjs_id integer,
              persen real,
              total_persen double precision,
              persen_kelompok real,
              total_persenkelompok double precision,
              CONSTRAINT monitorbpjspersen_r_pkey PRIMARY KEY (monitorbpjspersen_id)
            )
            WITH (
              OIDS=FALSE
            );
        ');

             $this->execute('
         ALTER TABLE public.monitorbpjspersen_r
        OWNER TO postgres;
        ');

             
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190805_032807_monitorbpjspersen_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_032807_monitorbpjspersen_r cannot be reverted.\n";

        return false;
    }
    */
}
