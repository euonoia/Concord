@extends('layouts.dashboard.app')

@section('title', 'Exam / Quiz')

@section('content')
<div class="container" style="padding: 20px; max-width: 900px;">
    <h2 style="margin-bottom: 20px;">Exam: {{ $moduleCode ?? 'Module' }}</h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if($questions && $questions->count() > 0)
        <form action="{{ route('user.learning.exam.submit', $moduleCode) }}" method="POST">
            @csrf
            @foreach($questions as $index => $q)
                <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                    <p><strong>Q{{ $index + 1 }}:</strong> {{ $q->question_text }}</p>
                    
                    @foreach($q->options as $option)
                        <div style="margin-bottom: 5px;">
                            <label>
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $option->id }}" required>
                                {{ $option->option_text }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">
                Submit Exam
            </button>
        </form>
    @else
        <p style="color: #999;">No exam questions are available for this module yet.</p>
    @endif
</div>
@endsection