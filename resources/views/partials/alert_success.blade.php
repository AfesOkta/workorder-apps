@if (session('status'))
<div class="row">
    <div class="col-12">
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    </div>
</div>
@endif
