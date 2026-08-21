<x-admin-layout>
    @push('styles')
    <style>
        .staff-page {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .service-category-title {
            color: #00A3B1;
            font-weight: 700;
            border-bottom: 2px solid rgba(0, 163, 177, 0.2);
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 20px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .current-photo-container {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 15px;
            border: 2px solid #00A3B1;
        }

        .current-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    @endpush

    @section('content')
    <div class="staff-page">
        <div class="container">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Edit Staff Member</h1>
                <div class="actions">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Staff
                    </a>
                </div>
            </div>

            <!-- Staff Form -->
            <div class="form-card">
                <form action="{{ route('admin.staff.update', $specialist->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="name" class="form-label">Staff Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $specialist->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="mobile_no" class="form-label">Personal Mobile No</label>
                            <input type="text" class="form-control @error('mobile_no') is-invalid @enderror"
                                id="mobile_no" name="mobile_no" value="{{ old('mobile_no', $specialist->mobile_no) }}">
                            @error('mobile_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="email" class="form-label">Email ID</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $specialist->email) }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="password" class="form-label">Login Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Leave blank to keep same">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" class="form-control @error('religion') is-invalid @enderror"
                                id="religion" name="religion" value="{{ old('religion', $specialist->religion) }}">
                            @error('religion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block">Job Category (Select Multiple)</label>
                            <div id="job_category_container" class="border rounded p-2" style="max-height: 120px; overflow-y: auto; background-color: #fff;">
                                @foreach($jobCategories as $cat)
                                    @php
                                        $checked = false;
                                        if (is_array(old('job_category', $specialist->job_category)) && in_array($cat, old('job_category', $specialist->job_category))) {
                                            $checked = true;
                                        }
                                    @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="job_category[]" value="{{ $cat }}" id="cat_{{ $loop->index }}" {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_{{ $loop->index }}">
                                            {{ $cat }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="input-group mt-2">
                                <input type="text" id="new_category_input" class="form-control form-control-sm" placeholder="New category...">
                                <button class="btn btn-outline-primary btn-sm" type="button" id="add_category_btn">Add</button>
                            </div>
                            @error('job_category')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="home_address" class="form-label">Home Address</label>
                            <textarea class="form-control @error('home_address') is-invalid @enderror"
                                id="home_address" name="home_address" rows="5" style="height: calc(120px + 31px);">{{ old('home_address', $specialist->home_address) }}</textarea>
                            @error('home_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-center justify-content-center">
                            <div class="form-check form-switch p-3 w-100 text-center" style="height: calc(120px + 31px); display: flex !important; flex-direction: column; justify-content: center; align-items: center;">
                                <input class="form-check-input ms-0 mb-2" type="checkbox" id="status" name="status" value="1"
                                    {{ old('status', $specialist->status) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold d-block" for="status">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>



                    <hr class="my-4">

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('add_category_btn');
            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    const input = document.getElementById('new_category_input');
                    const value = input.value.trim();
                    if (value) {
                        const container = document.getElementById('job_category_container');
                        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                        let exists = false;
                        checkboxes.forEach(function(cb) {
                            if (cb.value.toLowerCase() === value.toLowerCase()) {
                                cb.checked = true;
                                exists = true;
                            }
                        });

                        if (!exists) {
                            const index = checkboxes.length;
                            const div = document.createElement('div');
                            div.className = 'form-check';
                            div.innerHTML = `
                                <input class="form-check-input" type="checkbox" name="job_category[]" value="${value}" id="cat_new_${index}" checked>
                                <label class="form-check-label" for="cat_new_${index}">
                                    ${value}
                                </label>
                            `;
                            container.appendChild(div);
                        }
                        input.value = '';
                    }
                });
            }
        });
    </script>
    @endpush
    @endsection
</x-admin-layout>
