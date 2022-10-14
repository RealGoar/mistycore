<?php

declare(strict_types=1);

namespace Jacob\Orix\form;

class ModalForm extends FormInterface
{
    public function __construct(string $title = "", string $content = "")
    {
        $this->data["title"] = $title;
        $this->data["content"] = $content;
        $this->data["type"] = "modal";
        $this->data["button1"] = "";
        $this->data["button2"] = "";
    }

    public function setContent(string $content): void
    {
        $this->data["content"] = $content;
    }

    public function getContent(): string
    {
        return $this->data["content"];
    }

    public function setFirstButton(string $button): void
    {
        $this->data["button1"] = $button;
    }

    public function setSecondButton(string $button): void
    {
        $this->data["button2"] = $button;
    }

    public function getFirstButton(): string
    {
        return $this->data["button1"];
    }

    public function getSecondButton(): string
    {
        return $this->data["button2"];
    }
}