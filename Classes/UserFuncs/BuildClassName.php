<?php
  namespace LEF\LefResponsiveGrid\UserFuncs;

class BuildClassName
{
    private function getSettings()
    {
        return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_lefresponsivegrid.']['settings.'];
    }

    private function getConf($conf, $key, $default)
    {
        if (isset($conf[$key])) {
            return $this->cObj->stdWrap($conf[$key], $conf[$key . '.']);
        }
        return $default;
    }

    public function visibility($content= '', $conf)
    {
        $visible = $this->getConf($conf, 'visible', false);
        if (!$visible) {
            return '';
        }

        if ($visible) {
            $visible = explode(',', $visible);
        } else {
            return '';
        }
        if (count($visible) < 1) {
            return '';
        }

        $settings = $this->getSettings();
        $template = $settings['cssclasses.']['template.'][$conf['template']];

        $res = ' ';
        $search  = array(
        0=>'/\{visible\}/',
        1=>'/\{breakpoint\}/'
      );

        foreach ($visible as $k=>$breakpoint) {
            $visibility = explode('-', $breakpoint);
            $replace = array(
          0=>$visibility[0],
          1=>$visibility[1]
        );
            if ($k > 0) {
                $res .= ' ';
            }
            $res .= preg_replace($search, $replace, $template);
        }
        $this->cObj->data['noTrimWrap'] = '| ||';

        return $res;
    }

    public function main($content= '', $conf)
    {
        if ($this->getConf($conf, 'if', false)) {
            return '';
        }

        $columns = $this->getConf($conf, 'columns', false);
        if ($columns) {
            $columns  = explode('.', $columns);
        } else {
            return '';
        }

        if (intval($columns[0]) < 1) {
            return '';
        }

        $settings = $this->getSettings();
        $template = $settings['cssclasses.']['template.'][$conf['template']];

        $search  = array(
        0=>'/\{columns\}/',
        1=>'/\{column\}/',
        2=>'/\{breakpoint\}/'
      );

        $replace = array(
        0=>$columns[1],
        1=>$columns[0],
        2=>$conf['breakpoint']
      );

        $this->cObj->data['noTrimWrap'] = '| ||';

        return preg_replace($search, $replace, $template);
    }

    /*
    # available : {prefix}{modifier}{breakpoint}{column}{columns}
    # you may add separators within each variable eg {prefix-} or {prefix_}

   template{
    column = col-{breakpoint}-{column}
    offset = col-{breakpoint}-offset-{column}
    pull   = col-{breakpoint}-pull-{column}
    push   = col-{breakpoint}-push-{column}
    visible = {visible}-{breakpoint}
    visibleblock = {visible}-{breakpoint}-block
    hiddenprint = hidden-print
    # bootstrap4
    hideup   = {visible}-{breakpoint}-up
    hidedown = {visible}-{breakpoint}-down
  }

    breakpoints{
    xs=xs
    sm=sm
    md=md
    lg=lg
    xl=xl
  }
    columns{
    # bootstrap
      0=12
    # Pure
    # 0=24
    # 1=5
  }

    */
}
