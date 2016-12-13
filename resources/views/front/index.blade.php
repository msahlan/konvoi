@extends('layouts.creative')

@section('content')
    <h1 id="homeHeading">Tentukan Touring-mu & Dapatkan Partner-mu</h1>
    <hr>
    <h3>Daftarkan Diri Anda : Tentukan Tanggal Dan Lokasi Touring, Nikmati Indahnya Kebersamaan Bersama Konvoi.com</h3>
    <h4> Registrasi</h4>

    <a href="{{url('member/register')}}" class="btn btn-primary btn-xl ">
    Triper</a>
    <a href="{{url('creditor/register')}}" class="btn btn-primary btn-xl page-scroll">Anggota</a>
@endsection