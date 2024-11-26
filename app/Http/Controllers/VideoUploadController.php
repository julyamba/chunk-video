<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;

class VideoUploadController extends Controller
{
    public function upload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            return response()->json(['message' => 'File not uploaded'], 400);
        }

        $fileReceived = $receiver->receive();
        if ($fileReceived->isFinished()) {
            $file = $fileReceived->getFile();
            $extension = $file->getClientOriginalExtension();
            $fileName = str_random(20) . '.' . $extension;
            $path = storage_path('app/public/videos/');
            $file->move($path, $fileName);

            return response()->json([
                'path' => asset('storage/videos/' . $fileName),
                'name' => $fileName
            ]);
        }

        $handler = $fileReceived->handler();
        return response()->json([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
    }
}
