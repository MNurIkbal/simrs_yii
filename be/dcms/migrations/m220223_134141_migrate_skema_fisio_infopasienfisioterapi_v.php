<?php

use yii\db\Migration;

/**
 * Class m220223_134141_migrate_skema_fisio_infopasienfisioterapi_v
 */
class m220223_134141_migrate_skema_fisio_infopasienfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienfisioterapi_v\" AS
            SELECT 'APS'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama
            FROM ((((pendaftaran_t
            JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.pendaftaran_id,
            a.no_masukpenunjang,
            a.tglmasukpenunjang
            FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            WHERE (pendaftaran_t.instalasi_id = 7)
            ;");
        $this->execute('
            ALTER TABLE public.infopasienfisioterapi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_134141_migrate_skema_fisio_infopasienfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_134141_migrate_skema_fisio_infopasienfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
