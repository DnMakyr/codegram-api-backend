<?php

namespace App\Http\Controllers\Api;

use App\Events\PusherTestEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Pusher\Pusher;

use App\Events\Message as MessageEvent;

class ChatController extends Controller
{
    //Add the below functions

    public function index()
    {
        $conversations = Conversation::where('participant_1', Auth::user()->id)
            ->orWhere('participant_2', Auth::user()->id)
            ->with('participant_1', 'participant_1.profile', 'participant_2', 'participant_2.profile')->get();
        return response()->json(['conversations' => $conversations]);
    }

    public function createChat(User $user)
    {
        $loginUser = Auth::user();
        // $requestedUser = User::($reqUser);
        $existingConversation = Conversation::where(function ($query) use ($loginUser, $user) {
            $query->where('participant_1', $loginUser->id)->where('participant_2', $user->id);
        })->orWhere(function ($query) use ($loginUser, $user) {
            $query->where('participant_1', $user->id)->where('participant_2', $loginUser->id);
        })->first();

        if ($existingConversation) {
            return response()->json([
                'success' => 'Existed',
                'chatId' => $existingConversation->id
            ]);
        } else {
            $conversation = new Conversation();
            $conversation->participant_1 = $loginUser->id;
            $conversation->participant_2 = $user->id;
            $conversation->save();

            return response()->json(['newChatId' => $conversation->id]);
        }
    }
    public function loadChat(Conversation $conversation)
    {
        $conversationId = $conversation->id;
        $replier = User::find($conversation->participant_1 == Auth::user()->id ? $conversation->participant_2 : $conversation->participant_1);
        $replier->load('profile');

        $messages = Message::where('conversation_id', $conversationId)->with('user')->get();
        return response()->json([
            'replier' => $replier,
            'messages' => $messages
        ]);
    }
    public function sendMessage(Request $request)
    {
        $userId = $request->sender;
        $conversationId = $request->chatId;
        $message = $request->content;
        broadcast(new MessageEvent($conversationId, $message, $userId))->toOthers();
        return ['message' => array('message' => $message, 'sender' => $userId)];
    }
    public function receiveMessage(Request $request)
    {
        return ['message' => $request->get('message')];
    }
}
