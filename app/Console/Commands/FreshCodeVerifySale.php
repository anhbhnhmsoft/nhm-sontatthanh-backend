<?php

namespace App\Console\Commands;

use App\Service\ConfigService;
use Illuminate\Console\Command;

class FreshCodeVerifySale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fresh-code-verify-sale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh code verify sale';

    /**
     * Execute the console command.
     */
    public function handle(ConfigService $configService)
    {
        $configService->freshSaleCode();
    }
}
