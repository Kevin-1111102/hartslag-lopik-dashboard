<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Overzicht meldingen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">

                {{-- Header row --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h2 fw-bold mb-0">Overzicht meldingen</h1>
                        <p class="text-muted mb-0">Nieuwste meldingen staan bovenaan</p>
                    </div>

                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-bell me-1"></i> {{ $notifications->count() }} totaal
                        </span>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
                    </div>
                @endif

                @if ($notifications->isEmpty())
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-heart-pulse display-1"></i>
                        <p class="mt-3">Geen meldingen gevonden.</p>
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
<th class="w-[110px]">status</th>
<th class="w-[130px]">Type</th>
<th class="w-[120px]">AED</th>
                                            <th>Bericht</th>
<th class="w-[120px]">Datum</th>
<th class="w-[220px]">Actie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notifications as $notification)
                                            @php
                                                $isUnread = !$notification->gelezen;
                                            @endphp
                                            <tr class="{{ $isUnread ? 'bg-warning bg-opacity-10' : '' }}">
                                                <td>
                                                    @if ($isUnread)
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-star-fill me-1"></i> Nieuw
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            Gelezen
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="{{ $isUnread ? 'fw-bold' : '' }}">
                                                    {{ ucfirst($notification->type) }}
                                                </td>

                                                <td class="{{ $isUnread ? 'fw-bold' : '' }}">
                                                    AED-{{ str_pad($notification->aed_id, 3, '0', STR_PAD_LEFT) }}
                                                </td>

                                                <td>
                                                    <div class="{{ $isUnread ? 'fw-semibold' : '' }}">
                                                        {{ $notification->bericht }}
                                                    </div>
                                                </td>

                                                <td>
                                                    {{ $notification->datum?->format('Y-m-d') }}
                                                </td>

                                                <td class="text-end">
                                                    @if ($isUnread)
                                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="bi bi-check2-circle me-1"></i>
                                                                Markeer als gelezen
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.notifications.unread', $notification) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                <i class="bi bi-check-circle me-1"></i>
                                                                markeer als ongelezen
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
