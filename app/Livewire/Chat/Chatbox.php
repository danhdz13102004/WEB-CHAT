<?php

namespace App\Livewire\Chat;

use App\Events\MessageEvent;
use App\Events\MyEvent;
use App\Events\TestEvent;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageSent;
use App\Notifications\ReadMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

use Livewire\Attributes\On;

class Chatbox extends Component
{
    use WithFileUploads;
    public $selectedConversation;

    public $body = '';

    public $files = [];

    public $audio = [];

    public $loadedMessages;

    public $limitedMsg = 10;

    public $listeners = ['loadMore'];

    #[On('sendAudio')]
    public function audioRecorded()
    {
        try {
            Log::info('demo');

        // $audioPath = request()->file('audio')->store('audio-uploads');
        $path = '/storage/' .  request()->file('audio')->store('audio', 'public');

        Message::create([
            'conversation_id' => request()->input('id_conversation'),
            'sender_id' => request()->input('id_user'),
            'receiver_id' => request()->input('id_receiver'),
            'body' => $path,
            'type' => 'audio'
        ]);


        return response()->json(['path' => $path], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error message: ' . $e->getMessage());
    
            // Optionally, you can log the entire exception
            Log::error('Exception details: ', ['exception' => $e]);
        }
        

    }


    public function demo()
    {
        dd('vloiz');
    }

    public function getListeners()
    {
        $auth_id = Auth::user()->id;
        return [
            'loadMore',
            "echo-private:users.{$auth_id},.Illuminate\Notifications\Events\BroadcastNotificationCreated" => 'broadcastedNotifications',
            "echo-private:users.{$auth_id},.message-sent" => 'broadCastEvent'
        ];
    }

    public function broadcastedNotifications($event)
    {
        // dd($event);
        if ($event['type'] == MessageSent::class || $event['type'] == MessageEvent::class) {
            if ($this->selectedConversation->id == $event['conversation_id']) {
                $message = Message::find($event['message_id']);
                $this->loadedMessages->push($message);
                $this->dispatch('content-changed');
            }
            $this->dispatch('refresh')->to('chat.chat-list');
        } else if ($event['type'] == ReadMessage::class) {
            // dd($event['time']);
            $this->dispatch('read-msg');
        }
    }

    public function broadCastEvent($event)
    {
        // dd($event);
        if ($event['type'] == 'sendMsg') {
            if ($this->selectedConversation->id == $event['conversation_id']) {
                $message = Message::find($event['message_id']);
                $this->loadedMessages->push($message);
                $this->dispatch('content-changed');
            }
            $this->dispatch('refresh')->to('chat.chat-list');
        }
    }

    public function loadMore()
    {
        $this->limitedMsg += 10;
        $this->loadMessages();
        $this->dispatch('update-chat-height');
    }

    public function  mount()
    {
        $this->loadMessages();
    }
    public function loadMessages()
    {
        $messages = $this->selectedConversation->messages()->latest()
            ->limit($this->limitedMsg)
            ->get();
        $messages = $messages->sortBy('created_at');
        $this->loadedMessages = $messages;
    }

    public function sendMessage()
    {
        $this->validate([
            'body' => 'required|string'
        ]);
        $cnt = 0;
        dd($this->files);
        if ($this->files) {
            foreach ($this->files as $file) {
                // Get the MIME type of the file
                $mimeType = $file->getMimeType();

                // dd($mimeType);

                if (Str::startsWith($mimeType, 'image/')) {
                    $path = '/storage/' .  $file->store('images', 'public');
                    // dd($path);
                    $Img = Message::create([
                        'conversation_id' => $this->selectedConversation->id,
                        'sender_id' => Auth::user()->id,
                        'receiver_id' => $this->selectedConversation->getReceiver()->id,
                        'body' => $path,
                        'type' => 'image'
                    ]);
                    $this->loadedMessages->push($Img);
                    MessageEvent::dispatch(Auth::user(), $Img, $this->selectedConversation, $this->selectedConversation->getReceiver()->id);
                } else if (Str::startsWith($mimeType, 'application/')) {
                    $name = $file->getClientOriginalName();
                    $path = '/storage/' .  $file->store('doc', 'public');
                    // dd($path);
                    $Doc = Message::create([
                        'conversation_id' => $this->selectedConversation->id,
                        'sender_id' => Auth::user()->id,
                        'receiver_id' => $this->selectedConversation->getReceiver()->id,
                        'body' => $path . ' ' . $name,
                        'type' => 'doc'
                    ]);
                    $this->loadedMessages->push($Doc);
                    MessageEvent::dispatch(Auth::user(), $Doc, $this->selectedConversation, $this->selectedConversation->getReceiver()->id);
                } else if (Str::startsWith($mimeType, 'video/')) {
                    // dd('video nha');?
                    $path = '/storage/' .  $file->store('videos', 'public');
                    $Video = Message::create([
                        'conversation_id' => $this->selectedConversation->id,
                        'sender_id' => Auth::user()->id,
                        'receiver_id' => $this->selectedConversation->getReceiver()->id,
                        'body' => $path,
                        'type' => 'video'
                    ]);
                    $this->loadedMessages->push($Video);
                    MessageEvent::dispatch(Auth::user(), $Video, $this->selectedConversation, $this->selectedConversation->getReceiver()->id);
                }
            }
        }
        // dd($this->files);
        // dd($cnt);
        $this->files = [];
        $createdMsg = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'body' => $this->body,
            'type' => 'text'
        ]);
        $this->loadedMessages->push($createdMsg);

        // $this->selectedConversation->getReceiver()
        //     ->notify(new MessageSent(
        //     Auth::User(),
        //     $createdMsg,
        //     $this->selectedConversation,
        //     $this->selectedConversation->getReceiver()->id
        // ));

        // Notification::sendNow($this->selectedConversation->getReceiver(),(new MessageSent(
        //     Auth::User(),
        //     $createdMsg,
        //     $this->selectedConversation,
        //     $this->selectedConversation->getReceiver()->id
        // )));


        MessageEvent::dispatch(Auth::user(), $createdMsg, $this->selectedConversation, $this->selectedConversation->getReceiver()->id);


        $this->body = '';
        $this->dispatch('content-changed');
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->dispatch('refresh')->to('chat.chat-list');
        // dd($createdMsg);
    }

    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}
