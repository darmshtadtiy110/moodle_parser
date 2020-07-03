<?php


namespace Resources;

use General\Resource;

use Interfaces\ParentResource;

use Traits\ParentUtilities;

abstract class Attempt extends Resource implements ParentResource
{
	use ParentUtilities;
}