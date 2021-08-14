<?php

namespace App\Helper;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use function in_array;


class Params
{
    public const DEFAULT_IMAGE = 'default_image';

    /** @var ParameterBagInterface */
    private $params;

    /** @var array $datas */
    private $datas = [];

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->datas = [
            self::DEFAULT_IMAGE,
        ];
    }

    /**
     * @param string $param_name
     * @return string
     */
    public function get(string $param_name): string
    {
        if (!in_array($param_name, $this->datas)) {
            throw new InvalidArgumentException('Unknown parameter: ' . $param_name);
        }

        return $this->params->get($param_name);
    }
}
