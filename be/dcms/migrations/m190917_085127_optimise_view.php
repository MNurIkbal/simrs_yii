<?php

use yii\db\Migration;

/**
 * Class m190917_085127_optimise_view
 */
class m190917_085127_optimise_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/* -- View: public.ambiljenazah_v */

        $this->execute('DROP VIEW if exists public.ambiljenazah_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.ambiljenazah_v AS 
 SELECT ambiljenazah_t.ambiljenazah_id,
    ambiljenazah_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    ambiljenazah_t.nama_pengambil,
    ambiljenazah_t.pekerjaan,
    ambiljenazah_t.alamat,
    ambiljenazah_t.no_identitas,
    ambiljenazah_t.tempat_lahir,
    ambiljenazah_t.tgl_lahir,
    ambiljenazah_t.created_date AS tgl_ambil,
    ruangan_m.ruangan_nama,
    fgetnamalookup(ambiljenazah_t.hubungan_keluarga) AS hubungan_keluarga,
    pasien_m.nama_pasien,
    pasien_m.tempat_lahir AS tempat_lahirpasien,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    ambiljenazah_t.kondisi,
    ambiljenazah_t.tgl_meninggal,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruang_asal.ruangan_nama AS ruangan_asal,
    pegawai_m.nama_pegawai,
    profilrumahsakit_m.nama_rumahsakit
   FROM ambiljenazah_t
     JOIN pendaftaran_t ON ambiljenazah_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON ambiljenazah_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
     JOIN ruangan_m ruang_asal ON pasienmasukpenunjang_t.ruanganasal_id = ruang_asal.ruangan_id
     JOIN loginpemakai_k ON ambiljenazah_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN profilrumahsakit_m ON profilrumahsakit_m.is_deleted = false
  GROUP BY ambiljenazah_t.ambiljenazah_id, ambiljenazah_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, ambiljenazah_t.nama_pengambil, ambiljenazah_t.pekerjaan, ambiljenazah_t.alamat, ambiljenazah_t.no_identitas, ambiljenazah_t.tempat_lahir, ambiljenazah_t.tgl_lahir, ambiljenazah_t.created_date, ruangan_m.ruangan_nama, ambiljenazah_t.hubungan_keluarga, pasien_m.nama_pasien, pasien_m.tempat_lahir, pasien_m.tanggal_lahir, pasien_m.alamat_pasien, ambiljenazah_t.kondisi, ambiljenazah_t.tgl_meninggal, pasienmasukpenunjang_t.ruanganasal_id, ruang_asal.ruangan_nama, pegawai_m.nama_pegawai, profilrumahsakit_m.nama_rumahsakit;
");

        $this->execute('ALTER TABLE public.ambiljenazah_v
                    OWNER TO postgres;');

/* -- View: public.infopasiengizi_v */

        $this->execute('DROP VIEW if exists public.infopasiengizi_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasiengizi_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE asmen_gizi.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenawal_t.asmen_riwayat,
    asesmenmedis_t.r_peskk
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN lookupkeperawatan_m asmen_gizi ON skrininggizi_t.status_asesmen = asmen_gizi.lookupkeperawatan_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");

        $this->execute('ALTER TABLE public.infopasiengizi_v
                    OWNER TO postgres;');

/* -- View: public.infopasiengizi_v_old */

    $this->execute('DROP VIEW if exists public.infopasiengizi_v_old;');


    $this->execute("
        CREATE OR REPLACE VIEW public.infopasiengizi_v_old AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE asmen_gizi.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    ( SELECT count(*) AS count
           FROM asuhangizi_t
          WHERE asuhangizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AS jml_data_asuhan,
    asesmenawalgizi_t.asesmenawalgizi_id
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN lookupkeperawatan_m asmen_gizi ON skrininggizi_t.status_asesmen = asmen_gizi.lookupkeperawatan_id
     LEFT JOIN asesmenawalgizi_t ON asesmenawalgizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");
    
    $this->execute('ALTER TABLE public.infopasiengizi_v_old
  OWNER TO postgres;');
    
/* -- View: public.barang_v */

     $this->execute('DROP VIEW if exists public.barang_v;');

      $this->execute("
        CREATE OR REPLACE VIEW public.barang_v AS 
 SELECT hit.barang_id,
    hit.golonganbarang_id,
    hit.golonganbarang_nama,
    hit.kelompokbarang_id,
    hit.kelompokbarang_nama,
    hit.subkelompokbarang_id,
    hit.subkelompok_nama,
    hit.barang_nama,
    hit.barang_merk,
    hit.is_active,
    hit.is_deleted,
    hit.on_ro,
    hit.on_po,
    hit.satuankecil_id,
    hit.satuan_kecil,
    hit.is_kadaluarsa,
    hit.ppn,
    hit.margin,
    hit.disc,
    hit.hn_last,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hn_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hn_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hn_avg,
    hit.d1 AS hn_avg_margin,
    hit.d2 AS hn_avg_diskon,
    hit.d3 AS hn_avg_margin_diskon,
    hit.d4 AS hn_avg_ppn,
    hit.d5 AS hargajual_avg,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c5
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b5
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d5
            ELSE hit.a5
        END AS hargaygdipakai,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.hn_max
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.hn_min
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.hn_avg
            ELSE hit.hn_last
        END AS harganetto_ygdipakai,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c1
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b1
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d1
            ELSE hit.a1
        END AS hn_margin,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c2
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b2
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d2
            ELSE hit.a2
        END AS hn_diskon,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c4
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b4
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d4
            ELSE hit.a4
        END AS hn_ppn,
    hit.barang_kode
   FROM ( SELECT barang_m.barang_id,
            barang_m.barang_kode,
            barang_m.golonganbarang_id,
            fgetnamalookup(barang_m.golonganbarang_id) AS golonganbarang_nama,
            barang_m.kelompokbarang_id,
            kelompokbarang_m.kelompokbarang_nama,
            barang_m.subkelompokbarang_id,
            subkelompokbarang_m.subkelompok_nama,
            barang_m.barang_nama,
            barang_m.barang_merk,
            barang_m.is_active,
            barang_m.is_deleted,
            barang_m.on_ro,
            barang_m.on_po,
            barang_m.satuankecil_id,
            sat_kecil.satuanunit_nama AS satuan_kecil,
            barang_m.is_kadaluarsa,
            barang_m.barang_harganetto AS hn_last,
            barang_m.barang_min AS hn_min,
            barang_m.barang_max AS hn_max,
            barang_m.barang_average AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision AS a1,
            (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
            barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
            (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
            barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
            barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision AS b1,
            (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
            barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
            (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
            barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b5,
            barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision AS c1,
            (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
            barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
            (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
            barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c5,
            barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision AS d1,
            (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
            barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
            (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
            barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5
           FROM barang_m
             JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
             JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
             LEFT JOIN satuanunit_m sat_kecil ON barang_m.satuankecil_id = sat_kecil.satuanunit_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false) hit;");

       $this->execute('ALTER TABLE public.barang_v
  OWNER TO postgres;
');
  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_085127_optimise_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_085127_optimise_view cannot be reverted.\n";

        return false;
    }
    */
}
