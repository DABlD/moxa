<?php

namespace App\Traits;

trait BillingAttribute{
	public function getActionsAttribute(){
		$id = $this->id;
		$uid = $this->user_id;

		return ;
		// "<a class='btn btn-success' data-toggle='tooltip' title='View' onClick='view($id)'>" .
	    //     "<i class='fas fa-search'></i>" .
	    // "</a>&nbsp;" . 
		// "<a class='btn btn-info' data-toggle='tooltip' title='Subscriber Information' onClick='userDetails($this->name)'>" .
	    //     "<i class='fas fa-user'></i>" .
	    // "</a>&nbsp;" . 
		// "<a class='btn btn-danger' data-toggle='tooltip' title='Delete' onClick='del($id)'>" .
	    //     "<i class='fas fa-trash'></i>" .
	    // "</a>&nbsp;";
	}
}