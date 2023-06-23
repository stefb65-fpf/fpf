<?php

namespace App\Console\Commands;

use App\Models\Personne;
use Illuminate\Console\Command;

class SecurityCheckDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // supprime tous les secure_code
        $data = [
            'secure_code' => null
        ];
        Personne::where('secure_code', '!=', null)->update($data);
    }
}
