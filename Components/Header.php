<?php

require_once 'BaseComponent.php';

class Header extends BaseComponent
{
    protected function view(): string
    {
        return __DIR__ . '/../view/header.php';
    }
}
