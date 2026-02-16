@extends('layouts.residency_fellowship.app')

@section('content')
<header class="hero-gradient">
    <div class="container text-center">
        <span class="badge bg-info mb-3 text-uppercase px-3 py-2">Now Hiring: 2026-2027 Cohort</span>
        <h1 class="display-3 fw-bold mb-4">Shape the Future of <br>Modern Medicine</h1>
        <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 700px;">
            We don't just train doctors; we cultivate clinical leaders. Join a legacy of excellence with access to the nation's most advanced medical technology.
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
            <p class="text-muted">95% of our fellows secure leadership positions within 6 months of graduation.</p>
        </div>
        <div class="col-md-4">
            <div class="benefit-icon"><i class="bi bi-heart-pulse"></i></div>
            <h4>Work-Life Balance</h4>
            <p class="text-muted">Structured shifts and wellness stipends to ensure you perform at your absolute best.</p>
        </div>
        <div class="col-md-4">
            <div class="benefit-icon"><i class="bi bi-cash-stack"></i></div>
            <h4>Elite Compensation</h4>
            <p class="text-muted">Highly competitive salary packages including housing allowances and research grants.</p>
        </div>
    </div>

    <section class="my-5" id="open-positions">
        <h2 class="text-center fw-bold mb-5">Open Specialty Tracks</h2>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm p-4 hover-shadow">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Internal Medicine Residency</h5>
                            <span class="text-primary small fw-bold">38 Vacancies Available</span>
                        </div>
                        <span class="badge bg-light text-dark border">Full-Time</span>
                    </div>
                    <p class="text-muted small">Rotation includes ICU, Cardiology, and Emergency medicine in our Level 1 Trauma Center.</p>
                    <a href="#" class="mt-auto text-decoration-none fw-bold">Learn more & Apply <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Cardiovascular Fellowship</h5>
                            <span class="text-danger small fw-bold">Last 2 Vacancies</span>
                        </div>
                        <span class="badge bg-light text-dark border">Fellowship</span>
                    </div>
                    <p class="text-muted small">Advanced training in interventional cardiology and structural heart disease research.</p>
                    <a href="#" class="mt-auto text-decoration-none fw-bold text-danger">Apply Before Deadline <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection