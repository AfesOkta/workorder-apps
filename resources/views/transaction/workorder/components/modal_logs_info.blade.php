<div class="modal fade" id="modal_logs" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customermodalTitle">Logs Process Update Work Order</h5>
                <div class="loader" id="loaderProcess"></div>
            </div>
            <div class="modal-body">
                <label for="email_address_2">Nomer Work Order</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="no_work_orders" name="no_work_orders" class="form-control" readonly/>
                    </div>
                </div>


                <label for="uraian_input">Nama Produk</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="product_name" name="product_name" class="form-control" readonly />
                    </div>
                </div>

                <label for="uraian_input">Quantity Orders</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="quantity" name="quantity" class="form-control" readonly />
                    </div>
                </div>
                <hr />
                <table class="table table-bordered table-stripped dataTable rincian_updated" id="rincian_updated"
                    width="100%">
                    <thead>
                        <tr>
                            <th style="min-width: 30%">Status</th>
                            <th style="width: 30%">Quantity Updated</th>
                            <th style="width: 80%">Uraian</th>
                        </tr>
                    </thead>
                    <tbody id="logs_table_body">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button id="close" type="button" class="btn btn-outline-danger pull-right" data-dismiss="modal">Close
                </button>
                <input type="hidden" name="work_order_is" id="work_order_is">
            </div>
        </div>
    </div>
</div>
