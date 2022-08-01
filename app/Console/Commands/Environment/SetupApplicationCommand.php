<?php

namespace App\Console\Commands\Environment;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SetupApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line('Installing CRM...');
        $this->call('down');
        $this->call('key:generate');
        $this->call('shield:install');
        $this->call('crm:environment:app');
        $this->call('crm:environment:mail');
        $this->call('crm:environment:database');
        $this->call('migrate');
        $this->call('optimize');
        $this->call('up');
        $this->info('CRM is installed ⚡');
    }
}