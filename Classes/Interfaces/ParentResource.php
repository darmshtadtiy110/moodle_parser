<?php


namespace Interfaces;


use General\Resource;

interface ParentResource
{
	public function setChild(Resource $resource);

	public function getChild($child_type, $id);
}