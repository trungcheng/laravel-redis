<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventsController extends Controller {

    function getIndex(Request $request) {
        $this->authorize('ViewEvent');
        try {
            $data['request_all'] = $request->all();

            $events = Event::select('id', 'event_name', 'event_started', 'event_ended', 'type', 'created_at', 'event_thumbnail', 'status', 'rule');
            if ($request->has('key')) {
                $keyword = strip_tags($request->get('key'));
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $events = $events->whereRaw("event_name LIKE '%$keyword%'");
            }
////
            if ($request->has('status') && $request->get('status') != '-1') {
                $events = $events->where('status', (int) $request->get('status'));
            }
//die;
            if (\Request::has('order_name')) {
                $order_name = 'event_name';
                $order_type = \Request::get('order_name');
            } elseif (\Request::has('order_id')) {
                $order_name = 'id';
                $order_type = \Request::get('order_id');
            } else {
                $order_name = 'created_at';
                $order_type = 'desc';
            }
            $events = $events->orderBy($order_name, $order_type)->paginate(10);
            $data['events'] = $events;
            return view('childs.events.index')->with($data);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function getCreate() {
        $this->authorize('CreateEvent');
        return view('childs.events.create');
    }

    function postCreate() {
        $this->authorize('CreateEvent');
        try {
            $date = date('Y-m-d H:i:s');
            $data_event = \Input::only(['name', 'thumbnail', 'start_date', 'end_date', 'type', 'status', 'rule']);
            $name = !empty($data_event['name']) ? strip_tags($data_event['name']) : '';
            $thumbnail = !empty($data_event['thumbnail']) ? strip_tags($data_event['thumbnail']) : '';
            if (!empty($thumbnail)) {
                $this->resizeImage($thumbnail, 300, 180);
                $this->resizeImage($thumbnail, 600, 360);
                $this->resizeImage($thumbnail, 96, 72);
            }
            $rule = !empty($data_event['rule']) ? $data_event['rule'] : '';
            $start_date = !empty($data_event['start_date']) ? strip_tags($data_event['start_date']) : $date;
            $end_date = !empty($data_event['end_date']) ? strip_tags($data_event['end_date']) : $date;
            $type = !empty($data_event['type']) ? (int) strip_tags($data_event['type']) : 0;
            $status = !empty($data_event['status']) ? (int) strip_tags($data_event['status']) : 0;

            $event = new Event();
            $event->event_name = $name;
            $event->event_thumbnail = $thumbnail;
            $event->rule = $rule;
            $event->event_started = date('Y-m-d H:i:s', strtotime($start_date));
            $event->event_ended = date('Y-m-d H:i:s', strtotime($end_date));
            $event->type = $type;
            $event->status = $status;
            $event->created_at = $date;
            $event->updated_at = $date;

            if ($event->save()) {
                return json_encode(['status' => 'success', 'msg' => 'Create Event Successfully']);
            } else {
                return json_encode(['status' => 'fail', 'msg' => 'Create Event Fail']);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function getEdit($event_id) {
        $this->authorize('EditEvent');
        $data['event'] = Event::find($event_id);
        return view('childs.events.edit')->with($data);
    }

    function postEdit($event_id) {
        $this->authorize('EditEvent');
        try {
            $date = date('Y-m-d H:i:s');
            $data_event = \Input::only(['name', 'thumbnail', 'start_date', 'end_date', 'type', 'status', 'rule']);
            $name = !empty($data_event['name']) ? strip_tags($data_event['name']) : '';
            $thumbnail = !empty($data_event['thumbnail']) ? strip_tags($data_event['thumbnail']) : '';
            if (!empty($thumbnail)) {
                $this->resizeImage($thumbnail, 300, 180);
                $this->resizeImage($thumbnail, 600, 360);
                $this->resizeImage($thumbnail, 96, 72);
            }
            $rule = !empty($data_event['rule']) ? $data_event['rule'] : '';
            $start_date = !empty($data_event['start_date']) ? strip_tags($data_event['start_date']) : $date;
            $end_date = !empty($data_event['end_date']) ? strip_tags($data_event['end_date']) : $date;
            $type = !empty($data_event['type']) ? (int) strip_tags($data_event['type']) : 0;
            $status = !empty($data_event['status']) ? (int) strip_tags($data_event['status']) : 0;

            $event = Event::find($event_id);
            $event->event_name = $name;
            $event->event_thumbnail = $thumbnail;
            $event->rule = $rule;
            $event->event_started = date('Y-m-d H:i:s', strtotime($start_date));
            $event->event_ended = date('Y-m-d H:i:s', strtotime($end_date));
            $event->type = $type;
            $event->status = $status;
            $event->created_at = $date;
            $event->updated_at = $date;

            if ($event->save()) {
                return json_encode(['status' => 'success', 'msg' => 'Update Event Successfully']);
            } else {
                return json_encode(['status' => 'fail', 'msg' => 'Update Event Fail']);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function postDelete() {
        $this->authorize('DeleteEvent');
        $data = \Input::only(['id']);
        $id = !empty($data['id']) ? (int) strip_tags($data['id']) : 0;
        if ($id > 0) {
            $event = Event::findOrFail($id);
            $event->delete();
            return json_encode(['status' => 'success', 'msg' => 'Delete Event Successfully']);
        } else {
            return json_encode(['status' => 'fail', 'msg' => 'Delete Event Fail']);
        }
    }

    function postStatus() {
        $this->authorize('EditEvent');
        $data = \Input::only(['id', 'status']);
        $id = !empty($data['id']) ? (int) strip_tags($data['id']) : 0;
        $status = !empty($data['status']) ? (int) strip_tags($data['status']) : 0;
        if ($id > 0) {
            $event = Event::findOrFail($id);
            $event->status = $status;
            if ($event->save()) {
                return json_encode(['status' => 'success', 'msg' => 'Update Event Successfully']);
            } else {
                return json_encode(['status' => 'fail', 'msg' => 'Update Event Fail']);
            }
        } else {
            return json_encode(['status' => 'fail', 'msg' => 'Update Event Fail']);
        }
    }

}
