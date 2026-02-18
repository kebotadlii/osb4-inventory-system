<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemTransaction;

class RecalculateStock extends Command
{
    protected $signature = 'stock:recalculate';
    protected $description = 'Recalculate item stock from transactions';

    public function handle()
    {
        $this->info('Recalculating stock...');

        Item::query()->update(['stock' => 0]);

        ItemTransaction::orderBy('tanggal')->chunk(100, function ($transactions) {
            foreach ($transactions as $trx) {
                if (! $trx->item) continue;

                if ($trx->type === 'in') {
                    $trx->item->increment('stock', $trx->quantity);
                }

                if ($trx->type === 'out') {
                    $trx->item->decrement('stock', $trx->quantity);
                }
            }
        });

        $this->info('Stock recalculated successfully.');
        return Command::SUCCESS;
    }
}
