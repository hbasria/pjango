<?php
namespace Pjango\Phorm\Fields;


/**
 * OptionsField
 *
 * A selection of choices represented as a series of labeled checkboxes.
 * @author Jeff Ober
 * @package Fields
 **/
class OptionsField extends MultipleChoiceField
{
    /**
     * Returns a new OptionGroupWidget.
     * @author Jeff Ober
     * @return OptionGroupWidget
     **/
    public function get_widget()
    {
        return new OptionGroupWidget($this->choices);
    }
}