<x-mail::message>
# Appointment Update

Dear {{ $appointment->name }},

We regret to inform you that we are unable to confirm your appointment request at this time.

**Appointment Details:**
- **Reference No:** {{ $appointment->appointment_no }}
- **Date:** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}

**Reason:**
{{ $appointment->rejection_reason }}

Please feel free to book another slot or contact us for assistance.

<x-mail::button :url="route('landing.landingPage.index')">
Book Again
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
