<?php

use yii\db\Migration;

/**
 * Class m220618_043722_migrate_MHG1791_DBE23_infoorderanraddetail_v
 */
class m220618_043722_migrate_MHG1791_DBE23_infoorderanraddetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoorderanraddetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infoorderanraddetail_v\" AS
            SELECT permintaankepenunjang_t.permintaankepenunjang_id,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            daftartindakan_m.daftartindakan_nama,
            NULL::character varying AS tipepaket_nama,
            permintaankepenunjang_t.qtypermintaan,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tarif_pelayanan,
            permintaankepenunjang_t.daftartindakan_id,
            permintaankepenunjang_t.tipepaket_id,
            permintaankepenunjang_t.tarif_cytotindakan,
            permintaankepenunjang_t.satuan_tindakan,
            daftartindakan_m.daftartindakan_kode,
            permintaankepenunjang_t.dokter_id,
            permintaankepenunjang_t.is_deleted,
            permintaankepenunjang_t.alasan_batal,
            permintaankepenunjang_t.is_referred,
            permintaankepenunjang_t.is_approve
            FROM pasienkirimkeunitlain_t
            JOIN ( SELECT a.permintaankepenunjang_id,
            a.pasienkirimkeunitlain_id,
            a.qtypermintaan,
            a.is_cyto,
            a.tarif_pelayanan,
            a.daftartindakan_id,
            a.tipepaket_id,
            a.tarif_cytotindakan,
            a.satuan_tindakan,
            a.dokter_id,
            a.is_deleted,
            a.alasan_batal,
            a.is_referred,
            a.is_approve
            FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
            LEFT JOIN ( SELECT a.pemeriksaanradiologi_id,
            a.daftartindakan_id,
            a.jenispemeriksaanrad_id
            FROM pemeriksaanrad_m a) pemeriksaanrad_m ON permintaankepenunjang_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
            LEFT JOIN ( SELECT jenispemeriksaanrad_m_1.jenispemeriksaanrad_id,
            jenispemeriksaanrad_m_1.jenispemeriksaanrad_nama
            FROM jenispemeriksaanrad_m jenispemeriksaanrad_m_1) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
            JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE pasienkirimkeunitlain_t.instalasi_id = 5
            UNION ALL
            SELECT permintaankepenunjang_t.permintaankepenunjang_id,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            concat(tipepaket_m.tipepaket_nama, '-', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            permintaankepenunjang_t.qtypermintaan,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tarif_pelayanan,
            paketpelayanan_mp.daftartindakan_id,
            permintaankepenunjang_t.tipepaket_id,
            permintaankepenunjang_t.tarif_cytotindakan,
            permintaankepenunjang_t.satuan_tindakan,
            daftartindakan_m.daftartindakan_kode,
            permintaankepenunjang_t.dokter_id,
            permintaankepenunjang_t.is_deleted,
            permintaankepenunjang_t.alasan_batal,
            permintaankepenunjang_t.is_referred,
            permintaankepenunjang_t.is_approve
            FROM pasienkirimkeunitlain_t
            JOIN ( SELECT a.pasienkirimkeunitlain_id,
            a.tipepaket_id,
            a.permintaankepenunjang_id,
            a.qtypermintaan,
            a.is_cyto,
            a.tarif_pelayanan,
            a.tarif_cytotindakan,
            a.satuan_tindakan,
            a.dokter_id,
            a.is_deleted,
            a.alasan_batal,
            a.is_referred,
            a.is_approve
            FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
            JOIN ( SELECT a.tipepaket_id,
            a.tipepaket_nama
            FROM tipepaket_m a) tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
            JOIN ( SELECT a.tipepaket_id,
            a.daftartindakan_id
            FROM paketpelayanan_mp a) paketpelayanan_mp ON permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
            JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama,
            a.daftartindakan_kode
            FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN ( SELECT a.daftartindakan_id,
            a.jenispemeriksaanrad_id,
            a.pemeriksaanrad_nama
            FROM pemeriksaanrad_m a) pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
            JOIN ( SELECT a.jenispemeriksaanrad_id,
            a.jenispemeriksaanrad_nama
            FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
            WHERE pasienkirimkeunitlain_t.instalasi_id = 5
            ;");
        $this->execute('
            ALTER TABLE public.infoorderanraddetail_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220618_043722_migrate_MHG1791_DBE23_infoorderanraddetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220618_043722_migrate_MHG1791_DBE23_infoorderanraddetail_v cannot be reverted.\n";

        return false;
    }
    */
}
