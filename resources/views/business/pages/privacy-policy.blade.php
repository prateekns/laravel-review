{{-- resources/views/business/pages/privacy-policy.blade.php --}}

@extends('layouts.business.page')

@section('content')
    <div class="container mx-auto p-8">
        <div class="prose max-w-none">
            {!! $privacyPolicy !!}
        </div>
    </div>
@endsection

