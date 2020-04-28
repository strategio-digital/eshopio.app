<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\AssetLoader;

use Nette\Application\UI\Control;
use Nette\Utils\Json;

class AssetLoader extends Control
{
    /*
     * Manifest path
     */
    const MANIFEST_PATH = __DIR__ . '/../../../www/temp/static/manifest.json';


    /*
     * FileNames
     */
    const FILE_NAME = [
        'js' => 'sys-frontend-build.js',
        'css' => 'sys-frontend-build.css'
    ];


    /**
     * @var array<string>
     */
    protected array $json;


    /**
     * AssetLoader constructor.
     * @throws \Nette\Utils\JsonException
     */
    public function __construct()
    {
        $content = file_get_contents(self::MANIFEST_PATH);
        $this->json = Json::decode($content, Json::FORCE_ARRAY);
    }

    /**
     * @param string $type
     */
    public function render(string $type = 'js') : void
    {
        $fileName = self::FILE_NAME[$type];

        $this->template->render(__DIR__ . '/templates/asset-loader.latte', [
            'type' => $type,
            'currentFile' => $this->json[$fileName]
        ]);
    }
}
