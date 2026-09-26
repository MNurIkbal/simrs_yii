<?php

use yii\db\Migration;

/**
 * Class m200708_160101_migrate_mhkn_20200708
 */
class m200708_160101_migrate_mhkn_20200708 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."hasilpemeriksaanraddetail_t" ADD COLUMN "status_pemeriksaan" int4 NOT NULL DEFAULT 707;');
    
    $this->execute('DROP VIEW if exists "public"."infoorderanraddetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infoorderanraddetail_v\" AS  SELECT permintaankepenunjang_t.permintaankepenunjang_id,
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
    daftartindakan_m.daftartindakan_kode
   FROM ((((pasienkirimkeunitlain_t
     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
     LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
  WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
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
    daftartindakan_m.daftartindakan_kode
   FROM ((((((pasienkirimkeunitlain_t
     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
     JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON ((permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN pemeriksaanrad_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
     JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
  WHERE (pasienkirimkeunitlain_t.instalasi_id = 5);");

     $this->execute('DROP VIEW if exists "public"."infopasienibubayi_v";');

     $this->execute("
        CREATE VIEW \"public\".\"infopasienibubayi_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.jenisidentitas,
    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas_nama,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin AS nama_panggilan,
    pasien_m.nama_ibu,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    (pasien_m.jeniskelamin)::integer AS jenis_kelaminid,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.golongandarah AS golongandarah_id,
    fgetnamalookup(pasien_m.golongandarah) AS golongandarah_nama,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.nama_ayah,
    pasien_m.propinsi_id,
    pasien_m.propinsi_nama,
    pasien_m.kabupaten_id,
    pasien_m.kabupaten_nama,
    pasien_m.kecamatan_id,
    pasien_m.kecamatan_nama,
    pasien_m.kelurahan_id,
    pasien_m.kelurahan_nama,
    pasien_m.warga_negara,
    pasien_m.agama,
    pasien_m.no_telepon_pasien,
    fgetnamalookup((pasien_m.warga_negara)::integer) AS warganegara_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN r_pendaftaran.ruangan_id
            ELSE r_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN r_pendaftaran.ruangan_nama
            ELSE r_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN i_pendaftaran.instalasi_nama
            ELSE i_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
    pendaftaran_terakhir.tgl_pendaftaran,
    kelahiranbayi_t.bayi_urut,
    kelahiranbayi_t.berat_badan,
    kelahiranbayi_t.tinggi_badan,
    kelahiranbayi_t.jenis_kelamin AS jeniskelamin_id_bayi,
    fgetnamalookup((kelahiranbayi_t.jenis_kelamin)::integer) AS jeniskelamin_bayi,
    fgetnamalookupkeperawatan((kelahiranbayi_t.kondisi_bayi)::integer) AS kondisi_bayi,
    kelahiranbayi_t.pendaftaranbaru_id,
    pendaftaran_bayi.no_pendaftaran AS no_pendaftaranbayi,
    kelahiranbayi_t.kelahiranbayi_id
   FROM ((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.jenisidentitas,
            pasien_m_1.no_identitas_pasien,
            pasien_m_1.nama_pasien,
            pasien_m_1.nama_bin,
            pasien_m_1.nama_ibu,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin,
            (pasien_m_1.golongandarah)::integer AS golongandarah,
            pasien_m_1.alamat_pasien,
            pasien_m_1.rt,
            pasien_m_1.rw,
            pasien_m_1.nama_ayah,
            pasien_m_1.propinsi_id,
            fgetnamaarea(pasien_m_1.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
            pasien_m_1.kabupaten_id,
            fgetnamaarea(NULL::integer, pasien_m_1.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pasien_m_1.kecamatan_id,
            fgetnamaarea(NULL::integer, NULL::integer, pasien_m_1.kecamatan_id, NULL::integer) AS kecamatan_nama,
            pasien_m_1.kelurahan_id,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m_1.kelurahan_id) AS kelurahan_nama,
            pasien_m_1.warga_negara,
            pasien_m_1.agama,
            pasien_m_1.no_telepon_pasien
           FROM pasien_m pasien_m_1) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN instalasi_m i_pendaftaran ON ((r_pendaftaran.instalasi_id = i_pendaftaran.instalasi_id)))
     LEFT JOIN instalasi_m i_admisi ON ((r_admisi.instalasi_id = i_admisi.instalasi_id)))
     LEFT JOIN ( SELECT pendaftaran_t_1.pasien_id,
            max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
           FROM pendaftaran_t pendaftaran_t_1
          GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_terakhir ON (((pendaftaran_t.tgl_pendaftaran = pendaftaran_terakhir.tgl_pendaftaran) AND (pendaftaran_t.pasien_id = pendaftaran_terakhir.pasien_id))))
     LEFT JOIN kelahiranbayi_t ON (((kelahiranbayi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (kelahiranbayi_t.is_deleted = false))))
     LEFT JOIN pendaftaran_t pendaftaran_bayi ON ((kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_bayi.pendaftaran_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
  WHERE ((pasienpulang_t.carakeluar_id = 5) OR ((pasienadmisi_t.pasienpulang_id IS NULL) AND (pasienadmisi_t.pasienpulang_id IS NULL) AND (pendaftaran_t.pasienadmisi_id IS NOT NULL) AND ((pasien_m.jeniskelamin)::text = '16'::text)));");

     $this->execute('DROP VIEW if exists "public"."infopermintaanbmhpdetail_v";');

     $this->execute("
        CREATE VIEW \"public\".\"infopermintaanbmhpdetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    obatalkespasien_t.obatalkespasien_id,
    (to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text))::date AS tgl_permintaan,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    satuanunit_m.satuanunit_nama AS satuan_input,
    satuanunit_m.satuanunit_nama AS satuan_konversi,
    obatalkespasien_t.status_bmhp,
    obatalkespasien_t.satuankecil_id
   FROM (((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_tujuan ON ((obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false) AND (ruangan_tujuan.instalasi_id = 6));");

     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200708_160101_migrate_mhkn_20200708 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200708_160101_migrate_mhkn_20200708 cannot be reverted.\n";

        return false;
    }
    */
}
