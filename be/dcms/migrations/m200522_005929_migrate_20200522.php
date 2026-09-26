<?php

use yii\db\Migration;

/**
 * Class m200522_005929_migrate_20200522
 */
class m200522_005929_migrate_20200522 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	 $this->execute('DROP VIEW if exists "public"."tindakanoperasi_v";');

    	 $this->execute("
    	 	CREATE VIEW \"public\".\"tindakanoperasi_v\" AS  SELECT tindakanoperasi_mp.timoperasi_id,
    lookup_m.lookup_name AS timoperasi_nama,
    tindakanoperasi_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    lookup_m.lookup_value AS persentase,
    tindakanoperasi_mp.is_active
   FROM ((tindakanoperasi_mp
     JOIN daftartindakan_m ON ((tindakanoperasi_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN lookup_m ON ((tindakanoperasi_mp.timoperasi_id = lookup_m.lookup_id)));");

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
    inpostoperasidetail_t.is_penyulit
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
    inpostoperasidetail_t.is_penyulit
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
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.totalhargajual,
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihan,
    NULL::character varying AS nama_pembeli,
    penjualanresep_t.status_bayar,
    COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
    pasien_m.nama_pasien AS nama,
    NULL::character varying AS jenispenjualan_id,
    NULL::character varying AS jenispenjualan_nama
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
        END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama))
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
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenispenjualan_nama
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

    	 $this->execute("
    	 	CREATE VIEW \"public\".\"infoinpostoperasidetail_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    inpostoperasidetail_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    pasienmasukpenunjang_t.status_periksa,
    operasi_m.operasi_id,
    golonganoperasi_m.golonganoperasi_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id,
    jenisoperasi.golonganoperasi_nama AS jenis_operasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    inpostoperasidetail_t.harga,
    NULL::text AS tarif_satuan,
    NULL::text AS tarif_tindakan,
    inpostoperasidetail_t.dokter_id AS pegawai_id
   FROM ((((((((pasienmasukpenunjang_t
     JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
     JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
     LEFT JOIN operasi_m ON ((inpostoperasidetail_t.operasi_id = operasi_m.operasi_id)))
     LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
     LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)))
     LEFT JOIN daftartindakan_m ON ((inpostoperasidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)));");

    	 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_005929_migrate_20200522 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_005929_migrate_20200522 cannot be reverted.\n";

        return false;
    }
    */
}
