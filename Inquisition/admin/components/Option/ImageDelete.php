<?php

	'Inquisition/dataobjects/InquisitionQuestionOptionImageWrapper.php';

/**
 * Delete confirmation page for option images
 *
 * @package   Inquisition
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionOptionImageDelete extends InquisitionInquisitionImageDelete
{


	/**
	 * @var InquisitonQuestionOption
	 */
	protected $option;



	// helper methods


	public function setId($id)
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionOption');

		$this->option = new $class_name();
		$this->option->setDatabase($this->app->db);

		if ($id == '') {
			throw new AdminNotFoundException(
				'Option id not provided.'
			);
		}

		if (!$this->option->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Option with id ‘%s’ not found.',
					$id
				)
			);
		}

		parent::setId($id);
	}




	protected function getImageWrapper()
	{
		return SwatDBClassMap::get('InquisitionQuestionOptionImageWrapper');
	}



	// build phase


	protected function buildNavBar()
	{
		AdminDBDelete::buildNavBar();

		$this->navbar->popEntry();

		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		}

		$this->navbar->createEntry(
			$this->getQuestionTitle(),
			sprintf(
				'Question/Details?id=%s%s',
				$this->option->question->id,
				$this->getLinkSuffix()
			)
		);

		if ($this->option instanceof InquisitionQuestionOption) {
			$this->navbar->createEntry(
				$this->getOptionTitle(),
				sprintf(
					'Option/Details?id=%s%s',
					$this->option->id,
					$this->getLinkSuffix()
				)
			);
		}

		$this->navbar->createEntry(
			Inquisition::ngettext(
				'Delete Image',
				'Delete Images',
				count($this->images)
			)
		);
	}




	protected function getQuestionTitle()
	{
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
	}




	protected function getOptionTitle()
	{
		return sprintf(
				Inquisition::_('Option %s'),
				$this->option->position
			);
	}




	protected function getLinkSuffix()
	{
		$suffix = null;
		if ($this->inquisition instanceof InquisitionInquisition) {
			$suffix = sprintf(
				'&inquisition=%s',
				$this->inquisition->id
			);
		}

		return $suffix;
	}


}

?>
