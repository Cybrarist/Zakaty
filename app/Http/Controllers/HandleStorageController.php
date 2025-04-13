<?php

namespace App\Http\Controllers;

use App\Models\Gold;
use App\Models\Money;
use App\Models\Silver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HandleStorageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $path, string $filename)
    {
        if (Storage::disk($path)->exists($filename)) {

            $query = match ($path) {
                'money' => Money::query(),
                'silver' => Silver::query(),
                'gold' => Gold::query(),
            };

            if ($query->where('user_id', Auth::id())->whereLike('images', "%$filename%")->count()) {

                return Storage::disk($path)
                    ->response("$filename", $filename, [
                        "Content-Type" => "image/png",
                    ]);
            } else {
                abort(404);
            }

        }

        abort(404);


        //        return response()
        //            ->file(Storage::disk('dispatch_hub_aws')->temporaryUrl("payment_collections_images/$file_name", now()->addSeconds(5)));
        //            ->header('Content-Type', 'application/image')
        //            ->header('Content-Disposition', 'inline');
    }
}
