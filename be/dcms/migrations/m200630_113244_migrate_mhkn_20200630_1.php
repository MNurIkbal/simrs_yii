<?php

use yii\db\Migration;

/**
 * Class m200630_113244_migrate_mhkn_20200630_1
 */
class m200630_113244_migrate_mhkn_20200630_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopenerimaanobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopenerimaanobatdetail_v\" AS  SELECT terima.penerimaanobat_id,
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
    obatalkes_m.obatalkes_kode AS kode_item
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
            validasipoobat_t.pajak_id
           FROM ((penerimaanobat_t
             JOIN penerimaanobatdetail_t ON ((penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id)))
             JOIN validasipoobat_t ON ((penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id)))
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
          GROUP BY returpenerimaanobatdetail_t.penerimaanobatdetail_id) returdetailjumlah ON ((terima.penerimaanobatdetail_id = returdetailjumlah.penerimaanobatdetail_id)));");

        $this->execute('DROP VIEW if exists "public"."infokunjunganrj_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrj_v\" AS  SELECT 'PENDAFTARAN'::text AS jenis,
    pasien_m.pasien_id,
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
    pendaftaran_t.status_periksa,
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
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa1,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep,
    ruangan_m.lantai_id
   FROM ((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE (pendaftaran_t.instalasi_id = 1)
UNION
 SELECT 'KONSUL'::text AS jenis,
    pasien_m.pasien_id,
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
    konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
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
    konsulpoli_t.status_periksa,
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
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    konsulpoli_t.konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa1,
    konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruanganasal_m.ruangan_nama AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep,
    ruangan_m.lantai_id
   FROM ((((((((((((((((((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruanganasal_m ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NULL));");

        $this->execute('CREATE TABLE "public"."instrumenoperasi_t" (
  "instrumenoperasi_id" serial4,
  "pasienmasukpenunjang_id" int4 NOT NULL,
  "obatalkes_id" int4 NOT NULL,
  "satuan_id" int4,
  "persediaan" int4,
  "tambahan" int4,
  "terpakai" int4,
  "sisa" int4,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "pk_instrumenoperasi" PRIMARY KEY ("instrumenoperasi_id")
)
;');

        $this->execute('CREATE TABLE "public"."tindakanluaroperasi_t" (
  "tindakanluaroperasi_id" serial4,
  "pasienmasukpenunjang_id" int4 NOT NULL,
  "daftartindakan_id" int4 NOT NULL,
  "harga" float8,
  "qty" int4,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "pk_tindakanluaroperasi" PRIMARY KEY ("tindakanluaroperasi_id")
)
;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200630_113244_migrate_mhkn_20200630_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200630_113244_migrate_mhkn_20200630_1 cannot be reverted.\n";

        return false;
    }
    */
}
