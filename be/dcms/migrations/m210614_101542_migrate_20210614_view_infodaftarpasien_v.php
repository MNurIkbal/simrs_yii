<?php

use yii\db\Migration;

/**
 * Class m210614_101542_migrate_20210614_view_infodaftarpasien_v
 */
class m210614_101542_migrate_20210614_view_infodaftarpasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infodaftarpasien_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infodaftarpasien_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.status_pasien,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
            l_status_konfirmasi.lookup_name AS status_konfirmasirm,
            bpjs_t.nosep,
            l_status_pasien.lookup_name AS status_pasien_nama
            FROM (((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m l_status_konfirmasi ON (((pendaftaran_t.status_konfirmasi)::integer = l_status_konfirmasi.lookup_id)))
            LEFT JOIN lookup_m l_status_pasien ON (((pendaftaran_t.status_pasien)::integer = l_status_pasien.lookup_id)))
            WHERE (pendaftaran_t.instalasi_id <> 3)
            UNION ALL
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.status_pasien,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
            l_status_konfirmasi.lookup_name AS status_konfirmasirm,
            bpjs_t.nosep,
            l_status_pasien.lookup_name AS status_pasien_nama
            FROM ((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m l_status_konfirmasi ON (((pendaftaran_t.status_konfirmasi)::integer = l_status_konfirmasi.lookup_id)))
            LEFT JOIN lookup_m l_status_pasien ON (((pendaftaran_t.status_pasien)::integer = l_status_pasien.lookup_id)))
            ;");
        
        $this->execute('
            ALTER TABLE public.infodaftarpasien_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210614_101542_migrate_20210614_view_infodaftarpasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210614_101542_migrate_20210614_view_infodaftarpasien_v cannot be reverted.\n";

        return false;
    }
    */
}
