<div class="row">
    <div class="col-md-3">
        <div class="row iRow">
            <div class="col-md-4 iLabel" style="margin: auto;">
                Building Filter
            </div>
            <div class="col-md-8 iInput">
                <select id="bldg" name="bldg" class="form-control">
                    <option value="%%">Select Building / All</option>
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
                <select id="outlet" name="outlet" class="form-control">
                    <option value="%%">Select Device / All</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-3"></div>
    <div class="col-md-3">

        <div class="row iRow float-right">
            <a class="btn btn-success" data-toggle="tooltip" title="Add Entry" onclick="add()">
                <i class="fas fa-plus"></i>
            </a>&nbsp;
        </div>

    </div>
</div>

<br>

<div class="row">
    <div class="col-md-3" id="from"></div>
    <div class="col-md-3" id="to"></div>
</div>
