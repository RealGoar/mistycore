<?php

declare(strict_types=1);

namespace Jacob\Orix\form;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class NormalForm extends FormInterface
{
    const IMAGE_TYPE_URL = -1;
    const IMAGE_TYPE_PATH = 0;

    public function __construct(string $title = "", string $content = "")
    {
        $this->data["title"] = $title;
        $this->data["content"] = $content;
        $this->data["type"] = "form";
        $this->data["buttons"] = [];
    }

    public function setContent(string $content): void
    {
        $this->data["content"] = $content;
    }

    public function getContent(): string
    {
        return $this->data["content"];
    }

    public function addButton(string $text, int $imageType = -1, string $imagePath = ""): void
    {
        $content = ["text" => $text];
        if ($imageType !== -1) {
            $content["image"]["type"] = $imageType === 0 ? "path" : "url";
            $content["image"]["data"] = $imagePath;
        }
        $this->data["buttons"][] = $content;
    }

    final public function handleResponse(Player $player, $data): void
    {
        parent::handleResponse($player, TextFormat::clean($this->getButton($data) ?? ""));
    }

    public function getButton(?int $index): ?string
    {
        return isset($this->data["buttons"][$index]) ? TextFormat::clean($this->data["buttons"][$index]["text"]) : null;
    }
}