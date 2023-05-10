@csrf

<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">


        <div class="col-12s">
            <div class="row">
            @if($errors != null)
                @foreach($errors AS $error)
                    <div class="col-md-6" role="alert">
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    </div>
                @endforeach
            @endif
    
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-4">
                <label for="">Username</label>
                <input type="text" class="form-control" name="username" readonly id="username" value="{{ $user->username }}">
            </div>
            <div class="form-group col-md-4">
                <label for="">Current Expiration</label>
                <input type="date" class="form-control" name="expiration" id="username" value="{{ date('Y-m-d', strtotime($user->expiration)) }}" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="">new Expiration</label>
                <input type="date" class="form-control" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+30 days')) }}" name="new_expiration" id="username" value="{{ ($user->new_expiration != null) ? date('Y-m-d', strtotime($user->new_expiration)) : '' }}">
            </div>
        </div>
        <input type="hidden" name="user_id" id="user_id" value="{{ $user->hashid }}">

    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary">
        {{-- <button type="button" class="btn btn-primary">Send message</button> --}}
    </div>
</div>