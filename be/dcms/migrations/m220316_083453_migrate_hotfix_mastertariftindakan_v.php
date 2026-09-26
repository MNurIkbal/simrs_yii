<?php

use yii\db\Migration;

/**
 * Class m220316_083453_migrate_hotfix_mastertariftindakan_v
 */
class m220316_083453_migrate_hotfix_mastertariftindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DROP VIEW if exists public.mastertariftindakan_v;');

        $this->execute("
            CREATE VIEW \"public\".\"mastertariftindakan_v\" AS   SELECT 'TINDAKAN'::text AS jenis_tindakan_paket,
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
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama,
    tariftindakan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    komponentarif_m.komponentarif_kode,
    tariftindakan_m.persen_penyulit,
    tariftindakan_m.dokter_id,
    pegawai_m.nama_pegawai AS dokter,
    tariftindakan_m.ruangan_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     LEFT JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN pegawai_m ON tariftindakan_m.dokter_id = pegawai_m.pegawai_id
  WHERE tariftindakan_m.is_deleted = false
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
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
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    kelompoktindakan_m.kelompoktindakan_nama AS kelompok_nama,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    NULL::integer AS kamarruangan_id,
    NULL::character varying AS kamar,
    komponentarif_m.komponentarif_kode,
    tariftindakan_m.persen_penyulit,
    tariftindakan_m.dokter_id,
    pegawai_m.nama_pegawai AS dokter,
    tariftindakan_m.ruangan_id
   FROM tariftindakan_m
     LEFT JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN kelompoktindakan_m ON kelompoktindakan_m.kelompoktindakan_id = daftartindakan_m.kelompoktindakan_id
     JOIN ( SELECT tipepaket_m_1.tipepaket_id,
            tipepaket_m_1.tipepaket_nama
           FROM tipepaket_m tipepaket_m_1
          WHERE tipepaket_m_1.is_deleted = false) tipepaket_m ON tipepaket_m.tipepaket_id = tariftindakan_m.tipepaket_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = tariftindakan_m.ruangan_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     LEFT JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     LEFT JOIN pegawai_m ON tariftindakan_m.dokter_id = pegawai_m.pegawai_id
  WHERE tariftindakan_m.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220316_083453_migrate_hotfix_mastertariftindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220316_083453_migrate_hotfix_mastertariftindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
