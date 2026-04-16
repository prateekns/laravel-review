@extends('layouts.admin.app')

@section('title', __('Business Details'))

@section('content')
    <livewire:admin.business.show :business-id="$id" />
@endsection
