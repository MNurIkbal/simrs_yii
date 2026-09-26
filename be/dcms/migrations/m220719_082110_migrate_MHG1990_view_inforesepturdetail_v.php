<?php

use yii\db\Migration;

/**
 * Class m220719_082110_migrate_MHG1990_view_inforesepturdetail_v
 */
class m220719_082110_migrate_MHG1990_view_inforesepturdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."inforesepturdetail_v";
        '); 

        $this->execute('
            CREATE VIEW "public"."inforesepturdetail_v" AS  
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
                lkp_status_pendaftaran.lookup_name AS status_periksa_nama,
                reseptur_t.status_reseptur AS status_reseptur_id,
                lkp_status_reseptur.lookup_name AS status_reseptur,
                resepturdetail_t.is_deleted,
                resepturdetail_t.is_active,
                obatalkespasien_t.additional_data,
                obatalkespasien_t.hargasatuan_oa,
                resepturdetail_t.qty_konversi,
                resepturdetail_t.additional_data AS additional_reseptur,
                resepturdetail_t.additional_data::json ->> \'satuaninput_id\'::text AS satuaninput_id,
                resepturdetail_t.additional_data::json ->> \'satuan_input\'::text AS satuan_input,
                resepturdetail_t.additional_data::json ->> \'satuankonversi_id\'::text AS satuankonversi_id,
                resepturdetail_t.additional_data::json ->> \'satuan_konversi\'::text AS satuan_konversi,
                resepturdetail_t.additional_data::json ->> \'harga_konversi\'::text AS harga_konversi,
                resepturdetail_t.additional_data::json ->> \'nilai_konversi\'::text AS nilai_konversi,
                resepturdetail_t.det,
                resepturdetail_t.signa,
                    CASE
                        WHEN penjualanresep_t.status_bayar = 349 THEN false
                        ELSE true
                    END AS is_bayar,
                lkp_status_penjualanresep.lookup_name AS status_bayar,
                ruteobat_m.nama_rute,
                penjualanresep_t.noresep AS noresep_penjualan,
                retur.qty_retur,
                0 AS qty_pemberian,
                signaobat_m.qty_obat AS qty_signa,
                signaobat_m.iterasi AS iterasi_signa,
                false AS is_bpjs,
                obatalkes_m.obatalkes_kode AS kode_bpjs
               FROM resepturdetail_t
                 JOIN ( SELECT a.reseptur_id,
                        a.pendaftaran_id,
                        a.pasien_id,
                        a.noresep,
                        a.tglreseptur,
                        a.ruangan_id,
                        a.status_reseptur
                       FROM reseptur_t a) reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.status_periksa
                       FROM pendaftaran_t a) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT pasien_m_1.pasien_id,
                        pasien_m_1.no_rekam_medik,
                        pasien_m_1.nama_pasien
                       FROM pasien_m pasien_m_1) pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT a.obatalkes_id,
                        a.obatalkes_nama,
                        a.harganetto,
                        a.obatalkes_kode,
                        a.ruteobat_id
                       FROM obatalkes_m a) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT a.satuanunit_id,
                        a.satuanunit_nama
                       FROM satuanunit_m a) satuan_kecil ON resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
                 JOIN ( SELECT a.racikan_id,
                        a.racikan_nama
                       FROM racikan_m a) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
                 LEFT JOIN ( SELECT a.signa_id,
                        a.signa_nama,
                        a.qty_obat,
                        a.iterasi
                       FROM signaobat_m a) signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
                 JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                       FROM ruangan_m a) ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
                 LEFT JOIN ( SELECT a.resepturdetail_id,
                        a.interaksi,
                        a.duplikasi,
                        a.dosisi,
                        a.alergi,
                        a.kontradiksi,
                        a.review_note,
                        a.wkt_review,
                        a.pegawairotd_id
                       FROM rotd_t a) rotd_t ON resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON rotd_t.pegawairotd_id = pegawai_m.pegawai_id
                 LEFT JOIN ( SELECT a.reseptur_id,
                        a.status_bayar,
                        a.penjualanresep_id,
                        a.noresep
                       FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
                 LEFT JOIN ( SELECT a.obatalkespasien_id,
                        a.penjualanresep_id,
                        a.additional_data,
                        a.hargasatuan_oa,
                        a.obatalkes_id,
                        a.racikan_id
                       FROM obatalkespasien_t a) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id AND obatalkespasien_t.obatalkes_id = resepturdetail_t.obatalkes_id AND obatalkespasien_t.racikan_id = resepturdetail_t.racikan_id
                 JOIN ( SELECT a.persen_diskon,
                        a.persenppn,
                        a.is_deleted
                       FROM konfigfarmasi_k a) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                 LEFT JOIN ( SELECT a.ruteobat_id,
                        a.nama_rute
                       FROM ruteobat_m a) ruteobat_m ON obatalkes_m.ruteobat_id = ruteobat_m.ruteobat_id
                 LEFT JOIN ( SELECT returresepdetail_t.obatalkespasien_id,
                        sum(returresepdetail_t.qty_retur) AS qty_retur
                       FROM returresepdetail_t
                      GROUP BY returresepdetail_t.obatalkespasien_id) retur ON obatalkespasien_t.obatalkespasien_id = retur.obatalkespasien_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) lkp_status_pendaftaran ON pendaftaran_t.status_periksa::integer = lkp_status_pendaftaran.lookup_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) lkp_status_reseptur ON reseptur_t.status_reseptur = lkp_status_reseptur.lookup_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) lkp_status_penjualanresep ON penjualanresep_t.status_bayar::integer = lkp_status_penjualanresep.lookup_id
              WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220719_082110_migrate_MHG1990_view_inforesepturdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220719_082110_migrate_MHG1990_view_inforesepturdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
