<?php

use yii\db\Migration;

/**
 * Class m230711_131416_migrate_hotfix_Infopenerimaanobat_v
 */
class m230711_131416_migrate_hotfix_Infopenerimaanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("DROP VIEW IF EXISTS public.infopenerimaanobat_v;");
		
		
        $this->execute("CREATE OR REPLACE VIEW public.infopenerimaanobat_v
            AS  SELECT penerimaanobat_t.penerimaanobat_id,
    penerimaanobat_t.no_penerimaan,
    penerimaanobat_t.tgl_penerimaan,
    penerimaanobat_t.supplier_id,
    supplier_m.supplier_nama,
    validasipoobat_t.no_poobat,
    penerimaanobat_t.no_suratjalan,
    penerimaanobat_t.tgl_suratjalan,
    penerimaanobat_t.no_faktur,
    penerimaanobat_t.catatan,
    penerimaanobat_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS mengetahui,
    penerimaanobat_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS menyetujui,
    penerimaanobat_t.diterima_oleh,
    peg_menerima.nama_pegawai AS menerima,
    'Gudang Umum'::text AS nama_ruangan,
    penerimaanobat_t.is_verifikasi,
        CASE
            WHEN penerimaanobat_t.is_verifikasi = 0 THEN 'belum verifikasi'::text
            WHEN penerimaanobat_t.is_verifikasi = 1 THEN 'sudah verifikasi'::text
            WHEN penerimaanobat_t.is_verifikasi = 2 THEN 'batal'::text
            ELSE NULL::text
        END AS verifikasi_nama,
    validasipoobat_t.pajak_id,
    pajak_m.pajak_persen,
    penerimaanobatdetail_t.jumlah AS sub_total,
    penerimaanobatdetail_t.discount_rp AS total_discount,
    validasipoobat_t.ppn_persen,
    (penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS ppn_nilai,
    penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp + (penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS total,
    supplier_m.no_tlp,
    supplier_m.no_fax,
    supplier_m.supplier_alamat,
    penerimaanobat_t.no_faktur_sementara
   FROM penerimaanobat_t
     LEFT JOIN ( SELECT a.penerimaanobat_id,
            sum(a.jumlah) AS jumlah,
            sum(a.discount_rp) AS discount_rp
           FROM penerimaanobatdetail_t a
          WHERE a.is_deleted = false
          GROUP BY a.penerimaanobat_id) penerimaanobatdetail_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     JOIN validasipoobat_t ON penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
     JOIN supplier_m ON supplier_m.supplier_id = penerimaanobat_t.supplier_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaanobat_t.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaanobat_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_menerima ON penerimaanobat_t.diterima_oleh = peg_menerima.pegawai_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230711_131416_migrate_hotfix_Infopenerimaanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230711_131416_migrate_hotfix_Infopenerimaanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
