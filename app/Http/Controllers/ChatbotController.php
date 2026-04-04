<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function respond(Request $request)
    {
        $question = trim($request->query('question', ''));

        if ($question === '') {
            return response()->json([
                'answer' => 'Please ask me anything about appointments, doctors, or careers.',
            ]);
        }

        $answer = $this->generateAnswer(mb_strtolower($question));

        return response()->json(['answer' => $answer]);
    }

    protected function generateAnswer(string $text): string
    {
        if (preg_match('/\b(hi|hello|hey|good (morning|afternoon|evening))\b/', $text)) {
            return 'Hello! I\'m Concord Assistant. I can help you with appointments, doctors, and careers. Try asking: "How do I book an appointment?", "Tell me about doctors", or "What careers are available?"';
        }

        if (preg_match('/who (are you|r you)|what are you|your name/', $text)) {
            return 'I\'m Concord Assistant, your virtual helper for this site. I can answer questions about bookings, doctors, and our career openings.';
        }

        if (preg_match('/\b(appointment|book|schedule|reschedule|cancel|visit|availability)\b/', $text)) {
            return $this->appointmentAnswer();
        }

        if (preg_match('/\b(doctor|physician|specialist|surgeon|consultant|pediatric|pedia|cardio|neuro|ortho)\b/', $text)) {
            return $this->doctorAnswer();
        }

        if (preg_match('/\b(career|careers|job|jobs|internship|residency|apply|position|opening|opportunities|hiring)\b/', $text)) {
            return $this->careerAnswer();
        }

        return $this->defaultAnswer();
    }

    protected function appointmentAnswer(): string
    {
        $totalAppointments = DB::table('appointments')->count();
        $upcoming = DB::table('appointments')
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        $message = 'You can book an appointment using the form on this page. ';
        $message .= $totalAppointments > 0 ? "We currently have {$totalAppointments} appointments on record. " : '';

        if ($upcoming) {
            $message .= 'The next scheduled appointment is on ' . date('M j, Y', strtotime($upcoming->appointment_date)) . ' at ' . date('g:i A', strtotime($upcoming->appointment_time));
            if (!empty($upcoming->doctor_name)) {
                $message .= ' with Dr. ' . $upcoming->doctor_name . '.';
            } else {
                $message .= '.';
            }
        } else {
            $message .= 'No upcoming appointments are currently recorded in the system.';
        }

        $message .= ' If you want, ask me to show available doctors or career openings.';

        return $message;
    }

    protected function doctorAnswer(): string
    {
        $doctors = DB::table('appointments')
            ->whereNotNull('doctor_name')
            ->where('doctor_name', '<>', '')
            ->distinct()
            ->limit(5)
            ->pluck('doctor_name')
            ->toArray();

        if (empty($doctors)) {
            return 'We do not have doctor names in the appointment records yet, but our team includes specialists across internal medicine, surgery, pediatrics, and more. Try asking about careers to see our open positions.';
        }

        $list = implode(', ', $doctors);
        return 'Our current doctor list includes: ' . $list . '. You can ask about appointments to book with any of them or ask for career opportunities.';
    }

    protected function careerAnswer(): string
    {
        $jobs = DB::table('job_postings_hr1')
            ->where('is_active', 1)
            ->orderBy('track_type')
            ->limit(4)
            ->get(['title', 'track_type', 'description']);

        if ($jobs->isEmpty()) {
            return 'There are no active career openings in the job postings table right now, but please check back later or reach out through our contact options.';
        }

        $pairs = $jobs->map(function ($job) {
            $summary = trim($job->title);
            if (!empty($job->track_type)) {
                $summary .= ' (' . $job->track_type . ')';
            }
            if (!empty($job->description)) {
                $summary .= ' — ' . strip_tags(substr($job->description, 0, 120));
            }
            return $summary;
        });

        return 'Here are some active career openings: ' . $pairs->implode(' | ') . '. Ask me if you want help applying.';
    }

    protected function defaultAnswer(): string
    {
        return 'I can help with appointments, doctors, and careers. Try questions like "Hi", "Who are you?", "How do I book an appointment?", or "What career opportunities do you have?"';
    }
}
