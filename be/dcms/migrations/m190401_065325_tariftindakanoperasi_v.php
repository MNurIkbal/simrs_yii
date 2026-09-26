<?php

use yii\db\Migration;

/**
 * Class m190401_065325_tariftindakanoperasi_v
 */
class m190401_065325_tariftindakanoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tariftindakanoperasi_v AS 
             SELECT tariftindakan_m.tariftindakan_id,
                tindakanruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama,
                tariftindakan_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                kegiatanoperasi_m.kegiatanoperasi_id,
                kegiatanoperasi_m.kegiatanoperasi_nama,
                tariftindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                operasi_m.operasi_id,
                operasi_m.operasi_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tindakanruangan_mp.is_default
               FROM tariftindakan_m
                 JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id
                 JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                 JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
              WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false;
        ');

        $this->execute('
            ALTER TABLE tariftindakanoperasi_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_065325_tariftindakanoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_065325_tariftindakanoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
