<?php

use yii\db\Migration;

/**
 * Class m220530_073231_hotfix_PRODUK_VCS193_laporanpemakaianbmhp_v
 */
class m220530_073231_hotfix_PRODUK_VCS193_laporanpemakaianbmhp_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpemakaianbmhp_v";');
        $this->execute("CREATE VIEW \"public\".\"laporanpemakaianbmhp_v\" AS  SELECT obatalkespasien_t.tglpelayanan AS tgl_transaksi,
        pasien_m.no_rekam_medik AS no_rm,
        pendaftaran_t.no_pendaftaran,
        concat(look_namadepan.lookup_name, pasien_m.nama_pasien) AS nama_pasien,
        daftartindakan_m.daftartindakan_nama AS tindakan,
        obatalkes_m.obatalkes_kode,
        obatalkes_m.obatalkes_nama,
        obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
        obatalkespasien_t.satuankecil_id,
        satuanunit_m.satuanunit_nama::text AS satuan_kecil_nama,
        obatalkespasien_t.additional_data::json ->> 'qty_input'::text AS qty_input,
            CASE
                WHEN obatalkespasien_t.qty_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.qty_konversi, 0::double precision)
                ELSE COALESCE(obatalkespasien_t.qty_oa, 0::double precision)
            END AS qty,
        obatalkes_m.harganetto AS harga_netto,
            CASE
                WHEN obatalkespasien_t.qty_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.qty_konversi, 0::double precision) * obatalkes_m.harganetto
                ELSE COALESCE(obatalkespasien_t.qty_oa, 0::double precision) * obatalkes_m.harganetto
            END AS total,
            CASE
                WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN false
                ELSE true
            END AS is_ditagihkan,
        obatalkespasien_t.ruangan_id
       FROM obatalkespasien_t
         JOIN ( SELECT a.pendaftaran_id,
                a.pasien_id,
                a.no_pendaftaran
               FROM pendaftaran_t a) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN ( SELECT a.pasien_id,
                a.no_rekam_medik,
                a.nama_pasien,
                a.namadepan
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN ( SELECT a.obatalkes_kode,
                a.obatalkes_nama,
                a.harganetto,
                a.obatalkes_id
               FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
         LEFT JOIN ( SELECT a.instruksitindakanbmhp_id
               FROM instruksitindakanbmhp_t a) instruksitindakanbmhp_t ON obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
         LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                a.daftartindakan_id
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
         LEFT JOIN ( SELECT a.daftartindakan_nama,
                a.daftartindakan_id
               FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
         LEFT JOIN ( SELECT a.satuanunit_nama,
                a.satuanunit_id
               FROM satuanunit_m a) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
         JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
      WHERE obatalkespasien_t.is_deleted = false AND (obatalkespasien_t.instruksitindakanbmhp_id IS NOT NULL OR obatalkespasien_t.tindakanpelayanan_id IS NOT NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220530_073231_hotfix_PRODUK_VCS193_laporanpemakaianbmhp_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220530_073231_hotfix_PRODUK_VCS193_laporanpemakaianbmhp_v cannot be reverted.\n";

        return false;
    }
    */
}
