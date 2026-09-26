<?php

use yii\db\Migration;

/**
 * Class m190401_070949_kesimpulanrd_v
 */
class m190401_070949_kesimpulanrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW kesimpulanrd_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW kesimpulanrd_v AS 
             SELECT kesimpulanrd_t.kesimpulanrd_id,
                kesimpulanrd_t.pendaftaran_id,
                kesimpulanrd_t.pasienpulang_id,
                pasienpulang_t.carakeluar_id,
                carakeluar_m.carakeluar_nama,
                kondisikeluar_m.kondisikeluar_nama,
                pasienpulang_t.tglpasienpulang,
                pasienpulang_t.tgl_meninggal,
                kesimpulanrd_t.instruksi_lanjutan,
                kesimpulanrd_t.tgl_lanjut_rawat,
                kesimpulanrd_t.poliklinik_id,
                kesimpulanrd_t.dokter_id,
                kesimpulanrd_t.kondisi,
                kesimpulanrd_t.hr,
                kesimpulanrd_t.rr,
                kesimpulanrd_t.spo2,
                kesimpulanrd_t.t,
                kesimpulanrd_t.gcs_eye_id,
                eye.metodegcs_nilai AS nilai_eye,
                kesimpulanrd_t.gcs_verbal_id,
                verbal.metodegcs_nilai AS nilai_verbal,
                kesimpulanrd_t.gcs_motorik_id,
                motorik.metodegcs_nilai AS nilai_motorik,
                kesimpulanrd_t.hasil_gcs,
                kesimpulanrd_t.gcs_kategori,
                kesimpulanrd_t.is_kapitis,
                kesimpulanrd_t.reseptur_id,
                eye.metodegcs_nama AS gcs_eye_nama,
                verbal.metodegcs_nama AS gcs_verbal_nama,
                motorik.metodegcs_nama AS gcs_motorik_nama,
                dokter.nama_pegawai AS dokter_pulang,
                poliklinik.ruangan_nama AS poliklinik_nama,
                to_json(inforeseptur.*) AS info_resep,
                array_to_json(ARRAY( SELECT to_json(inforesepturdetail.*) AS to_json
                       FROM ( SELECT resepturdetail_t.resepturdetail_id,
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
                                obatalkes_v.harganetto_ygdipakai AS harganetto,
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
                                status_periksa.lookup_name AS status_periksa_nama,
                                reseptur_t.status_reseptur AS status_reseptur_id,
                                status_reseptur.lookup_name AS status_reseptur,
                                resepturdetail_t.is_deleted,
                                resepturdetail_t.is_active,
                                obatalkespasien_t.additional_data,
                                obatalkespasien_t.hargasatuan_oa
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
                                 JOIN obatalkes_v ON resepturdetail_t.obatalkes_id = obatalkes_v.obatalkes_id
                                 JOIN lookup_m status_periksa ON pendaftaran_t.status_periksa::integer = status_periksa.lookup_id
                                 JOIN lookup_m status_reseptur ON reseptur_t.status_reseptur = status_reseptur.lookup_id
                                 JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                              WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true) inforesepturdetail(resepturdetail_id, reseptur_id, pendaftaran_id, pasien_id, obatalkes_id, satuankecil_id, racikan_id, signa_id, no_pendaftaran, no_rekam_medik, nama_pasien, noresep, tglreseptur, racikan_nama, r, rke, obatalkes_nama, qty_reseptur, satuan_kecil, hargajual_satuan, totalharga_jual, etiket, iter, signa_nama, ruangantujuan_id, ruangan_tujuan, harganetto, harganetto_1, interaksi, duplikasi, dosisi, alergi, kontradiksi, review_note, wkt_review, nama_pegawai, obatalkespasien_id, harga_netto, harga_jual, margin, hn_margin, disc, hn_diskon, ppn, hn_ppn, status_periksa, status_periksa_nama, status_reseptur_id, status_reseptur, is_deleted, is_active, additional_data, hargasatuan_oa)
                      WHERE inforesepturdetail.reseptur_id = kesimpulanrd_t.reseptur_id)) AS detail_resep
               FROM kesimpulanrd_t
                 JOIN pasienpulang_t ON kesimpulanrd_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                 JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
                 JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
                 LEFT JOIN metodegcs_m eye ON kesimpulanrd_t.gcs_eye_id = eye.metodegcs_id
                 LEFT JOIN metodegcs_m verbal ON kesimpulanrd_t.gcs_verbal_id = verbal.metodegcs_id
                 LEFT JOIN metodegcs_m motorik ON kesimpulanrd_t.gcs_motorik_id = motorik.metodegcs_id
                 LEFT JOIN pegawai_m dokter ON kesimpulanrd_t.dokter_id = dokter.pegawai_id
                 LEFT JOIN ruangan_m poliklinik ON kesimpulanrd_t.poliklinik_id = poliklinik.ruangan_id
                 LEFT JOIN ( SELECT reseptur_t.reseptur_id,
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
                        reseptur_t.noresep,
                        reseptur_t.penjualanresep_id,
                        pendaftaran_t.no_pendaftaran,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasien_m.tanggal_lahir,
                        jk.lookup_name AS jenis_kelamin,
                        carabayar_m.carabayar_nama,
                        penjamin_m.penjamin_nama,
                        ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
                        ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
                        status_reseptur.lookup_name AS status_reseptur,
                        reseptur_t.pegawai_id,
                        pegawai_m.nama_pegawai,
                        ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
                        instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
                        ruangan_tujuan.instalasi_id AS instalasi_tujuan_id,
                        instalasi_tujuan.instalasi_nama AS instalasi_tujuan,
                        sum(obatalkes_m.harganetto) AS total_harganetto,
                        antrian_t.no_antrian,
                        reseptur_t.status_reseptur AS status_reseptur_id,
                        reseptur_t.is_hamil,
                        reseptur_t.berat_badan,
                        reseptur_t.tinggi_badan,
                        reseptur_t.luas_tubuh,
                        reseptur_t.diagnosa_id,
                        diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
                        reseptur_t.instruksi_id,
                        reseptur_t.antrian_id,
                        string_agg(resepturdetail_t.racikan_id::text, \'-\'::text) AS antrian_racikan,
                        penjualanresep_t.catatan,
                        iter.iter,
                        penjualanresep_t.noresep AS noresep_penjualan,
                        penjualanresep_t.iter AS iter_penjualan
                       FROM reseptur_t
                         JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
                         JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
                         JOIN ruangan_m ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
                         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                         JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                         JOIN lookup_m status_reseptur ON reseptur_t.status_reseptur = status_reseptur.lookup_id
                         JOIN pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
                         JOIN instalasi_m instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
                         JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
                         JOIN resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
                         JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                         LEFT JOIN antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
                         LEFT JOIN diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
                         JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
                         JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                         LEFT JOIN penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                         JOIN ( SELECT resepturdetail_t_1.reseptur_id,
                                resepturdetail_t_1.iter
                               FROM resepturdetail_t resepturdetail_t_1
                              GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON iter.reseptur_id = reseptur_t.reseptur_id
                      WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true
                      GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, jk.lookup_name, diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, status_reseptur.lookup_name, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.status_reseptur, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, iter.iter, penjualanresep_t.noresep, penjualanresep_t.iter) inforeseptur ON kesimpulanrd_t.reseptur_id = inforeseptur.reseptur_id;
        ');

        $this->execute('
            ALTER TABLE kesimpulanrd_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_070949_kesimpulanrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_070949_kesimpulanrd_v cannot be reverted.\n";

        return false;
    }
    */
}
