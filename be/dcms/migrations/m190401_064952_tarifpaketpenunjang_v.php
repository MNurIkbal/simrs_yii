<?php

use yii\db\Migration;

/**
 * Class m190401_064952_tarifpaketpenunjang_v
 */
class m190401_064952_tarifpaketpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tarifpaketpenunjang_v AS 
             SELECT \'LAB\'::text AS jenis_paket,
                tariftindakan_m.tariftindakan_id,
                paketruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.hargadiskon_tindakan,
                perdatarif_m.perdanama_sk
               FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                 JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
              WHERE ruangan_m.instalasi_id = 4 AND perdatarif_m.is_active = true
            UNION ALL
             SELECT \'RAD\'::text AS jenis_paket,
                tariftindakan_m.tariftindakan_id,
                paketruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.hargadiskon_tindakan,
                perdatarif_m.perdanama_sk
               FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                 JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
              WHERE ruangan_m.instalasi_id = 5 AND perdatarif_m.is_active = true
            UNION ALL
             SELECT \'IBS\'::text AS jenis_paket,
                tariftindakan_m.tariftindakan_id,
                paketruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.hargadiskon_tindakan,
                perdatarif_m.perdanama_sk
               FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                 JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
              WHERE ruangan_m.instalasi_id = 12 AND perdatarif_m.is_active = true;
        ');

        $this->execute('
            ALTER TABLE tarifpaketpenunjang_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_064952_tarifpaketpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_064952_tarifpaketpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
