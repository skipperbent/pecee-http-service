<?php

namespace Pecee\Http\Rest;

interface IRestEventListener {

	public function onCreateCollection();

	public function onCreateItem();

}