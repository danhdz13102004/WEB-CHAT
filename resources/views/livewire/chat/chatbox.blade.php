

<div x-data = "{
    height:0,
    btnLoad: document.querySelector('.btn-load'),

}" class="w-full overflow-hidden">
    <div class="border-b flex flex-col overflow-y-scroll grow h-full">
        <header class="w-full sticky inset-x-0 flex py-1 top-0 z-10 bg-white border-b">
            <div class="flex w-full items-center px-2 lg:px-4 gap-2 md:gap-5">
                <a class="shrink-0 lg:hidden" href="#">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                    </svg>

                </a>
                <div class="shrink-0">
                    <x-avatar
                        src="https://api.dicebear.com/6.x/fun-emoji/svg?seed={{ $selectedConversation->getReceiver()->name }}"
                        class="h-9 w-9 lg:w-11 lg:h-11"></x-avatar>
                </div>

                <h6 class="font-bold truncate"> {{ $selectedConversation->getReceiver()->name }} </h6>


            </div>
        </header>

        <main
            @scroll="
        scropTop = $el.scrollTop;
        height = $el.scrollHeight;
        if(scropTop <= 0){
            console.log('on top');
          $wire.loadMore();
        }
       "
            @update-chat-height.window="
        setTimeout(() => {
            newHeight= $el.scrollHeight;
            console.log(height,newHeight);

            oldHeight= height;

            $el.scrollTop= newHeight- oldHeight;
   
            height=newHeight;
            }, 0);
        "
            class="main-chat flex flex-col gap-3 p-2.5 overflow-y-auto flex-grow overscroll-contain  overflow-x-hidden w-full my-auto">
            @if ($loadedMessages)
                @php
                    $beforeId = null;
                    $status = true;

                @endphp

                @foreach ($loadedMessages as $message)
                    @php
                        if ($beforeId == null && $message->sender_id != auth()->id()) {
                            $status = false;
                            $beforeId = $message->sender_id;
                        } else {
                            $status = true;
                            if ($message->sender_id == auth()->id()) {
                                $beforeId = null;
                            }
                        }
                    @endphp

                    @if ($message->type == 'text')
                        <div @class([
                            'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                            'ml-auto' => $message->sender_id == auth()->id(),
                        ])>
                            <div @class(['shrink-0', 'invisible' => $status])>
                                <x-avatar />
                            </div>
                            <div @class([
                                'flex flex-wrap text-[15px] rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8]',
                                'rounded-bl-none border border-gray-200/40' =>
                                    !$message->sender_id == auth()->id(),
                                'rounded-br-none bg-blue-500/80 text-white' =>
                                    $message->sender_id == auth()->id(),
                            ])>
                                <p
                                    class="whitespace-normal truncate text-sm md:text-base tracking-wide lg:tracking-normal">
                                    {{ $message->body }}
                                </p>

                                <div class="ml-auto flex gap-2">

                                    <p @class([
                                        'text-xs ',
                                        'text-gray-500' => $message->sender_id != auth()->user()->id,
                                        'text-white' => $message->sender_id === auth()->user()->id,
                                    ])>


                                        {{ $message->created_at->format('g:i a') }}

                                    </p>


                                    {{-- message status , only show if message belongs auth --}}


                                    <div x-data="{ markAsRead: false }">

                                        {{-- double ticks --}}

                                        @if ($message->read_at)
                                            <span x-cloak @class([
                                                'text-gray-500' => $message->sender_id != auth()->user()->id,
                                                'text-white' => $message->sender_id === auth()->user()->id,
                                            ])>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z" />
                                                    <path
                                                        d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z" />
                                                </svg>
                                            </span>
                                        @else
                                            {{-- single ticks --}}
                                            <span @class([
                                                'text-gray-500' => $message->sender_id != auth()->user()->id,
                                                'text-white' => $message->sender_id === auth()->user()->id,
                                            ])>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                                    <path
                                                        d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                                                </svg>
                                            </span>
                                        @endif



                                    </div>



                                </div>

                            </div>
                        </div>
                    @elseif($message->type == 'image')
                        <div @class([
                            'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                            'ml-auto' => $message->sender_id == auth()->id(),
                        ])>
                            <div @class(['shrink-0', 'invisible' => $status])>
                                <x-avatar />
                            </div>
                            <div>
                                <img src="{{ $message->body }}" class="w-52 h-auto ">
                            </div>
                        </div>
                    @elseif($message->type == 'video')
                        <div @class([
                            'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                            'ml-auto' => $message->sender_id == auth()->id(),
                        ])>
                            <div @class(['shrink-0', 'invisible' => $status])>
                                <x-avatar />
                            </div>
                            <div>
                                <video controls class="w-72 h-auto">
                                    <source src="{{ $message->body }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    @elseif($message->type == 'doc')
                        <div @class([
                            'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                            'ml-auto' => $message->sender_id == auth()->id(),
                        ])>
                            <div @class(['shrink-0', 'invisible' => $status])>
                                <x-avatar />
                            </div>
                            <div @class([
                                'flex flex-wrap text-[15px] rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8]',
                                'rounded-bl-none border border-gray-200/40' =>
                                    !$message->sender_id == auth()->id(),
                                'rounded-br-none bg-blue-500/80 text-white' =>
                                    $message->sender_id == auth()->id(),
                            ])>
                            <div ">
                                @php
                                    $word = explode(' ', $message->body);
                                    $s = count($word);
                                    $str = '';

                                    for ($i = 1; $i < $s; $i++) {
                                        $str .= $word[$i];
                                        if ($i < $s - 1) {
                                            $str .= ' ';
                                        } 
                                    }
                                @endphp
                                <a class="font-bold flex" href="{{ $word[0] }}" download>
                                    <svg style="width: 20px;" fill="#000000" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M17.5 0h-11c-1.104 0-2 0.895-2 2v28c0 1.105 0.896 2 2 2h19c1.105 0 2-0.895 2-2v-20zM25.5 10.829v0.171h-9v-9h0.172zM6.5 30v-28h8v11h11v17h-19z"></path> </g></svg>
                            <span class="mx-2">{{ $str }}</span>            
                        </a>
                            </div>
                            </div>
                        </div>
                    @elseif ($message->type == 'audio')
                    <div @class([
                        'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                        'ml-auto' => $message->sender_id == auth()->id(),
                    ])>
                        <div @class(['shrink-0', 'invisible' => $status])>
                            <x-avatar />
                        </div>
                        <div>
                            <audio src="{{ $message->body }}" controls></audio>
                        </div>
                    </div>
                    
                        @endif
                @endforeach


                @php
                    $newestMsg = $selectedConversation->getNewestMsg();
                @endphp

                @if ($newestMsg != null && $newestMsg->sender_id === auth()->user()->id && $newestMsg->read_at != null)
                    <div class="flex justify-end">
                        <span class="fs-sm #ccc">
                            @php
                                $formattedTime = \Carbon\Carbon::createFromTimestamp($newestMsg->read_at)->format(
                                    'g:i a',
                                );
                            @endphp
                            <svg style="width: 20px;" class="inline-block" fill="#000000" version="1.1" id="Capa_1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                viewBox="0 0 442.04 442.04" xml:space="preserve">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g>
                                        <g>
                                            <path
                                                d="M221.02,341.304c-49.708,0-103.206-19.44-154.71-56.22C27.808,257.59,4.044,230.351,3.051,229.203 c-4.068-4.697-4.068-11.669,0-16.367c0.993-1.146,24.756-28.387,63.259-55.881c51.505-36.777,105.003-56.219,154.71-56.219 c49.708,0,103.207,19.441,154.71,56.219c38.502,27.494,62.266,54.734,63.259,55.881c4.068,4.697,4.068,11.669,0,16.367 c-0.993,1.146-24.756,28.387-63.259,55.881C324.227,321.863,270.729,341.304,221.02,341.304z M29.638,221.021 c9.61,9.799,27.747,27.03,51.694,44.071c32.83,23.361,83.714,51.212,139.688,51.212s106.859-27.851,139.688-51.212 c23.944-17.038,42.082-34.271,51.694-44.071c-9.609-9.799-27.747-27.03-51.694-44.071 c-32.829-23.362-83.714-51.212-139.688-51.212s-106.858,27.85-139.688,51.212C57.388,193.988,39.25,211.219,29.638,221.021z">
                                            </path>
                                        </g>
                                        <g>
                                            <path
                                                d="M221.02,298.521c-42.734,0-77.5-34.767-77.5-77.5c0-42.733,34.766-77.5,77.5-77.5c18.794,0,36.924,6.814,51.048,19.188 c5.193,4.549,5.715,12.446,1.166,17.639c-4.549,5.193-12.447,5.714-17.639,1.166c-9.564-8.379-21.844-12.993-34.576-12.993 c-28.949,0-52.5,23.552-52.5,52.5s23.551,52.5,52.5,52.5c28.95,0,52.5-23.552,52.5-52.5c0-6.903,5.597-12.5,12.5-12.5 s12.5,5.597,12.5,12.5C298.521,263.754,263.754,298.521,221.02,298.521z">
                                            </path>
                                        </g>
                                        <g>
                                            <path
                                                d="M221.02,246.021c-13.785,0-25-11.215-25-25s11.215-25,25-25c13.786,0,25,11.215,25,25S234.806,246.021,221.02,246.021z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            Seen at:
                            {{ \Carbon\Carbon::parse($newestMsg->read_at)->format('g:i a') }}
                        </span>
                    </div>
                @endif

            @endif

        </main>

        <footer class="shrink-0 z-10 bg-white inset-x-0">

            <div class=" p-2 border-t">

                <form x-data="{ msg: @entangle('body').live }" @submit.prevent="$wire.sendMessage" method="POST" autocapitalize="off">
                    @csrf

                    <input type="hidden" autocomplete="false" style="display:none">

                    <div class="grid grid-cols-12">
                        <input x-model="msg" type="text" autocomplete="off" autofocus
                            placeholder="write your message here" maxlength="1700"
                            class="inp-text col-span-10 bg-gray-100 border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg  focus:outline-none">
                            <div style="width: 100%;" class="flex items-center justify-between">
                                 <div style="margin-right: 200px" class="record hidden"  style="flex-grow: 1">  
                                        <svg class="inline-block" style="width: 20px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10.0303 8.96965C9.73741 8.67676 9.26253 8.67676 8.96964 8.96965C8.67675 9.26255 8.67675 9.73742 8.96964 10.0303L10.9393 12L8.96966 13.9697C8.67677 14.2625 8.67677 14.7374 8.96966 15.0303C9.26255 15.3232 9.73743 15.3232 10.0303 15.0303L12 13.0607L13.9696 15.0303C14.2625 15.3232 14.7374 15.3232 15.0303 15.0303C15.3232 14.7374 15.3232 14.2625 15.0303 13.9696L13.0606 12L15.0303 10.0303C15.3232 9.73744 15.3232 9.26257 15.0303 8.96968C14.7374 8.67678 14.2625 8.67678 13.9696 8.96968L12 10.9393L10.0303 8.96965Z" fill="#1C274C"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 17.1086 17.1086 21.25 12 21.25C6.89137 21.25 2.75 17.1086 2.75 12Z" fill="#1C274C"></path> </g></svg>
                                        <audio id="audioPlayback" controls></audio>
                                </div>
                                <div class="flex" >
                                    <svg class="hidden stop-record" style="width: 20px;"  viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect x="1" y="1" width="14" height="14" fill="#000000"></rect> </g></svg>
                                    <svg class="start-record" style="width: 20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 14.2857C13.4229 14.2857 14.5714 13.1371 14.5714 11.7143V6.57143C14.5714 5.14857 13.4229 4 12 4C10.5771 4 9.42857 5.14857 9.42857 6.57143V11.7143C9.42857 13.1371 10.5771 14.2857 12 14.2857Z" fill="#000000"></path> <path d="M16.5429 11.7143H18C18 14.6371 15.6686 17.0543 12.8571 17.4743V20.2857H11.1429V17.4743C8.33143 17.0543 6 14.6371 6 11.7143H7.45714C7.45714 14.2857 9.63429 16.0857 12 16.0857C14.3657 16.0857 16.5429 14.2857 16.5429 11.7143Z" fill="#000000"></path> </g></svg>
                                    <svg class="send-file block ml-4" style="width: 20px; height: 20px; " viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7 8.00092L7 17C7 17.5523 6.55228 18 6 18C5.44772 18 5.00001 17.4897 5 16.9374C5 16.9374 5 16.9374 5 16.9374C5 16.937 5.00029 8.01023 5.00032 8.00092C5.00031 7.96702 5.00089 7.93318 5.00202 7.89931C5.00388 7.84357 5.00744 7.76644 5.01426 7.67094C5.02788 7.4803 5.05463 7.21447 5.10736 6.8981C5.21202 6.27011 5.42321 5.41749 5.85557 4.55278C6.28989 3.68415 6.95706 2.78511 7.97655 2.10545C9.00229 1.42162 10.325 1 12 1C13.6953 1 14.9977 1.42162 16.0235 2.10545C17.0429 2.78511 17.7101 3.68415 18.1444 4.55278C18.5768 5.41749 18.788 6.27011 18.8926 6.8981C18.9454 7.21447 18.9721 7.4803 18.9857 7.67094C18.9926 7.76644 18.9961 7.84357 18.998 7.89931C18.9991 7.93286 18.9997 7.96641 19 7.99998C19.0144 10.7689 19.0003 17.7181 19 18.001C19 18.0268 18.9993 18.0525 18.9985 18.0782C18.9971 18.1193 18.9945 18.175 18.9896 18.2431C18.9799 18.3791 18.961 18.5668 18.9239 18.7894C18.8505 19.2299 18.7018 19.8325 18.3944 20.4472C18.0851 21.0658 17.6054 21.7149 16.8672 22.207C16.1227 22.7034 15.175 23 14 23C12.825 23 11.8773 22.7034 11.1328 22.207C10.3946 21.7149 9.91489 21.0658 9.60557 20.4472C9.29822 19.8325 9.14952 19.2299 9.07611 18.7894C9.039 18.5668 9.02007 18.3791 9.01035 18.2431C9.00549 18.175 9.0029 18.1193 9.00153 18.0782C9.00069 18.0529 9.00008 18.0275 9 18.0022C8.99621 15.0044 9 12.0067 9 9.00902C9.00101 8.95723 9.00276 8.89451 9.00645 8.84282C9.01225 8.76155 9.02338 8.65197 9.04486 8.5231C9.08702 8.27011 9.17322 7.91749 9.35558 7.55278C9.53989 7.18415 9.83207 6.78511 10.2891 6.48045C10.7523 6.17162 11.325 6 12 6C12.675 6 13.2477 6.17162 13.7109 6.48045C14.1679 6.78511 14.4601 7.18415 14.6444 7.55278C14.8268 7.91749 14.913 8.27011 14.9551 8.5231C14.9766 8.65197 14.9877 8.76155 14.9936 8.84282C14.9984 8.91124 14.9999 8.95358 15 8.99794L15 17C15 17.5523 14.5523 18 14 18C13.4477 18 13 17.5523 13 17V9.00902C12.9995 8.99543 12.9962 8.93484 12.9824 8.8519C12.962 8.72989 12.9232 8.58251 12.8556 8.44722C12.7899 8.31585 12.7071 8.21489 12.6015 8.14455C12.5023 8.07838 12.325 8 12 8C11.675 8 11.4977 8.07838 11.3985 8.14455C11.2929 8.21489 11.2101 8.31585 11.1444 8.44722C11.0768 8.58251 11.038 8.72989 11.0176 8.8519C11.0038 8.93484 11.0005 8.99543 11 9.00902V17.9957C11.0009 18.0307 11.0028 18.0657 11.0053 18.1006C11.0112 18.1834 11.0235 18.3082 11.0489 18.4606C11.1005 18.7701 11.2018 19.1675 11.3944 19.5528C11.5851 19.9342 11.8554 20.2851 12.2422 20.543C12.6227 20.7966 13.175 21 14 21C14.825 21 15.3773 20.7966 15.7578 20.543C16.1446 20.2851 16.4149 19.9342 16.6056 19.5528C16.7982 19.1675 16.8995 18.7701 16.9511 18.4606C16.9765 18.3082 16.9888 18.1834 16.9947 18.1006C16.9972 18.0657 16.9991 18.0307 17 17.9956L16.9999 7.99892C16.9997 7.98148 16.9982 7.91625 16.9908 7.81343C16.981 7.67595 16.9609 7.47303 16.9199 7.2269C16.837 6.72989 16.6732 6.08251 16.3556 5.44722C16.0399 4.81585 15.5821 4.21489 14.9141 3.76955C14.2523 3.32838 13.325 3 12 3C10.675 3 9.7477 3.32838 9.08595 3.76955C8.41793 4.21489 7.96011 4.81585 7.64443 5.44722C7.32678 6.08251 7.16298 6.72989 7.08014 7.2269C7.03912 7.47303 7.019 7.67595 7.00918 7.81343C7.0025 7.90687 7.00117 7.9571 7 8.00092Z"
                                                fill="#0F0F0F"></path>
                                        </g>
                                    </svg>
                                </div>
                                <button x-bind:disabled="!msg.trim()" class="col-span-2 btn-submit ml-8"
                                    type='submit'>Send</button>

                                    <div class="hidden send-record col-span-2 ml-8"
                                   >
                                   <svg style="width: 20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11.5003 12H5.41872M5.24634 12.7972L4.24158 15.7986C3.69128 17.4424 3.41613 18.2643 3.61359 18.7704C3.78506 19.21 4.15335 19.5432 4.6078 19.6701C5.13111 19.8161 5.92151 19.4604 7.50231 18.7491L17.6367 14.1886C19.1797 13.4942 19.9512 13.1471 20.1896 12.6648C20.3968 12.2458 20.3968 11.7541 20.1896 11.3351C19.9512 10.8529 19.1797 10.5057 17.6367 9.81135L7.48483 5.24303C5.90879 4.53382 5.12078 4.17921 4.59799 4.32468C4.14397 4.45101 3.77572 4.78336 3.60365 5.22209C3.40551 5.72728 3.67772 6.54741 4.22215 8.18767L5.24829 11.2793C5.34179 11.561 5.38855 11.7019 5.407 11.8459C5.42338 11.9738 5.42321 12.1032 5.40651 12.231C5.38768 12.375 5.34057 12.5157 5.24634 12.7972Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                    </div>
                                </div>

                    </div>
                    <input id="inpFile" wire:model="files" style="display: none" type="file" multiple>
                    <input class="inp-audio" id="inpFile" wire:model="audio" style="display: none" type="file" multiple>
                </form>

                @error('body')
                    <p> {{ $message }} </p>
                @enderror

                <input id="id_user" value="{{ auth()->user()->id }}" type="hidden">
                <input id="id_receiver" value="{{  $selectedConversation->getReceiver()->id}}" type="hidden">
                <input id="id_conversation" value="{{  $selectedConversation->id}}" type="hidden">
            </div>


            {{-- <button style="display: none" class="btn-load" wire:click="loadMore">Hih</button> --}}

            {{-- <button wire:click="sendMessage">Send API</button> --}}

        </footer>

    </div>
</div>
@vite('resources/js/chat-box.js')



<script type="module">
    var userId = {{ auth()->user()->id }};
    //   alert(userId);
    Echo.private(`users.${userId}`)
        .listen('.message-sent', (e) => {
            // alert('nghe thanh cong');
            console.log(e);
        });
</script>

