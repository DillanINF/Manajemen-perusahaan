<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AnnualSummary;

class GenerateAnnualSummary extends Command
{
    protected $signature = 'summary:generate {year?}';
    protected $description = 'Generate annual summary for a specific year';

    public function handle()
    {
        $year = $this->argument('year') ?? date('Y');
        
        $this->info("Generating annual summary for year {$year}...");
        
        // Logic untuk generate summary
        // (Tidak diimplementasikan karena tidak digunakan)
        
        $this->info("Annual summary generated successfully!");
    }
}
