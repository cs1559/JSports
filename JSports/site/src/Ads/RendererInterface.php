<?php
namespace FP4P\Component\JSports\Site\Ads;

interface RendererInterface
{
    public function render(Ad $ad, string $position = null);
}

