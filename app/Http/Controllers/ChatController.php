<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function vindex()
    {
        if(check_permissions('manage_chat')){
            $chats = current_vendor()->chats()->orderBy('updated_at', 'DESC')->get();
            return view('admin.chat.vindex', compact('chats'));
        }
        abort(404);
    }
}
