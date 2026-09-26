<?php

use yii\db\Migration;

/**
 * Class m230704_090413_DSV311_improve_sy_infopasienbpjsklaimlist_v
 */
class m230704_090413_DSV311_improve_sy_infopasienbpjsklaimlist_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS public.sy_infopasienbpjsklaimlist_v;");
        $this->execute("CREATE OR REPLACE VIEW public.sy_infopasienbpjsklaimlist_v
        AS SELECT sy_kunjungan.kunjungan_id,
            sy_kunjungan.no_pendaftaran,
            sy_kunjungan.no_rekammedik,
            sy_kunjungan.nama_pasien,
            sy_kunjungan.jenis_kelamin,
            sy_kunjungan.tgl_lahir,
            sy_kunjungan.umur,
            sy_kunjungan.tgl_pendaftaran,
            sy_kunjungan.tgl_pulang,
            sy_kunjungan.instalasi_kode,
            sy_kunjungan.instalasi_nama,
            sy_kunjungan.ruangan_kode,
            sy_kunjungan.ruangan_nama,
            sy_kunjungan.carabayar_kode,
            sy_kunjungan.carabayar_nama,
            sy_kunjungan.penjamin_kode,
            sy_kunjungan.penjamin_nama,
            sy_kunjungan.kelas_kode,
            sy_kunjungan.kelas_nama,
            sy_kunjungan.dokter_kode,
            sy_kunjungan.dokter_nama,
            sy_kunjungan.no_sep,
            sy_kunjungan.status_kunjungan,
            sy_kunjungan.no_kamar,
            sy_kunjungan.no_tempattidur,
            sy_kunjungan.hak_kelasbpjs,
            sy_kunjungan.carakeluar_kode,
            sy_kunjungan.lama_rawat,
            sy_kunjungan.no_asuransi,
            sy_kunjungan.is_verifikasi,
            sy_kunjungan.total_verifikasi,
            sy_kunjungan.tgl_verifikasi,
            sy_kunjungan.identitas_id,
            sy_kunjungan.identitas_nama,
            sy_kunjungan.identitas_value,
            sy_kunjungan.no_klaimcovid,
            sy_kunjungan.pasien_id,
            sy_kunjungan.additional_data,
            sy_kunjungan.created_date,
            sy_kunjungan.created_by,
            sy_kunjungan.modified_count,
            sy_kunjungan.last_modified_date,
            sy_kunjungan.last_modified_by,
            sy_kunjungan.is_deleted,
            sy_kunjungan.is_active,
            sy_kunjungan.deleted_date,
            sy_kunjungan.deleted_by,
            sy_kunjungan.nosep,
            sy_kunjungan.jeniskasuspenyakit_id,
            sy_kunjungan.jeniskasuspenyakit_nama,
            sy_kunjungan.instalasi_id,
            sy_kunjungan.ruangan_id,
                CASE
                    WHEN sum(tarif_data.tagihan_rumahsakit) IS NULL THEN sum(tagihan_rs.tarif_rs)::double precision
                    ELSE sum(tarif_data.tagihan_rumahsakit)
                END AS tarif_rs,
                CASE
                    WHEN sum(tarif_data.tarif_inacbg) IS NULL THEN '0'::double precision
                    ELSE sum(tarif_data.tarif_inacbg)
                END AS plafon
           FROM sy_kunjungan
             LEFT JOIN ( SELECT sy_klaiminacbg.kunjungan_id,
                    tagihan.tarif_inacbg,
                    sy_klaiminacbg.total_tarifrs AS tagihan_rumahsakit
                   FROM sy_klaiminacbg
                     LEFT JOIN ( SELECT sy_klaimgroup_t.sy_klaimgroup_id,
                            sum(sy_klaimgroup_t.total) AS tarif_inacbg
                           FROM sy_klaimgroup_t
                          GROUP BY sy_klaimgroup_t.sy_klaimgroup_id) tagihan ON sy_klaiminacbg.klaimgroup_id = tagihan.sy_klaimgroup_id
                  WHERE sy_klaiminacbg.is_deleted = false AND sy_klaiminacbg.is_active = true) tarif_data ON sy_kunjungan.kunjungan_id = tarif_data.kunjungan_id
             LEFT JOIN ( SELECT sk.kunjungan_id,
                    sum(sk.layanan_tarif) AS tarif_rs
                   FROM sy_kunjungantagihan sk
                  GROUP BY sk.kunjungan_id) tagihan_rs ON tagihan_rs.kunjungan_id = sy_kunjungan.kunjungan_id
          WHERE sy_kunjungan.is_deleted = false AND sy_kunjungan.is_active = true
          GROUP BY sy_kunjungan.kunjungan_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230704_090413_DSV311_improve_sy_infopasienbpjsklaimlist_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230704_090413_DSV311_improve_sy_infopasienbpjsklaimlist_v cannot be reverted.\n";

        return false;
    }
    */
}
