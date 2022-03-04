<?php 
function main_navigation($navs = array(), $is_sub = FALSE){
	$html = '';
	if( is_array($navs) && count($navs) > 0){
		$html .= $is_sub === TRUE ? '<ul class="nav-main-submenu">' : '<ul class="nav-main">';
		foreach($navs AS $nav){
			$nav 		= (object)$nav;
			$is_heading = (isset($nav->is_heading) && $nav->is_heading == TRUE);
			$html .= '<li class="nav-main-'. ($is_heading ? "heading" : "item") .'">';
			if($is_heading){
				$html .= $nav->title;
			} else {
				$any_child = (isset($nav->childs) && is_array($nav->childs) && count($nav->childs) > 0);
				if($any_child){
					$html .= '<a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">';
				} else {
					$html .= '<a class="nav-main-link" href="'. (isset($nav->href) ? $nav->href : NULL) .'">';
				}
				$html .= isset($nav->icon) ? '<i class="nav-main-link-icon '. $nav->icon .'"></i>' : NULL;
				$html .= '<span class="nav-main-link-name">'. $nav->title .'</span></a>';
				$html .= $any_child ? main_navigation($nav->childs, TRUE) : NULL;
			}
			$html .= '</li>';
		}
		$html .= '</ul>';
	}
	return $html;
}