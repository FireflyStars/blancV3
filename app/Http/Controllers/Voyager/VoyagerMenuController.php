<?php

namespace App\Http\Controllers\Voyager;

use TCG\Voyager\Http\Controllers\VoyagerMenuController as BaseVoyagerMenuController;
use TCG\Voyager\Voyager;
use Illuminate\Support\Facades\Artisan;


class VoyagerMenuController extends BaseVoyagerMenuController
{
    /*
	 public function delete_menu($menu, $id)
    {
        Artisan::call('cache:clear'); // clear cache command

		$item = Voyager::model('MenuItem')->findOrFail($id);

        $this->authorize('delete', $item);

        $item->deleteAttributeTranslation('title');

        $item->destroy($id);

		Artisan::call('cache:clear'); // clear cache command

        return redirect()
            ->route('voyager.menus.builder', [$menu])
            ->with([
                'message'    => __('voyager::menu_builder.successfully_deleted'),
                'alert-type' => 'success',
            ]);
    }
    */
}
