<?php

use yii\db\Migration;

/**
 * Class m190401_064635_tariftindakan_v
 */
class m190401_064635_tariftindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tariftindakan_v AS 
             SELECT \'TINDAKAN\'::text AS jenis_tindakan_paket,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
                daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.carabayar_id,
                carabayar_m.carabayar_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.is_active,
                tariftindakan_m.komponentarif_id,
                daftartindakan_m.daftartindakan_kode,
                tindakanruangan_mp.ruangan_id
               FROM tariftindakan_m
                 JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
              WHERE tariftindakan_m.is_deleted = false AND daftartindakan_m.is_deleted = false AND tindakanruangan_mp.is_deleted = false
            UNION ALL
             SELECT \'PAKET\'::text AS jenis_tindakan_paket,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.tipepaket_id AS tindakan_paket_id,
                tipepaket_m.tipepaket_nama AS nama_tindakan_paket,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.carabayar_id,
                carabayar_m.carabayar_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.is_active,
                tariftindakan_m.komponentarif_id,
                NULL::character varying AS daftartindakan_kode,
                paketruangan_mp.ruangan_id
               FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN paketruangan_mp ON paketruangan_mp.tipepaket_id = tariftindakan_m.tipepaket_id
              WHERE tariftindakan_m.is_deleted = false AND paketruangan_mp.is_deleted = false;
        ');

        $this->execute('
            ALTER TABLE tariftindakan_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_064635_tariftindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_064635_tariftindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
