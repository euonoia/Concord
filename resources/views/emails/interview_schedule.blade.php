<h2>Interview Invitation</h2>

<p>Dear {{ $applicant->first_name }} {{ $applicant->last_name }},</p>

<p>
We are pleased to inform you that your interview has been scheduled.
</p>

<p><strong>Date:</strong> {{ $schedule->schedule_date }}</p>
<p><strong>Time:</strong> {{ $schedule->schedule_time }}</p>
<p><strong>Location:</strong> {{ $schedule->location }}</p>

@if($schedule->notes)
<p><strong>Notes:</strong> {{ $schedule->notes }}</p>
@endif

<p>
Please arrive at least 10 minutes before your scheduled time.
</p>

<p>Thank you,<br>HR Department</p>