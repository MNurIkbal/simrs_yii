<?php

use yii\db\Migration;

/**
 * Class m190924_065718_optimize_view_13
 */
class m190924_065718_optimize_view_13 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*sync_pasien_copy*/
$this->execute('DROP VIEW if exists public.sync_pasien_copy;');

/*sync_pendaftaran_bu*/
$this->execute('DROP VIEW if exists public.sync_pendaftaran_bu;');

/*infopemakaianambulan_v*/
$this->execute('DROP VIEW if exists public.infopemakaianambulan_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopemakaianambulan_v AS 
 SELECT pemakaianambulan_t.pemakaianambulan_id,
    ambulan_m.ambulan_id,
    ambulan_m.no_polisi,
    pesanambulan_t.tgl_pesanambulan,
    pemakaianambulan_t.tgl_pemakaiandari,
    pemakaianambulan_t.tgl_pemakaiansampai,
    pesanambulan_t.no_pesanambulan,
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
            WHEN ambulan_m.is_emergency IS TRUE THEN 'EMERGENCY'::text
            ELSE 'NON EMERGENCY'::text
        END AS jenis_ambulan,
    pesanambulan_t.status_ambulan,
    pesanambulan_t.asal_pasien,
    pesanambulan_t.keluhan,
    fgetnamalookup(pemakaianambulan_t.pelayanan_ambulan) AS pelayanan,
    COALESCE(pemakaianambulan_t.km_awal, 0) AS km_awal,
    COALESCE(pemakaianambulan_t.km_akhir, 0) AS km_akhir,
    COALESCE(pemakaianambulan_t.km_akhir, 0) - COALESCE(pemakaianambulan_t.km_awal, 0) AS jarak_pemakian,
    COALESCE(pemakaianambulan_t.total_biaya, 0::double precision) AS nominal_tagihan,
    fgetnamalookup(pesanambulan_t.status_ambulan) AS status_ambulan_nama,
    pegawai.nama_pegawai::character varying AS supir,
    pesanambulan_t.umur,
    pesanambulan_t.is_sadar,
    pesanambulan_t.is_nafas,
    pesanambulan_t.is_nadi,
    pesanambulan_t.nama_pj,
    pesanambulan_t.kontak_pj,
    pemakaianambulan_t.created_date,
    pemakaianambulan_t.biaya_pemakaian,
    pemakaianambulan_t.tgl_realisasikembali,
    pemakaianambulan_t.lama_pemakaian,
    pesanambulan_t.status_pesan
   FROM pemakaianambulan_t
     JOIN pesanambulan_t ON pemakaianambulan_t.pemakaianambulan_id = pesanambulan_t.pemakaianambulan_id
     LEFT JOIN pasien_m ON pesanambulan_t.pasien_id = pasien_m.pasien_id
     JOIN ambulan_m ON pesanambulan_t.ambulan_id = ambulan_m.ambulan_id
     LEFT JOIN ( SELECT pemakaianambulandetail_t.pemakaianambulan_id,
            string_agg(pegawai_m.nama_pegawai::text, ' ,'::text) AS nama_pegawai
           FROM pemakaianambulandetail_t
             JOIN pegawai_m ON pemakaianambulandetail_t.petugas_id = pegawai_m.pegawai_id AND pegawai_m.jabatan_id = 38
          GROUP BY pemakaianambulandetail_t.pemakaianambulan_id) pegawai ON pemakaianambulan_t.pemakaianambulan_id = pegawai.pemakaianambulan_id;
");
$this->execute('ALTER TABLE public.infopemakaianambulan_v
  OWNER TO postgres;');

/*infotagihanobat_v*/
$this->execute('DROP VIEW if exists public.infotagihanobat_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infotagihanobat_v AS 
 SELECT penjualanresep_t.penjualanresep_id,
    penjualanresep_t.tglpenjualan,
    fgetnamalookup(penjualanresep_t.jenispenjualan::integer) AS jenis_penjualan,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    penjualanresep_t.noresep,
        CASE
            WHEN pasien_m.nama_pasien IS NOT NULL THEN pasien_m.nama_pasien::text
            WHEN penjualanresep_t.nama_pembeli IS NOT NULL THEN penjualanresep_t.nama_pembeli::text
            WHEN karyawan.nama_pegawai IS NOT NULL THEN karyawan.nama_pegawai::text
            ELSE ''::text
        END AS nama_pembeli,
    penjualanresep_t.carabayar_id,
    carabayar_m.carabayar_nama,
    penjualanresep_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjualanresep_t.totharganetto::integer AS totalharga_netto,
    pasien_m.no_rekam_medik,
    penjualanresep_t.jenispenjualan,
    penjualanresep_t.status_bayar,
    fgetnamalookup(penjualanresep_t.status_bayar::integer) AS status_bayar_nama,
    COALESCE(penjualanresep_t.totaltarifservice::integer::double precision, 0::double precision) AS jasa,
    COALESCE(penjualanresep_t.biayaadministrasi::integer::double precision, 0::double precision) AS administrasi,
    COALESCE(penjualanresep_t.totalhargajual::integer::double precision, 0::double precision) AS obat,
    COALESCE(penjualanresep_t.totalhargajual::integer::double precision, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice::integer::double precision, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi::integer::double precision, 0::double precision) AS totalharga_jual
   FROM penjualanresep_t
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infotagihanobat_v
  OWNER TO postgres;');

/*infopasiensudahbayar_v*/
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
    pendaftaran.kelaspelayanan_nama,
    pendaftaran.penjualanresep_id
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
            fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer) AS status_bayar,
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
            kelaspelayanan_m.kelaspelayanan_nama,
            NULL::integer AS penjualanresep_id,
            NULL::character varying AS nama_pembeli
           FROM pembayaranpelayanan_t
             JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
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
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli
           FROM pembayaranpelayanan_t
             JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false) pendaftaran
     LEFT JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
     LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
     LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id;");

$this->execute('ALTER TABLE public.infopasiensudahbayar_v
  OWNER TO postgres;');

/*infopenjualanresep_v*/
$this->execute('DROP VIEW if exists public.infopenjualanresep_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopenjualanresep_v AS 
 SELECT penjualanresep_t.penjualanresep_id,
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
    penjualanresep_t.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    penjualanresep_t.penjamin_id,
    penjualanresep_t.tglpenjualan,
    penjualanresep_t.discount,
    penjualanresep_t.subsidiasuransi,
    penjualanresep_t.subsidipemerintah,
    penjualanresep_t.subsidirs,
    penjualanresep_t.iurbiaya,
    penjualanresep_t.lamapelayanan,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.nama_pembeli,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    penjualanresep_t.pegawai_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    pegawai_m.nama_pegawai,
    penjualanresep_t.ruangan_id,
    penjualanresep_t.karyawan_id,
    karyawan.nama_pegawai AS nama_karyawan,
    reseptur.antrian_id,
    antrian.no_antrian,
    peg_reseptur.nama_pegawai AS pegawai_reseptur,
    penjualanresep_t.catatan,
    fgetstatuspembayaranresep(penjualanresep_t.penjualanresep_id) AS status_pembayaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    penjualanresep_t.iter
   FROM penjualanresep_t
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN reseptur_t reseptur ON penjualanresep_t.reseptur_id = reseptur.reseptur_id
     LEFT JOIN antrian_t antrian ON reseptur.antrian_id = antrian.antrian_id
     LEFT JOIN pegawai_m peg_reseptur ON reseptur.pegawai_id = peg_reseptur.pegawai_id
     LEFT JOIN ruangan_m ON reseptur.ruanganreseptur_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;");

$this->execute('ALTER TABLE public.infopenjualanresep_v
  OWNER TO postgres;');

/*infosisaantrian_v*/
$this->execute('DROP VIEW if exists public.infosisaantrian_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infosisaantrian_v AS 
 SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.groupcarabayar_id,
    fgetnamalookup(antrian_t.groupcarabayar_id) AS group_carabayar,
    antrian_t.klasifikasipasien_id,
    klasifikasipasien_m.klasifikasipasien_nama,
    antrian_t.status_antrian,
    antrian_t.jenisantrian_id,
    antrian_t.is_online,
        CASE
            WHEN antrian_t.status_antrian = 0 THEN 'Belum Dipanggil'::text
            WHEN antrian_t.status_antrian = 1 THEN 'Dipanggil'::text
            WHEN antrian_t.status_antrian = 2 THEN 'Dipilih'::text
            WHEN antrian_t.status_antrian = 3 THEN 'Lewati'::text
            WHEN antrian_t.status_antrian = 4 THEN 'Batal'::text
            ELSE ''::text
        END AS status_antrian_nama,
    konfigantrian_m.konfigantrian_id,
    antrian_t.loket_id,
    antrian_t.tgl_antrian,
    ruangan_m.instalasi_id
   FROM antrian_t
     JOIN klasifikasipasien_m ON antrian_t.klasifikasipasien_id = klasifikasipasien_m.klasifikasipasien_id
     JOIN konfigantrian_m ON antrian_t.konfigantrian_id = konfigantrian_m.konfigantrian_id
     LEFT JOIN ruangan_m ON antrian_t.ruangan_id = ruangan_m.ruangan_id
  WHERE antrian_t.jenisantrian_id = 177 AND antrian_t.is_online = false;");

$this->execute('ALTER TABLE public.infosisaantrian_v
  OWNER TO postgres;
');

/*infostokopnamebarang_v*/
$this->execute('DROP VIEW if exists public.infostokopnamebarang_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infostokopnamebarang_v AS 
 SELECT stokopnamebarang_t.stokopnamebarang_id,
    stokopnamebarang_t.formsobarang_id,
    stokopnamebarang_t.ruangan_id,
    stokopnamebarang_t.pegmengetahui_id,
    stokopnamebarang_t.petugas_id,
    ruangan_m.instalasi_id,
    stokopnamebarang_t.jenisstokopname,
    instalasi_m.instalasi_nama,
    fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_stokopname,
    ruangan_m.ruangan_nama,
    stokopnamebarang_t.tglstokopname,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.totalharga_fisik,
    stokopnamebarang_t.totalharga_sistem,
    stokopnamebarang_t.totalharga_sistem - stokopnamebarang_t.totalharga_fisik AS selisih,
    formsobarang_t.noformulir,
    min(periodestokbarang_m.tglperiodestok_awal) AS periode_awal,
    max(periodestokbarang_m.tglperiodestok_akhir) AS periode_akhir
   FROM stokopnamebarang_t
     JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON stokopnamebarang_t.pegmengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_petugas ON stokopnamebarang_t.petugas_id = pegawai_petugas.pegawai_id
     LEFT JOIN formsobarang_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
     LEFT JOIN formsobarangdetail_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
     LEFT JOIN periodestokbarang_m ON formsobarangdetail_t.periodestok_id = periodestokbarang_m.periodestokbarang_id
  WHERE stokopnamebarang_t.is_deleted = false AND stokopnamebarang_t.is_active = true
  GROUP BY stokopnamebarang_t.stokopnamebarang_id, stokopnamebarang_t.formsobarang_id, stokopnamebarang_t.ruangan_id, stokopnamebarang_t.pegmengetahui_id, stokopnamebarang_t.petugas_id, ruangan_m.instalasi_id, stokopnamebarang_t.jenisstokopname, instalasi_m.instalasi_nama, (fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer)), ruangan_m.ruangan_nama, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.nostokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, formsobarang_t.noformulir;
");

$this->execute('ALTER TABLE public.infostokopnamebarang_v
  OWNER TO postgres;');

/*infobayaruangmuka_v*/
$this->execute('DROP VIEW if exists public.infobayaruangmuka_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infobayaruangmuka_v AS 
 SELECT hit.pendaftaran_id,
    hit.tgl_pendaftaran,
    hit.no_pendaftaran,
    hit.pasien_id,
    hit.no_rekam_medik,
    hit.nama_pasien,
    hit.tanggal_lahir,
    hit.umur,
    hit.jeniskelamin,
    hit.carabayar_id,
    hit.carabayar_nama,
    hit.penjamin_id,
    hit.penjamin_nama,
    hit.kelaspelayanan_nama,
    sum(hit.jumlah_uangmuka) AS jumlah_uangmuka,
    sum(hit.pemakaian_uangmuka) AS pemakaian_uangmuka,
    sum(hit.sisa_uangmuka) AS sisa_uangmuka
   FROM ( SELECT bayaruangmuka_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            kelaspelayanan_m.kelaspelayanan_nama,
            sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision)) AS jumlah_uangmuka,
            COALESCE(pemakaian.pemakaian_uangmuka, 0::double precision) AS pemakaian,
            COALESCE(pengembalian.total_pengembalian, 0::double precision) AS pengembalian,
            COALESCE(pemakaian.pemakaian_uangmuka, 0::double precision) + COALESCE(pengembalian.total_pengembalian, 0::double precision) AS pemakaian_uangmuka,
            sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision)) - COALESCE(pemakaian.pemakaian_uangmuka, 0::double precision) - COALESCE(pengembalian.total_pengembalian, 0::double precision) AS sisa_uangmuka
           FROM pendaftaran_t
             JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT pengembalianuangmuka_t.pendaftaran_id,
                    sum(pengembalianuangmuka_t.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t
                  GROUP BY pengembalianuangmuka_t.pendaftaran_id) pengembalian ON bayaruangmuka_t.pendaftaran_id = pengembalian.pendaftaran_id
             LEFT JOIN ( SELECT pemakaianuangmuka_t.pendaftaran_id,
                    sum(pemakaianuangmuka_t.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t
                  GROUP BY pemakaianuangmuka_t.pendaftaran_id) pemakaian ON bayaruangmuka_t.pendaftaran_id = pemakaian.pendaftaran_id
          WHERE bayaruangmuka_t.is_active = true AND bayaruangmuka_t.is_deleted = false
          GROUP BY bayaruangmuka_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.umur, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pasien_m.jeniskelamin, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pengembalian.total_pengembalian, pemakaian.pemakaian_uangmuka) hit
  GROUP BY hit.pendaftaran_id, hit.tgl_pendaftaran, hit.no_pendaftaran, hit.pasien_id, hit.no_rekam_medik, hit.nama_pasien, hit.tanggal_lahir, hit.umur, hit.jeniskelamin, hit.carabayar_id, hit.carabayar_nama, hit.penjamin_id, hit.penjamin_nama, hit.kelaspelayanan_nama;
");
$this->execute('ALTER TABLE public.infobayaruangmuka_v
  OWNER TO postgres;');

/*infotagihanpasienpulang_v*/
$this->execute('DROP VIEW if exists public.infotagihanpasienpulang_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infotagihanpasienpulang_v AS 
 SELECT gabung.pendaftaran_id,
    gabung.pasienpulang_id,
    gabung.pasienpulangri_id,
    gabung.tglpasienpulang,
    gabung.no_pendaftaran,
    gabung.instalasi_id,
    gabung.instalasi_nama,
    gabung.ruanganakhir_id AS ruangan_id,
    gabung.ruangan_nama,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.carabayar_id,
    gabung.carabayar_nama,
    gabung.penjamin_id,
    gabung.penjamin_nama,
    gabung.jeniskasuspenyakit_nama,
    gabung.status_bayar,
    gabung.kelaspelayanan_nama,
    gabung.nama_pegawai AS dokter,
    COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) AS total_tindakan,
    COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision) AS total_obat,
    (COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) + COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision))::integer AS total_tagihan,
    gabung.pegawai_id,
    gabung.photopasien,
    gabung.tanggal_lahir,
    gabung.umur,
    gabung.jeniskelamin,
    gabung.jenis_kelamin,
    gabung.tgl_pendaftaran
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
            NULL::double precision AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
           FROM pendaftaran_t
             LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND pendaftaran_t.instalasi_id = 1 OR pendaftaran_t.instalasi_id = 2 AND pasienpulang_t.carakeluar_id <> 5
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
            NULL::double precision AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id
           FROM pendaftaran_t
             LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pasienpulang_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            NULL::double precision AS total_tindakan,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id
           FROM pendaftaran_t
             LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.instalasi_id = 1 OR pendaftaran_t.instalasi_id = 2 AND pasienpulang_t.carakeluar_id <> 5
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            NULL::double precision AS total_tindakan,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id
           FROM pendaftaran_t
             LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pasienpulang_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id) gabung
  WHERE gabung.sudah_bayar IS NULL
  GROUP BY gabung.kelaspelayanan_nama, gabung.nama_pegawai, gabung.status_bayar, gabung.pendaftaran_id, gabung.pasienpulang_id, gabung.tglpasienpulang, gabung.no_pendaftaran, gabung.instalasi_id, gabung.instalasi_nama, gabung.ruanganakhir_id, gabung.ruangan_nama, gabung.no_rekam_medik, gabung.nama_pasien, gabung.carabayar_id, gabung.carabayar_nama, gabung.penjamin_id, gabung.penjamin_nama, gabung.jeniskasuspenyakit_nama, gabung.pegawai_id, gabung.pasienpulangri_id, gabung.photopasien, gabung.tanggal_lahir, gabung.umur, gabung.jeniskelamin, gabung.jenis_kelamin, gabung.tgl_pendaftaran;
");

$this->execute('ALTER TABLE public.infotagihanpasienpulang_v
  OWNER TO postgres;');

/*infotagihanpenunjang_v*/
$this->execute('DROP VIEW if exists public.infotagihanpenunjang_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.infotagihanpenunjang_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruang_pendaftaran.ruangan_nama AS ruang_pendaftaran,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) + sum(COALESCE(obatalkespasien_t.hargajual_oa::integer, 0))::double precision AS jumlah_tagihan,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.ruangan_id
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t_1.tarif_tindakan, 0::double precision)) AS tarif_tindakan
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
          WHERE tindakanpelayanan_t_1.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.tindakanpelayanan_id, tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT obatalkespasien_t_1.pasienmasukpenunjang_id,
            sum(COALESCE(obatalkespasien_t_1.hargajual_oa::integer, 0)) AS hargajual_oa
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE obatalkespasien_t_1.obatsudahbayar_id IS NULL AND obatalkespasien_t_1.is_deleted = false
          GROUP BY obatalkespasien_t_1.pasienmasukpenunjang_id) obatalkespasien_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = obatalkespasien_t.pasienmasukpenunjang_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ruangan_m ruang_pendaftaran ON pendaftaran_t.ruangan_id = ruang_pendaftaran.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE pendaftaran_t.status_bayar = 349
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, ruang_pendaftaran.ruangan_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.pendaftaran_id, pasienmasukpenunjang_t.tglmasukpenunjang, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pendaftaran_t.tgl_pendaftaran, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pasien_m.tanggal_lahir, pasienmasukpenunjang_t.no_masukpenunjang;
");

$this->execute('ALTER TABLE public.infotagihanpenunjang_v
  OWNER TO postgres;');


/*nilaipemeriksaanlabdetail_v*/
$this->execute('DROP VIEW if exists public.nilaipemeriksaanlabdetail_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.nilaipemeriksaanlabdetail_v AS 
 SELECT pemeriksaanlab_m.pemeriksaanlab_id,
    pemeriksaanlab_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    nilairujukan_m.nilairujukan_id,
    nilairujukan_m.nama_rujukan,
    nilairujukan_m.jenis_kelamin,
    fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
    nilairujukan_m.golonganumur_id,
    golonganumurlab_m.gol_umurlab_nama,
    golonganumurlab_m.gol_umurlab_minimal,
    golonganumurlab_m.gol_umurlab_maksimal,
    concat(nilairujukan_m.nilai_min, ' ', '-', ' ', nilairujukan_m.nilai_max) AS nilai_rujukan,
    nilairujukan_m.nilai_min,
    nilairujukan_m.nilai_max,
    nilairujukan_m.satuan_hasillab AS satuanlab_nama,
    nilairujukan_m.keterangan,
    hasilpemeriksaanlabdetail_t.hasil,
    hasilpemeriksaanlabdetail_t.petugaslab_id,
    hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
    petugaslab.nama_pegawai AS petugaslab_nama,
    ambilsample_t.samplelab_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
    nilairujukan_m.is_deleted
   FROM ambilsample_t
     JOIN pemeriksaanlab_m ON ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.daftartindakan_id
     JOIN pasienmasukpenunjang_t ON ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     JOIN daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN nilairujukan_m ON pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id
     LEFT JOIN golonganumurlab_m ON nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id
     LEFT JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id
     LEFT JOIN hasilpemeriksaanlabdetail_t ON pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id AND nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id AND hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id
     LEFT JOIN pegawai_m petugaslab ON hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id
     LEFT JOIN samplelab_m ON hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id
  WHERE ambilsample_t.is_deleted = false AND ambilsample_t.is_active = true AND pemeriksaanlab_m.is_deleted = false AND pemeriksaanlab_m.is_active = true AND daftartindakan_m.is_deleted = false AND daftartindakan_m.is_active = true
UNION ALL
 SELECT pemeriksaanlab_m.pemeriksaanlab_id,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanlab_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    nilairujukan_m.nilairujukan_id,
    nilairujukan_m.nama_rujukan,
    nilairujukan_m.jenis_kelamin,
    fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
    nilairujukan_m.golonganumur_id,
    golonganumurlab_m.gol_umurlab_nama,
    golonganumurlab_m.gol_umurlab_minimal,
    golonganumurlab_m.gol_umurlab_maksimal,
    concat(nilairujukan_m.nilai_min, ' ', '-', ' ', nilairujukan_m.nilai_max) AS nilai_rujukan,
    nilairujukan_m.nilai_min,
    nilairujukan_m.nilai_max,
    nilairujukan_m.satuan_hasillab AS satuanlab_nama,
    nilairujukan_m.keterangan,
    hasilpemeriksaanlabdetail_t.hasil,
    hasilpemeriksaanlabdetail_t.petugaslab_id,
    hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
    petugaslab.nama_pegawai AS petugaslab_nama,
    ambilsample_t.samplelab_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
    nilairujukan_m.is_deleted
   FROM ambilsample_t
     JOIN pemeriksaanlab_m ON ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.tipepaket_id
     JOIN pasienmasukpenunjang_t ON ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     JOIN tipepaket_m ON pemeriksaanlab_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON pemeriksaanlab_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN nilairujukan_m ON pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id
     LEFT JOIN golonganumurlab_m ON nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id
     LEFT JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id
     LEFT JOIN hasilpemeriksaanlabdetail_t ON pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id AND nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id AND hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id
     LEFT JOIN pegawai_m petugaslab ON hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id
     LEFT JOIN samplelab_m ON hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id
  WHERE ambilsample_t.is_deleted = false AND ambilsample_t.is_active = true AND pemeriksaanlab_m.is_deleted = false AND pemeriksaanlab_m.is_active = true AND daftartindakan_m.is_deleted = false AND daftartindakan_m.is_active = true;
");

$this->execute('ALTER TABLE public.nilaipemeriksaanlabdetail_v
  OWNER TO postgres;');

/*obatalkesdetail_v*/
$this->execute('DROP VIEW if exists public.obatalkesdetail_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.obatalkesdetail_v AS 
 SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.ven AS ven_id,
    fgetnamalookup(obatalkes_m.ven) AS ven,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.kekuatan_obat,
    obatalkes_m.satuankecil_id,
    satuankecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.satuansedang_id,
    satuan_1.satuanunit_nama AS satuan_1,
    obatalkes_m.satuanbesar_id,
    satuan_2.satuanunit_nama AS satuan_2,
    obatalkes_m.kemasan_sedang,
    obatalkes_m.kemasan_besar,
    obatalkes_m.is_generik,
    obatalkes_m.tglkadaluarsa,
    obatalkes_m.obatalkes_nobatch,
    obatalkes_m.obatalkes_kategori,
    fgetnamalookup(obatalkes_m.obatalkes_kategori::integer) AS kategori,
    obatalkes_m.maksimalstok,
    obatalkes_m.supplier_id,
    supplier_m.supplier_nama,
    obatalkes_m.harga_beli,
    obatalkes_m.discount,
    obatalkes_m.ppn_persen,
    obatalkes_m.harganetto,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.hargaterakhir,
    obatalkes_m.indikasi,
    obatalkes_m.interaksi,
    obatalkes_m.kontradiksi,
    obatalkes_m.efek_samping,
    obatalkes_m.satuankekuatan,
    fgetnamalookup(obatalkes_m.satuankekuatan::integer) AS lookup_name,
    obatalkes_m.groupinacbg_id,
    obatalkes_m.lead_time,
    obatalkes_m.minimalstok,
    obatalkes_m.avg_usage,
    obatalkes_m.min_order,
    obatalkes_m.max_order,
    obatalkes_m.nilai_ro,
    obatalkes_m.on_po,
    obatalkes_m.on_ro
   FROM obatalkes_m
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_1 ON obatalkes_m.satuansedang_id = satuan_1.satuanunit_id
     LEFT JOIN satuanunit_m satuan_2 ON obatalkes_m.satuanbesar_id = satuan_2.satuanunit_id
     LEFT JOIN supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false;");

$this->execute('ALTER TABLE public.obatalkesdetail_v
  OWNER TO postgres;');

/*orderpenunjang_v*/
$this->execute('DROP VIEW if exists public.orderpenunjang_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.orderpenunjang_v AS 
 SELECT
        CASE
            WHEN gabung.pendaftaran_id IS NULL THEN pasienadmisi_t.pendaftaran_id
            ELSE gabung.pendaftaran_id
        END AS pendaftaran_id,
        CASE
            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN pasienadmisi_t.no_pendaftaran
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
        CASE
            WHEN pendaftaran_t.instalasi_nama IS NULL THEN pasienadmisi_t.instalasi_nama
            ELSE pendaftaran_t.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.ruangan_nama IS NULL THEN pasienadmisi_t.ruangan_nama
            ELSE pendaftaran_t.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.no_rekam_medik IS NULL THEN pasienadmisi_t.no_rekam_medik
            ELSE pendaftaran_t.no_rekam_medik
        END AS no_rekam_medik,
        CASE
            WHEN pendaftaran_t.nama_pasien IS NULL THEN pasienadmisi_t.nama_pasien
            ELSE pendaftaran_t.nama_pasien
        END AS nama_pasien,
    gabung.instalasi_nama AS instalasi_penunjang,
    gabung.ruangan_nama AS ruangan_penunjang,
        CASE
            WHEN pendaftaran_t.tanggal_lahir IS NULL THEN pasienadmisi_t.tanggal_lahir
            ELSE pendaftaran_t.tanggal_lahir
        END AS tanggal_lahir,
        CASE
            WHEN pendaftaran_t.jenis_kelamin IS NULL THEN pasienadmisi_t.jenis_kelamin
            ELSE pendaftaran_t.jenis_kelamin
        END AS jenis_kelamin,
        CASE
            WHEN pendaftaran_t.carabayar_nama IS NULL THEN pasienadmisi_t.carabayar_nama
            ELSE pendaftaran_t.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.penjamin_nama IS NULL THEN pasienadmisi_t.penjamin_nama
            ELSE pendaftaran_t.penjamin_nama
        END AS penjamin_nama,
    gabung.tgl_kirimpasien,
    gabung.no_orderkeunitlain,
    gabung.nama_pegawai AS dokter_perujuk,
        CASE
            WHEN gabung.pemeriksaanrad_id IS NOT NULL THEN jenispemeriksaanrad_m.jenispemeriksaanrad_nama::text
            WHEN gabung.pemeriksaanlab_id IS NOT NULL THEN jenispemeriksaanlab_m.jenispemeriksaanlab_nama::text
            WHEN gabung.operasi_id IS NOT NULL THEN kegiatanoperasi_m.kegiatanoperasi_nama::text
            ELSE gabung.pemeriksaan::text
        END AS jenis_periksa,
    gabung.pemeriksaan_nama,
    gabung.tarif_pelayanan,
    gabung.is_cyto,
    gabung.tarif_cytotindakan,
    pasienadmisi_t.pasienadmisi_id,
    gabung.instruksi_id,
    gabung.pasienkirimkeunitlain_id
   FROM ( SELECT 'NON_PAKET'::text AS jenis,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien,
            pasienkirimkeunitlain_t.no_orderkeunitlain,
            pasienkirimkeunitlain_t.ruangan_id,
            ruangan_m.ruangan_nama,
            daftartindakan_m.daftartindakan_nama AS pemeriksaan,
            permintaankepenunjang_t.qtypermintaan,
            pasienkirimkeunitlain_t.instalasi_id,
            pasienkirimkeunitlain_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            instalasi_m.instalasi_nama,
            pasienkirimkeunitlain_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasienmasukpenunjang_t.catatan,
            fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
            permintaankepenunjang_t.pemeriksaanrad_id,
            permintaankepenunjang_t.pemeriksaanlab_id,
            permintaankepenunjang_t.operasi_id,
            pasienkirimkeunitlain_t.instruksi_id,
            permintaankepenunjang_t.tarif_pelayanan,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tarif_cytotindakan,
            daftartindakan_m.daftartindakan_nama AS pemeriksaan_nama,
            batalorderpenunjang_t.alasan AS alasan_batal
           FROM pasienkirimkeunitlain_t
             JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
             JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN batalorderpenunjang_t ON batalorderpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
        UNION ALL
         SELECT 'PAKET'::text AS jenis,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien,
            pasienkirimkeunitlain_t.no_orderkeunitlain,
            pasienkirimkeunitlain_t.ruangan_id,
            ruangan_m.ruangan_nama,
            tipepaket_m.tipepaket_nama AS pemeriksaan,
            permintaankepenunjang_t.qtypermintaan,
            pasienkirimkeunitlain_t.instalasi_id,
            pasienkirimkeunitlain_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            instalasi_m.instalasi_nama,
            pasienkirimkeunitlain_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasienmasukpenunjang_t.catatan,
            fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
            permintaankepenunjang_t.pemeriksaanrad_id,
            permintaankepenunjang_t.pemeriksaanlab_id,
            permintaankepenunjang_t.operasi_id,
            pasienkirimkeunitlain_t.instruksi_id,
            permintaankepenunjang_t.tarif_pelayanan,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tarif_cytotindakan,
            ( SELECT string_agg(tindakan.daftartindakan_nama::text, ', '::text) AS string_agg
                   FROM paketpelayanan_mp mp
                     JOIN daftartindakan_m tindakan ON mp.daftartindakan_id = tindakan.daftartindakan_id
                  WHERE mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id) AS pemeriksaan_nama,
            batalorderpenunjang_t.alasan AS alasan_batal
           FROM pasienkirimkeunitlain_t
             JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
             JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
             JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN batalorderpenunjang_t ON batalorderpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id) gabung
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            pasien_m.tanggal_lahir,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m ON pendaftaran_t_1.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON pendaftaran_t_1.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN carabayar_m ON pendaftaran_t_1.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t_1.penjamin_id = penjamin_m.penjamin_id) pendaftaran_t ON gabung.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.no_pendaftaran,
            pasienadmisi_t_1.pasienadmisi_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            pasien_m.tanggal_lahir,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama
           FROM pasienadmisi_t pasienadmisi_t_1
             JOIN pendaftaran_t pendaftaran_t_1 ON pasienadmisi_t_1.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t_1.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON pendaftaran_t_1.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN carabayar_m ON pendaftaran_t_1.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t_1.penjamin_id = penjamin_m.penjamin_id) pasienadmisi_t ON gabung.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pemeriksaanlab_m ON gabung.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON gabung.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN operasi_m ON gabung.operasi_id = operasi_m.operasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
  ORDER BY (
        CASE
            WHEN gabung.pendaftaran_id IS NULL THEN pasienadmisi_t.pendaftaran_id
            ELSE gabung.pendaftaran_id
        END);
");

$this->execute('ALTER TABLE public.orderpenunjang_v
  OWNER TO postgres;');

/*riwayatpenunjangdetail_v*/
$this->execute('DROP VIEW if exists public.riwayatpenunjangdetail_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.riwayatpenunjangdetail_v AS 
 SELECT gabung.jenis,
    gabung.pasienkirimkeunitlain_id,
    gabung.tgl_kirimpasien,
    gabung.no_orderkeunitlain,
    gabung.ruangan_id,
    gabung.ruangan_nama,
    gabung.pemeriksaan,
    gabung.qtypermintaan,
    gabung.instalasi_id,
    gabung.pendaftaran_id,
    gabung.pasienadmisi_id,
    gabung.instalasi_nama,
    gabung.pegawai_id,
    gabung.nama_pegawai,
    gabung.catatan_dokterpengirim,
    gabung.catatan,
    gabung.status,
    gabung.pemeriksaanrad_id,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis_rad,
    gabung.pemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis_lab,
    gabung.operasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama AS jenis_operasi,
    gabung.instruksi_id,
        CASE
            WHEN gabung.pemeriksaanrad_id IS NOT NULL THEN jenispemeriksaanrad_m.jenispemeriksaanrad_nama::text
            WHEN gabung.pemeriksaanlab_id IS NOT NULL THEN jenispemeriksaanlab_m.jenispemeriksaanlab_nama::text
            WHEN gabung.operasi_id IS NOT NULL THEN kegiatanoperasi_m.kegiatanoperasi_nama::text
            ELSE gabung.pemeriksaan::text
        END AS jenis_periksa,
    gabung.tarif_pelayanan,
    gabung.is_cyto,
    gabung.tarif_cytotindakan,
    gabung.pemeriksaan_nama,
    gabung.alasan_batal
   FROM ( SELECT 'NON_PAKET'::text AS jenis,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien,
            pasienkirimkeunitlain_t.no_orderkeunitlain,
            pasienkirimkeunitlain_t.ruangan_id,
            ruangan_m.ruangan_nama,
            daftartindakan_m.daftartindakan_nama AS pemeriksaan,
            permintaankepenunjang_t.qtypermintaan,
            pasienkirimkeunitlain_t.instalasi_id,
            pasienkirimkeunitlain_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            instalasi_m.instalasi_nama,
            pasienkirimkeunitlain_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasienmasukpenunjang_t.catatan,
            fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
            permintaankepenunjang_t.pemeriksaanrad_id,
            permintaankepenunjang_t.pemeriksaanlab_id,
            permintaankepenunjang_t.operasi_id,
            pasienkirimkeunitlain_t.instruksi_id,
            permintaankepenunjang_t.tarif_pelayanan,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tarif_cytotindakan,
            daftartindakan_m.daftartindakan_nama AS pemeriksaan_nama,
            batalorderpenunjang_t.alasan AS alasan_batal
           FROM pasienkirimkeunitlain_t
             JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
             JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN batalorderpenunjang_t ON batalorderpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
        UNION ALL
         SELECT 'PAKET'::text AS jenis,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien,
            pasienkirimkeunitlain_t.no_orderkeunitlain,
            pasienkirimkeunitlain_t.ruangan_id,
            ruangan_m.ruangan_nama,
            tipepaket_m.tipepaket_nama AS pemeriksaan,
            permintaankepenunjang_t.qtypermintaan,
            pasienkirimkeunitlain_t.instalasi_id,
            pasienkirimkeunitlain_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            instalasi_m.instalasi_nama,
            pasienkirimkeunitlain_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasienmasukpenunjang_t.catatan,
            fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
            permintaankepenunjang_t.pemeriksaanrad_id,
            permintaankepenunjang_t.pemeriksaanlab_id,
            permintaankepenunjang_t.operasi_id,
            pasienkirimkeunitlain_t.instruksi_id,
            permintaankepenunjang_t.tarif_pelayanan,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tarif_cytotindakan,
            ( SELECT string_agg(tindakan.daftartindakan_nama::text, ', '::text) AS string_agg
                   FROM paketpelayanan_mp mp
                     JOIN daftartindakan_m tindakan ON mp.daftartindakan_id = tindakan.daftartindakan_id
                  WHERE mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id) AS pemeriksaan_nama,
            batalorderpenunjang_t.alasan AS alasan_batal
           FROM pasienkirimkeunitlain_t
             JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
             JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
             JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN batalorderpenunjang_t ON batalorderpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id) gabung
     LEFT JOIN pemeriksaanlab_m ON gabung.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON gabung.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN operasi_m ON gabung.operasi_id = operasi_m.operasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id;
");

$this->execute('ALTER TABLE public.riwayatpenunjangdetail_v
  OWNER TO postgres;');

/*pengajuanklaimalokasi_v*/
$this->execute('DROP VIEW if exists public.pengajuanklaimalokasi_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.pengajuanklaimalokasi_v AS 
 SELECT pengajuanklaim_t.pengajuanklaim_id,
    pengajuanklaim_t.no_pengajuanklaim,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
        CASE
            WHEN pengajuanklaim_t.instalasi_id = 0 THEN 'Semua'::text
            ELSE ''::text
        END AS instalasi,
        CASE
            WHEN pengajuanklaim_t.ruangan_id = 0 THEN 'Semua'::text
            ELSE ''::text
        END AS ruangan,
    pengajuanklaim_t.status_pengajuanklaim,
    fgetnamalookup(pengajuanklaim_t.status_pengajuanklaim::integer) AS status,
    terimabayarklaimdetail_t.terimabayarklaimdetail_id,
    terimabayarklaim_t.terimabayarklaim_id,
    terimabayarklaim_t.no_terimabayarklaim,
    pengajuanklaim_t.total_piutang AS total_pengajuan,
    terimabayarklaimdetail_t.pembayaran,
    pengajuanklaim_t.total_piutang - terimabayarklaimdetail_t.pembayaran AS sisa
   FROM pengajuanklaim_t
     JOIN terimabayarklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = terimabayarklaimdetail_t.pengajuanklaim_id
     JOIN terimabayarklaim_t ON terimabayarklaimdetail_t.terimabayarklaim_id = terimabayarklaim_t.terimabayarklaim_id
     JOIN carabayar_m ON pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
  WHERE terimabayarklaimdetail_t.is_alokasi = false
  GROUP BY pengajuanklaim_t.pengajuanklaim_id, pengajuanklaim_t.no_pengajuanklaim, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, (
        CASE
            WHEN pengajuanklaim_t.instalasi_id = 0 THEN 'Semua'::text
            ELSE ''::text
        END), (
        CASE
            WHEN pengajuanklaim_t.ruangan_id = 0 THEN 'Semua'::text
            ELSE ''::text
        END), pengajuanklaim_t.status_pengajuanklaim, (fgetnamalookup(pengajuanklaim_t.status_pengajuanklaim::integer)), terimabayarklaimdetail_t.terimabayarklaimdetail_id, terimabayarklaim_t.terimabayarklaim_id, terimabayarklaim_t.no_terimabayarklaim, pengajuanklaim_t.total_piutang, terimabayarklaimdetail_t.pembayaran;
");

$this->execute('ALTER TABLE public.pengajuanklaimalokasi_v
  OWNER TO postgres;');

/*pengajuanklaimalokasidetail_v*/
$this->execute('DROP VIEW if exists public.pengajuanklaimalokasidetail_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.pengajuanklaimalokasidetail_v AS 
 SELECT pengajuan.pengajuanklaim_id,
    pengajuan.pengajuanklaimdetail_id,
    pengajuan.no_pengajuanklaim,
    pengajuan.pendaftaran_id,
    pengajuan.pasienadmisi_id,
    pengajuan.pasien_id,
    pengajuan.nama_pasien,
    pengajuan.no_rekam_medik,
    pengajuan.jumlah_bayar,
    pengajuan.jumlah_piutang,
    pengajuan.jumlah_telahbayar,
    pengajuan.jumlah_sisapiutang,
    pengajuan.tgl_pendaftaran,
    pengajuan.tglpasienpulang,
    pengajuan.no_pendaftaran,
    pengajuan.nosep,
    pengajuan.instalasi_id,
    pengajuan.instalasi_nama,
    pengajuan.ruangan_id,
    pengajuan.ruangan_nama,
    rincian_header_tagihan_pasien.total_tagihan,
    klaiminacbg_t.total_tarifrs
   FROM ( SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pendaftaran_t.bpjs_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
        UNION ALL
         SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pasienadmisi_t ON pengajuanklaimdetail_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pengajuan
     LEFT JOIN rincian_header_tagihan_pasien ON pengajuan.pendaftaran_id = rincian_header_tagihan_pasien.pendaftaran_id
     LEFT JOIN klaiminacbg_t ON pengajuan.pendaftaran_id = klaiminacbg_t.pendaftaran_id;
");

$this->execute('ALTER TABLE public.pengajuanklaimalokasidetail_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190924_065718_optimize_view_13 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190924_065718_optimize_view_13 cannot be reverted.\n";

        return false;
    }
    */
}
