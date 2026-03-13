@extends('layouts.residency_fellowship.app')

@section('content')
<header class="hero-gradient">
    <div class="container text-center">
        <span class="badge bg-info mb-3 text-uppercase px-3 py-2">Now Hiring: 2026-2027 Cohort</span>
        <h1 class="display-3 fw-bold mb-4 text-white">Shape the Future of <br>Modern Medicine</h1>
        <p class="lead mb-5 opacity-75 mx-auto text-white" style="max-width: 700px;">
            We don't just train doctors and nurses; we cultivate clinical leaders. Join a legacy of excellence with access to the nation's most advanced medical technology.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#open-positions" class="btn btn-light btn-recruit btn-lg shadow">View Open Tracks</a>
            <a href="#benefits" class="btn btn-outline-light btn-recruit btn-lg">Why Choose Us?</a>
        </div>
    </div>
</header>

<div class="container py-5">
    <div class="row g-4 py-5" id="benefits">
        <div class="col-md-4">
            <div class="benefit-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <h4>Rapid Career Growth</h4>
            <p class="text-muted small">95% of our residents secure senior or specialist positions within 6 months of graduation.</p>
        </div>
        <div class="col-md-4">
            <div class="benefit-icon"><i class="bi bi-heart-pulse"></i></div>
            <h4>Work-Life Balance</h4>
            <p class="text-muted small">Structured shifts and wellness stipends to ensure you perform at your absolute best.</p>
        </div>
        <div class="col-md-4">
            <div class="benefit-icon"><i class="bi bi-cash-stack"></i></div>
            <h4>Elite Compensation</h4>
            <p class="text-muted small">Highly competitive salary packages including housing allowances and research grants.</p>
        </div>
    </div>

    <section class="my-5" id="open-positions">
        <h2 class="text-center fw-bold mb-2">Open Specialty Tracks</h2>
        <p class="text-center text-muted mb-5">Select a department to view specific residency and fellowship requirements.</p>

        @php
            $residencyPostings  = $postings->where('track_type', 'residency')->values();
            $fellowshipPostings = $postings->where('track_type', 'fellowship')->values();
            $nursingPostings    = $postings->where('track_type', 'nursing')->values();
        @endphp

        {{-- ===== RESIDENCY ===== --}}
        @if($residencyPostings->count())
        <h4 class="mb-4 border-bottom pb-2 text-primary-base">Medical Residency &amp; General Practice</h4>
        <div class="row g-4 mb-5">
            @foreach($residencyPostings as $p)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4 hover-shadow">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-dark">{{ $p->title }}</h5>
                        <span class="badge bg-light text-primary border">{{ $p->needed_applicants }} Vacancies</span>
                    </div>
                    <p class="text-muted small mb-4">{{ $p->description }}</p>
                    <a href="{{ route('careers.apply', ['dept' => $p->dept_code]) }}"
                       class="mt-auto text-decoration-none fw-bold small">
                        Apply for Track <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ===== FELLOWSHIP ===== --}}
        @if($fellowshipPostings->count())
        <h4 class="mb-4 border-bottom pb-2 text-primary-base">Specialized Surgery &amp; Fellowships</h4>
        <div class="row g-4 mb-5">
            @foreach($fellowshipPostings as $p)
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm p-4 hover-shadow border-start border-primary border-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $p->title }}</h5>
                            @if($p->needed_applicants > 0)
                                <span class="text-primary small fw-bold">{{ $p->needed_applicants }} Seats Remaining</span>
                            @else
                                <span class="text-danger small fw-bold"><i class="bi bi-fire"></i> High Demand</span>
                            @endif
                        </div>
                        <span class="badge bg-primary">Fellowship</span>
                    </div>
                    <p class="text-muted small">{{ $p->description }}</p>
                    <a href="{{ route('careers.apply', ['dept' => $p->dept_code]) }}"
                       class="mt-auto text-decoration-none fw-bold">
                        Apply for Fellowship <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ===== NURSING ===== --}}
        @if($nursingPostings->count())
        <h4 class="mb-4 border-bottom pb-2 text-primary-base">Nursing Specialization Tracks</h4>
        <div class="row g-4">
            @foreach($nursingPostings as $p)
            <div class="col-md-12">
                <div class="card border-0 shadow-sm p-4 bg-accent-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold">{{ $p->title }}</h5>
                            <p class="text-muted small mb-0">{{ $p->description }}</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('careers.apply', ['dept' => $p->dept_code]) }}"
                               class="btn btn-primary px-4">Apply as Specialist Nurse</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Empty state --}}
        @if($postings->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-megaphone fs-1 d-block mb-3"></i>
            <p>No open positions at this time. Please check back later.</p>
        </div>
        @endif

    </section>

</div>
@endsection