<?php

namespace App\Console\Commands;

use App\Services\DeployRunner;
use Illuminate\Console\Command;

class DeployPull extends Command
{
    protected $signature = 'app:deploy';

    protected $description = 'Pull the latest code from GitHub and clear caches (the same routine the admin panel Update button runs)';

    public function handle(DeployRunner $runner): int
    {
        $result = $runner->run();
        $this->line($result['log']);

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
