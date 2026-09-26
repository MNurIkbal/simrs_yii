<?php

use yii\db\Migration;

/**
 * Class m190401_064753_tariftindakanrad_v
 */
class m190401_064753_tariftindakanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tariftindakanrad_v AS 
             SELECT tariftindakan_m.tariftindakan_id,
                tindakanruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama,
                tariftindakan_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                pemeriksaanrad_m.jenispemeriksaanrad_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                tariftindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                pemeriksaanrad_m.pemeriksaanradiologi_id,
                pemeriksaanrad_m.pemeriksaanrad_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tindakanruangan_mp.is_default
               FROM tariftindakan_m
                 JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND pemeriksaanrad_m.is_active = true AND pemeriksaanrad_m.is_deleted = false
                 JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                 JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
              WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false AND pemeriksaanrad_m.is_deleted = false;
        ');

        $this->execute('
            ALTER TABLE tariftindakanrad_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_064753_tariftindakanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_064753_tariftindakanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
