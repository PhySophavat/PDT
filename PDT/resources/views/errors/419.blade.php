@extends('layouts.guest')

@section('title', 'ទំព័របានផុតកំណត់ (419 Page Expired) | portal sala')

@section('content')
<div class="bg-white/95 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-7 sm:p-9 shadow-xl shadow-slate-200/60 text-center space-y-6">
    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-sm">
        <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
    </div>

    <div class="space-y-2">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-bold">
            <span>កំហុសសុវត្ថិភាពលេខ 419</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            សម័យការបានផុតកំណត់
        </h1>
        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
            ទំព័រនេះត្រូវបានទុកចោលយូរពេក ឬ Cookie ត្រូវបានបិទនៅក្នុងកម្មវិធីរុករក (Browser)។ សូមចុចប៊ូតុងខាងក្រោម ដើម្បីផ្ទុកទំព័រឡើងវិញ។
        </p>
    </div>

    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
        <button 
            type="button" 
            onclick="window.location.reload()" 
            class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2"
        >
            <i class="fa-solid fa-rotate-right"></i>
            <span>ផ្ទុកទំព័រឡើងវិញ (Reload)</span>
        </button>
        <a 
            href="{{ url('/') }}" 
            class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>ត្រឡប់ទៅទំព័រដើម</span>
        </a>
    </div>
</div>
@endsection

