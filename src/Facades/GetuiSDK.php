<?php
namespace Miluo\MiluoGetui\Facades;

use Illuminate\Support\Facades\Facade;

class GetuiSDK extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'miluo.getui';
	}
}