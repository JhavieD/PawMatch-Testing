@extends ('layouts.messages')

@section('title', 'Messages')

@section('shelter-content')
<div class="main-container">
    <!-- Conversations List -->
    <div class="conversations">
        <div class="conversation active">
            <div class="conversation-header">
                <span class="conversation-name">Jan Vincent Dominguez</span>
                <span class="conversation-time">2:30 PM</span>
            </div>
            <div class="conversation-preview">
                Thank you for your interest in Ester! We'd be happy to...
            </div>
        </div>

        <div class="conversation">
            <div class="conversation-header">
                <span class="conversation-name">Vince Joseph Rubio</span>
                <span class="conversation-time">Yesterday</span>
            </div>
            <div class="conversation-preview">
                Hi! Thank you for your interest in adopting Pipoy. ...
            </div>
        </div>

        <div class="conversation">
            <div class="conversation-header">
                <span class="conversation-name">Allainne Villanueva</span>
                <span class="conversation-time">2 days ago</span>
            </div>
            <div class="conversation-preview">
                Hi! Thank you for your interest in adopting Fort. ...
            </div>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="chat-area">
        <div class="chat-header">
            <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/476486323_608751341590637_3882524015070156262_n.png?_nc_cat=111&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAyCH12mIL6NzUvlgseqDY4rL9WdfeKM3isv1Z194ozYgZ-9UKbsk65TCe8Nkb-jswJb96y7c5-4ubO3YgvEiH&_nc_ohc=-1JlxoF7RywQ7kNvgESo7VP&_nc_oc=AdjsVM141uQAKT9LZBlLbPhWPJrXPxdO9KhelOerqjj8beDLoBzB7q5a_JIYRHov3Ig&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wH1X3E_IEqjyyt_Ac_XgN-bhjx9I83wi6IeMjVlQcDh1A&oe=67F7A820" alt="Profile Image" class="profile-image">
            <div class="chat-name">Jan Vincent Dominguez</div>
        </div>

        <div class="chat-messages">
            <div class="message sent">
                <div class="message-content">
                    Hi! Thank you for your interest in adopting Ester. He's a wonderful Tabby Cat with a gentle temperament.
                </div>
                <div class="message-time">2:31 PM</div>
            </div>

            <div class="message received">
                <div class="message-content">
                    Thanks for getting back to me! I'd love to learn more about Ester. Is he good with children?
                </div>
                <div class="message-time">2:30 PM</div>
            </div>

            <div class="message sent">
                <div class="message-content">
                    Yes, Ester is great with children! He's very patient and gentle. He's been living in a foster home with kids ages 5 and 8.
                </div>
                <div class="message-time">2:34 PM</div>
            </div>
            <div class="message received">
                <div class="message-content">
                    That's perfect! Would it be possible to schedule a visit to meet him?
                </div>
                <div class="message-time">2:33 PM</div>
            </div>
        </div>


        <div class="chat-input">
            <textarea class="message-input" placeholder="Type your message..."></textarea>
            <button class="send-btn">Send</button>
        </div>
    </div>
</div>

@endsection