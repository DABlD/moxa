<?php

namespace App\Traits;

trait BillingAttribute{
	public function getActionsAttribute(){
		$id = $this->id;

		if($this->status == "Unpaid"){
			return 
			"<a class='btn btn-success' data-toggle='tooltip' title='Input Payment' onClick='pay($id)'>" .
		        "<i class='fas fa-hand-holding-dollar'></i>" .
		    "</a>&nbsp;" .
			"<a class='btn btn-warning' data-toggle='tooltip' title='Send Billing' onClick='sendBilling($id)'>" .
		        "<i class='fas fa-envelope'></i>" .
		    "</a>&nbsp;" .
			"<a class='btn btn-primary' data-toggle='tooltip' title='Generate PDF' onClick='generatePDF($id)'>" .
		        "<i class='fas fa-file-pdf'></i>" .
		    "</a>&nbsp;"
		    ;
		}
		else{
			return 
			"<a class='btn btn-info' data-toggle='tooltip' title='View Payment Details' onClick='viewPayment($id)'>" .
		        "<i class='fas fa-search'></i>" .
		    "</a>&nbsp;";
		}

		return;
		// "<a class='btn btn-info' data-toggle='tooltip' title='Subscriber Information' onClick='userDetails($this->name)'>" .
	    //     "<i class='fas fa-user'></i>" .
	    // "</a>&nbsp;" . 
		// "<a class='btn btn-danger' data-toggle='tooltip' title='Delete' onClick='del($id)'>" .
	    //     "<i class='fas fa-trash'></i>" .
	    // "</a>&nbsp;";
	}
}