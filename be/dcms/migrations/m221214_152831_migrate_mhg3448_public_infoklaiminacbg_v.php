<?php

use yii\db\Migration;

/**
 * Class m221214_152831_migrate_mhg3448_public_infoklaiminacbg_v
 */
class m221214_152831_migrate_mhg3448_public_infoklaiminacbg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infoklaiminacbg_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infoklaiminacbg_v
            AS SELECT
                    CASE
                        WHEN klaiminacbg_t.instalasi_id = 3 THEN 'RI'::text
                        ELSE 'RJ'::text
                    END AS tipe,
                klaiminacbg_t.instalasi_id,
                klaiminacbg_t.klaiminacbg_id,
                klaimgroup_t.klaimgroup_id,
                klaiminacbg_t.tgl_masuk,
                klaiminacbg_t.tgl_keluar,
                klaimgroup_t.created_date AS tgl_group,
                klaiminacbg_t.no_sep,
                klaiminacbg_t.no_rekam_medik,
                klaiminacbg_t.nama_pasien,
                klaiminacbg_t.diagnosa_primer,
                klaiminacbg_t.diagnosa_sekunder,
                klaimgroup_t.spesial_procedure,
                klaimgroup_t.spesial_prosthesis,
                klaimgroup_t.spesial_investigation,
                klaimgroup_t.spesial_drug,
                klaiminacbg_t.is_terkirim,
                    CASE
                        WHEN klaiminacbg_t.is_terkirim = false THEN '-'::text
                        ELSE 'Terkirim'::text
                    END AS status_kirim,
                jenis_tarif.lookup_name AS jenis_tarif,
                klaiminacbg_t.pendaftaran_id,
                klaiminacbg_t.pasienadmisi_id,
                klaiminacbg_t.nama_dokter,
                klaiminacbg_t.carapulang_id,
                cara_keluar.lookup_name AS cara_keluar,
                klaiminacbg_t.prosedur_bedah,
                klaiminacbg_t.prosedur_nonbedah,
                klaiminacbg_t.konsultasi,
                klaiminacbg_t.tenaga_ahli,
                klaiminacbg_t.keperawatan,
                klaiminacbg_t.penunjang,
                klaiminacbg_t.radiologi,
                klaiminacbg_t.laboratorium,
                klaiminacbg_t.pelayanan_darah,
                klaiminacbg_t.rehabilitasi,
                klaiminacbg_t.kamar_akomodasi,
                klaiminacbg_t.rawat_intensif,
                klaiminacbg_t.obat,
                klaiminacbg_t.alkes,
                klaiminacbg_t.bmhp,
                klaiminacbg_t.sewa_alat,
                klaiminacbg_t.obat_kemoterapi,
                klaiminacbg_t.obat_kronis,
                klaiminacbg_t.is_naikkelas,
                klaiminacbg_t.lama_naikkelas,
                klaiminacbg_t.is_kelasintensif,
                klaiminacbg_t.kelas_intensif,
                klaiminacbg_t.lama_kelasintensif,
                klaiminacbg_t.ventilator,
                klaiminacbg_t.los,
                klaiminacbg_t.adl_cronic,
                klaiminacbg_t.adl_subacute,
                klaimgroup_t.cbg,
                klaimgroup_t.special_group,
                klaiminacbg_t.total_tarifrs,
                klaimgroup_t.total AS tarif_klaim,
                sy_kunjungan.carabayar_nama AS cara_bayar,
                sy_kunjungan.penjamin_nama AS penjamin,
                sy_kunjungan.no_asuransi,
                    CASE
                        WHEN klaiminacbg_t.is_naikkelas = true AND klaiminacbg_t.naik_kelas::text = 'kelas_1'::text THEN 'kelas 1'::text
                        WHEN klaiminacbg_t.is_naikkelas = true AND klaiminacbg_t.naik_kelas::text = 'kelas_2'::text THEN 'kelas 2'::text
                        WHEN klaiminacbg_t.is_naikkelas = true AND klaiminacbg_t.naik_kelas::text = 'kelas_3'::text THEN 'kelas 3'::text
                        WHEN klaiminacbg_t.is_naikkelas = true AND klaiminacbg_t.naik_kelas::text = 'kelas_4'::text THEN 'kelas VIP'::text
                        WHEN klaiminacbg_t.is_naikkelas = true AND klaiminacbg_t.naik_kelas::text = 'kelas_5'::text THEN 'kelas VVIP'::text
                        ELSE klaimgroup_t.additional_data::json ->> 'kelas_awal'::text
                    END AS kelas,
                klaimgroup_t.additional_data::json ->> 'kelas_awal'::text AS kelas_hak,
                sy_kunjungan.umur,
                    CASE
                        WHEN klaiminacbg_t.instalasi_id = 3 THEN 'Rawat Inap'::text
                        ELSE 'Rawat Jalan'::text
                    END AS jenis_rawat,
                klaimgroup_t.group_tarif,
                klaimgroup_t.group_nama,
                klaimgroup_t.sp_procedure_kode,
                klaimgroup_t.sp_prosthesis_kode,
                klaimgroup_t.sp_investigation_kode,
                klaimgroup_t.sp_drug_kode,
                klaimgroup_t.sp_procedure_nama,
                klaimgroup_t.sp_prosthesis_nama,
                klaimgroup_t.sp_investigation_nama,
                klaimgroup_t.sp_drug_nama,
                klaiminacbg_t.status_klaim,
                klaimgroup_t.additional_data::json ->> 'info'::text AS info,
                klaiminacbg_t.tarif_polieksekutif,
                klaiminacbg_t.jenis_kelasrawat AS kelas_id,
                klaiminacbg_t.tarif,
                klaimgroup_t.tambahan_biaya,
                klaimgroup_t.persen_tambahan,
                klaimgroup_t.total_kelaspelayanan,
                klaimgroup_t.total_naikkelas,
                klaiminacbg_t.berat_lahir,
                klaiminacbg_t.status_covid,
                klaiminacbg_t.is_komplikasi,
                klaiminacbg_t.is_pemulasaranjenazah,
                klaiminacbg_t.is_kantongjenazah,
                klaiminacbg_t.is_petijenazah,
                klaiminacbg_t.is_plastikerat,
                klaiminacbg_t.is_desinfektanjenazah,
                klaiminacbg_t.is_transport,
                klaiminacbg_t.is_desinfektanmobil
            FROM klaiminacbg_t
                LEFT JOIN klaimgroup_t ON klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id
                JOIN sy_kunjungan ON klaiminacbg_t.pendaftaran_id = sy_kunjungan.kunjungan_id
                LEFT JOIN carakeluar_m ON sy_kunjungan.carakeluar_kode::text = carakeluar_m.carakeluar_kode::text
                LEFT JOIN lookup_m jenis_tarif ON klaiminacbg_t.tarif::text = jenis_tarif.lookup_value::text AND jenis_tarif.lookup_type::text = 'kode_tarifbpjs'::text
                LEFT JOIN lookup_m cara_keluar ON klaiminacbg_t.carapulang_id::text = cara_keluar.lookup_value::text AND cara_keluar.lookup_type::text = 'carapulang_inacbg'::text
            WHERE klaiminacbg_t.is_deleted = false
            ORDER BY klaiminacbg_t.klaiminacbg_id DESC;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221214_152831_migrate_mhg3448_public_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221214_152831_migrate_mhg3448_public_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }
    */
}
