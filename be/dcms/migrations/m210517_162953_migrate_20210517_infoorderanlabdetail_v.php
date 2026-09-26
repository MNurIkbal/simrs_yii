<?php

use yii\db\Migration;

/**
 * Class m210517_162953_migrate_20210517_infoorderanlabdetail_v
 */
class m210517_162953_migrate_20210517_infoorderanlabdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
   $this->execute('DROP VIEW if exists "public"."infoorderanlabdetail_v";');

   $this->execute("
    CREATE VIEW \"public\".\"infoorderanlabdetail_v\" AS  SELECT permintaankepenunjang_t.permintaankepenunjang_id,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    daftartindakan_m.daftartindakan_nama,
    NULL::character varying AS tipepaket_nama,
    permintaankepenunjang_t.qtypermintaan,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tarif_pelayanan,
    permintaankepenunjang_t.daftartindakan_id,
    permintaankepenunjang_t.tipepaket_id,
    permintaankepenunjang_t.tarif_cytotindakan,
    permintaankepenunjang_t.satuan_tindakan,
    permintaankepenunjang_t.is_approve,
    permintaankepenunjang_t.tgl_approve,
    permintaankepenunjang_t.is_deleted,
    permintaankepenunjang_t.deleted_date
   FROM pasienkirimkeunitlain_t
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pemeriksaanlab_m.is_deleted = false AND jenispemeriksaanlab_m.is_deleted = false AND daftartindakan_m.is_deleted = false
UNION ALL
 SELECT permintaankepenunjang_t.permintaankepenunjang_id,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    concat(tipepaket_m.tipepaket_nama, '-', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
    tipepaket_m.tipepaket_nama,
    permintaankepenunjang_t.qtypermintaan,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tarif_pelayanan,
    paketpelayanan_mp.daftartindakan_id,
    permintaankepenunjang_t.tipepaket_id,
    permintaankepenunjang_t.tarif_cytotindakan,
    permintaankepenunjang_t.satuan_tindakan,
    permintaankepenunjang_t.is_approve,
    permintaankepenunjang_t.tgl_approve,
    permintaankepenunjang_t.is_deleted,
    permintaankepenunjang_t.deleted_date
   FROM pasienkirimkeunitlain_t
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienkirimkeunitlain_t.is_deleted = false AND jenispemeriksaanlab_m.is_deleted = false AND daftartindakan_m.is_deleted = false AND paketpelayanan_mp.is_deleted = false AND pemeriksaanlab_m.is_deleted = false;");
   
   $this->execute('ALTER TABLE "public"."infoorderanlabdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210517_162953_migrate_20210517_infoorderanlabdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210517_162953_migrate_20210517_infoorderanlabdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
