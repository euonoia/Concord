<x-mail::message>
# Appointment Approved

Dear {{ $appointment->patient->name }},

We are pleased to inform you that your appointment has been approved.

**Appointment Details:**
- **Reference No:** {{ $appointment->appointment_id }}
- **Date:** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
- **Service:** {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
@if($appointment->doctor && $appointment->doctor->employee)
- **Doctor:** {{ $appointment->doctor->employee->full_name }}
@endif

Please arrive 15 minutes before your scheduled time.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
