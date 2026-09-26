<?php

use yii\db\Migration;

/**
 * Class m190508_081401_persalinandetail_t_create
 */
class m190508_081401_persalinandetail_t_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF exists persalinandetail_t;');

         $this->execute('CREATE TABLE persalinandetail_t
(
  persalinandetail_id serial NOT NULL,
  persalinan_id integer,
  pendaftaran_id integer NOT NULL,
  jam_ke smallint NOT NULL,
  waktu timestamp(0) without time zone,
  td_systolic smallint,
  td_diastolic smallint,
  detak_nadi smallint,
  suhu real,
  tinggi_fundus real,
  kontraksi_uterus character varying(255),
  kandung_kemih character varying(255),
  darah_keluar smallint,
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
  CONSTRAINT persalinandetail_t_pkey PRIMARY KEY (persalinandetail_id)
)
WITH (
  OIDS=FALSE
)');

 $this->execute('ALTER TABLE persalinandetail_t OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190508_081401_persalinandetail_t_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190508_081401_persalinandetail_t_create cannot be reverted.\n";

        return false;
    }
    */
}
