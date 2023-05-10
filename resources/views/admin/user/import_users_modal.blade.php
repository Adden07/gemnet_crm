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
            <div class="form-group col-md-6">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $user->name }}">
            </div>
            <div class="form-group col-md-6">
                <label for="">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="{{ $user->username }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Password</label>
                <input type="text" class="form-control" name="password" id="password" value="{{ $user->password }}">
            </div>
            <div class="form-group col-md-6">
                <label for="">Nic</label>
                <input type="text" class="form-control" name="nic" id="nic" value="{{ $user->nic }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Mobile</label>
                <input type="text" class="form-control" name="mobile" id="mobile" value="{{ $user->mobile }}">
            </div>
            <div class="form-group col-md-6">
                <label for="">Address</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $user->address }}">
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