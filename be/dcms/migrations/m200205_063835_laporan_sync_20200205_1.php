<?php

use yii\db\Migration;

/**
 * Class m200205_063835_laporan_sync_20200205_1
 */
class m200205_063835_laporan_sync_20200205_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanclosingkasir_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporanclosingkasir_v AS 
 SELECT closingkasir_t.closingkasir_id,
    shift_m.shift_id,
    shift_m.shift_nama,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai,
    setorbank_t.setorbank_id,
    closingkasir_t.no_closingkasir AS no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.closing_dari,
    closingkasir_t.sampai_dengan,
    closingkasir_t.closing_saldoawal,
    closingkasir_t.terima_uangmuka,
    closingkasir_t.terima_uangpelayanan,
    closingkasir_t.total_pengeluaran,
    closingkasir_t.total_setoran,
    closingkasir_t.keterangan_closing,
    closingkasir_t.jumlah_uanglogam,
    closingkasir_t.jumlah_uangkertas,
    closingkasir_t.jumlah_transaksi,
    closingkasir_t.piutang,
    closingkasir_t.nilai_closingtransaksi,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.no_closingkasir
   FROM closingkasir_t
     JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
     JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
  WHERE closingkasir_t.is_active = true AND closingkasir_t.is_deleted = false;
");
        
        $this->execute('ALTER TABLE public.laporanclosingkasir_v
  OWNER TO postgres;');
        
        $this->execute('DROP VIEW if exists public.laporanpaisenigd_v;');

        $this->execute('DROP VIEW if exists public.laporanpasienigd_v;');
        
        $this->execute("
            CREATE OR REPLACE VIEW public.laporanpasienigd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.pegawai_id AS dokter_jaga_id,
    dokter_jaga.nama_pegawai AS dokter_jaga,
        CASE
            WHEN (( SELECT count(dokpj.pendaftaran_id) AS count
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false)) > 1 THEN ( SELECT dokpj.dokterbaru_id
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE pendaftaran_t.pegawai_id
        END AS dokter_id,
        CASE
            WHEN (( SELECT count(dokpj.pendaftaran_id) AS count
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false)) > 1 THEN ( SELECT dokter.nama_pegawai
               FROM gantidokterpj_t dokpj
                 JOIN pegawai_m dokter ON dokpj.dokterbaru_id = dokter.pegawai_id AND dokter.kelompokpegawai_id = 1 AND dokter.is_active = true AND dokter.is_deleted = false
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE dokter_jaga.nama_pegawai
        END AS dokter,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    pasien_m.jeniskelamin
   FROM pendaftaran_t
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m dokter_jaga ON pendaftaran_t.pegawai_id = dokter_jaga.pegawai_id
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
     LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id
  WHERE pendaftaran_t.instalasi_id = 2;");
        
        $this->execute('ALTER TABLE public.laporanpasienigd_v
  OWNER TO postgres;');
        
        $this->execute('DROP VIEW if exists public.laporankunjunganri_v;');
        
        $this->execute("
            CREATE OR REPLACE VIEW public.laporankunjunganri_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
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
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.nama_pegawai,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.pasienpulang_id,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_namalain,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
    fgetnamalookup(pendaftaran_t.status_masuk::integer) AS status_masuk,
    pasien_m.jeniskelamin,
    pasienbatalperiksa_t.alasan_batal,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama,
    golonganumur_m.golonganumur_nama,
    carakeluar_m.carakeluar_nama,
    pasienadmisi_t.kamartempattidur_id,
    bpjs_t.nosep,
    bpjs_t.bpjs_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pasienadmisi_t ON pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.laporankunjunganri_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.sie_carabayarklaimpulang;');

        $this->execute("
            CREATE OR REPLACE VIEW public.sie_carabayarklaimpulang AS 
 SELECT 'RJ/RD'::text AS pengunjung,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE
            WHEN pendaftaran_t.status_verifikasi = 551 THEN 'Sudah Klaim'::text
            ELSE 'Belum Klaim'::text
        END AS status_klaim,
    pendaftaran_t.status_verifikasi,
    fgetnamalookup(pendaftaran_t.status_verifikasi) AS verifikasi
   FROM pendaftaran_t
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])
UNION ALL
 SELECT 'RI'::text AS pengunjung,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_pendaftaran,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE
            WHEN pasienadmisi_t.status_verifikasi = 551 THEN 'Sudah Klaim'::text
            ELSE 'Belum Klaim'::text
        END AS status_klaim,
    pasienadmisi_t.status_verifikasi,
    fgetnamalookup(pasienadmisi_t.status_verifikasi) AS verifikasi
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id;");

        $this->execute('ALTER TABLE public.sie_carabayarklaimpulang
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.sie_indikatorrs;');

        $this->execute("
            CREATE OR REPLACE VIEW public.sie_indikatorrs AS 
 SELECT x.tahun,
    round(sum(x.bor) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS bor,
    round(sum(x.avlos) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS avlos,
    round(sum(x.toi) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS toi,
    round(sum(x.bto) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS bto
   FROM ( SELECT to_char(indikatorrs_r.created_date, 'YYYY-MM-DD'::text)::date AS created_date,
            to_char(indikatorrs_r.created_date, 'MM'::text) AS bulan,
            to_char(indikatorrs_r.created_date, 'YYYY'::text) AS tahun,
            round(indikatorrs_r.hari_perawatan::numeric * 1::numeric / (indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric), 2) AS bor,
            round(indikatorrs_r.lama_dirawat::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS avlos,
            round((indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric - indikatorrs_r.hari_perawatan::numeric) / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS toi,
            round(indikatorrs_r.jumlah_pasien_keluar::numeric / indikatorrs_r.jumlah_tempat_tidur::numeric, 2) AS bto,
            round(indikatorrs_r.pasien_mati_48_jam::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS ndr,
            round(indikatorrs_r.pasien_mati_all::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS gdr
           FROM indikatorrs_r) x
  WHERE to_char(x.created_date::timestamp with time zone, 'YYYY'::text) = date_part('year'::text, CURRENT_DATE)::text
  GROUP BY x.tahun;
");
        $this->execute('ALTER TABLE public.sie_indikatorrs
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.sie_monitoringbpjspersen;');

        $this->execute("
            CREATE OR REPLACE VIEW public.sie_monitoringbpjspersen AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienpulang_t.tglpasienpulang,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelaspelayanan_m.urutankelas,
    pasien_m.no_rekam_medik,
    bpjs_t.nosep,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    bpjs_t.klsrawat AS hak_kelas,
    COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
    (COALESCE(tagihan.sub_total, 0::double precision) * 100::double precision /
        CASE COALESCE(monitorsetdiagnosa.total, 1::double precision)
            WHEN 0 THEN 1::double precision
            ELSE COALESCE(monitorsetdiagnosa.total, 1::double precision)
        END)::numeric(15,2) AS persen,
        CASE
            WHEN (COALESCE(tagihan.sub_total, 0::double precision) * 100::double precision /
            CASE COALESCE(monitorsetdiagnosa.total, 1::double precision)
                WHEN 0 THEN 1::double precision
                ELSE COALESCE(monitorsetdiagnosa.total, 1::double precision)
            END)::numeric(15,2) >= 100::numeric THEN 'merah'::text
            WHEN (COALESCE(tagihan.sub_total, 0::double precision) * 100::double precision /
            CASE COALESCE(monitorsetdiagnosa.total, 1::double precision)
                WHEN 0 THEN 1::double precision
                ELSE COALESCE(monitorsetdiagnosa.total, 1::double precision)
            END)::numeric(15,2) < 75::numeric THEN 'hijau'::text
            ELSE 'kuning'::text
        END AS warna,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id AND carabayar_m.carabayar_id = 6
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM monitorsetdiagnosa_t
             JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
          WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id;
");

        $this->execute('ALTER TABLE public.sie_monitoringbpjspersen
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_063835_laporan_sync_20200205_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_063835_laporan_sync_20200205_1 cannot be reverted.\n";

        return false;
    }
    */
}
