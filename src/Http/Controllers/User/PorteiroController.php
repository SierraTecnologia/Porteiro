<?php

namespace Porteiro\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use League\Flysystem\Util;
use Porteiro\Facades\Porteiro;
use Porteiro\Http\Controllers\User\Controller;

class PorteiroController extends Controller
{
    public function index(Request $request)
    {
        return Porteiro::view('facilitador::index');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('rica.login');
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');

        $path = $slug.'/'.date('F').date('Y').'/';

        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(\Illuminate\Support\Facades\Config::get('sitec.facilitador.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }

        $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::make($file)
                ->resize(
                    $resizeWidth, $resizeHeight, function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }
                );
            if ($ext !== 'gif') {
                $image->orientate();
            }
            $image->encode($file->getClientOriginalExtension(), 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(\Illuminate\Support\Facades\Config::get('sitec.facilitador.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = __('pedreiro::media.success_uploading');
                $fullFilename = $fullPath;
            } else {
                $status = __('pedreiro::media.error_uploading');
            }
        } else {
            $status = __('pedreiro::media.uploading_wrong_type');
        }

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.helpers.setImageValue('".Porteiro::image($fullFilename)."'); </script>";
    }
}
