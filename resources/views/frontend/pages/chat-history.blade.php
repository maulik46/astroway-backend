@extends('frontend.layout.master')
@section('content')
    @if (authcheck())
        @php
            $userId = authcheck()['id'];
            $astrologerId = request()->query('astrologerId');

        @endphp
    @endif

    <div id="reviewmodal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm h-100 d-flex align-items-center">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        Review
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="Review">
                        <input type="hidden" name="userId" id="userId" value="{{ $userId }}">
                        <input type="hidden" id="astrologerId" name="astrologerId" value="{{ $astrologerId }}">

                        <div class="text-center">
                            <div class="form-group">
                                <label for="rating">Rating:</label>
                                <div class="star-rating"
                                    data-rating="{{ isset($getUserHistoryReview['recordList'][0]['rating']) ? $getUserHistoryReview['recordList'][0]['rating'] : '' }}">
                                    <input type="radio" id="star5" name="rating" value="5"><label
                                        for="star5"></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label
                                        for="star4"></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label
                                        for="star3"></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label
                                        for="star2"></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label
                                        for="star1"></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="review">Description:</label>
                                <textarea class="form-control" id="review" name="review" rows="3" placeholder="Enter your review">{{ isset($getUserHistoryReview['recordList'][0]['review']) ? $getUserHistoryReview['recordList'][0]['review'] : '' }}</textarea>
                            </div>
                            <button class="btn btn-chat" id="reviewbtn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <main class="content">
        <div class="container p-0">

            <h1 class="h3 mb-3 mt-4 ml-4">Chat</h1>

            <div class="card ">
                <div class="row g-0">

                    <div class="col-12 col-lg-12 col-xl-12">

                        <div class="py-2 px-4 border-bottom d-none d-lg-block">
                            <div class="d-flex align-items-center py-1">
                                <div class="position-relative">
                                    <img src="/{{ $getAstrologer['recordList'][0]['profileImage'] }}"
                                        class="rounded-circle mr-1" alt="Sharon Lessman" width="40" height="40">
                                </div>
                                <div class="flex-grow-1 pl-3">
                                    <strong>{{ $getAstrologer['recordList'][0]['name'] }}</strong>
                                </div>

                                <div id="timerContainer">
                                    <div class="text-muted small">
                                        <button class="btn view-more" data-toggle="modal" data-target="#reviewmodal"
                                            id="endChat">Review</button>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="position-relative">
                            <div class="chat-messages p-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            var existingRating =
                "{{ isset($getUserHistoryReview['recordList'][0]['rating']) ? $getUserHistoryReview['recordList'][0]['rating'] : '' }}";
            $('.star-rating input[type="radio"]').filter(function() {
                return $(this).val() == existingRating;
            }).prop('checked', true);
        });


        var userId = "{{ $userId }}";
        var astrologerId = "{{ $astrologerId }}";


        const firestore = firebase.firestore();


        function fetchAndRenderMessages(receiverId, senderId) {
            const senderChatRef = firestore.collection('chats').doc(`${receiverId}_${senderId}`).collection('userschat')
                .doc(receiverId).collection('messages');

            senderChatRef.orderBy('createdAt', 'asc').onSnapshot(snapshot => {
                snapshot.docChanges().forEach(change => {
                    if (change.type === 'added') {
                        const message = change.doc.data();
                        renderMessage(message, receiverId);
                    }
                });
            });
        }



        function renderMessage(message, receiverId) {
            const chatMessagesContainer = document.querySelector('.chat-messages');
            const isScrolledToBottom = chatMessagesContainer.scrollHeight - chatMessagesContainer.clientHeight <=
                chatMessagesContainer.scrollTop + 1;

            const messageElement = document.createElement('div');
            messageElement.classList.add('chat-message');

            const timestamp = message.createdAt.toDate();
            const formattedTime = timestamp.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit'
            });

            if (message.isEndMessage == true) {
                messageElement.innerHTML = `
            <div class="chat-message chat-message-center d-flex m-3" style="justify-content: center;">
                <div class="color-red bg-pink rounded-pill border py-1 px-3 mr-3 mb-2 text-center" style="width: 60%;">
                    ${message.message}
                </div>
            </div>`;
            } else if (message.userId1 === receiverId) {
                // Message sent by the receiver, render on the left side
                messageElement.classList.add('chat-message-left');
                messageElement.innerHTML = `
        <div>
            <img src="/{{ $getAstrologer['recordList'][0]['profileImage'] }}" class="rounded-circle mr-1" alt="Sender" width="40" height="40">
            <div class="text-muted small text-nowrap mt-2">${formattedTime}</div>
        </div>
        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3 mb-2">
            <div class="font-weight-bold mb-1">{{ $getAstrologer['recordList'][0]['name'] }}</div>
            ${message.attachementPath ? renderAttachment(message.attachementPath) : `<p>${message.message}</p>`}
        </div>`;

            } else {
                // Message sent by the sender, render on the right side
                messageElement.classList.add('chat-message-right');
                messageElement.innerHTML = `
            <div>
                <img src="/{{ authcheck()['profile'] }}" class="rounded-circle mr-1" alt="You" width="40" height="40">
                <div class="text-muted small text-nowrap mt-2">${formattedTime}</div>
            </div>
            <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 mb-2">
                <div class="font-weight-bold mb-1">You</div>
                ${message.attachementPath ? renderAttachment(message.attachementPath) : `<p>${message.message}</p>`}
            </div>`;
            }

            chatMessagesContainer.appendChild(messageElement);

            if (isScrolledToBottom) {
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
            }
        }


        // Function to render attachment based on its type
        function renderAttachment(attachementPath) {
            if (!attachementPath) return ''; // No attachment provided

            return `<img src="${attachementPath}" style="max-height:250px;" alt="Attachment" class="img-fluid">`;


        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchAndRenderMessages(astrologerId, userId);
        });
    </script>

    <script>
        $('#reviewbtn').click(function(e) {
            e.preventDefault();

            @php
                use Symfony\Component\HttpFoundation\Session\Session;
                $session = new Session();

                $token = $session->get('token');
            @endphp

            var formData = $('#Review').serialize();
            // console.log(formData);

            $.ajax({
                url: "{{ route('api.addUserReview', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Review Added Successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });
    </script>
@endsection
