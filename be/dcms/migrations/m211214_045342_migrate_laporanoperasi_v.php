<?php

use yii\db\Migration;

/**
 * Class m211214_045342_migrate_laporanoperasi_v
 */
class m211214_045342_migrate_laporanoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanoperasi_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"laporanoperasi_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran AS tanggal_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tanggal_operasi,
    ruangan_perujuk.ruangan_nama AS ruang_perujuk,
    verifikasibedah_r.daftartindakan_nama AS nama_tindakan,
    verifikasibedah_r.operasi_nama AS nama_operasi,
    verifikasibedah_r.qty,
    verifikasibedah_r.is_cyto AS cyto,
    verifikasibedah_r.is_penyulit AS penyulit,
    verifikasibedah_r.nama_pegawai AS dokter,
    verifikasibedah_r.posisi_tim AS posisi,
    verifikasibedah_r.total_harga AS subtotal
   FROM pendaftaran_t
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.pendaftaran_id,
            a.ruangan_id,
            a.ruanganasal_id,
            a.tglmasukpenunjang
           FROM pasienmasukpenunjang_t a
          WHERE a.is_deleted = false) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a
          WHERE a.instalasi_id = 12) ruangan_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_perujuk ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_perujuk.ruangan_id
     JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.timoperasi_id
           FROM timoperasi_t a
          WHERE a.is_deleted = false) timoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = timoperasi_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.daftartindakan_nama,
            a.operasi_nama,
            a.timoperasi_id,
            a.posisi_tim,
            a.qty,
            a.is_cyto,
            a.is_penyulit,
            a.nama_pegawai,
            a.total_harga,
            a.dokter_id,
            a.daftartindakan_id
           FROM verifikasibedah_r a
          WHERE a.is_deleted = false) verifikasibedah_r ON timoperasi_t.timoperasi_id = verifikasibedah_r.timoperasi_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.dokterpenanggungjawab_id AS dokter_id,
            a.kelaspelayanan_id,
            a.penjamin_id
           FROM tindakanpelayanan_t a
          WHERE a.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND verifikasibedah_r.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id AND verifikasibedah_r.dokter_id = tindakanpelayanan_t.dokter_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211214_045342_migrate_laporanoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211214_045342_migrate_laporanoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
