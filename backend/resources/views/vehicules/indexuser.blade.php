<!DOCTYPE html>
<html>
<head>
    <title>Vehicules Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #e6f2e6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar { position: fixed; top: 0; left: 0; height: 100%; width: 220px; background-color: #1b2a2f; padding-top: 20px; color: white; }
        .sidebar h4 { margin-bottom: 20px; }
        .sidebar a { display: flex; align-items: center; color: white; padding: 10px 20px; text-decoration: none; margin-bottom: 2px; border-radius: 4px; gap: 10px; }
        .sidebar a:hover { background-color: #162125; }

        .content { margin-left: 240px; padding: 20px; }
        .dashboard-header { color: green; padding: 20px; border-radius: 5px; margin-bottom: 20px; }

        .btn-trash { color: white; background-color: #e63946; border: none; padding: 5px 10px; border-radius: 3px; }
        .btn-trash:hover { background-color: #d62828; }
    </style>
</head>
<body>

<div class="content">
    <div class="dashboard-header">
        <h1>Ma Liste de Véhicules</h1>
    </div>

    <a href="/vehicules/create" class="btn btn-success mb-3">Ajouter +</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Availability Start</th>
                    <th>Availability End</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicules as $vehicule)
                    <tr data-id="{{ $vehicule->id }}" 
                        data-description="{{ $vehicule->description }}"
                        data-start="{{ $vehicule->availability_start }}"
                        data-end="{{ $vehicule->availability_end }}">
                        <td>{{ $vehicule->type }}</td>
                        <td>{{ $vehicule->capacity }}</td>
                        <td>{{ $vehicule->availability_start }}</td>
                        <td>{{ $vehicule->availability_end }}</td>
                        <td>{{ $vehicule->location }}</td>
                        <td>{{ $vehicule->status }}</td>
                        <td>
                            <button class="btn btn-warning btn-edit" data-id="{{ $vehicule->id }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                        <td>
                            <form action="/vehicules/{{ $vehicule->id }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-trash">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit form -->
    <div id="editFormContainer" style="display:none; margin-top:20px;">
        <div class="card p-3 bg-light">
            <h5>Edit Vehicule</h5>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Type</label>
                        <input type="text" name="type" class="form-control" id="editType" required>
                    </div>
                    <div class="col-md-6">
                        <label>Capacity</label>
                        <input type="number" name="capacity" class="form-control" id="editCapacity" required>
                    </div>
                    <div class="col-md-6">
                        <label>Availability Start</label>
                        <input type="date" name="availability_start" class="form-control" id="editStart" required>
                    </div>
                    <div class="col-md-6">
                        <label>Availability End</label>
                        <input type="date" name="availability_end" class="form-control" id="editEnd" required>
                    </div>
                    <div class="col-12">
                        <label>Description</label>
                        <textarea name="description" class="form-control" id="editDescription"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" id="editLocation" required>
                    </div>
                    <div class="col-md-6">
                        <label>Status</label>
                        <input type="text" name="status" class="form-control" id="editStatus" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const id = this.dataset.id;

        document.getElementById('editType').value = row.children[0].textContent;
        document.getElementById('editCapacity').value = row.children[1].textContent;
        document.getElementById('editStart').value = row.dataset.start;
        document.getElementById('editEnd').value = row.dataset.end;
        document.getElementById('editDescription').value = row.dataset.description;
        document.getElementById('editLocation').value = row.children[4].textContent;
        document.getElementById('editStatus').value = row.children[5].textContent;

        const form = document.getElementById('editForm');
        form.action = `/vehicules/${id}`;
        document.getElementById('editFormContainer').style.display = 'block';
        window.scrollTo({ top: form.offsetTop, behavior: 'smooth' });
    });
});

document.getElementById('cancelEdit').addEventListener('click', function() {
    document.getElementById('editFormContainer').style.display = 'none';
});
</script>

</body>
</html>
