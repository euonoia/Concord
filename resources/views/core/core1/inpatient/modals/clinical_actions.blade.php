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
                    <input type="number" name="spo2" class="core1-input w-full" placeholder="e.g. 98">
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
            <div class="mb-10" style="position: relative;">
                <label class="font-bold block mb-5">Medication Name</label>
                <input type="text" name="medication" id="medicationSearchInpatient" class="core1-input w-full" autocomplete="off" required placeholder="e.g. Cefuroxime 500mg IV">
                <div id="drugSearchResultsInpatient" class="core1-card" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1100; max-height:200px; overflow-y:auto; padding:5px; margin-top:5px; box-shadow: var(--shadow-md);"></div>
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
            <div class="mb-10">
                <label class="font-bold block mb-5">Quantity</label>
                <input type="number" name="quantity" class="core1-input w-full" required placeholder="Number of units to dispense" min="1">
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

<!-- Surgery Order Modal -->
<div id="surgeryModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:450px; max-width:90%;">
        <div class="core1-header border-bottom mb-15">
            <h4 class="font-bold">Order Surgical Procedure</h4>
        </div>
        <form method="POST" action="{{ route('core1.outpatient.storeSurgeryOrder') }}">
            @csrf
            <input type="hidden" name="encounter_id" id="surgeryEncounterId">
            <div class="mb-10">
                <label class="font-bold block mb-5">Procedure Name</label>
                <input type="text" name="procedure_name" class="core1-input w-full" required placeholder="e.g. Appendectomy, Hernia Repair">
            </div>
            <div class="mb-10">
                <label class="font-bold block mb-5">Priority</label>
                <select name="priority" class="core1-input w-full">
                    <option value="Routine">Routine</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>
            <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                <div>
                    <label class="font-bold block mb-5">Proposed Date</label>
                    <input type="date" name="proposed_date" class="core1-input w-full" required min="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label class="font-bold block mb-5">Proposed Time</label>
                    <input type="time" name="proposed_time" class="core1-input w-full" required>
                </div>
            </div>
            <div class="mb-15">
                <textarea name="clinical_indication" class="core1-input w-full" rows="3" placeholder="Reason for surgery..."></textarea>
            </div>
            <div class="core1-flex-gap-2 justify-end">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('surgeryModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Order Surgery</button>
            </div>
        </form>
    </div>
</div>

<!-- Diet Order Modal -->
<div id="dietModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
        <div class="core1-header border-bottom mb-15">
            <h4 class="font-bold">Nutrition & Diet Order</h4>
        </div>
        <form method="POST" action="{{ route('core1.outpatient.storeDietOrder') }}">
            @csrf
            <input type="hidden" name="encounter_id" id="dietEncounterId">
            <div class="mb-10">
                <label class="font-bold block mb-5">Diet Type</label>
                <select name="diet_type" class="core1-input w-full" required>
                    <option value="Regular">Regular Diet</option>
                    <option value="NPO">NPO (Nothing by Mouth)</option>
                    <option value="Soft">Soft / Low Residue</option>
                    <option value="Liquid">Clear Liquid</option>
                    <option value="Diabetic">Diabetic (Low Sugar)</option>
                    <option value="Renal">Renal Diet</option>
                    <option value="High Protein">High Protein</option>
                </select>
            </div>
            <div class="mb-15">
                <label class="font-bold block mb-5">Special Instructions</label>
                <textarea name="instructions" class="core1-input w-full" rows="2" placeholder="e.g. No seafood, allergy to nuts..."></textarea>
            </div>
            <div class="core1-flex-gap-2 justify-end">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('dietModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Set Diet</button>
            </div>
        </form>
    </div>
</div>

<!-- Discharge Clearance Modal -->
<div id="dischargeModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:600px; max-width:95%; max-height: 90vh; overflow-y: auto;">
        <div class="core1-header border-bottom mb-20 pb-10">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div>
                    <h3 class="core1-title">Doctor's Discharge Clearance</h3>
                    <p class="core1-subtitle">Finalizing care for <span id="dischargePatientName" class="font-bold text-dark"></span></p>
                </div>
            </div>
        </div>
        <form id="dischargeForm" method="POST">
            @csrf
            
            <div class="core1-stats-grid mb-20" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label class="font-bold block mb-5">Condition on Discharge</label>
                    <select name="condition_on_discharge" class="core1-input w-full" required>
                        <option value="Recovered">Recovered</option>
                        <option value="Improved" selected>Improved</option>
                        <option value="Stable">Stable</option>
                        <option value="Guarded">Guarded</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold block mb-5">Discharge Type</label>
                    <select name="discharge_type" class="core1-input w-full" required>
                        <option value="Routine">Routine Discharge</option>
                        <option value="DAMA">DAMA (Against Medical Advice)</option>
                        <option value="Transfer">Transfer to another Facility</option>
                        <option value="Death">Expired</option>
                    </select>
                </div>
            </div>

            <div class="mb-15">
                <label class="font-bold block mb-5">Final Diagnosis</label>
                <textarea name="final_diagnosis" class="core1-input w-full" rows="2" required placeholder="Enter final clinical diagnosis..."></textarea>
            </div>

            <div class="mb-15">
                <label class="font-bold block mb-5">Discharge Summary & Treatment Given</label>
                <textarea name="discharge_summary" class="core1-input w-full" rows="3" required placeholder="Brief summary of inpatient management..."></textarea>
            </div>

            <div class="core1-stats-grid mb-20" style="grid-template-columns: 2fr 1fr; gap: 20px; padding: 15px; background: var(--bg-light); border-radius: 10px; border: 1px dashed var(--border-color);">
                <div>
                    <label class="font-bold block mb-5">Follow-up Instructions</label>
                    <textarea name="follow_up_instructions" class="core1-input w-full" rows="2" placeholder="Diet, activity, medications..."></textarea>
                </div>
                <div>
                    <label class="font-bold block mb-5">Follow-up Date</label>
                    <input type="date" name="follow_up_date" class="core1-input w-full">
                </div>
            </div>

            <div class="core1-flex-gap-2 justify-end pt-10 border-top">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('dischargeModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Approve Discharge</button>
            </div>
        </form>
    </div>
</div>
<!-- Transfer Request Modal -->
<div id="transferRequestModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:500px; max-width:95%;">
        <div class="core1-header border-bottom mb-20 pb-10">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <h3 class="core1-title">Request Patient Transfer</h3>
                    <p class="core1-subtitle">Initiate a transfer request for <span id="trPatientName" class="font-bold text-dark"></span></p>
                </div>
            </div>
        </div>
        <form id="transferRequestForm" method="POST">
            @csrf
            <div class="mb-20" style="padding: 15px; background: var(--bg-light); border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size: 11px; text-transform: uppercase; color: var(--text-gray); font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">Current Location</div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-geo-alt-fill" style="color: var(--primary);"></i>
                    <span id="trCurrentBed" class="font-bold text-dark" style="font-size: 14px;"></span>
                </div>
            </div>

            <div class="mb-20">
                <label class="font-bold block mb-10" style="font-size: 14px; color: var(--text-dark);">Target Bed <span style="font-weight: normal; color: var(--text-gray); font-size: 11px;">(Optional)</span></label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div id="trSelectedBedDisplay" style="flex: 1; padding: 12px 16px; background: #fff; border: 1px solid var(--border-color); border-radius: 10px; font-size: 13px; color: var(--text-gray); min-height: 45px; display: flex; align-items: center;">
                        <span style="font-style: italic;">No specific bed selected yet...</span>
                    </div>
                    <button type="button" onclick="launchBedPickerForRequest()" class="core1-btn core1-btn-outline" style="padding: 11px 15px; border-radius: 10px; white-space: nowrap;">
                        <i class="bi bi-grid-3x3-gap"></i> Pick Bed
                    </button>
                </div>
                <input type="hidden" name="target_bed_id" id="trTargetBedId">
            </div>

            <div class="mb-20">
                <label class="font-bold block mb-5" style="font-size: 14px;">Clinical Reason for Transfer</label>
                <textarea name="reason" class="core1-input w-full" rows="2" placeholder="e.g. Condition escalation, proximity to nursing..."></textarea>
            </div>

            <div class="core1-flex-gap-2 justify-end pt-10 border-top">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('transferRequestModal')">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- Medication Administration (Selection) Modal -->
<div id="administrationModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:550px; max-width:90%; max-height: 85vh; overflow-y: auto;">
        <div class="core1-header border-bottom mb-20 pb-10">
            <h3 class="core1-title">Medication Administration</h3>
            <p class="core1-subtitle">Select medication to mark as administered for <span id="adminPatientName" class="font-bold text-dark"></span></p>
        </div>
        <div id="adminMedsList" style="display: flex; flex-direction: column; gap: 12px;">
            <!-- Medications will be loaded here via AJAX -->
            <div style="padding: 20px; text-align: center; color: var(--text-gray);">
                <i class="bi bi-arrow-repeat spin" style="font-size: 1.5rem; display: block; margin-bottom: 10px;"></i>
                Loading medications...
            </div>
        </div>
        <div class="core1-flex-gap-2 justify-end pt-20 border-top mt-20">
            <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('administrationModal')">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Shared behavior for clinical modals
    let administrationNeedsReload = false;

    function openTransferRequestModal(admissionId, patientName, currentBedLabel) {
        console.log('Opening transfer request modal for:', patientName);
        document.getElementById('trPatientName').innerText = patientName;
        document.getElementById('trCurrentBed').innerText = currentBedLabel;
        document.getElementById('trTargetBedId').value = '';
        document.getElementById('trSelectedBedDisplay').innerHTML = '<span style="font-style: italic;">No specific bed selected yet...</span>';
        
        const form = document.getElementById('transferRequestForm');
        form.action = `/core/admissions/${admissionId}/request-transfer`;
        
        document.getElementById('transferRequestModal').style.display = 'flex';
    }

    function launchBedPickerForRequest() {
        console.log('Launch bed picker for request clicked');
        // Hide the transfer request modal temporarily
        const transferModal = document.getElementById('transferRequestModal');
        if (transferModal) transferModal.style.display = 'none';

        // Show the floor map in selector mode
        const floorModal = document.getElementById('floorMapModal');
        if (!floorModal) {
            alert('Floor map not found in this page.');
            if (transferModal) transferModal.style.display = 'flex';
            return;
        }
        openFloorMap(null, 'Select Target Bed', '', null, false, true);
    }

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

    function openSurgeryOrderModal(encounterId) {
        document.getElementById('surgeryEncounterId').value = encounterId;
        document.getElementById('surgeryModal').style.display = 'flex';
    }

    function openDietOrderModal(encounterId) {
        document.getElementById('dietEncounterId').value = encounterId;
        document.getElementById('dietModal').style.display = 'flex';
    }

    function openAdministrationModal(encounterId, patientName) {
        const modal = document.getElementById('administrationModal');
        const list = document.getElementById('adminMedsList');
        document.getElementById('adminPatientName').innerText = patientName;
        
        modal.style.display = 'flex';
        list.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-gray);"><i class="bi bi-arrow-repeat core1-spin" style="font-size: 1.5rem; display: block; margin-bottom: 10px;"></i>Loading medications...</div>';

        fetch(`/core/inpatient/encounters/${encounterId}/prescriptions`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                if (data.length === 0) {
                    list.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-gray); font-style: italic;">No medications found for this patient.</div>';
                    return;
                }

                data.forEach(rx => {
                    const item = document.createElement('div');
                    item.style.padding = '12px';
                    item.style.background = 'var(--bg-light)';
                    item.style.borderRadius = '10px';
                    item.style.border = '1px solid var(--border-color)';
                    item.style.display = 'flex';
                    item.style.justifyContent = 'space-between';
                    item.style.alignItems = 'center';
                    item.style.gap = '15px';

                    const info = `
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-dark); font-size: 14px; margin-bottom: 2px;">${rx.medication}</div>
                            <div style="font-size: 11px; color: var(--text-gray); display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span style="background: var(--primary-light); color: var(--primary); padding: 1px 6px; border-radius: 4px; font-weight: 800;">Dosage: ${rx.dosage}</span>
                                ${rx.instructions ? `<span style="opacity: 0.8;"><i class="bi bi-info-circle"></i> ${rx.instructions}</span>` : ''}
                            </div>
                        </div>
                    `;

                    let action = '';
                    if (rx.status === 'Administered') {
                        action = `
                            <div style="text-align: right; min-width: 140px;">
                                <div style="color: var(--success); font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-bottom: 2px;">
                                    <i class="bi bi-check-circle-fill"></i> Administered
                                </div>
                                <div style="font-size: 10px; color: var(--text-gray); line-height: 1.2;">
                                    <div style="font-weight: 600;">By: ${rx.administered_by || 'Unknown'}</div>
                                    <div style="opacity: 0.8;">${rx.administered_at || 'N/A'}</div>
                                </div>
                            </div>
                        `;
                    } else if (rx.status === 'Dispensed') {
                        action = `
                            <button onclick="administerMedSingle(this, ${rx.id}, '${rx.administer_url}')" class="core1-btn-sm core1-btn-primary" style="padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 8px;">
                                <i class="bi bi-check2-circle"></i> Administer
                            </button>
                        `;
                    } else {
                        action = `
                            <div style="text-align: right;">
                                <span style="color: var(--warning); font-weight: 700; font-size: 11px; display: inline-flex; flex-direction: column; align-items: flex-end; gap: 2px;">
                                    <span style="background: var(--warning-light); padding: 2px 8px; border-radius: 6px; border: 1px solid var(--warning); display: flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-hourglass-split"></i> Pending Pharmacy
                                    </span>
                                    <span style="font-size: 9px; opacity: 0.7; font-style: italic;">Dispensing Status: ${rx.status}</span>
                                </span>
                            </div>
                        `;
                    }

                    item.innerHTML = info + action;
                    list.appendChild(item);
                });
            })
            .catch(err => {
                console.error('Failed to load prescriptions:', err);
                list.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--danger);">Failed to load medications.</div>';
            });
    }

    function administerMedSingle(btn, rxId, url) {
        if (!confirm('Mark this medication as administered?')) return;
        
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat core1-spin"></i>';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (res.ok) {
                btn.parentElement.innerHTML = '<span style="color: var(--success); font-weight: 700; font-size: 12px; display: flex; align-items: center; gap: 4px;"><i class="bi bi-check-circle-fill"></i> Administered</span>';
                administrationNeedsReload = true;
            } else {
                alert('Administration failed. Please try again.');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        })
        .catch(err => {
            console.error('Administration error:', err);
            alert('An error occurred.');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        });
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        if (id === 'medicalRecordModal') {
            document.body.style.overflow = '';
        }
        if (id === 'administrationModal' && administrationNeedsReload) {
            window.location.reload();
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

        // ── Inpatient Medication Search (Autocomplete) ──────────────────────────────
        const searchInput = document.getElementById('medicationSearchInpatient');
        const resultsContainer = document.getElementById('drugSearchResultsInpatient');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                clearTimeout(debounceTimer);

                if (query.length < 2) {
                    resultsContainer.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    resultsContainer.innerHTML = '<div style="padding: 10px; color: var(--text-gray); font-style: italic;"><i class="bi bi-arrow-repeat core1-spin"></i> Searching inventory...</div>';
                    resultsContainer.style.display = 'block';

                    fetch(`/api/pharmacy-sync/search-drugs?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(drug => {
                                    const div = document.createElement('div');
                                    div.style.padding = '10px 14px';
                                    div.style.cursor = 'pointer';
                                    div.style.borderBottom = '1px solid var(--border-color)';
                                    div.className = 'hover:bg-slate-50';
                                    div.innerHTML = `
                                        <div class="font-bold text-sm" style="color: var(--text-dark);">${drug.drug_name}</div>
                                        <div style="font-size: 11px; color: var(--text-gray); margin-top: 2px;">
                                            <span style="background: var(--info-light); color: var(--info); padding: 1px 6px; border-radius: 4px; font-weight: 700; margin-right: 6px;">
                                                Stock: ${drug.quantity}
                                            </span>
                                            <span style="opacity: 0.7;">Code: ${drug.drug_num}</span>
                                        </div>
                                    `;
                                    div.onclick = () => {
                                        searchInput.value = drug.drug_name;
                                        resultsContainer.style.display = 'none';
                                    };
                                    resultsContainer.appendChild(div);
                                });
                                resultsContainer.style.display = 'block';
                            } else {
                                resultsContainer.innerHTML = '<div style="padding: 12px; color: var(--text-gray); font-style: italic; text-align: center;">No matching medicine found.</div>';
                            }
                        })
                        .catch(err => {
                            console.error('Drug search failed:', err);
                            resultsContainer.innerHTML = '<div style="padding: 12px; color: var(--danger); font-size: 11px;">Search failed. Try again.</div>';
                        });
                }, 400);
            });

            // Close results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
