<?php

declare(strict_types=1);

namespace Jacob\Orix\particle;

class ThreeDimensionalPoint
{
    public float $x;
    public float $y;
    public float $z;

    public function __construct(float $x, float $y, float $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function rotate(float $rot): ?ThreeDimensionalPoint
    {
        $cos = cos($rot);
        $sin = sin($rot);

        return new ThreeDimensionalPoint(
            (float)($this->x * $cos + $this->z * $sin),
            $this->y,
            (float)($this->x * -$sin + $this->z * $cos));
    }
}
