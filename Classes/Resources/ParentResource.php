<?php


namespace Resources;


use General\Tools;

trait ParentResource
{
	public function setChild(Resource $resource)
	{
		$resource_class = Tools::get_object_class_name($resource);

		//$list_name = strtolower($resource_class)."_list";


		$list_name = Tools::camel_to_snake($resource_class)."_list";

		if( !array_key_exists($resource->id, $this->$list_name) )
			$this->$list_name[$resource->id] = $resource;
	}

	/**
	 * @param $child_type
	 * Type of Resource: Course | Quiz | Attempt | etc.
	 * @param $id
	 * @return mixed | bool
	 */
	public function getChild($child_type, $id)
	{
		$parent_name = get_class($this);
		$list_name = strtolower($child_type)."_list";
		$parent_vars = get_class_vars($parent_name);

		if(
			array_key_exists($list_name, $parent_vars) &
			is_array($this->$list_name) &
			array_key_exists($id, $this->$list_name)
		)
			return $this->$list_name[$id];

		return false;
	}
}