<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DeployRunner
{
    private string $repoRoot;

    private string $deployDir;

    public function __construct()
    {
        $this->repoRoot = dirname(base_path());
        $this->deployDir = storage_path('app/deploy');
        File::ensureDirectoryExists($this->deployDir);
    }

    /**
     * Pull the latest code from GitHub and clear caches. Safe to call from
     * either a web request or the CLI — guarded by its own lock file so two
     * overlapping calls (e.g. a double-click) can't run at the same time.
     *
     * @return array{ok: bool, log: string}
     */
    public function run(): array
    {
        $lockFile = $this->deployDir . '/deploy.lock';

        if ($this->lockIsHeld($lockFile)) {
            return ['ok' => false, 'log' => 'An update is already running. Please wait for it to finish.'];
        }

        File::put($lockFile, getmypid() . '|' . now()->toIso8601String());

        $log = ['[' . now()->toIso8601String() . '] Update started'];

        try {
            // -uno excludes untracked files (e.g. runtime uploads under assets/)
            // so only changes to already-tracked files can block a deploy.
            $dirty = $this->runGit(['status', '--porcelain', '-uno']);
            $log[] = '$ git status --porcelain -uno';
            $log[] = $dirty['output'] !== '' ? $dirty['output'] : '(clean)';

            if ($dirty['output'] !== '') {
                $log[] = 'ABORTED: server has uncommitted changes to tracked files. Resolve manually, then retry.';
                return ['ok' => false, 'log' => implode("\n", $log)];
            }

            Artisan::call('down');
            $log[] = '$ php artisan down';

            $fetch = $this->runGit(['fetch', 'origin']);
            $log[] = '$ git fetch origin';
            $log[] = $fetch['output'] !== '' ? $fetch['output'] : '(no output)';

            $pull = $this->runGit(['pull', 'origin', 'main']);
            $log[] = '$ git pull origin main';
            $log[] = $pull['output'];

            if (!$pull['ok']) {
                $log[] = 'ABORTED: git pull failed.';
                return ['ok' => false, 'log' => implode("\n", $log)];
            }

            Artisan::call('migrate', ['--force' => true]);
            $log[] = '$ php artisan migrate --force';
            $log[] = trim(Artisan::output()) ?: 'Nothing to migrate.';

            Artisan::call('optimize:clear');
            $log[] = '$ php artisan optimize:clear';
            $log[] = trim(Artisan::output()) ?: 'Done.';

            Artisan::call('view:clear');
            $log[] = '$ php artisan view:clear';
            $log[] = trim(Artisan::output()) ?: 'Done.';

            Artisan::call('cache:clear');
            $log[] = '$ php artisan cache:clear';
            $log[] = trim(Artisan::output()) ?: 'Done.';

            $head = $this->runGit(['log', '-1', '--format=%h %s']);
            $log[] = 'Now at: ' . $head['output'];

            return ['ok' => true, 'log' => implode("\n", $log)];
        } catch (\Throwable $e) {
            $log[] = 'ERROR: ' . $e->getMessage();
            return ['ok' => false, 'log' => implode("\n", $log)];
        } finally {
            // Always bring the site back up, even if an exception was thrown above.
            Artisan::call('up');
            File::delete($lockFile);
        }
    }

    private function runGit(array $args): array
    {
        $process = new Process(array_merge(['git'], $args), $this->repoRoot);
        $process->setTimeout(240);
        $process->run();

        return [
            'ok' => $process->isSuccessful(),
            'output' => trim($process->getOutput() . $process->getErrorOutput()),
        ];
    }

    private function lockIsHeld(string $lockFile): bool
    {
        if (!File::exists($lockFile)) {
            return false;
        }

        [, $timestamp] = array_pad(explode('|', File::get($lockFile)), 2, null);

        // A lock older than 10 minutes means a previous run crashed without cleaning up.
        if ($timestamp && now()->diffInMinutes($timestamp) > 10) {
            File::delete($lockFile);
            return false;
        }

        return true;
    }
}
