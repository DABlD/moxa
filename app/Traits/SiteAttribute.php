<?php

namespace App\Traits;

trait SiteAttribute{
	public function getActionsAttribute(){
		$id = $this->id;
		
		return 
		"<a class='btn btn-success' data-toggle='tooltip' title='Edit' onClick='view($id)'>" .
	        "<i class='fas fa-pencil'></i>" .
	    "</a>&nbsp;" . 
		"<a class='btn btn-danger' data-toggle='tooltip' title='Delete' onClick='del($id)'>" .
	        "<i class='fas fa-trash'></i>" .
	    "</a>&nbsp;";
	}
}