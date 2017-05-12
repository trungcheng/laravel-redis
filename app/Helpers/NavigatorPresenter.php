<?php
/**
 * Created by PhpStorm.
 * User: tanlinh
 * Date: 2/26/2016
 * Time: 9:56 PM
 */

namespace App\Helpers;

use Pingpong\Menus\Presenters\Presenter;

class NavigatorPresenter extends Presenter
{
    /**
     * {@inheritdoc }
     */
    public function getOpenTagWrapper()
    {
        return PHP_EOL . '<ul class="sidebar-menu">' . PHP_EOL;
    }

    /**
     * {@inheritdoc }
     */
    public function getCloseTagWrapper()
    {
        return PHP_EOL . '</ul>' . PHP_EOL;
    }

    /**
     * {@inheritdoc }
     */
    public function getMenuWithoutDropdownWrapper($item)
    {
        if (isset($item->properties['header']) && $item->properties['header'] == true) {
            return '<li class="header">' . $item->properties['title'] . '</li>';
        }
        return '<li class="' . $this->getActiveState($item) . '"><a href="' . $item->getUrl() . '">' . $item->getIcon() . '<span>' . $item->title . '</span></a></li>';
    }

    /**
     * {@inheritdoc }
     */
    public function getActiveState($item)
    {
        return \Request::is($item->getRequest()) ? 'active' : null;
    }

    /**
     * {@inheritdoc }
     */
    public function getDividerWrapper()
    {
        return '<li class="divider"></li>';
    }

    /**
     * {@inheritdoc }
     */
    public function getMenuWithDropDownWrapper($item)
    {
        return '<li class="treeview ' . $this->getParentState($item) . '">
                <a href="#">
                 ' . $item->getIcon() . ' <span>' . $item->title . ' </span ><i class="fa fa-angle-left pull-right" ></i >
                </a >
                <ul class="treeview-menu" >
    ' . $this->getChildMenuItems($item) . '
                </ul >
              </li > ' . PHP_EOL;
    }

    public function getParentState($item)
    {
        if (\Request::segment(1) == $item->getProperties()['attributes']['parent_url']) {
            return 'active';
        }
        return '';
    }
}