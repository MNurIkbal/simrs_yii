<?php

use yii\db\Migration;

/**
 * Class m220512_103919_migrate_VCS162_laporanrekappenerimaanbarang_v
 */
class m220512_103919_migrate_VCS162_laporanrekappenerimaanbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanrekappenerimaanbarang_v";');
        $this->execute("CREATE VIEW \"public\".\"laporanrekappenerimaanbarang_v\" AS  SELECT rekap.penerimaanbarang_id,
        rekap.supplier_id,
        rekap.supplier_kode,
        rekap.supplier_nama,
        rekap.tgl_penerimaan,
        rekap.payterm_id,
        rekap.payterm_nama,
        rekap.no_penerimaan,
        rekap.diterima_oleh,
        rekap.status_penerimaan,
        rekap.nomor_po,
        rekap.tgl_po,
        rekap.tgl_validasi_po,
        rekap.no_suratjalan,
        rekap.no_faktur,
        sum(rekap.total) AS total
       FROM ( SELECT penerimaanbarang_t.penerimaanbarang_id,
                supplier_m.supplier_id,
                supplier_m.supplier_kode,
                supplier_m.supplier_nama,
                penerimaanbarang_t.tgl_penerimaan,
                penerimaanbarang_t.no_penerimaan,
                pegawai_m.nama_pegawai AS diterima_oleh,
                    CASE
                        WHEN penerimaanbarang_t.is_verifikasi::integer = 1 THEN 'Sudah diverifikasi'::text
                        WHEN penerimaanbarang_t.is_verifikasi::integer = 2 THEN 'Dibatalkan'::text
                        ELSE 'Belum diverifikasi'::text
                    END AS status_penerimaan,
                validasipobarang_t.no_pobarang AS nomor_po,
                validasipobarang_t.created_date AS tgl_po,
                validasipobarang_t.tgl_validasi AS tgl_validasi_po,
                penerimaanbarang_t.no_suratjalan,
                penerimaanbarang_t.no_faktur,
                penerimaanbarangdetail_t.qty_diterima::double precision * penerimaanbarangdetail_t.harga - penerimaanbarangdetail_t.discount_rp + (penerimaanbarangdetail_t.qty_diterima::double precision * penerimaanbarangdetail_t.harga - penerimaanbarangdetail_t.discount_rp) * (pajak_m.pajak_persen::double precision / 100::double precision) AS total,
                validasipobarang_t.payterm_id,
                payterm_m.payterm_nama
               FROM penerimaanbarang_t
                 JOIN ( SELECT a.penerimaanbarang_id,
                        a.qty_diterima,
                        a.harga,
                        a.discount_rp,
                        a.validasipobarangdetail_id,
                        a.is_deleted
                       FROM penerimaanbarangdetail_t a) penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id
                 LEFT JOIN ( SELECT a.validasipobarang_id,
                        a.no_pobarang,
                        a.created_date,
                        a.tgl_validasi,
                        a.payterm_id,
                        a.pajak_id
                       FROM validasipobarang_t a) validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
                 JOIN ( SELECT a.validasipobarangdetail_id,
                        a.purchasereqbrgdetail_id
                       FROM validasipobarangdetail_t a) validasipobarangdetail_t ON penerimaanbarangdetail_t.validasipobarangdetail_id = validasipobarangdetail_t.validasipobarangdetail_id
                 LEFT JOIN ( SELECT a.purchasereqbrgdetail_id,
                        a.purchasereqbrg_id
                       FROM purchasereqbrgdetail_t a) purchasereqbrgdetail_t ON validasipobarangdetail_t.purchasereqbrgdetail_id = purchasereqbrgdetail_t.purchasereqbrgdetail_id
                 LEFT JOIN ( SELECT a.purchasereqbrg_id
                       FROM purchasereqbrg_t a) purchasereqbrg_t ON purchasereqbrgdetail_t.purchasereqbrg_id = purchasereqbrg_t.purchasereqbrg_id
                 JOIN ( SELECT a.supplier_id,
                        a.supplier_kode,
                        a.supplier_nama
                       FROM supplier_m a) supplier_m ON penerimaanbarang_t.supplier_id = supplier_m.supplier_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON penerimaanbarang_t.diterima_oleh = pegawai_m.pegawai_id
                 LEFT JOIN ( SELECT a.pajak_id,
                        a.pajak_persen
                       FROM pajak_m a) pajak_m ON pajak_m.pajak_id = validasipobarang_t.pajak_id
                 JOIN ( SELECT a.payterm_id,
                        a.payterm_nama
                       FROM payterm_m a) payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
              WHERE penerimaanbarangdetail_t.is_deleted = false AND penerimaanbarang_t.is_deleted = false) rekap
      GROUP BY rekap.penerimaanbarang_id, rekap.supplier_id, rekap.supplier_kode, rekap.supplier_nama, rekap.tgl_penerimaan, rekap.no_penerimaan, rekap.diterima_oleh, rekap.status_penerimaan, rekap.nomor_po, rekap.tgl_po, rekap.tgl_validasi_po, rekap.no_suratjalan, rekap.no_faktur, rekap.payterm_id, rekap.payterm_nama;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220512_103919_migrate_VCS162_laporanrekappenerimaanbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_103919_migrate_VCS162_laporanrekappenerimaanbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
