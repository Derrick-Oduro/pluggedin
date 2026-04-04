@section('page-title', 'Audit Logs')

<x-superadmin-layout>
    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-bold">Audit Logs</h1>
            <p class="text-sm text-gray-600 dark:text-text-secondary mt-1">Track super-admin changes across users, categories, campaigns, and homepage content.</p>
        </div>

        <x-table-shell>
                <table class="backend-table">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-dark">
                        <tr>
                            <th class="backend-th">When</th>
                            <th class="backend-th">Action</th>
                            <th class="backend-th">Actor</th>
                            <th class="backend-th">Entity</th>
                            <th class="backend-th">Meta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="backend-row">
                                <td class="backend-td text-xs text-gray-600 dark:text-text-secondary">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td class="backend-td font-medium">{{ str_replace('.', ' • ', $log->action) }}</td>
                                <td class="backend-td">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="backend-td text-xs text-gray-600 dark:text-text-secondary">{{ class_basename($log->entity_type ?? 'N/A') }} #{{ $log->entity_id ?? 'N/A' }}</td>
                                <td class="backend-td">
                                    @if($log->meta)
                                        <pre class="text-xs whitespace-pre-wrap text-gray-600 dark:text-text-secondary">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-text-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-600 dark:text-text-secondary">No audit entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </x-table-shell>

        <div>{{ $logs->links() }}</div>
    </div>
</x-superadmin-layout>
