<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JatuhTempo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function jatuhTempoNotifications()
    {
        $today = Carbon::today();
        
        // Get all pending items (not accepted) - filter out 'Lunas' status
        $unpaidItems = JatuhTempo::where('status_pembayaran', '!=', 'Lunas')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();
        
        $overdue = [];
        $todayDue = [];
        $upcoming = [];
        
        foreach ($unpaidItems as $item) {
            $dueDate = Carbon::parse($item->tanggal_jatuh_tempo);
            $daysLeft = $today->diffInDays($dueDate, false);
            
            $notificationItem = [
                'id' => $item->id,
                'no_invoice' => $item->no_invoice,
                'customer' => $item->customer,
                'tanggal_invoice' => $item->tanggal_invoice ? $item->tanggal_invoice->format('d/m/Y') : null,
                'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                'daysLeft' => $daysLeft,
                'jumlah_tagihan' => $item->jumlah_tagihan
            ];
            
            if ($daysLeft < 0) {
                // Overdue
                $overdue[] = $notificationItem;
            } elseif ($daysLeft == 0) {
                // Due today
                $todayDue[] = $notificationItem;
            } elseif ($daysLeft <= 7) {
                // Due within 7 days
                $upcoming[] = $notificationItem;
            }
        }
        
        return response()->json([
            'overdue' => $overdue,
            'today' => $todayDue,
            'upcoming' => $upcoming,
            'total' => count($overdue) + count($todayDue) + count($upcoming)
        ]);
    }
}
