<div class="modal fade" id="importLPB" tabindex="-1" aria-labelledby="importLPB" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('lpb.import') }}" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import LPB</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('post')
                    <div class="row">
                        <label for="file" class="col-sm-2 col-form-label">Excel</label>
                        <div class="col-sm-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file" id="file">
                                <label class="custom-file-label" for="file">Choose file</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
