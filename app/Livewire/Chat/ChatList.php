<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ChatList extends Component
{
    public $selectedConversation;

    public $index;

    protected $listeners = ['refresh' => '$refresh',
                        ];


    public function render()
    {
        return view('livewire.chat.chat-list',[
            'conversations' => auth()->user()->conversations()->latest('updated_at')->get()
        ]);
    }
}
