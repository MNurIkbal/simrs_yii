<?php

use yii\db\Migration;

/**
 * Class m220707_122226_migrate_skema_fisio_lappemeriksaanpasienfisiorj_v
 */
class m220707_122226_migrate_skema_fisio_lappemeriksaanpasienfisiorj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lappemeriksaanpasienfisiorj_v;');
        $this->execute("
            CREATE VIEW \"public\".\"lappemeriksaanpasienfisiorj_v\" AS
            SELECT 'PAKET'::text AS type,
            pendaftaran_t.pendaftaran_id,
            soapfisioterapi_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            look_jenkel.lookup_name AS jeniskelamin_nama,
            look_jenkel.lookup_kode AS jeniskelamin_kode,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
            programterapidetail_t.programterapidetail_id,
            array_to_string(array_agg(daftartindakan_detail.daftartindakan_id), ','::text) AS daftartindakan_id,
            array_to_string(array_agg(daftartindakan_detail.daftartindakan_nama), ','::text) AS daftartindakan_nama,
            soapfisioterapi_t.terapis_id,
            terapis.nama_pegawai AS terapis_nama,
            programterapi_t.is_active,
            programterapi_t.is_deleted,
            programterapi_t.is_paket
            FROM ((((((((((((((((soapfisioterapi_t
            LEFT JOIN ( SELECT a.soapfisioterapi_id,
            a.programterapi_id
            FROM soapfisioterapidetail_t a
            GROUP BY a.soapfisioterapi_id, a.programterapi_id) soapfisioterapidetail_t ON ((soapfisioterapi_t.soapfisioterapi_id = soapfisioterapidetail_t.soapfisioterapi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.tgl_pendaftaran,
            a.pasien_id,
            a.carabayar_id,
            a.penjamin_id,
            a.ruangan_id,
            a.instalasi_id
            FROM pendaftaran_t a) pendaftaran_t ON ((soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((soapfisioterapi_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name,
            a.lookup_kode
            FROM lookup_m a) look_jenkel ON (((pasien_m.jeniskelamin)::integer = look_jenkel.lookup_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.pendaftaran_id,
            a.programterapi_id,
            a.is_paket,
            a.is_active,
            a.is_deleted
            FROM programterapi_t a
            WHERE (a.is_paket = true)) programterapi_t ON ((soapfisioterapidetail_t.programterapi_id = programterapi_t.programterapi_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            a.programterapidetail_id,
            a.daftartindakan_id
            FROM programterapidetail_t a) programterapidetail_t ON ((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.jenispemeriksaanfisio_id,
            a.pemeriksaanfisio_nama
            FROM pemeriksaanfisio_m a) pemeriksaanfisio_m ON ((programterapidetail_t.daftartindakan_id = pemeriksaanfisio_m.daftartindakan_id)))
            LEFT JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
            FROM jenispemeriksaanfisio_m a) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
            LEFT JOIN ( SELECT a.programterapidetailpaket_id,
            a.programterapidetail_id,
            a.daftartindakan_id
            FROM programterapidetailpaket_t a) programterapidetailpaket_t ON ((programterapidetail_t.programterapidetail_id = programterapidetailpaket_t.programterapidetail_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_m ON ((programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_detail ON ((programterapidetailpaket_t.daftartindakan_id = daftartindakan_detail.daftartindakan_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) terapis ON ((soapfisioterapi_t.terapis_id = terapis.pegawai_id)))
            WHERE (pendaftaran_t.instalasi_id = ( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE ((lookuptransaksi_m.kode_transaksi)::text = 'instalasi_fisio'::text)))
            GROUP BY pendaftaran_t.pendaftaran_id, soapfisioterapi_t.pasien_id, pendaftaran_t.tgl_pendaftaran, pasien_m.nama_pasien, pasien_m.no_rekam_medik, pasien_m.jeniskelamin, look_jenkel.lookup_name, look_jenkel.lookup_kode, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, jenispemeriksaanfisio_m.jenispemeriksaanfisio_id, jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama, programterapidetail_t.programterapidetail_id, soapfisioterapi_t.terapis_id, terapis.nama_pegawai, programterapi_t.is_active, programterapi_t.is_deleted, programterapi_t.is_paket
            UNION ALL
            SELECT 'NON PAKET'::text AS type,
            pendaftaran_t.pendaftaran_id,
            soapfisioterapi_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            look_jenkel.lookup_name AS jeniskelamin_nama,
            look_jenkel.lookup_kode AS jeniskelamin_kode,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
            programterapidetail_t.programterapidetail_id,
            array_to_string(array_agg(daftartindakan_m.daftartindakan_id), ','::text) AS daftartindakan_id,
            array_to_string(array_agg(daftartindakan_m.daftartindakan_nama), ','::text) AS daftartindakan_nama,
            soapfisioterapi_t.terapis_id,
            terapis.nama_pegawai AS terapis_nama,
            programterapi_t.is_active,
            programterapi_t.is_deleted,
            programterapi_t.is_paket
            FROM ((((((((((((((soapfisioterapi_t
            LEFT JOIN ( SELECT a.soapfisioterapi_id,
            a.programterapi_id
            FROM soapfisioterapidetail_t a
            GROUP BY a.soapfisioterapi_id, a.programterapi_id) soapfisioterapidetail_t ON ((soapfisioterapi_t.soapfisioterapi_id = soapfisioterapidetail_t.soapfisioterapi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.tgl_pendaftaran,
            a.pasien_id,
            a.carabayar_id,
            a.penjamin_id,
            a.ruangan_id,
            a.instalasi_id
            FROM pendaftaran_t a) pendaftaran_t ON ((soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((soapfisioterapi_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name,
            a.lookup_kode
            FROM lookup_m a) look_jenkel ON (((pasien_m.jeniskelamin)::integer = look_jenkel.lookup_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.programterapi_id,
            a.is_paket,
            a.is_active,
            a.is_deleted
            FROM programterapi_t a
            WHERE (a.is_paket = false)) programterapi_t ON ((soapfisioterapidetail_t.programterapi_id = programterapi_t.programterapi_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            a.programterapidetail_id,
            a.daftartindakan_id
            FROM programterapidetail_t a) programterapidetail_t ON ((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.jenispemeriksaanfisio_id,
            a.pemeriksaanfisio_nama
            FROM pemeriksaanfisio_m a) pemeriksaanfisio_m ON ((programterapidetail_t.daftartindakan_id = pemeriksaanfisio_m.daftartindakan_id)))
            LEFT JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
            FROM jenispemeriksaanfisio_m a) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_m ON ((programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) terapis ON ((soapfisioterapi_t.terapis_id = terapis.pegawai_id)))
            WHERE (pendaftaran_t.instalasi_id = ( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE ((lookuptransaksi_m.kode_transaksi)::text = 'instalasi_fisio'::text)))
            GROUP BY pendaftaran_t.pendaftaran_id, soapfisioterapi_t.pasien_id, pendaftaran_t.tgl_pendaftaran, pasien_m.nama_pasien, pasien_m.no_rekam_medik, pasien_m.jeniskelamin, look_jenkel.lookup_name, look_jenkel.lookup_kode, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, jenispemeriksaanfisio_m.jenispemeriksaanfisio_id, jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama, programterapidetail_t.programterapidetail_id, soapfisioterapi_t.terapis_id, terapis.nama_pegawai, programterapi_t.is_active, programterapi_t.is_deleted, programterapi_t.is_paket
            ;");
        $this->execute('
            ALTER TABLE public.lappemeriksaanpasienfisiorj_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_122226_migrate_skema_fisio_lappemeriksaanpasienfisiorj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_122226_migrate_skema_fisio_lappemeriksaanpasienfisiorj_v cannot be reverted.\n";

        return false;
    }
    */
}
