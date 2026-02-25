<div class="row mt-4">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-header bg-info text-white text-center fw-bold">
                Activity Execution History
            </div>

            @php
                $project_id = request('project_id');
                $activities = \App\Models\Pro_StudyActivities::where('project_id', $project_id)
                    ->whereNotNull('actual_activity_date')
                    ->with(['executedBy'])
                    ->orderBy('actual_activity_date', 'desc')
                    ->get();
            @endphp

            <div class="card-body">
                @if ($activities->count() > 0)
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-info">
                            <tr>
                                <th>Activity</th>
                                <th>Execution Date</th>
                                <th>Executed By</th>
                                <th>Status</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>
                                        <strong>{{ $activity->study_activity_name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ \Carbon\Carbon::parse($activity->actual_activity_date)->format('Y-m-d') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $activity->executedBy ? $activity->executedBy->prenom . ' ' . $activity->executedBy->nom : 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($activity->status === 'completed')
                                            <span class="badge bg-success">✓ Completed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($activity->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($activity->commentaire)
                                            <small class="text-muted">{{ Str::limit($activity->commentaire, 50) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info text-center mb-0">
                        <i class="bi bi-info-circle"></i> No execution history yet for this project
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
