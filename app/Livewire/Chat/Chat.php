<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\ReadMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $index;

    public $selectedConversation;

    public function mount() {
        $this->selectedConversation = Conversation::findOrFail($this->index);

        if($this->selectedConversation->getNewestMsg()?->read_at == null) {
            $user = null;
            if($this->selectedConversation->sender_id != Auth::user()->id) {
                $user = User::find($this->selectedConversation->sender_id);
            }
            else {
                $user = User::find($this->selectedConversation->receiver_id);
            }
            $user->notify(new ReadMessage($this->selectedConversation->id,now()));
        }


        
        Message::where('conversation_id',$this->selectedConversation->id)
                    ->where('receiver_id',Auth::user()->id)
                    ->whereNull('read_at')
                    ->update(['read_at'=>now()]);
    }
    public function render()
    {
        return view('livewire.chat.chat');
    }
}
