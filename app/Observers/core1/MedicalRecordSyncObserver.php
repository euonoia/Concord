<?php

namespace App\Observers\core1;

use App\Models\user\Core\core1\MedicalRecord;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\Prescription;
use App\Models\core1\LabOrder;
use App\Models\core1\SurgeryOrder;
use App\Models\core1\DietOrder;

class MedicalRecordSyncObserver
{
    /**
     * Handle the "saved" event for specialized models.
     */
    public function saved($model): void
    {
        $patientId = $model->patient_id ?? $model->encounter->patient_id ?? null;
        $doctorId = $model->doctor_id ?? $model->encounter->doctor_id ?? null;

        if (!$patientId) {
            return;
        }

        $recordData = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'record_date' => $model->created_at ?? now(),
            'reference_id' => $model->id,
            'reference_type' => get_class($model),
        ];

        // Model-specific mapping
        if ($model instanceof Triage) {
            $recordData['record_type'] = 'triage';
            $recordData['notes'] = sprintf(
                "Vitals recorded - BP: %s, HR: %s, RR: %s, Temp: %s, SpO2: %s. Triage Level: %s. Notes: %s",
                $model->blood_pressure ?? 'N/A',
                $model->heart_rate ?? 'N/A',
                $model->respiratory_rate ?? 'N/A',
                $model->temperature ?? 'N/A',
                $model->spo2 ?? 'N/A',
                $model->triage_level ?? 'N/A',
                $model->notes ?? 'None'
            );
        } elseif ($model instanceof Consultation) {
            $recordData['record_type'] = 'consultation';
            $recordData['diagnosis'] = $model->assessment;
            $recordData['treatment'] = $model->plan;
            $recordData['notes'] = sprintf(
                "Subjective: %s\nObjective: %s\nNotes: %s",
                $model->subjective ?? 'N/A',
                $model->objective ?? 'N/A',
                $model->doctor_notes ?? 'None'
            );
        } elseif ($model instanceof Prescription) {
            $recordData['record_type'] = 'prescription';
            $recordData['prescription'] = sprintf(
                "%s - Dosage: %s, Instructions: %s, Duration: %s, Qty: %s",
                $model->medication,
                $model->dosage,
                $model->instructions ?? 'N/A',
                $model->duration ?? 'N/A',
                $model->quantity ?? 'N/A'
            );
        } elseif ($model instanceof LabOrder) {
            $recordData['record_type'] = 'lab_order';
            $recordData['diagnosis'] = $model->test_name;
            $recordData['notes'] = $model->clinical_note;
        } elseif ($model instanceof SurgeryOrder) {
            $recordData['record_type'] = 'surgery';
            $recordData['diagnosis'] = $model->procedure_name;
            $recordData['notes'] = $model->clinical_indication;
        } elseif ($model instanceof DietOrder) {
            $recordData['record_type'] = 'diet';
            $recordData['diagnosis'] = $model->diet_type;
            $recordData['notes'] = $model->instructions;
        } else {
            return;
        }

        MedicalRecord::updateOrCreate(
            [
                'reference_id' => $model->id,
                'reference_type' => get_class($model),
            ],
            $recordData
        );
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted($model): void
    {
        MedicalRecord::where('reference_id', $model->id)
            ->where('reference_type', get_class($model))
            ->delete();
    }
}
