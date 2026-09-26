<?php

use yii\db\Migration;

/**
 * Class m210408_075132_improvment_update_view_detail_lab
 */
class m210408_075132_improvment_update_view_detail_lab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."permintaankepenunjang_t" ALTER COLUMN "is_approve" DROP NOT NULL;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infoorderanlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanlabdetail_v" AS  SELECT permintaankepenunjang_t.permintaankepenunjang_id,
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
                COALESCE(permintaankepenunjang_t.is_approve,FALSE) AS is_approve,
                permintaankepenunjang_t.tgl_approve,
                permintaankepenunjang_t.is_deleted,
                permintaankepenunjang_t.deleted_date
               FROM ((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pemeriksaanlab_m.is_deleted = false) AND (jenispemeriksaanlab_m.is_deleted = false) AND (daftartindakan_m.is_deleted = false))
            UNION ALL
             SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                concat(tipepaket_m.tipepaket_nama, \'-\', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
                tipepaket_m.tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan,
                paketpelayanan_mp.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                COALESCE(permintaankepenunjang_t.is_approve,FALSE) AS is_approve,
                permintaankepenunjang_t.tgl_approve,
                permintaankepenunjang_t.is_deleted,
                permintaankepenunjang_t.deleted_date
               FROM ((((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pemeriksaanlab_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienkirimkeunitlain_t.is_deleted = false) AND (jenispemeriksaanlab_m.is_deleted = false) AND (daftartindakan_m.is_deleted = false) AND (paketpelayanan_mp.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210408_075132_improvment_update_view_detail_lab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210408_075132_improvment_update_view_detail_lab cannot be reverted.\n";

        return false;
    }
    */
}
