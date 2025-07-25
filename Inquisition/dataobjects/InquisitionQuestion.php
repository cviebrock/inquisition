<?php

/**
 * An inquisition question
 *
 * @package   Inquisition
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestion extends SwatDBDataObject
{


	const TYPE_RADIO_LIST = 1;
	const TYPE_FLYDOWN = 2;
	const TYPE_RADIO_ENTRY = 3;
	const TYPE_TEXT = 4;
	const TYPE_CHECKBOX_LIST = 5;
	const TYPE_CHECKBOX_ENTRY = 6;




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
	public $question_type;

	/**
	 * @var integer
	 */
	public $displayorder;

	/**
	 * @var boolean
	 */
	public $required;

	/**
	 * @var boolean
	 */
	public $enabled;

	/**
	 * Internal reference to the inquisition this question was loaded for. Not
	 * for saving.
	 *
	 * @var integer
	 */
	public $inquisition;




	public function getView(InquisitionInquisitionQuestionBinding $binding)
	{
		switch ($this->question_type) {
		default:
		case self::TYPE_RADIO_LIST:
			$view = new InquisitionRadioListQuestionView($binding);
			break;
		case self::TYPE_FLYDOWN:
			$view = new InquisitionFlydownQuestionView($binding);
			break;
		case self::TYPE_RADIO_ENTRY:
			$view = new InquisitionRadioEntryQuestionView($binding);
			break;
		case self::TYPE_TEXT:
			$view = new InquisitionTextQuestionView($binding);
			break;
		case self::TYPE_CHECKBOX_LIST:
			$view = new InquisitionCheckboxListQuestionView($binding);
			break;
		case self::TYPE_CHECKBOX_ENTRY:
			$view = new InquisitionCheckboxEntryQuestionView($binding);
			break;
		}

		return $view;
	}




	protected function init()
	{
		$this->table = 'InquisitionQuestion';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'correct_option',
			SwatDBClassMap::get('InquisitionQuestionOption')
		);

		$this->registerInternalProperty(
			'question_group',
			SwatDBClassMap::get('InquisitionQuestionGroup')
		);
	}




	protected function getSerializableSubDataObjects()
	{
		return array_merge(
			parent::getSerializableSubDataObjects(),
			array('options', 'correct_option')
		);
	}



	// loader methods


	protected function loadOptions()
	{
		$sql = sprintf(
			'select * from InquisitionQuestionOption
			where question = %s
			order by displayorder',
			$this->db->quote($this->id, 'integer')
		);

		$wrapper = SwatDBClassMap::get('InquisitionQuestionOptionWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}




	protected function loadHints()
	{
		$sql = sprintf(
			'select * from InquisitionQuestionHint
			where question = %s
			order by displayorder',
			$this->db->quote($this->id, 'integer')
		);

		$wrapper = SwatDBClassMap::get('InquisitionQuestionHintWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}




	protected function loadImages()
	{
		$sql = sprintf(
			'select * from Image
			inner join InquisitionQuestionImageBinding
				on InquisitionQuestionImageBinding.image = Image.id
			where InquisitionQuestionImageBinding.question = %s
			order by InquisitionQuestionImageBinding.displayorder,
				InquisitionQuestionImageBinding.image',
			$this->db->quote($this->id, 'integer')
		);

		$wrapper = SwatDBClassMap::get('InquisitionQuestionImageWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}



	// saver methods


	protected function saveOptions()
	{
		foreach ($this->options as $option) {
			$option->question = $this;
		}

		$this->options->setDatabase($this->db);
		$this->options->save();
	}


}

?>
