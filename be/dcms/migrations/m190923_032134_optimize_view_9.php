<?php

use yii\db\Migration;

/**
 * Class m190923_032134_optimize_view_9
 */
class m190923_032134_optimize_view_9 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    /*infopasienrd_v_old*/
     $this->execute('DROP VIEW if exists public.infopasienrd_v_old;');
     
     /*infopasiengizi_v_old*/
     $this->execute('DROP VIEW if exists public.infopasiengizi_v_old;');
     
     /*kasuspenyakitdiagnosa_v_old*/
     $this->execute('DROP VIEW if exists public.kasuspenyakitdiagnosa_v_old;');
     
     /*cetakpemesanankamar_v_old*/
     $this->execute('DROP VIEW if exists public.cetakpemesanankamar_v_old;');
     
     /*riwayatpenunjangdetail_v_old*/
     $this->execute('DROP VIEW if exists public.riwayatpenunjangdetail_v_old;');
     
     /*riwayatpenunjangdetail_v_old2*/
     $this->execute('DROP VIEW if exists public.riwayatpenunjangdetail_v_old2;');
        
    /*orderpenunjang_v_old*/
     $this->execute('DROP VIEW if exists public.orderpenunjang_v_old;');
     
     /*orderpenunjang_v_old2*/
     $this->execute('DROP VIEW if exists public.orderpenunjang_v_old2;');
     
     /*sie_tagihanpengajuan_old*/
     $this->execute('DROP VIEW if exists  public.sie_tagihanpengajuan_old;');
   

     /*infopasienmeninggaldetail_v*/
     $this->execute('DROP VIEW if exists public.infopasienmeninggaldetail_v;');

     $this->execute("
        CREATE OR REPLACE VIEW public.infopasienmeninggaldetail_v AS 
 SELECT gabung.pendaftaran_id,
    gabung.pasienadmisi_id,
    gabung.no_pendaftaran,
    gabung.pasienpulang_id,
    gabung.tgl_meninggal,
    gabung.pasien_id,
    gabung.nama_pasien,
    gabung.no_rekam_medik,
    gabung.alamat_pasien,
    gabung.tanggal_lahir,
    gabung.umur,
    gabung.jeniskelamin,
    gabung.jenis_kelamin,
    gabung.tgl_pengambilan,
    gabung.alatterpasang_id,
    gabung.barang_id,
    gabung.obatalkes_id,
    gabung.alat_id,
    gabung.nama_alat,
    gabung.qty
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasienpulang_id,
            pasienpulang_t.tgl_meninggal,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.alamat_pasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            ambiljenazah_t.tgl_pengambilan,
            alatterpasang_t.alatterpasang_id,
            alatterpasang_t.barang_id,
            alatterpasang_t.obatalkes_id,
                CASE
                    WHEN alatterpasang_t.barang_id IS NULL THEN 'ALAT'::text
                    ELSE 'LINEN'::text
                END AS alat_id,
                CASE
                    WHEN alatterpasang_t.barang_id IS NULL THEN obatalkes_m.obatalkes_nama
                    ELSE barang_m.barang_nama
                END AS nama_alat,
            alatterpasang_t.qty
           FROM pendaftaran_t
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
             LEFT JOIN ambiljenazah_t ON pendaftaran_t.pendaftaran_id = ambiljenazah_t.pendaftaran_id
             LEFT JOIN alatterpasang_t ON pendaftaran_t.pendaftaran_id = alatterpasang_t.pendaftaran_id
             LEFT JOIN barang_m ON alatterpasang_t.barang_id = barang_m.barang_id
             LEFT JOIN obatalkes_m ON alatterpasang_t.obatalkes_id = obatalkes_m.obatalkes_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.pasienpulang_id,
            pasienpulang_t.tgl_meninggal,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.alamat_pasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            ambiljenazah_t.tgl_pengambilan,
            alatterpasang_t.alatterpasang_id,
            alatterpasang_t.barang_id,
            alatterpasang_t.obatalkes_id,
                CASE
                    WHEN alatterpasang_t.barang_id IS NULL THEN 'ALAT'::text
                    ELSE 'LINEN'::text
                END AS alat_id,
                CASE
                    WHEN alatterpasang_t.barang_id IS NULL THEN obatalkes_m.obatalkes_nama
                    ELSE barang_m.barang_nama
                END AS nama_alat,
            alatterpasang_t.qty
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
             LEFT JOIN ambiljenazah_t ON pendaftaran_t.pasienadmisi_id = ambiljenazah_t.pasienadmisi_id
             LEFT JOIN alatterpasang_t ON pendaftaran_t.pendaftaran_id = alatterpasang_t.pendaftaran_id
             LEFT JOIN barang_m ON alatterpasang_t.barang_id = barang_m.barang_id
             LEFT JOIN obatalkes_m ON alatterpasang_t.obatalkes_id = obatalkes_m.obatalkes_id) gabung
  GROUP BY gabung.pendaftaran_id, gabung.pasienadmisi_id, gabung.no_pendaftaran, gabung.pasienpulang_id, gabung.tgl_meninggal, gabung.pasien_id, gabung.nama_pasien, gabung.no_rekam_medik, gabung.alamat_pasien, gabung.tanggal_lahir, gabung.umur, gabung.jeniskelamin, gabung.jenis_kelamin, gabung.tgl_pengambilan, gabung.alatterpasang_id, gabung.barang_id, gabung.obatalkes_id, gabung.alat_id, gabung.nama_alat, gabung.qty;");
     
     $this->execute('ALTER TABLE public.infopasienmeninggaldetail_v
                  OWNER TO postgres;
                ');

     /*infopasienmeninggaldetail2_v*/
     $this->execute('DROP VIEW if exists public.infopasienmeninggaldetail2_v;');
     $this->execute("
        CREATE OR REPLACE VIEW public.infopasienmeninggaldetail2_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    'tindakan'::text AS jenis,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    tindakanpelayanan_t.tgl_tindakan AS tindakan_obat_tgl,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    tindakanpelayanan_t.qty_tindakan AS qty,
    NULL::character varying AS satuan,
    tindakanpelayanan_t.tarif_satuan
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    'tindakan'::text AS jenis,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    tindakanpelayanan_t.tgl_tindakan AS tindakan_obat_tgl,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    tindakanpelayanan_t.qty_tindakan AS qty,
    NULL::character varying AS satuan,
    tindakanpelayanan_t.tarif_satuan
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    'obat'::text AS jenis,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkespasien_t.tglpelayanan AS tindakan_obat_tgl,
    obatalkes_m.obatalkes_nama AS tindakan_obat,
    obatalkespasien_t.qty_oa AS qty,
    satuanunit_m.satuanunit_nama AS satuan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.ruangan_id = 38
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    'obat'::text AS jenis,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkespasien_t.tglpelayanan AS tindakan_obat_tgl,
    obatalkes_m.obatalkes_nama AS tindakan_obat,
    obatalkespasien_t.qty_oa AS qty,
    satuanunit_m.satuanunit_nama AS satuan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.ruangan_id = 38
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id;");

     $this->execute('ALTER TABLE public.infopasienmeninggaldetail2_v
  OWNER TO postgres;');

     /*infopasienpenunjang_v*/
     $this->execute('DROP VIEW if exists public.infopasienpenunjang_v;');

     $this->execute("
        CREATE OR REPLACE VIEW public.infopasienpenunjang_v AS 
 SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    ruangasal.ruangan_nama AS ruangan_asal,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienmasukpenunjang_t.status_periksa,
    ruangan_m.instalasi_id
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ruangan_m ruangasal ON pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id
     JOIN kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
  WHERE pasienmasukpenunjang_t.is_active = true AND pasienmasukpenunjang_t.is_deleted = false;");

     $this->execute('ALTER TABLE public.infopasienpenunjang_v
  OWNER TO postgres;
');
    
     /*infopasienpulangri_v*/
     $this->execute('DROP VIEW if exists public.infopasienpulangri_v;');

     $this->execute("
        CREATE OR REPLACE VIEW public.infopasienpulangri_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienpulang_t.tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.lama_rawat,
    pasienpulang_t.pasienpulang_id,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jns_kelamin,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
  WHERE pasienpulang_t.pasienbatalpulang_id IS NULL;");

     $this->execute('ALTER TABLE public.infopasienpulangri_v
  OWNER TO postgres;');

     /*infopasienpulangrjrd_v*/
     $this->execute('DROP VIEW if exists public.infopasienpulangrjrd_v;');

     $this->execute("
        CREATE OR REPLACE VIEW public.infopasienpulangrjrd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienpulang_t.tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    carakeluar_m.carakeluar_nama,
    pendaftaran_t.instalasi_id,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.pasienpulang_id,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id AND pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN pasien_m ON pasienpulang_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
  WHERE pasienpulang_t.pasienbatalpulang_id IS NULL;");

     $this->execute('ALTER TABLE public.infopasienpulangrjrd_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190923_032134_optimize_view_9 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190923_032134_optimize_view_9 cannot be reverted.\n";

        return false;
    }
    */
}
