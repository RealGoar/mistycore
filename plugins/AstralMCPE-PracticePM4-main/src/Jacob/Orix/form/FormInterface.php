<?php

declare(strict_types=1);

namespace Jacob\Orix\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

abstract class FormInterface implements Form
{
    protected array $data = [];
    /**
     * @var callable|null
     */
    protected $callback = null;
    protected ?FormInterface $previous = null;

    public function setTitle(string $title): void
    {
        $this->data["title"] = $title;
    }

    public function getTitle(): string
    {
        return $this->data["title"];
    }

    public function getPrevious(): ?FormInterface
    {
        return $this->previous;
    }

    public function setPrevious(?FormInterface $form): void
    {
        $this->previous = $form;
    }

    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    public function setCallback(?callable $callback): void
    {
        $this->callback = $callback;
    }

    public function handleResponse(Player $player, $data): void
    {
        $this->onResponse($player, $data);
    }

    public function onResponse(Player $player, $data)
    {
        if ($this->callback) {
            $callback = $this->callback;
            $callback($player, $data);
        }
    }

    public function sendPrevious(Player $player): void
    {
        if (!$this->previous !== null) {
            $player->sendForm($this->previous);
        }
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}