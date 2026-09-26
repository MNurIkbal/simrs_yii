<?php

use yii\db\Migration;

/**
 * Class m190510_015238_kelahiranbayi_t_create
 */
class m190510_015238_kelahiranbayi_t_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute('DROP VIEW IF exists kelahiranbayi_v;');

        $this->execute('DROP TABLE IF exists  kelahiranbayi_t;');

         $this->execute('CREATE TABLE kelahiranbayi_t
(
  kelahiranbayi_id serial NOT NULL,
  pendaftaran_id integer NOT NULL, 
  pasienadmisi_id integer, 
  pendaftaranbaru_id integer,
  bayi_urut smallint,
  berat_badan real,
  tinggi_badan real,
  jenis_kelamin smallint, 
  penilaian smallint, 
  kondisi_bayi smallint, 
  normal_tindakan text, 
  asfiksia smallint, 
  asfiksia_tindakan text,
  keterangan_kondisi text,
  is_asi boolean,
  keterangan_asi character varying(255),
  masalah_lain text,
  hasil text,
  additional_data text,
  created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
  created_by integer,
  modified_count integer,
  last_modified_date timestamp(6) without time zone,
  last_modified_by integer,
  is_deleted boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  deleted_date timestamp(6) without time zone,
  deleted_by integer
)
WITH (
  OIDS=FALSE)');

 $this->execute('ALTER TABLE kelahiranbayi_t
  OWNER TO postgres;');

 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.pendaftaran_id IS \'pendaftaran_id ibu bayi\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.pasienadmisi_id IS \'pasienadmisi_id ibu bayi\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.pendaftaranbaru_id IS \'pendaftaran_id  BBL\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.jenis_kelamin IS \'lookup_type=\'\'jenis_kelamin\'\'\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.penilaian IS \'lookupkeperawatan=\'\'penilaian\'\'\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.kondisi_bayi IS \'lookupkeperawatan=\'\'kondisi_bayi\'\'\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.normal_tindakan IS \'lookupkeperawatan=\'\'bayinormal_tindakan\'\'\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.asfiksia IS \'lookupkeperawatan=\'\'asfiksia\'\'\';');
 $this->execute('COMMENT ON COLUMN kelahiranbayi_t.asfiksia_tindakan IS \'lookupkeperawatan=\'\'asfiksia_tindakan\'\'\';');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190510_015238_kelahiranbayi_t_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_015238_kelahiranbayi_t_create cannot be reverted.\n";

        return false;
    }
    */
}
