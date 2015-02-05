<?php namespace Tavicu\Translation\Loaders;

use Illuminate\Translation\LoaderInterface;
use Tavicu\Translation\Loaders\Loader;
use Tavicu\Translation\Providers\LanguageProvider as LanguageProvider;
use Tavicu\Translation\Providers\LanguageEntryProvider as LanguageEntryProvider;

class MixedLoader extends Loader implements LoaderInterface {

	/**
	 *	The file loader.
	 *	@var \Tavicu\Translation\Loaders\FileLoader
	 */
	protected $fileLoader;

	/**
	 *	The database loader.
	 *	@var \Tavicu\Translation\Loaders\DatabaseLoader
	 */
	protected $databaseLoader;

	/**
	 * 	Create a new mixed loader instance.
	 *
	 * 	@param  \Tavicu\Lang\Providers\LanguageProvider  			$languageProvider
	 * 	@param 	\Tavicu\Lang\Providers\LanguageEntryProvider		$languageEntryProvider
	 *	@param 	\Illuminate\Foundation\Application  				$app
	 */
	public function __construct($languageProvider, $languageEntryProvider, $app)
	{
		parent::__construct($languageProvider, $languageEntryProvider, $app);

		$this->fileLoader     = new FileLoader($languageProvider, $languageEntryProvider, $app);
		$this->databaseLoader = new DatabaseLoader($languageProvider, $languageEntryProvider, $app);
	}

	/**
	 * Load the messages strictly for the given locale.
	 *
	 * @param  Language  	$language
	 * @param  string  		$group
	 * @param  string  		$namespace
	 * @return array
	 */
	public function loadRawLocale($locale, $group, $namespace = null)
	{
		$namespace = $namespace ?: '*';

		return array_merge($this->databaseLoader->loadRawLocale($locale, $group, $namespace), $this->fileLoader->loadRawLocale($locale, $group, $namespace));
	}
}