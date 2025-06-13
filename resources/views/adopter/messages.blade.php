@extends('layouts.adopter-messages')


@section('title', 'Application Status - PawMatch')

@section('adopter-content')
<div class="main-container">
    <!-- Conversations List -->
    <div class="conversations">
        <div class="conversation active">
            <div class="conversation-header">
                <span class="conversation-name">Strays Worth Saving</span>
                <span class="conversation-time">2:30 PM</span>
            </div>
            <div class="conversation-preview">
                Thank you for your interest in Ester! We'd be happy to...
            </div>
        </div>

        <div class="conversation">
            <div class="conversation-header">
                <span class="conversation-name">Lara's Ark
                </span>
                <span class="conversation-time">Yesterday</span>
            </div>
            <div class="conversation-preview">
                Fort is still available for adoption. Would you like to...
            </div>
        </div>

        <div class="conversation">
            <div class="conversation-header">
                <span class="conversation-name">Biyaya Animal Welfare</span>
                <span class="conversation-time">2 days ago</span>
            </div>
            <div class="conversation-preview">
                We received your application and would like to schedule...
            </div>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="chat-area">
        <div class="chat-header">
            <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t39.30808-6/347439792_262689872915779_1734511534281161924_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEx3xFl7u5jfTV_5eckNDfEgqlXj1z3avOCqVePXPdq89whJ29W46pl6MVM84KD1wjFepXD-UaW6DDSW4eQHod7&_nc_ohc=m_7I_NE9-K0Q7kNvgFi0lNg&_nc_oc=AdiRt7GPOP7QJ-gxFl1lG4A2UBe1eZ6L8UajEeeXX8PUb4BGMftVOv8-jx1oI9sk0LA&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&_nc_gid=ApMVLbMVp0Xh_QdDjSWwWuS&oh=00_AYHRwxYGUVlma7qO1-YvO5im2ZUUEf-Y_wPUtUTpjQBrEg&oe=67D60523" alt="Profile Image" class="profile-image" style="width: 40px; height: 40px; border-radius: 50%;" />
            <div class="chat-name">Strays Worth Saving</div>
        </div>

        <div class="chat-messages">
            <div class="message received">
                <div class="message-content">
                    Hi! Thank you for your interest in adopting Ester. He's a wonderful Tabby Cat with a gentle temperament.
                </div>
                <div class="message-time">2:30 PM</div>
            </div>

            <div class="message sent">
                <div class="message-content">
                    Thanks for getting back to me! I'd love to learn more about Ester. Is he good with children?
                </div>
                <div class="message-time">2:31 PM</div>
            </div>

            <div class="message received">
                <div class="message-content">
                    Yes, Ester is great with children! He's very patient and gentle. He's been living in a foster home with kids ages 5 and 8.
                </div>
                <div class="message-time">2:33 PM</div>
            </div>

            <div class="message sent">
                <div class="message-content">
                    That's perfect! Would it be possible to schedule a visit to meet him?
                </div>
                <div class="message-time">2:34 PM</div>
            </div>
        </div>

        <div class="chat-input">
            <textarea class="message-input" placeholder="Type your message..."></textarea>
            <button class="send-btn">Send</button>
        </div>
    </div>
</div>
@endsection