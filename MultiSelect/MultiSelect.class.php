<?php
/**
 * Database MultiSelect Widget
 *
 * @version    5.0
 * @package    widget
 * @subpackage wrapper
 * @author     Antonio Carlos da Rosa
 */
class MultiSelect extends TSelect
{
    protected $items; // array containing the combobox options
    protected $options;
    
    /**
     * Class Constructor
     * @param  $name     widget's name
     * @param  $database database name
     * @param  $model    model class name
     * @param  $key      table field to be used as key in the combo
     * @param  $value    table field to be listed in the combo
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name);
        
        $key   = trim($key);
        $value = trim($value);
        
        if (empty($database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }
        
        if (empty($model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
        }
        
        if (empty($key))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'key', __CLASS__));
        }
        
        if (empty($value))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'value', __CLASS__));
        }
        
        TTransaction::open($database);
        
        // creates repository
        $repository = new TRepository($model);
        if (is_null($criteria))
        {
            $criteria = new TCriteria;
        }
        $criteria->setProperty('order', isset($ordercolumn) ? $ordercolumn : $key);
        
        // load all objects
        $collection = $repository->load($criteria, FALSE);
        
        // add objects to the options
        if ($collection)
        {
            $items = array();
            foreach ($collection as $object)
            {
                if (isset($object->$value))
                {
                    $items[$object->$key] = $object->$value;
                }
                else
                {
                    $items[$object->$key] = $object->render($value);
                }
            }
            
            if (strpos($value, '{') !== FALSE AND is_null($ordercolumn))
            {
                asort($items);
            }
            parent::addItems($items);
        }
        TTransaction::close();
        
        $this->options = ['columns'=>2,'search'=>true,'texts'=>['placeholder'=>'Selecione','search'=>'Busca']];
        parent::setDefaultOption(false);
    }
    
    public function enableSearch()
    {
        $this->options['search'] = true;
    }
    
    public function disableSearch()
    {
        $this->options['search'] = false;
    }
    
    public function setColumns($cols)
    {
        $this->options['columns'] = $cols;
    }
    
    /**
     * Set extra datepicker options (ex: autoclose, startDate, daysOfWeekDisabled, datesDisabled)
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }
    
    public static function reload($formname, $name, $items, $startEmpty = FALSE)
    {
        parent::reload($formname, $name, $items);
        
        TScript::create("$('form[name={$formname}] [name={$name}\\\[\\\]]').multiselect('reload')");
    }
    
    public static function refresh($formname, $name)
    {
        TScript::create("$('form[name={$formname}] [name={$name}\\\[\\\]]').multiselect('reload')");
    }
    
    /**
     * Reload combo from model data
     * @param  $formname    form name
     * @param  $field       field name
     * @param  $database    database name
     * @param  $model       model class name
     * @param  $key         table field to be used as key in the combo
     * @param  $value       table field to be listed in the combo
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria    criteria (TCriteria object) to filter the model (optional)
     * @param  $startEmpty  if the combo will have an empty first item
     */
    public static function reloadFromModel($formname, $field, $database, $model, $key, $value, $ordercolumn = NULL, $criteria = NULL, $startEmpty = FALSE)
    {
        TTransaction::open($database);
        
        // creates repository
        $repository = new TRepository($model);
        if (is_null($criteria))
        {
            $criteria = new TCriteria;
        }
        $criteria->setProperty('order', isset($ordercolumn) ? $ordercolumn : $key);
        
        // load all objects
        $collection = $repository->load($criteria, FALSE);
        
        $items = array();
        // add objects to the combo
        if ($collection)
        {
            foreach ($collection as $object)
            {
                if (isset($object->$value))
                {
                    $items[$object->$key] = $object->$value;
                }
                else
                {
                    $items[$object->$key] = $object->render($value);
                }
            }
        }
        TTransaction::close();
        
        parent::reload($formname, $field, $items, $startEmpty);
        
        TScript::create("$('form[name={$formname}] [name={$field}\\\[\\\]]').multiselect('reload')");
    }
    
    public function show()
    {
        parent::show();
        
        $options_json = json_encode($this->options);
        
        TScript::create("$('#{$this->id}').multiselect($options_json);");
    }
}
