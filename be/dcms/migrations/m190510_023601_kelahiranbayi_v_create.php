<?php

use yii\db\Migration;

/**
 * Class m190510_023601_kelahiranbayi_v_create
 */
class m190510_023601_kelahiranbayi_v_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
     {
        $this->execute('
      DROP VIEW IF exists kelahiranbayi_v;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW kelahiranbayi_v AS 
 SELECT kelahiranbayi_t.kelahiranbayi_id,
    kelahiranbayi_t.pendaftaran_id,
    kelahiranbayi_t.pasienadmisi_id,
    kelahiranbayi_t.pendaftaranbaru_id,
    pendaftaran_t.no_pendaftaran,
        CASE
            WHEN kelahiranbayi_t.pendaftaranbaru_id IS NULL THEN \'BELUM TERDAFTAR\'::text
            ELSE \'SUDAH TERDAFTAR\'::text
        END AS status_pendaftaran,
    kelahiranbayi_t.bayi_urut,
    kelahiranbayi_t.berat_badan,
    kelahiranbayi_t.tinggi_badan,
    fgetnamalookup(kelahiranbayi_t.jenis_kelamin::integer) AS jenis_kelamin,
    fgetnamalookupkeperawatan(kelahiranbayi_t.penilaian::integer) AS penilaian,
    fgetnamalookupkeperawatan(kelahiranbayi_t.kondisi_bayi::integer) AS kondisi_bayi,
    fgetnamalookupkeperawatan(kelahiranbayi_t.asfiksia::integer) AS normal_tindakan,
    kelahiranbayi_t.is_asi,
    kelahiranbayi_t.keterangan_asi,
    kelahiranbayi_t.masalah_lain,
    kelahiranbayi_t.hasil
   FROM kelahiranbayi_t
     LEFT JOIN pendaftaran_t ON kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id;



        ');

        $this->execute('
 ALTER TABLE kelahiranbayi_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190510_023601_kelahiranbayi_v_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_023601_kelahiranbayi_v_create cannot be reverted.\n";

        return false;
    }
    */
}
