<?php

use yii\db\Migration;

/**
 * Class m230405_034649_migrate_sy_infoklaiminacbg_v
 */
class m230405_034649_migrate_sy_infoklaiminacbg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."sy_infoklaiminacbg_v";');
        $this->execute("CREATE OR REPLACE VIEW public.sy_infoklaiminacbg_v
        AS SELECT sy_kunjungan.instalasi_kode AS tipe,
            sy_kunjungan.instalasi_kode,
            sy_klaiminacbg.sy_klaiminacbg_id AS klaiminacbg_id,
            sy_klaimgroup_t.sy_klaimgroup_id,
            sy_kunjungan.tgl_pendaftaran AS tgl_masuk,
            sy_kunjungan.tgl_pulang AS tgl_keluar,
            sy_klaimgroup_t.created_date AS tgl_group,
            sy_kunjungan.nosep AS no_sep,
            sy_kunjungan.no_rekammedik AS no_rekam_medik,
            sy_kunjungan.nama_pasien,
            sy_klaiminacbg.diagnosa_primer,
            sy_klaiminacbg.diagnosa_sekunder,
            sy_klaimgroup_t.spesial_procedure,
            sy_klaimgroup_t.spesial_prosthesis,
            sy_klaimgroup_t.spesial_investigation,
            sy_klaimgroup_t.spesial_drug,
            sy_klaiminacbg.is_terkirim,
                CASE
                    WHEN sy_klaiminacbg.is_terkirim = false THEN '-'::text
                    ELSE 'Terkirim'::text
                END AS status_kirim,
            sy_klaiminacbg.kunjungan_id,
            sy_kunjungan.carakeluar_kode AS carapulang_id,
            cara_keluar.lookup_name AS cara_keluar,
            sy_klaiminacbg.prosedur_bedah,
            sy_klaiminacbg.prosedur_nonbedah,
            sy_klaiminacbg.konsultasi,
            sy_klaiminacbg.tenaga_ahli,
            sy_klaiminacbg.keperawatan,
            sy_klaiminacbg.penunjang,
            sy_klaiminacbg.radiologi,
            sy_klaiminacbg.laboratorium,
            sy_klaiminacbg.pelayanan_darah,
            sy_klaiminacbg.rehabilitasi,
            sy_klaiminacbg.kamar_akomodasi,
            sy_klaiminacbg.rawat_intensif,
            sy_klaiminacbg.obat,
            sy_klaiminacbg.alkes,
            sy_klaiminacbg.bmhp,
            sy_klaiminacbg.sewa_alat,
            sy_klaiminacbg.obat_kemoterapi,
            sy_klaiminacbg.obat_kronis,
            sy_klaiminacbg.is_naikkelas,
            sy_klaiminacbg.lama_naikkelas,
            sy_klaiminacbg.ventilator,
            sy_klaiminacbg.los,
            sy_klaiminacbg.adl_cronic,
            sy_klaiminacbg.adl_subacute,
            sy_klaimgroup_t.cbg,
            sy_klaimgroup_t.special_group,
            sy_klaiminacbg.total_tarifrs,
            sy_klaimgroup_t.total AS tarif_klaim,
            sy_kunjungan.carabayar_nama AS cara_bayar,
            sy_kunjungan.penjamin_nama AS penjamin,
            sy_kunjungan.no_asuransi,
                CASE
                    WHEN sy_klaiminacbg.is_naikkelas = true AND sy_klaiminacbg.naik_kelas::text = 'kelas_1'::text THEN 'kelas 1'::text
                    WHEN sy_klaiminacbg.is_naikkelas = true AND sy_klaiminacbg.naik_kelas::text = 'kelas_2'::text THEN 'kelas 2'::text
                    WHEN sy_klaiminacbg.is_naikkelas = true AND sy_klaiminacbg.naik_kelas::text = 'kelas_3'::text THEN 'kelas 3'::text
                    WHEN sy_klaiminacbg.is_naikkelas = true AND sy_klaiminacbg.naik_kelas::text = 'kelas_4'::text THEN 'kelas VIP'::text
                    WHEN sy_klaiminacbg.is_naikkelas = true AND sy_klaiminacbg.naik_kelas::text = 'kelas_5'::text THEN 'kelas VVIP'::text
                    ELSE sy_klaimgroup_t.additional_data::json ->> 'kelas_awal'::text
                END AS kelas,
            sy_klaimgroup_t.additional_data::json ->> 'kelas_awal'::text AS kelas_hak,
            sy_kunjungan.umur,
                CASE
                    WHEN sy_kunjungan.instalasi_kode::text = 'RI'::text THEN 'Rawat Inap'::text
                    ELSE 'Rawat Jalan'::text
                END AS jenis_rawat,
            sy_klaimgroup_t.group_tarif,
            sy_klaimgroup_t.group_nama,
            sy_klaimgroup_t.sp_procedure_kode,
            sy_klaimgroup_t.sp_prosthesis_kode,
            sy_klaimgroup_t.sp_investigation_kode,
            sy_klaimgroup_t.sp_drug_kode,
            sy_klaimgroup_t.sp_procedure_nama,
            sy_klaimgroup_t.sp_prosthesis_nama,
            sy_klaimgroup_t.sp_investigation_nama,
            sy_klaimgroup_t.sp_drug_nama,
            sy_klaiminacbg.status_klaim,
            sy_klaimgroup_t.additional_data::json ->> 'info'::text AS info,
            sy_klaiminacbg.tarif_polieksekutif,
            sy_klaiminacbg.jenis_kelasrawat AS kelas_id,
            sy_klaiminacbg.tarif,
            sy_klaimgroup_t.tambahan_biaya,
            sy_klaimgroup_t.persen_tambahan,
            sy_klaimgroup_t.total_kelaspelayanan,
            sy_klaimgroup_t.total_naikkelas,
            sy_klaiminacbg.status_covid,
            sy_klaiminacbg.is_komplikasi,
            sy_klaiminacbg.is_pemulasaranjenazah,
            sy_klaiminacbg.is_kantongjenazah,
            sy_klaiminacbg.is_petijenazah,
            sy_klaiminacbg.is_plastikerat,
            sy_klaiminacbg.is_desinfektanjenazah,
            sy_klaiminacbg.is_transport,
            sy_klaiminacbg.is_desinfektanmobil
           FROM sy_klaiminacbg
             LEFT JOIN sy_klaimgroup_t ON sy_klaiminacbg.klaimgroup_id = sy_klaimgroup_t.sy_klaimgroup_id
             LEFT JOIN sy_kunjungan ON sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id
             LEFT JOIN carakeluar_m ON sy_kunjungan.carakeluar_kode::text = carakeluar_m.carakeluar_kode::text
             LEFT JOIN lookup_m cara_keluar ON sy_kunjungan.carakeluar_kode::text = cara_keluar.lookup_value::text AND cara_keluar.lookup_type::text = 'carapulang_inacbg'::text
          WHERE sy_klaiminacbg.is_deleted = false AND sy_kunjungan.instalasi_kode IS NOT NULL
          ORDER BY sy_klaiminacbg.sy_klaiminacbg_id DESC;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230405_034649_migrate_sy_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230405_034649_migrate_sy_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }
    */
}
