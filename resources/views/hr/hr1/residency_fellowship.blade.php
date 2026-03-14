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

    {{-- Social Recognition Section --}}
    <section class="mt-5 pt-5 border-top" id="social-recognition">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Community Appreciation</h2>
            <p class="text-muted">Celebrating our team's milestones and professional achievements.</p>
        </div>

        @php
            $recognitionPosts = \App\Models\RecognitionPost::with(['admin', 'comments.user'])->orderBy('created_at', 'desc')->get();
        @endphp

        <div class="row g-4 d-flex justify-content-center">
            @forelse($recognitionPosts as $post)
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm overflow-hidden mb-4 recognition-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-person-badge fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $post->title }}</h6>
                                <small class="text-muted">Posted by {{ $post->admin->username ?? 'Hospital Admin' }} &bull; {{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <p class="mb-4">{{ $post->content }}</p>

                        @if($post->image_path)
                        <div class="mb-4 rounded-3 overflow-hidden shadow-sm">
                            <img src="{{ asset('storage/' . $post->image_path) }}" class="img-fluid w-100" alt="Recognition Image">
                        </div>
                        @endif

                        <div class="d-flex align-items-center gap-4 pt-3 border-top">
                            <button class="btn btn-sm btn-interaction like-btn {{ $post->likes->contains('user_id', Auth::id()) ? 'active' : '' }}" 
                                    data-url="{{ route('recognition.like', $post->id) }}" data-id="{{ $post->id }}">
                                <i class="bi {{ $post->likes->contains('user_id', Auth::id()) ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                <span class="likes-count">{{ $post->likes_count }}</span>
                            </button>
                            <button class="btn btn-sm btn-interaction" type="button" data-bs-toggle="collapse" data-bs-target="#comments-{{ $post->id }}">
                                <i class="bi bi-chat-dots me-1"></i>
                                <span class="comments-count">{{ $post->comments_count }}</span>
                            </button>
                        </div>

                        {{-- Comments Section --}}
                        <div class="collapse mt-3" id="comments-{{ $post->id }}">
                            <div class="p-3 bg-light rounded-3">
                                <div class="comments-list-{{ $post->id }} mb-3">
                                    @foreach($post->comments as $comment)
                                    <div class="d-flex gap-2 mb-2 p-2 bg-white rounded shadow-sm">
                                        <div class="fw-bold small">{{ $comment->user->username ?? 'User' }}:</div>
                                        <div class="small flex-grow-1">{{ $comment->comment_text }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                @if(Auth::check())
                                <form class="comment-form" data-url="{{ route('recognition.comment', $post->id) }}" data-id="{{ $post->id }}">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="comment_text" class="form-control" placeholder="Write a comment..." required>
                                        <button class="btn btn-primary" type="submit">Post</button>
                                    </div>
                                </form>
                                @else
                                <p class="small text-muted mb-0">Please <a href="/login">log in</a> to comment.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">
                <i class="bi bi-stars fs-1 d-block mb-2"></i>
                <p>No community appreciations yet. Check back soon!</p>
            </div>
            @endforelse
        </div>
    </section>
</div>

<style>
    .recognition-card { transition: transform 0.3s ease; }
    .recognition-card:hover { transform: translateY(-3px); }
    .btn-interaction { color: #6c757d; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 20px; transition: all 0.2s; }
    .btn-interaction:hover { background: #f8f9fa; color: #0d6efd; }
    .like-btn.active { color: #dc3545; }
    .like-btn.active:hover { color: #bb2d3b; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Like functionality
    $('.like-btn').click(function() {
        const btn = $(this);
        const url = btn.data('url');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                btn.find('.likes-count').text(response.likes_count);
                if (response.status === 'liked') {
                    btn.addClass('active').find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                } else {
                    btn.removeClass('active').find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                }
            },
            error: function() {
                alert('Please login to like posts.');
            }
        });
    });

    // Comment functionality
    $('.comment-form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.data('url');
        const id = form.data('id');
        const list = $('.comments-list-' + id);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                const comment = response.comment;
                const html = `
                    <div class="d-flex gap-2 mb-2 p-2 bg-white rounded shadow-sm">
                        <div class="fw-bold small">${comment.username}:</div>
                        <div class="small flex-grow-1">${comment.text}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">${comment.date}</div>
                    </div>
                `;
                list.prepend(html);
                form.find('input').val('');
                $('[data-bs-target="#comments-' + id + '"] .comments-count').text(response.comments_count);
            },
            error: function() {
                alert('An error occurred while posting your comment.');
            }
        });
    });
});
</script>
@endsection