<?php

use yii\db\Migration;

/**
 * Class m190930_064933_infopenjualanresepdetail_v
 */
class m190930_064933_infopenjualanresepdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    obatalkespasien_t.resepturdetail_id,
    resepturdetail_t.additional_data::json ->> 'satuaninput_id'::text AS satuaninput_id,
    resepturdetail_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    resepturdetail_t.additional_data::json ->> 'satuankonversi_id'::text AS satuankonversi_id,
    resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    resepturdetail_t.hargasatuan_reseptur,
    resepturdetail_t.harganetto_reseptur,
    resepturdetail_t.hargajual_reseptur,
    resepturdetail_t.qty_reseptur,
    resepturdetail_t.etiket AS etiket_reseptur
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
     LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
     LEFT JOIN satuanunit_m satuan_input ON resepturdetail_t.satuankecil_id = satuan_input.satuanunit_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;");
     
     $this->execute('ALTER TABLE public.infopenjualanresepdetail_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190930_064933_infopenjualanresepdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190930_064933_infopenjualanresepdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
