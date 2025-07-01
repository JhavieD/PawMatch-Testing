<?php


namespace App\Http\Controllers\Shared;

use App\Models\Shared\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Shared\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // If the request expects JSON, return messages as before
        if ($request->expectsJson() || $request->wantsJson()) {
            $receiverId = $request->query('receiver_id');
            $messages = Message::where(function ($q) use ($receiverId) {
                $q->where('sender_id', auth()->id())
                    ->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)
                    ->where('receiver_id', auth()->id());
            })
                ->orderBy('sent_at')
                ->get();

            return response()->json(
                $messages->map(function ($msg) {
                    // Handle double encryption if needed
                    try {
                        $decrypted = Crypt::decryptString($msg->message_content);
                        if (Str::startsWith($decrypted, 'eyJpdiI6')) {
                            $decrypted = Crypt::decryptString($decrypted);
                        }
                    } catch (\Exception $e) {
                        $decrypted = '[Unable to decrypt]';
                    }
                    return [
                        'message_id' => $msg->message_id,
                        'sender_id' => $msg->sender_id,
                        'receiver_id' => $msg->receiver_id,
                        'message_content' => $decrypted,
                        'sent_at' => optional($msg->sent_at)->timezone('Asia/Manila')->toIso8601String(),
                    ];
                })
            );
        }

        // Only for the generic /messages view, provide $partners for the view
        $partners = User::where('user_id', '!=', auth()->id())->get();
        $receiver = $request->query('receiver_id')
            ? User::where('user_id', $request->query('receiver_id'))->first()
            : null;
        return view('messages', compact('partners', 'receiver'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message_content' => Crypt::encryptString($request->message),
            'sent_at' => now(),
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message_id' => $message->message_id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message_content' => Crypt::decryptString($message->message_content),
            'sent_at' => $message->sent_at->timezone('Asia/Manila')->toIso8601String(),
        ], 201);
    }

    public function shelterMessages(Request $request)
    {
        // fetch all adopters
        $partners = User::where('role', 'adopter')->get();
        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', auth()->id());
            })->orderByDesc('sent_at')->first();

            if ($lastMessage && !empty($lastMessage->message_content)) {
                try {
                    $partner->decrypted_last_message = Crypt::decryptString($lastMessage->message_content);
                    \Log::info('Decryption success', [
                        'partner_id' => $partner->user_id,
                        'decrypted_message' => $partner->decrypted_last_message,
                        'raw_message_content' => $lastMessage->message_content
                    ]);
                } catch (DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                    \Log::error('Decryption failed', [
                        'partner_id' => $partner->user_id,
                        'error' => $e->getMessage(),
                        'raw_message_content' => $lastMessage->message_content
                    ]);
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
            \Log::info('Final last_message for partner', [
                'partner_id' => $partner->user_id,
                'last_message' => $partner->decrypted_last_message,
                'raw_message_content' => $lastMessage ? $lastMessage->message_content : null
            ]);
        }

        $receiver = $request->query('receiver_id')
            ? User::where('user_id', $request->query('receiver_id'))->first()
            : null;

        return view('shelter.messages', compact('partners', 'receiver'));
    }

    public function adopterMessages(Request $request)
    {
        $authId = auth()->id();
        // Find all user IDs (shelter or rescuer) who have messaged or been messaged by the adopter
        $partnerIds = \App\Models\Shared\Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->get()
            ->flatMap(function ($msg) use ($authId) {
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->unique()
            ->filter(fn($id) => $id != $authId)
            ->values();

        // Get all users (shelter or rescuer) who are in those conversations
        $partners = \App\Models\Shared\User::with('shelterProfile')
            ->whereIn('user_id', $partnerIds)
            ->get();

        // Always include the selected receiver if not already in partners
        $receiverId = $request->query('receiver_id');
        if ($receiverId && !$partners->pluck('user_id')->contains((int)$receiverId)) {
            $receiverUser = \App\Models\Shared\User::with('shelterProfile')->where('user_id', $receiverId)->first();
            if ($receiverUser) {
                $partners->push($receiverUser);
            }
        }
        $partners = $partners->unique('user_id')->values();

        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner, $authId) {
                $q->where('sender_id', $authId)->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner, $authId) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', $authId);
            })
                ->orderByDesc('sent_at')
                ->orderByDesc('message_id')
                ->first();
            if ($lastMessage && !empty($lastMessage->message_content)) {
                try {
                    $decrypted = \Crypt::decryptString($lastMessage->message_content);
                    if (\Illuminate\Support\Str::startsWith($decrypted, 'eyJpdiI6')) {
                        $decrypted = \Crypt::decryptString($decrypted);
                    }
                    $partner->decrypted_last_message = $decrypted;
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
        }
        $receiver = $receiverId
            ? \App\Models\Shared\User::with('shelterProfile')->where('user_id', $receiverId)->first()
            : null;
        return view('adopter.messages', compact('partners', 'receiver'));
    }

    public function rescuerMessages(Request $request)
    {
        // Fetch all adopters and shelters as possible partners for rescuers
        $partners = User::whereIn('role', ['adopter', 'shelter'])->get();
        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', auth()->id());
            })->orderByDesc('sent_at')->first();

            if ($lastMessage && !empty($lastMessage->message_content)) {
                try {
                    $partner->decrypted_last_message = Crypt::decryptString($lastMessage->message_content);
                } catch (DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
        }

        $receiver = $request->query('receiver_id')
            ? User::where('user_id', $request->query('receiver_id'))->first()
            : null;


        $receiverId = $request->query('receiver_id');
        if ($receiverId && !$partners->pluck('user_id')->contains((int)$receiverId)) {
            $receiverUser = User::where('user_id', $receiverId)->first();
            if ($receiverUser) {
                $partners->push($receiverUser);
            }
        }
        $partners = $partners->unique('user_id')->values();

        return view('rescuer.rescuer-messages', compact('partners', 'receiver'));
    }
}
