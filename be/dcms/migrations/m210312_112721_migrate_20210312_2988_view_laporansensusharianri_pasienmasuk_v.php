<?php

use yii\db\Migration;

/**
 * Class m210312_112721_migrate_20210312_2988_view_laporansensusharianri_pasienmasuk_v
 */
class m210312_112721_migrate_20210312_2988_view_laporansensusharianri_pasienmasuk_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienmasuk_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienmasuk_v\" AS
            SELECT (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date AS tgl_admisi,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            kamar_asal.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            masukkamar_t.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_asal.kamarruangan_nokamar AS kamar,
            tempattidur_asal.no_tempattidur AS tempattidur,
            penjamin_m.penjamin_nama,
            pegawai_m.nama_pegawai AS nama_dokter,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama
            FROM (((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((masukkamar_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN kamarruangan_m kamar_asal ON ((masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
            LEFT JOIN kamartempattidur_m tempattidur_asal ON ((pasienadmisi_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
            JOIN kelaspelayanan_m ON ((masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_pasienmasuk_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_112721_migrate_20210312_2988_view_laporansensusharianri_pasienmasuk_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_112721_migrate_20210312_2988_view_laporansensusharianri_pasienmasuk_v cannot be reverted.\n";

        return false;
    }
    */
}
