<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Object;

use Integrasi\Components\DocoConstants;

class InaRisObject extends DocoBaseObject
{

    const CYTO = "A21";

    const UMUM = "A23";

    private $country = "ID";

    private $accession_number;

    private $filler_order_number;

    private $placer_order_number;

    private $admission_id;

    private $station_ae_title;

    private $station_name;

    private $modality;

    private $scheduled_date_time;

    private $requesting_service;

    private $mrn;

    private $other_id;

    private $patientName;

    private $dob;

    private $patientAddress;

    private $patientPhone;

    private $sex;

    private $weight;

    private $height;

    private $religion;

    private $patientComment;

    private $occupation;

    private $ethnic_group;

    private $resident_country;

    private $state;

    private $smoking_status;

    private $pregnancy_status;

    private $medical_aller;

    private $special_needs;

    private $allergies;

    private $referringPhysicianName;

    private $referringPhysicianAddress;

    private $referringPhysicianPhone;

    private $referringPhysicianInstitutionName;

    private $referringPhysicianInstitutionAddress;

    private $referringPhysicianInstitutionPhone;

    private $requestingPhysicianName;

    private $requestingPhysicianAddress;

    private $requestingPhysicianPhone;

    private $requestingPhysicianInstitutionName;

    private $requestingPhysicianInstitutionAddress;

    private $requestingPhysicianInstitutionPhone;

    private $requestedProcedureId;

    private $requestedProcedureDescription;

    private $comment;

    private $priority;

    private $location;

    private $scheduledProcedureStepId;

    private $scheduledProcedureStepDescription;

    private $start_date_time;

    private $end_date_time;

    /**
     * @return mixed
     */
    public function getAccessionNumber()
    {
        return $this->accession_number;
    }

    /**
     * @param mixed $accession_number
     *
     * @return self
     */
    public function setAccessionNumber($accession_number)
    {
        $this->accession_number = $accession_number;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getFillerOrderNumber()
    {
        return $this->filler_order_number;
    }

    /**
    * @param mixed $filler_order_number
    *
    * @return self
    */
    public function setFillerOrderNumber($filler_order_number)
    {
        $this->filler_order_number = $filler_order_number;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPlacerOrderNumber()
    {
        return $this->placer_order_number;
    }

    /**
    * @param mixed $placer_order_number
    *
    * @return self
    */
    public function setPlacerOrderNumber($placer_order_number)
    {
        $this->placer_order_number = $placer_order_number;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getAdmissionId()
    {
        return $this->admission_id;
    }

    /**
    * @param mixed $admission_id
    *
    * @return self
    */
    public function setAdmissionId($admission_id)
    {
        $this->admission_id = $admission_id;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getStationAeTitle()
    {
        return $this->station_ae_title;
    }

    /**
    * @param mixed $station_ae_title
    *
    * @return self
    */
    public function setStationAeTitle($station_ae_title)
    {
        $this->station_ae_title = $station_ae_title;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getStationName()
    {
        return $this->station_name;
    }

    /**
    * @param mixed $station_name
    *
    * @return self
    */
    public function setStationName($station_name)
    {
        $this->station_name = $station_name;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getModality()
    {
        return $this->modality;
    }

    /**
    * @param mixed $modality
    *
    * @return self
    */
    public function setModality($modality)
    {
        $this->modality = $modality;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getScheduledDateTime()
    {
        return $this->scheduled_date_time;
    }

    /**
    * @param mixed $scheduled_date_time
    *
    * @return self
    */
    public function setScheduledDateTime($scheduled_date_time)
    {
        $this->scheduled_date_time = $scheduled_date_time;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingService()
    {
        return $this->requesting_service;
    }

    /**
    * @param mixed $requesting_service
    *
    * @return self
    */
    public function setRequestingService($requesting_service)
    {
        $this->requesting_service = $requesting_service;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getMrn()
    {
        return $this->mrn;
    }

    /**
    * @param mixed $mrn
    *
    * @return self
    */
    public function setMrn($mrn)
    {
        $this->mrn = $mrn;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getOtherId()
    {
        return $this->other_id;
    }

    /**
    * @param mixed $other_id
    *
    * @return self
    */
    public function setOtherId($other_id)
    {
        $this->other_id = $other_id;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getpatientName()
    {
        return $this->patientName;
    }

    /**
    * @param mixed $patientName
    *
    * @return self
    */
    public function setpatientName($patientName)
    {
        $this->patientName = $patientName;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getDob()
    {
        return $this->dob;
    }

    /**
    * @param mixed $dob
    *
    * @return self
    */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPatientAddress()
    {
        return $this->patientAddress;
    }

    /**
    * @param mixed $patientAddress
    *
    * @return self
    */
    public function setPatientAddress($patientAddress)
    {
        $this->patientAddress = $patientAddress;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPatientPhone()
    {
        return $this->patientPhone;
    }

    /**
    * @param mixed $patientPhone
    *
    * @return self
    */
    public function setPatientPhone($patientPhone)
    {
        $this->patientPhone = $patientPhone;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getSex()
    {
        return $this->sex;
    }

    /**
    * @param mixed $sex
    *
    * @return self
    */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
    * @param mixed $weight
    *
    * @return self
    */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getHeight()
    {
        return $this->height;
    }

    /**
    * @param mixed $height
    *
    * @return self
    */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
    * @param mixed $religion
    *
    * @return self
    */
    public function setReligion($religion)
    {
        $this->religion = $religion;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPatientComment()
    {
        return $this->patientComment;
    }

    /**
    * @param mixed $patientComment
    *
    * @return self
    */
    public function setPatientComment($patientComment)
    {
        $this->patientComment = $patientComment;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
    * @param mixed $occupation
    *
    * @return self
    */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getEthnicGroup()
    {
        return $this->ethnic_group;
    }

    /**
    * @param mixed $ethnic_group
    *
    * @return self
    */
    public function setEthnicGroup($ethnic_group)
    {
        $this->ethnic_group = $ethnic_group;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getResidentCountry()
    {
        return $this->resident_country;
    }

    /**
    * @param mixed $resident_country
    *
    * @return self
    */
    public function setResidentCountry($resident_country)
    {
        $this->resident_country = $resident_country;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getState()
    {
        return $this->state;
    }

    /**
    * @param mixed $state
    *
    * @return self
    */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getSmokingStatus()
    {
        return $this->smoking_status;
    }

    /**
    * @param mixed $smoking_status
    *
    * @return self
    */
    public function setSmokingStatus($smoking_status)
    {
        $this->smoking_status = $smoking_status;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPregnancyStatus()
    {
        return $this->pregnancy_status;
    }

    /**
    * @param mixed $pregnancy_status
    *
    * @return self
    */
    public function setPregnancyStatus($pregnancy_status)
    {
        $this->pregnancy_status = $pregnancy_status;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getMedicalAller()
    {
        return $this->medical_aller;
    }

    /**
    * @param mixed $medical_aller
    *
    * @return self
    */
    public function setMedicalAller($medical_aller)
    {
        $this->medical_aller = $medical_aller;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getSpecialNeeds()
    {
        return $this->special_needs;
    }

    /**
    * @param mixed $special_needs
    *
    * @return self
    */
    public function setSpecialNeeds($special_needs)
    {
        $this->special_needs = $special_needs;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getAllergies()
    {
        return $this->allergies;
    }

    /**
    * @param mixed $allergies
    *
    * @return self
    */
    public function setAllergies($allergies)
    {
        $this->allergies = $allergies;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReferringPhysicianName()
    {
        return $this->referringPhysicianName;
    }

    /**
    * @param mixed $referringPhysicianName
    *
    * @return self
    */
    public function setReferringPhysicianName($referringPhysicianName)
    {
        $this->referringPhysicianName = $referringPhysicianName;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReferringPhysicianAddress()
    {
        return $this->referringPhysicianAddress;
    }

    /**
    * @param mixed $referringPhysicianAddress
    *
    * @return self
    */
    public function setReferringPhysicianAddress($referringPhysicianAddress)
    {
        $this->referringPhysicianAddress = $referringPhysicianAddress;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReferringPhysicianPhone()
    {
        return $this->referringPhysicianPhone;
    }

    /**
    * @param mixed $referringPhysicianPhone
    *
    * @return self
    */
    public function setReferringPhysicianPhone($referringPhysicianPhone)
    {
        $this->referringPhysicianPhone = $referringPhysicianPhone;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReferringPhysicianInstitutionName()
    {
        return $this->referringPhysicianInstitutionName;
    }

    /**
    * @param mixed $referringPhysicianInstitutionName
    *
    * @return self
    */
    public function setReferringPhysicianInstitutionName($referringPhysicianInstitutionName)
    {
        $this->referringPhysicianInstitutionName = $referringPhysicianInstitutionName;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReferringPhysicianInstitutionAddress()
    {
        return $this->referringPhysicianInstitutionAddress;
    }

    /**
    * @param mixed $referringPhysicianInstitutionAddress
    *
    * @return self
    */
    public function setReferringPhysicianInstitutionAddress($referringPhysicianInstitutionAddress)
    {
        $this->referringPhysicianInstitutionAddress = $referringPhysicianInstitutionAddress;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getReferringPhysicianInstitutionPhone()
    {
        return $this->referringPhysicianInstitutionPhone;
    }

    /**
    * @param mixed $referringPhysicianInstitutionPhone
    *
    * @return self
    */
    public function setReferringPhysicianInstitutionPhone($referringPhysicianInstitutionPhone)
    {
        $this->referringPhysicianInstitutionPhone = $referringPhysicianInstitutionPhone;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingPhysicianName()
    {
        return $this->requestingPhysicianName;
    }

    /**
    * @param mixed $requestingPhysicianName
    *
    * @return self
    */
    public function setRequestingPhysicianName($requestingPhysicianName)
    {
        $this->requestingPhysicianName = $requestingPhysicianName;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingPhysicianAddress()
    {
        return $this->requestingPhysicianAddress;
    }

    /**
    * @param mixed $requestingPhysicianAddress
    *
    * @return self
    */
    public function setRequestingPhysicianAddress($requestingPhysicianAddress)
    {
        $this->requestingPhysicianAddress = $requestingPhysicianAddress;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingPhysicianPhone()
    {
        return $this->requestingPhysicianPhone;
    }

    /**
    * @param mixed $requestingPhysicianPhone
    *
    * @return self
    */
    public function setRequestingPhysicianPhone($requestingPhysicianPhone)
    {
        $this->requestingPhysicianPhone = $requestingPhysicianPhone;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingPhysicianInstitutionName()
    {
        return $this->requestingPhysicianInstitutionName;
    }

    /**
    * @param mixed $requestingPhysicianInstitutionName
    *
    * @return self
    */
    public function setRequestingPhysicianInstitutionName($requestingPhysicianInstitutionName)
    {
        $this->requestingPhysicianInstitutionName = $requestingPhysicianInstitutionName;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingPhysicianInstitutionAddress()
    {
        return $this->requestingPhysicianInstitutionAddress;
    }

    /**
    * @param mixed $requestingPhysicianInstitutionAddress
    *
    * @return self
    */
    public function setRequestingPhysicianInstitutionAddress($requestingPhysicianInstitutionAddress)
    {
        $this->requestingPhysicianInstitutionAddress = $requestingPhysicianInstitutionAddress;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestingPhysicianInstitutionPhone()
    {
        return $this->requestingPhysicianInstitutionPhone;
    }

    /**
    * @param mixed $requestingPhysicianInstitutionPhone
    *
    * @return self
    */
    public function setRequestingPhysicianInstitutionPhone($requestingPhysicianInstitutionPhone)
    {
        $this->requestingPhysicianInstitutionPhone = $requestingPhysicianInstitutionPhone;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestedProcedureId()
    {
        return $this->requestedProcedureId;
    }

    /**
    * @param mixed $requestedProcedureId
    *
    * @return self
    */
    public function setRequestedProcedureId($requestedProcedureId)
    {
        $this->requestedProcedureId = $requestedProcedureId;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getRequestedProcedureDescription()
    {
        return $this->requestedProcedureDescription;
    }

    /**
    * @param mixed $requestedProcedureDescription
    *
    * @return self
    */
    public function setRequestedProcedureDescription($requestedProcedureDescription)
    {
        $this->requestedProcedureDescription = $requestedProcedureDescription;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getComment()
    {
        return $this->comment;
    }

    /**
    * @param mixed $comment
    *
    * @return self
    */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
    * @param mixed $priority
    *
    * @return self
    */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getLocation()
    {
        return $this->location;
    }

    /**
    * @param mixed $location
    *
    * @return self
    */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getScheduledProcedureStepId()
    {
        return $this->scheduledProcedureStepId;
    }

    /**
    * @param mixed $scheduledProcedureStepId
    *
    * @return self
    */
    public function setScheduledProcedureStepId($scheduledProcedureStepId)
    {
        $this->scheduledProcedureStepId = $scheduledProcedureStepId;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getScheduledProcedureStepDescription()
    {
        return $this->scheduledProcedureStepDescription;
    }

    /**
    * @param mixed $scheduledProcedureStepDescription
    *
    * @return self
    */
    public function setScheduledProcedureStepDescription($scheduledProcedureStepDescription)
    {
        $this->scheduledProcedureStepDescription = $scheduledProcedureStepDescription;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getStartDateTime()
    {
        return $this->start_date_time;
    }

    /**
    * @param mixed $start_date_time
    *
    * @return self
    */
    public function setStartDateTime($start_date_time)
    {
        $this->start_date_time = $start_date_time;

        return $this;
    }

    /**
    * @return mixed
    */
    public function getEndDateTime()
    {
        return $this->end_date_time;
    }

    /**
    * @param mixed $end_date_time
    *
    * @return self
    */
    public function setEndDateTime($end_date_time)
    {
        $this->end_date_time = $end_date_time;

        return $this;
    }

    private function setNullAttr($value)
    {
        return isset($value) ? $value : 'none';
    }

    private function setZeroAttr($value)
    {
        return isset($value) ? $value : 0;
    }

    public function buildArray()
    {
        return [
            'accession_number' => $this->setNullAttr($this->accession_number),
            'filler_order_number' => $this->setNullAttr($this->filler_order_number),
            'placer_order_number' => $this->setNullAttr($this->placer_order_number),
            'admission_id' => $this->setNullAttr($this->admission_id),
            'station_ae_title' => $this->setNullAttr($this->station_ae_title),
            'station_name' => $this->setNullAttr($this->station_name),
            'modality' => $this->setNullAttr($this->modality),
            'scheduled_date_time' => $this->setNullAttr($this->scheduled_date_time),
            'requesting_service' => $this->setNullAttr($this->requesting_service),
            'patient' => [
                'mrn' => $this->setNullAttr($this->mrn),
                'other_id' => $this->setNullAttr($this->other_id),
                'name' => $this->setNullAttr($this->patientName),
                'dob' => $this->setNullAttr($this->dob),
                'address' => $this->setNullAttr($this->patientAddress),
                'phone' => $this->setNullAttr($this->patientPhone),
                'sex' => $this->setNullAttr($this->sex),
                'weight' => $this->setZeroAttr($this->weight),
                'height' => $this->setZeroAttr($this->height),
                'religion' => $this->setNullAttr($this->religion),
                'comment' => $this->setNullAttr($this->comment),
                'occupation' => $this->setNullAttr($this->occupation),
                'ethnic_group' => $this->setNullAttr($this->ethnic_group),
                'resident_country' => $this->setNullAttr($this->resident_country),
                'state' => $this->setNullAttr($this->state),
                'smoking_status' => $this->setNullAttr($this->smoking_status),
                'pregnancy_status' => $this->setNullAttr($this->pregnancy_status),
                'medical_aller' => $this->setNullAttr($this->medical_aller),
                'special_needs' => $this->setNullAttr($this->special_needs),
                'allergies' => $this->setNullAttr($this->allergies),
            ],
            'referring_physician' => [
                'name' => $this->setNullAttr($this->referringPhysicianName),
                'address' => $this->setNullAttr($this->referringPhysicianAddress),
                'phone' => $this->setNullAttr($this->referringPhysicianPhone),
                'institution_name' => $this->setNullAttr($this->referringPhysicianInstitutionName),
                'institution_address' => $this->setNullAttr($this->referringPhysicianInstitutionAddress),
                'institution_phone' => $this->setNullAttr($this->referringPhysicianInstitutionPhone),
            ],
            'requesting_physician' => [
                'name' => $this->setNullAttr($this->requestingPhysicianName),
                'address' => $this->setNullAttr($this->requestingPhysicianAddress),
                'phone' => $this->setNullAttr($this->requestingPhysicianPhone),
                'institution_name' => $this->setNullAttr($this->requestingPhysicianInstitutionName),
                'institution_address' => $this->setNullAttr($this->requestingPhysicianInstitutionAddress),
                'institution_phone' => $this->setNullAttr($this->requestingPhysicianInstitutionPhone),
            ],
            'requested_procedure' => [
                'id' => $this->setNullAttr($this->requestedProcedureId),
                'description' => $this->setNullAttr($this->requestedProcedureDescription),
                'comment' => $this->setNullAttr($this->comment),
                'priority' => $this->setNullAttr($this->priority),
                'location' => $this->setNullAttr($this->location),
            ],
            'scheduled_procedure_step' => [
                'id' => $this->setNullAttr($this->scheduledProcedureStepId),
                'description' => $this->setNullAttr($this->requestedProcedureDescription),
                'start_date_time' => $this->setNullAttr($this->start_date_time),
                'end_date_time' => $this->setNullAttr($this->end_date_time),
            ]
        ];
    }

}