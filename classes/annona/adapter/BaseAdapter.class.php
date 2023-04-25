<?php 
namespace Flex\Annona\Adapter;

class BaseAdapter extends \ReflectionClass
{
    private string $version = '0.5.1';
    private array $instance = [];
    public function __construct () {}

    public function add(string $classname,mixed ...$argv) : void 
    {
        parent::__construct($classname);
        $instance = parent::newInstanceArgs($argv);
        $shortname = $this->getShortName();
        $this->instance[$shortname] = $instance;
    }

    public function &getInstant(string $classname) : mixed {
        return $this->instance[$classname];
    }

    public function &__get(string $classname) : mixed 
    {
        return $this->instance[$classname];
    }

    public function fetchInstances() : array {
        return $this->instance;
    }
}
?>