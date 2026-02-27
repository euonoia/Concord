<x-mail::message>
# Appointment Approved

Dear {{ $appointment->name }},

We are pleased to inform you that your appointment has been approved.

**Appointment Details:**
- **Reference No:** {{ $appointment->appointment_no }}
- **Date:** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
- **Service:** {{ ucfirst(str_replace('_', ' ', $appointment->service_type)) }}
@if($appointment->doctor_name)
- **Doctor:** {{ $appointment->doctor_name }}
@endif

Please arrive 15 minutes before your scheduled time.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
