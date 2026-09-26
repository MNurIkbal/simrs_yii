<?php

namespace Doco\Libraries\Asuransi\TaskInter;

interface InsuranceInterface {
    public function Authentication();

    public function GetReferensi();

    public function CekSisaLimitPeserta();

    public function CekEligiblePeserta();

    public function CetakStrukPendaftaran();
    
    public function CetakStrukPengesahan();
    
    public function CetakSuratJaminan();

    public function CreateTagihan();

    public function DaftarKunjungan();

    public function Discharging();

    public function KlaimPending();

    public function MonitoringTagihan();

    public function Pendaftaran();

    public function ReferensiBenefitPeserta();

    public function ReferensiSubBenefitPeserta();

    public function Pembatalan();

    public function UploadDokumenKlaim();
    
    public function UnbatchKlaim();

    public function ReferensiKepesertaan();
}