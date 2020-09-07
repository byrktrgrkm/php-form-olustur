<?php 


class Form{

    private $html = "";
    private $attr = [
        "method"=>'GET',
        "action"=>'/',
        "enctype"=>"application/x-www-form-urlencoded"
    ];
    function __construct(){
        
    }
    public function action($action){
        $this->action = $action;
        return $this;
    }
    public function method($method){
        $this->attr('method',strtoupper($method));
        return $this;
    }
    public function enctype($etype){
        $this->attr('enctype', $etype);
        return $this;
    }
    public function class($class){
        $this->attr('class',$class);
        return $this;
    }
    public function name($name){
        $this->attr('name', $name);
        return $this;
    }
    private function attr($key,$value){
        $this->attr[$key] = $value;
    }
    public function inputs($elements){
        $s = "";
        foreach($elements as $e ){
            $str = "";
            $str .= "<div>";
            
            if(isset($e['label']) && isset($e['attr']['type']) && $e['attr']['type'] != 'radio'){
                $str .= '<label for="'.$e['attr']['id'].'">'.$e['label']."</label>";
            }
            if(!isset($e['attr']['type']) || $e['attr']['type'] != 'radio'){
                $str .= '<'.$e['element'].' ';
                if(isset($e['attr'])){
                    foreach($e['attr'] as $key => $attr){
                        if(strtolower($e['element']) == 'textarea' && $key == 'value') 
                            return;
                        $str .= $key.'="'.$attr.'" ';
                    }
                }
            }
            
            if(in_array(strtolower($e['element']),['textarea','button'])){
                $str .= '>';
                if(isset($e['attr']['value'])){
                    $str .= $e['attr']['value'];
                }
                $str .="</".$e['element'].'>';
            }else if($e['element'] == 'select'){
                $str .= '>';
                if(isset($e['data'])){
                    foreach($e['data'] as $value => $option){
                        $str .= '<option value="'.$value.'">'.$option.'</option>';
                    }
               
                }else{
                    $str .= "<option>Veri yüklenmedi</option>";
                }
                 $str .= "</".$e['element'].'>';
            }else if($e['attr']['type'] == 'radio'){
                if(isset($e['group'])){
                    foreach($e['group'] as $item){
                        $str .= '<input type="radio" ';
                         foreach($e['attr'] as $key => $attr)
                            $str .= $key.'="'.$attr.'" ';
                        $str .= 'id="'.$item['key'].'"';
                        $str .= "/>";
                        $str .= '<label for="'.$item['key'].'">'.$item['label'].'</label>';
                    }
                }
                
                
                
            }
            
            else{
                $str .= ' />';
            }
            
            $str .= "</div>";
 
            $s .= $str;
        }
        $this->html = $s;
        return $this;
    }
    public function html(){
        $form = '<form ';
        foreach($this->attr as $attr => $value){
            $form .= $attr .'="'.$value.'" ';
        }
        $form .='/>';
        
        $form .= $this->html;
        $form .= "</form>";
        return $form;
    }
    
}
class FormElement{
    private static $attributes = [];
    private static $_instance = null;
    
    public static function make ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
    public function input($type = 'text'){
        $this->element('input',strtolower($type));
        return $this;
    }
    public function name($name){
        $this->attr('name',$name);
        return $this;
    }
    public function value($val){
        $this->attr('value',$val);
        return $this;
    }
    public function button($type = 'button'){
        $this->element('button',$type);
        return $this;
    }
    public function select($data){
        $this->prop('element','select');
        $this->prop('data',$data);
        return $this;
    }
    public function id($id){
        $this->attr('id',$id);
        return $this;
    }
    public function group($items){
        $f = [];
        foreach($items as $p => $item){
            $f[$p]['key'] = $item[0];
            $f[$p]['label'] = $item[1];
        }
        $this->prop('group',$f);
        return $this;
    }
    public function class($classname){
        $this->attr('class',$classname);
        return $this;
    }
    public function checked($s = true){
        $this->attr('checked',$s);
        return $this;
    }
    public function label($label){
        $this->prop('label',$label);
        return $this;
    }
    private function element($elem,$type){
        $this->prop('element',$elem);
        $this->attr('type',$type);
    }
    private function attr($key,$value){
         self::$attributes['attr'][$key] = $value;
    }
    private function prop($key,$value){
        self::$attributes[$key] = $value;
    }
    
    public function get(){
        if(
            isset(self::$attributes['label']) && 
            !isset(self::$attributes['attr']['id'])){ 
            $this->attr('id',substr(uniqid(mt_rand(), true),0,8));
        }
        $vals = self::$attributes;
        $this->clear();
        return $vals;
    }
    private function clear(){
        self::$attributes = [];
    }
 
    
}

$val[] = FormElement::make()
->input('text')->class('strong')->label('Kullanıcı adı')->get();
$val[] = FormElement::make()
->input('password')->label('Şifre')->get();
$val[] = FormElement::make()
->select(['1'=>'Adana','2'=>'...'])->label('Şehir')->name('sehir')->get();
$val[] = FormElement::make()->input('radio')
->group([["kadın","Kadın"],["erkek","Erkek"]])->name('cinsiyet')->get();
$val[] = FormElement::make()->button('submit')->value('Gönder')->get();

print_r($val);
$form = new Form;

$html = $form
->method('post')
->inputs($val)
->html();

echo $html;





?>