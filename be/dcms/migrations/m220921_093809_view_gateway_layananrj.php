<?php

use yii\db\Migration;

/**
 * Class m220921_093809_view_gateway_layananrj
 */
class m220921_093809_view_gateway_layananrj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_layananrj_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_layananrj_v\"
        AS SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran AS waktu_pendaftaran,
            pendaftaran_t.tgl_pendaftaran AS waktu_masuk,
            pasienpulang_t.tglpasienpulang AS waktu_keluar,
            ruangan_m.ruangan_nama AS poliklinik,
            pasien_m.no_identitas_pasien AS no_identias,
            pasien_m.no_rekam_medik AS no_rekammedik,
            pasien_m.nama_pasien,
            pendaftaran_t.keterangan_pendaftaran AS keterangan,
            penjamin_m.penjamin_nama AS penjamin,
            carabayar_m.carabayar_nama AS cara_bayar,
            COALESCE(asalrujukan_m.asalrujukan_nama, 'Datang Sendiri'::character varying) AS asal_rujukan,
            keadaan_masuk.lookup_name AS keadaan_masuk,
            COALESCE(soaprj_t.data_pemeriksaan, pendaftaran_periksa.data_pemeriksaan) AS data_pemeriksaan,
            COALESCE(pemeriksaanfisik_t.keluhan_utama, anamnesa_t.keluhan_utama) AS keluhan_utama,
            COALESCE(pemeriksaanfisik_t.berat_badan::character varying, anamnesa_t.berat_badan) AS berat_badan,
            COALESCE(pemeriksaanfisik_t.tinggi_badan::character varying, anamnesa_t.tinggi_badan) AS tinggi_badan,
            COALESCE(pemeriksaanfisik_t.nadi::character varying, anamnesa_t.nadi) AS nadi,
            COALESCE(pemeriksaanfisik_t.rr, anamnesa_t.rr::text) AS respiration_rate,
            COALESCE(pemeriksaanfisik_t.td_systolic, anamnesa_t.td_systolic) AS td_systolic,
            COALESCE(pemeriksaanfisik_t.td_diastolic, anamnesa_t.td_diastolic) AS td_diastolic,
            COALESCE(pemeriksaanfisik_t.suhu::character varying, anamnesa_t.suhu) AS suhu,
            diagnosa.diagnosa_utama AS diag_utama,
            diagnosa.diagnosa_penyerta AS diag_penunjang,
            tindakanpelayanan_t.data_tindakan
        FROM pendaftaran_t
            LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            JOIN ( SELECT a.pasien_id,
                    a.no_identitas_pasien,
                    a.no_rekam_medik,
                    a.nama_pasien
                FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                FROM ruangan_m a
                WHERE a.instalasi_id = 1) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
            LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
            LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id
                FROM rujukan_t a) rujukan_m ON pendaftaran_t.rujukan_id = rujukan_m.rujukan_id
            LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                FROM asalrujukan_m a) asalrujukan_m ON rujukan_m.asalrujukan_id = asalrujukan_m.asalrujukan_id
            LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) keadaan_masuk ON pendaftaran_t.keadaan_masuk::integer = keadaan_masuk.lookup_id
            LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                        FROM ( SELECT a.tindakanpelayanan_id,
                                    a.pendaftaran_id,
                                    a.daftartindakan_id,
                                    a.tgl_tindakan::text AS tgl_tindakan,
                                    a.qty_tindakan,
                                    a.tarif_satuan,
                                    daftartindakan_m.daftartindakan_nama,
                                    kelompoktindakan_m.kelompoktindakan_nama,
                                    a.dokterpenanggungjawab_id,
                                    a.perawat1_id
                                FROM tindakanpelayanan_t a
                                    LEFT JOIN daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                                WHERE a.is_deleted = false AND a.is_active = true AND pendaftaran.pendaftaran_id = a.pendaftaran_id) d) AS data_tindakan
                FROM pendaftaran_t pendaftaran) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
            LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.keluhan_utama,
                    a.berat_badan,
                    a.tinggi_badan,
                    a.nadi,
                    a.rr,
                    a.td,
                    a.suhu,
                    split_part(a.nadi::text, '/'::text, 1) AS td_systolic,
                    split_part(a.nadi::text, '/'::text, 2) AS td_diastolic
                FROM anamnesa_t a
                    JOIN ( SELECT max(anamnesa_t_1.anamesa_id) AS anamesa_id,
                            anamnesa_t_1.pendaftaran_id
                        FROM anamnesa_t anamnesa_t_1
                        GROUP BY anamnesa_t_1.pendaftaran_id) last_anamnesa ON a.anamesa_id = last_anamnesa.anamesa_id) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
            LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.keluhan_utama,
                    a.beratbadan_kg AS berat_badan,
                    a.tinggibadan_cm AS tinggi_badan,
                    a.detaknadi AS nadi,
                    a.pernapasan AS rr,
                    a.tekanandarah AS td,
                    a.suhutubuh AS suhu,
                    split_part(a.tekanandarah::text, '/'::text, 1) AS td_systolic,
                    split_part(a.tekanandarah::text, '/'::text, 2) AS td_diastolic
                FROM pemeriksaanfisik_t a
                    JOIN ( SELECT max(pemeriksaanfisik_t_1.pemeriksaanfisik_id) AS pemeriksaanfisik_id,
                            pemeriksaanfisik_t_1.pendaftaran_id
                        FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
                        GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) last_pemeriksaanfisik ON a.pemeriksaanfisik_id = last_pemeriksaanfisik.pemeriksaanfisik_id) pemeriksaanfisik_t ON pendaftaran_t.pendaftaran_id = pemeriksaanfisik_t.pendaftaran_id
            LEFT JOIN ( SELECT soaprj.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                        FROM ( SELECT a.tgl_soaprj::text AS tgl_periksa,
                                    dokter.nama_pegawai AS dokter,
                                    dokter.nomorindukpegawai AS nik_dokter,
                                    perawat.nama_pegawai AS perawat,
                                    perawat.nomorindukpegawai AS nik_perawat
                                FROM soaprj_t a
                                    LEFT JOIN ( SELECT b.pegawai_id,
                                            b.nama_pegawai,
                                            b.nomorindukpegawai
                                        FROM pegawai_m b
                                        WHERE b.kelompokpegawai_id = 1) dokter ON a.pegawai_id = dokter.pegawai_id
                                    LEFT JOIN ( SELECT b.pegawai_id,
                                            b.nama_pegawai,
                                            b.nomorindukpegawai
                                        FROM pegawai_m b
                                        WHERE b.kelompokpegawai_id = 2) perawat ON a.pegawai_id = perawat.pegawai_id
                                WHERE soaprj.pendaftaran_id = a.pendaftaran_id) d) AS data_pemeriksaan
                FROM soaprj_t soaprj
                GROUP BY soaprj.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
            LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.a_diag_utama AS diagnosa_utama,
                    a.a_diag_penyerta AS diagnosa_penyerta
                FROM soaprj_t a
                    JOIN ( SELECT max(soaprj_t_1.soaprj_id) AS soaprj_id,
                            soaprj_t_1.pendaftaran_id
                        FROM soaprj_t soaprj_t_1
                        WHERE soaprj_t_1.is_deleted IS FALSE
                        GROUP BY soaprj_t_1.pendaftaran_id) last_soaprj ON a.soaprj_id = last_soaprj.soaprj_id
                WHERE a.is_deleted IS FALSE) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
            LEFT JOIN ( SELECT a.pendaftaran_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                        FROM ( SELECT pendaftaran_t_1.tgl_masukperiksa::text AS tgl_periksa,
                                    dokter.nama_pegawai AS dokter,
                                    dokter.nomorindukpegawai AS nik_dokter,
                                    perawat.nama_pegawai AS perawat,
                                    perawat.nomorindukpegawai AS nik_perawat
                                FROM pendaftaran_t pendaftaran_t_1
                                    LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                                            pegawai_m.nama_pegawai,
                                            pegawai_m.nomorindukpegawai
                                        FROM pegawai_m
                                        WHERE pegawai_m.kelompokpegawai_id = 1) dokter ON pendaftaran_t_1.pegawai_id = dokter.pegawai_id
                                    LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                                            pegawai_m.nama_pegawai,
                                            pegawai_m.nomorindukpegawai
                                        FROM pegawai_m
                                        WHERE pegawai_m.kelompokpegawai_id = 2) perawat ON pendaftaran_t_1.pegawai_id = perawat.pegawai_id
                                WHERE pendaftaran_t_1.tgl_masukperiksa IS NOT NULL AND pendaftaran_t_1.instalasi_id = 1 AND a.pendaftaran_id = pendaftaran_t_1.pendaftaran_id) d) AS data_pemeriksaan
                FROM pendaftaran_t a
                WHERE a.instalasi_id = 1) pendaftaran_periksa ON pendaftaran_t.pendaftaran_id = pendaftaran_periksa.pendaftaran_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_093809_view_gateway_layananrj cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_093809_view_gateway_layananrj cannot be reverted.\n";

        return false;
    }
    */
}
