@if (isset($errors) && $errors->any())
<div class="row">
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div class="row">
                    <label class="float-right">{{ $error }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
