@extends('admin.hr4.layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:          #eef3f7;
        --c-surface:     #ffffff;
        --c-border:      #d4e3ee;
        --c-teal:        #0a7c6e;
        --c-teal-light:  #e4f4f1;
        --c-teal-mid:    #b8e0da;
        --c-green:       #1a7a52;
        --c-green-light: #e4f5ed;
        --c-green-mid:   #b2ddc8;
        --c-indigo:      #3548c7;
        --c-indigo-light:#eceffe;
        --c-indigo-mid:  #c0c9f5;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
        --shadow-md:     0 4px 20px rgba(10,50,80,.10);
        --shadow-hover:  0 10px 36px rgba(10,124,110,.16);
    }

    .hr4-wrap * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .hr4-wrap {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* Header */
    .hr4-header {
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
        animation: fadeDown .45s ease both;
    }

    .hr4-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2.1rem;
        color: var(--c-text);
        margin: 0 0 .3rem;
        line-height: 1.1;
    }

    .hr4-header h1 em {
        color: var(--c-teal);
        font-style: italic;
    }

    .hr4-header p {
        color: var(--c-muted);
        font-size: .95rem;
        margin: 0;
    }

    /* Module Cards */
    .hr4-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .hr4-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 16px;
        padding: 1.6rem;
        box-shadow: var(--shadow-sm);
        transition: transform .3s cubic-bezier(.22,.68,0,1.2),
                    box-shadow .3s ease,
                    border-color .3s ease;
        animation: fadeUp .5s ease both;
        position: relative;
        overflow: hidden;
    }

    .hr4-card:hover {
        transform: translateY(-6px) scale(1.015);
        box-shadow: var(--shadow-hover);
    }

    .hr4-card:nth-child(1) { animation-delay: .1s; }
    .hr4-card:nth-child(2) { animation-delay: .2s; }
    .hr4-card:nth-child(3) { animation-delay: .3s; }

    .hr4-card:nth-child(1):hover { border-color: var(--c-teal-mid); }
    .hr4-card:nth-child(2):hover { border-color: var(--c-green-mid); }
    .hr4-card:nth-child(3):hover { border-color: var(--c-indigo-mid); }

    .card-top {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .card-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        transition: transform .3s cubic-bezier(.22,.68,0,1.2);
    }

    .hr4-card:hover .card-icon {
        transform: scale(1.15) rotate(-5deg);
    }

    .icon-teal   { background: var(--c-teal-light);   color: var(--c-teal); }
    .icon-green  { background: var(--c-green-light);  color: var(--c-green); }
    .icon-indigo { background: var(--c-indigo-light); color: var(--c-indigo); }

    .card-meta h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--c-text);
        margin: 0 0 .2rem;
    }

    .card-meta p {
        font-size: .82rem;
        color: var(--c-muted);
        margin: 0;
    }

    .card-divider {
        height: 1px;
        background: var(--c-line);
        margin-bottom: 1rem;
    }

    .card-link {
        font-size: .83rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        text-decoration: none;
        transition: gap .2s ease, opacity .2s ease;
    }

    .card-link:hover { gap: .6rem; opacity: .85; text-decoration: none; }

    .link-teal   { color: var(--c-teal); }
    .link-green  { color: var(--c-green); }
    .link-indigo { color: var(--c-indigo); }

    /* Quick Actions */
    .qa-box {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 16px;
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
        animation: fadeUp .5s .4s ease both;
    }

    .qa-box h2 {
        font-family: 'Instrument Serif', serif;
        font-size: 1.35rem;
        color: var(--c-text);
        margin: 0 0 1.25rem;
    }

    .qa-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1rem;
    }

    .qa-item {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1rem 1.2rem;
        border-radius: 12px;
        border: 1px solid var(--c-line);
        background: var(--c-bg);
        text-decoration: none;
        transition: background .22s ease,
                    border-color .22s ease,
                    transform .25s cubic-bezier(.22,.68,0,1.2),
                    box-shadow .22s ease;
    }

    .qa-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(10,124,110,.10);
        text-decoration: none;
    }

    .qa-item.qa-teal:hover  { background: var(--c-teal-light);  border-color: var(--c-teal-mid); }
    .qa-item.qa-green:hover { background: var(--c-green-light); border-color: var(--c-green-mid); }

    .qa-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        transition: transform .25s cubic-bezier(.22,.68,0,1.2);
    }

    .qa-item:hover .qa-icon { transform: scale(1.12); }

    .qa-item.qa-teal  .qa-icon { background: var(--c-teal-light);  color: var(--c-teal);  border: 1px solid var(--c-teal-mid); }
    .qa-item.qa-green .qa-icon { background: var(--c-green-light); color: var(--c-green); border: 1px solid var(--c-green-mid); }

    .qa-text h3 {
        font-size: .9rem;
        font-weight: 600;
        color: var(--c-text);
        margin: 0 0 .15rem;
    }

    .qa-text p {
        font-size: .78rem;
        color: var(--c-muted);
        margin: 0;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="hr4-wrap">

    <div class="hr4-header">
        <h1>Welcome to <em>HR4 Dashboard</em></h1>
        <p>Hello, <strong>{{ Auth::user()->username }}</strong>! Here's your HR4 summary overview.</p>
    </div>

    <div class="hr4-grid">
        <div class="hr4-card">
            <div class="card-top">
                <div class="card-icon icon-teal">
                    <i class="bi bi-briefcase"></i>
                </div>
                <div class="card-meta">
                    <h3>Available Jobs</h3>
                    <p>Manage job vacancies</p>
                </div>
            </div>
            <div class="card-divider"></div>
            <a href="{{ route('hr4.job_postings.index') }}" class="card-link link-teal">
                View Jobs <span>→</span>
            </a>
        </div>

        <div class="hr4-card">
            <div class="card-top">
                <div class="card-icon icon-green">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="card-meta">
                    <h3>Direct Compensation</h3>
                    <p>Employee salary management</p>
                </div>
            </div>
            <div class="card-divider"></div>
            <a href="{{ route('hr4.direct_compensation.index') }}" class="card-link link-green">
                Manage Compensation <span>→</span>
            </a>
        </div>

        <div class="hr4-card">
            <div class="card-top">
                <div class="card-icon icon-indigo">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="card-meta">
                    <h3>Core Human Capital</h3>
                    <p>Human resources core functions</p>
                </div>
            </div>
            <div class="card-divider"></div>
            <a href="{{ route('hr4.core') }}" class="card-link link-indigo">
                Access Core <span>→</span>
            </a>
        </div>
    </div>

    <div class="qa-box">
        <h2>Quick Actions</h2>
        <div class="qa-grid">
            <a href="{{ route('hr4.job_postings.create') }}" class="qa-item qa-teal">
                <div class="qa-icon"><i class="bi bi-plus-circle"></i></div>
                <div class="qa-text">
                    <h3>Add New Available Job</h3>
                    <p>Create job vacancies for HR1</p>
                </div>
            </a>
            <a href="{{ route('hr4.direct_compensation.index') }}" class="qa-item qa-green">
                <div class="qa-icon"><i class="bi bi-calculator"></i></div>
                <div class="qa-text">
                    <h3>Generate Compensation</h3>
                    <p>Calculate employee salaries</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection
