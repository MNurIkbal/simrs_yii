<?php

use yii\db\Migration;

/**
 * Class m231026_143209_migrate_returresep_per_pendaftaran_improve_pasien_inforeturresep_v
 */
class m231026_143209_migrate_returresep_per_pendaftaran_improve_pasien_inforeturresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS public.inforeturresep_v");

        $this->execute("
        CREATE OR REPLACE VIEW public.inforeturresep_v
        AS  SELECT returresep_t.returresep_id,
    returresep_t.tgl_retur,
    returresep_t.no_returresep,
    lookup_m.lookup_name AS status_retur,
    lookup_m.lookup_id AS status_retur_id,
    returresep_t.tgl_verif,
    penjualanresep_t.pasien_id,
    pasien_m.nama_pasien,
    returresep_t.penjualanresep_id,
    COALESCE(penjualanresep_t.noresep, '-'::text::character varying) AS noresep,
    penjualanresep_t.carabayar_id,
    carabayar_m.carabayar_nama,
    penjualanresep_t.penjamin_id,
    penjamin_m.penjamin_nama,
    returresep_t.ruangan_id,
    ruangan_m.ruangan_nama,
    COALESCE(instalasi_m.instalasi_nama, ins.instalasi_nama) AS instalasi_asal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pegawai_m.nama_pegawai AS nama_dokter,
    pendaftaran_t.pendaftaran_id
   FROM returresep_t
     LEFT JOIN penjualanresep_t ON returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN ruangan_m rm ON reseptur_t.ruanganreseptur_id = rm.ruangan_id
     LEFT JOIN instalasi_m ON rm.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id =
        CASE
            WHEN returresep_t.pendaftaran_id IS NOT NULL THEN returresep_t.pendaftaran_id
            ELSE penjualanresep_t.pendaftaran_id
        END
     JOIN ruangan_m ON returresep_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ins ON ruangan_m.instalasi_id = ins.instalasi_id
     LEFT JOIN pasien_m ON COALESCE(penjualanresep_t.pasien_id, pendaftaran_t.pasien_id) = pasien_m.pasien_id
     LEFT JOIN lookup_m ON returresep_t.status_retur = lookup_m.lookup_id
  ORDER BY returresep_t.tgl_retur DESC
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_143209_migrate_returresep_per_pendaftaran_improve_pasien_inforeturresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_143209_migrate_returresep_per_pendaftaran_improve_pasien_inforeturresep_v cannot be reverted.\n";

        return false;
    }
    */
}
