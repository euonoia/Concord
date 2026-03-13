<h2>Interview Invitation</h2>

<p>Dear {{ $applicant->first_name }} {{ $applicant->last_name }},</p>

<p>We are pleased to inform you that your interview has been scheduled. Please reference your Application ID below.</p>

<p><strong>Application ID:</strong> {{ $applicant->application_id }}</p>
<p><strong>Date:</strong> {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('F j, Y') }}</p>
<p><strong>Time:</strong> {{ \Carbon\Carbon::parse($schedule->schedule_time)->format('g:i A') }}</p>
<p><strong>Location:</strong> {{ $schedule->location }}</p>

@if($schedule->notes)
<p><strong>Notes:</strong> {{ $schedule->notes }}</p>
@endif

<p>Please arrive at least 10 minutes before your scheduled time.</p>

<p>Thank you,<br>HR Department</p>