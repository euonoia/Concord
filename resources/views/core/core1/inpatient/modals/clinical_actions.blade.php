{{-- Reusable Clinical Actions Modals --}}

<!-- Vitals (Triage) Modal -->
<div id="vitalsModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:450px; max-width:90%;">
        <div class="core1-header border-bottom mb-20 pb-10">
            <h3 class="core1-title">Record Patient Vitals</h3>
            <p class="core1-subtitle">Patient: <span id="vitalsPatientName" class="font-bold text-dark"></span></p>
        </div>
        <form id="vitalsForm" method="POST">
            @csrf
            <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="mb-10">
                    <label class="font-bold block mb-5">Blood Pressure</label>
                    <input type="text" name="blood_pressure" class="core1-input w-full" placeholder="e.g. 120/80">
                </div>
                <div class="mb-10">
                    <label class="font-bold block mb-5">Heart Rate (bpm)</label>
                    <input type="number" name="heart_rate" class="core1-input w-full" placeholder="e.g. 72">
                </div>
                <div class="mb-10">
                    <label class="font-bold block mb-5">Temperature (°C)</label>
                    <input type="number" step="0.1" name="temperature" class="core1-input w-full" placeholder="e.g. 36.5">
                </div>
                <div class="mb-10">
                    <label class="font-bold block mb-5">SpO2 (%)</label>
                    <input type="number" name="oxygen_saturation" class="core1-input w-full" placeholder="e.g. 98">
                </div>
            </div>
            <div class="mb-20">
                <label class="font-bold block mb-5">Clinical Observation Notes</label>
                <textarea name="notes" class="core1-input w-full" rows="2" placeholder="Any visible symptoms or nursing observations..."></textarea>
            </div>
            <div class="core1-flex-gap-2 justify-end pt-10 border-top">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('vitalsModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Save Vitals</button>
            </div>
        </form>
    </div>
</div>

<!-- Clinical Notes (Consultation) Modal -->
<div id="notesModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:700px; max-width:90%; max-height: 90vh; overflow-y: auto;">
        <div class="core1-header border-bottom mb-20 pb-10">
            <h3 class="core1-title">Clinical Notes</h3>
            <p class="core1-subtitle">Patient: <span id="notesPatientName" class="font-bold text-dark"></span></p>
        </div>
        <form id="notesForm" method="POST">
            @csrf
            <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label class="font-bold block mb-5">Subjective</label>
                    <textarea name="subjective" class="core1-input w-full" rows="3" placeholder="Symptoms, patient complaints..."></textarea>
                </div>
                <div>
                    <label class="font-bold block mb-5">Objective</label>
                    <textarea name="objective" class="core1-input w-full" rows="3" placeholder="Physical examination, observations..."></textarea>
                </div>
                <div>
                    <label class="font-bold block mb-5">Assessment</label>
                    <textarea name="assessment" class="core1-input w-full" rows="3" placeholder="Clinical diagnosis / impression..."></textarea>
                </div>
                <div>
                    <label class="font-bold block mb-5">Plan</label>
                    <textarea name="plan" class="core1-input w-full" rows="3" placeholder="Treatment plan, orders, next steps..."></textarea>
                </div>
            </div>
            <div class="mt-15 mb-20">
                <label class="font-bold block mb-5">Confidential internal remarks</label>
                <textarea name="doctor_notes" class="core1-input w-full" rows="2" placeholder="Internal clinical notes..."></textarea>
            </div>
            <div class="core1-flex-gap-2 justify-end pt-10 border-top">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('notesModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Save Clinical Note</button>
            </div>
        </form>
    </div>
</div>

<!-- Lab Order Modal -->
<div id="labModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
        <div class="core1-header border-bottom mb-15">
            <h4 class="font-bold">Order Laboratory Test</h4>
        </div>
        <form method="POST" action="{{ route('core1.outpatient.storeLabOrder') }}">
            @csrf
            <input type="hidden" name="encounter_id" id="labEncounterId">
            <div class="mb-10">
                <label class="font-bold block mb-5">Test Name</label>
                <select name="test_name" class="core1-input w-full" required>
                    <option value="">Select Test...</option>
                    <option value="Complete Blood Count (CBC)">Complete Blood Count (CBC)</option>
                    <option value="Urinalysis">Urinalysis</option>
                    <option value="Blood Chemistry Panel">Blood Chemistry Panel</option>
                    <option value="Lipid Panel">Lipid Panel</option>
                    <option value="Microbiology/Molecular Tests">Microbiology/Molecular Tests</option>
                    <option value="X-Ray Chest">X-Ray Chest</option>
                </select>
            </div>
            <div class="mb-10">
                <label class="font-bold block mb-5">Priority</label>
                <select name="priority" class="core1-input w-full">
                    <option value="Routine">Routine</option>
                    <option value="Urgent">Urgent</option>
                    <option value="STAT">STAT (Emergency)</option>
                </select>
            </div>
            <div class="mb-15">
                <label class="font-bold block mb-5">Clinical Indication</label>
                <textarea name="clinical_note" class="core1-input w-full" rows="2" placeholder="Reason for test..."></textarea>
            </div>
            <div class="core1-flex-gap-2 justify-end">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('labModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Order Test</button>
            </div>
        </form>
    </div>
</div>

<!-- Prescription Modal -->
<div id="prescriptionModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
        <div class="core1-header border-bottom mb-15">
            <h4 class="font-bold">Issue Inpatient Prescription</h4>
        </div>
        <form method="POST" action="{{ route('core1.outpatient.storePrescription') }}">
            @csrf
            <input type="hidden" name="encounter_id" id="rxEncounterId">
            <div class="mb-10">
                <label class="font-bold block mb-5">Medication Name</label>
                <input type="text" name="medication" class="core1-input w-full" required placeholder="e.g. Cefuroxime 500mg IV">
            </div>
            <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                <div>
                    <label class="font-bold block mb-5">Dosage</label>
                    <input type="text" name="dosage" class="core1-input w-full" required placeholder="e.g. 1 vial q12h">
                </div>
                <div>
                    <label class="font-bold block mb-5">Duration</label>
                    <input type="text" name="duration" class="core1-input w-full" placeholder="e.g. 7 days">
                </div>
            </div>
            <div class="mb-15">
                <label class="font-bold block mb-5">Instructions</label>
                <input type="text" name="instructions" class="core1-input w-full" placeholder="e.g. Administer via slow IV push">
            </div>
            <div class="core1-flex-gap-2 justify-end">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('prescriptionModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Prescribe</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Shared behavior for clinical modals
    function openVitalsModal(encounterId, patientName) {
        document.getElementById('vitalsPatientName').innerText = patientName;
        document.getElementById('vitalsForm').action = '/core/outpatient/' + encounterId + '/triage';
        document.getElementById('vitalsModal').style.display = 'flex';
    }

    function openNotesModal(encounterId, patientName) {
        document.getElementById('notesPatientName').innerText = patientName;
        document.getElementById('notesForm').action = '/core/outpatient/' + encounterId + '/consultation';
        document.getElementById('notesModal').style.display = 'flex';
    }

    function openLabOrderModal(encounterId) {
        document.getElementById('labEncounterId').value = encounterId;
        document.getElementById('labModal').style.display = 'flex';
    }

    function openMedicationModal(encounterId) {
        document.getElementById('rxEncounterId').value = encounterId;
        document.getElementById('prescriptionModal').style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        if (id === 'medicalRecordModal') {
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const clinicalModals = ['vitalsModal', 'notesModal', 'labModal', 'prescriptionModal', 'medicalRecordModal', 'dischargeModal'];
        clinicalModals.forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(id);
                });
            }
        });
    });
</script>
