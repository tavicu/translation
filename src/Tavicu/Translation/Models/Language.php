<?php namespace Tavicu\Translation\Models;

use Waavi\Model\WaaviModel;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Language extends WaaviModel {

	 use SoftDeletingTrait;

	/**
	 *  Table name in the database.
	 *  @var string
	 */
	 protected $table = 'languages';

	/**
	 *  List of variables that cannot be mass assigned
	 *  @var array
	 */
	protected $guarded = array('id');

	/**
	 *  Table name in the database.
	 *  @var string
	 */
	 protected $dates = ['deleted_at'];

	/**
	 *  Validation rules
	 *  @var array
	 */
	public $rules = array(
		'locale'  => 'required|unique:languages',
		'name'    => 'required|unique:languages',
	);

	/**
	 *	Each language may have several entries.
	 */
	public function entries()
	{
		return $this->hasMany('Tavicu\Translation\Models\LanguageEntry');
	}

	/**
	 *  Transforms a uri into one containing the current locale slug.
	 *  Examples: login/ => /es/login . / => /es
	 *
	 *  @param string $uri Current uri.
	 *  @return string Target uri.
	 */
	public function uri($uri)
	{
		// Delete the forward slash if any at the beginning of the uri:
		$uri = substr($uri, 0, 1) == '/' ? substr($uri, 1) : $uri;

		$segments = explode('/', $uri);
		$newUri = "/{$this->locale}/{$uri}";

		if (sizeof($segments) && strlen($segments[0]) == 2)
		{
			$newUri = "/{$this->locale}";
			for($i = 1; $i < sizeof($segments); $i++)
			{
				$newUri .= "/{$segments[$i]}";
			}
		}

		return $newUri;
	}

}