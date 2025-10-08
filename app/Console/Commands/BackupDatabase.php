<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--keep-days=30 : Number of days to keep backups}';
    protected $description = 'Backup MySQL database and cleanup old backups';

    public function handle()
    {
        $this->info('🔄 Starting database backup...');
        
        // Konfigurasi database dari .env
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        
        // Validasi konfigurasi
        if (empty($dbName)) {
            $this->error('❌ Database name not configured in .env file!');
            return 1;
        }
        
        // Buat folder backup jika belum ada
        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            $this->info("📁 Created backup directory: {$backupPath}");
        }
        
        // Nama file backup dengan tanggal
        $fileName = 'backup_' . Carbon::now()->format('Y-m-d_His') . '.sql';
        $filePath = $backupPath . '/' . $fileName;
        
        // Command mysqldump
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s > %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            !empty($dbPass) ? '--password=' . escapeshellarg($dbPass) : '',
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );
        
        // Jalankan backup
        $this->info("💾 Backing up database: {$dbName}");
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            $this->error('❌ Backup failed! Make sure mysqldump is installed and accessible.');
            return 1;
        }
        
        // Cek ukuran file
        $fileSize = File::size($filePath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        $this->info("✅ Backup completed successfully!");
        $this->info("📦 File: {$fileName}");
        $this->info("📊 Size: {$fileSizeMB} MB");
        $this->info("📍 Location: {$filePath}");
        
        // Cleanup old backups
        $keepDays = (int) $this->option('keep-days');
        $this->cleanupOldBackups($backupPath, $keepDays);
        
        return 0;
    }
    
    private function cleanupOldBackups($backupPath, $keepDays)
    {
        $this->info("\n🗑️  Cleaning up old backups (keeping last {$keepDays} days)...");
        
        $files = File::files($backupPath);
        $deleted = 0;
        $cutoffDate = Carbon::now()->subDays($keepDays);
        
        foreach ($files as $file) {
            $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
            
            if ($fileDate->lt($cutoffDate)) {
                File::delete($file);
                $deleted++;
                $this->line("   🗑️  Deleted: " . $file->getFilename() . " (created: {$fileDate->format('Y-m-d')})");
            }
        }
        
        if ($deleted > 0) {
            $this->info("✅ Deleted {$deleted} old backup(s)");
        } else {
            $this->info("✅ No old backups to delete");
        }
        
        // Tampilkan jumlah backup yang tersisa
        $remaining = count(File::files($backupPath));
        $this->info("📦 Total backups available: {$remaining}");
    }
}
