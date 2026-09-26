<?php

use yii\db\Migration;

/**
 * Class m221208_041908_hotfix_temuan_live_view_hasilpemeriksaanrad_v
 */
class m221208_041908_hotfix_temuan_live_view_hasilpemeriksaanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."hasilpemeriksaanrad_v";
        ');

        $this->execute('
            CREATE VIEW "public"."hasilpemeriksaanrad_v" AS  SELECT \'NON_PAKET\'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tipepaket_id,
    \'\'::character varying AS tipepaket_nama,
    NULL::text AS detail_2,
    tindakanpelayanan_t.daftartindakan_id, 
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    kelompokpemeriksaanrad_m.nama_kelompok,
    tindakanpelayanan_t.cyto_tindakan,
    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
    hasilpemeriksaanrad_t.no_hasilrad,
    hasilpemeriksaanrad_t.tgl_ambilfoto,
    hasilpemeriksaanrad_t.tgl_uploadhasil,
    hasilpemeriksaanrad_t.tgl_hasilrad,
    hasilpemeriksaanrad_t.kesan,
    hasilpemeriksaanrad_t.kesimpulan,
    hasilpemeriksaanrad_t.penanggungjawab_id,
    penanggungjawab.nama_pegawai AS penanggung_jawab,
    hasilpemeriksaanrad_t.hasil_expertise,
    hasilpemeriksaanrad_t.expertise_id,
    hasilpemeriksaanrad_t.is_hasilkritis,
    pasienmasukpenunjang_t.status_periksa,
    hasilpemeriksaanrad_t.tgl_verifikasi,
    hasilpemeriksaanrad_t.status_pemeriksaan,
    fgetnamalookup(hasilpemeriksaanrad_t.status_pemeriksaan) AS status_pemeriksaan_nama,
    COALESCE(hasilpemeriksaanrad_t.is_deleted, false) AS is_deleted,
    tindakanpelayanan_t.is_deleted AS delete_tindakan,
    COALESCE(hasilpemeriksaanrad_t.is_active, true) AS is_active,
    pasienmasukpenunjang_t.created_date
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id AND hasilpemeriksaanrad_t.is_deleted IS FALSE
     LEFT JOIN pegawai_m penanggungjawab ON hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id
     LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
  WHERE tindakanpelayanan_t.instalasi_id = 5 AND tindakanpelayanan_t.is_deleted IS FALSE
UNION ALL
 SELECT \'PAKET\'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::text AS detail_2,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    kelompokpemeriksaanrad_m.nama_kelompok,
    tindakanpelayanan_t.cyto_tindakan,
    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
    hasilpemeriksaanrad_t.no_hasilrad,
    hasilpemeriksaanrad_t.tgl_ambilfoto,
    hasilpemeriksaanrad_t.tgl_uploadhasil,
    hasilpemeriksaanrad_t.tgl_hasilrad,
    hasilpemeriksaanrad_t.kesan,
    hasilpemeriksaanrad_t.kesimpulan,
    hasilpemeriksaanrad_t.penanggungjawab_id,
    penanggungjawab.nama_pegawai AS penanggung_jawab,
    hasilpemeriksaanrad_t.hasil_expertise,
    hasilpemeriksaanrad_t.expertise_id,
    hasilpemeriksaanrad_t.is_hasilkritis,
    pasienmasukpenunjang_t.status_periksa,
    hasilpemeriksaanrad_t.tgl_verifikasi,
    hasilpemeriksaanrad_t.status_pemeriksaan,
    fgetnamalookup(hasilpemeriksaanrad_t.status_pemeriksaan) AS status_pemeriksaan_nama,
    COALESCE(hasilpemeriksaanrad_t.is_deleted, false) AS is_deleted,
    tindakanpelayanan_t.is_deleted AS delete_tindakan,
    COALESCE(hasilpemeriksaanrad_t.is_active, true) AS is_active,
    pasienmasukpenunjang_t.created_date
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
     LEFT JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id AND hasilpemeriksaanrad_t.is_deleted IS FALSE
     LEFT JOIN pegawai_m penanggungjawab ON hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id
     LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
  WHERE tindakanpelayanan_t.instalasi_id = 5 AND tindakanpelayanan_t.is_deleted IS FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221208_041908_hotfix_temuan_live_view_hasilpemeriksaanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221208_041908_hotfix_temuan_live_view_hasilpemeriksaanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
