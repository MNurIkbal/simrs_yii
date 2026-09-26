<?php

use yii\db\Migration;

/**
 * Class m190719_094055_asuhangizi_v
 */
class m190719_094055_asuhangizi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP VIEW if exists public.asuhangizi_v;
        ');


        $this->execute('
         CREATE OR REPLACE VIEW public.asuhangizi_v AS 
 SELECT pasien.no_rekam_medik,
    pasien.pendaftaran_id,
    pasien.tgl_pendaftaran,
    pasien.no_pendaftaran,
    pasien.nama_pasien,
    pasien.jenis_kelamin,
    pasien.jeniskasuspenyakit_nama,
    pasien.tanggal_lahir,
    pasien.umur,
    pasien.dokter_admisi,
    pasien.kelaspelayanan_nama,
    pasien.carabayar_nama,
    pasien.penjamin_nama,
    pasien.r_penyakitkeluarga,
    pasien.is_merokok,
    pasien.jml_rokok,
    t.gizi_makanan,
    t.antropometri,
    t.biokimia,
    t.fisikklinis_gizi,
    t.diagnosa_gizi,
    t.intervensi_gizi,
    t.rencana_gizi,
    t.asuhangizi_id,
    t.created_date,
    pasien.ruangan_nama,
    pasien.kamarruangan_nokamar,
    pasien.no_tempattidur,
    t.peg_gizi_id AS dietisen_id,
    dietisen.nama_pegawai AS dietisen_nama,
    asesmenmedis_t.r_peskk,
    d_asmenmedis.diagnosa_namalainnya AS diagnosa_nama
   FROM asuhangizi_t t
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            kelaspelayanan_m.kelaspelayanan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            asesmenmedis_t_1.r_penyakitkeluarga,
            asesmenmedis_t_1.is_merokok,
            asesmenmedis_t_1.jml_rokok,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN asesmenmedis_t asesmenmedis_t_1 ON pendaftaran_t.pendaftaran_id = asesmenmedis_t_1.pendaftaran_id AND asesmenmedis_t_1.is_deleted = false
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) pasien ON t.pendaftaran_id = pasien.pendaftaran_id
     LEFT JOIN asesmenmedis_t ON t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN diagnosa_m d_asmenmedis ON asesmenmedis_t.diagnosa_id = d_asmenmedis.diagnosa_id
     JOIN pegawai_m dietisen ON t.peg_gizi_id = dietisen.pegawai_id;
        ');

        $this->execute('
         ALTER TABLE public.asuhangizi_v
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_094055_asuhangizi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_094055_asuhangizi_v cannot be reverted.\n";

        return false;
    }
    */
}
