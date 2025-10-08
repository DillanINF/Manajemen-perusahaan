<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class RestoreDatabase extends Command
{
    protected $signature = 'backup:restore {--file= : Specific backup file to restore} {--list : List available backups}';
    protected $description = 'Restore MySQL database from backup';

    public function handle()
    {
        // Jika opsi --list, tampilkan daftar backup
        if ($this->option('list')) {
            return $this->listBackups();
        }
        
        $backupPath = storage_path('app/backups');
        
        // Cek apakah folder backup ada
        if (!File::exists($backupPath)) {
            $this->error('❌ No backup directory found!');
            $this->info('💡 Run "php artisan backup:database" first to create backups.');
            return 1;
        }
        
        // Ambil semua file backup
        $backupFiles = collect(File::files($backupPath))
            ->filter(fn($file) => $file->getExtension() === 'sql')
            ->sortByDesc(fn($file) => File::lastModified($file))
            ->values();
        
        if ($backupFiles->isEmpty()) {
            $this->error('❌ No backup files found!');
            $this->info('💡 Run "php artisan backup:database" first to create backups.');
            return 1;
        }
        
        // Jika ada opsi --file, gunakan file tersebut
        $selectedFile = null;
        if ($this->option('file')) {
            $fileName = $this->option('file');
            $selectedFile = $backupFiles->first(fn($file) => $file->getFilename() === $fileName);
            
            if (!$selectedFile) {
                $this->error("❌ Backup file not found: {$fileName}");
                return 1;
            }
        } else {
            // Tampilkan menu pilihan
            $this->info('📦 Available Backups:');
            $this->newLine();
            
            $choices = [];
            foreach ($backupFiles as $index => $file) {
                $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
                $fileSize = round(File::size($file) / 1024 / 1024, 2);
                $daysAgo = $fileDate->diffInDays(Carbon::now());
                $timeAgo = $daysAgo === 0 ? 'today' : ($daysAgo === 1 ? 'yesterday' : "{$daysAgo} days ago");
                
                $choices[] = sprintf(
                    '%s (%s) - %s MB',
                    $file->getFilename(),
                    $timeAgo,
                    $fileSize
                );
            }
            
            $selectedIndex = $this->choice(
                'Select backup to restore',
                $choices,
                0
            );
            
            $selectedFile = $backupFiles->get(array_search($selectedIndex, $choices));
        }
        
        // Konfirmasi restore
        $this->newLine();
        $this->warn('⚠️  WARNING: This will replace your current database!');
        $this->info('   Current database will be backed up first for safety.');
        $this->newLine();
        
        if (!$this->confirm('Do you want to continue?', false)) {
            $this->info('❌ Restore cancelled.');
            return 0;
        }
        
        // Backup database saat ini sebelum restore
        $this->info("\n💾 Backing up current database first...");
        $this->call('backup:database');
        
        // Restore database
        $this->info("\n🔄 Restoring database from: " . $selectedFile->getFilename());
        
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s %s %s < %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            !empty($dbPass) ? '--password=' . escapeshellarg($dbPass) : '',
            escapeshellarg($dbName),
            escapeshellarg($selectedFile->getPathname())
        );
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            $this->error('❌ Restore failed! Please check your MySQL configuration.');
            return 1;
        }
        
        $this->newLine();
        $this->info('✅ Database restored successfully!');
        $this->info('📦 Restored from: ' . $selectedFile->getFilename());
        $this->newLine();
        $this->info('💡 Please test your application to ensure everything works correctly.');
        
        return 0;
    }
    
    private function listBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            $this->error('❌ No backup directory found!');
            return 1;
        }
        
        $backupFiles = collect(File::files($backupPath))
            ->filter(fn($file) => $file->getExtension() === 'sql')
            ->sortByDesc(fn($file) => File::lastModified($file));
        
        if ($backupFiles->isEmpty()) {
            $this->info('📦 No backups available.');
            return 0;
        }
        
        $this->info('📦 Available Backups:');
        $this->newLine();
        
        $headers = ['File Name', 'Created', 'Size', 'Age'];
        $rows = [];
        
        foreach ($backupFiles as $file) {
            $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
            $fileSize = round(File::size($file) / 1024 / 1024, 2) . ' MB';
            $daysAgo = $fileDate->diffInDays(Carbon::now());
            $age = $daysAgo === 0 ? 'today' : ($daysAgo === 1 ? 'yesterday' : "{$daysAgo} days ago");
            
            $rows[] = [
                $file->getFilename(),
                $fileDate->format('Y-m-d H:i:s'),
                $fileSize,
                $age
            ];
        }
        
        $this->table($headers, $rows);
        $this->newLine();
        $this->info('Total: ' . count($rows) . ' backup(s)');
        
        return 0;
    }
}
