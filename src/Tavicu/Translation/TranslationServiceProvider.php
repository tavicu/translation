<?php namespace Tavicu\Translation;

use Illuminate\Translation\TranslationServiceProvider as LaravelTranslationServiceProvider;
use Tavicu\Translation\Facades\Translator;
use Tavicu\Translation\Loaders\FileLoader;
use Tavicu\Translation\Loaders\DatabaseLoader;
use Tavicu\Translation\Loaders\MixedLoader;
use Tavicu\Translation\Providers\LanguageProvider;
use Tavicu\Translation\Providers\LanguageEntryProvider;
use Config;

class TranslationServiceProvider extends LaravelTranslationServiceProvider {

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
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('tavicu/translation', 'tavicu/translation', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');

		$this->registerLoader();
		$this->registerTranslationFileLoader();

		$this->commands('translator.load');

		$this->app['translator'] = $this->app->share(function($app)
		{
			$loader = $app['translation.loader'];

			// When registering the translator component, we'll need to set the default
			// locale as well as the fallback locale. So, we'll grab the application
			// configuration so we can easily get both of these values from there.
			$locale = $app['config']['app.locale'];

			$trans = new Translator($loader, $locale);

			return $trans;
		});
	}

	/**
	 * Register the translation line loader.
	 *
	 * @return void
	 */
	protected function registerLoader()
	{
		$app = $this->app;

		$this->app['translation.loader'] = $this->app->share(function($app)
		{
			$languageProvider 	= new LanguageProvider($app['config']['tavicu/translation::language.model']);
			$langEntryProvider 	= new LanguageEntryProvider($app['config']['tavicu/translation::language_entry.model']);

			$mode = $app['config']['tavicu/translation::mode'];

			if ($mode == 'auto' || empty($mode))
			{
				$mode = ($app['config']['app.debug'] ? 'mixed' : 'database');
			}

			switch ($mode)
			{
				case 'mixed':
					return new MixedLoader($languageProvider, $langEntryProvider, $app);

				default: case 'filesystem':
					return new FileLoader($languageProvider, $langEntryProvider, $app);

				case 'database':
					return new DatabaseLoader($languageProvider, $langEntryProvider, $app);
			}
		});
	}

	/**
	 * Register the translation file loader command.
	 *
	 * @return void
	 */
	public function registerTranslationFileLoader()
	{
		$this->app['translator.load'] = $this->app->share(function($app)
		{
			$languageProvider 	= new LanguageProvider($app['config']['tavicu/translation::language.model']);
			$langEntryProvider 	= new LanguageEntryProvider($app['config']['tavicu/translation::language_entry.model']);
			$fileLoader 				= new FileLoader($languageProvider, $langEntryProvider, $app);

			return new Commands\FileLoaderCommand($languageProvider, $langEntryProvider, $fileLoader);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('translator', 'translation.loader');
	}

}