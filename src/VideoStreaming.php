<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Filesystem\FilesystemInterface;

final class VideoStreaming
{
    protected $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        $fileName = $params['video'] ?? null;

        if ($fileName) {
            return $this->makeResponseFile($fileName);
        }
        return new Response(200, ['Content-Type' => 'text/plain'], "This is streaming application");
    }

    /**
     * @param string $fileName
     * @return \React\Promise\PromiseInterface
     */
    protected function makeResponseFile(string $fileName) : \React\Promise\PromiseInterface
    {
        $file = $this->getFile($fileName);
        return $file->exists()
            ->then(
                function () use ($file, $fileName) {
                    return new Response(200, ['Content-Type' => 'video/mp4'], \React\Promise\Stream\unwrapReadable($file->open('r')));
                },
                function () use ($fileName) {
                    return new Response(404, ['Content-Type' => 'text/plain'], 'file '. $fileName .' does not exist.');
                });
    }

    /**
     * @param string $fileName
     * @return \React\Filesystem\Node\FileInterface
     */
    protected function getFile(string $fileName): \React\Filesystem\Node\FileInterface
    {
        $filePath = public_path . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . basename($fileName);
        return $this->filesystem->file($filePath);
    }
}
