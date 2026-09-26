<?php

use yii\db\Migration;

/**
 * Class m210723_033638_improvment_catatan_keperawatan_US165
 */
class m210723_033638_improvment_catatan_keperawatan_US165 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS catatankeperawatan_t(
                catatankeperawatan_id serial8 NOT NULL PRIMARY KEY ,
                pendaftaran_id int4,
                pasienadmisi_id int4,
                tgl_catatan TIMESTAMP(6) ,
                waktu_catatan TIME(6) ,
                kegiatankeperawatan_id INT,
                kegiatan_perawat TEXT,
                catatan TEXT,
                pegawai_id INT,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4
            );
        ');

        $this->execute('
            DROP TABLE IF EXISTS kegiatankeperawatan_m ;
        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS kegiatankeperawatan_m(
                kegiatankeperawatan_id serial8 NOT NULL PRIMARY KEY ,
                jenis_kegiatan int4 ,
                nama_kegiatan VARCHAR(100),
                is_catatan BOOLEAN DEFAULT FALSE,
                catatan TEXT,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4
            );
        ');

        $this->execute('
            COMMENT ON COLUMN "public"."kegiatankeperawatan_m"."jenis_kegiatan" IS \'lookup_type kegiatan_keperawatan\';
        ');

        $this->execute('
            DELETE FROM lookup_m WHERE lookup_type = \'kegiatan_keperawatan\';
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1038, \'kegiatan_keperawatan\', \'Perawat\', \'Perawat\', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1039, \'kegiatan_keperawatan\', \'Bidan\', \'Bidan\', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');

        $this->execute('
            INSERT INTO kegiatankeperawatan_m(jenis_kegiatan, nama_kegiatan, is_catatan, catatan) VALUES
            (1038,\'Melakukan serah terima pasien\',\'f\',NULL),
            (1038,\'Melakukan serah terima pasien dengan unit lain saat transfer pasien\',\'f\',NULL),
            (1038,\'Melakukan pengkajian awal keperawatan rawat inap\',\'f\',NULL),
            (1038,\'Melakukan pengkajian awal keperawatan rawat jalan\',\'f\',NULL),
            (1038,\'Menghubungi dokter jaga\',\'t\',\'…... (Free text untuk tujuan menghubungi dokter jaga contoh pasien baru sudah masuk, kondisi pasien)\'),
            (1038,\'Mengubungi DPJP\',\'t\',\'…... (Free text untuk tujuan menghubungi DPJP contoh pasien baru sudah masuk, kondisi pasien )\'),
            (1038,\'Memasang monitor tanda-tanda\',\'f\',NULL),
            (1038,\'Mengukur suhu tubuh pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan suhu tubuh)\'),
            (1038,\'Mengukur tekanan darah pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan tekanan darah)\'),
            (1038,\'Mengukur nadi dan pernapasan pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan nadi dan pernapasan)\'),
            (1038,\'Melakukan triase\',\'f\',NULL),
            (1038,\'Observasi keadaan umum pasien \',\'t\',\'…... (Free text untuk penulisan KU pasien, contoh KU lemah, composmentis)\'),
            (1038,\'Observasi kondisi pasien \',\'t\',\'…... (Free text untuk penulisan kondisi pasien, contoh pasien sedang tidur)\'),
            (1038,\'Membina hubungan terapeutik\',\'f\',NULL),
            (1038,\'Menimbang berat badan pasien \',\'f\',NULL),
            (1038,\'Mengukur tinggi badan pasien \',\'f\',NULL),
            (1038,\'Memandikan pasien\',\'f\',\' \'),
            (1038,\'Mengganti sprei pasien\',\'f\',NULL),
            (1038,\'Mengganti baju pasien\',\'f\',NULL),
            (1038,\'Memindahkan pasien dari tempat tidur ke tempat tidur lain\',\'f\',NULL),
            (1038,\'Memindahkan pasien dari tempat tidur ke strecher\',\'f\',NULL),
            (1038,\'Memindahkan pasien dari tempat tidur ke kursi roda\',\'f\',NULL),
            (1038,\'Memindahkan pasien ke ruang rawat inap\',\'t\',\'…... (Free text untuk penulisan nama ruangan)\'),
            (1038,\'Memindahkan pasien ke ICU\',\'f\',NULL),
            (1038,\'Memindahkan pasien ke kamar operasi\',\'f\',NULL),
            (1038,\'Melakukan pengkajian awal keperawatan di IGD\',\'f\',NULL),
            (1038,\'Melakukan pengkajian awal keperawatan di rawat jalan \',\'f\',NULL),
            (1038,\'Melakukan pengkajian ulang keperawatan di rawat jalan \',\'f\',NULL),
            (1038,\'Melakukan pengkajian awal keperawatan di rawat inap\',\'f\',NULL),
            (1038,\'Memasang NGT\',\'t\',\'…... (Free text untuk alasan pemasangan NGT, penulisan ukuran NGT) dll\'),
            (1038,\'Memasang OGT\',\'t\',\'…... (Free text untuk alasan memasang OGT, ukuran)\'),
            (1038,\'Melepaskan NGT \',\'f\',NULL),
            (1038,\'Melepaskan OGT\',\'f\',NULL),
            (1038,\'Memasang infus \',\'t\',\'…... (Free text untuk nama cairan infus, tetesan infus)\'),
            (1038,\'Melepaskan infus\',\'t\',\'…... (Free text untuk alasan melepaskan infus)\'),
            (1038,\'Mengganti cairan infus \',\'t\',\'…... (Free text untuk nama cairan infus, tetesan infus)\'),
            (1038,\'Memberikan terapi oral\',\'f\',NULL),
            (1038,\'Memberikan terapi injeksi intra vena\',\'t\',\'…... (Free text nama obat)\'),
            (1038,\'Memberikan terapi injeksi intra muskular\',\'t\',\'…... (Free text nama obat)\'),
            (1038,\'Memberikan terapi injeksi sub cutan\',\'t\',\'…... (Free text nama obat)\'),
            (1038,\'Memberikan terapi injeksi intra cutan\',\'t\',\'…... (Free text nama obat)\'),
            (1038,\'Memberikan terapi supositorial\',\'t\',\'…... (Free text nama obat)\'),
            (1038,\'Perawatan luka diabetes\',\'t\',\'…... (Free text nama jenis perawatan luka) cth luka infus/ luka CVC/ luka operasi kondisi luka, besar luka\'),
            (1038,\'Perawatan luka operasi\',\'t\',\'…... (Free text nama jenis perawatan luka) cth luka infus/ luka CVC/ luka operasi kondisi luka, besar luka\'),
            (1038,\'Perawatan luka kotor\',\'t\',\'…... (Free text nama jenis perawatan luka) cth luka infus/ luka CVC/ luka operasi kondisi luka, besar luka\'),
            (1038,\'Perawatan luka gangren\',\'t\',\'…... (Free text nama jenis perawatan luka) cth luka infus/ luka CVC/ luka operasi kondisi luka, besar luka\'),
            (1038,\'Perawatan luka infus\',\'t\',\'…... (Free text nama jenis perawatan luka) cth luka infus/ luka CVC/ luka operasi kondisi luka, besar luka\'),
            (1038,\'Perawatan luka \',\'t\',\'…... (Free text nama jenis perawatan luka) cth luka infus/ luka CVC/ luka operasi kondisi luka, besar luka\'),
            (1038,\'Melakukan kompres hangat \',\'t\',\'…... (Free text respon pasien)\'),
            (1038,\'Melakukan kompres dingin\',\'t\',\'…... (Free text respon pasien)\'),
            (1038,\'Memberikan edukasi kepada pasien/ keluarga tentang: \',\'t\',\'…... (Free text edukasi yang diberikan)\'),
            (1038,\'Mengambil darah intra vena untuk pemeriksaan laboratorium\',\'t\',\'…... (Free text jenis pemeriksaan lab)\'),
            (1038,\'Mengambil darah arteri untuk pemeriksaan analisa gas darah\',\'t\',\'…... (Free text jenis pemeriksaan lab)\'),
            (1038,\'Memberikan terapi oksigen \',\'t\',\'…... (Free text untuk penulisan jenis oksigen yang diberikan, contoh rebreathing mask & berapa liter/menit)\'),
            (1038,\'Memonitor mode ventilator pasien \',\'t\',\'…... (Free text untuk penulisan jenis mode ventilator) cth SIMV, PEEP, IMV, CPAP\'),
            (1038,\'Memberikan nebulizer \',\'f\',NULL),
            (1038,\'Melakukan balans cairan 24 Jam\',\'f\',NULL),
            (1038,\'Melakukan balans cairan per 6 Jam \',\'f\',NULL),
            (1038,\'Membuang cairan per NGT\',\'f\',NULL),
            (1038,\'Membuang cairan urin\',\'f\',NULL),
            (1038,\'Memasang kateter urin\',\'f\',NULL),
            (1038,\'Melepas kateter urin\',\'f\',NULL),
            (1038,\'Mambantu pasien BAK\',\'f\',NULL),
            (1038,\'Membantu pasien BAB\',\'f\',NULL),
            (1038,\'Memberikan nutrisi per NGT\',\'f\',NULL),
            (1038,\'Memberikan nutrisi per OGT\',\'f\',NULL),
            (1038,\'Menyajikan makanan pasien\',\'f\',NULL),
            (1038,\'Oral care\',\'t\',\'…... (Free text untuk kondisi gusi perdarahan/tidak, pasien tampak segar dstnya)\'),
            (1038,\'Memberikan fototerapi\',\'f\',NULL),
            (1038,\'Perawatan tali pusat \',\'f\',NULL),
            (1038,\'Mengukur lingkar kepala \',\'f\',NULL),
            (1038,\'Mengukur lingkar perut\',\'f\',NULL),
            (1038,\'Mengukur lingkar lengan\',\'f\',NULL),
            (1038,\'Melakukan nebulizer\',\'f\',NULL),
            (1038,\'Melakukan perekaman EKG \',\'f\',NULL),
            (1038,\'Menghitung balans cairan per 6 Jam\',\'f\',NULL),
            (1038,\'Menghitung balans cairan per 24 Jam\',\'f\',NULL),
            (1038,\'Discharge planning\',\'f\',NULL),
            (1038,\'Menyiapkan dokumen pasien pulang \',\'f\',NULL),
            (1038,\'Menyiapkan obat-obatan pasien pulang\',\'f\',NULL),
            (1038,\'Mendampingi Dokter visit\',\'f\',NULL),
            (1038,\'Memberikan kompres dingin\',\'f\',NULL),
            (1038,\'Memberikan kompres hangat\',\'f\',NULL),
            (1038,\'Memberikan transfusi darah \',\'t\',\'…... (Free text untuk penulisan jenis transfusi) cth PRC, WB, TC, FFP\'),
            (1038,\'Melakukan gastro cooling\',\'t\',\'…... (Free text untuk kesiapan tempat dstnya)\'),
            (1038,\'Menghubungi RS tempat pasien akan dirujuk\',\'f\',NULL),
            (1038,\'Merujuk pasien ke RS lain \',\'f\',NULL),
            (1038,\'Ceklis pemakaian alat \',\'f\',NULL),
            (1038,\'Menginput pemakaian obat dari troli emergensi\',\'t\',\'…... (Free text untuk pemakaian obat troli emergensi saat code blue)\'),
            (1038,\'Menghubungi unit farmasi untuk pemberian terapi pasien\',\'t\',\'…... (Free text untuk pemakaian obat stop/lanjut/penambahan)\'),
            (1038,\'Mengukur CVP\',\'t\',\'…... (Free text untuk nilai CVP)\'),
            (1038,\'Monitor cairan serebro-spinalis (mis: warna, konsistensi)\',\'t\',\'…... (Free text untuk warna, jumlah)\'),
            (1038,\'Memberikan posisi nyaman untuk pasien \',\'t\',\'…... (Free text untuk fowler, semifowler dll)\'),
            (1038,\'Memonitor tanda dan gejala peningkatan TIK\',\'t\',\'…... (Free text untuk TD meningkat, bradikardi, pola napas ireguler dstnya)\'),
            (1038,\'Memberikan pencahar \',\'t\',\'…... (Free text untuk obat pencahar )\'),
            (1038,\'Memberikan makan pasien \',\'t\',\'…... (Free text untuk apakah apakah makan habis )\'),
            (1038,\'Memotivasi pasien untuk menarik napas dalam jika nyeri timbul\',\'f\',NULL),
            (1038,\'Morning care \',\'t\',\'…... (Free text untuk tindakan morning care )\'),
            (1039,\'Pengkajian awal kebidanan\',\'f\',NULL),
            (1039,\'Anamnesa pasien: keluhan dan melengkapi data pasien\',\'f\',NULL),
            (1039,\'Mengukur suhu tubuh pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan suhu tubuh)\'),
            (1039,\'Mengukur tekanan darah pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan tekanan darah)\'),
            (1039,\'Mengukur nadi dan pernapasan pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan nadi dan pernapasan)\'),
            (1039,\'Mengukur tinggi badan pasien \',\'f\',NULL),
            (1039,\'Menimbang berat badan pasien \',\'f\',NULL),
            (1039,\'Mengukur Tinggi Fundus Uteri (TFU) \',\'f\',NULL),
            (1039,\'Cek status imunisasi TT\',\'f\',NULL),
            (1039,\'Mengukur LILA (Lingkar Lengan Atas) \',\'f\',NULL),
            (1039,\'Melakukan pemeriksaan dalam jika terdapat indikasi\',\'f\',NULL),
            (1039,\'Melakukan pengukuran denyut jantung janin (DJJ) \',\'f\',NULL),
            (1039,\'Asisten dokter dalam pemeriksaan pasien \',\'f\',NULL),
            (1039,\'Menyerahkan hasil pemeriksaan kepada pasien\',\'t\',\'…... (Free text untuk penulisan hasil pemeriksaan laboratorium / radiologi / dll\'),
            (1039,\'Memberikan edukasi sesuai dengan kondisi dan keluhan pasien \',\'t\',\'…... (Free text untuk penulisan edukasi)\'),
            (1039,\'Memberikan konseling sesuai dengan advice dokter\',\'f\',NULL),
            (1039,\'Asisten dokter dalam pemeriksaan USG\',\'f\',NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210723_033638_improvment_catatan_keperawatan_US165 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210723_033638_improvment_catatan_keperawatan_US165 cannot be reverted.\n";

        return false;
    }
    */
}
