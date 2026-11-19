@section('page-title', 'Audit Logs')

<x-superadmin-layout>
    <div class="space-y-5">
        <x-page-header badge="Super admin" title="Audit logs" subtitle="Changes made by super admins to users, categories, campaigns and homepage content." />

        <x-table-shell>
                <table class="backend-table">
                    <thead class="border-b border-stone-200 bg-stone-50">
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
                                <td class="backend-td text-xs text-stone-600">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td class="backend-td font-medium">{{ str_replace('.', ' • ', $log->action) }}</td>
                                <td class="backend-td">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="backend-td text-xs text-stone-600">{{ class_basename($log->entity_type ?? 'N/A') }} #{{ $log->entity_id ?? 'N/A' }}</td>
                                <td class="backend-td">
                                    @if($log->meta)
                                        <pre class="text-xs whitespace-pre-wrap text-stone-600">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <span class="text-xs text-stone-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-stone-600">No audit entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </x-table-shell>

        <div>{{ $logs->links() }}</div>
    </div>
</x-superadmin-layout>
