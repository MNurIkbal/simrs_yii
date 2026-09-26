<?php

use yii\db\Migration;

/**
 * Class m190520_081055_tarifpaketpenunjang_v_update
 */
class m190520_081055_tarifpaketpenunjang_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     DROP VIEW tarifpaketpenunjang_v;
        ');

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
    paket.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.hargadiskon_tindakan,
    perdatarif_m.perdanama_sk
   FROM tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE paketpelayanan_mp_1.is_deleted IS FALSE
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                     JOIN pemeriksaanlab_m pemeriksaanlab_m_1 ON paketpelayanan_mp_1.daftartindakan_id = pemeriksaanlab_m_1.daftartindakan_id AND pemeriksaanlab_m_1.is_deleted = false
                  WHERE paketpelayanan_mp_1.is_deleted = false
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) pemeriksaanlab_m ON paketpelayanan_mp.tipepaket_id = pemeriksaanlab_m.tipepaket_id AND paketpelayanan_mp.jumlah = pemeriksaanlab_m.jumlah
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON tariftindakan_m.tipepaket_id = paket.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = false
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
  WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
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
    paket.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.hargadiskon_tindakan,
    perdatarif_m.perdanama_sk
   FROM tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE paketpelayanan_mp_1.is_deleted IS FALSE
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                     JOIN pemeriksaanrad_m pemeriksaanrad_m_1 ON paketpelayanan_mp_1.daftartindakan_id = pemeriksaanrad_m_1.daftartindakan_id AND pemeriksaanrad_m_1.is_deleted = false
                  WHERE paketpelayanan_mp_1.is_deleted = false
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) pemeriksaanrad_m ON paketpelayanan_mp.tipepaket_id = pemeriksaanrad_m.tipepaket_id AND paketpelayanan_mp.jumlah = pemeriksaanrad_m.jumlah
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON tariftindakan_m.tipepaket_id = paket.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = false
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
  WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
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
    paket.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.hargadiskon_tindakan,
    perdatarif_m.perdanama_sk
   FROM tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE paketpelayanan_mp_1.is_deleted IS FALSE
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                     JOIN operasi_m operasi_m_1 ON paketpelayanan_mp_1.daftartindakan_id = operasi_m_1.daftartindakan_id AND operasi_m_1.is_deleted = false
                  WHERE paketpelayanan_mp_1.is_deleted = false
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) operasi_m ON paketpelayanan_mp.tipepaket_id = operasi_m.tipepaket_id AND paketpelayanan_mp.jumlah = operasi_m.jumlah
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON tariftindakan_m.tipepaket_id = paket.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = false
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 12
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
  WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true;


        ');

        $this->execute('
ALTER TABLE tarifpaketpenunjang_v
  OWNER TO postgres;
        ');

        $this->execute('
GRANT ALL ON TABLE tarifpaketpenunjang_v TO postgres;
        ');

        $this->execute('
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE tarifpaketpenunjang_v TO dev;
        ');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190520_081055_tarifpaketpenunjang_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_081055_tarifpaketpenunjang_v_update cannot be reverted.\n";

        return false;
    }
    */
}
