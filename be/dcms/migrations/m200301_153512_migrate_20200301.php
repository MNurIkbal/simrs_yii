<?php

use yii\db\Migration;

/**
 * Class m200301_153512_migrate_20200301
 */
class m200301_153512_migrate_20200301 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
                 $this->execute("
                  INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
                (664, 'status_konfirmasirm', 'Belum Proses', 'Belum Proses', NULL, NULL, NULL, '2020-02-26 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (665, 'status_konfirmasirm', 'Proses', 'Proses', NULL, NULL, NULL, '2020-02-26 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
                    ");

                $this->execute('TRUNCATE TABLE lookuptransaksi_m RESTART IDENTITY;');


                 $this->execute("
                INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES 
                ('kelompok_karcis', 17, 'digunakan untuk pengelompokan tindakan karcis (kelompoktindakan_m)'),
                ('RJ', 1, 'kode instalasi rawat jalan (instalasi_m)'),
                ('RD', 2, 'kode instalasi rawat darurat (instalasi_m)'),
                ('RI', 3, 'kode instalasi rawat inap (instalasi_m)'),
                ('komponen_total', 6, 'digunakan untuk komponen total tarif tindakan (komponentarif_m)'),
                ('kelompok_rad', 10, 'digunakan untuk pengelompokan tindakan radiologi (kelompoktindakan_m)'),
                ('kelompok_lab', 26, 'digunakan untuk pengelompokan tindakan laboratorium (kelompoktindakan_m)'),
                ('rujuk_ranap', 5, 'cara keluar rujuk rawat inap (carakeluar_m)'),
                ('meninggal', 4, 'cara keluar meninggal (carakeluar_m)'),
                ('t_medis', 1, 'tenaga medis - dokter, dokter gigi, dokter spesialis, dokter gigi spesialis (kelompokpegawai_m)'),
                ('t_keperawatan', 2, 'perawat, suster (kelompokpegawai_m)'),
                ('t_kebidanan', 3, 'bidan (kelompokpegawai_m)'),
                ('t_kefarmasian', 4, 'apoteker (kelompokpegawai_m)'),
                ('t_kesehatan', 5, 'epidemiolog kesehatan, tenaga promosi kesehatan dan ilmu perilaku, pembimbing kesehatan kerja, tenaga administrasi dan kebijakan kesehatan, tenaga biostatistik dan kependudukan, serta tenaga kesehatan reproduksi dan keluarga (kelompokpegawai_m)'),
                ('t_kesling', 6, 'tenaga sanitasi lingkungan, entomolog kesehatan, dan mikrobiolog kesehatan (kelompokpegawai_m)'),
                ('t_nonkesehatan', 7, 'non kesehatan (kelompokpegawai_m)'),
                ('t_keterapian', 8, 'fisioterapis, okupasi terapis, terapis wicara, dan akupunktur (kelompokpegawai_m)'),
                ('t_tekmedik', 9, 'perekam medis dan informasi kesehatan, teknik kardiovaskuler, teknisi pelayanan darah, refraksionis optisien / optometris, teknisi gigi, penata anestesi, terapis gigi dan mulut, dan audiologis (kelompokpegawai_m)'),
                ('t_tekbiomedik', 10, 'radiografer, elektromedis, ahli teknologi laboratorium medik, fisikawan medik, radioterapis, dan ortotik prostetik (kelompokpegawai_m)'),
                ('t_kestradisional', 11, 'tenaga kesehatan tradisional ramuan dan tenaga kesehatan tradisional keterampilan (kelompokpegawai_m)'),
                ('t_gizi', 12, 'nutrisionis dan dietisien (kelompokpegawai_m)'),
                ('asal_rujukan', 1, 'default asal rujukan'),
                ('kasus_penyakit', 23, 'default kasus penyakit'),
                ('MCU', 21, 'instalasi MCU'),
                ('visite', 32, 'kelompok tindakan visite dokter'),
                ('penjamin_umum', 1, 'Penjamin perorangan'),
                ('jasa_dokter', 11, 'komponen Jasa Dokter');");
                
                 $this->execute('COMMENT ON COLUMN public.pembayaran_t.total_sisatagihan IS \'nominal Pemberian piutang\';');

                 $this->execute('DROP VIEW if exists public.infokunjunganrs_v;');

                 $this->execute("
                    CREATE OR REPLACE VIEW public.infokunjunganrs_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.pembayaranpelayanan_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama AS carakeluar,
    kondisikeluar_m.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    pendaftaran_t.status_periksa::integer AS status_periksa_id,
    pendaftaran_t.pasienpulang_id AS pulang_rj_rd,
    NULL::integer AS pulang_ri,
    NULL::integer AS pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pendaftaran_t.bpjs_id,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan_nama,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama,
    bpjs_t.nosep,
    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
    fgetnamalookup(pendaftaran_t.status_konfirmasi::integer) AS status_konfirmasirm
   FROM pendaftaran_t
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
  WHERE pendaftaran_t.instalasi_id <> 3
UNION ALL
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    pendaftaran_t.rujukan_id,
    pasienadmisi_t.pasienpulang_id,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pendaftaran_t.pembayaranpelayanan_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama AS carakeluar,
    kondisikeluar_m.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    NULL::integer AS pulang_rj_rd,
    pasienadmisi_t.pasienpulang_id AS pulang_ri,
    pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pasienadmisi_t.bpjs_id,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan_nama,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama,
    bpjs_t.nosep,
    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
    fgetnamalookup(pendaftaran_t.status_konfirmasi::integer) AS status_konfirmasirm
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id;
");

                 $this->execute('ALTER TABLE public.infokunjunganrs_v
  OWNER TO postgres;');

                 $this->execute('DROP VIEW if exists public.inforiwayatresep_v;');

                 $this->execute("
                    CREATE OR REPLACE VIEW public.inforiwayatresep_v AS 
 SELECT obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    obatalkespasien_t.tglpelayanan::date AS tgl_transaksi,
    penjualanresep_t.noresep AS no_resep,
    signaobat_m.signa_id,
    signaobat_m.signa_nama,
    obatalkespasien_t.qty_oa AS qty,
    satuan_kecil.satuanunit_id,
    satuan_kecil.satuanunit_nama,
    instalasi_m.instalasi_nama,
    ruangan_tujuan.ruangan_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    fgetnamalookup(penjualanresep_t.status_reseptur::integer) AS status_resep
   FROM obatalkespasien_t
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.reseptur_id IS NULL
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ruangan_m ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true AND penjualanresep_t.status_reseptur = '660'::smallint;
");
                 $this->execute('ALTER TABLE public.inforiwayatresep_v
  OWNER TO postgres;');

                 $this->execute('DROP VIEW if exists public.rincianpasiendetail_v;');

                 $this->execute("
                    CREATE OR REPLACE VIEW public.rincianpasiendetail_v AS 
 SELECT 'tindakan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.instalasi_id AS instalasi_pelayanan_id,
    instalasi_pelayanan.instalasi_nama AS instalasi_pelayanan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    ruangan_pelayanan.ruangan_nama AS ruangan_pelayanan,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    false AS is_obat,
    tindakanpelayanan_t.tindakansudahbayar_id,
    daftartindakan_m.is_akomodasi,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN instalasi_m instalasi_pelayanan ON tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN ruangan_m ruangan_pelayanan ON tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
UNION ALL
 SELECT 'obat'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    NULL::integer AS instalasi_pelayanan_id,
    NULL::character varying AS instalasi_pelayanan,
    NULL::integer AS ruangan_pelayanan_id,
    NULL::character varying AS ruangan_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS pemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama,
    NULL::character varying AS pemeriksaanrad_nama,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    true AS is_obat,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    false AS is_akomodasi,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id;");

                 $this->execute('ALTER TABLE public.rincianpasiendetail_v
  OWNER TO postgres;');

                 $this->execute('DROP VIEW if exists public.infoclosingkasir_v;');

                 $this->execute("
                    CREATE OR REPLACE VIEW public.infoclosingkasir_v AS 
 SELECT 'pembayaran'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pembayaran_t.total_tagihan,
    pembayaran_t.total_dibayar - pembayaran_t.total_kembalian AS total_tunai,
    pembayaran_t.total_nontunai,
    pembayaran_t.total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar
UNION ALL
 SELECT 'uang_muka'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    0 AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.bayaruangmuka_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.bayaruangmuka_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.tglbuktibayar, tandabuktibayar_t.uangditerima, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
UNION ALL
 SELECT 'resep_bebas'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    pasien_m.pasien_id,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    0 AS total_tunai,
    0 AS total_nontunai,
    0 AS total_dijamin
   FROM closingkasir_t
     JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
            tandabuktibayar_t_1.pembayaranpelayanan_id,
            tandabuktibayar_t_1.uangditerima,
            tandabuktibayar_t_1.tglbuktibayar
           FROM tandabuktibayar_t tandabuktibayar_t_1
          GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, penjualanresep_t.penjualanresep_id, penjualanresep_t.noresep, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama;
");
 
                 $this->execute('ALTER TABLE public.infoclosingkasir_v
  OWNER TO postgres;');

                 $this->execute('DROP VIEW if exists public.infopasiensudahbayar_v;');

                 $this->execute("
                    CREATE OR REPLACE VIEW public.infopasiensudahbayar_v AS 
 SELECT pendaftaran.pendaftaran_id,
    pendaftaran.pembayaranpelayanan_id,
    pendaftaran.tgl_pembayaran,
    pendaftaran.no_pembayaran,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_id
            ELSE ruang_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_nama
            ELSE ruang_ri.ruangan_nama
        END AS ruangan_nama,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    pendaftaran.no_rekam_medik,
        CASE
            WHEN pendaftaran.nama_pasien IS NULL THEN pendaftaran.nama_pembeli
            ELSE pendaftaran.nama_pasien
        END AS nama_pasien,
    pendaftaran.tanggal_lahir,
    pendaftaran.umur,
    pendaftaran.jeniskelamin,
    pendaftaran.penjamin_id,
    pendaftaran.penjamin_nama,
    pendaftaran.carabayar_id,
    pendaftaran.carabayar_nama,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_id
            ELSE ruang_ri.instalasi_id
        END AS instalasi_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
    pendaftaran.status_bayar,
    pendaftaran.closingkasir_id,
    pendaftaran.total_tagihan::integer AS total_tagihan,
    pendaftaran.total_uang_muka::integer AS total_uang_muka,
    pendaftaran.subsidi_asuransi::integer AS subsidi_asuransi,
    pendaftaran.total_sudah_dibayarkan::integer AS total_sudah_dibayarkan,
    pendaftaran.total_sisa_tagihan::integer AS total_sisa_tagihan,
    pendaftaran.biaya_administrasi::integer AS biaya_administrasi,
    pendaftaran.pembulatan,
    pendaftaran.jeniskasuspenyakit_nama,
    pendaftaran.pegawai_rd_rj,
    pendaftaran.kelaspelayanan_id,
    pendaftaran.kelaspelayanan_nama,
    pendaftaran.penjualanresep_id,
    pendaftaran.pembayaran_id,
    pendaftaran.total_ditagihkan
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_pendaftaran.kelaspelayanan_id
                    ELSE kelas_admisi.kelaspelayanan_id
                END AS kelaspelayanan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_pendaftaran.kelaspelayanan_nama
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            NULL::integer AS penjualanresep_id,
            NULL::character varying AS nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan + pembayaran_t.total_dijamin AS total_ditagihkan
           FROM pembayaranpelayanan_t
             JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t pasienadmisi_t_1 ON pembayaranpelayanan_t.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
             LEFT JOIN kelaspelayanan_m kelas_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id
             LEFT JOIN kelaspelayanan_m kelas_admisi ON pasienadmisi_t_1.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
             LEFT JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            NULL::character varying AS umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer) AS status_bayar,
            NULL::integer AS instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            NULL::character varying AS jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan + pembayaran_t.total_dijamin AS total_ditagihkan
           FROM pembayaranpelayanan_t
             JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id
             LEFT JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false) pendaftaran
     LEFT JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
     LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
     LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id;");

                 $this->execute('ALTER TABLE public.infopasiensudahbayar_v
  OWNER TO postgres;');

                 $this->execute('DROP VIEW if exists public.inforesep_v;');

                 $this->execute("
                    CREATE OR REPLACE VIEW public.inforesep_v AS 
 SELECT 'reseptur'::text AS jenis,
    reseptur_t.reseptur_id,
    reseptur_t.penjualanresep_id AS resep_id,
    reseptur_t.pasien_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasienadmisi_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama,
    reseptur_t.ruangan_id,
    reseptur_t.ruanganreseptur_id,
    reseptur_t.tglreseptur,
    penjualanresep_t.tglresep,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    reseptur_t.noresep AS nomor,
    penjualanresep_t.penjualanresep_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    ruangan_tujuan.ruangan_nama AS ruangan_reseptur,
        CASE
            WHEN penjualanresep_t.penjualanresep_id IS NULL THEN 'Belum Proses'::character varying
            WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
            ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
        END AS status_reseptur,
    reseptur_t.pegawai_id,
    pegawai_m.nama_pegawai,
    ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
    instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
    ruangan_tujuan.instalasi_id AS instalasi_resep_id,
    instalasi_tujuan.instalasi_nama AS instalasi_resep,
    antrian_t.no_antrian,
    reseptur_t.status_reseptur AS status_reseptur_id,
    reseptur_t.is_hamil,
    reseptur_t.berat_badan,
    reseptur_t.tinggi_badan,
    reseptur_t.luas_tubuh,
    reseptur_t.diagnosa_id,
    concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
    reseptur_t.instruksi_id,
    reseptur_t.antrian_id,
    penjualanresep_t.catatan,
    resepturdetail_t.iter,
    penjualanresep_t.noresep AS noresep_penjualan,
    resepturdetail_t.iter AS iter_penjualan,
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN anamnesa_t.riwayat_alergiobat::character varying
            WHEN ruangan_reseptur.instalasi_id = 2 THEN asesmenperawatrd_t.alergi_obat::character varying
            ELSE asesmenawal_t.nama_alergi
        END AS riwayat_alergi,
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 2 THEN cppt_rd.diagnosa_utama
            WHEN ruangan_reseptur.instalasi_id = 3 THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END AS diagnosa_text,
    string_agg(resepturdetail_t.racikan_id::text, '-'::text) AS antrian_racikan,
    sum(obatalkes_m.harganetto) AS total_harganetto,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.totalhargajual,
    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihan,
    NULL::character varying AS nama_pembeli,
    penjualanresep_t.status_bayar,
    antrian_t.tgl_antrian,
    COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
    COALESCE(penjualanresep_t.nama_pembeli, pasien_m.nama_pasien) AS nama
   FROM reseptur_t
     LEFT JOIN penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.is_deleted = false
     JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ruangan_m ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
     JOIN instalasi_m instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
     JOIN resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id AND resepturdetail_t.is_deleted = false
     JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
     LEFT JOIN diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
     LEFT JOIN anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
     LEFT JOIN asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
     LEFT JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false AND pasienmorbiditas_t.kelompokdiagnosa_id = 2
     LEFT JOIN ( SELECT instruksi_t.instruksi_id,
            cppt_t.cppt_id,
            cppt_t.pendaftaran_id,
            cppt_t.a_diag_utama ->> 'text'::text AS diagnosa_utama
           FROM instruksi_t
             JOIN cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id AND cppt_t.is_deleted = false AND cppt_t.is_active = true
          WHERE instruksi_t.is_deleted = false AND instruksi_t.is_active = true) cppt_rd ON pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id AND reseptur_t.instruksi_id = cppt_rd.instruksi_id
  WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true
  GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup(pasien_m.jeniskelamin::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep, penjualanresep_t.tglresep, penjualanresep_t.penjualanresep_id, (
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN anamnesa_t.riwayat_alergiobat::character varying
            WHEN ruangan_reseptur.instalasi_id = 2 THEN asesmenperawatrd_t.alergi_obat::character varying
            ELSE asesmenawal_t.nama_alergi
        END), (
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 2 THEN cppt_rd.diagnosa_utama
            WHEN ruangan_reseptur.instalasi_id = 3 THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END), (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama)), antrian_t.tgl_antrian
UNION ALL
 SELECT 'resep'::text AS jenis,
    NULL::integer AS reseptur_id,
    penjualanresep_t.penjualanresep_id AS resep_id,
    penjualanresep_t.pasien_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.carabayar_id,
    penjualanresep_t.penjamin_id,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjualanresep_t.ruangan_id,
    NULL::integer AS ruanganreseptur_id,
    NULL::date AS tglreseptur,
    penjualanresep_t.tglresep,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    penjualanresep_t.noresep AS nomor,
    penjualanresep_t.penjualanresep_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_resep.ruangan_nama AS ruangan_tujuan,
    NULL::text AS ruangan_reseptur,
        CASE
            WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
            ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
        END AS status_reseptur,
    penjualanresep_t.pegawai_id,
    pegawai_m.nama_pegawai,
    NULL::integer AS instalasi_reseptur_id,
    NULL::character varying AS instalasi_reseptur,
    ruangan_resep.instalasi_id AS instalasi_resep_id,
    instalasi_resep.instalasi_nama AS instalasi_resep,
    antrian_t.no_antrian,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    NULL::boolean AS is_hamil,
    NULL::integer AS berat_badan,
    NULL::integer AS tinggi_badan,
    NULL::character varying AS luas_tubuh,
    NULL::integer AS diagnosa_id,
    NULL::text AS diagnosa_nama,
    NULL::integer AS instruksi_id,
    NULL::integer AS antrian_id,
    penjualanresep_t.catatan,
    penjualanresep_t.iter,
    penjualanresep_t.noresep AS noresep_penjualan,
    NULL::integer AS iter_penjualan,
    NULL::text AS riwayat_alergi,
    NULL::text AS diagnosa_text,
    NULL::text AS antrian_racikan,
    NULL::double precision AS total_harganetto,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.totalhargajual,
    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihan,
    penjualanresep_t.nama_pembeli,
    penjualanresep_t.status_bayar,
    antrian_t.tgl_antrian,
    penjualanresep_t.tglresep AS tgl_resep_dibuat,
    COALESCE(penjualanresep_t.nama_pembeli, pasien_m.nama_pasien) AS nama
   FROM penjualanresep_t
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ruangan_resep ON penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id
     JOIN instalasi_m instalasi_resep ON ruangan_resep.instalasi_id = instalasi_resep.instalasi_id
     LEFT JOIN kelaspelayanan_m ON penjualanresep_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN antrian_t ON penjualanresep_t.antrian_id = antrian_t.antrian_id
  WHERE penjualanresep_t.reseptur_id IS NULL;");

                 $this->execute('ALTER TABLE public.inforesep_v
  OWNER TO postgres;');

                 $this->execute('GRANT ALL ON TABLE public.inforesep_v TO postgres;');
                 

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200301_153512_migrate_20200301 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200301_153512_migrate_20200301 cannot be reverted.\n";

        return false;
    }
    */
}
