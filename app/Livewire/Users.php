<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\User;
use Livewire\Component;

class Users extends Component
{
    public function message($userId) {
        $authenticatedUserId  = auth()->id();
        $existingConversation = Conversation::betweenUsers($authenticatedUserId,$userId)->first();

        if($existingConversation) {
            return redirect()->route('chat', ['index' => $existingConversation->id]);
        }


        $conversation = Conversation::create([
            'sender_id' => $authenticatedUserId,
            'receiver_id' => $userId
        ]);
        return redirect()->route('chat', ['index' => $conversation->id]);



    }


    public function render()
    {

        // dd('hehe');
        return view('livewire.users',[
            'users' => User::where('id','!=',auth()->id())->get()
        ]);
    }
}
