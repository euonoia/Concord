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

        <h4 class="mb-4 border-bottom pb-2 text-primary-base">Medical Residency & General Practice</h4>
        <div class="row g-4 mb-5">
            @php
                $tracks = [
                    ['title' => 'General Physician', 'vacancies' => '15 Vacancies', 'desc' => 'Comprehensive primary care training focusing on diagnostics and preventive medicine.'],
                    ['title' => 'Pediatrics', 'vacancies' => '8 Vacancies', 'desc' => 'Specialized training in neonatal care and adolescent medicine.'],
                    ['title' => 'Psychology', 'vacancies' => '5 Vacancies', 'desc' => 'Advanced clinical psychology tracks with focus on behavioral health and therapy.'],
                    ['title' => 'Neurology', 'vacancies' => '3 Vacancies', 'desc' => 'Deep dive into neuro-diagnostics and complex brain disorder management.'],
                    ['title' => 'Pathology', 'vacancies' => '4 Vacancies', 'desc' => 'Laboratory-based residency focusing on cellular analysis and forensic pathology.'],
                    ['title' => 'Radiology', 'vacancies' => '6 Vacancies', 'desc' => 'Training in MRI, CT imaging, and interventional radiology techniques.'],
                ];
            @endphp

            @foreach($tracks as $track)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4 hover-shadow">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-dark">{{ $track['title'] }}</h5>
                        <span class="badge bg-light text-primary border">{{ $track['vacancies'] }}</span>
                    </div>
                    <p class="text-muted small mb-4">{{ $track['desc'] }}</p>
                    <a href="#" class="mt-auto text-decoration-none fw-bold small">Apply for Track <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>

        <h4 class="mb-4 border-bottom pb-2 text-primary-base">Specialized Surgery & Fellowships</h4>
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm p-4 hover-shadow border-start border-primary border-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Cardiology & Cardiac Surgery</h5>
                            <span class="text-danger small fw-bold"><i class="bi bi-fire"></i> High Demand</span>
                        </div>
                        <span class="badge bg-primary">Fellowship</span>
                    </div>
                    <p class="text-muted small">Specialized surgical training in invasive cardiology and open-heart procedures.</p>
                    <a href="#" class="mt-auto text-decoration-none fw-bold">View Fellowship Requirements <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm p-4 hover-shadow border-start border-primary border-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Orthopedics & Orthopedic Surgery</h5>
                            <span class="text-primary small fw-bold">4 Seats Remaining</span>
                        </div>
                        <span class="badge bg-primary">Fellowship</span>
                    </div>
                    <p class="text-muted small">Comprehensive training in musculoskeletal trauma and joint replacement surgery.</p>
                    <a href="#" class="mt-auto text-decoration-none fw-bold">View Fellowship Requirements <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <h4 class="mb-4 border-bottom pb-2 text-primary-base">Nursing Specialization Tracks</h4>
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm p-4 bg-accent-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold">Specialized Nursing Program</h5>
                            <p class="text-muted small mb-0">We are recruiting nurses for **ICU, Pediatric, and Surgical** specializations. Join our multidisciplinary teams.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="#" class="btn btn-primary px-4">Apply as Specialist Nurse</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection