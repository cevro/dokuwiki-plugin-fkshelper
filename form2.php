<?php



if(!function_exists('form_makeDatalist')){

    /**
     * form_makeDatalist
     * Creates hidden datalist of values 
     * Attribute list=$id has to be used to join datalist with textfield
     * The list of values is arrays of (value,text),
     * or an associative array with the values as keys and text as values.
     * 
     * @var $id string id of datalist to contact with textfield
     * @author  Michal Cervenak <miso@fykos.cz>
     */
    function form_makeDatalist($id,$values,$attrs = array()) {
        $options = array();
        reset($values);

        // for ascociative array
        if(is_string(key($values))){
            foreach ($values as $val => $text) {
                $options[] = array($val,$text);
            }
        }else{
            foreach ($values as $val) {

                if(is_array($val)){
                    @list($val,$text) = $val;
                }else{
                    $text = $val;
                }
                $options[] = array($val,$text);
            }
        }
        $elem = array('_elem' => 'datalist','_options' => $options,
            'id' => $id);
        return array_merge($elem,$attrs);
    }

}

if(!function_exists('form_datalist')){

    /**
     * form_datalist
     * 
     * Print the HTML datalist.
     * @var $attrs array ivt _optonst and id 
     * 
     * _options: array of  (value,text) for datalist
     * @author  Michal Cervenak <miso@fykos.cz>
     */
    function form_datalist($attrs) {

        $s = '<datalist '.buildAttributes($attrs,true).'>'.DOKU_LF;
        if(!empty($attrs['_options'])){
            foreach ($attrs['_options'] as $opt) {
                @list($value,$text) = $opt;
                $p = '';
                if(is_null($text)){
                    $text = $value;
                }
                $p .= ' value="'.formText($value).'"';
                $s .= '<option'.$p.'>'.formText($text).'</option>';
            }
        }else{
            $s .= '';
        }
        $s .= DOKU_LF.'</datalist>';

        return $s;
    }

}

if(!function_exists('form2_makeDateTimeField')){

    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $label
     * @param type $id
     * @param type $class
     * @param type $required
     * @param type $step
     * @param type $attrs
     * @return type
     */
    function form2_makeDateTimeField($name,$value = null,$label = "",$id = null,$class = "",$required = FALSE,$step = 1,$attrs = array()) {

        if(is_null($label)){
            $label = $name;
        }
        if($value == null || $value==0){
            $value = date('Y-m-d\TH:i:s',time());
        }
        $elem = array('_elem' => 'datetimefield','_text' => $label,'_class' => $class,
            'id' => $id,'name' => $name,'value' => $value,'class' => 'edit','step' => $step);
        if($required){
            $elem['required'] = 'required';
        }
        return array_merge($elem,$attrs,array('pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}'));
    }

    /**
     * 
     * @param type $attrs
     * @return string
     */
    function form_datetimefield($attrs) {


        unset($attrs['type']);
        $s = '<label';
        if($attrs['_class']){
            $s .= ' class="'.$attrs['_class'].'"';
        }
        if(!empty($attrs['id'])){
            $s .= ' for="'.$attrs['id'].'"';
        }
        $s .= '><span>'.$attrs['_text'].'</span> ';
        $s .= '<input type="datetime-local" '.buildAttributes($attrs,true).' /></label>';
        if(preg_match('/(^| )block($| )/',$attrs['_class'])){
            $s .= '<br />';
        }
        return $s;
    }
    
    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $label
     * @param type $id
     * @param type $class
     * @param type $required
     * @param type $step
     * @param type $attrs
     * @return type
     */
    function form2_makeWeekField($name,$value= null,$label = "",$id = null,$class = "",$required = FALSE,$step = 1,$attrs = array()){
        
        if(is_null($label)){
            $label = $name;
        }
        if($value === null){
            $value = date('Y-\WW',time());
        }elseif($value==0){
            $value="";
        }
        $elem = array('_elem' => 'weekfield','_text' => $label,'_class' => $class,
            'id' => $id,'name' => $name,'value' => $value,'class' => 'edit','step' => $step);
        if($required){
            $elem['required'] = 'required';
        }
        return array_merge($elem,$attrs);
    }
    
    function form_weekfield($attrs) {


        unset($attrs['type']);
        $s = '<label';
        if($attrs['_class']){
            $s .= ' class="'.$attrs['_class'].'"';
        }
        if(!empty($attrs['id'])){
            $s .= ' for="'.$attrs['id'].'"';
        }
        $s .= '><span>'.$attrs['_text'].'</span> ';
        $s .= '<input type="week" '.buildAttributes($attrs,true).' /></label>';
        if(preg_match('/(^| )block($| )/',$attrs['_class'])){
            $s .= '<br />';
        }
        return $s;
    }

}