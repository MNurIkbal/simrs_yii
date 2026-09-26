<?php

use yii\db\Migration;

/**
 * Class m220420_180122_migrate_sobarang_laporanhasilsobarang_v
 */
class m220420_180122_migrate_sobarang_laporanhasilsobarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanhasilsobarang_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasilsobarang_v\" AS   SELECT a.stokopnamebarang_id,
    a.kondisi,
    a.tgl_form_so,
    a.no_form_so,
    a.tgl_validasi_so,
    a.validasi_by,
    a.ruangan_id,
    a.instalasi_id,
    a.nostokopname,
    a.instalasi_ruangan,
    a.kelompokbarang_nama,
    a.subkelompok_nama,
    a.barang_id,
    a.kode_barang,
    a.nama_barang,
    a.harganetto,
    a.satuan_kecil,
    a.stok_sistem,
    a.stok_fisik,
    a.selisih,
    a.stok_akhir,
    a.selisih_akhir,
    a.total_harga_selisih,
    a.total_harga_netto,
    a.tgl_implementasi
   FROM ( SELECT stokopnamebarang_t.stokopnamebarang_id,
                CASE
                    WHEN stokbarang_t.tglstok_in IS NOT NULL THEN 'IN'::text
                    ELSE 'OUT'::text
                END AS kondisi,
                CASE
                    WHEN stokbarang_t.tglstok_in IS NULL THEN max(stokbarang_t.tglstok_out)
                    WHEN stokbarang_t.tglstok_in IS NOT NULL THEN max(stokbarang_t.tglstok_in)
                    ELSE NULL::timestamp without time zone
                END AS tgl_stokopname,
            formsobarang_t.created_date AS tgl_form_so,
            formsobarang_t.noformulir AS no_form_so,
            stokopnamebarang_t.tglverifikasi AS tgl_validasi_so,
            peg_verif_so.nama_pegawai AS validasi_by,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokopnamebarang_t.nostokopname,
            concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
            kelompokbarang_m.kelompokbarang_nama,
            subkelompokbarang_m.subkelompok_nama,
            stokopnamebarangdetail_t.barang_id,
            barang_m.barang_kode AS kode_barang,
            barang_m.barang_nama AS nama_barang,
            barang_m.barang_harganetto AS harganetto,
            sat_kecil.satuanunit_nama AS satuan_kecil,
            formsobarangdetail_t.stok AS stok_sistem,
            stokopnamebarangdetail_t.volume_fisik AS stok_fisik,
            stokopnamebarangdetail_t.volume_fisik - stokopnamebarangdetail_t.volume_sistem AS selisih,
            COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) AS stok_akhir,
            COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) - stokopnamebarangdetail_t.volume_sistem AS selisih_akhir,
            barang_m.barang_harganetto * (stokopnamebarangdetail_t.volume_fisik - stokopnamebarangdetail_t.volume_sistem) AS total_harga_selisih,
            barang_m.barang_harganetto * stokopnamebarangdetail_t.volume_fisik AS total_harga_netto,
            stokopnamebarang_t.tgl_implementasi
           FROM stokopnamebarang_t
             LEFT JOIN stokopnamebarangdetail_t ON stokopnamebarang_t.stokopnamebarang_id = stokopnamebarangdetail_t.stokopnamebarang_id
             LEFT JOIN stokbarang_t ON stokopnamebarangdetail_t.stokopnamebarangdetail_id = stokbarang_t.stokopnamebarangdetail_id
             LEFT JOIN formsobarang_t ON stokopnamebarang_t.stokopnamebarang_id = formsobarang_t.stokopnamebarang_id
             LEFT JOIN pegawai_m peg_verif_so ON stokopnamebarang_t.pegawaiverifikasi_id = peg_verif_so.pegawai_id
             LEFT JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN barang_m ON stokopnamebarangdetail_t.barang_id = barang_m.barang_id
             LEFT JOIN formsobarangdetail_t ON stokopnamebarangdetail_t.stokopnamebarangdetail_id = formsobarangdetail_t.stokopnamebarangdetail_id
             LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
             LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
             LEFT JOIN satuanunit_m sat_kecil ON barang_m.satuankecil_id = sat_kecil.satuanunit_id
          GROUP BY stokopnamebarang_t.stokopnamebarang_id, formsobarang_t.created_date, formsobarang_t.noformulir, stokbarang_t.tglstok_in, stokopnamebarangdetail_t.barang_id, barang_m.barang_kode, barang_m.barang_nama, stokopnamebarang_t.tglverifikasi, peg_verif_so.nama_pegawai, ruangan_m.ruangan_id, instalasi_m.instalasi_id, stokopnamebarang_t.nostokopname, formsobarangdetail_t.stok, stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem, barang_m.barang_harganetto, stokopnamebarang_t.tgl_implementasi, kelompokbarang_m.kelompokbarang_nama, sat_kecil.satuanunit_nama, subkelompokbarang_m.subkelompok_nama) a;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_180122_migrate_sobarang_laporanhasilsobarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_180122_migrate_sobarang_laporanhasilsobarang_v cannot be reverted.\n";

        return false;
    }
    */
}
