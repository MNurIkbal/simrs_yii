<?php

use yii\db\Migration;

/**
 * Class m200624_102401_migrate_mhkn_20200624
 */
class m200624_102401_migrate_mhkn_20200624 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienmasukpenunjang_t" ADD COLUMN "kamarruangan_id" int4;');

        $this->execute('DROP VIEW if exists "public"."inforesep_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforesep_v\" AS  SELECT 'reseptur'::text AS jenis,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    ruangan_tujuan.ruangan_nama AS ruangan_reseptur,
        CASE
            WHEN (penjualanresep_t.penjualanresep_id IS NULL) THEN 'Belum Proses'::character varying
            WHEN (penjualanresep_t.status_reseptur = 347) THEN 'Dalam Proses'::character varying
            ELSE fgetnamalookup((penjualanresep_t.status_reseptur)::integer)
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
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
            ELSE asesmenawal_t.nama_alergi
        END AS riwayat_alergi,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
            WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END AS diagnosa_text,
    string_agg((resepturdetail_t.racikan_id)::text, '-'::text) AS antrian_racikan,
    sum(obatalkes_m.harganetto) AS total_harganetto,
        CASE
            WHEN (reseptur_t.penjualanresep_id IS NULL) THEN reseptur_t.biaya_administrasi
            ELSE penjualanresep_t.biayaadministrasi
        END AS biayaadministrasi,
    penjualanresep_t.totalhargajual,
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihan,
    NULL::character varying AS nama_pembeli,
    penjualanresep_t.status_bayar,
    COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
    pasien_m.nama_pasien AS nama,
    NULL::character varying AS jenispenjualan_id,
    NULL::character varying AS jenispenjualan_nama,
    reseptur_t.status_worklist
   FROM ((((((((((((((((((((reseptur_t
     LEFT JOIN penjualanresep_t ON (((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id) AND (penjualanresep_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
     JOIN instalasi_m instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
     JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (resepturdetail_t.is_deleted = false))))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
     LEFT JOIN diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
     LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
     LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
     LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
     LEFT JOIN ( SELECT instruksi_t.instruksi_id,
            cppt_t.cppt_id,
            cppt_t.pendaftaran_id,
            (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
           FROM (instruksi_t
             JOIN cppt_t ON (((instruksi_t.cppt_id = cppt_t.cppt_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true))))
          WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
  WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true))
  GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep, penjualanresep_t.tglresep, penjualanresep_t.penjualanresep_id,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
            ELSE asesmenawal_t.nama_alergi
        END,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
            WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama)), reseptur_t.status_worklist
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_resep.ruangan_nama AS ruangan_tujuan,
    NULL::text AS ruangan_reseptur,
        CASE
            WHEN (penjualanresep_t.status_reseptur = 347) THEN 'Dalam Proses'::character varying
            ELSE fgetnamalookup((penjualanresep_t.status_reseptur)::integer)
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
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihan,
    penjualanresep_t.nama_pembeli,
    penjualanresep_t.status_bayar,
    penjualanresep_t.tglresep AS tgl_resep_dibuat,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pasien_m.nama_pasien
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE NULL::character varying
        END AS nama,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenispenjualan_nama,
    penjualanresep_t.status_worklist
   FROM ((((((((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangan_resep ON ((penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id)))
     JOIN instalasi_m instalasi_resep ON ((ruangan_resep.instalasi_id = instalasi_resep.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((penjualanresep_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN antrian_t ON ((penjualanresep_t.antrian_id = antrian_t.antrian_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

        $this->execute('DROP VIEW if exists "public"."inforesepdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforesepdetail_v\" AS  SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
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
    rotd_t.dosisi AS dosis,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (((resepturdetail_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((resepturdetail_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    (0)::double precision AS biayaadministrasiresep,
    (0)::double precision AS totalhargajualresep,
    (0)::double precision AS totaltagihanresep,
    NULL::character varying AS nama_pembeli,
    resepturdetail_t.qty_reseptur AS qty_oa,
    reseptur_t.ruanganreseptur_id AS ruanganasal_id,
    ruangan_asal.instalasi_id AS instalasiasal_id,
    resepturdetail_t.det
   FROM (((((((((((((resepturdetail_t
     JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
     LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
     LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
  WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true))
UNION ALL
 SELECT 'resep'::text AS jenis,
    obatalkespasien_t.resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    NULL::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::integer AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    signaobat_m.signa_nama,
    penjualanresep_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    NULL::character varying AS interaksi,
    NULL::character varying AS duplikasi,
    NULL::character varying AS dosis,
    NULL::character varying AS alergi,
    NULL::character varying AS kontradiksi,
    NULL::character varying AS review_note,
    NULL::timestamp without time zone AS wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (((obatalkespasien_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((obatalkespasien_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
    penjualanresep_t.totalhargajual AS totalhargajualresep,
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihanresep,
    penjualanresep_t.nama_pembeli,
    obatalkespasien_t.qty_oa,
    penjualanresep_t.ruangan_id AS ruanganasal_id,
    ruangan_tujuan.instalasi_id AS instalasiasal_id,
    obatalkespasien_t.det
   FROM ((((((((((obatalkespasien_t
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true));");

        $this->execute('DROP VIEW if exists "public"."historireturtagihan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"historireturtagihan_v\" AS  SELECT returtagihan_r.pembayaran_id,
    returtagihan_r.no_tagihan,
    pasien_m.no_rekam_medik,
    peg_pembayaran.nama_pegawai AS pegawai_pembayaran,
    peg_retur.nama_pegawai AS pegawai_retur,
    returtagihan_r.tgl_returtagihan,
    returtagihan_r.total_returtagihan,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT returtagihandetail_r.returtagihandetail_id,
                    returtagihandetail_r.returtagihan_id,
                    returtagihandetail_r.tindakanpelayanan_id,
                    returtagihandetail_r.obatalkespasien_id,
                    returtagihandetail_r.nama_tagihan,
                    returtagihandetail_r.qty_tagihan,
                    returtagihandetail_r.tarif_tagihan,
                    returtagihandetail_r.additional_data,
                    returtagihandetail_r.created_date,
                    returtagihandetail_r.created_by,
                    returtagihandetail_r.modified_count,
                    returtagihandetail_r.last_modified_date,
                    returtagihandetail_r.last_modified_by,
                    returtagihandetail_r.is_deleted,
                    returtagihandetail_r.is_active,
                    returtagihandetail_r.deleted_date,
                    returtagihandetail_r.deleted_by
                   FROM returtagihandetail_r
                  WHERE (returtagihandetail_r.returtagihan_id = returtagihan_r.returtagihan_id)) d2) AS detail_tagihan
   FROM (((((returtagihan_r
     JOIN pembayaran_t ON ((returtagihan_r.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m peg_pembayaran ON ((returtagihan_r.pegawaipembayaran_id = peg_pembayaran.pegawai_id)))
     LEFT JOIN pegawai_m peg_retur ON ((returtagihan_r.pegawairetur_id = peg_retur.pegawai_id)));");

        $this->execute('DROP VIEW if exists "public"."infotagihanpasien_v";');
        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpasien_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    (tagihan.tarif_satuan)::integer AS tarif_satuan,
    tagihan.qty,
    (tagihan.tarif_cyto)::integer AS tarif_cyto,
    (tagihan.sub_total)::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_deleted,
    carabayar_m.groupcarabayar_id,
    tagihan.penjualanresep_id,
    tagihan.is_valid,
    tagihan.is_cyto,
    tagihan.pasienadmisi_id,
    tagihan.implementasi_id
   FROM (((((((( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id
           FROM (((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN (pendaftaran_t.instalasi_id = 21) THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id
           FROM ((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id
           FROM ((pendaftaran_t
             JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))) tagihan
     LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((tagihan.carabayar_pelayanan_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m dokter_dpjp ON ((tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id)))
  WHERE ((tagihan.tindakansudahbayar_id IS NULL) AND (tagihan.is_deleted = false))
  ORDER BY tagihan.tgl_pelayanan DESC;");

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    (cppt_t.a_diag_utama ->> 'text'::text) AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM (((((((((((((((((pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true) AND (cppt_t.is_instruksi_pulang = false))))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.instalasi_id = 12))
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM (((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ruangan_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (ruangan_penunjang.instalasi_id = 12) AND (pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (pendaftaran_t.instalasi_id <> 12));");

        $this->execute('DROP VIEW if exists "public"."infopasienoperasidetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasidetail_v\" AS  SELECT 'NON_PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    tindakanpelayanan_t.tipepaket_id,
    ''::character varying AS tipepaket_nama,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    operasi_m.operasi_id,
    golonganoperasi_m.golonganoperasi_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id,
    jenisoperasi.golonganoperasi_nama AS jenis_operasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    pegawai_m.pegawai_id
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
     LEFT JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
     LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     LEFT JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
     LEFT JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
     LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
     LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)))
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    operasi_m.operasi_id,
    golonganoperasi_m.golonganoperasi_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id,
    jenisoperasi.golonganoperasi_nama AS jenis_operasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    pegawai_m.pegawai_id
   FROM ((((((((((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
     LEFT JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
     LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     LEFT JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
     LEFT JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
     LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
     LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)));");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200624_102401_migrate_mhkn_20200624 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200624_102401_migrate_mhkn_20200624 cannot be reverted.\n";

        return false;
    }
    */
}
