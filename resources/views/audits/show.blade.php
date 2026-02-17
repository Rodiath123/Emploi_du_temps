@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold">{{ __('Change History') }}</h1>
            <p class="text-gray-600 mt-2">
                {{ class_basename($modelType) }} #{{ $modelId }}
            </p>
        </div>
        <a href="{{ route('audits.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            {{ __('Back to Audit Log') }}
        </a>
    </div>

    <!-- Timeline -->
    <div class="space-y-4">
        @forelse($audits as $audit)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 @if($audit->action === 'created') border-green-500 @elseif($audit->action === 'updated') border-yellow-500 @else border-red-500 @endif">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center gap-3">
                            @if($audit->action === 'created')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ __('Created') }}
                                </span>
                            @elseif($audit->action === 'updated')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    {{ __('Updated') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    {{ __('Deleted') }}
                                </span>
                            @endif

                            @if($audit->field_name)
                                <span class="text-gray-600">
                                    <strong>{{ $audit->field_name }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">
                            {{ $audit->created_at->format('Y-m-d H:i:s') }}
                        </p>
                        @if($audit->user)
                            <p class="text-sm font-medium text-gray-900">
                                {{ $audit->user->name }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">
                                {{ __('System') }}
                            </p>
                        @endif
                    </div>
                </div>

                @if($audit->field_name)
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-2">{{ __('Old Value') }}</p>
                            <div class="bg-red-50 border border-red-200 rounded p-3">
                                <code class="text-sm text-red-900 break-words">
                                    {{ $audit->old_value ?? __('(empty)') }}
                                </code>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-2">{{ __('New Value') }}</p>
                            <div class="bg-green-50 border border-green-200 rounded p-3">
                                <code class="text-sm text-green-900 break-words">
                                    {{ $audit->new_value ?? __('(empty)') }}
                                </code>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between text-xs text-gray-500">
                    @if($audit->ip_address)
                        <span>{{ __('IP') }}: {{ $audit->ip_address }}</span>
                    @endif
                    @if($audit->user_agent)
                        <span class="text-right max-w-xl truncate" title="{{ $audit->user_agent }}">
                            {{ __('Agent') }}: {{ Str::limit($audit->user_agent, 40) }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                {{ __('No changes found for this item.') }}
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($audits->hasPages())
        <div class="mt-8">
            {{ $audits->links() }}
        </div>
    @endif
</div>
@endsection
