<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessagesOutbox;
use Illuminate\Support\Facades\Http;


class MessageController extends Controller
{
    public function send(Request $req, $cid) {
        $user = $req->auth_user;
        
        $conv = Conversation::find($cid) ?? abort(404);
        if (!in_array($user->id, [$conv->customer_id, $conv->agent_id]))
            return response()->json(['error'=>'Forbidden'],403);

        $validated = $req->validate([
            'content'=>'required|string'
        ]);
        
        $msg = Message::create([
            'conversation_id'=>$cid,
            'sender_id'=>$user->id,
            'content'=>$validated['content']
        ]);

        $goServerUrl = env('GO_SERVER_URL', 'http://localhost:8080/push_message');
        
        try {
           $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($goServerUrl, [
                'message_id' => $msg->id,
                'conversation_id' => (int)$cid,
                'sender_id' => $user->id,
                'content' => $validated['content'],
                'created_at' => $msg->created_at->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to notify Go server: ' . $e->getMessage());
        }

        return response()->json([
            'message_id' => $msg->id,
            'timestamp' => $msg->created_at
        ], 201);

    }

    public function index(Request $req, $cid) {
        $user = $req->auth_user;
        $conv = Conversation::find($cid) ?? abort(404);
        if (!in_array($user->id, [$conv->customer_id, $conv->agent_id]))
            return response()
            ->json([
                'error'=>'Forbidden'
            ],403);

        $msgs = Message::where('conversation_id', $cid)
            ->orderBy('created_at','desc')
            ->paginate(20);
        return response()->json($msgs);
    }

}
