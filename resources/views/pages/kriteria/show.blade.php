@extends('layouts/master')

@section('title', 'Kriteria ' . $id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kriteria {{ $id }}</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <!-- Content for Kriteria {{ $id }} -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 