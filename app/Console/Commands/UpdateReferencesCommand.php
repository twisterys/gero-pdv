<?php

namespace App\Console\Commands;

use Database\Seeders\demos\ReferencesSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UpdateReferencesCommand extends Command
{
    protected $signature = 'update:references';

    protected $description = 'update references format';

    public function handle(): void
    {
        $this->line('Truncate References table <fg=gray>........................................................</> <fg=yellow;options=bold>RUNNING</>');
        \DB::table('references')->truncate();
        $this->line('Truncate References table <fg=gray>...........................................................</> <fg=green;options=bold>DONE</>');
        $this->newLine(1);
        $this->line('Database\Seeders\ReferencesSeeder <fg=gray>................................................</> <fg=yellow;options=bold>RUNNING</>');
        Artisan::call('db:seed',['class' => 'ReferencesSeeder']);
        $this->line('Database\Seeders\ReferencesSeeder <fg=gray>...................................................</> <fg=green;options=bold>DONE</>');
        $this->newLine(1);
        $this->line('Database\Seeders\ReferenceDepenseSeeder <fg=gray>...........................................</> <fg=yellow;options=bold>RUNNING</>');
        Artisan::call('db:seed',['class' => 'ReferenceDepenseSeeder']);
        $this->line('Database\Seeders\ReferenceDepenseSeeder <fg=gray>..............................................</> <fg=green;options=bold>DONE</>');
    }
}
