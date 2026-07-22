@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">@lang('System Update')</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-1">@lang('Current commit'):
                            <strong>{{ $hash ?? 'unknown' }}</strong>
                            @if ($subject)
                                — {{ $subject }}
                            @endif
                        </p>
                        @if ($date)
                            <p class="text-muted small mb-0">{{ $date }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-outline--primary" id="btnCheck">@lang('Check for
                            Updates')</button>
                        <button type="button" class="btn btn--primary" id="btnUpdate">@lang('Update from
                            GitHub')</button>
                    </div>
                </div>
                <div id="checkResult" class="mt-3"></div>
            </div>
            <div class="card-footer">
                <form action="{{ route('admin.system.update.clear.cache') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline--primary btn-sm">@lang('Clear Cache Only')</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">@lang('Output')</h5>
            </div>
            <div class="card-body">
                <div id="deployState" class="mb-2"></div>
                <pre id="deployLog"
                    style="max-height:420px; overflow:auto; background:#111; color:#0f0; padding:15px; border-radius:4px; white-space:pre-wrap;">{{ $status['log'] ?? 'No update has been run yet.' }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            const token = '{{ csrf_token() }}';
            const urls = {
                check: '{{ route('admin.system.update.check') }}',
                run: '{{ route('admin.system.update.run') }}',
                status: '{{ route('admin.system.update.status') }}',
            };
            const stateMeta = {
                idle: ['secondary', '@lang('Idle')'],
                running: ['info', '@lang('Running…')'],
                success: ['success', '@lang('Last update succeeded')'],
                failed: ['danger', '@lang('Last update failed')'],
            };

            function renderState(state) {
                const meta = stateMeta[state] || stateMeta.idle;
                $('#deployState').html('<span class="badge bg-' + meta[0] + '">' + meta[1] + '</span>');
            }

            $('#btnCheck').on('click', function() {
                const btn = $(this).prop('disabled', true);
                $.get(urls.check, function(res) {
                    if (!res.ok) {
                        $('#checkResult').html('<div class="alert alert-danger">' + res.message + '</div>');
                    } else if (res.behind > 0) {
                        $('#checkResult').html(
                            '<div class="alert alert-warning">' + res.behind +
                            ' commit(s) behind origin/main:<pre class="mb-0">' + res.commits + '</pre></div>'
                        );
                    } else {
                        $('#checkResult').html(
                            '<div class="alert alert-success">@lang('Up to date with origin/main')</div>');
                    }
                }).always(function() {
                    btn.prop('disabled', false);
                });
            });

            $('#btnUpdate').on('click', function() {
                if (!confirm(
                        '@lang('This will pull the latest code now and briefly put the site into maintenance mode. Continue?')'
                    )) {
                    return;
                }
                const btn = $(this).prop('disabled', true);
                renderState('running');
                $('#deployLog').text('@lang('Running… this may take a minute, please stay on this page.')');

                $.ajax({
                    type: 'POST',
                    url: urls.run,
                    data: {
                        _token: token
                    },
                    timeout: 300000,
                    success: function(res) {
                        renderState(res.ok ? 'success' : 'failed');
                        $('#deployLog').text(res.log || '');
                    },
                    error: function(xhr) {
                        renderState('failed');
                        const log = xhr.responseJSON && xhr.responseJSON.log;
                        $('#deployLog').text(log || '@lang('Request failed — check the server error log.')');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });

            renderState('{{ $status['state'] ?? 'idle' }}');
        })(jQuery);
    </script>
@endpush
