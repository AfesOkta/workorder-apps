<div class="modal fade" id="pilih_skpd" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Pilih SKPD</h4>
                <br>
                <form autocomplete="false" class="form-group" method="post" action="{{route('master.skpd.synchronize')}}">
                    @csrf
                    <div class="form-group">
                        <label>SKPD</label>
                        <select name="skpd_id" id="skpd_id" class="form-control">
                            <option value="">Pilih SKPD</option>
                            @foreach ($skpd as $item)
                                <option value="{{$item->id}}">{{$item->kd_skpd}} - {{$item->nama_skpd}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nama Bendahara</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="id_bendahara">
                                <option value="">Silahkan pilih bendahara</option>
                                @foreach ($users as $user )
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary float-lg-right" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
