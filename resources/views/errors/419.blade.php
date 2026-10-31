@extends('errors.layout')

@section('code', '419')
@section('title', __('errors.419.title'))
@section('message', __('errors.419.message'))

@section('actions')
    <a href="{{ url()->previous() }}" class="btn-ghost px-8">{{ __('errors.419.action') }}</a>
@endsection
