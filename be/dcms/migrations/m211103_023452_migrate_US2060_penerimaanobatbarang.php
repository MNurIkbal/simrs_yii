<?php

use yii\db\Migration;

/**
 * Class m211103_023452_migrate_US2060_penerimaanobatbarang
 */
class m211103_023452_migrate_US2060_penerimaanobatbarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopenerimaanobatdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopenerimaanobatdetail_v\" AS
            SELECT terima.penerimaanobat_id,
            terima.tgl_penerimaan,
            terima.no_penerimaan,
            terima.nomor_po,
            terima.supplier_id,
            supplier_m.supplier_nama,
            terima.obatalkes_id,
            obatalkes_m.obatalkes_nama,
            terima.qty_po,
            terima.qty_diterima,
            terima.po_balance,
            terima.tgl_kadaluarsa,
            terima.no_batch,
            terima.s_konversiobt_id,
            satuankonversi_m.satuanbesar_id,
            concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuanunit_nama,
            terima.no_suratjalan,
            terima.tgl_suratjalan,
            terima.no_faktur,
            terima.diterima_oleh,
            terima.keterangan,
            terima.upload_berkas,
            terima.catatan_berkas,
            terima.catatan,
            terima.is_verifikasi AS status_invoice,
            besar.satuanunit_nama AS satuan_besar,
            kecil.satuanunit_nama AS satuan_kecil,
            terima.validasipoobatdetail_id,
            terima.harga,
            terima.discount,
            terima.discount_rp,
            terima.jumlah,
            terima.pajak_id,
            terima.penerimaanobatdetail_id,
            satuankonversi_m.nilai_konversi,
            COALESCE(returdetailjumlah.on_retur, (0)::bigint) AS on_retur,
            obatalkes_m.obatalkes_kode AS kode_item,
            terima.sub_total,
            terima.total_discount,
            terima.ppn_persen,
            terima.ppn_nilai,
            terima.total,
            terima.harga_total
            FROM ((((((((( SELECT penerimaanobat_t.penerimaanobat_id,
            penerimaanobat_t.tgl_penerimaan,
            penerimaanobat_t.no_penerimaan,
            validasipoobat_t.no_poobat AS nomor_po,
            penerimaanobat_t.supplier_id,
            penerimaanobat_t.no_suratjalan,
            penerimaanobat_t.tgl_suratjalan,
            penerimaanobat_t.no_faktur,
            penerimaanobat_t.diterima_oleh,
            penerimaanobat_t.upload_berkas,
            penerimaanobat_t.catatan_berkas,
            penerimaanobat_t.catatan,
            penerimaanobat_t.peg_mengetahui,
            penerimaanobat_t.peg_menyetujui,
            penerimaanobatdetail_t.penerimaanobatdetail_id,
            penerimaanobatdetail_t.obatalkes_id,
            penerimaanobatdetail_t.qty_po,
            penerimaanobatdetail_t.qty_diterima,
            penerimaanobatdetail_t.po_balance,
            penerimaanobatdetail_t.tgl_kadaluarsa,
            penerimaanobatdetail_t.no_batch,
            penerimaanobatdetail_t.s_konversiobt_id,
            penerimaanobatdetail_t.keterangan,
            penerimaanobat_t.is_verifikasi,
            penerimaanobatdetail_t.validasipoobatdetail_id,
            penerimaanobatdetail_t.harga,
            penerimaanobatdetail_t.discount,
            penerimaanobatdetail_t.discount_rp,
            penerimaanobatdetail_t.jumlah,
            validasipoobat_t.pajak_id,
            validasipoobat_t.sub_total,
            validasipoobat_t.total_discount,
            validasipoobat_t.ppn_persen,
            validasipoobat_t.ppn_nilai,
            validasipoobat_t.total,
            ((((penerimaanobatdetail_t.qty_diterima)::double precision * penerimaanobatdetail_t.harga) - ((penerimaanobatdetail_t.discount / (100)::double precision) * ((penerimaanobatdetail_t.qty_diterima)::double precision * penerimaanobatdetail_t.harga))) + (((((penerimaanobatdetail_t.qty_diterima)::double precision * penerimaanobatdetail_t.harga) - ((penerimaanobatdetail_t.discount / (100)::double precision) * ((penerimaanobatdetail_t.qty_diterima)::double precision * penerimaanobatdetail_t.harga))) * (pajak_m.pajak_persen)::double precision) / (100)::double precision)) AS harga_total
            FROM (((penerimaanobat_t
            JOIN penerimaanobatdetail_t ON ((penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id)))
            LEFT JOIN validasipoobat_t ON ((penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id)))
            LEFT JOIN pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
            WHERE ((penerimaanobatdetail_t.is_deleted = false) AND (penerimaanobat_t.is_deleted = false))) terima
            JOIN supplier_m ON ((terima.supplier_id = supplier_m.supplier_id)))
            JOIN obatalkes_m ON ((terima.obatalkes_id = obatalkes_m.obatalkes_id)))
            JOIN satuankonversi_m ON ((terima.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
            JOIN satuanunit_m besar ON ((satuankonversi_m.satuanbesar_id = besar.satuanunit_id)))
            JOIN satuanunit_m kecil ON ((satuankonversi_m.satuankecil_id = kecil.satuanunit_id)))
            LEFT JOIN pegawai_m peg_mengetahui ON ((terima.peg_mengetahui = peg_mengetahui.pegawai_id)))
            LEFT JOIN pegawai_m peg_menyetujui ON ((terima.peg_menyetujui = peg_menyetujui.pegawai_id)))
            LEFT JOIN ( SELECT returpenerimaanobatdetail_t.penerimaanobatdetail_id,
            sum(returpenerimaanobatdetail_t.qty_retur) AS on_retur
            FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t
            GROUP BY returpenerimaanobatdetail_t.penerimaanobatdetail_id) returdetailjumlah ON ((terima.penerimaanobatdetail_id = returdetailjumlah.penerimaanobatdetail_id)))
        ;");
        $this->execute('
            ALTER TABLE public.infopenerimaanobatdetail_v OWNER TO postgres;
        ');

        $this->execute('DROP VIEW if exists public.infopenerimaanbarangdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopenerimaanbarangdetail_v\" AS
            SELECT terima.penerimaanbarang_id,
            terima.tgl_penerimaan,
            terima.no_penerimaan,
            terima.nomor_po,
            terima.supplier_id,
            supplier_m.supplier_nama,
            terima.barang_id,
            barang_m.barang_nama,
            terima.qty_po,
            terima.qty_diterima,
            terima.po_balance,
            CASE
            WHEN (barang_m.is_kadaluarsa = true) THEN terima.tgl_kadaluarsa
            ELSE NULL::date
            END AS tgl_kadaluarsa,
            terima.no_batch,
            terima.s_konversibrg_id,
            satuankonversibrg_m.satuanbesar_id,
            concat('1 ', besar.satuanunit_nama, ' - ', satuankonversibrg_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuanunit_nama,
            terima.no_suratjalan,
            terima.tgl_suratjalan,
            terima.no_faktur,
            terima.diterima_oleh,
            terima.keterangan,
            terima.upload_berkas,
            terima.catatan_berkas,
            terima.catatan,
            COALESCE((terima.is_verifikasi)::integer, 0) AS status_invoice,
            besar.satuanunit_nama AS satuan_besar,
            kecil.satuanunit_nama AS satuan_kecil,
            barang_m.is_kadaluarsa,
            terima.penerimaanbarangdetail_id,
            terima.harga,
            terima.discount_rp,
            terima.discount,
            terima.jumlah,
            terima.validasipobarangdetail_id,
            terima.pajak_id,
            satuankonversibrg_m.nilai_konversi,
            COALESCE(returdetailjumlah.on_retur, (0)::bigint) AS on_retur,
            barang_m.barang_kode AS kode_item,
            terima.harga_total
            FROM ((((((((( SELECT penerimaanbarang_t.penerimaanbarang_id,
            penerimaanbarang_t.tgl_penerimaan,
            penerimaanbarang_t.no_penerimaan,
            validasipobarang_t.no_pobarang AS nomor_po,
            penerimaanbarang_t.supplier_id,
            penerimaanbarang_t.no_suratjalan,
            penerimaanbarang_t.tgl_suratjalan,
            penerimaanbarang_t.no_faktur,
            penerimaanbarang_t.diterima_oleh,
            penerimaanbarang_t.upload_berkas,
            penerimaanbarang_t.catatan_berkas,
            penerimaanbarang_t.catatan,
            penerimaanbarang_t.peg_mengetahui,
            penerimaanbarang_t.peg_menyetujui,
            penerimaanbarangdetail_t.penerimaanbarangdetail_id,
            penerimaanbarangdetail_t.barang_id,
            penerimaanbarangdetail_t.qty_po,
            penerimaanbarangdetail_t.qty_diterima,
            penerimaanbarangdetail_t.po_balance,
            penerimaanbarangdetail_t.tgl_kadaluarsa,
            penerimaanbarangdetail_t.no_batch,
            penerimaanbarangdetail_t.s_konversibrg_id,
            penerimaanbarangdetail_t.keterangan,
            penerimaanbarangdetail_t.harga,
            penerimaanbarangdetail_t.discount_rp,
            penerimaanbarangdetail_t.discount,
            penerimaanbarangdetail_t.jumlah,
            penerimaanbarangdetail_t.validasipobarangdetail_id,
            penerimaanbarang_t.is_verifikasi,
            validasipobarang_t.pajak_id,
            ((((penerimaanbarangdetail_t.qty_diterima)::double precision * penerimaanbarangdetail_t.harga) - ((penerimaanbarangdetail_t.discount / (100)::double precision) * ((penerimaanbarangdetail_t.qty_diterima)::double precision * penerimaanbarangdetail_t.harga))) + (((((penerimaanbarangdetail_t.qty_diterima)::double precision * penerimaanbarangdetail_t.harga) - ((penerimaanbarangdetail_t.discount / (100)::double precision) * ((penerimaanbarangdetail_t.qty_diterima)::double precision * penerimaanbarangdetail_t.harga))) * (pajak_m.pajak_persen)::double precision) / (100)::double precision)) AS harga_total
            FROM (((penerimaanbarang_t
            JOIN penerimaanbarangdetail_t ON ((penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id)))
            JOIN validasipobarang_t ON ((penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id)))
            LEFT JOIN pajak_m ON ((validasipobarang_t.pajak_id = pajak_m.pajak_id)))
            WHERE (penerimaanbarangdetail_t.is_deleted = false)) terima
            JOIN supplier_m ON ((terima.supplier_id = supplier_m.supplier_id)))
            JOIN barang_m ON ((terima.barang_id = barang_m.barang_id)))
            JOIN satuankonversibrg_m ON ((terima.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id)))
            JOIN satuanunit_m besar ON ((satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id)))
            JOIN satuanunit_m kecil ON ((satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id)))
            LEFT JOIN pegawai_m peg_mengetahui ON ((terima.peg_mengetahui = peg_mengetahui.pegawai_id)))
            LEFT JOIN pegawai_m peg_menyetujui ON ((terima.peg_menyetujui = peg_menyetujui.pegawai_id)))
            LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.penerimaanbarangdetail_id,
            sum(returpenerimaanbarangdetail_t.qty_retur) AS on_retur
            FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t
            GROUP BY returpenerimaanbarangdetail_t.penerimaanbarangdetail_id) returdetailjumlah ON ((terima.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id)))
        ;");
        $this->execute('
            ALTER TABLE public.infopenerimaanbarangdetail_v OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211103_023452_migrate_US2060_penerimaanobatbarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211103_023452_migrate_US2060_penerimaanobatbarang cannot be reverted.\n";

        return false;
    }
    */
}
