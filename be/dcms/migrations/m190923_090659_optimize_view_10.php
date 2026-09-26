<?php

use yii\db\Migration;

/**
 * Class m190923_090659_optimize_view_10
 */
class m190923_090659_optimize_view_10 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    /*infopemakaianambulandetail_v*/
$this->execute('DROP VIEW if exists public.infopemakaianambulandetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopemakaianambulandetail_v AS 
 SELECT pemakaianambulan_t.pemakaianambulan_id,
    pesanambulan_t.tgl_pesanambulan,
    pemakaianambulan_t.tgl_pemakaiandari,
    pemakaianambulan_t.tgl_pemakaiansampai,
    pemakaianambulan_t.durasi_pemakaian,
    pemakaianambulan_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.pemesan
            ELSE pasien_m.nama_pasien
        END AS nama_pemesan,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN fgetnamalookup(pesanambulan_t.jenis_kelamin::integer)
            ELSE fgetnamalookup(pasien_m.jeniskelamin::integer)
        END AS jns_kelamin,
        CASE
            WHEN ambulan_m.is_emergency = true THEN 'Gawat Darurat'::text
            ELSE 'Bukan Gawat Darurat'::text
        END AS jenis_ambulan,
    pesanambulan_t.asal_pasien,
    pesanambulan_t.keluhan,
    fgetnamalookup(pemakaianambulan_t.pelayanan_ambulan) AS pelayanan,
    pemakaianambulan_t.km_awal,
    petugas.nama_pegawai,
    petugas.nomorindukpegawai,
    jabatan_m.jabatan_nama,
    obatalkes_m.obatalkes_nama,
    pemakaianambulandetail_t.qty
   FROM pemakaianambulan_t
     JOIN pesanambulan_t ON pemakaianambulan_t.pemakaianambulan_id = pesanambulan_t.pemakaianambulan_id
     JOIN pemakaianambulandetail_t ON pemakaianambulan_t.pemakaianambulan_id = pemakaianambulandetail_t.pemakaianambulan_id
     LEFT JOIN pasien_m ON pesanambulan_t.pasien_id = pasien_m.pasien_id
     JOIN ambulan_m ON pesanambulan_t.ambulan_id = ambulan_m.ambulan_id
     LEFT JOIN pegawai_m petugas ON pemakaianambulandetail_t.petugas_id = petugas.pegawai_id
     LEFT JOIN jabatan_m ON petugas.jabatan_id = jabatan_m.jabatan_id
     LEFT JOIN obatalkes_m ON pemakaianambulandetail_t.obatalkes_id = obatalkes_m.obatalkes_id;");

$this->execute('ALTER TABLE public.infopemakaianambulandetail_v
  OWNER TO postgres;');

/*infopemesananbarang_v*/
$this->execute('DROP VIEW if exists public.infopemesananbarang_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopemesananbarang_v AS 
 SELECT pesanbarang_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    instalasipemesan.instalasi_id AS instalasipemesan_id,
    instalasipemesan.instalasi_nama AS instalasi_pemesan,
    ruanganpemesan.ruangan_id AS ruanganpemesan_id,
    ruanganpemesan.ruangan_nama AS ruangan_pemesan,
    instalasitujuan.instalasi_id,
    instalasitujuan.instalasi_nama AS instalasi_tujuan,
    ruangantujuan.ruangan_id,
    ruangantujuan.ruangan_nama AS ruangan_tujuan,
    pesanbarang_t.no_pemesanan,
    fgetnamalookup(pesanbarang_t.statuspesan::integer) AS status_pesan,
    pesanbarang_t.tgl_mintadikirim,
    pesanbarang_t.keterangan_pesan,
    pesanbarang_t.statuspesan,
    pesanbarang_t.is_deleted
   FROM pesanbarang_t
     JOIN ruangan_m ruangantujuan ON pesanbarang_t.ruangantujuan_id = ruangantujuan.ruangan_id
     JOIN instalasi_m instalasitujuan ON ruangantujuan.instalasi_id = instalasitujuan.instalasi_id
     JOIN ruangan_m ruanganpemesan ON pesanbarang_t.ruanganpemesan_id = ruanganpemesan.ruangan_id
     JOIN instalasi_m instalasipemesan ON ruanganpemesan.instalasi_id = instalasipemesan.instalasi_id
  WHERE pesanbarang_t.is_active = true;");

$this->execute('ALTER TABLE public.infopemesananbarang_v
  OWNER TO postgres;');

/*pasienrsambulan_v*/
$this->execute('DROP VIEW if exists public.pasienrsambulan_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.pasienrsambulan_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_id
            ELSE r_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_nama
            ELSE r_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_pendaftaran.instalasi_id
            ELSE i_admisi.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_pendaftaran.instalasi_nama
            ELSE i_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN pendaftaran_t.instalasi_id = 2 THEN cppt_t.a_diag_utama ->> 'text'::text
            ELSE
            CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
                WHEN 0 THEN cppt_t.a_diag_utama ->> 'text'::text
                ELSE resumemedisri_t.diag_utama ->> 'text'::text
            END
        END AS diagnosa,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_pendaftaran.carabayar_id
            ELSE cb_admisi.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_pendaftaran.carabayar_nama
            ELSE cb_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_pendaftaran.penjamin_nama
            ELSE pj_admisi.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kls_pendaftaran.kelaspelayanan_nama
            ELSE kls_pendaftaran.kelaspelayanan_nama
        END AS kelaspelayanan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN instalasi_m i_pendaftaran ON r_pendaftaran.instalasi_id = i_pendaftaran.instalasi_id
     LEFT JOIN instalasi_m i_admisi ON r_admisi.instalasi_id = i_admisi.instalasi_id
     LEFT JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false AND pasienmorbiditas_t.kelompokdiagnosa_id = 2
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
     LEFT JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pasien_id,
            max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
           FROM pendaftaran_t pendaftaran_t_1
          GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_t1 ON pasien_m.pasien_id = pendaftaran_t1.pasien_id
     LEFT JOIN carabayar_m cb_pendaftaran ON pendaftaran_t.carabayar_id = cb_pendaftaran.carabayar_id
     LEFT JOIN carabayar_m cb_admisi ON pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id
     LEFT JOIN penjamin_m pj_pendaftaran ON pendaftaran_t.penjamin_id = pj_pendaftaran.penjamin_id
     LEFT JOIN penjamin_m pj_admisi ON pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id
     LEFT JOIN kelaspelayanan_m kls_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kls_pendaftaran.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_admisi ON pasienadmisi_t.kelaspelayanan_id = kls_admisi.kelaspelayanan_id;
");

$this->execute('ALTER TABLE public.pasienrsambulan_v
  OWNER TO postgres;');

/*infopemesanankamar_v*/
$this->execute('DROP VIEW if exists public.infopemesanankamar_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopemesanankamar_v AS 
 SELECT bookingkamar_t.bookingkamar_id,
    bookingkamar_t.tgl_transaksibooking AS tgl_transaksi,
    bookingkamar_t.tgl_bookingkamar AS tgl_pesan,
    bookingkamar_t.bookingkamar_no AS no_pemesanan,
    bookingkamar_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    bookingkamar_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    bookingkamar_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    kamartempattidur_m.kettempattidur_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    bookingkamar_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    fgetnamalookup(bookingkamar_t.status_booking::integer) AS status_booking,
    bookingkamar_t.pendaftaran_id,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    bookingkamar_t.nama_pemesan,
    bookingkamar_t.status_booking AS statusbooking,
    bookingkamar_t.jeniskasuspenyakit_id,
    kamarruangan_m.kamarruangan_jenis,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS jenis_kamar,
    bookingkamar_t.keterangan_booking,
    bookingkamar_t.additional_data::json ->> 'jeniskelamin'::text AS jk,
    bookingkamar_t.tgl_expired,
    bookingkamar_t.additional_data,
    bookingkamar_t.additional_data::json ->> 'nama_pasien'::text AS namapasien_additional,
    bookingkamar_t.additional_data::json ->> 'no_telepon_pasien'::text AS notlp_additional
   FROM bookingkamar_t
     LEFT JOIN pasien_m ON bookingkamar_t.pasien_id = pasien_m.pasien_id
     JOIN kamarruangan_m ON bookingkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON bookingkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pendaftaran_t ON bookingkamar_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN kamartempattidur_m ON bookingkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
  WHERE bookingkamar_t.is_active = true AND bookingkamar_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infopemesanankamar_v
  OWNER TO postgres;
');

/*infopemesananobatalkes_v*/
$this->execute('DROP VIEW if exists public.infopemesananobatalkes_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopemesananobatalkes_v AS 
 SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatalkes_t.mutasiobatruangan_id,
    pesanobatalkes_t.statuspesan,
    fgetnamalookup(pesanobatalkes_t.statuspesan::integer) AS status_pengiriman,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatalkes_t.keterangan_pesan
   FROM pesanobatalkes_t
     JOIN ruangan_m ON pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasiobatruangan_t ON pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
  WHERE pesanobatalkes_t.is_active = true AND pesanobatalkes_t.is_deleted = false;
");

$this->execute('ALTER TABLE public.infopemesananobatalkes_v
  OWNER TO postgres;
');

/*infostokopnamedetail_v*/
$this->execute('DROP VIEW if exists public.infostokopnamedetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infostokopnamedetail_v AS 
 SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.stokopname_id,
    stokopname_t.tglstokopname,
    stokopname_t.nostokopname,
    stokopnamedetail_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    stokopnamedetail_t.tglkadaluarsa,
    stokopnamedetail_t.volume_fisik,
    stokopnamedetail_t.volume_sistem,
    stokopnamedetail_t.volume_fisik * stokopnamedetail_t.harganetto AS harga_netto_fisik,
    stokopnamedetail_t.volume_sistem * stokopnamedetail_t.harganetto AS harga_netto_sistem,
    stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem AS selisih_jumlah,
    stokopnamedetail_t.jmlselisihstok * stokopnamedetail_t.harganetto AS selisih_harganetto,
    petugas1.pegawai_id AS petugas1_id,
    petugas1.nomorindukpegawai AS petugas1_nip,
    petugas1.noidentitas AS petugas1_noidentitas,
    petugas1.gelardepan AS petugas1_gelardepan,
    petugas1.nama_pegawai AS petugas1_nama,
    gelarbelakangpetugas1.gelarbelakang_nama AS petugas1_gelarbelakang,
    petugas2.pegawai_id AS petugas2_id,
    petugas2.nomorindukpegawai AS petugas2_nip,
    petugas2.noidentitas AS petugas2_noidentitas,
    petugas2.gelardepan AS petugas2_gelardepan,
    petugas2.nama_pegawai AS petugas2_nama,
    gelarbelakangpetugas2.gelarbelakang_nama AS petugas2_gelarbelakang,
    pegawaimengetahui.pegawai_id AS pegawaimengetahui_id,
    pegawaimengetahui.nomorindukpegawai AS pegawaimengetahui_nip,
    pegawaimengetahui.noidentitas AS pegawaimengetahui_noidentitas,
    pegawaimengetahui.gelardepan AS pegawaimengetahui_gelardepan,
    pegawaimengetahui.nama_pegawai AS pegawaimengetahui_nama,
    gelarbelakangpegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    obatalkes_m.obatalkes_nama,
    fgetnamalookup(stokopnamedetail_t.kondisibarang::integer) AS kondisibarang_nama,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir
   FROM stokopnamedetail_t
     JOIN stokopname_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN pegawai_m petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN pegawai_m petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
     JOIN formstokopname_r ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_r.stokopnamedetail_id
     LEFT JOIN periodestokobat_m ON formstokopname_r.periodestok_id = periodestokobat_m.periodestokobat_id
  WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infostokopnamedetail_v
  OWNER TO postgres;
');

/*inforesepturdetail_v*/
$this->execute('DROP VIEW if exists public.inforesepturdetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.inforesepturdetail_v AS 
 SELECT resepturdetail_t.resepturdetail_id,
    resepturdetail_t.reseptur_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasien_id,
    resepturdetail_t.obatalkes_id,
    resepturdetail_t.satuankecil_id,
    resepturdetail_t.racikan_id,
    resepturdetail_t.signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    reseptur_t.noresep,
    reseptur_t.tglreseptur,
    racikan_m.racikan_nama,
    resepturdetail_t.r,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama,
    resepturdetail_t.qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
    resepturdetail_t.etiket,
    resepturdetail_t.iter,
    signaobat_m.signa_nama,
    reseptur_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    rotd_t.interaksi,
    rotd_t.duplikasi,
    rotd_t.dosisi,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkespasien_t.obatalkespasien_id,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS margin,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS hn_margin,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
    obatalkes_m.harganetto + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    resepturdetail_t.additional_data::json ->> 'satuaninput_id'::text AS satuaninput_id,
    resepturdetail_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    resepturdetail_t.additional_data::json ->> 'satuankonversi_id'::text AS satuankonversi_id,
    resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    resepturdetail_t.additional_data::json ->> 'harga_konversi'::text AS harga_konversi
   FROM resepturdetail_t
     JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m satuan_kecil ON resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
     JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN rotd_t ON resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id
     LEFT JOIN pegawai_m ON rotd_t.pegawairotd_id = rotd_t.pegawairotd_id
     LEFT JOIN obatalkespasien_t ON resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true;");

$this->execute('ALTER TABLE public.inforesepturdetail_v
  OWNER TO postgres;
');

/*infopengajuanklaim_v*/
$this->execute('DROP VIEW if exists public.infopengajuanklaim_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopengajuanklaim_v AS 
 SELECT pengajuanklaim_t.pengajuanklaim_id,
    pengajuanklaim_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pengajuanklaim_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pengajuanklaim_t.tgl_pengajuanklaim,
    pengajuanklaim_t.no_pengajuanklaim,
    pengajuanklaim_t.tgl_jatuhtempo,
    pengajuanklaim_t.tgl_pelayanansampai,
    pengajuanklaim_t.tgl_pelayanandari,
    pengajuanklaim_t.total_piutang,
    pengajuanklaim_t.total_terbayar,
    pengajuanklaim_t.total_sisapiutang,
    pengajuanklaim_t.alamat_penjamin,
    pengajuanklaim_t.npwp,
    pengajuanklaim_t.totalbiaya_obat,
    pengajuanklaim_t.totalbiaya_tindakan,
    pengajuanklaim_t.pegawaimengetahui_id,
    pengajuanklaim_t.catatan,
    pengajuanklaim_t.status_pengajuanklaim,
    fgetnamalookup(pengajuanklaim_t.status_pengajuanklaim::integer) AS s_pengajuanklaim,
    pengajuanklaim_t.is_deleted,
    pengajuanklaim_t.is_active,
        CASE
            WHEN instalasi_m.instalasi_nama IS NULL THEN 'Semua Instalasi'::character varying
            ELSE instalasi_m.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN ruangan_m.ruangan_nama IS NULL THEN 'Semua Ruangan'::character varying
            ELSE ruangan_m.ruangan_nama
        END AS ruangan_nama
   FROM pengajuanklaim_t
     JOIN carabayar_m ON pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN instalasi_m ON pengajuanklaim_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON pengajuanklaim_t.ruangan_id = ruangan_m.ruangan_id
  WHERE pengajuanklaim_t.is_deleted = false;
");

$this->execute('ALTER TABLE public.infopengajuanklaim_v
  OWNER TO postgres;');

/*infopenjualanresepdetail_v*/
$this->execute('DROP VIEW if exists public.infopenjualanresepdetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopenjualanresepdetail_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.jenispenjualan,
    fgetnamalookup(penjualanresep_t.jenispenjualan::integer) AS jenis_penjualan,
    penjualanresep_t.tglresep,
    penjualanresep_t.noresep,
    penjualanresep_t.totharganetto,
    penjualanresep_t.totalhargajual,
    penjualanresep_t.totaltarifservice,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.biayakonseling,
    penjualanresep_t.pembulatanharga,
    penjualanresep_t.jasadokterresep,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai,
    pegawai_m.gelardepan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjualanresep_t.tglpenjualan,
    penjualanresep_t.discount,
    penjualanresep_t.subsidiasuransi,
    penjualanresep_t.subsidipemerintah,
    penjualanresep_t.subsidirs,
    penjualanresep_t.iurbiaya,
    penjualanresep_t.lamapelayanan,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.reseptur_id,
    pendaftaran_t.pendaftaran_id,
    obatalkes_m.obatalkes_id,
    jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nobatch AS obatalkes_golongan,
    obatalkes_m.obatalkes_kategori,
    obatalkes_m.obatalkes_kadarobat,
    obatalkes_m.kekuatan_obat AS kekuatan,
    obatalkes_m.ppn_persen,
    obatalkespasien_t.racikan_id,
    obatalkespasien_t.shift_id,
    obatalkespasien_t.tglpelayanan,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargasatuan_oa,
    signaobat_m.signa_nama AS signa_oa,
    obatalkespasien_t.harganetto_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.etiket,
    obatalkespasien_t.biayaservice,
    obatalkespasien_t.biayakemasan,
    obatalkespasien_t.oa,
    sumberdana_m.sumberdana_id,
    sumberdana_m.sumberdana_nama,
    satuanunit_m.satuanunit_id AS satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuankecil_nama,
    obatalkespasien_t.tipepaket_id,
    obatsudahbayar_t.obatsudahbayar_id,
    pasien_m.statusperkawinan,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    antrianfarmasi_t.antrianfarmasi_id,
    antrianfarmasi_t.no_antrian,
    antrianfarmasi_t.panggil_antrian,
    antrianfarmasi_t.antrian_lewat,
    antrianfarmasi_t.tglambil_antrian,
    racikan_m.racikan_id AS racikanantrian_id,
    racikan_m.racikan_nama AS racikanantrian_nama,
    racikan_m.racikan_singkatan AS racikanantrian_singkatan,
    racikan_m.tarif_service AS racikanantrian_tarifservice,
    racikan_m.persen_service AS racikanantrian_persenservice,
    racikan_m.biaya_kemasan AS racikanantrian_biayakemasan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    tandabuktibayar_t.tandabuktibayar_id,
    tandabuktibayar_t.tglbuktibayar,
    tandabuktibayar_t.nobuktibayar,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pegawaipasien.nomorindukpegawai AS nomorindukpasien,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.iter,
    penjualanresep_t.nama_pembeli,
    karyawan.nama_pegawai AS nama_karyawan,
    obatalkes_m.obatalkes_nama,
    peg_reseptur.nama_pegawai AS pegawai_reseptur,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.resepturdetail_id
   FROM obatalkespasien_t
     LEFT JOIN pasien_m ON obatalkespasien_t.pasien_id = pasien_m.pasien_id
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN pegawai_m peg_reseptur ON reseptur_t.pegawai_id = peg_reseptur.pegawai_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN sumberdana_m ON obatalkespasien_t.sumberdana_id = sumberdana_m.sumberdana_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN antrianfarmasi_t ON penjualanresep_t.antrianfarmasi_id = antrianfarmasi_t.antrianfarmasi_id
     LEFT JOIN racikan_m ON antrianfarmasi_t.racikan_id = racikan_m.racikan_id
     JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
     LEFT JOIN pegawai_m pegawaipasien ON pasien_m.pegawai_id = pegawaipasien.pegawai_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN signaobat_m ON
        CASE
            WHEN obatalkespasien_t.signa_oa IS NULL OR obatalkespasien_t.signa_oa::text = ''::text THEN '999'::character varying
            ELSE obatalkespasien_t.signa_oa
        END::integer = signaobat_m.signa_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infopenjualanresepdetail_v
  OWNER TO postgres;');

/*infopermintaankonsul_v*/
$this->execute('DROP VIEW if exists public.infopermintaankonsul_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopermintaankonsul_v AS 
 SELECT permintaankonsul_t.waktu_permintaan,
    permintaankonsul_t.waktu_persetujuan,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin::integer AS jenis_kelamin_id,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienadmisi_t.pegawai_id AS dok_dpjp_id,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.kelaspelayanan_id AS kls_rawat_id,
    kls_rawat.kelaspelayanan_nama AS kls_rawat,
    bpjs_t.klsrawat AS kls_hak_id,
    kls_hak.kelaspelayanan_nama AS kls_hak,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    permintaankonsul_t.jenis_konsul,
    permintaankonsul_t.dokter_id,
    dok_konsul.nama_pegawai AS dok_konsul,
    permintaankonsul_t.status_konsul,
    fgetnamalookup(permintaankonsul_t.status_konsul) AS status_konsul_nama,
    permintaankonsul_t.permintaankonsul_id,
    permintaankonsul_t.ket_konsul,
    fgetnamalookup(permintaankonsul_t.jenis_konsul::integer) AS jenis_konsul_nama,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.pendaftaran_id,
    permintaankonsul_t.jawaban_konsul,
    pendaftaran_t.pasien_id,
    permintaankonsul_t.pasienadmisi_id,
    permintaankonsul_t.created_by,
    permintaankonsul_t.created_by AS creator,
    pasienadmisi_t.pasienpulang_id,
    permintaankonsul_t.dokterdpjpasal_id,
    dok_dpjp_asal.nama_pegawai AS dokterdpjpasal_nama
   FROM permintaankonsul_t
     JOIN pendaftaran_t ON permintaankonsul_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN pegawai_m dok_konsul ON permintaankonsul_t.dokter_id = dok_konsul.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m kls_rawat ON pasienadmisi_t.kelaspelayanan_id = kls_rawat.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_hak ON bpjs_t.klsrawat = kls_hak.kelaspelayanan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pegawai_m dok_dpjp_asal ON permintaankonsul_t.dokterdpjpasal_id = dok_dpjp_asal.pegawai_id
  WHERE permintaankonsul_t.is_active = true AND permintaankonsul_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infopermintaankonsul_v
  OWNER TO postgres;
');

/*infopermintaanmakan_v*/
$this->execute('DROP VIEW if exists public.infopermintaanmakan_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopermintaanmakan_v AS 
 SELECT permintaanmakan_t.permintaaanmakan_id,
    permintaanmakan_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    permintaanmakan_t.no_permintaanmakan,
    pegawai_m.nama_pegawai,
    kelaspelayanan_m.kelaspelayanan_nama,
    permintaanmakan_t.tgl_permintaanmakan,
    permintaanmakan_t.status AS status_permintaanmakan,
        CASE
            WHEN permintaanmakan_t.status = 1 THEN 'PROSES'::text
            ELSE 'BATAL'::text
        END AS status_permintaan,
    permintaanmakan_t.no_pembatalan,
    permintaanmakan_t.waktu_pembatalan,
    permintaanmakan_t.alasan_pembatalan,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    ruangan_m.ruangan_id,
    kamarruangan_m.kamarruangan_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    kamartempattidur_m.no_tempattidur,
    pemesan.nama_pegawai AS nama_pemesan
   FROM permintaanmakan_t
     JOIN pendaftaran_t ON permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pegawai_m dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN pegawai_m pemesan ON permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id;
");

$this->execute('ALTER TABLE public.infopermintaanmakan_v
  OWNER TO postgres;
');

/*infopermintaanmakandetail_v*/
$this->execute('DROP VIEW if exists public.infopermintaanmakandetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopermintaanmakandetail_v AS 
 SELECT permintaanmakandetail_t.permintaanmakandetail_id,
    permintaanmakan_t.permintaaanmakan_id,
    permintaanmakan_t.no_permintaanmakan,
    jenisdiet_m.jenisdiet_nama,
    makanandiet_m.makanandiet_nama,
    fgetnamalookup(permintaanmakandetail_t.waktu_diet) AS waktu,
    permintaanmakandetail_t.jumlah,
    permintaanmakandetail_t.keterangan,
    permintaanmakan_t.tgl_permintaanmakan,
    permintaanmakandetail_t.jenisdiet_id,
    permintaanmakandetail_t.makanandiet_id,
    permintaanmakandetail_t.waktu_diet
   FROM permintaanmakan_t
     JOIN permintaanmakandetail_t ON permintaanmakan_t.permintaaanmakan_id = permintaanmakandetail_t.permintaanmakan_id
     JOIN jenisdiet_m ON permintaanmakandetail_t.jenisdiet_id = jenisdiet_m.jenisdiet_id
     JOIN makanandiet_m ON permintaanmakandetail_t.makanandiet_id = makanandiet_m.makanandiet_id;");

$this->execute('ALTER TABLE public.infopermintaanmakandetail_v
  OWNER TO postgres;');

/*infopesanambulan_v*/
$this->execute('DROP VIEW if exists public.infopesanambulan_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopesanambulan_v AS 
 SELECT pesanambulan_t.pesanambulan_id,
    pesanambulan_t.tgl_pesanambulan,
    pesanambulan_t.no_pesanambulan,
    pesanambulan_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.pemesan
            ELSE pasien_m.nama_pasien
        END AS nama_pemesan,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN fgetnamalookup(pesanambulan_t.jenis_kelamin::integer)
            ELSE fgetnamalookup(pasien_m.jeniskelamin::integer)
        END AS jns_kelamin,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.tempat_lahir
            ELSE pasien_m.tempat_lahir
        END AS tempat_lahir,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.tgl_lahir
            ELSE pasien_m.tanggal_lahir
        END AS tgl_lahir,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.umur
            ELSE pendaftaran_t.umur
        END AS umur,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_1.instalasi_nama
            ELSE i_2.instalasi_nama
        END AS instalasi_asal,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_1.ruangan_nama
            ELSE r_2.ruangan_nama
        END AS ruangan_asal,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pesanambulan_t.tujuan_pasien,
        CASE
            WHEN ambulan_m.is_emergency = true THEN 'EMERGENCY'::text
            ELSE 'NON EMERGENCY'::text
        END AS jenis_ambulan,
    pesanambulan_t.asal_pasien,
    pesanambulan_t.kesadaran,
    pesanambulan_t.tanda_vital,
    pesanambulan_t.td_systolic,
    pesanambulan_t.td_diastolic,
    pesanambulan_t.detaknadi,
    pesanambulan_t.respirasi,
    pesanambulan_t.saturasi,
    pesanambulan_t.keluhan,
    fgetnamalookup(ambulan_m.status_ambulan::integer) AS status_ambulan,
    pesanambulan_t.status_pesan,
    fgetnamalookup(pesanambulan_t.status_pesan) AS status_pesanambulan,
        CASE
            WHEN pesanambulan_t.pendaftaran_id IS NULL THEN 'Luar RS'::text
            ELSE 'Pasien RS'::text
        END AS jenis_pasien,
        CASE
            WHEN pesanambulan_t.pendaftaran_id IS NULL THEN 2
            ELSE 1
        END AS jenis_pasien_id,
    ambulan_m.no_polisi,
    pesanambulan_t.is_sadar,
    pesanambulan_t.is_nafas,
    pesanambulan_t.is_nadi,
    pesanambulan_t.nama_pj,
    pesanambulan_t.kontak_pj,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.jenis_kelamin::integer
            ELSE pasien_m.jeniskelamin::integer
        END AS jenis_kelamin,
    pesanambulan_t.ambulan_id,
    pesanambulan_t.created_date
   FROM pesanambulan_t
     LEFT JOIN pendaftaran_t ON pesanambulan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ruangan_m r_1 ON pendaftaran_t.ruangan_id = r_1.ruangan_id
     LEFT JOIN instalasi_m i_1 ON r_1.instalasi_id = i_1.instalasi_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ruangan_m r_2 ON pasienadmisi_t.ruangan_id = r_2.ruangan_id
     LEFT JOIN instalasi_m i_2 ON r_2.instalasi_id = i_2.instalasi_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pasien_m ON pesanambulan_t.pasien_id = pasien_m.pasien_id
     JOIN ambulan_m ON pesanambulan_t.ambulan_id = ambulan_m.ambulan_id;");

$this->execute('ALTER TABLE public.infopesanambulan_v
  OWNER TO postgres;');

/*infopesandokrm_v*/
$this->execute('DROP VIEW if exists public.infopesandokrm_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopesandokrm_v AS 
 SELECT pesandokrm_t.pesandokrm_id,
    pesandokrm_t.ruanganpemesan_id,
    pesandokrm_t.ruangantujuan_id,
    pesandokrm_t.status_pesan,
    pesandokrm_t.no_pesandokrm,
    ruangan_pemesan.ruangan_nama AS ruangan_pemesan,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    pesandokrm_t.tgl_mintakirim,
    pesandokrm_t.tgl_pesandokrm,
    fgetnamalookup(pesandokrm_t.status_pesan) AS status,
    instalasi_pemesan.instalasi_nama AS instalasi_pemesan,
    instalasi_tujuan.instalasi_nama AS instalasi_tujuan,
    ruangan_pemesan.instalasi_id AS instalasi_pemesan_id,
    ruangan_tujuan.instalasi_id AS instalasi_tujuan_id,
    pesandokrm_t.created_by,
    pemesan.nama_pegawai
   FROM pesandokrm_t
     JOIN ruangan_m ruangan_pemesan ON pesandokrm_t.ruanganpemesan_id = ruangan_pemesan.ruangan_id
     JOIN ruangan_m ruangan_tujuan ON pesandokrm_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_pemesan ON ruangan_pemesan.instalasi_id = instalasi_pemesan.instalasi_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN loginpemakai_k ON pesandokrm_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m pemesan ON loginpemakai_k.pegawai_id = pemesan.pegawai_id
  WHERE pesandokrm_t.is_deleted = false AND pesandokrm_t.is_active = true;");

$this->execute('ALTER TABLE public.infopesandokrm_v
  OWNER TO postgres;');

/*infopindahkamar_v*/
$this->execute('DROP VIEW if exists public.infopindahkamar_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopindahkamar_v AS 
 SELECT pindahkamar_t.pindahkamar_id,
    pasienadmisi_t.pasien_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.pegawai_id AS pegawaipendaftaran_id,
    pasienadmisi_t.pegawai_id AS pegawaiadmisi_id,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id AS ruangan_sekarang_id,
    pindahkamar_t.ruangan_id AS ruangan_pindah_id,
    pasienadmisi_t.tgl_admisi,
    pindahkamar_t.tgl_pindahkamar,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_asal.ruangan_nama AS ruangan_sekarang,
    kamar_asal.kamarruangan_nokamar AS kamar_sekarang,
    tempattidur_asal.no_tempattidur AS tempattidur_sekarang,
    ruangan_pindah.ruangan_nama AS ruangan_pindah,
    kamar_pindah.kamarruangan_nokamar AS kamar_pindah,
    tempattidur_pindah.no_tempattidur AS tempattidur_pindah
   FROM pasienadmisi_t
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
     JOIN pindahkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ruangan_asal ON masukkamar_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN kamarruangan_m kamar_asal ON masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id
     JOIN kamartempattidur_m tempattidur_asal ON masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id
     JOIN ruangan_m ruangan_pindah ON pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id
     JOIN kamarruangan_m kamar_pindah ON pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id
     JOIN kamartempattidur_m tempattidur_pindah ON pindahkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id
  WHERE pindahkamar_t.is_active = true AND pindahkamar_t.is_deleted = false;
");

$this->execute('ALTER TABLE public.infopindahkamar_v
  OWNER TO postgres;
');

/*infopo_v*/
$this->execute('DROP VIEW if exists public.infopo_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopo_v AS 
 SELECT
        CASE
            WHEN validasipoobat_t.is_manual = false THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    'obat'::text AS type_po,
    validasipoobat_t.validasipoobat_id AS transaksi_id,
    validasipoobat_t.tgl_validasi AS tanggal_po,
    rekomendasiobat_t.tgl_rekomendasiobat AS tgl_rekomendasi,
    rekomendasiobat_t.no_rekomendasiobat AS nomor,
    validasipoobat_t.no_poobat AS no_transaksi,
    validasipoobat_t.supplier_id,
    supplier_m.supplier_nama,
    validasipoobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    validasipoobat_t.is_validasi,
        CASE
            WHEN validasipoobat_t.is_validasi = false THEN 'Belum Validasi'::text
            ELSE 'Sudah Validasi'::text
        END AS status_validasi,
    validasipoobat_t.status_penerimaan,
    fgetnamalookup(validasipoobat_t.status_penerimaan) AS stat_penerimaan,
    payterm_m.jumlah_hari AS payment_term,
    validasipoobat_t.total AS total_harga_po,
    validasipoobat_t.tgl_rencanaterima,
    validasipoobat_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS peg_mengetahui,
    validasipoobat_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS peg_menyetujui,
    validasipoobat_t.sub_total,
    validasipoobat_t.total_discount,
    validasipoobat_t.ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobat_t.total,
    validasipoobat_t.status_penerimaan AS lookup_id,
    payterm_m.payterm_id,
    validasipoobat_t.diorder_oleh,
    diorder_oleh.nama_pegawai AS diorder_oleh_nama,
    validasipoobat_t.pajak_id,
    validasipoobat_t.catatan1,
    validasipoobat_t.catatan2,
    validasipoobat_t.is_closing
   FROM validasipoobat_t
     JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN rekomendasiobatdetail_t ON validasipoobatdetail_t.rekomendasiobatdetail_id = rekomendasiobatdetail_t.rekomendasiobatdetail_id
     LEFT JOIN rekomendasiobat_t ON rekomendasiobatdetail_t.rekomendasiobat_id = rekomendasiobat_t.rekomendasiobat_id
     JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pegawai_m peg_mengetahui ON validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m diorder_oleh ON validasipoobat_t.diorder_oleh = diorder_oleh.pegawai_id
  WHERE validasipoobat_t.is_deleted = false
UNION ALL
 SELECT
        CASE
            WHEN validasipobarang_t.is_manual = false THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    'barang'::text AS type_po,
    validasipobarang_t.validasipobarang_id AS transaksi_id,
    validasipobarang_t.tgl_validasi AS tanggal_po,
    rekomendasibarang_t.tgl_rekomendasibarang AS tgl_rekomendasi,
    rekomendasibarang_t.no_rekomendasibarang AS nomor,
    validasipobarang_t.no_pobarang AS no_transaksi,
    validasipobarang_t.supplier_id,
    supplier_m.supplier_nama,
    validasipobarang_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    validasipobarang_t.is_validasi,
        CASE
            WHEN validasipobarang_t.is_validasi = false THEN 'Belum Validasi'::text
            ELSE 'Sudah Validasi'::text
        END AS status_validasi,
    validasipobarang_t.status_penerimaan,
    fgetnamalookup(validasipobarang_t.status_penerimaan) AS stat_penerimaan,
    payterm_m.jumlah_hari AS payment_term,
    validasipobarang_t.total AS total_harga_po,
    validasipobarang_t.tgl_rencanaterima,
    validasipobarang_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS peg_mengetahui,
    validasipobarang_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS peg_menyetujui,
    validasipobarang_t.sub_total,
    validasipobarang_t.total_discount,
    validasipobarang_t.ppn_persen,
    validasipobarang_t.ppn_nilai,
    validasipobarang_t.total,
    validasipobarang_t.status_penerimaan AS lookup_id,
    payterm_m.payterm_id,
    validasipobarang_t.diorder_oleh,
    diorder_oleh.nama_pegawai AS diorder_oleh_nama,
    validasipobarang_t.pajak_id,
    validasipobarang_t.catatan1,
    validasipobarang_t.catatan2,
    validasipobarang_t.is_closing
   FROM validasipobarang_t
     JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN rekomendasibarangdetail_t ON validasipobarangdetail_t.rekomendasibarangdetail_id = rekomendasibarangdetail_t.rekomendasibarangdetail_id
     LEFT JOIN rekomendasibarang_t ON rekomendasibarangdetail_t.rekomendasibarang_id = rekomendasibarang_t.rekomendasibarang_id
     JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     JOIN ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pegawai_m peg_mengetahui ON validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m diorder_oleh ON validasipobarang_t.diorder_oleh = diorder_oleh.pegawai_id
  WHERE validasipobarang_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infopo_v
  OWNER TO postgres;');

/*infopodetail_v*/
$this->execute('DROP VIEW if exists public.infopodetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopodetail_v AS 
 SELECT 'obat'::text AS jenis,
        CASE
            WHEN validasipoobat_t.is_manual = false THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    validasipoobat_t.validasipoobat_id AS transaksi_id,
    validasipoobat_t.tgl_validasi AS tanggal_po,
    rekomendasiobat_t.no_rekomendasiobat AS nomor,
    validasipoobat_t.no_poobat AS no_transaksi,
    validasipoobat_t.supplier_id,
    supplier_m.supplier_nama,
    validasipoobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    validasipoobat_t.is_validasi,
        CASE
            WHEN validasipoobat_t.is_validasi = false THEN 'Belum Validasi'::text
            ELSE 'Sudah Validasi'::text
        END AS status_validasi,
    validasipoobat_t.status_penerimaan,
    fgetnamalookup(validasipoobat_t.status_penerimaan) AS stat_penerimaan,
    payterm_m.jumlah_hari AS payment_term,
    validasipoobat_t.total AS total_harga_po,
    validasipoobat_t.tgl_rencanaterima,
    validasipoobat_t.diorder_oleh,
    validasipoobat_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS peg_mengetahui,
    validasipoobat_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS peg_menyetujui,
    validasipoobat_t.sub_total,
    validasipoobat_t.total_discount,
    validasipoobat_t.ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobat_t.total,
    validasipoobatdetail_t.obatalkes_id AS obat_barang_id,
    obatalkes_m.obatalkes_nama AS obat_barang_nama,
    rekomendasiobatdetail_t.rekomendasi AS qty_rekomendasi,
    validasipoobatdetail_t.qty_po AS qty,
    validasipoobatdetail_t.qty_penerimaan,
    validasipoobatdetail_t.s_konversiobt_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuan,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount,
    validasipoobatdetail_t.discount_rp,
    validasipoobatdetail_t.jumlah,
    COALESCE(validasipoobatdetail_t.qty_input, 0) - COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_closing, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
        CASE
            WHEN validasipoobatdetail_t.is_completed = false THEN '-'::text
            ELSE 'Completed'::text
        END AS is_completed,
    validasipoobatdetail_t.qty_input,
    true AS is_obat,
    validasipoobatdetail_t.validasipoobatdetail_id AS id_detail,
    true AS is_kadaluarsa,
    validasipoobat_t.is_verifikasi,
    kecil.satuanunit_nama AS satuan_kecil
   FROM validasipoobat_t
     JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN rekomendasiobatdetail_t ON validasipoobatdetail_t.rekomendasiobatdetail_id = rekomendasiobatdetail_t.rekomendasiobatdetail_id
     LEFT JOIN rekomendasiobat_t ON rekomendasiobatdetail_t.rekomendasiobat_id = rekomendasiobat_t.rekomendasiobat_id
     JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pegawai_m peg_mengetahui ON validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
  WHERE validasipoobat_t.is_deleted = false
UNION ALL
 SELECT 'barang'::text AS jenis,
        CASE
            WHEN validasipobarang_t.is_manual = false THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    validasipobarang_t.validasipobarang_id AS transaksi_id,
    validasipobarang_t.tgl_validasi AS tanggal_po,
    rekomendasibarang_t.no_rekomendasibarang AS nomor,
    validasipobarang_t.no_pobarang AS no_transaksi,
    validasipobarang_t.supplier_id,
    supplier_m.supplier_nama,
    validasipobarang_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    validasipobarang_t.is_validasi,
        CASE
            WHEN validasipobarang_t.is_validasi = false THEN 'Belum Validasi'::text
            ELSE 'Sudah Validasi'::text
        END AS status_validasi,
    validasipobarang_t.status_penerimaan,
    fgetnamalookup(validasipobarang_t.status_penerimaan) AS stat_penerimaan,
    payterm_m.jumlah_hari AS payment_term,
    validasipobarang_t.total AS total_harga_po,
    validasipobarang_t.tgl_rencanaterima,
    validasipobarang_t.diorder_oleh,
    validasipobarang_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS peg_mengetahui,
    validasipobarang_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS peg_menyetujui,
    validasipobarang_t.sub_total,
    validasipobarang_t.total_discount,
    validasipobarang_t.ppn_persen,
    validasipobarang_t.ppn_nilai,
    validasipobarang_t.total,
    validasipobarangdetail_t.barang_id AS obat_barang_id,
    barang_m.barang_nama AS obat_barang_nama,
    rekomendasibarangdetail_t.rekomendasi AS qty_rekomendasi,
    validasipobarangdetail_t.qty_po AS qty,
    validasipobarangdetail_t.qty_penerimaan,
    validasipobarangdetail_t.s_konversibrg_id AS s_konversiobt_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversibrg_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuan,
    validasipobarangdetail_t.harga,
    validasipobarangdetail_t.discount,
    validasipobarangdetail_t.discount_rp,
    validasipobarangdetail_t.jumlah,
    COALESCE(validasipobarangdetail_t.qty_input, 0) - COALESCE(validasipobarangdetail_t.qty_penerimaan, 0) - COALESCE(validasipobarangdetail_t.qty_closing, 0) + COALESCE(validasipobarangdetail_t.qty_retur, 0) AS po_balance,
        CASE
            WHEN validasipobarangdetail_t.is_completed = false THEN '-'::text
            ELSE 'Completed'::text
        END AS is_completed,
    validasipobarangdetail_t.qty_input,
    false AS is_obat,
    validasipobarangdetail_t.validasipobarangdetail_id AS id_detail,
    barang_m.is_kadaluarsa,
    validasipobarang_t.is_verifikasi,
    kecil.satuanunit_nama AS satuan_kecil
   FROM validasipobarang_t
     JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN rekomendasibarangdetail_t ON validasipobarangdetail_t.rekomendasibarangdetail_id = rekomendasibarangdetail_t.rekomendasibarangdetail_id
     LEFT JOIN rekomendasibarang_t ON rekomendasibarangdetail_t.rekomendasibarang_id = rekomendasibarang_t.rekomendasibarang_id
     JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     JOIN ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN satuankonversibrg_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN satuanunit_m kecil ON satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN satuanunit_m besar ON satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pegawai_m peg_mengetahui ON validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
  WHERE validasipobarang_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infopodetail_v
  OWNER TO postgres;');

/*inforekomendasibarang_v*/
$this->execute('DROP VIEW if exists public.inforekomendasibarang_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.inforekomendasibarang_v AS 
 SELECT rekomendasibarang_t.rekomendasibarang_id,
    rekomendasibarang_t.tgl_rekomendasibarang,
    rekomendasibarang_t.no_rekomendasibarang,
    rekomendasibarang_t.ruangan_id,
    ruangan_m.ruangan_nama,
    rekomendasibarang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    rekomendasibarang_t.status_po,
    fgetnamalookup(rekomendasibarang_t.status_po) AS status,
    rekomendasibarang_t.catatan
   FROM rekomendasibarang_t
     JOIN ruangan_m ON rekomendasibarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON rekomendasibarang_t.pegawai_id = pegawai_m.pegawai_id;");

$this->execute('ALTER TABLE public.inforekomendasibarang_v
  OWNER TO postgres;');

/*inforekomendasiobat_v*/
$this->execute('DROP VIEW if exists public.inforekomendasiobat_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.inforekomendasiobat_v AS 
 SELECT rekomendasiobat_t.rekomendasiobat_id,
    rekomendasiobat_t.tgl_rekomendasiobat,
    rekomendasiobat_t.no_rekomendasiobat,
    rekomendasiobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    rekomendasiobat_t.pegawai_id,
    pegawai_m.nama_pegawai,
    rekomendasiobat_t.status_po,
    fgetnamalookup(rekomendasiobat_t.status_po) AS status,
    rekomendasiobat_t.catatan
   FROM rekomendasiobat_t
     JOIN ruangan_m ON rekomendasiobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON rekomendasiobat_t.pegawai_id = pegawai_m.pegawai_id;");

$this->execute('ALTER TABLE public.inforekomendasiobat_v
  OWNER TO postgres;');

/*inforinciantagihanpasien_v*/
$this->execute('DROP VIEW if exists public.inforinciantagihanpasien_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.inforinciantagihanpasien_v AS 
 SELECT pasien_m.profilrs_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.statusperkawinan,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    asuransipasien_m.namaperusahaan,
    pendaftaran_t.tgl_selesaiperiksa,
    tindakanpelayanan_t.tindakanpelayanan_id,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.tgl_tindakan,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    tipepaket_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tindakanpelayanan_t.tarif_rsakomodasi,
    tindakanpelayanan_t.tarif_medis,
    tindakanpelayanan_t.tarif_paramedis,
    tindakanpelayanan_t.tarif_bhp,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.satuan_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.discount_tindakan,
    tindakanpelayanan_t.pembebasan_tindakan,
    tindakanpelayanan_t.subsidiasuransi_tindakan,
    tindakanpelayanan_t.subsidipemerintah_tindakan,
    tindakanpelayanan_t.subsisidirumahsakit_tindakan,
    tindakanpelayanan_t.uangditerima_tindakan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pembayaranpelayanan_id,
    kategoritindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelar_belakang,
    pendaftaran_t.ruangan_id AS ruanganpendaftaran_id,
    tindakanpelayanan_t.tindakansudahbayar_id,
    0 AS biayaservice,
    0 AS biayaadministrasi,
    0 AS biayakonseling,
    false AS is_alkes,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
        CASE
            WHEN pendaftaran_t.pembayaranpelayanan_id IS NULL THEN 'belum_lunas'::character varying
            ELSE fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer)
        END AS statusbayar_nama,
    pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
    pembayaranpelayanan_t.total_bayartindakan AS total_sudah_bayar,
    pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
    bayaruangmuka_t.jumlah_uangmuka AS total_uang_muka,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama
   FROM tindakanpelayanan_t
     JOIN pasien_m ON tindakanpelayanan_t.pasien_id = pasien_m.pasien_id
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN tandabuktibayar_t ON pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     LEFT JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_active = true AND tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT pasien_m.profilrs_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.statusperkawinan,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    asuransipasien_m.namaperusahaan,
    pendaftaran_t.tgl_selesaiperiksa,
    obatalkespasien_t.obatalkespasien_id AS tindakanpelayanan_id,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkes_m.obatalkes_id AS daftartindakan_id,
    obatalkes_m.obatalkes_kode AS daftartindakan_kode,
    obatalkes_m.obatalkes_namalain AS daftartindakan_nama,
    tipepaket_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    0 AS tarif_rsakomodasi,
    0 AS tarif_medis,
    0 AS tarif_paramedis,
    0 AS tarif_bhp,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    obatalkespasien_t.qty_oa * obatalkespasien_t.hargasatuan_oa AS tarif_tindakan,
    satuanunit_m.satuanunit_nama AS satuan_tindakan,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    false AS cyto_tindakan,
    obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
    obatalkespasien_t.discount AS discount_tindakan,
    0 AS pembebasan_tindakan,
    obatalkespasien_t.subsidiasuransi AS subsidiasuransi_tindakan,
    obatalkespasien_t.subsidipemerintah AS subsidipemerintah_tindakan,
    obatalkespasien_t.subsidirs AS subsisidirumahsakit_tindakan,
    obatalkespasien_t.iurbiaya AS uangditerima_tindakan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pembayaranpelayanan_id,
    jenisobatalkes_m.jenisobatalkes_id AS kategoritindakan_id,
    jenisobatalkes_m.jenisobatalkes_nama AS kategoritindakan_nama,
    pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelar_belakang,
    pendaftaran_t.ruangan_id AS ruanganpendaftaran_id,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    obatalkespasien_t.biayaservice,
    obatalkespasien_t.biayaadministrasi,
    obatalkespasien_t.biayakonseling,
    true AS is_alkes,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
        CASE
            WHEN pendaftaran_t.pembayaranpelayanan_id IS NULL THEN 'belum_lunas'::character varying
            ELSE fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer)
        END AS statusbayar_nama,
    pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
    pembayaranpelayanan_t.total_bayartindakan AS total_sudah_bayar,
    pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
    bayaruangmuka_t.jumlah_uangmuka AS total_uang_muka,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama
   FROM pendaftaran_t
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN tipepaket_m ON obatalkespasien_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN tandabuktibayar_t ON pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     LEFT JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
  WHERE obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;
");

$this->execute('ALTER TABLE public.inforinciantagihanpasien_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190923_090659_optimize_view_10 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190923_090659_optimize_view_10 cannot be reverted.\n";

        return false;
    }
    */
}
