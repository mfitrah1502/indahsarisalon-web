@extends('errors::layout')

@section('title', 'Halaman Tidak Ditemukan')

@section('icon')
<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
@endsection

@section('code', '404')
@section('message', 'Oops! Halaman Hilang')
@section('description', 'Maaf, halaman yang Anda cari tidak dapat kami temukan. Mungkin alamatnya salah atau halaman telah dipindahkan.')
