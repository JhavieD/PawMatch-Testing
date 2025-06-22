<?php


namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\User;
use Carbon\Carbon;

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
                    'message_content' => $msg->message_content,
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
            'message_content' => $request->message,
            'sent_at' => now(),

        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message_id' => $message->message_id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message_content' => $message->message_content,
            'sent_at' => $message->sent_at->timezone('Asia/Manila')->toIso8601String(),
        ], 201);
    }

    public function shelterMessages(Request $request)
    {
        // fetch all adopters
        $partners = User::where('role', 'adopter')->get();

        // ✅ Attach latest message + time to each adopter
        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', auth()->id());
            })->orderByDesc('sent_at')->first();
        }

        // ✅ Get the selected receiver if provided
        $receiver = $request->query('receiver_id')
            ? User::where('user_id', $request->query('receiver_id'))->first()
            : null;

        return view('shelter.messages', compact('partners', 'receiver'));
    }

    public function adopterMessages(Request $request)
    {
        $partners = User::with('shelterProfile')
            ->where('role', 'shelter')
            ->get();

        foreach ($partners as $partner) {
            $lastMessage = Message::where(function ($q) use ($partner) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', auth()->id());
            })->orderByDesc('sent_at')->first();
        }

        $receiver = $request->query('receiver_id')
            ? User::with('shelterProfile')->where('user_id', $request->query('receiver_id'))->first()
            : null;

        return view('adopter.messages', compact('partners', 'receiver'));
    }
}
