<?php

/**
 * Inquisition index.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisitionIndex extends AdminIndex
{
    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->ui->loadFromXML($this->getUiXml());

        $view = $this->ui->getWidget('inquisition_view');
        $view->setDefaultOrderbyColumn(
            $view->getColumn('title'),
            SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING
        );
    }

    protected function getUiXml()
    {
        return __DIR__ . '/index.xml';
    }

    // build phase

    protected function getTableModel(SwatView $view): ?SwatTableModel
    {
        switch ($view->id) {
            case 'inquisition_view':
                return $this->getInquisitionTableModel($view);
        }

        return null;
    }

    protected function getInquisitionTableModel(SwatView $view)
    {
        $sql = sprintf(
            'select * from Inquisition
			order by %s',
            $this->getOrderByClause($view, 'title asc')
        );

        $wrapper = SwatDBClassMap::get('InquisitionInquisitionWrapper');
        $inquisitions = SwatDB::query($this->app->db, $sql, $wrapper);

        // efficiently load the question bindings
        $inquisitions->loadAllSubRecordsets(
            'question_bindings',
            SwatDBClassMap::get('InquisitionInquisitionQuestionBindingWrapper'),
            'InquisitionInquisitionQuestionBinding',
            'inquisition',
            '',
            'inquisition, displayorder, question'
        );

        $locale = SwatI18NLocale::get();
        $store = new SwatTableStore();

        foreach ($inquisitions as $inquisition) {
            $ds = new SwatDetailsStore($inquisition);
            $question_count = count($inquisition->question_bindings);
            $visible_question_count = count(
                $inquisition->visible_question_bindings
            );

            if ($visible_question_count != $question_count) {
                $ds->question_count = sprintf(
                    Inquisition::ngettext(
                        '%s question, %s shown on site',
                        '%s questions, %s shown on site',
                        $question_count
                    ),
                    $locale->formatNumber($question_count),
                    $locale->formatNumber($visible_question_count)
                );
            } else {
                $ds->question_count = sprintf(
                    Inquisition::ngettext(
                        '%s question',
                        '%s questions',
                        $question_count
                    ),
                    $locale->formatNumber($question_count)
                );
            }

            $store->add($ds);
        }

        return $store;
    }
}
