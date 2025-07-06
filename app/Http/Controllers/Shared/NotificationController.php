<?php

namespace App\Http\Controllers\Shared;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        return response()->json(['success' => true]);
    }

    /**
     * Delete a single notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications for the user.
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();
        return response()->json(['success' => true]);
    }
} 