<?php

namespace kitbs\SlimTwigExts;

class SlimTwigExts extends \Twig_Extension
{
	public function getName()
	{
		return 'kitbs';
	}
	
	public function getFunctions()
	{
		return array(
			'urlActive' => new \Twig_Function_Method($this, 'urlActive', array('is_safe' => array('html'))),
			'truncTag' => new \Twig_Function_Method($this, 'truncTag', array('is_safe' => array('html'))),
			'getColumns' => new \Twig_Function_Method($this, 'getColumns'),
			'testSite' => new \Twig_Function_Method($this, 'testSite'),
			'logSpan' => new \Twig_Function_Method($this, 'logSpan', array('is_safe' => array('html'))),
			'unHTML' => new \Twig_Function_Method($this, 'unHTML', array('is_safe' => array('html'))),
			);
	}

	public function logSpan($logLine, $appName = 'default') {
		$class = '';

		if (strpos($logLine, 'WARN') === 0) {
			$class = 'text-warning strong';
		}
		elseif (
			strpos($logLine, 'FATAL') === 0 ||
			strpos($logLine, 'ERROR') === 0

		) {
			$class = 'text-error';
		}
		elseif (
			strpos($logLine, '-----') !== FALSE ||
			strpos($logLine, '=====') !== FALSE

		) {
			$class = 'muted text-muted';
		}
		elseif (
			strpos($logLine, ' successful') !== FALSE
		) {
			$class = 'text-success strong';
		}

		return '<span class="'.$class.'">' . $logLine . '</span>';
	}

	public function testSite() {
		return TESTSITE;
	}

	public function unHTML($html, $appName = 'default') {
		return html_entity_decode(htmlspecialchars_decode(strip_tags($html)));
	}
	
	public function urlActive($route, $useAttr = true, $appName = 'default')
	{
		$getRoute = explode('/', trim(\Slim\Slim::getInstance($appName)->request()->getResourceUri(),'/'));		
		
		if (!is_array($route)) {
			$isRoute = $route == $getRoute[0];
			//if (!$isRoute) $isRoute = rtrim($route, 's') == $getRoute[0]; // allow for plurals
		}
		else {
			$isRoute = in_array($getRoute[0], $route);
		}
		
		return $isRoute ? ($useAttr ? ' class="active"' : ' active') : false;
	}
	
	public function truncTag($tag, $len = 3, $appName = 'default')
	{	
		$fulltag = $tag;
		$tag = $this->truncOneTag($tag, $len);
		
		$link = \Slim\Slim::getInstance($appName)->urlFor('tag', array('tag' => $fulltag));
		
		return sprintf('<span class="label" title="%s"><a href="%s">%s</a></span>', $fulltag, $link, $tag);
	}
	
	public function truncOneTag($tag, $len = 3) {
		if (strlen($tag) <= ($len*2)+2) return $tag;
		
		return substr($tag, 0, $len) . '&hellip;'. substr($tag, -1*$len) . '<span class="taghide">'.$tag.'</span>';
	}
	
	public function getColumns($key, $useDefault = true) {
		
		$operators = '<>!';
		
		$default_col = array(
			'sortable' => true
			);
		
		$default_cols = array(
			'code' => array(
				'title' => 'Code',
				'class' => 'rec-cd',				
				),
			'title' => array(
				'title' => 'Title',
				),
			'>active' => array(
				'title' => 'Active',
				'class' => 'rec-fg',
				'dropdown' => true,
				)
			);
		
		$config_cols = array(
			
			'tag' => array(
				'title' => array(
					'title' => 'Tag'
					),
				'!active',
				'!code'
				),
			
			'tags' => array(
				'type' => array(
					'title' => 'Type',
					'class' => 'rec-cd-wide',
					'dropdown' => true,
					),
				'record' => array(
					'title' => 'Used By',
					'class' => 'rec-cd-wide',
					),
				'title' => array(
					'title' => 'Title'
					),
				//'!active',
				'!title',
				'!code',
				'!tags',
				),
			
			'urls' => array(
				'title' => array(
					'title' => 'Address'
					),
				'record' => array(
					'title' => 'Used By',
					'class' => 'rec-cd-wide',
					),
				'status' => array(
					'title' => 'Status',
					'class' => 'rec-fg',
					'dropdown' => true
					),
				'status_date' => array(
					'title' => 'Last Checked',
					'class' => 'rec-cd-wide',
					),
				'!active',
				'!code'
				),
			
			'emails' => array(
				'title' => array(
					'title' => 'Address'
					),
				'record' => array(
					'title' => 'Used By',
					'class' => 'rec-cd-wide',
					),
				'!active',
				'!code'
				),

			'logs' => array(
				'title' => array(
					'title' => 'Log File'
					),
				'date' => array(
					'title' => 'Date',
					'class' => 'rec-cd-wide'
					),
				'filename' => array(
					'title' => 'Import File'
					),
				'!active',
				'!code'
				),
			
			'app_req' => array(
				'code' => array(
					'class' => 'rec-cd-wide'
					),
				'docs' => array(
					'title' => 'Docs',
					'class' => 'rec-fg',
					'dropdown' => true,
					),
				'>tags' => array(
					'title' => 'Tags',
					'class' => 'rec-cd'
					)
				
				),
			'doc_req' => array(
				'code' => array(
					'class' => 'rec-cd-wide'
					),
				'>tags' => array(
					'title' => 'Tags',
					'class' => 'rec-cd'
					)
				),
			'cnf_em' => array(
				'!active',
				'>tags' => array(
					'title' => 'Tags',
					'class' => 'rec-cd'
					)
				),
			'std_cd' => array(
				'code' => array(
					'class' => 'rec-cd-wide'
					),
				'!active',
				'>tags' => array(
					'title' => 'Tags',
					'class' => 'rec-cd'
					)
				),
			
			'spk_cat' => array(
				'code' => array(
					'class' => 'rec-fg',
					),
				'level' => array(
					'title' => 'Level',
					'class' => 'rec-fg',
					'dropdown' => true,
					),
				'type' => array(
					'title' => 'Type',
					'class' => 'rec-cd',
					'dropdown' => true,
					),
				'custom' => array(
					'title' => 'Custom',
					'class' => 'rec-fg',
					'dropdown' => true,
					),
				'!active',
				'!tags',
				'apply' => array(
					'title' => 'Apply',
					'class' => 'rec-fg',
					'dropdown' => true,
					)
				),
			
			'spk_cd' => array(
				'code' => array(
					'class' => 'rec-cd-thin',
					),
				'type' => array(
					'title' => 'Type',
					'class' => 'rec-fg',
					'dropdown' => true,
					),
				'custom' => array(
					'title' => 'Custom',
					'class' => 'rec-fg',
					'dropdown' => true,
					),
				'!active',
				'!tags',
				'apply' => array(
					'title' => 'Apply',
					'class' => 'rec-fg',
					'dropdown' => true,
					)
				),
			
			'sql_query' => array(
				'code' => array(
					'title' => 'File Name',
					'class' => 'rec-cd-wide'
					),
				'!active',
				'!tags'
				),
			
			'spk_app_req' => array(
				'<inherit' => array(
					'title' => 'Inherits',
					'class' => 'rec-fg',
					),
				'code' => array(
					'class' => 'rec-cd-wide'
					),
				'!tags',
				'docs' => array(
					'title' => 'Docs',
					'class' => 'rec-fg',
					)
				),
			
			'spk_cnf_em' => array(
				'<inherit' => array(
					'title' => 'Inherits',
					'class' => 'rec-fg',
					),
				'!tags',
				'code' => array(
					'class' => 'rec-cd-wide'
					)
				),
			
			);

$this_cols = $config_cols[$key];

if (is_array($this_cols)) {

	if ($useDefault) {

		$start = array();
		$end = array();

		foreach ($default_cols as $dkey => $dval) {
			if (in_array('!'.ltrim($dkey, $operators), $this_cols)) {
						// don't add a skipped key
			}
			elseif (substr($dkey, 0, 1) == '>') {
				$end[ltrim($dkey, $operators)] = array_merge($default_col, $dval);
			}
			else {
				$start[$dkey] = array_merge($default_col, $dval);
			}
		}

		$cols = array();
		$cols_start = array();
		$cols_end = array();

		foreach ($this_cols as $ckey => $cval) {
			if (is_array($cval)) {
				if (is_array(@$default_cols[ltrim($ckey, $operators)])) {
					$defaults = array_merge($default_col, $default_cols[ltrim($ckey, $operators)]);
				}
				else {
					$defaults = $default_col;
				}

				if (substr($ckey, 0, 1) == '>') {
					$cols_end[ltrim($ckey, $operators)] = array_merge($defaults, $cval);
				}
				elseif (substr($ckey, 0, 1) == '<') {
					$cols_start[ltrim($ckey, $operators)] = array_merge($defaults, $cval);
				}
				else {
					$cols[ltrim($ckey, $operators)] = array_merge($defaults, $cval);
				}
			}
		}

		$cols = array_merge($cols_start, $start, $cols, $end, $cols_end);

	}
	else {
		$cols = $this_cols;
	}
}
else {

	$cols = array();
	foreach ($default_cols as $dkey => $dval) {
		$cols[ltrim($dkey, $operators)] = array_merge($default_col, $dval);
	}
}	

return $cols;	

}
}
