<?php

abstract class BaseComponent
{
    protected array $props;

    public function __construct(array $props = [])
    {
        $this->props = $props;
    }

    abstract protected function view(): string;

    public function render(): string
    {
        ob_start();
        extract($this->props);
        include $this->view();
        return ob_get_clean();
    }
}
