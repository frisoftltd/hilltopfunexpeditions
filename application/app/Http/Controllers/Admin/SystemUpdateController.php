<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DeployRunner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SystemUpdateController extends Controller
{
    public function index()
    {
        $pageTitle = 'System Update';

        $head = $this->runGit(['log', '-1', '--format=%h|%s|%cI']);
        [$hash, $subject, $date] = $head['ok'] && trim($head['output']) !== ''
            ? array_pad(explode('|', trim($head['output']), 3), 3, null)
            : [null, null, null];

        $status = $this->readStatus();

        return view('admin.system_update.index', compact('pageTitle', 'hash', 'subject', 'date', 'status'));
    }

    public function check()
    {
        $fetch = $this->runGit(['fetch', 'origin', '--quiet']);
        if (!$fetch['ok']) {
            return response()->json([
                'ok' => false,
                'message' => 'git fetch failed. Check the deploy key / network on the server.',
                'output' => $fetch['output'],
            ]);
        }

        $count = $this->runGit(['rev-list', '--count', 'HEAD..origin/main']);
        $log = $this->runGit(['log', '--oneline', 'HEAD..origin/main', '-n', '20']);

        return response()->json([
            'ok' => true,
            'behind' => (int) trim($count['output'] ?? '0'),
            'commits' => trim($log['output'] ?? ''),
        ]);
    }

    public function update(Request $request, DeployRunner $runner)
    {
        set_time_limit(300);

        $result = $runner->run();

        $this->writeStatus([
            'state' => $result['ok'] ? 'success' : 'failed',
            'finished_at' => now()->toIso8601String(),
            'log' => $result['log'],
        ]);

        return response()->json(['ok' => $result['ok'], 'log' => $result['log']]);
    }

    public function status()
    {
        return response()->json($this->readStatus());
    }

    public function clearCache(Request $request)
    {
        Artisan::call('optimize:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        $notify[] = ['success', 'Cache cleared successfully'];
        return back()->withNotify($notify);
    }

    private function repoRoot(): string
    {
        return dirname(base_path());
    }

    private function deployDir(): string
    {
        $dir = storage_path('app/deploy');
        File::ensureDirectoryExists($dir);
        return $dir;
    }

    private function statusFile(): string
    {
        return $this->deployDir() . '/status.json';
    }

    private function readStatus(): array
    {
        $file = $this->statusFile();
        if (!File::exists($file)) {
            return ['state' => 'idle'];
        }

        return json_decode(File::get($file), true) ?: ['state' => 'idle'];
    }

    private function writeStatus(array $data): void
    {
        File::put($this->statusFile(), json_encode($data, JSON_PRETTY_PRINT));
    }

    private function runGit(array $args): array
    {
        $process = new Process(array_merge(['git'], $args), $this->repoRoot());
        $process->setTimeout(30);

        try {
            $process->run();
            return [
                'ok' => $process->isSuccessful(),
                'output' => trim($process->getOutput() . $process->getErrorOutput()),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'output' => $e->getMessage()];
        }
    }
}
