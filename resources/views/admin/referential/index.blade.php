<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Académique</title>
    <!-- CSS : Bootstrap & Google Fonts & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f2f5; color: #1a202c; }
        .card { border: none; border-radius: 15px; transition: transform 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .card:hover { transform: translateY(-5px); }
        .btn-action { border-radius: 8px; font-weight: 600; }
        .table-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .header-section { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; padding: 40px 0; border-radius: 0 0 30px 30px; margin-bottom: 40px; }
        .form-control { border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px; }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container text-center">
            <h1 class="fw-bold"><i class="fas fa-university me-2"></i> Configuration du Référentiel</h1>
            <p class="opacity-75">Gérez les structures fondamentales de votre établissement en un clic.</p>
        </div>
    </div>

    <div class="container">
        
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Formulaires d'ajout -->
        <div class="row g-4 mb-5">
            <!-- Salles -->
            <div class="col-lg-4">
                <div class="card p-3 h-100">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-door-open fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Nouvelle Salle</h5>
                    <form action="{{ route('referential.rooms.store') }}" method="POST">
                        @csrf
                        <input type="text" name="name" class="form-control mb-3" placeholder="Nom (ex: Amphi A)" required>
                        <input type="number" name="capacity" class="form-control mb-3" placeholder="Capacité d'accueil" required>
                        <button class="btn btn-primary w-100 btn-action shadow-sm">Enregistrer</button>
                    </form>
                </div>
            </div>

            <!-- Matières -->
            <div class="col-lg-4">
                <div class="card p-3 h-100">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-book fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Nouveau Cours</h5>
                    <form action="{{ route('referential.subjects.store') }}" method="POST">
                        @csrf
                        <input type="text" name="name" class="form-control mb-3" placeholder="Libellé (ex: Algèbre)" required>
                        <input type="text" name="code" class="form-control mb-3" placeholder="Code UE (ex: MTH101)" required>
                        <input type="hidden" name="credits" value="1">
                        <button class="btn btn-success w-100 btn-action shadow-sm">Enregistrer</button>
                    </form>
                </div>
            </div>

            <!-- Classes -->
            <div class="col-lg-4">
                <div class="card p-3 h-100">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-users-class fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Nouveau Groupe</h5>
                    <form action="{{ route('referential.classes.store') }}" method="POST">
                        @csrf
                        <input type="text" name="name" class="form-control mb-3" placeholder="Nom (ex: L3 Informatique)" required>
                        <input type="text" name="code" class="form-control mb-3" placeholder="Code Promo" required>
                        <input type="hidden" name="level" value="Cycle">
                        <button class="btn btn-warning w-100 btn-action shadow-sm text-dark">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableaux de données -->
        <div class="row g-4">
            <!-- Liste Salles -->
            <div class="col-md-4">
                <div class="table-container shadow-sm border-top border-primary border-4">
                    <h6 class="fw-bold mb-4 text-primary text-uppercase"><i class="fas fa-list me-2"></i>Salles</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                @foreach($rooms as $room)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $room->name }}</div>
                                        <small class="text-muted">{{ $room->capacity }} places</small>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('referential.rooms.destroy', $room->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-link text-danger p-0"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Liste Matières -->
            <div class="col-md-4">
                <div class="table-container shadow-sm border-top border-success border-4">
                    <h6 class="fw-bold mb-4 text-success text-uppercase"><i class="fas fa-bookmark me-2"></i>Matières</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                @foreach($subjects as $subject)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $subject->name }}</div>
                                        <small class="badge bg-light text-success border">{{ $subject->code }}</small>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('referential.subjects.destroy', $subject->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-link text-danger p-0"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Liste Classes -->
            <div class="col-md-4">
                <div class="table-container shadow-sm border-top border-warning border-4">
                    <h6 class="fw-bold mb-4 text-warning text-uppercase"><i class="fas fa-users me-2"></i>Classes</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                @foreach($classes as $class)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $class->name }}</div>
                                        <small class="text-muted">Promotion : {{ $class->code }}</small>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('referential.classes.destroy', $class->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-link text-danger p-0"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
