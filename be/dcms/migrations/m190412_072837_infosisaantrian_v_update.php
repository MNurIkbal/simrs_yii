<?php

use yii\db\Migration;

/**
 * Class m190412_072837_infosisaantrian_v_update
 */
class m190412_072837_infosisaantrian_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     DROP VIEW infosisaantrian_v;
        ');

        $this->execute("
 CREATE OR REPLACE VIEW infosisaantrian_v AS 
 SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.groupcarabayar_id,
    group_carabayar.lookup_name AS group_carabayar,
    antrian_t.klasifikasipasien_id,
    klasifikasipasien_m.klasifikasipasien_nama,
    antrian_t.status_antrian,
    antrian_t.jenisantrian_id,
    antrian_t.is_online,
        CASE
            WHEN antrian_t.status_antrian = 0 THEN 'Belum Dipanggil'::text
            WHEN antrian_t.status_antrian = 1 THEN 'Dipanggil'::text
            WHEN antrian_t.status_antrian = 2 THEN 'Dipilih'::text
            WHEN antrian_t.status_antrian = 3 THEN 'Lewati'::text
            WHEN antrian_t.status_antrian = 4 THEN 'Batal'::text
            ELSE ''::text
        END AS status_antrian_nama,
    konfigantrian_m.konfigantrian_id,
    antrian_t.loket_id,
    antrian_t.tgl_antrian,
    ruangan_m.instalasi_id
   FROM antrian_t
     JOIN lookup_m group_carabayar ON antrian_t.groupcarabayar_id = group_carabayar.lookup_id
     JOIN klasifikasipasien_m ON antrian_t.klasifikasipasien_id = klasifikasipasien_m.klasifikasipasien_id
     JOIN konfigantrian_m ON antrian_t.konfigantrian_id = konfigantrian_m.konfigantrian_id
     LEFT JOIN ruangan_m ON antrian_t.ruangan_id = ruangan_m.ruangan_id
  WHERE antrian_t.jenisantrian_id = 177 AND antrian_t.is_online = false;


               ");
        
        $this->execute('
        ALTER TABLE infosisaantrian_v
  OWNER TO postgres;

        ');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190412_072837_infosisaantrian_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190412_072837_infosisaantrian_v_update cannot be reverted.\n";

        return false;
    }
    */
}
