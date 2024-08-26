
var container = document.querySelector('.main-chat');
var inp_msg = document.querySelector('.inp-text');
var oldSize = container.scrollHeight;

function scrollToBottom() {
    setTimeout(() => {
        container.scrollTop = container.scrollHeight;
    }, 200);
}
function formatTime() {
    const now = new Date();
    const options = {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true, // Use 12-hour clock
        timeZone: 'Asia/Ho_Chi_Minh', // Vietnam timezone
    };
    const formatter = new Intl.DateTimeFormat('en-US', options);
    return formatter.format(now);
}
document.addEventListener('content-changed', () => {
    console.log('changed');
    inp_msg.value = '';
    scrollToBottom();
});
document.addEventListener('read-msg', () => {
    console.log(container.scrollHeight);
    var time = formatTime();
    console.log(time);
    container.innerHtml += `<div class="flex justify-end">
                        <span class="fs-sm #ccc">
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
                            Seen at: ${time}
                        </span>
                    </div>`;
    console.log(document.querySelector('.main-chat').scrollHeight);
    document.querySelector('.main-chat').scrollIntoView({ behavior: 'smooth', block: 'end' });
});
scrollToBottom();
var btnLoad = document.querySelector('.btn-load');
container.addEventListener('scroll', () => {
    if (container.scrollTop == 0) {
        btnLoad.click();
        // Livewire.emit('callLoadMore');
    }
})

document.addEventListener('load-more', () => {
    setTimeout(() => { }, 20);
    newHeight = container.scrollHeight;
    container.scrollTop = newHeight - oldSize;
    oldSize = newHeight;
});

var clickFile = document.querySelector('.send-file');
clickFile.addEventListener('click', () => {
    // console.log('alo');
    // document.getElementById('inpFile').value = "";
    document.getElementById('inpFile').click();
})



var startRecord = document.querySelector('.start-record');
var stopRecord = document.querySelector('.stop-record');

let mediaRecorder;
let audioChunks = [];
let audioPlay = document.querySelector('#audioPlayback');
let sendRecord = document.querySelector('.send-record');

startRecord.addEventListener('click', () => {
    document.querySelector('.stop-record').classList.remove('hidden');
    document.querySelector('.start-record').classList.add('hidden');
    document.querySelector('.inp-text').classList.add('hidden');
    document.querySelector('.record').classList.remove('hidden');
    document.querySelector('.record').classList.add('flex');
    document.querySelector('.btn-submit').classList.add('hidden')
    sendRecord.classList.remove('hidden');

    navigator.mediaDevices.getUserMedia({ audio: true })
    .then(stream => {
        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = (event) => {
            audioChunks.push(event.data);
        };
    
        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            const audioUrl = URL.createObjectURL(audioBlob);

            console.log(audioChunks);
            audioPlay.src = audioUrl;
            audioPlay.play();

            
            // audioChunks = [];
            // Preview or use audioBlob here if needed
        };
    
        mediaRecorder.start();
    })
    .catch(error => {
        // Handle the error here
        console.error('Error accessing media devices.', error);
    });


})

stopRecord.addEventListener('click',() => {
    mediaRecorder.stop();

})

sendRecord.addEventListener('click',() => {
    console.log('send')
    const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });

    const audioFile = new File([audioBlob], 'recording.mp3', { type: 'audio/mpeg' });

    sendAudioToLivewire(audioFile);
})


function sendAudioToLivewire(audioBlob) {

    const formData = new FormData();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var id_user = document.querySelector('#id_user').value;
    var id_receiver = document.querySelector('#id_receiver').value;
    var id_conversation = document.querySelector('#id_conversation').value;

    formData.append('audio', audioBlob, 'recording.wav');
    formData.append('_token', csrfToken); 
    formData.append('id_user',id_user);
    formData.append('id_receiver',id_receiver);
    formData.append('id_conversation',id_conversation);

    console.log(id_user,id_receiver,id_conversation)
    

    fetch('/livewire/audio-upload', {

        method: 'POST',

        body: formData,

        headers: {
            'Accept': 'application/json',
        },

    }).then(response => response.json()).then(data => {

        console.log('Success:', data);

    }).catch(error => {

        console.error('Error:', error);

    });

}