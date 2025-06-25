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
        $receiverId = $request->query('receiver_id');
        $messages = Message::where(function ($q) use ($receiverId) {
            $q->where('sender_id', auth()->id())
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($receiverId) {
            $q->where('sender_id', $receiverId)
                ->where('receiver_id', auth()->id());
        })->orderBy('sent_at')->get();

        return response()->json(
            $messages->map(function ($msg) {
                return [
                    'message_id' => $msg->message_id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'message_content' => Crypt::decryptString($msg->message_content),
                    'sent_at' => optional($msg->sent_at)->timezone('Asia/Manila')->toIso8601String(),
                ];
            })
        );
    }

    public function send(Request $request)
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
        \Log::info('DEBUG: adopterMessages controller called', [
            'receiver_id' => $request->query('receiver_id'),
            'auth_id' => auth()->id(),
            'time' => now()->toDateTimeString(),
        ]);

        $partners = User::with('shelterProfile')
            ->where('role', 'shelter')
            ->get();
        $receiverId = $request->query('receiver_id');
        if ($receiverId && !$partners->pluck('user_id')->map(fn($id) => (int)$id)->contains((int)$receiverId)) {
            $receiverUser = User::with('shelterProfile')->where('user_id', $receiverId)->first();
            if ($receiverUser) {
                $partners->push($receiverUser);
            }
        }
        \Log::info('DEBUG partners list', [
            'partner_ids' => $partners->pluck('user_id')->all(),
            'receiver_id' => $receiverId,
        ]);

        $partners = $partners->unique('user_id')->values();

        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', auth()->id());
            })
                ->orderByDesc('sent_at')
                ->orderByDesc('message_id')
                ->first();
            \Log::info('DEBUG lastMessage for partner', [
                'auth_id' => auth()->id(),
                'partner_id' => $partner->user_id,
                'lastMessage' => $lastMessage,
            ]);
            if ($lastMessage && !empty($lastMessage->message_content)) {
                try {
                    $decrypted = \Crypt::decryptString($lastMessage->message_content);
                    if (\Illuminate\Support\Str::startsWith($decrypted, 'eyJpdiI6')) {
                        $decrypted = \Crypt::decryptString($decrypted);
                    }
                    $partner->decrypted_last_message = $decrypted; // Use real property
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
            \Log::info('Adopter sidebar debug', [
                'partner_id' => $partner->user_id,
                'last_message' => $lastMessage ? $lastMessage->message_content : null,
                'decrypted_last_message' => $partner->decrypted_last_message,
                'last_message_time' => $partner->last_message_time,
                'auth_id' => auth()->id(),
                'last_message_id' => $lastMessage ? $lastMessage->message_id : null,
                'last_message_sent_at' => $lastMessage ? $lastMessage->sent_at : null,
            ]);
        }
        $receiver = $receiverId
            ? User::with('shelterProfile')->where('user_id', $receiverId)->first()
            : null;
        return view('adopter.messages', compact('partners', 'receiver'));
    }
}
