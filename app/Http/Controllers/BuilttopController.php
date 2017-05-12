<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\BuiltTop;
use App\Http\Controllers\Controller;

class BuilttopController extends Controller {

    private $_request;

    function __construct(Request $request) {
        $this->_request = $request;
    }

    function getIndex() {
        $this->authorize('ViewBuilttop');
        try {
            $search = $this->_request->get('key');
            if ($search != '') {
                $articles = Article::where('title' , 'like' , '%'.$search.'%')->paginate(20);
                $data['articles'] = $articles;
                return view('childs.builttop.index')->with($data);
            } else {
                return view('childs.builttop.index');
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    function getAdd($id) {
        $this->authorize('AddBuilttop');
        try {
            $ar = Article::find($id);
            $position = (int) BuiltTop::max('position');
            $built = new BuiltTop();

            $built->name = $ar->title;
            $built->link_img = $ar->thumbnail;
            $built->post_id = $ar->id;
            $built->position = ($position + 1);


            $built->save();
            return json_encode(['status' => 'success', 'msg' => 'Add bài viết thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => 'Bài viết đã tồn tại trong danh sách']);
        }
    }

    function getList() {
        try {
            $builttop = BuiltTop::orderBy('position', 'asc')->paginate(20);
            $data['articles'] = $builttop;
            return view('childs.builttop.list-built')->with($data);
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    function postPublish($status) {
        $this->authorize('PublishBuilttop');
        try {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($id > 0) {
                $builttop = BuiltTop::find($id);
                $builttop->status = (int) $status;
                $builttop->save();
                return json_encode(['status' => 'success', 'msg' => 'Publish bài viết thành công']);
            } else {
                return json_encode(['status' => 'error', 'msg' => 'Không tồn tại bài viết trong danh sách']);
            }

        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => 'Xảy ra lỗi khi publish bài viết']);
        }
    }

    function postChangepos() {
        $this->authorize('ChangePosition');
        try {
            $strID = isset($_POST['id']) ? $_POST['id'] : '';
            $strPos = isset($_POST['position']) ? $_POST['position'] : '';
            $arrID = explode(',', $strID);
            $arrPos = explode(',', $strPos);
            $check = 0;
            for ($i = 0; $i < count($arrID); $i++) {
                $id = (int) $arrID[$i];
                if ($id > 0) {
                    $pos = (int) $arrPos[$i];
                    $builttop = BuiltTop::find($id);
                    $builttop->position = $pos;
                    $builttop->save();
                    $check = 1;
                }
            }
            if ($check) {
                return json_encode(['status' => 'success', 'msg' => 'Publish bài viết thành công']);
            } else {
                return json_encode(['status' => 'error', 'msg' => 'Xảy ra lỗi khi publish bài viết']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => 'Xảy ra lỗi khi publish bài viết']);
        }
    }

    function postDelete() {
        $this->authorize('DeleteBuilttop');
        try {
            $strID = isset($_POST['strID']) ? $_POST['strID'] : '';
            $arrID = explode(',', $strID);
            $check = 0;

            for ($i = 0; $i < count($arrID); $i++) {
                $id = (int) $arrID[$i];
                if ($id > 0) {
                    $builttop = BuiltTop::find($id);
                    $builttop->delete();
                    $check = 1;
                }
            }
            if ($check) {
                return json_encode(['status' => 'success', 'msg' => 'Delete builttop ']);
            } else {
                return json_encode(['status' => 'error', 'msg' => 'Error delete builttop !']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => 'Error delete builttop !']);
        }
    }

    function postSubmit() {
        $this->authorize('SubmitChange');
        try {
            $strID = isset($_POST['strID']) ? $_POST['strID'] : '';
            $strImg = isset($_POST['strImg']) ? $_POST['strImg'] : '';
            $arrID = explode(',', $strID);
            $arrImg = explode(',', $strImg);
            $check = 0;

            for ($i = 0; $i < count($arrID); $i++) {
                $id = (int) $arrID[$i];
                if ($id > 0) {
                    $builttop = BuiltTop::find($id);
                    $builttop->link_img = $arrImg[$i];
                    $builttop->save();

                    $check = 1;
                }
            }
            if ($check) {
                return json_encode(['status' => 'success', 'msg' => 'Change builttop success']);
            } else {
                return json_encode(['status' => 'error', 'msg' => 'Error change builttop !']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => 'Error change builttop !']);
        }
    }

}
