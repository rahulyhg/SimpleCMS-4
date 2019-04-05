<?php
/*
1.0.0
 - links 		supported
 - Pages 		NOT supported
 - Categories	NOT supported
 - Idividueel	NOT supported
*/


include 'databaseconnection.php';

function get_menu($menuname, $output = "list", $echo = false) {
	global $db_connection;
	if (empty($menuname)) {
		echo "Missing first parameter: menuname.<br><br>";
	} else {
		$sql = $db_connection->prepare("
            SELECT menu_ID
            FROM menus
            WHERE menuname = :menunaam;
        ");

        $sql->execute([
			':menunaam' => $menuname 
        ]);

        if ($sql->rowCount() < 1) {
            echo "Menu " . $menuname . " does not exist.<br><br>";
        } else {
        	$result = $sql->fetch(PDO::FETCH_ASSOC);
        	$sql = $db_connection->prepare("
	            SELECT  menusettings.link_ID, menusettings.linkname, menusettings.target, menusettings.classname, links.url
	            FROM menusettings
	            LEFT JOIN links
	            ON menusettings.link_ID = links.link_ID
	            WHERE menusettings.menu_ID = '$result[menu_ID]';
	        ");

	        $sql->execute();

	        if ($sql->rowCount() < 1) {
	        	echo "Menu " . $menuname . " does not have anny urls jet.<br><br>";
	        } else {
	        	$links = $sql->fetchAll(PDO::FETCH_ASSOC);
	        	if ($output == "array") {
	        		if ($echo === true) {
	        			echo "<pre>" . print_r($links, true) . "</pre>";
	        		} elseif ($echo === false) {
	        			return $links;
	        		} else {
	        			echo "The third parameter has to be either true or false.<br><br>";
	        		}
	        	} elseif ($output == "list") {
	    			$menu = "<ul class=menu".$result['menu_ID'].">";
	        		foreach ($links as $link):
	        			$class = empty($link['classname']) ? '' : 'class="'.$link['classname'].'"'; 
	        			$target = empty($link['target']) ? '' : 'target="'.$link['target'].'"'; 
	        			$url = empty($link['url']) ? '#404' : $link['url']; 
	        			$menu .= '<li '.$class.'><a href="'.$url.'" '.$target.'>'.$link['linkname'].'</a></li>';
	        		endforeach;
	        		$menu .= "</ul>";
	        		if ($echo === true) {
	        			echo $menu;
	        		} elseif ($echo === false) {
	        			return $menu;
	        		} else {
	        			echo "The third parameter has to be either true or false.<br><br>";
	        		}
	        	} else {
	        		echo "unknown output type, try list or array.<br><br>";
	        	}
	        } 	
        }
	}
}

echo get_menu('test1');
echo get_menu('test2');

    category-slug.php
    category-ID.php
    category.php
    archive.php
    index.php


