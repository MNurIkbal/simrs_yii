<?php

use yii\db\Migration;

/**
 * Class m190411_093221_infotarifpenunjang_v_update
 */
class m190411_093221_infotarifpenunjang_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW infotarifpenunjang_v;
        ');

        $this->execute("
   CREATE OR REPLACE VIEW infotarifpenunjang_v AS 
 SELECT 'LAB'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanlab_m.pemeriksaanlab_id,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false AND pemeriksaanlab_m.is_deleted = false AND perdatarif_m.is_active = true
UNION ALL
 SELECT 'RAD'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pemeriksaanrad_m.kelompokpemeriksaanrad_id AS kelompokpemeriksaanlab_id,
    kelompokpemeriksaanrad_m.nama_kelompok,
    pemeriksaanrad_m.jenispemeriksaanrad_id AS jenispemeriksaanlab_id,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id AS pemeriksaanlab_id,
    pemeriksaanrad_m.pemeriksaanrad_nama AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false AND pemeriksaanrad_m.is_deleted = false AND perdatarif_m.is_active = true
UNION ALL
 SELECT 'IBS'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    operasi_m.golonganoperasi_id AS kelompokpemeriksaanlab_id,
    golonganoperasi_m.golonganoperasi_nama AS nama_kelompok,
    operasi_m.kegiatanoperasi_id AS jenispemeriksaanlab_id,
    kegiatanoperasi_m.kegiatanoperasi_nama AS jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    operasi_m.operasi_id AS pemeriksaanlab_id,
    operasi_m.operasi_nama AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id
     JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
     JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false AND operasi_m.is_deleted = false AND perdatarif_m.is_active = true;


               ");
        
        $this->execute('
            ALTER TABLE infotarifpenunjang_v
            OWNER TO postgres;

        ');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_093221_infotarifpenunjang_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_093221_infotarifpenunjang_v_update cannot be reverted.\n";

        return false;
    }
    */
}
