<?php

namespace Atabasch;

class Validator{
    /*
    $values = [
            'title' => 'deneme',
            'slug' => 'falan-filan',
            'user' => 1,
            'status' => true,
            'username'  => 'asw'
    ]

    $rulesData = [
        'title' => [
            'required' => [true, 'başlık alanı boş bırakılamaz'],
            'minLength' => [5, 'en az 3 karakter'],
            'maxLength' => [15, 'en fazla 15 karakter'],
            'min' => [3, 'en az 3 karakter'],
            'max' => [15, 'en az 3 karakter'],
            'type' => ['string', 'en az 3 karakter'], 'string', 'number', 'boolean'
        ]
    ]

    $result = [
        'title' => [
            'message' => '...',
            'value' => '...'
        ]
    ]
     *
     *
     * */

    private array $values;
    private array $rulesData;
    private array $result = [];

    // %field, %number, %type
    private array $messages = [
        'required'  => '%field gerekli',
        'minLength' => '%field en az %number karakterden oluşmalı',
        'maxLength' => '%field en çok %number karakter olabilir',
        'min'       => '%field en az %number olarak girilebilir',
        'max'       => '%field en fazla %number olarak girilebilir',
        'type'      => '%field %type türünde olmalıdır',
        'enum'      => '%field için geçerli bir değer girilmedi',
    ];


    public function __construct($data=[], $rulesData=[]){
        $this->values    = $data;
        $this->rulesData = $rulesData;
        $this->validation();
    }

    public function getResult(){
        return count($this->result) > 0? $this->result : false;
    }

    //=============================================================================

    // Bütün alanları tek tek kontröle yollayan method
    private function validation(){
        // Girilen kuralları döngüye sok
        foreach($this->rulesData as $field => $rulesList){

            // eğer kurallarda bulunup gönderiilen değerlerde olmayan data varsa atla
            if(!isset($this->values[$field])){
                $this->fieldValidation($field, null, $rulesList);
                continue;
            }

            // bir alan için kural kontrolü yap.
            $this->fieldValidation($field, $this->values[$field], $rulesList);

        }
    }

    // Tek bir alanın tüm kontrollerini yapan method
    private function fieldValidation($field, $value, $rules){
        foreach ($rules as $rule => $options){
            $checkVerify = $this->verify($value, $rule, $options[0]);
            if(!$checkVerify){
                $this->setMessage($field, $value, $rule, $options);
                break;
            }
        }
    }

    private function verify($value, $rule, $option=null){
        return match ($rule){
            'required'  => $this->required($value),
            'minLength' => $this->minLength($value, $option),
            'maxLength' => $this->maxLength($value, $option),
            'min'       => $this->min($value, $option),
            'max'       => $this->max($value, $option),
            'type'      => $this->type($value, $option),
            'enum'      => $this->enum($value, $option),
        };
    }



    private function setMessage($field, $value, $rule, $options){
        $this->result[] = [
            'field'     => $field,
            'value'     => $value,
            'message'   => $this->getMessage($field, $value, $rule, $options)
        ];
    }

    private function getMessage($field, $value, $rule, $options){
        $resultMessage = !isset($options[1])? $this->messages[$rule] : $options[1];
        $optionValue = (is_array($options[0]) || is_object($options[0]))? '' : $options[0];
        $resultMessage = str_replace(['%field', '%value', '%type', '%number'], [$field, $value, $optionValue, $optionValue], $resultMessage);
        return $resultMessage;
    }

    //=============================================================================

    // En az uzunluk kontrolü
    private function minLength(string $value, int $length=0): bool{
        return strlen($value) >= $length;
    }

    // En fazla uzunluk kontrolü
    private function maxLength(string $value, int $length=16): bool{
        return strlen($value) <= $length;
    }

    // minimum sayı kontrolü
    private function min(int|float $value, int|float $limit=0): bool{
        return $value >= $limit;
    }

    // maximum sayı kontrolü
    private function max(int|float $value, int|float $limit=16): bool{
        return $value <= $limit;
    }

    private function enum($value, $options=[]){
        return in_array($value, $options);
    }

    // tür doğrulaması
    private function type($value, $type='string'): bool{
        $isType = match ($type){
            'str', 'string'     => is_string($value),
            'number', 'numeric' => is_numeric($value),
            'integer', 'int'    => is_integer($value),
            'bool', 'boolean'   => is_bool($value),
        };
        return  $isType;
    }

    // gereklilik kontrolü
    private function required($value): bool{
        return !empty($value) && !is_null($value);
    }

}
