<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class fixpaymentcolumnsCommand extends Command
{
    protected $signature = 'fixpaymentcolumns';

    protected $description = 'Command description';

    public function handle(): void
    {
        \DB::statement('UPDATE paiements SET paiements.decaisser = paiements.credit ,paiements.encaisser = paiements.debit WHERE paiements.encaisser IS NULL AND paiements.decaisser IS null;');
        \DB::statement('ALTER TABLE paiements drop column debit;');
        \DB::statement('ALTER TABLE paiements drop column credit;');
    }
}
