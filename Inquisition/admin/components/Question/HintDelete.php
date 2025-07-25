<?php

/**
 * Delete confirmation page for question hints
 *
 * @package   Inquisition
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionHintDelete extends AdminDBDelete
{


	/**
	 * @var InquisitonQuestionHintWrapper
	 */
	protected $hints;

	/**
	 * @var InquisitonQuestion
	 */
	protected $question;

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;



	// helper methods


	public function setId($id)
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestion');

		$this->question = new $class_name();
		$this->question->setDatabase($this->app->db);

		if ($id == '') {
			throw new AdminNotFoundException(
				'Question id not provided.'
			);
		}

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Question with id ‘%s’ not found.',
					$id
				)
			);
		}

		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('id', $id);
	}




	public function setItems($items, $extended_selected = false)
	{
		parent::setItems($items, $extended_selected);

		$sql = sprintf(
			'select InquisitionQuestionHint.*
			from InquisitionQuestionHint where id in (%s)',
			$this->getItemList('integer')
		);

		$this->hints = SwatDB::query(
			$this->app->db,
			$sql,
			SwatDBClassMap::get('InquisitionQuestionHintWrapper')
		);
	}




	public function setInquisition(?InquisitionInquisition $inquisition = null)
	{
		if ($inquisition instanceof InquisitionInquisition) {
			$this->inquisition = $inquisition;

			$form = $this->ui->getWidget('confirmation_form');
			$form->addHiddenField('inquisition_id', $this->inquisition->id);
		}
	}



	// init phase


	protected function initInternal()
	{
		parent::initInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$id = $form->getHiddenField('id');
		if ($id != '') {
			$this->setId($id);
		}

		$inquisition_id = $form->getHiddenField('inquisition_id');
		if ($inquisition_id != '') {
			$inquisition = $this->loadInquisition($inquisition_id);
			$this->setInquisition($inquisition);
		}
	}




	protected function loadInquisition($inquisition_id)
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$inquisition = new $class;
		$inquisition->setDatabase($this->app->db);

		if (!$inquisition->load($inquisition_id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition with id ‘%s’ not found.',
					$inquisition_id
				)
			);
		}

		return $inquisition;
	}



	// process phase


	protected function processDBData(): void
	{
		parent::processDBData();

		$sql = sprintf(
			'delete from InquisitionQuestionHint where id in (%s)',
			$this->getItemList('integer')
		);

		$delete_count = SwatDB::exec($this->app->db, $sql);
		$locale = SwatI18NLocale::get();

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					Inquisition::ngettext(
						'One hint has been deleted.',
						'%s hints have been deleted.',
						$delete_count
					),
					$locale->formatNumber($delete_count)
				)
			)
		);
	}




	protected function relocate()
	{
		AdminDBConfirmation::relocate();
	}



	// build phase


	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->setTitle(
			Inquisition::_('hint'),
			Inquisition::_('hints')
		);

		$dep->entries = AdminListDependency::queryEntries(
			$this->app->db,
			'InquisitionQuestionHint', 'id', null, 'text:bodytext',
			'displayorder, id', 'id in ('.$item_list.')',
			AdminDependency::DELETE
		);

		foreach ($dep->entries as $entry) {
			$entry->title = SwatString::condense($entry->title, 50);
		}

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
			$this->switchToCancelButton();
		}
	}




	protected function buildForm()
	{
		parent::buildForm();

		$yes_button = $this->ui->getWidget('yes_button');
		$yes_button->title = Inquisition::_('Delete');
	}




	protected function buildNavBar()
	{
		parent::buildNavBar();

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
				$this->question->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry(
			Inquisition::ngettext(
				'Delete Hint',
				'Delete Hints',
				count($this->hints)
			)
		);
	}




	protected function getQuestionTitle()
	{
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
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
