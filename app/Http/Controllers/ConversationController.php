<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;

class ConversationController extends Controller
{
    public function start(Request $req) {
        $customer = $req->auth_user;
        if ($customer->is_agent) return response('', 403);

        $agent = User::where('is_agent', true)
        ->orderBy('id')
        ->first();

        $conv = Conversation::create([
            'customer_id'=>$customer->id,
            'agent_id' => $agent->id,
        ]);

        return response()->json([
            'conversation_id' => $conv->id,
            'agent' => $agent->only('id','name')
        ], 201);
    }

    public function index(Request $req) {
        $user = $req->auth_user;
        $query = Conversation::query();

        if ($user->is_agent) {
            $query
            ->where('agent_id', $user->id)
            ->where('status','open');
        } else {
            $query
            ->where('customer_id', $user->id);
        }

        return response()->json($query->get());
    }
}
