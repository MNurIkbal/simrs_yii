<?php

use yii\db\Migration;

/**
 * Class m230721_072649_migrate_GM87_view_rl3_8_laboratoriumdetail_v
 */
class m230721_072649_migrate_GM87_view_rl3_8_laboratoriumdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."rl3_8_laboratoriumdetail_v";
        '); 

        $this->execute('
            CREATE VIEW "public"."rl3_8_laboratoriumdetail_v" AS  SELECT lab.tindakanpelayanan_id,
    lab.tgl_tindakan,
    lab.ruangan_id,
    lab.jenispemeriksaanlab_id, 
    lab.kelompokpemeriksaanlab_id,
    lab.pemeriksaanlab_id,
    lab.qty_tindakan
   FROM ( SELECT \'NON_PAKET\'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            pasienmasukpenunjang_t.ruangan_id,
            ruangan_m.ruangan_nama,
            tindakanpelayanan_t.tipepaket_id,
            \'\'::character varying AS tipepaket_nama,
            pemeriksaanlab_m.pemeriksaanlab_id,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.jenispemeriksaanlab_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
            pemeriksaanlab_m.kelompokpemeriksaanlab_id,
            kelompokpemeriksaanlab_m.nama_kelompok,
            tindakanpelayanan_t.qty_tindakan
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
          WHERE pemeriksaanlab_m.is_deleted = false AND tindakanpelayanan_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT \'PAKET\'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            pasienmasukpenunjang_t.ruangan_id,
            ruangan_m.ruangan_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            pemeriksaanlab_m.pemeriksaanlab_id,
            paketpelayanan_mp.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.jenispemeriksaanlab_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
            pemeriksaanlab_m.kelompokpemeriksaanlab_id,
            kelompokpemeriksaanlab_m.nama_kelompok,
            tindakanpelayanan_t.qty_tindakan
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
          WHERE pemeriksaanlab_m.is_deleted = false AND tindakanpelayanan_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false) lab;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230721_072649_migrate_GM87_view_rl3_8_laboratoriumdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230721_072649_migrate_GM87_view_rl3_8_laboratoriumdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
