<div>
    <div class="pagetitle">
        <div class="row">
            <div class="col">
                <h1>Students Results</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('lecturer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Students Results</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="row g-3 mt-2 justify-content-between">
                {{-- <div class="col-md-6">
                    <label for="course_id" class="form-label">Course</label>
                    <select id="course_id" wire:model="course_id" class="form-select">
                        <option selected>Select Course</option>
                        @foreach ($courses as $key => $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div> --}}

                <div class="col-md-6">
                    <label for="module_id" class="form-label">Module</label>
                    <select id="module_id" wire:model.live="module_id" class="form-select">
                        <option value="0" selected>Select Module</option>
                        @foreach ($modules as $key => $module)
                            <option value="{{ $module->module_id }}">{{ $module->module->code }}:
                                {{ $module->module->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('module_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3">
                    <button type="button" wire:click="refreshPage"
                        class="btn btn-primary rounded-3 btn-sm fw-bold font-12 mt-2" style="float: right;"
                        title="Refresh"><i class="bi bi-bootstrap-reboot"></i> Refresh</button>
                </div>
                <div class="col-md-3">
                    <span for="keywords" class="badge bg-success py-2 fw-bold font-14 mt-2" style="float: right;">
                        Students Enrolling: <span class="mx-2">{{ $student_enrolled_count }}</span></span>
                </div>
                {{-- <div class="col-md-{{ $show_assigned_modules == true ? '12' : '3' }}"
                    style="display: {{ $show_assigned_modules == true ? ' ' : 'none' }};">
                    <button type="button" class="btn btn-primary mt-4" wire:click="getModules"
                        style="float: right;">Assign modules</button>
                </div>
                <div class="col-md-3" style="display: {{ $show_assigned_modules == false ? '' : 'none' }};">
                    <button type="button" class="btn btn-success mt-4" wire:click="getAssignedModules"
                        style="float: right;">Assigned modules</button>
                </div> --}}
                <div class="col-md-12 mt-4">
                    <!-- Primary Color Bordered Table -->
                    <table class="table table-bordered border-primary">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Student name</th>
                                <th scope="col">Registration number</th>
                                <th scope="col">CA marks</th>
                                <th scope="col">FE marks</th>
                                <th scope="col">Toatal marks</th>
                                <th scope="col">Grade</th>
                                <th scope="col" class="text-center">Blockchain</th>
                                <th scope="col" class="text-center">Save results</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @forelse ($students_enrolled as $data)
                                <tr>
                                    <th scope="row">{{ $i++ }}</th>
                                    <td>{{ $data->student->first_name }} {{ $data->student->middle_name }}
                                        {{ $data->student->last_name }}</td>
                                    <td>{{ $data->student->registration_no }}</td>
                                    <td>
                                        <center>
                                            <input type="number" min="0" step="0.1"
                                                wire:model.live="stu_c_a_marks.{{ $data->student->registration_no }}"
                                                id="" class="form-control form-control-sm text-center"
                                                style="width: 80px; height: 30px;">
                                        </center>
                                    </td>
                                    <td>
                                        <center>
                                            <input type="number" min="0" step="0.1"
                                                wire:model.live="stu_f_e_marks.{{ $data->student->registration_no }}"
                                                id="" class="form-control form-control-sm text-center"
                                                style="width: 80px; height: 30px;">
                                        </center>
                                    </td>
                                    <td>
                                        <center>
                                            <input type="text" min="0" step="0.1"
                                                wire:model.live="total_marks.{{ $data->student->registration_no }}"
                                                id="" class="text-center"
                                                style="width: 80px; height: 30px; border: none;"
                                                value="{{ $total_marks[$data->student->registration_no] ?? 0 }}"
                                                readonly>
                                        </center>
                                        <span hidden>
                                            {{ $this->calculateTotalMarks($data->student->registration_no) ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <center>
                                            <input type="number" min="0" step="0.1"
                                                wire:model="grade_ids.{{ $data->student->registration_no }}"
                                                id="" class="form-control form-control-sm text-center"
                                                style="width: 80px; height: 30px; border: none;" hidden>
                                            <input type="text"
                                                wire:model="grade_names.{{ $data->student->registration_no }}"
                                                id="" class="text-center"
                                                style="width: 80px; height: 30px; border: none;">
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $regNo = $data->student->registration_no;
                                            $resultId = $result_ids[$regNo] ?? null;
                                            $status = $verification_status[$resultId] ?? null;
                                        @endphp
                                        @if (empty($anchored_hashes[$regNo] ?? null))
                                            <span class="badge bg-secondary">Not yet anchored</span>
                                        @else
                                            <span class="badge bg-success d-block mb-1">Anchored</span>
                                            <small class="d-block text-muted" title="{{ $anchor_tx_hashes[$regNo] }}">
                                                tx: {{ Str::limit($anchor_tx_hashes[$regNo], 14, '…') }}
                                            </small>
                                            <button type="button" wire:click="verifyOnBlockchain({{ $resultId }})"
                                                class="btn btn-outline-primary btn-sm mt-1" title="Verify against blockchain">
                                                Verify
                                            </button>
                                            @if ($status === 'match')
                                                <span class="badge bg-success d-block mt-1">✔ Matches chain</span>
                                            @elseif ($status === 'mismatch')
                                                <span class="badge bg-danger d-block mt-1">⚠ Tampered</span>
                                            @elseif ($status === 'error')
                                                <span class="badge bg-warning d-block mt-1">Verify failed</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            wire:click="saveStudentResults({{ $data->student->registration_no }})"
                                            class="btn btn-primary" title="Save Results"><i
                                                class="bi bi-save-fill"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <td colspan="9" class="text-center">No Student Enrolled found.</td>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- End Primary Color Bordered Table -->
                </div>
            </form>
        </div>
    </div>
</div>
