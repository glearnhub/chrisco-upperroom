<?php

namespace App\Http\Controllers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImage;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $url  = $request->query('url');
        $size = max(100, min(800, (int) $request->query('size', 300)));

        if (!$url || !preg_match('/^https?:\/\//i', $url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400, 'Invalid URL');
        }

        $scale = (int) max(3, ceil($size / 33));

        $options = new QROptions;
        $options->outputType       = QRGdImage::class;
        $options->scale            = $scale;
        $options->imageBase64      = false;
        $options->quietzoneSize    = 2;
        $options->imageTransparent = false;
        $options->bgColor          = [255, 255, 255];

        $png = (new QRCode($options))->render($url);

        return response($png, 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
