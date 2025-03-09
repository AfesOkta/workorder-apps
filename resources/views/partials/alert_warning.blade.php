@if (isset($errors) && $errors->any())
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning" role="alert">
            {{$data}}
        </div>
    </div>
</div>
@endif
<script>
