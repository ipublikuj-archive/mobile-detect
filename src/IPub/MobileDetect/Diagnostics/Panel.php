<?php
/**
 * MobileDetectExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           21.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Diagnostics;

/**
 * Mobile device detect tracy panel
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Diagnostics
 *
 * @author         Václav Pelíšek <info@peldax.com>
 */
final class Panel extends \Nette\Object implements \Tracy\IBarPanel
{
    /** @var \IPub\MobileDetect\MobileDetect
     */
    private $mobileDetect;

    public function register(\IPub\MobileDetect\MobileDetect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;

        \Tracy\Debugger::getBar()->addPanel($this, 'ipub.mobileDetect');

        return $this;
    }

    protected function getView()
    {
        if (!$this->mobileDetect->isMobile())
        {
            return 'Full';
        }

        if ($this->mobileDetect->isPhone())
        {
            return 'Phone';
        }

        return 'Tablet';
    }

    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return '<span title="Mobile detect"><img width="16px" height="16px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAACNCAYAAAAeuFBXAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFMzg3RTE0RTg0MUYxMUUyQkRDMEVDMzQ5RjcxOEI3OCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowOUUwNjQ2QTg0MjAxMUUyQkRDMEVDMzQ5RjcxOEI3OCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkUzODdFMTRDODQxRjExRTJCREMwRUMzNDlGNzE4Qjc4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkUzODdFMTREODQxRjExRTJCREMwRUMzNDlGNzE4Qjc4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+kH3WGwAABD9JREFUeNrs3d1uFVUYh/EZw3l7B2zvoHpi1Gh3JZoYY6xX0HrgiRptUROMke6Nn1GQatQDj+AO8AOMgLD9gnhg5A6sdwA3wHKm1AMT7S666ax51+9JFlMSkuZd+z8vz1ozq61TShUQhboNdH30zELz9abpQF9Jx5aH7fXAzt/nm7FoWtB37jIFEGhAoAGBBm6LW4vCut7t34xNEzJh0IyV6YHehTR+amQekQP1xhfDaYGmHChOOYBMWvT0nOrQCIVAQ6ABDg1waIByQKABDg1k59AHZvr9nHzBbfLXSZPZdujZ4eQLMlCOakbKQV1wZ51jrw5tqhAiz3Y5EFI59m8VCvTHoYFQDq1Dg0MDlANaNOUA5QD6qBz7eAsBvXFoeUYohwY6dmhPChEKi0JwaKAMh6Yc6NihKQcox/+6hYA7rhyVPCNShxZEBHFo+9Dg0EDeDl3v3zcEeqQc5hzdYh8aHFqLRhkOLc8I5dASjVDKIc/ouEVbFCJgh7YPDQ4tz4ju0BINDg3k6tAaNPJwaIlGjERbFIJySDRyVQ6LQgTs0B59g0NLNMI7NNCtQ9vlAIeWaJTh0PKMWA4t0cjCoQURlEODRq6LQokGh5ZnRHdoiUa3ifZyEiiHBo3MlcOiEJE6tCAiSIu2Dw0OLdHI3KGdWAHlkGhkh31ocGgNGmU4tESj40RTDgRUjpndQDo0Ijk0EMqh3RjoONH2oUE5gEKUw52BbhNt2w6hEGhwaMqBMhwa6DjRnhSCQwOZO7TOij4Yh207FNmhZ8dWM8amFSECnZ69tw30yLSCQwMcGhBoxFcOE4E+KMeeF4USjRiJphzg0EDmDk050AfjoBygHEDflWO3Lv/5rxPThEyYn4VDL5pHUA6gM+VIyUxAoAGBBvYh0FuVkybDQAvgYj/LOunOVf3ZL+12UHtTzwUpaT09d9+mQJca6E+vth/+S4FKutGMQXr+/usCXdoEfHJl0Fx+D1ja6fTCA6sCXdoEfPzzpIr78Oie9OKD18pbFJYa5o9+Glaxn4Ru7ix2degiit/8sV0IHgxe5tNp7aEzAh298JM/rDWXkwWU+kczFtL6w9cFOmrRH34fbZtuGuN0eHEk0FGLPnH5VHNZKajkG9td+uWlLYGOVvDxSwvN5bcCLet0euWRVYGOVvAH302qct/xXkqvHppELrCobbv6/YvLVdkHFlqPHurQUYp970LrkAersnkmHXn0lED3vdB3z7fdaaPCrfc8Xnss5DZeEcpRv/PtoLmsyfI2cztzMdKh+1rk29+0/8WuyPLfuDu9/viWDt23ML91bijM/0j7nseyDt23At88O6n8KIZ/Yym98cREh+5LmI99vSrMU7v0gg7dh8LGX5X2vsZ/ZT1tPBnmuFbcHzST0loz5rZPtBu7jVE9+nI+ysceVzlu3mz/9DsT98agGSFOtjgki1D8KcAASOpOloXu01cAAAAASUVORK5CYII=" />'
            . $this->getView() . '</span>';
    }

    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {

    }
}
