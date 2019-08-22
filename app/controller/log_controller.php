<?php
F::redirect('auth', !Auth::user());
F::redirect(F::config('defaultCommand'), !Auth::activeUserInRole('SUPER,ADMIN'));


switch ($fusebox->action) :


	case 'index':
		$arguments['page'] = !empty($arguments['page']) ? $arguments['page'] : 1;
		// show own records if not admin
		if ( method_exists('Auth', 'userInRole') and !Auth::userInRole('SUPER,ADMIN') ) {
			$arguments['search']['username'] = Auth::user('username');
		}
		// filter all records
		$filter = '1 = 1 ';
		$param = array();
		if ( isset($arguments['search']) ) {
			foreach ( $arguments['search'] as $key => $val ) {
				if ( trim($val) != '' ) {
					$col = ( substr($key, -8) == '_keyword' ) ? substr($key, 0, strlen($key)-8) : $key;
					$filter .= "AND {$col} LIKE ? ";
					$param[] = ( substr($key, -8) == '_keyword' ) ? "%{$val}%" : $val;
				}
			}
		}
		// ordering
		$filter .= 'ORDER BY datetime DESC, id DESC ';
		// pagination
		$logCount = Log::count($filter, $param);
		$arguments['pagination'] = array(
			'record_count' => $logCount,
			'record_per_page' => 20,
			'page_visible' => 10
		);
		// get records
		$limit = (($arguments['page']-1)*$arguments['pagination']['record_per_page']) . ',' . $arguments['pagination']['record_per_page'];
		$logs = Log::find($filter, $param, $limit);
		// listbox : action
		if ( !empty($arguments['search']['username']) ) {
			$filter = "username = '{$arguments['search']['username']}' ";
		} else {
			$filter = '';
		}
		$logActions = Log::getDistinct('action', $filter);
		// listbox : user (if admin)
		if ( !method_exists('Auth', 'userInRole') or Auth::userInRole('SUPER,ADMIN') ) {
			if ( !empty($arguments['search']['action']) ) {
				$filter = "action = '{$arguments['search']['action']}' ";
			} else {
				$filter = '';
			}
			$logUsers = Log::getDistinct('username', $filter);
		}
		// exit point
		$xfa['search'] = 'log.search';
		if ( isset($arguments['search']) ) {
			$xfa['reset'] = 'log';
		}
		// display
		ob_start();
		include F::config('appPath').'view/log/header.php';
		if ( isset($xfa['search']) ) include F::config('appPath').'view/log/search.php';
		foreach ( $logs as $bean ) include F::config('appPath').'view/log/row.php';
		$layout['content'] = ob_get_clean();
		// breadcrumb
		$arguments['breadcrumb'] = array('Audit Log');
		// layout
		if ( Framework::$mode == Framework::FUSEBOX_UNIT_TEST ) {
			echo $layout['content'];
		} else {
			$layout['width'] = 'full';
			include F::config('appPath').'view/global/layout.php';
		}
		break;


	// convert form variable to url variable so that user can refresh
	case 'search':
		// adjust query string
		$queryString = '';
		if ( isset($arguments['search']) ) {
			foreach ( $arguments['search'] as $key => $val ) {
				$val = trim($val);
				if ( trim($val) != '' ) {
					$queryString .= '&'.urlencode("search[{$key}]").'='.urlencode($val);
				}
			}
		}
		// redirect to listing
		$xfa['redirect'] = "log";
		F::redirect($xfa['redirect'].$queryString);
		break;


	default:
		F::pageNotFound();


endswitch;