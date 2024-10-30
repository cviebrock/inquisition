<?php

/**
 * Edit page for inquisitions
 *
 * @package   Inquisition
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisitionEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML($this->getUiXml());
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$this->inquisition = new $class;
		$this->inquisition->setDatabase($this->app->db);

		if ($this->id != '') {
			if (!$this->inquisition->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf('Inquisition with id ‘%s’ not found.', $this->id));
			}
		}
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return __DIR__.'/edit.xml';
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData(): void
	{
		$this->updateInquisition();

		if ($this->inquisition->isModified()) {
			$this->inquisition->save();
			$this->app->messages->add($this->getSavedMessage());
		}
	}

	// }}}
	// {{{ protected function updateInquisition()

	protected function updateInquisition()
	{
		$values = $this->ui->getValues(
			array(
				'title',
			)
		);

		$this->inquisition->title = $values['title'];

		if ($this->ui->hasWidget('enabled')) {
			$this->inquisition->enabled =
				$this->ui->getWidget('enabled')->value;
		}

		if ($this->inquisition->id === null) {
			$now = new SwatDate();
			$now->toUTC();
			$this->inquisition->createdate = $now;
		}
	}

	// }}}
	// {{{ protected function getSavedMessage()

	protected function getSavedMessage()
	{
		return new SwatMessage(Inquisition::_('Inquisition has been saved.'));
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Inquisition/Details?id=%s',
				$this->inquisition->id
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues($this->inquisition->getAttributes());
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$last = $this->navbar->popEntry();

		if ($this->id != '') {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		}

		$this->navbar->addEntry($last);
	}

	// }}}
}

?>
