<?php

namespace Pecee\Http\Rest;

interface IRestEventListener
{

    public function onCreateCollection() : IRestCollection;

    public function onCreateItem() : IRestResult;

}