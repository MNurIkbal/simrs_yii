<?php

use yii\db\Migration;

/**
 * Class m190618_082648_infotarifrs_v_update
 */
class m190618_082648_infotarifrs_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
    DROP VIEW infotarifrs_v;
        ');
        

        $this->execute('
  CREATE OR REPLACE VIEW infotarifrs_v AS 
 SELECT tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    r_tindakan.ruangan_nama,
    r_tindakan.instalasi_id,
    ins_tindakan.instalasi_nama,
    NULL::integer AS ruanganpaket_id,
    NULL::character varying AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    daftartindakan_m.is_akomodasi
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
     JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
     JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
  WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
UNION ALL
 SELECT tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    r_paket.ruangan_nama,
    r_paket.instalasi_id,
    ins_paket.instalasi_nama,
    paketruangan_mp.ruangan_id AS ruanganpaket_id,
    r_paket.ruangan_namalainnya AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama,
    NULL::integer AS kategoritindakan_id,
    NULL::character varying AS kategoritindakan_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    NULL::boolean AS is_akomodasi
   FROM tariftindakan_m
     JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
     JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
     JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
  WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true;

        ');

        $this->execute('
ALTER TABLE infotarifrs_v
  OWNER TO postgres;

        ');

    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190618_082648_infotarifrs_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190618_082648_infotarifrs_v_update cannot be reverted.\n";

        return false;
    }
    */
}
