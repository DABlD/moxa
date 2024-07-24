<div class="row">
    
    <div class="col-md-3">
        <div class="row iRow">
            <div class="col-md-4 iLabel" style="margin: auto;">
                Subscriber Filter
            </div>
            <div class="col-md-8 iInput">
                <select id="user_id" name="user_id" class="form-control">
                    <option value="%%">Select Subscriber / All</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="row iRow">
            <div class="col-md-4 iLabel" style="margin: auto;">
                Device Filter
            </div>
            <div class="col-md-8 iInput">
                <select id="moxa_id" name="moxa_id" class="form-control">
                    <option value="%%">Select Device / All</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="row iRow">
            <div class="col-md-4 iLabel" style="margin: auto;">
                Status Filter
            </div>
            <div class="col-md-8 iInput">
                <select id="status" name="status" class="form-control">
                    <option value="%%">Select Status / All</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-3" style="text-align: right;">
        <a class="btn btn-success btn-sm" data-toggle="tooltip" title="Add RHU" onclick="create()">
            <i class="fas fa-plus fa-2xl"></i>
        </a>
    </div>
</div>

<br>