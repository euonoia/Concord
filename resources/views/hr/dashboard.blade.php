@extends('layouts.app')
@section('title', 'My Health Portal')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-cyan-500">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Welcome back, {{ Auth::user()->username }}</h1>
                    <p class="text-slate-500">Medical Record Number: <span class="font-mono font-bold text-slate-700">{{ Auth::user()->uuid }}</span></p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded-full text-xs font-bold uppercase">HR ACCOUNT</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 bg-slate-50 rounded-lg border border-slate-200 hover:border-cyan-300 transition cursor-pointer">
                    <h3 class="font-bold text-lg text-slate-800">My Appointments</h3>
                    <p class="text-sm text-slate-600 mb-4">View or schedule your next visit.</p>
                    <span class="text-cyan-600 font-semibold text-sm">View Calendar →</span>
                </div>

                <div class="p-6 bg-slate-50 rounded-lg border border-slate-200 hover:border-cyan-300 transition cursor-pointer">
                    <h3 class="font-bold text-lg text-slate-800">Lab Results</h3>
                    <p class="text-sm text-slate-600 mb-4">Check your latest blood tests and imaging.</p>
                    <span class="text-cyan-600 font-semibold text-sm">Download Reports →</span>
                </div>

                <div class="p-6 bg-slate-50 rounded-lg border border-slate-200 hover:border-cyan-300 transition cursor-pointer">
                    <h3 class="font-bold text-lg text-slate-800">Billing & Payments</h3>
                    <p class="text-sm text-slate-600 mb-4">Pay outstanding hospital invoices.</p>
                    <span class="text-cyan-600 font-semibold text-sm">Pay Now →</span>
                </div>

                <div class="p-6 bg-slate-50 rounded-lg border border-slate-200 hover:border-cyan-300 transition cursor-pointer">
                    <h3 class="font-bold text-lg text-slate-800">Prescriptions</h3>
                    <p class="text-sm text-slate-600 mb-4">Request refills for current medications.</p>
                    <span class="text-cyan-600 font-semibold text-sm">Refill Request →</span>
                </div>
            </div>
        </div>
    </div>
@endsection