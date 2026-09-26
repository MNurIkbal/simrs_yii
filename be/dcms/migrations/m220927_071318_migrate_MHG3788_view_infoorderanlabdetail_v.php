<?php

use yii\db\Migration;

/**
 * Class m220927_071318_migrate_MHG3788_view_infoorderanlabdetail_v
 */
class m220927_071318_migrate_MHG3788_view_infoorderanlabdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoorderanlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanlabdetail_v" AS  
            SELECT permintaankepenunjang_t.permintaankepenunjang_id,
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
                permintaankepenunjang_t.deleted_date,
                permintaankepenunjang_t.is_referred,
                permintaankepenunjang_t.is_exception
               FROM pasienkirimkeunitlain_t
                 JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.permintaankepenunjang_id,
                        a.qtypermintaan,
                        a.is_cyto,
                        a.tarif_pelayanan,
                        a.daftartindakan_id,
                        a.tipepaket_id,
                        a.tarif_cytotindakan,
                        a.satuan_tindakan,
                        a.is_approve,
                        a.tgl_approve,
                        a.is_deleted,
                        a.deleted_date,
                        a.is_referred,
                        a.pemeriksaanlab_id,
                        a.is_exception
                       FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.pemeriksaanlab_id,
                        a.pemeriksaanlab_nama,
                        a.daftartindakan_id,
                        a.jenispemeriksaanlab_id,
                        a.is_deleted
                       FROM pemeriksaanlab_m a) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                 JOIN ( SELECT a.jenispemeriksaanlab_id,
                        a.jenispemeriksaanlab_nama,
                        a.is_deleted
                       FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                 JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                        daftartindakan_m_1.daftartindakan_nama,
                        daftartindakan_m_1.is_deleted
                       FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
              WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pemeriksaanlab_m.is_deleted = false AND jenispemeriksaanlab_m.is_deleted = false AND daftartindakan_m.is_deleted = false
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
                permintaankepenunjang_t.is_approve,
                permintaankepenunjang_t.tgl_approve,
                permintaankepenunjang_t.is_deleted,
                permintaankepenunjang_t.deleted_date,
                permintaankepenunjang_t.is_referred,
                permintaankepenunjang_t.is_exception
               FROM pasienkirimkeunitlain_t
                 JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.permintaankepenunjang_id,
                        a.qtypermintaan,
                        a.is_cyto,
                        a.tarif_pelayanan,
                        a.daftartindakan_id,
                        a.tipepaket_id,
                        a.tarif_cytotindakan,
                        a.satuan_tindakan,
                        a.is_approve,
                        a.tgl_approve,
                        a.is_deleted,
                        a.deleted_date,
                        a.is_referred,
                        a.is_exception
                       FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.tipepaket_id,
                        a.tipepaket_nama
                       FROM tipepaket_m a) tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN ( SELECT a.tipepaket_id,
            a.daftartindakan_id,
            a.is_deleted
           FROM paketpelayanan_mp a) paketpelayanan_mp ON permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
            daftartindakan_m_1.daftartindakan_nama,
            daftartindakan_m_1.is_deleted
           FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.pemeriksaanlab_id,
            a.pemeriksaanlab_nama,
            a.daftartindakan_id,
            a.jenispemeriksaanlab_id,
            a.is_deleted
           FROM pemeriksaanlab_m a) pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN ( SELECT a.jenispemeriksaanlab_id,
            a.jenispemeriksaanlab_nama,
            a.is_deleted
           FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienkirimkeunitlain_t.is_deleted = false AND jenispemeriksaanlab_m.is_deleted = false AND daftartindakan_m.is_deleted = false AND paketpelayanan_mp.is_deleted = false AND pemeriksaanlab_m.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220927_071318_migrate_MHG3788_view_infoorderanlabdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220927_071318_migrate_MHG3788_view_infoorderanlabdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
