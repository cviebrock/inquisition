<?php

/**
 * An inquisition question hint
 *
 * @package   Inquisition
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionHint extends SwatDBDataObject
{


	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var text
	 */
	public $bodytext;

	/**
	 * @var integer
	 */
	public $displayorder;




	protected function init()
	{
		$this->table = 'InquisitionQuestionHint';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'question',
			SwatDBClassMap::get('InquisitionQuestion')
		);
	}


}

?>
