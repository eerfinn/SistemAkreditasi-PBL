<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.notifications.index', compact('notifications'));
    }

    /**
     * Get user's notifications for navbar dropdown
     */
    public function getNavbarNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Mark a notification as read and redirect to its link
     */
    public function read($id)
    {
        $notification = Notification::findOrFail($id);

        // Verify the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke notifikasi ini.');
        }

        $notification->markAsRead();

        // Redirect to the notification link if available
        if ($notification->link) {
            // Redirect dokumen links to kriteria page
            if ($notification->type === 'dokumen' && Str::startsWith($notification->link, '/dokumen/')) {
                if ($notification->kriteria_id) {
                    return redirect("/kriteria/{$notification->kriteria_id}");
                }
            }

            // For all other notifications, use the original link
            return redirect($notification->link);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        // Verify the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke notifikasi ini.');
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi telah dihapus.');
    }

    /**
     * Delete all read notifications
     */
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', true)
            ->delete();

        return redirect()->back()->with('success', 'Semua notifikasi yang telah dibaca telah dihapus.');
    }
}
