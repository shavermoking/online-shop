<?php

namespace Support\Testing;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;

final class FakerImageProvider extends Base
{
    public function fixtureImage(string $fixturesDir, string $storageDir): string
    {
        // Создаем директорию, если она не существует
        if (!Storage::exists($storageDir)) {
            Storage::makeDirectory($storageDir);
        }

        // Копируем файл из фикстур в нужное хранилище
        $file = $this->generator->file(
            base_path("tests/Fixtures/images/$fixturesDir"),
            Storage::path($storageDir),
            false // Возвращает только имя файла
        );

        // Возвращаем относительный путь, чтобы хранить его в базе данных
        return '/storage/' . trim($storageDir, '/') . '/' . $file;
    }
}
