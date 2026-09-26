<?php

use yii\db\Migration;

/**
 * Class m200701_122508_migrate_mhkn_20200701
 */
class m200701_122508_migrate_mhkn_20200701 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokobatalkes_t" ADD COLUMN "harga_netto_avg" float8 DEFAULT 0;');

        $this->execute('DROP VIEW if exists "public"."infokonsulpoli_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokonsulpoli_v\" AS  SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    konsulpoli_t.tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul,
    pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
    dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.status_konsul AS status_konsul_id,
    fgetnamalookup((konsulpoli_t.status_konsul)::integer) AS status_konsul,
    konsulpoli_t.status_approve AS status_approve_id,
    fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve
   FROM ((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
  WHERE ((konsulpoli_t.is_active = true) AND (konsulpoli_t.is_deleted = false));");

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
        CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN periksa_fisik_rj.berat
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.berat)::double precision
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.berat)::double precision
            ELSE NULL::double precision
        END AS berat_badan,
        CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN periksa_fisik_rj.tinggi
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.tinggi)::double precision
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.tinggi)::double precision
            ELSE NULL::double precision
        END AS tinggi_badan,
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
   FROM (((((((((((((penjualanresep_t
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
     LEFT JOIN ( SELECT pemeriksaanfisik_t.pendaftaran_id,
            pemeriksaanfisik_t.tinggibadan_cm AS tinggi,
            pemeriksaanfisik_t.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t
          WHERE (pemeriksaanfisik_t.is_deleted = false)) periksa_fisik_rj ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id)))
     LEFT JOIN ( SELECT asesmenperawatrd_t.pendaftaran_id,
            asesmenperawatrd_t.tinggi_badan AS tinggi,
            asesmenperawatrd_t.berat_badan AS berat
           FROM asesmenperawatrd_t
          WHERE (asesmenperawatrd_t.is_deleted = false)) periksa_fisik_rd ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id)))
     LEFT JOIN ( SELECT asesmenmedis_t.pendaftaran_id,
            asesmenmedis_t.tinggi_badan AS tinggi,
            asesmenmedis_t.berat_badan AS berat
           FROM asesmenmedis_t
          WHERE (asesmenmedis_t.is_deleted = false)) periksa_fisik_ri ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id)))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

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
    obatalkes_m.obatalkes_kode AS kode_item,
    terima.sub_total,
    terima.total_discount,
    terima.ppn_persen,
    terima.ppn_nilai,
    terima.total
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
            validasipoobat_t.pajak_id,
            validasipoobat_t.sub_total,
            validasipoobat_t.total_discount,
            validasipoobat_t.ppn_persen,
            validasipoobat_t.ppn_nilai,
            validasipoobat_t.total
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

        $this->execute('DROP VIEW if exists "public"."infopenerimaanobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopenerimaanobat_v\" AS  SELECT penerimaanobat_t.penerimaanobat_id,
    penerimaanobat_t.no_penerimaan,
    penerimaanobat_t.tgl_penerimaan,
    penerimaanobat_t.supplier_id,
    supplier_m.supplier_nama,
    validasipoobat_t.no_poobat,
    penerimaanobat_t.no_suratjalan,
    penerimaanobat_t.tgl_suratjalan,
    penerimaanobat_t.no_faktur,
    penerimaanobat_t.catatan,
    penerimaanobat_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS mengetahui,
    penerimaanobat_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS menyetujui,
    penerimaanobat_t.diterima_oleh,
    peg_menerima.nama_pegawai AS menerima,
    'Gudang Umum'::text AS nama_ruangan,
    penerimaanobat_t.is_verifikasi,
        CASE
            WHEN (penerimaanobat_t.is_verifikasi = 0) THEN 'belum verifikasi'::text
            WHEN (penerimaanobat_t.is_verifikasi = 1) THEN 'sudah verifikasi'::text
            WHEN (penerimaanobat_t.is_verifikasi = 2) THEN 'batal'::text
            ELSE NULL::text
        END AS verifikasi_nama,
    validasipoobat_t.pajak_id,
    pajak_m.pajak_persen,
    validasipoobat_t.sub_total,
    validasipoobat_t.total_discount,
    validasipoobat_t.ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobat_t.total,
    supplier_m.no_tlp,
    supplier_m.no_fax,
    supplier_m.supplier_alamat
   FROM ((((((penerimaanobat_t
     JOIN validasipoobat_t ON ((penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id)))
     JOIN supplier_m ON ((supplier_m.supplier_id = penerimaanobat_t.supplier_id)))
     LEFT JOIN pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
     LEFT JOIN pegawai_m peg_mengetahui ON ((penerimaanobat_t.peg_mengetahui = peg_mengetahui.pegawai_id)))
     LEFT JOIN pegawai_m peg_menyetujui ON ((penerimaanobat_t.peg_menyetujui = peg_menyetujui.pegawai_id)))
     LEFT JOIN pegawai_m peg_menerima ON ((penerimaanobat_t.diterima_oleh = peg_menerima.pegawai_id)));");

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
    ruangan_asal.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep,
    ruangan_m.lantai_id
   FROM (((((((((((((((((((((pendaftaran_t
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
     LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
            konsulpoli_t.asalpoliklinikkonsul_id,
            poli_asal.ruangan_nama
           FROM (konsulpoli_t
             LEFT JOIN ruangan_m poli_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id)))) ruangan_asal ON ((pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id)))
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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200701_122508_migrate_mhkn_20200701 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200701_122508_migrate_mhkn_20200701 cannot be reverted.\n";

        return false;
    }
    */
}
