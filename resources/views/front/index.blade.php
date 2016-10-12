@extends('layouts.creative')

@section('content')
    <h1 id="homeHeading">Bayar Cicilan Di Tempat</h1>
    <hr>
    <h3>Daftarkan Diri Anda : Tentukan Tanggal Dan Lokasi,
    Nikmati Layanan JemputCicilan.com</h3>
    <h4> Registrasi</h4>

    <a href="{{url('member/register')}}" class="btn btn-primary btn-xl ">Debitur</a>
    <a href="{{url('creditor/register')}}" class="btn btn-primary btn-xl page-scroll">Kreditur</a>
@endsection