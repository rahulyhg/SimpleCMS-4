<?php

function exists_in_theme_dir($file_path = "") {
    if (!file_exists('../Themes/'. THEME .'/'.$file_path))
        return false;
    return true;
}

function path_to_theme_dir($file_path = "") {
    return '../Themes/'. THEME .'/'.$file_path;
}

function get_title() {
    global $pagetype, $page_ID;
    switch ($pagetype) {
        case 'pages':
            $title = execute_sql("
                SELECT page_name
	            FROM pages 
	            WHERE page_ID = :page_ID;
            ",
                [':page_ID' => $page_ID]
            );
            $result = $title->fetchColumn();
            break;
        case 'posts':
            $title = execute_sql("
                SELECT post_name
	            FROM posts 
	            WHERE post_ID = :page_ID;
            ",
                [':page_ID' => $page_ID]
            );
            $result = $title->fetchColumn();
            break;
        case 'categories':
            $title = execute_sql("
                SELECT cat_name
	            FROM categories
	            WHERE cat_ID = :page_ID;
            ",
                [':page_ID' => $page_ID]
            );
            $result = $title->fetchColumn();
            break;
        default:
            return 'ERROR';
            break;
    }
    return $result;
}

function get_date() {
    global $pagetype, $page_ID;
    switch ($pagetype) {
        case 'pages':
            $date = execute_sql("
                SELECT created_at
	            FROM pages 
	            WHERE page_ID = :page_ID;
            ",
                [':page_ID' => $page_ID]
            );
            $result = $date->fetchColumn();
            break;
        case 'posts':
            $date = execute_sql("
                SELECT created_at
	            FROM posts 
	            WHERE post_ID = :page_ID;
            ",
                [':page_ID' => $page_ID]
            );
            $result = $date->fetchColumn();
            break;
        case 'categories':
            $date = execute_sql("
                SELECT created_at
	            FROM categories
	            WHERE cat_ID = :page_ID;
            ",
                [':page_ID' => $page_ID]
            );
            $result = $date->fetchColumn();
            break;
        default:
            return 'ERROR';
            break;
    }
    return $result;
}