<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use Livewire\Component;
use App\Models\Contact;
use App\Models\Message;

use Illuminate\Support\Facades\Notification;
use Livewire\WithFileUploads;

class ChatComponent extends Component
{
    use WithFileUploads;

    public $search;

    public $contactChat, $chat, $chat_id;

    public $bodyMessage;
    public $images = [];
    public $audio;

    public $users;

    public function mount(){
        $this->users = collect();
    }

    //Oyentes

    public function getListeners()
    {
        $user_id = auth()->user()->id;

        return [
            "echo-notification:App.Models.User.{$user_id},notification" => 'render',
            "echo-presence:chat.1,here" => 'chatHere',
            "echo-presence:chat.1,joining" => 'chatJoining',
            "echo-presence:chat.1,leaving" => 'chatLeaving',
        ];
    }

    //Propiedad computadas
    public function getContactsProperty(){
        return Contact::where('user_id', auth()->id())
                ->when($this->search, function($query){

                    $query->where(function($query){
                        $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function($query){
                                $query->where('email', 'like', '%'.$this->search.'%');
                            });
                    });

                })
                ->get() ?? [];
    }

    public function getMessagesProperty(){
        return $this->chat ? $this->chat->messages()->get() : [];
        //$this->chat->messages()->get()
        //Message::where('chat_id', $this->chat->id)->get()
    }

    public function getChatsProperty(){
        return auth()->user()->chats()->get()->sortByDesc('last_message_at'); 
    }

    public function getUsersNotificationsProperty(){
        return  $this->chat ? $this->chat->users->where('id', '!=', auth()->id()) : collect();
    }


    public function getActiveProperty(){
        $user = $this->users_notifications->first();
        return $user ? $this->users->contains($user->id) : false;
    }


    //Ciclo de vida
    public function updatedBodyMessage($value){

        if($value && $this->chat && $this->users_notifications->isNotEmpty()){
            Notification::send($this->users_notifications, new \App\Notifications\UserTyping($this->chat->id));
        }

    }



    //Métodos
    public function open_chat_contact(Contact $contact){

        $chat = auth()->user()->chats()
                    ->whereHas('users', function($query) use ($contact){
                        $query->where('user_id', $contact->contact_id);
                    })
                    ->has('users', 2)
                    ->first();

        if($chat){
            $this->chat = $chat;
            $this->chat_id = $chat->id;
            $this->reset('contactChat', 'bodyMessage', 'search');
        }else{
            $this->contactChat = $contact;
            $this->reset('chat', 'bodyMessage', 'search');
        }


    }

    public function open_chat(Chat $chat){
        $this->chat = $chat;
        $this->chat_id = $chat->id;
        $this->reset('contactChat', 'bodyMessage');
    }

    public function sendMessage(){
        $this->validate([
            'bodyMessage' => 'required_without_all:images,audio|nullable',
            'images' => 'required_without_all:bodyMessage,audio|nullable|array|max:10',
            'images.*' => 'image|max:4096',
            'audio' => 'required_without_all:bodyMessage,images|nullable|mimes:mp3,ogg,webm,wav|max:5120'
        ]);

        if(!$this->chat){
            $this->chat = Chat::create();
            $this->chat_id = $this->chat->id;
            $this->chat->users()->attach([auth()->user()->id, $this->contactChat->contact_id]);
        }

        if ($this->bodyMessage) {
            $this->chat->messages()->create([
                'body' => $this->bodyMessage,
                'user_id' => auth()->user()->id
            ]);
        }

        if (is_array($this->images) && count($this->images) > 0) {
            foreach ($this->images as $img) {
                $path = $img->store('messages', 'public');
                $this->chat->messages()->create([
                    'body' => '',
                    'image_path' => $path,
                    'user_id' => auth()->user()->id
                ]);
            }
        }

        if ($this->audio) {
            $audioPath = $this->audio->store('messages', 'public');
            $this->chat->messages()->create([
                'body' => '',
                'audio_path' => $audioPath,
                'user_id' => auth()->user()->id
            ]);
        }

        Notification::send($this->users_notifications, new \App\Notifications\NewMessage());

        $this->reset('bodyMessage', 'images', 'audio', 'contactChat');
    }

    public function removeImage($index){
        if (is_array($this->images) && array_key_exists($index, $this->images)) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    public function chatHere($users){
        $this->users = collect($users)->pluck('id');
    }

    public function chatJoining($user){
        $this->users->push($user['id']);
    }

    public function chatLeaving($user){
        $this->users = $this->users->filter(function($id) use ($user){
            return $id != $user['id'];
        }); 
    }

    public function render()
    {

        if ($this->chat) {

            $this->chat->messages()->where('user_id', '!=', auth()->id())->where('is_read', false)->update([
                'is_read' => true
            ]);
    
            /* Notification::send($this->users_notifications, new \App\Notifications\NewMessage()); */


            $this->emit('scrollIntoView');
        }

        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
