@php
$messages = App\Models\Chat::orderBy('created_at', 'asc')->get();
@endphp
<div wire:poll>
    <div class="container">
        <div class="messaging">
            <div class="inbox_msg">
                <div class="mesgs">
                    <div id="chat" class="msg_history">
                        @forelse ($messages as $message)
                            @if (App\Models\User::find($message->userid)->name == auth()->user()->name)
                                <!-- Reciever Message-->
                                <div class="outgoing_msg">
                                    <div class="sent_msg bg-primary-500">
                                        <p>
                                            {{ $message->message }}
                                            <span class="time_date" style="color: #fff;">{{ $message->created_at->diffForHumans(null, false, false) }}</span>
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="incoming_msg">
                                    {{ App\Models\User::find($message->userid)->name }}
                                    <br />
                                    <div class="received_msg">
                                        <div class="received_withd_msg">
                                            <p>
                                                {{ $message->message }}
                                                <span class="time_date">
                                                    {{ $message->created_at->diffForHumans(null, false, false) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <h5 style="text-align: center;color:red">Aucun message précédent</h5>
                        @endforelse
                    </div>
                    <div style="margin-top: 30px;">
                        <form wire:submit.prevent="submit">
                            {{ $this->form }}

                            @if((new \Jenssegers\Agent\Agent())->isMobile())
                                <button type="submit" class="inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
                                    Envoyer
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">

img{ max-width:100%;}
.incoming_msg_img {
  display: inline-block;
  width: 6%;
}

.incoming_msg {
    margin-top: 15px;
}

.inbox_msg {
  clear: both;
  overflow: hidden;
}

.received_msg {
  display: inline-block;
  padding: 0 0 0 0px;
  vertical-align: top;
  width: 92%;
 }
 .received_withd_msg p {
  background: #ebebeb none repeat scroll 0 0;
  border-radius: 3px;
  color: #646464;
  font-size: 14px;
  margin: 0;
  padding: 5px 10px 5px 12px;
  width: 100%;
}
.time_date {
  color: #747474;
  display: block;
  font-size: 12px;
  margin: 8px 0 0;
}
.received_withd_msg { width: 57%;}
.mesgs {
  float: left;
  padding: 30px 15px 0 25px;
  width: 100%;
}

 .sent_msg p {
  /*background: #05728f none repeat scroll 0 0;*/
  border-radius: 3px;
  font-size: 14px;
  margin: 0; color:#fff;
  padding: 5px 10px 5px 12px;
  width:100%;
}

.chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
.chat_ib h5 span{ font-size:13px; float:right;}
.chat_ib p{ font-size:14px; color:#989898; margin:auto}
.chat_img {
  float: left;
  width: 11%;
}
.chat_ib {
  float: left;
  padding: 0 0 0 15px;
  width: 88%;
}
.outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
.sent_msg {
  float: right;
  width: 46%;
}

.messaging { padding: 0 0 50px 0;}

.msg_history {
  height: 516px;
  width: 100%;
  overflow-y: auto;
}
</style>
    <script>
    function scrollDown() {
        document.getElementById('chat').scrollTop =  document.getElementById('chat').scrollHeight
    }
    setInterval(scrollDown, 1000);
</script>