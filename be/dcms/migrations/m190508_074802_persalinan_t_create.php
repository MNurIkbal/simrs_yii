<?php

use yii\db\Migration;

/**
 * Class m190508_074802_persalinan_t_create
 */
class m190508_074802_persalinan_t_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF exists  persalinan_t;');

         $this->execute('CREATE TABLE persalinan_t
(
  persalinan_id serial NOT NULL,
  pendaftaran_id integer NOT NULL,
  pasienadmisi_id integer,
  tgl_persalinan timestamp(0) without time zone,
  penolong character varying(255),
  tempat_persalinan character varying(255),
  rujuk_kala smallint, 
  alasan_merujuk character varying(255),
  tempat_rujukan character varying(255),
  pendamping smallint, 
  masalah_persalinan smallint, 
  k1_gariswaspada boolean,
  k1_masalah text,
  k1_pelaksanaanmasalah text,
  k1_hasil text,
  k2_episitomi boolean,
  k2_indikasi character varying(255),
  k2_pendamping smallint, 
  k2_gawatjanin boolean,
  k2_tindakanjanin text,
  k2_hasil character varying(255),
  k2_distosiabahu boolean,
  k2_tindakandistosia text,
  k2_masalah text,
  k3 text, 
  k4_keadaanumum text,
  k4_td_systolic smallint,
  k4_td_diastolic smallint,
  k4_detaknadi smallint,
  k4_pernapasan integer,
  k4_masalah text,
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
  CONSTRAINT persalinan1_t_pkey PRIMARY KEY (persalinan_id)
)
WITH (
  OIDS=FALSE)');

 $this->execute('ALTER TABLE persalinan_t OWNER TO postgres;');

 $this->execute('COMMENT ON COLUMN persalinan_t.rujuk_kala IS \'lookupkeperawatan.lookup_type=\'\'rujuk_kala\'\'\';');
 $this->execute('COMMENT ON COLUMN persalinan_t.pendamping IS \'lookupkeperawatan.lookup_type=\'\'pendamping\'\'\';');
 $this->execute('COMMENT ON COLUMN persalinan_t.masalah_persalinan IS \'lookupkeperawatan.lookup_type=\'\'masalah_persalinan\'\'\';');
 $this->execute('COMMENT ON COLUMN persalinan_t.k2_pendamping IS \'lookupkeperawatan.lookup_type=\'\'pendamping\'\'\';');
 $this->execute('COMMENT ON COLUMN persalinan_t.k3 IS \'JSON Format *lookupkeperawatan.lookup_type=\'\'laserisasi\'\'\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190508_074802_persalinan_t_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190508_074802_persalinan_t_create cannot be reverted.\n";

        return false;
    }
    */
}
