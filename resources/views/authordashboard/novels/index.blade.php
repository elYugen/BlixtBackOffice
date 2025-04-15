@extends('authordashboard.layouts.base')
@section('title', 'Mes romans')
@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .create-button {
        background-color: #0a549a;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }
    
    .create-button:hover {
        background-color: #083d71;
        color: white;
        transform: translateY(-1px);
    }

    .table img {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .table img:hover {
        transform: scale(1.05);
    }

    .prospect-table {
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    .prospect-table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <div class="page-header">
            <h1 class="mb-0">Mes romans</h1>
            <a href="{{ route('authordashboard.novels.create') }}" class="create-button text-decoration-none">
                <i class="bi bi-plus-circle"></i> Créer un roman
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover prospect-table">
                <thead class="table">
                    <tr>
                        <th>#</th>
                        <th style="width:10%">Couverture</th>
                        <th>Titre</th>
                        <th>Genres</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($novels as $novel)
                    <tr>
                        <td>{{ $novel->id }}</td>
                        <td><img src="{{ url($novel->cover_image) }}" alt="Couverture" style="width:50%"></td>
                        <td>{{ $novel->title }}</td>
                        <td>
                            @foreach($novel->genres as $genre)
                                <span class="badge bg-secondary me-1">{{ $genre->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'en_cours' => 'En cours',
                                    'termine' => 'Terminé',
                                    'en_pause' => 'En pause',
                                    'abandonne' => 'Abandonné'
                                ];
                            @endphp
                            {{ $statusMap[$novel->status] ?? $novel->status }}
                        </td>
                        <td>{{ $novel->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('authordashboard.novels.show', $novel->id) }}" class="btn btn-sm" style="background-color: #0a549a; color: white;">
                                    <i class="bi bi-eye"></i> Afficher
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('.prospect-table').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [1, 6] }
            ],
            pageLength: 10,
        });
    });
</script>
@endsection