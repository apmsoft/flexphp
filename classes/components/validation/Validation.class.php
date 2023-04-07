<?php 
namespace Flex\Components\Validation;

use Flex\Annona\Request\FormValidation;
use Flex\Components\Columns\ColumnsEnum;

class Validation
{
    public function __construct(){
    }

    public function id (int $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::ID->value, ColumnsEnum::ID->label(),$value))
            ->null()->number();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function name (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::NAME->value, ColumnsEnum::NAME->label(),$value))
            ->null()->space()->disliking([]);
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function userid (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::USERID->value, ColumnsEnum::USERID->label(),$value))
            ->null()->length(4,16)->space()->disliking()->alnum();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function passwd (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::PASSWD->value, ColumnsEnum::PASSWD->label(),$value))
            ->null()->length(4,16)->space()->liking();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function email (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::EMAIL->value, ColumnsEnum::EMAIL->label(),$value))
            ->null()->space()->email();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function birthday (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::BIRTHDAY->value, ColumnsEnum::BIRTHDAY->label(),$value))
            ->null()->space()->datef();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function start_date (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::START_DATE->value, ColumnsEnum::START_DATE->label(),$value))
            ->null()->space()->datef();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function end_date (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::END_DATE->value, ColumnsEnum::END_DATE->label(),$value))
            ->null()->space()->datef();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function linkurl (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::LINKURL->value, ColumnsEnum::LINKURL->label(),$value))
            ->null()->space()->url();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function view_count (int $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::VIEW_COUNT->value, ColumnsEnum::VIEW_COUNT->label(),$value))
            ->null()->number();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function title (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::TITLE->value, ColumnsEnum::TITLE->label(),$value))
            ->null()->number();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function extract_id (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::EXTRACT_ID->value, ColumnsEnum::EXTRACT_ID->label(),$value))
            ->null()->disliking(['_','-']);
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }

    public function extract_data (string $value) : Validation 
    {
        try{
            (new FormValidation(ColumnsEnum::EXTRACT_DATA->value, ColumnsEnum::EXTRACT_DATA->label(),$value))
            ->null()->jsonf();
        }catch(\Exception $e){ throw new \Exception($e->getMessage());}

    return $this;
    }
}
?>