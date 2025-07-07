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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // Debug logging
        Log::info('MessageController@index called', [
            'auth_id' => Auth::id(),
            'receiver_id' => $request->query('receiver_id')
        ]);

        // If the request expects JSON, return messages as before
        if ($request->expectsJson() || $request->wantsJson()) {
            $receiverId = $request->query('receiver_id');
            $messages = Message::where(function ($q) use ($receiverId) {
                $q->where('sender_id', Auth::id())
                    ->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)
                    ->where('receiver_id', Auth::id());
            })
                ->orderBy('sent_at')
                ->get();

            Log::info('Fetched messages', [
                'count' => $messages->count(),
                'messages' => $messages->toArray()
            ]);

            // If no messages, return a dummy message for debugging
            if ($messages->isEmpty()) {
                return response()->json([
                    [
                        'message_id' => 0,
                        'sender_id' => Auth::id(),
                        'receiver_id' => $receiverId,
                        'message_content' => '[No messages found]',
                        'sent_at' => now()->toIso8601String(),
                        'is_read' => 0,
                    ]
                ]);
            }

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
                        'is_read' => $msg->is_read,
                        'attachments' => $msg->attachments,
                        'file_url' => $msg->attachments ? $this->getS3Url($msg->attachments) : null,
                    ];
                })
            );
        }

        // Only for the generic /messages view, provide $partners for the view
        $partners = User::where('user_id', '!=', Auth::id())->get();
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
            'sender_id' => Auth::id(),
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
        $authId = Auth::id();
        // Get all adopter user IDs who have messaged or been messaged by the shelter
        $partnerIds = Message::where(function ($q) use ($authId) {
            $q->where('sender_id', $authId);
        })
            ->orWhere(function ($q) use ($authId) {
                $q->where('receiver_id', $authId);
            })
            ->get()
            ->flatMap(function ($msg) use ($authId) {
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->unique()
            ->filter(function ($id) use ($authId) {
                return $id != $authId;
            });

        // Only fetch adopters who have a message history with the shelter
        $partners = User::where('role', 'adopter')
            ->whereIn('user_id', $partnerIds)
            ->get();

        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner, $authId) {
                $q->where('sender_id', $authId)->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner, $authId) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', $authId);
            })->orderByDesc('sent_at')->first();

            if ($lastMessage && !empty($lastMessage->message_content)) {
                try {
                    $partner->decrypted_last_message = Crypt::decryptString($lastMessage->message_content);
                    Log::info('Decryption success', [
                        'partner_id' => $partner->user_id,
                        'decrypted_message' => $partner->decrypted_last_message,
                        'raw_message_content' => $lastMessage->message_content
                    ]);
                } catch (DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                    Log::error('Decryption failed', [
                        'partner_id' => $partner->user_id,
                        'error' => $e->getMessage(),
                        'raw_message_content' => $lastMessage->message_content
                    ]);
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
            Log::info('Final last_message for partner', [
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
        $authId = Auth::id();
        // Find all user IDs (shelter or rescuer) who have messaged or been messaged by the adopter
        $partnerIds = Message::where(function ($q) use ($authId) {
            $q->where('sender_id', $authId);
        })
            ->orWhere(function ($q) use ($authId) {
                $q->where('receiver_id', $authId);
            })
            ->get()
            ->flatMap(function ($msg) use ($authId) {
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->unique()
            ->filter(function ($id) use ($authId) {
                return $id != $authId;
            });

        // Get all users (shelter or rescuer) who are in those conversations
        $partners = User::with('shelterProfile')
            ->whereIn('user_id', $partnerIds)
            ->get();

        // Always include the selected receiver if not already in partners
        $receiverId = $request->query('receiver_id');
        if ($receiverId && !$partners->pluck('user_id')->contains((int)$receiverId)) {
            $receiverUser = User::with('shelterProfile')->where('user_id', $receiverId)->first();
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
                    $decrypted = Crypt::decryptString($lastMessage->message_content);
                    if (Str::startsWith($decrypted, 'eyJpdiI6')) {
                        $decrypted = Crypt::decryptString($decrypted);
                    }
                    $partner->decrypted_last_message = $decrypted;
                } catch (DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
        }
        $receiver = $receiverId
            ? User::with('shelterProfile')->where('user_id', $receiverId)->first()
            : null;
        return view('adopter.messages', compact('partners', 'receiver'));
    }

    public function rescuerMessages(Request $request)
    {
        $authId = Auth::id();
        // Get all user IDs (adopter or shelter) who have messaged or been messaged by the rescuer
        $partnerIds = Message::where(function ($q) use ($authId) {
            $q->where('sender_id', $authId);
        })
            ->orWhere(function ($q) use ($authId) {
                $q->where('receiver_id', $authId);
            })
            ->get()
            ->flatMap(function ($msg) use ($authId) {
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->unique()
            ->filter(function ($id) use ($authId) {
                return $id != $authId;
            });

        // Only fetch adopters and shelters who have a message history with the rescuer
        $partners = User::whereIn('role', ['adopter', 'shelter'])
            ->whereIn('user_id', $partnerIds)
            ->get();

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

        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner, $authId) {
                $q->where('sender_id', $authId)->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner, $authId) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', $authId);
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

        return view('rescuer.rescuer-messages', compact('partners', 'receiver'));
    }

    public function markAsRead(Request $request)
    {
        $senderId = $request->input('sender_id');
        $receiverId = Auth::id();

        Message::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        return response()->json(['success' => true]);
    }

    public function scheduleMeet(Request $request)
    {
        try {
            $request->validate([
                'application_id' => 'required|exists:adoption_applications,application_id',
                'meet_date' => 'required|date',
                'meet_time' => 'required',
                'meet_message' => 'nullable|string|max:1000',
            ]);

            $application = \App\Models\Shared\AdoptionApplication::with('pet')->findOrFail($request->application_id);
            if (!$application->pet) {
                \Log::error('ScheduleMeet: Missing pet relationship', ['application_id' => $request->application_id]);
                return response()->json(['success' => false, 'error' => 'Pet not found for this application.'], 422);
            }
            if (!$application->pet->shelter || !$application->pet->shelter->user_id) {
                \Log::error('ScheduleMeet: Missing shelter relationship', [
                    'application_id' => $request->application_id,
                    'pet_id' => $application->pet->pet_id ?? null,
                    'pet' => $application->pet,
                ]);
                return response()->json(['success' => false, 'error' => 'Shelter not found for this pet.'], 422);
            }

            // Compose the auto-generated message
            $date = date('F j, Y', strtotime($request->meet_date));
            $time = date('g:i A', strtotime($request->meet_time));
            $optional = $request->meet_message ? 'Message: ' . $request->meet_message : '';
            $content = "Your scheduled meet and greet is $date at $time. $optional";

            $message = \App\Models\Shared\Message::create([
                'sender_id' => \Auth::id(),
                'receiver_id' => $application->pet->shelter->user_id,
                'message_content' =>  \Crypt::encryptString($content),
                'sent_at' => now(),
            ]);

            \Log::info('ScheduleMeet: Message created', ['message_id' => $message->message_id, 'content' => $message->message_content]);
            return response()->json([
                'success' => true,
                'message_id' => $message->message_id,
                'message_content' => $message->message_content,
                'receiver_id' => $application->pet->shelter->user_id, // Add receiver_id for redirect
                'redirect_url' => url('/adopter/messages?receiver_id=' . $application->pet->shelter->user_id)
            ]);
        } catch (\Exception $e) {
            \Log::error('ScheduleMeet Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10mbs
            'receiver_id' => 'required|exists:users,user_id',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('attachments', 's3');
            $url = $this->getS3Url($path);


            $message = \App\Models\Shared\Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message_content' => Crypt::encryptString('[File Attachment]'),
                'attachments' => $path,
                'sent_at' => now(),
            ]);

            // Build a response array for the frontend
            $messageArr = [
                'message_id' => $message->message_id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message_content' => $message->message_content,
                'sent_at' => optional($message->sent_at)->timezone('Asia/Manila')->toIso8601String(),
                'is_read' => $message->is_read,
                'attachments' => $message->attachments,
                'file_url' => $url,
            ];
            return response()->json([
                'success' => true,
                'message' => $messageArr,
                'file_url' => $url
            ]);
        }
        return response()->json(['success' => false, 'error' => 'No file uploaded.']);
    }


    private function getS3Url($path)
    {
        // Try the most common method
        try {
            // Use the AWS S3 client directly if Storage::disk('s3')->url() is not available
            $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();
            $bucket = config('filesystems.disks.s3.bucket');
            if ($client && $bucket) {
                return $client->getObjectUrl($bucket, $path);
            }
        } catch (\Throwable $e) {
            // fallback to manual URL
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            if ($bucket && $region) {
                return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
            }
        }
        return null;
    }
}
