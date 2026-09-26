<?php

use yii\db\Migration;

/**
 * Class m220725_040913_migrate_MHG3572_infopasienkarcis_v
 */
class m220725_040913_migrate_MHG3572_infopasienkarcis_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienkarcis_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienkarcis_v\" AS
            SELECT 'non_paket'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.tgl_pendaftaran,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS tarif_tindakan,
            carabayar_m.carabayar_kode_warna,
            pendaftaran_t.status_periksa
            FROM ((((((((tindakanpelayanan_t
            JOIN ( SELECT a.pendaftaran_id,
            a.status_periksa,
            a.instalasi_id,
            a.carabayar_id,
            a.penjamin_id,
            a.pasien_id,
            a.tgl_pendaftaran,
            a.no_pendaftaran
            FROM pendaftaran_t a) pendaftaran_t ON (((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND ((pendaftaran_t.status_periksa)::text <> '4'::text) AND (pendaftaran_t.instalasi_id <> 21))))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.carabayar_kode_warna
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.daftartindakan_id,
            a.kelompoktindakan_id
            FROM daftartindakan_m a) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            JOIN ( SELECT a.kelompoktindakan_id
            FROM kelompoktindakan_m a) kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
            WHERE ((daftartindakan_m.kelompoktindakan_id = 17) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.parent_id IS NULL))
            GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.pasien_id, pendaftaran_t.instalasi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, pendaftaran_t.tgl_pendaftaran, instalasi_m.instalasi_nama, pendaftaran_t.no_pendaftaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_kode_warna, pendaftaran_t.status_periksa
            UNION ALL
            SELECT 'paket'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.tgl_pendaftaran,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS tarif_tindakan,
            carabayar_m.carabayar_kode_warna,
            pendaftaran_t.status_periksa
            FROM ((((((tindakanpelayanan_t
            JOIN ( SELECT a.pendaftaran_id,
            a.instalasi_id,
            a.status_periksa,
            a.carabayar_id,
            a.penjamin_id,
            a.pasien_id,
            a.tgl_pendaftaran,
            a.no_pendaftaran
            FROM pendaftaran_t a) pendaftaran_t ON (((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pendaftaran_t.instalasi_id = 21) AND ((pendaftaran_t.status_periksa)::text <> '4'::text))))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.carabayar_kode_warna
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.parent_id IS NULL))
            GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.pasien_id, pendaftaran_t.instalasi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, pendaftaran_t.tgl_pendaftaran, instalasi_m.instalasi_nama, pendaftaran_t.no_pendaftaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_kode_warna, pendaftaran_t.status_periksa
            ;");
        $this->execute('
            ALTER TABLE public.infopasienkarcis_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220725_040913_migrate_MHG3572_infopasienkarcis_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_040913_migrate_MHG3572_infopasienkarcis_v cannot be reverted.\n";

        return false;
    }
    */
}
