@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Fleet Management</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addVehicleModal">
            + Add New Vehicle
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Plate Number</th>
                        <th>Type</th>
                        <th>Model</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fleet as $vehicle)
                    <tr>
                        <td><strong>{{ $vehicle->plate_number }}</strong></td>
                        <td>{{ $vehicle->vehicle_type }}</td>
                        <td>{{ $vehicle->model_name ?? 'N/A' }}</td>
                        <td>
                            @if($vehicle->status == 'available')
                                <span class="badge badge-success">Available</span>
                            @elseif($vehicle->status == 'in_use')
                                <span class="badge badge-warning">In Use (Delivery)</span>
                            @else
                                <span class="badge badge-danger">Maintenance</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.logistics2.fleet.update_status', $vehicle->id) }}" method="POST">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="form-control form-control-sm">
                                    <option value="available" {{ $vehicle->status == 'available' ? 'selected' : '' }}>Set Available</option>
                                    <option value="maintenance" {{ $vehicle->status == 'maintenance' ? 'selected' : '' }}>Set Maintenance</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.logistics2.fleet.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Register Vehicle</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Plate Number</label>
                        <input type="text" name="plate_number" class="form-control" required placeholder="e.g. ABC-123">
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="vehicle_type" class="form-control">
                            <option value="Standard Truck">Standard Truck</option>
                            <option value="Ambulance">Ambulance</option>
                            <option value="Refrigerated Truck">Refrigerated Truck</option>
                            <option value="Van">Van</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Model Name</label>
                        <input type="text" name="model_name" class="form-control" placeholder="e.g. Toyota Hiace">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Vehicle</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection