<?php

use yii\db\Migration;

/**
 * Class m220221_102400_migrate_BTS164_infokunjunganrd_v
 */
class m220221_102400_migrate_BTS164_infokunjunganrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infokunjunganrd_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrd_v\" AS
            SELECT pasien_m.pasien_id,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            pasien_m.namadepan,
            pasien_m.nama_pasien,
            pasien_m.nama_bin,
            pasien_m.jeniskelamin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pasien_m.alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.agama,
            pasien_m.golongandarah,
            pasien_m.photopasien,
            pasien_m.alamatemail,
            pasien_m.statusrekammedis,
            pasien_m.statusperkawinan,
            pasien_m.no_rekam_medik,
            pasien_m.tgl_rekam_medik,
            pasien_m.propinsi_id,
            fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
            pasien_m.kabupaten_id,
            fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pasien_m.kecamatan_id,
            fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
            pasien_m.kelurahan_id,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
            pendaftaran_t.pendaftaran_id,
            pekerjaan_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.status_pasien,
            pendaftaran_t.kunjungan,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
            pendaftaran_t.status_masuk,
            pendaftaran_t.umur,
            asuransipasien_m.nokartuasuransi AS no_asuransi,
            asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
            asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            caramasuk_m.caramasuk_id,
            caramasuk_m.caramasuk_nama,
            pendaftaran_t.shift_id,
            golonganumur_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            rujukan_t.no_rujukan,
            rujukan_t.nama_perujuk,
            rujukan_t.tanggal_rujukan,
            rujukan_t.kodediagnosa_rujukan,
            asalrujukan_m.asalrujukan_id,
            asalrujukan_m.asalrujukan_nama,
            penanggungjawab_m.penanggungjawab_id,
            penanggungjawab_m.pengantar,
            penanggungjawab_m.hubungankeluarga,
            penanggungjawab_m.penanggungjawab_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.gelardepan,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            pendaftaran_t.rujukan_id,
            pendaftaran_t.pasienpulang_id,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pembayaranpelayanan_id,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.warga_negara,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            suku_m.suku_id,
            suku_m.suku_nama,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            carakeluar_m.carakeluar_id,
            carakeluar_m.carakeluar_nama AS carakeluar,
            kondisikeluar_m.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
            asuransipasien_m.nopeserta,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            NULL::integer AS konsulpoli_id,
            pasien_m.is_deleted,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            bpjs_t.nosep,
            pendaftaran_t.status_periksa AS status_periksa_id,
            pendaftaran_t.status_bayar
            FROM (((((((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            WHERE (instalasi_m.instalasi_id = 2)
            ;");
        $this->execute('
            ALTER TABLE public.infokunjunganrd_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220221_102400_migrate_BTS164_infokunjunganrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220221_102400_migrate_BTS164_infokunjunganrd_v cannot be reverted.\n";

        return false;
    }
    */
}
