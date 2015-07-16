<?php
namespace Miluo\GetuiSDK;

use Illuminate\Support\ServiceProvider;
use Miluo\MiluoGetui\MiluoGetui;

class GetuiServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->handleConfigs();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('getui.sdk', function ($app) {
			$config = $app->config->get('miluo.getui');
			return new MiluoGetui($config['APPKEY'], $config['APPID'], $config['MASTERSECRET'], $config['HOST'], $config['CID'],$config['DEVICETOKEN'],$config['Alias']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'miluo.getui'
		];
	}

	private function handleConfigs()
	{
		$configPath = __DIR__ . '/../config/miluo-getui.php';

		$this->publishes([
			$configPath => config_path('miluo-getui.php')
		]);

		$this->mergeConfigFrom($configPath, 'miluo-getui');
	}
}
